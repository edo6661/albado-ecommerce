<?php

namespace App\Repositories;

use App\Contracts\Repositories\TransactionRepositoryInterface;
use App\Models\Transaction;
use App\Enums\TransactionStatus;
use App\Enums\PaymentType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(protected Transaction $model) {}
    public function create(array $data): Transaction
    {
        return $this->model->create($data);
    }

    public function update(Transaction $transaction, array $data): bool
    {
        return $transaction->update($data);
    }

    public function delete(Transaction $transaction): bool
    {
        return $transaction->delete();
    }
    public function findById(int $id): ?Transaction
    {
        return $this->model->with(['order.user', 'order.items.product'])->find($id);
    }

    public function findByTransactionId(string $transactionId): ?Transaction
    {
        return $this->model->with(['order.user', 'order.items.product'])
            ->where('transaction_id', $transactionId)
            ->first();
    }

    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['order.user', 'order.items.product'])
            ->orderBy('created_at', 'desc');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['payment_type'])) {
            $query->where('payment_type', $filters['payment_type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['order_id'])) {
            $query->where('order_id', $filters['order_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('transaction_id', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('order_id_midtrans', 'like', '%' . $filters['search'] . '%')
                  ->orWhereHas('order', function ($orderQuery) use ($filters) {
                      $orderQuery->where('order_number', 'like', '%' . $filters['search'] . '%')
                               ->orWhereHas('user', function ($userQuery) use ($filters) {
                                   $userQuery->where('name', 'like', '%' . $filters['search'] . '%')
                                            ->orWhere('email', 'like', '%' . $filters['search'] . '%');
                               });
                  });
            });
        }

        return $query->paginate($perPage);
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->with(['order.user', 'order.items.product'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByOrderId(int $orderId): Collection
    {
        return $this->model->with(['order.user'])
            ->where('order_id', $orderId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    

    public function getRecentTransactions(int $limit = 10): Collection
    {
        return $this->model->with(['order.user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTotalTransactionsByStatus(): array
    {
        $result = [];
        foreach (TransactionStatus::cases() as $status) {
            $result[$status->value] = $this->model->where('status', $status->value)->count();
        }
        return $result;
    }

    public function getByPaymentType(string $paymentType): Collection
    {
        return $this->model->with(['order.user', 'order.items.product'])
            ->where('payment_type', $paymentType)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTransactionsByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->with(['order.user', 'order.items.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTotalRevenue(): float
    {
        return $this->model->where('status', TransactionStatus::SETTLEMENT->value)
            ->sum('gross_amount');
    }

   public function getMonthlyRevenue(): array
    {
        $databaseDriver = $this->model->getConnection()->getDriverName();

        if ($databaseDriver === 'sqlite') {
            return $this->model->select(
                    DB::raw("strftime('%Y', created_at) as year"),
                    DB::raw("strftime('%m', created_at) as month"),
                    DB::raw('SUM(gross_amount) as total')
                )
                ->where('status', TransactionStatus::SETTLEMENT->value)
                ->whereYear('created_at', now()->year)
                ->groupBy('year', 'month')
                ->orderBy('month')
                ->get()
                ->toArray();
        } else {
            return $this->model->select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(gross_amount) as total')
                )
                ->where('status', TransactionStatus::SETTLEMENT->value)
                ->whereYear('created_at', now()->year)
                ->groupBy('year', 'month')
                ->orderBy('month')
                ->get()
                ->toArray();
        }
    }
    public function getFilteredTransactions(array $filters = []): Collection
    {
        $query = $this->model->with(['order.user', 'order.items.product'])
            ->orderBy('created_at', 'desc');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['payment_type'])) {
            $query->where('payment_type', $filters['payment_type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['order_id'])) {
            $query->where('order_id', $filters['order_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('transaction_id', 'like', '%' . $filters['search'] . '%')
                ->orWhere('order_id_midtrans', 'like', '%' . $filters['search'] . '%')
                ->orWhereHas('order', function ($orderQuery) use ($filters) {
                    $orderQuery->where('order_number', 'like', '%' . $filters['search'] . '%')
                            ->orWhereHas('user', function ($userQuery) use ($filters) {
                                $userQuery->where('name', 'like', '%' . $filters['search'] . '%')
                                            ->orWhere('email', 'like', '%' . $filters['search'] . '%');
                            });
                });
            });
        }

        return $query->get();
    }
    public function findByOrderIdMidtrans(string $orderId): ?Transaction
    {
        return $this->model->where('order_id_midtrans', $orderId)->first();
    }
}