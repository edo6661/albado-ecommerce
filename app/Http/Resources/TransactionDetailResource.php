<?php
// app/Http/Resources/TransactionDetailResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDetailResource extends JsonResource
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
                'status' => [
                    'value' => $this->order->status->value,
                    'label' => $this->order->status->label(),
                ],
                'user' => [
                    'id' => $this->order->user->id,
                    'name' => $this->order->user->name,
                    'email' => $this->order->user->email,
                ],
                'subtotal' => $this->order->subtotal,
                'tax' => $this->order->tax,
                'shipping_cost' => $this->order->shipping_cost,
                'total' => $this->order->total,
                'total_quantity' => $this->order->total_quantity,
                'items_count' => $this->order->items->count(),
                'shipping_address' => $this->order->shipping_address,
                'notes' => $this->order->notes,
                'created_at' => $this->order->created_at->format('Y-m-d H:i:s'),
            ],
            // 'payment_type' => [
            //     'value' => $this->payment_type->value,
            //     'label' => $this->payment_type->label(),
            // ],
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
            'snap_token' => $this->snap_token,
            'snap_url' => $this->snap_url,
            'midtrans_response' => $this->midtrans_response,
            'is_success' => $this->isSuccess(),
            'is_failed' => $this->isFailed(),
            'is_pending' => $this->isPending(),
            'order_items' => $this->when($this->order && $this->order->items, function () {
                return $this->order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product_name,
                        'product_price' => $item->product_price,
                        'quantity' => $item->quantity,
                        'total' => $item->total,
                        'product' => $item->product ? [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'slug' => $item->product->slug,
                            'image' => $item->product->images->first()?->image_url ?? null,
                        ] : null,
                    ];
                });
            }),
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