<?php

namespace App\Repositories;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(protected Order $model) {}

    public function findById(int $id): ?Order
    {
        return $this->model->with(['user', 'items.product', 'transaction'])->find($id);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->model->with(['user', 'items.product', 'transaction'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['user', 'items.product', 'transaction'])
            ->orderBy('created_at', 'desc');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('order_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('user', function ($userQuery) use ($filters) {
                      $userQuery->where('name', 'like', '%' . $filters['search'] . '%')
                               ->orWhere('email', 'like', '%' . $filters['search'] . '%');
                  });
            });
        }

        return $query->paginate($perPage);
    }

    public function getByUserId(int $userId): Collection
    {
        return $this->model->with(['items.product', 'transaction'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->with(['user', 'items.product'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data): Order
    {
        return $this->model->create($data);
    }

    public function update(Order $order, array $data): bool
    {
        return $order->update($data);
    }

    public function delete(Order $order): bool
    {
        return $order->delete();
    }

    public function getRecentOrders(int $limit = 10): Collection
    {
        return $this->model->with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTotalOrdersByStatus(): array
    {
        $result = [];
        foreach (OrderStatus::cases() as $status) {
            $result[$status->value] = $this->model->where('status', $status->value)->count();
        }
        return $result;
    }

    public function getOrdersByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->with(['user', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function getFilteredOrders(array $filters = []): Collection
    {
        $query = $this->model->with(['user', 'items.product', 'transaction'])
            ->orderBy('created_at', 'desc');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('order_number', 'like', '%' . $filters['search'] . '%')
                ->orWhereHas('user', function ($userQuery) use ($filters) {
                    $userQuery->where('name', 'like', '%' . $filters['search'] . '%')
                            ->orWhere('email', 'like', '%' . $filters['search'] . '%');
                });
            });
        }

        return $query->get();
    }
    public function getUserOrdersPaginated(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('user_id', $userId)
            ->with(['items.product.images', 'transaction'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}