<?php

namespace App\Contracts\Services;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface OrderServiceInterface
{
    public function getOrderById(int $id): Order;
    
    public function getOrderByNumber(string $orderNumber): Order;
    
    public function getPaginatedOrders(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    public function getUserOrders(int $userId): Collection;
    
    public function getOrdersByStatus(string $status): Collection;
    
    public function createOrder(array $orderData, array $items): Order;
    
    public function updateOrderStatus(int $orderId, string $status): Order;
    
    public function updateOrder(int $orderId, array $data): Order;
    
    public function cancelOrder(int $orderId): Order;
    
    public function deleteOrder(int $orderId): bool;
    
    public function getOrderStatistics(): array;
    
    public function getRecentOrders(int $limit = 10): Collection;
    
    public function calculateOrderTotal(array $items, float $taxRate = 0): array;
    public function getFilteredOrders(array $filters = []): Collection;
    public function getUserOrdersPaginated(int $userId, int $perPage = 15): LengthAwarePaginator;   
    public function getUserOrdersCursorPaginated(int $userId, int $perPage = 15, ?int $cursor = null): array;
}