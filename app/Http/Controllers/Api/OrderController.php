<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Services\RatingServiceInterface;
use App\Contracts\Services\TransactionServiceInterface;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderDetailResource;
use App\Http\Requests\Api\ResumePaymentRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        protected OrderServiceInterface $orderService,
        protected TransactionServiceInterface $transactionService,
        protected RatingServiceInterface $ratingService
    ) {}

    /**
     * Display a listing of user orders
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $orders = $this->orderService->getUserOrdersPaginated(Auth::id());
            
            // Add rating information for each order item
            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    $userRating = $this->ratingService->getUserRatingForProduct(
                        Auth::id(), 
                        $item->product_id
                    );
                    
                    $item->user_has_rated = $userRating !== null;
                    $item->user_rating = $userRating; 
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Daftar pesanan berhasil diambil',
                'data' => [
                    'orders' => OrderResource::collection($orders->items()),
                    'pagination' => [
                        'current_page' => $orders->currentPage(),
                        'per_page' => $orders->perPage(),
                        'total' => $orders->total(),
                        'last_page' => $orders->lastPage(),
                        'from' => $orders->firstItem(),
                        'to' => $orders->lastItem(),
                        'has_more_pages' => $orders->hasMorePages(),
                        'prev_page_url' => $orders->previousPageUrl(),
                        'next_page_url' => $orders->nextPageUrl()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified order
     *
     * @param Order $order
     * @return JsonResponse
     */
    public function show(Order $order): JsonResponse
    {
        try {
            $order->load(['user', 'items.product.images', 'transaction']);

            return response()->json([
                'success' => true,
                'message' => 'Detail pesanan berhasil diambil',
                'data' => new OrderDetailResource($order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resume payment for pending order
     *
     * @param ResumePaymentRequest $request
     * @param Order $order
     * @return JsonResponse
     */
    public function resumePayment(ResumePaymentRequest $request, Order $order): JsonResponse
    {
        try {
            $transaction = $order->latestTransaction;
            
            if (!$transaction || !$transaction->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak dapat dilanjutkan'
                ], 400);
            }

            $paymentData = $this->transactionService->resumePayment($transaction->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dilanjutkan',
                'data' => [
                    'snap_token' => $paymentData['snap_token'],
                    'redirect_url' => $paymentData['redirect_url'],
                    'order_id' => $order->id,
                    'transaction_id' => $transaction->id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}