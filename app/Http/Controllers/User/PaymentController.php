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

            if ($order->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

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
        return view('user.payment.finish');
    }

    public function paymentUnfinish(Request $request): View
    {
        return view('user.payment.unfinish');
    }

    public function paymentError(Request $request): View
    {
        return view('user.payment.error');
    }

}
