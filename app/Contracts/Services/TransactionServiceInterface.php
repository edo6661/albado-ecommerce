<?php

namespace App\Contracts\Services;

use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TransactionServiceInterface
{
    public function getTransactionById(int $id): Transaction;
    
    public function getTransactionByTransactionId(string $transactionId): Transaction;
    
    public function getPaginatedTransactions(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    public function getTransactionsByStatus(string $status): Collection;
    
    public function getTransactionsByOrderId(int $orderId): Collection;
    
    public function updateTransactionStatus(int $transactionId, string $status): Transaction;
    
    public function updateTransaction(int $transactionId, array $data): Transaction;
    
    public function deleteTransaction(int $transactionId): bool;
    
    public function getTransactionStatistics(): array;
    
    public function getRecentTransactions(int $limit = 10): Collection;
    
    public function getTransactionsByPaymentType(string $paymentType): Collection;
    
    public function getTransactionsByDateRange(string $startDate, string $endDate): Collection;
    public function getFilteredTransactions(array $filters = []): Collection;
    public function resumePayment(int $transactionId): array;
}