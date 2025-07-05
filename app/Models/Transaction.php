<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\TransactionStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'order_id_midtrans',
        'payment_type',
        'status',
        'gross_amount',
        'currency',
        'transaction_time',
        'settlement_time',
        'midtrans_response',
        'fraud_status',
        'status_message',
        'snap_token',
        'snap_url',
    
    ];

    protected $casts = [
        'payment_type' => PaymentType::class,
        'status' => TransactionStatus::class,
        'gross_amount' => 'decimal:2',
        'transaction_time' => 'datetime',
        'settlement_time' => 'datetime',
        'midtrans_response' => 'json',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function generateMidtransOrderId(): string
    {
        return 'TXN-' . now()->format('YmdHis') . '-' . uniqid();
    }

    public function isSuccess(): bool
    {
        return $this->status->isSuccess();
    }

    public function isFailed(): bool
    {
        return $this->status->isFailed();
    }

    public function isPending(): bool
    {
        return $this->status->isPending();
    }
}