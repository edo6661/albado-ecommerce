<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contracts\Services\CartServiceInterface;
use App\Contracts\Services\CategoryServiceInterface;
use App\Contracts\Services\MidtransServiceInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Services\ProductServiceInterface;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
class PaymentController extends Controller
{
    public function __construct(
        protected CartServiceInterface $cartService,
        protected OrderServiceInterface $orderService,
        protected MidtransServiceInterface $midtransService
    ) {}

    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'integer|exists:cart_items,id'
        ]);

        try {
            $cartItems = $this->cartService->getCartItemsByIds(Auth::id(), $request->selected_items);
            
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item yang dipilih tidak ditemukan'
                ], 400);
            }

            
            $orderData = [
                'user_id' => Auth::id(),
                'tax_rate' => 0 
            ];

            $items = $cartItems->map(function ($cartItem) {
                return [
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity
                ];
            })->toArray();

            $order = $this->orderService->createOrder($orderData, $items);

            
            foreach ($cartItems as $cartItem) {
                $this->cartService->removeFromCart(Auth::id(), $cartItem->product_id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil',
                'redirect' => route('checkout.summary', $order->id)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function checkoutSummary(Order $order): View
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('user.checkout.summary', compact('order'));
    }

    public function processPayment(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'payment_method' => 'required|in:pay_now,pay_later'
        ]);

        try {
            $order = $this->orderService->getOrderById($request->order_id);
            if ($request->payment_method === 'pay_now') {
                $paymentResult = $this->midtransService->createPayment($order);

                if ($paymentResult['success']) {
                    return response()->json([
                        'success' => true,
                        'payment_type' => 'midtrans',
                        'snap_token' => $paymentResult['snap_token']
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $paymentResult['error']
                    ], 400);
                }
            } else {
                
                return response()->json([
                    'success' => true,
                    'payment_type' => 'pay_later',
                    'redirect' => route('home') 
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function paymentFinish(Request $request): View
    {
        $user = Auth::user();
        $latestOrder = $user->orders()->with('latestTransaction')->latest()->first();
        
        return view('user.payment.finish', compact('latestOrder'));
    }
    public function paymentCallback(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'transaction_status' => 'required|string|in:settlement,pending,failure,cancel,expire,deny',
            'midtrans_result' => 'nullable|array'
        ]);

        try {
            $order = $this->orderService->getOrderById($request->order_id);
            
            $transaction = $order->transaction;
            
            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            
            $transactionStatus = $this->mapCallbackStatus($request->transaction_status);
            
            
            $transaction->update([
                'status' => $transactionStatus,
                'midtrans_response' => $request->midtrans_result,
                'settlement_time' => $request->transaction_status === 'settlement' ? now() : null,
            ]);

            
            $orderStatus = $this->mapTransactionToOrderStatus($request->transaction_status);
            $this->orderService->updateOrderStatus($order->id, $orderStatus);

            return response()->json([
                'success' => true,
                'message' => 'Transaction status updated successfully',
                'transaction_status' => $transactionStatus,
                'order_status' => $orderStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    
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
