
<?php
// app/Http/Resources/OrderDetailResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'color' => $this->getStatusColor()
            ],
            'user' => new UserResource($this->whenLoaded('user')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'transaction' => new TransactionResource($this->whenLoaded('transaction')),
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'shipping_cost' => $this->shipping_cost,
            'total' => $this->total,
            'total_quantity' => $this->total_quantity,
            'items_count' => $this->items->count(),
            'notes' => $this->notes,
            'shipping_address' => $this->shipping_address,
            'distance_km' => $this->distance_km,
            'payment_status' => $this->isPaid() ? 'Sudah Bayar' : 'Belum Bayar',
            'is_paid' => $this->isPaid(),
            'can_be_cancelled' => $this->canBeCancelled(),
            'snap_token' => $this->getSnapToken(),
            'snap_url' => $this->getSnapUrl(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    private function getStatusColor(): string
    {
        return match($this->status->value) {
            'pending' => '#f59e0b',
            'processing' => '#3b82f6',
            'shipped' => '#8b5cf6',
            'delivered' => '#10b981',
            'cancelled' => '#ef4444',
            default => '#6b7280'
        };
    }
}