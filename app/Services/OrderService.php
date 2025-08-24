<?php

namespace App\Services;

use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Enums\OrderStatus;
use App\Exceptions\OrderNotFoundException;
use App\Exceptions\OrderCannotBeCancelledException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository
    ) {}

    public function getOrderById(int $id): Order
    {
        $order = $this->orderRepository->findById($id);
        
        if (!$order) {
            throw new OrderNotFoundException("Order dengan ID {$id} tidak ditemukan.");
        }
        
        return $order;
    }

    public function getOrderByNumber(string $orderNumber): Order
    {
        $order = $this->orderRepository->findByOrderNumber($orderNumber);
        
        if (!$order) {
            throw new OrderNotFoundException("Order dengan nomor {$orderNumber} tidak ditemukan.");
        }
        
        return $order;
    }

    public function getPaginatedOrders(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->orderRepository->getAllPaginated($perPage, $filters);
    }

    public function getUserOrders(int $userId): Collection
    {
        return $this->orderRepository->getByUserId($userId);
    }

    public function getOrdersByStatus(string $status): Collection
    {
        return $this->orderRepository->getByStatus($status);
    }

    public function createOrder(array $orderData, array $items): Order
    {
        try {
            DB::beginTransaction();

            if (empty($items)) {
                throw new \InvalidArgumentException('Order harus memiliki minimal 1 item.');
            }

            
            $totals = $this->calculateOrderTotal($items, $orderData['tax_rate'] ?? 0);
            
            
            $shippingCost = $orderData['shipping_cost'] ?? 0;
            $shippingAddress = $orderData['shipping_address'] ?? null;
            $distanceKm = $orderData['distance_km'] ?? null;
            
            
            $finalTotal = $totals['total'] + $shippingCost;

            $orderData = array_merge($orderData, [
                'order_number' => Order::generateOrderNumber(),
                'status' => OrderStatus::PENDING,
                'subtotal' => $totals['subtotal'],
                'tax' => $totals['tax'],
                'total' => $finalTotal, 
                'shipping_cost' => $shippingCost,
                'shipping_address' => $shippingAddress,
                'address_id' => $orderData['address_id'] ?? null,
                'distance_km' => $distanceKm,
            ]);

            $order = $this->orderRepository->create($orderData);

            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new \InvalidArgumentException("Produk dengan ID {$item['product_id']} tidak ditemukan.");
                }
            
                
                $price = $product->discount_price ?? $product->price;
            
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $price,  
                    'quantity' => $item['quantity'],
                    'total' => $price * $item['quantity'],  
                ]);
            }

            DB::commit();

            return $this->orderRepository->findById($order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating order: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateOrderStatus(int $orderId, string $status): Order
    {
        $order = $this->getOrderById($orderId);
        
        if (!in_array($status, OrderStatus::values())) {
            throw new \InvalidArgumentException("Status '{$status}' tidak valid.");
        }

        $this->orderRepository->update($order, ['status' => $status]);
        
        return $this->getOrderById($orderId);
    }

    public function updateOrder(int $orderId, array $data): Order
    {
        $order = $this->getOrderById($orderId);
        
        $this->orderRepository->update($order, $data);
        
        return $this->getOrderById($orderId);
    }

    public function cancelOrder(int $orderId): Order
    {
        $order = $this->getOrderById($orderId);
        
        if (!$order->canBeCancelled()) {
            throw new OrderCannotBeCancelledException("Order dengan status '{$order->status->label()}' tidak dapat dibatalkan.");
        }

        $this->orderRepository->update($order, ['status' => OrderStatus::CANCELLED]);
        
        return $this->getOrderById($orderId);
    }

    public function deleteOrder(int $orderId): bool
    {
        $order = $this->getOrderById($orderId);
        
        return $this->orderRepository->delete($order);
    }

    public function getOrderStatistics(): array
    {
        $statusCounts = $this->orderRepository->getTotalOrdersByStatus();
        $totalOrders = array_sum($statusCounts);
        
        return [
            'total_orders' => $totalOrders,
            'status_counts' => $statusCounts,
            'recent_orders' => $this->getRecentOrders(5),
        ];
    }

    public function getRecentOrders(int $limit = 10): Collection
    {
        return $this->orderRepository->getRecentOrders($limit);
    }

    public function calculateOrderTotal(array $items, float $taxRate = 0): array
    {
        $subtotal = 0;
        
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $price = $product->discount_price ?? $product->price;
                $subtotal += $price * $item['quantity'];
            }
        }
        
        $tax = $subtotal * ($taxRate / 100);
        $total = $subtotal + $tax;
        
        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ];
    }
    public function getFilteredOrders(array $filters = []): Collection
    {
        return $this->orderRepository->getFilteredOrders($filters);
    }
    public function getUserOrdersPaginated(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->getUserOrdersPaginated($userId, $perPage);
    }
    public function getUserOrdersCursorPaginated(int $userId, int $perPage = 15, ?int $cursor = null): array
    {
        $orders = $this->orderRepository->getUserOrdersCursorPaginated($userId, $perPage, $cursor);

        $hasNextPage = $orders->count() > $perPage;

        if ($hasNextPage) {
            $orders->pop(); 
        }

        $nextCursor = $hasNextPage && $orders->isNotEmpty() ? $orders->last()->id : null;

        return [
            'data' => $orders,
            'has_next_page' => $hasNextPage,
            'next_cursor' => $nextCursor,
            'per_page' => $perPage,
        ];
    }
}