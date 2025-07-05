<?php

namespace App\Repositories;

use App\Contracts\Repositories\MidtransRepositoryInterface;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransTransaction;
use Midtrans\CoreApi;
use Illuminate\Support\Facades\Log;

class MidtransRepository implements MidtransRepositoryInterface
{
    public function createPaymentToken(array $transactionDetails, array $customerDetails, array $itemDetails = []): array
    {
        try {
            $payload = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
            ];

            if (!empty($itemDetails)) {
                $payload['item_details'] = $itemDetails;
            }

            $payload['enabled_payments'] = [
                'credit_card', 'bca_va', 'bni_va', 'bri_va', 'mandiri_va',
                'permata_va', 'other_va', 'gopay', 'shopeepay', 'qris'
            ];

            $payload['credit_card'] = [
                'secure' => true,
                'channel' => 'migs',
                'bank' => 'bca',
                'installment' => [
                    'required' => false,
                    'terms' => [
                        'bni' => [3, 6, 12],
                        'mandiri' => [3, 6, 12],
                        'cimb' => [3],
                        'bca' => [3, 6, 12],
                        'maybank' => [3, 6, 12],
                    ]
                ]
            ];

            $payload['callbacks'] = [
                'finish' => route('payment.finish'),
            ];
            

            $snapToken = Snap::getSnapToken($payload);

            Log::info('Midtrans payment token created', [
                'order_id' => $transactionDetails['order_id'],
                'gross_amount' => $transactionDetails['gross_amount']
            ]);

            return [
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => "https://app.sandbox.midtrans.com/snap/v1/transactions/{$snapToken}/pay"
            ];

        } catch (\Exception $e) {
            Log::error('Failed to create Midtrans payment token', [
                'error' => $e->getMessage(),
                'transaction_details' => $transactionDetails
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function getTransactionStatus(string $orderId): array
    {
        try {
            $status = MidtransTransaction::status($orderId);

            Log::info('Midtrans transaction status retrieved', [
                'order_id' => $orderId,
                'status' => $status
            ]);

            return [
                'success' => true,
                'data' => (array) $status
            ];

        } catch (\Exception $e) {
            Log::error('Failed to get Midtrans transaction status', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function cancelTransaction(string $orderId): array
    {
        try {
            $result = MidtransTransaction::cancel($orderId);

            Log::info('Midtrans transaction cancelled', [
                'order_id' => $orderId,
                'result' => $result
            ]);

            return [
                'success' => true,
                'data' => (array) $result
            ];

        } catch (\Exception $e) {
            Log::error('Failed to cancel Midtrans transaction', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function captureTransaction(string $orderId): array
    {
        try {
            $result = CoreApi::capture($orderId);

            Log::info('Midtrans transaction captured', [
                'order_id' => $orderId,
                'result' => $result
            ]);

            return [
                'success' => true,
                'data' => (array) $result
            ];

        } catch (\Exception $e) {
            Log::error('Failed to capture Midtrans transaction', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function approveTransaction(string $orderId): array
    {
        try {
            $result = MidtransTransaction::approve($orderId);

            Log::info('Midtrans transaction approved', [
                'order_id' => $orderId,
                'result' => $result
            ]);

            return [
                'success' => true,
                'data' => (array) $result
            ];

        } catch (\Exception $e) {
            Log::error('Failed to approve Midtrans transaction', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function denyTransaction(string $orderId): array
    {
        try {
            $result = MidtransTransaction::deny($orderId);

            Log::info('Midtrans transaction denied', [
                'order_id' => $orderId,
                'result' => $result
            ]);

            return [
                'success' => true,
                'data' => (array) $result
            ];

        } catch (\Exception $e) {
            Log::error('Failed to deny Midtrans transaction', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function expireTransaction(string $orderId): array
    {
        try {
            $result = MidtransTransaction::expire($orderId);

            Log::info('Midtrans transaction expired', [
                'order_id' => $orderId,
                'result' => $result
            ]);

            return [
                'success' => true,
                'data' => (array) $result
            ];

        } catch (\Exception $e) {
            Log::error('Failed to expire Midtrans transaction', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function refundTransaction(string $orderId, array $refundData = []): array
    {
        try {
            $result = MidtransTransaction::refund($orderId, $refundData);

            Log::info('Midtrans transaction refunded', [
                'order_id' => $orderId,
                'refund_data' => $refundData,
                'result' => $result
            ]);

            return [
                'success' => true,
                'data' => (array) $result
            ];

        } catch (\Exception $e) {
            Log::error('Failed to refund Midtrans transaction', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}