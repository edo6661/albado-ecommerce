<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Services\AddressServiceInterface;
use App\Contracts\Services\CartServiceInterface;
use App\Contracts\Services\MidtransServiceInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Services\ShippingServiceInterface;
use App\Http\Requests\Api\Payment\CheckoutRequest;
use App\Http\Requests\Api\Payment\PaymentCallbackRequest;
use App\Http\Requests\Api\Payment\CalculateShippingRequest;
use App\Http\Resources\OrderDetailResource;
use App\Http\Resources\TransactionDetailResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(
        protected CartServiceInterface $cartService,
        protected OrderServiceInterface $orderService,
        protected MidtransServiceInterface $midtransService,
        protected AddressServiceInterface $addressService,
        protected ShippingServiceInterface $shippingService
    ) {}

    /**
     * Checkout process for mobile app
     *
     * @param CheckoutRequest $request
     * @return JsonResponse
     */
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            
            $address = $this->addressService->getAddressById($validated['address_id']);
            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat pengiriman tidak ditemukan.'
                ], 404);
            }

            
            $shipping = $this->shippingService->calculate($address->latitude, $address->longitude);
            if (!$shipping['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $shipping['message']
                ], 400);
            }

            
            $cartItems = $this->cartService->getCartItemsByIds(Auth::id(), $validated['selected_items']);

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item yang dipilih tidak ditemukan'
                ], 400);
            }

            
            $orderData = [
                'user_id' => Auth::id(),
                'tax_rate' => 0,
                'shipping_cost' => $shipping['cost'],
                'distance_km' => $shipping['distance_km'],
                'shipping_address' => $address->getFullAddressAttribute(),
                'address_id' => $address->id,
            ];

            
            $items = $cartItems->map(function ($cartItem) {
                return [
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity
                ];
            })->toArray();

            
            $order = $this->orderService->createOrder($orderData, $items);

            
            $paymentResult = $this->midtransService->createPayment($order);

            if (!$paymentResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['error']
                ], 400);
            }

            
            foreach ($cartItems as $cartItem) {
                $this->cartService->removeFromCart(Auth::id(), $cartItem->product_id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil',
                'data' => [
                    'order' => new OrderDetailResource($order->load(['items.product', 'transaction'])),
                    'payment' => [
                        'snap_token' => $paymentResult['snap_token'],
                        'redirect_url' => $paymentResult['redirect_url']
                    ],
                    'shipping' => [
                        'cost' => $shipping['cost'],
                        'distance_km' => $shipping['distance_km'],
                        'formatted_cost' => $shipping['formatted_cost']
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get checkout summary for mobile app
     *
     * @param Order $order
     * @return JsonResponse
     */
    public function checkoutSummary(Order $order): JsonResponse
    {
        try {
            
            if ($order->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Order tidak ditemukan.'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Summary checkout berhasil diambil',
                'data' => new OrderDetailResource($order->load(['items.product', 'transaction', 'user']))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary checkout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle payment callback from Midtrans
     *
     * @param PaymentCallbackRequest $request
     * @return JsonResponse
     */
    public function paymentCallback(PaymentCallbackRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $order = $this->orderService->getOrderById($validated['order_id']);

            $transaction = $order->transaction;

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            
            $transactionStatus = $this->mapCallbackStatus($validated['transaction_status']);

            
            $transaction->update([
                'status' => $transactionStatus,
                'midtrans_response' => $validated['midtrans_result'] ?? null,
                'settlement_time' => $validated['transaction_status'] === 'settlement' ? now() : null,
            ]);

            
            $orderStatus = $this->mapTransactionToOrderStatus($validated['transaction_status']);
            $this->orderService->updateOrderStatus($order->id, $orderStatus);

            return response()->json([
                'success' => true,
                'message' => 'Transaction status updated successfully',
                'data' => [
                    'transaction_status' => $transactionStatus,
                    'order_status' => $orderStatus,
                    'order' => new OrderDetailResource($order->fresh(['items.product', 'transaction']))
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get payment status/result for mobile app
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function paymentStatus(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            
            if ($request->has('order_id')) {
                $request->validate([
                    'order_id' => 'required|integer|exists:orders,id,user_id,' . $user->id
                ]);

                $order = $this->orderService->getOrderById($request->order_id);
            } else {
                
                $order = $user->orders()->with(['latestTransaction', 'items.product'])->latest()->first();
            }

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status pembayaran berhasil diambil',
                'data' => [
                    'order' => new OrderDetailResource($order),
                    'transaction' => $order->transaction ? new TransactionDetailResource($order->transaction) : null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil status pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate shipping cost
     *
     * @param CalculateShippingRequest $request
     * @return JsonResponse
     */
    public function calculateShipping(CalculateShippingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $address = $this->addressService->getAddressById($validated['address_id']);
            
            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            $shipping = $this->shippingService->calculate($address->latitude, $address->longitude);

            if (!$shipping['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $shipping['message']
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ongkos kirim berhasil dihitung',
                'data' => [
                    'distance_km' => $shipping['distance_km'],
                    'cost' => $shipping['cost'],
                    'formatted_cost' => $shipping['formatted_cost']
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung ongkos kirim',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resume payment for existing order
     *
     * @param Order $order
     * @return JsonResponse
     */
    public function resumePayment(Order $order): JsonResponse
    {
        try {
            
            if ($order->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Order tidak ditemukan.'
                ], 403);
            }

            
            if ($order->status->value !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order dengan status ' . $order->status->label() . ' tidak dapat dilanjutkan pembayarannya.'
                ], 400);
            }

            $transaction = $order->transaction;
            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction tidak ditemukan'
                ], 404);
            }

            
            $paymentResult = $this->midtransService->resumePayment($transaction);

            if (!$paymentResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['error'] ?? 'Gagal melanjutkan pembayaran'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dilanjutkan',
                'data' => [
                    'order' => new OrderDetailResource($order->load(['items.product', 'transaction'])),
                    'payment' => [
                        'snap_token' => $paymentResult['snap_token'],
                        'redirect_url' => $paymentResult['redirect_url']
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melanjutkan pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Map callback status to transaction status
     *
     * @param string $callbackStatus
     * @return string
     */
    private function mapCallbackStatus(string $callbackStatus): string
    {
        return match($callbackStatus) {
            'settlement' => 'settlement',
            'pending' => 'pending',
            'failure' => 'failure',
            'cancel' => 'cancel',
            'expire' => 'expire',
            'deny' => 'deny',
            default => 'pending'
        };
    }

    /**
     * Map transaction status to order status
     *
     * @param string $transactionStatus
     * @return string
     */
    private function mapTransactionToOrderStatus(string $transactionStatus): string
    {
        return match($transactionStatus) {
            'settlement' => 'paid',
            'pending' => 'pending',
            'failure', 'cancel', 'expire', 'deny' => 'failed',
            default => 'pending'
        };
    }
}