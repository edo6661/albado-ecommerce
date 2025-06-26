<?php

namespace App\Services;

use App\Contracts\Services\TransactionServiceInterface;
use App\Contracts\Repositories\TransactionRepositoryInterface;
use App\Models\Transaction;
use App\Enums\TransactionStatus;
use App\Exceptions\TransactionNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionService implements TransactionServiceInterface
{
    public function __construct(
        protected TransactionRepositoryInterface $transactionRepository
    ) {}

    public function getTransactionById(int $id): Transaction
    {
        $transaction = $this->transactionRepository->findById($id);
        
        if (!$transaction) {
            throw new TransactionNotFoundException("Transaksi dengan ID {$id} tidak ditemukan.");
        }
        
        return $transaction;
    }

    public function getTransactionByTransactionId(string $transactionId): Transaction
    {
        $transaction = $this->transactionRepository->findByTransactionId($transactionId);
        
        if (!$transaction) {
            throw new TransactionNotFoundException("Transaksi dengan ID {$transactionId} tidak ditemukan.");
        }
        
        return $transaction;
    }

    public function getPaginatedTransactions(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->transactionRepository->getAllPaginated($perPage, $filters);
    }

    public function getTransactionsByStatus(string $status): Collection
    {
        return $this->transactionRepository->getByStatus($status);
    }

    public function getTransactionsByOrderId(int $orderId): Collection
    {
        return $this->transactionRepository->getByOrderId($orderId);
    }

    public function updateTransactionStatus(int $transactionId, string $status): Transaction
    {
        $transaction = $this->getTransactionById($transactionId);
        
        if (!in_array($status, TransactionStatus::values())) {
            throw new \InvalidArgumentException("Status '{$status}' tidak valid.");
        }

        $this->transactionRepository->update($transaction, ['status' => $status]);
        
        return $this->getTransactionById($transactionId);
    }

    public function updateTransaction(int $transactionId, array $data): Transaction
    {
        $transaction = $this->getTransactionById($transactionId);
        
        $this->transactionRepository->update($transaction, $data);
        
        return $this->getTransactionById($transactionId);
    }

    public function deleteTransaction(int $transactionId): bool
    {
        $transaction = $this->getTransactionById($transactionId);
        
        return $this->transactionRepository->delete($transaction);
    }

    public function getTransactionStatistics(): array
    {
        $statusCounts = $this->transactionRepository->getTotalTransactionsByStatus();
        $totalTransactions = array_sum($statusCounts);
        $totalRevenue = $this->transactionRepository->getTotalRevenue();
        $monthlyRevenue = $this->transactionRepository->getMonthlyRevenue();
        
        return [
            'total_transactions' => $totalTransactions,
            'total_revenue' => $totalRevenue,
            'status_counts' => $statusCounts,
            'monthly_revenue' => $monthlyRevenue,
            'recent_transactions' => $this->getRecentTransactions(5),
        ];
    }

    public function getRecentTransactions(int $limit = 10): Collection
    {
        return $this->transactionRepository->getRecentTransactions($limit);
    }

    public function getTransactionsByPaymentType(string $paymentType): Collection
    {
        return $this->transactionRepository->getByPaymentType($paymentType);
    }

    public function getTransactionsByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->transactionRepository->getTransactionsByDateRange($startDate, $endDate);
    }
}