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

    private function formatIDRAmount(float $amount): int
    {
        return (int) round($amount);
    }
                            
    private function validateItemDetails(array $itemDetails, int $expectedGrossAmount): array
    {
        $totalItemPrice = 0;
        $validatedItems = [];

        foreach ($itemDetails as $item) {
            $validatedItem = [
                'id' => $item['id'],
                'price' => $this->formatIDRAmount($item['price']),
                'quantity' => $item['quantity'],
                'name' => $item['name'],
            ];
            
            $totalItemPrice += $validatedItem['price'] * $validatedItem['quantity'];
            $validatedItems[] = $validatedItem;
        }

        $difference = $expectedGrossAmount - $totalItemPrice;
        if ($difference !== 0 && !empty($validatedItems)) {
            $lastIndex = count($validatedItems) - 1;
            $validatedItems[$lastIndex]['price'] += $difference;
            
            Log::info('Adjusted item price for IDR rounding', [
                'difference' => $difference,
                'adjusted_item' => $validatedItems[$lastIndex]
            ]);
        }

        return $validatedItems;
    }

    public function createPayment(Order $order): array
    {
        try {
            DB::beginTransaction();

            $midtransOrderId = Transaction::generateMidtransOrderId();
            $grossAmount = $this->formatIDRAmount($order->total);

            $transactionDetails = [
                'order_id' => $midtransOrderId,
                'gross_amount' => $grossAmount
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

            $validatedItemDetails = $this->validateItemDetails($itemDetails, $grossAmount);

            Log::info('Creating Midtrans payment', [
                'order_id' => $order->id,
                'midtrans_order_id' => $midtransOrderId,
                'gross_amount' => $grossAmount,
                'original_total' => $order->total,
                'item_count' => count($validatedItemDetails)
            ]);

            $paymentResult = $this->midtransRepository->createPaymentToken(
                $transactionDetails,
                $customerDetails,
                $validatedItemDetails
            );

            if (!$paymentResult['success']) {
                throw new \Exception('Failed to create payment token: ' . $paymentResult['error']);
            }

            $transaction = $this->transactionRepository->create([
                'order_id' => $order->id,
                'transaction_id' => uniqid('TXN_'),
                'order_id_midtrans' => $midtransOrderId,
                'status' => TransactionStatus::PENDING,
                'gross_amount' => $grossAmount,
                'currency' => 'IDR',
                'transaction_time' => now(),
                'snap_token' => $paymentResult['snap_token'],
                'snap_url' => $paymentResult['redirect_url'],
            ]);

            DB::commit();

            Log::info('Payment created successfully', [
                'order_id' => $order->id,
                'transaction_id' => $transaction->id,
                'midtrans_order_id' => $midtransOrderId,
                'formatted_amount' => $grossAmount
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
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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

            Log::info('Processing Midtrans notification', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus
            ]);

            // Cari transaksi berdasarkan order_id_midtrans
            $transaction = $this->transactionRepository->findByOrderIdMidtrans($orderId);
            if (!$transaction) {
                Log::error('Transaction not found for order ID', [
                    'order_id' => $orderId,
                    'available_transactions' => $this->transactionRepository->getRecentTransactions(5)->pluck('order_id_midtrans')
                ]);
                throw new \Exception("Transaction not found for order ID: {$orderId}");
            }

            // Update transaction dari data notification langsung (tanpa query ke Midtrans)
            $updatedTransaction = $this->updateTransactionFromNotification($transaction, $notification);

            // Update status order
            $this->updateOrderStatus($updatedTransaction);

            // Fire events
            $this->fireTransactionEvents($updatedTransaction);

            DB::commit();

            Log::info('Notification handled successfully', [
                'order_id' => $orderId,
                'transaction_id' => $updatedTransaction->id,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'new_status' => $updatedTransaction->status->value
            ]);

            return $updatedTransaction;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to handle notification', [
                'notification' => $notification,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
                $refundData['amount'] = $this->formatIDRAmount($amount);
            }

            $result = $this->midtransRepository->refundTransaction(
                $transaction->order_id_midtrans,
                $refundData
            );

            if ($result['success']) {
                Log::info('Payment refunded successfully', [
                    'transaction_id' => $transaction->id,
                    'order_id_midtrans' => $transaction->order_id_midtrans,
                    'amount' => $amount,
                    'formatted_amount' => $refundData['amount'] ?? 'full'
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

    /**
     * Update transaction dari data notification langsung
     */
    public function updateTransactionFromNotification(Transaction $transaction, array $notificationData): Transaction
    {
        $status = $this->mapMidtransStatus($notificationData['transaction_status'], $notificationData['fraud_status'] ?? null);
        $paymentType = $this->mapPaymentType($notificationData['payment_type'] ?? null);

        $updateData = [
            'status' => $status,
            'payment_type' => $paymentType,
            'transaction_time' => isset($notificationData['transaction_time']) ? 
                Carbon::parse($notificationData['transaction_time']) : null,
            'settlement_time' => isset($notificationData['settlement_time']) ? 
                Carbon::parse($notificationData['settlement_time']) : null,
            'fraud_status' => $notificationData['fraud_status'] ?? null,
            'status_message' => $notificationData['status_message'] ?? null,
            'midtrans_response' => $notificationData,
        ];

        $this->transactionRepository->update($transaction, $updateData);

        return $this->transactionRepository->findById($transaction->id);
    }

    /**
     * Update transaction dari data Midtrans API (untuk backward compatibility)
     */
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
    public function resumePayment(Transaction $transaction): array
    {
        if ($transaction->snap_token && $transaction->snap_url) {
            return [
                'success' => true,
                'snap_token' => $transaction->snap_token,
                'redirect_url' => $transaction->snap_url
            ];
        }
        
        $order = $transaction->order;
        return $this->createPayment($order);
    }
}