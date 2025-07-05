<?php

namespace App\Contracts\Services;

use App\Models\Order;
use App\Models\Transaction;

interface MidtransServiceInterface
{
    public function createPayment(Order $order): array;
    
    public function handleNotification(array $notification): Transaction;
    
    public function checkTransactionStatus(string $orderId): array;
    
    public function cancelPayment(Transaction $transaction): bool;
    
    public function refundPayment(Transaction $transaction, ?float $amount = null): bool;
    
    public function updateTransactionFromMidtrans(Transaction $transaction, array $midtransData): Transaction;
    public function resumePayment(Transaction $transaction): array;
}