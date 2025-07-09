<?php

namespace App\Http\Controllers;

use App\Contracts\Services\OrderServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    public function __construct(protected OrderServiceInterface $orderService) {}

    public function show(int $orderId): View
    {
        $order = $this->orderService->getOrderById($orderId);

        if (auth()->id() !== $order->user_id && !auth()->user()->isAdmin()) {
            abort(403, 'Anda tidak diizinkan mengakses halaman ini.');
        }

        $storeLocation = [
            'lat' => (float) config('services.shipping.store_lat'),
            'lng' => (float) config('services.shipping.store_lng'),
        ];
        
        $userAddress = $order->user->defaultAddress;
        if (!$userAddress) {
            $userAddress = $order->user->addresses()->first();
        }

        return view('user.orders.tracking', compact('order', 'storeLocation', 'userAddress'));
    }
}