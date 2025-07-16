<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Services\RatingServiceInterface;
use App\Contracts\Services\TransactionServiceInterface;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    public function __construct(
        protected OrderServiceInterface $orderService,
        protected TransactionServiceInterface $transactionService,
        protected RatingServiceInterface $ratingService
    ) {}

    public function index()
    {
        $orders = $this->orderService->getUserOrdersPaginated(Auth::id());
        
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
        
        return view('user.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('user.orders.show', compact('order'));
    }

    public function resumePayment(Order $order)
    {
        
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $transaction = $order->latestTransaction;
        
        if (!$transaction || !$transaction->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak dapat dilanjutkan'
            ]);
        }

        try {
            $paymentData = $this->transactionService->resumePayment($transaction->id);
            
            return response()->json([
                'success' => true,
                'snap_token' => $paymentData['snap_token'],
                'redirect_url' => $paymentData['redirect_url']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}