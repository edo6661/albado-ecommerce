<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Services\OrderServiceInterface;
use App\Http\Resources\OrderTrackingResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderTrackingController extends Controller
{
    public function __construct(protected OrderServiceInterface $orderService) {}

    /**
     * Show order tracking information
     *
     * @param int $orderId
     * @return JsonResponse
     */
    public function show(int $orderId): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($orderId);

            // Check authorization - user must own the order or be admin
            if (Auth::id() !== $order->user_id && !Auth::user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak diizinkan mengakses halaman ini'
                ], 403);
            }

            $storeLocation = [
                'lat' => (float) config('services.shipping.store_lat'),
                'lng' => (float) config('services.shipping.store_lng'),
            ];
            
            $userAddress = $order->user->defaultAddress;
            if (!$userAddress) {
                $userAddress = $order->user->addresses()->first();
            }

            return response()->json([
                'success' => true,
                'message' => 'Data tracking pesanan berhasil diambil',
                'data' => [
                    'order' => new OrderTrackingResource($order),
                    'store_location' => $storeLocation,
                    'user_address' => $userAddress ? [
                        'id' => $userAddress->id,
                        'label' => $userAddress->label,
                        'address' => $userAddress->address,
                        'city' => $userAddress->city,
                        'postal_code' => $userAddress->postal_code,
                        'latitude' => $userAddress->latitude,
                        'longitude' => $userAddress->longitude,
                        'is_default' => $userAddress->is_default
                    ] : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tracking pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}