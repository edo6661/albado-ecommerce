<?php

namespace App\Contracts\Repositories;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    public function findById(int $id): ?Transaction;
    
    public function findByTransactionId(string $transactionId): ?Transaction;
    
    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    public function getByStatus(string $status): Collection;
    
    public function getByOrderId(int $orderId): Collection;
    
    public function create(array $data): Transaction;
    
    public function update(Transaction $transaction, array $data): bool;
    
    public function delete(Transaction $transaction): bool;
    
    public function getRecentTransactions(int $limit = 10): Collection;
    
    public function getTotalTransactionsByStatus(): array;
    
    public function getByPaymentType(string $paymentType): Collection;
    
    public function getTransactionsByDateRange(string $startDate, string $endDate): Collection;
    
    public function getTotalRevenue(): float;
    
    public function getMonthlyRevenue(): array;
    public function getFilteredTransactions(array $filters = []): Collection;
}