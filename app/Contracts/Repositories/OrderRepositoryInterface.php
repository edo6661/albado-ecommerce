<?php

namespace App\Contracts\Repositories;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function findById(int $id): ?Order;
    
    public function findByOrderNumber(string $orderNumber): ?Order;
    
    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    public function getByUserId(int $userId): Collection;
    
    public function getByStatus(string $status): Collection;
    
    public function create(array $data): Order;
    
    public function update(Order $order, array $data): bool;
    
    public function delete(Order $order): bool;
    
    public function getRecentOrders(int $limit = 10): Collection;
    
    public function getTotalOrdersByStatus(): array;
    
    public function getOrdersByDateRange(string $startDate, string $endDate): Collection;
}