<?php

namespace App\Services;

use App\Contracts\Services\MidtransServiceInterface;
use App\Contracts\Repositories\MidtransRepositoryInterface;
use App\Contracts\Repositories\TransactionRepositoryInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\Models\Order;
use App\Models\Transaction;
use App\Enums\TransactionStatus;
use App\Enums\PaymentType;
use App\Enums\OrderStatus;
use App\Events\Order\PaymentProcessed;
use App\Events\Order\PaymentFailed;
use App\Events\Order\PaymentSuccess;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class MidtransService implements MidtransServiceInterface
{
    public function __construct(
        protected MidtransRepositoryInterface $midtransRepository,
        protected TransactionRepositoryInterface $transactionRepository,
        protected OrderServiceInterface $orderService
    ) {}

    public function createPayment(Order $order): array
    {
        try {
            DB::beginTransaction();

            $midtransOrderId = Transaction::generateMidtransOrderId();

            $transactionDetails = [
                'order_id' => $midtransOrderId,
                'gross_amount' => $order->total
            ];

            $customerDetails = [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone ?? '',
            ];

            $itemDetails = [];
            foreach ($order->items as $item) {
                $itemDetails[] = [
                    'id' => $item->product_id,
                    'price' => $item->product_price,
                    'quantity' => $item->quantity,
                    'name' => $item->product_name,
                ];
            }

            if ($order->tax > 0) {
                $itemDetails[] = [
                    'id' => 'TAX',
                    'price' => $order->tax,
                    'quantity' => 1,
                    'name' => 'Tax',
                ];
            }

            $paymentResult = $this->midtransRepository->createPaymentToken(
                $transactionDetails,
                $customerDetails,
                $itemDetails
            );

            if (!$paymentResult['success']) {
                throw new \Exception('Failed to create payment token: ' . $paymentResult['error']);
            }

            $transaction = $this->transactionRepository->create([
                'order_id' => $order->id,
                'transaction_id' => uniqid('TXN_'),
                'order_id_midtrans' => $midtransOrderId,
                'status' => TransactionStatus::PENDING,
                'gross_amount' => $order->total,
                'currency' => 'IDR',
                'transaction_time' => now(),
            ]);

            DB::commit();

            Log::info('Payment created successfully', [
                'order_id' => $order->id,
                'transaction_id' => $transaction->id,
                'midtrans_order_id' => $midtransOrderId
            ]);

            return [
                'success' => true,
                'transaction' => $transaction,
                'snap_token' => $paymentResult['snap_token'],
                'redirect_url' => $paymentResult['redirect_url']
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create payment', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function handleNotification(array $notification): Transaction
    {
        try {
            DB::beginTransaction();

            $orderId = $notification['order_id'];
            $transactionStatus = $notification['transaction_status'];
            $fraudStatus = $notification['fraud_status'] ?? null;

            $transaction = $this->transactionRepository->findByOrderIdMidtrans($orderId);
            if (!$transaction) {
                throw new \Exception("Transaction not found for order ID: {$orderId}");
            }

            $statusResult = $this->midtransRepository->getTransactionStatus($orderId);
            if (!$statusResult['success']) {
                throw new \Exception('Failed to get transaction status from Midtrans');
            }

            $midtransData = $statusResult['data'];

            $updatedTransaction = $this->updateTransactionFromMidtrans($transaction, $midtransData);

            $this->updateOrderStatus($updatedTransaction);

            $this->fireTransactionEvents($updatedTransaction);

            DB::commit();

            Log::info('Notification handled successfully', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus
            ]);

            return $updatedTransaction;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to handle notification', [
                'notification' => $notification,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    public function checkTransactionStatus(string $orderId): array
    {
        return $this->midtransRepository->getTransactionStatus($orderId);
    }

    public function cancelPayment(Transaction $transaction): bool
    {
        try {
            $result = $this->midtransRepository->cancelTransaction($transaction->order_id_midtrans);

            if ($result['success']) {
                $this->transactionRepository->update($transaction, [
                    'status' => TransactionStatus::CANCEL,
                    'status_message' => 'Payment cancelled by system'
                ]);

                $this->orderService->updateOrderStatus($transaction->order_id, OrderStatus::CANCELLED->value);

                Log::info('Payment cancelled successfully', [
                    'transaction_id' => $transaction->id,
                    'order_id_midtrans' => $transaction->order_id_midtrans
                ]);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to cancel payment', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    public function refundPayment(Transaction $transaction, ?float $amount = null): bool
    {
        try {
            $refundData = [];
            if ($amount) {
                $refundData['amount'] = $amount;
            }

            $result = $this->midtransRepository->refundTransaction(
                $transaction->order_id_midtrans,
                $refundData
            );

            if ($result['success']) {
                Log::info('Payment refunded successfully', [
                    'transaction_id' => $transaction->id,
                    'order_id_midtrans' => $transaction->order_id_midtrans,
                    'amount' => $amount
                ]);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to refund payment', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    public function updateTransactionFromMidtrans(Transaction $transaction, array $midtransData): Transaction
    {
        $status = $this->mapMidtransStatus($midtransData['transaction_status'], $midtransData['fraud_status'] ?? null);
        $paymentType = $this->mapPaymentType($midtransData['payment_type'] ?? null);

        $updateData = [
            'status' => $status,
            'payment_type' => $paymentType,
            'transaction_time' => isset($midtransData['transaction_time']) ? 
                Carbon::parse($midtransData['transaction_time']) : null,
            'settlement_time' => isset($midtransData['settlement_time']) ? 
                Carbon::parse($midtransData['settlement_time']) : null,
            'fraud_status' => $midtransData['fraud_status'] ?? null,
            'status_message' => $midtransData['status_message'] ?? null,
            'midtrans_response' => $midtransData,
        ];

        $this->transactionRepository->update($transaction, $updateData);

        return $this->transactionRepository->findById($transaction->id);
    }

    protected function mapMidtransStatus(string $transactionStatus, ?string $fraudStatus = null): TransactionStatus
    {
        return match($transactionStatus) {
            'capture' => $fraudStatus === 'challenge' ? TransactionStatus::PENDING : TransactionStatus::CAPTURE,
            'settlement' => TransactionStatus::SETTLEMENT,
            'pending' => TransactionStatus::PENDING,
            'deny' => TransactionStatus::DENY,
            'cancel' => TransactionStatus::CANCEL,
            'expire' => TransactionStatus::EXPIRE,
            'failure' => TransactionStatus::FAILURE,
            default => TransactionStatus::PENDING,
        };
    }

    protected function mapPaymentType(?string $paymentType): ?PaymentType
    {
        if (!$paymentType) return null;

        return match($paymentType) {
            'credit_card' => PaymentType::CREDIT_CARD,
            'bank_transfer', 'bca_va', 'bni_va', 'bri_va', 'mandiri_va', 'permata_va', 'other_va' => PaymentType::BANK_TRANSFER,
            'echannel' => PaymentType::ECHANNEL,
            'gopay' => PaymentType::GOPAY,
            'shopeepay' => PaymentType::SHOPEEPAY,
            'qris' => PaymentType::QRIS,
            'cstore' => PaymentType::CSTORE,
            'akulaku' => PaymentType::AKULAKU,
            'bca_klikpay' => PaymentType::BCA_KLIKPAY,
            'bca_klikbca' => PaymentType::BCA_KLIKBCA,
            'bri_epay' => PaymentType::BRI_EPAY,
            'cimb_clicks' => PaymentType::CIMB_CLICKS,
            'danamon_online' => PaymentType::DANAMON_ONLINE,
            default => PaymentType::OTHER,
        };
    }

    protected function updateOrderStatus(Transaction $transaction): void
    {
        $orderStatus = match($transaction->status) {
            TransactionStatus::SETTLEMENT, TransactionStatus::CAPTURE => OrderStatus::PAID,
            TransactionStatus::DENY, TransactionStatus::CANCEL, TransactionStatus::EXPIRE, TransactionStatus::FAILURE => OrderStatus::FAILED,
            default => OrderStatus::PENDING,
        };

        $this->orderService->updateOrderStatus($transaction->order_id, $orderStatus->value);
    }

    protected function fireTransactionEvents(Transaction $transaction): void
    {
        Event::dispatch(new PaymentProcessed($transaction));

        if ($transaction->status->isSuccess()) {
            Event::dispatch(new PaymentSuccess($transaction));
        } elseif ($transaction->status->isFailed()) {
            Event::dispatch(new PaymentFailed($transaction));
        }
    }
}