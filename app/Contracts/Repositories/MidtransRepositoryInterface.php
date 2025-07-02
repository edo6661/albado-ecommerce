<?php

namespace App\Contracts\Repositories;

use App\Models\Transaction;

interface MidtransRepositoryInterface
{
    public function createPaymentToken(array $transactionDetails, array $customerDetails, array $itemDetails = []): array;
    
    public function getTransactionStatus(string $orderId): array;
    
    public function cancelTransaction(string $orderId): array;
    
    public function captureTransaction(string $orderId): array;
    
    public function approveTransaction(string $orderId): array;
    
    public function denyTransaction(string $orderId): array;
    
    public function expireTransaction(string $orderId): array;
    
    public function refundTransaction(string $orderId, array $refundData = []): array;
}