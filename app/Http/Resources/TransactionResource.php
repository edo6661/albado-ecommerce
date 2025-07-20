
<?php
// app/Http/Resources/TransactionResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_id' => $this->transaction_id,
            'order_id_midtrans' => $this->order_id_midtrans,
            'order' => [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'user_name' => $this->order->user->name ?? null,
                'user_email' => $this->order->user->email ?? null,
            ],
            'payment_type' => [
                'value' => $this->payment_type->value,
                'label' => $this->payment_type->label(),
            ],
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'color' => $this->getStatusColor(),
            ],
            'gross_amount' => $this->gross_amount,
            'currency' => $this->currency,
            'transaction_time' => $this->transaction_time?->format('Y-m-d H:i:s'),
            'settlement_time' => $this->settlement_time?->format('Y-m-d H:i:s'),
            'fraud_status' => $this->fraud_status,
            'status_message' => $this->status_message,
            'is_success' => $this->isSuccess(),
            'is_failed' => $this->isFailed(),
            'is_pending' => $this->isPending(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    private function getStatusColor(): string
    {
        return match($this->status->value) {
            'pending' => '#f59e0b',
            'settlement' => '#10b981',
            'deny' => '#ef4444',
            'cancel' => '#6b7280',
            'expire' => '#f97316',
            'failure' => '#dc2626',
            'refund' => '#8b5cf6',
            'partial_refund' => '#a855f7',
            default => '#6b7280'
        };
    }
}