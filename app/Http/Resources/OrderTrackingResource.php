<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderTrackingResource extends JsonResource
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
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone ?? null
            ],
            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'product_price' => $item->product_price,
                    'quantity' => $item->quantity,
                    'total' => $item->total,
                    'product' => $item->product ? [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'slug' => $item->product->slug,
                        'images' => $item->product->images->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'path' => $image->path,
                                'url' => $image->url,
                                'is_primary' => $image->is_primary
                            ];
                        })
                    ] : null
                ];
            }),
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'shipping_cost' => $this->shipping_cost,
            'total' => $this->total,
            'total_quantity' => $this->total_quantity,
            'notes' => $this->notes,
            'shipping_address' => $this->shipping_address,
            'distance_km' => $this->distance_km,
            'payment_status' => $this->isPaid() ? 'Sudah Bayar' : 'Belum Bayar',
            'is_paid' => $this->isPaid(),
            'tracking_info' => $this->getTrackingInfo(),
            'estimated_delivery' => $this->getEstimatedDelivery(),
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

    private function getTrackingInfo(): array
    {
        $steps = [
            [
                'status' => 'pending',
                'label' => 'Pesanan Dibuat',
                'description' => 'Pesanan Anda telah dibuat dan menunggu pembayaran',
                'completed' => true,
                'active' => $this->status->value === 'pending',
                'timestamp' => $this->created_at->format('Y-m-d H:i:s')
            ],
            [
                'status' => 'processing',
                'label' => 'Sedang Diproses',
                'description' => 'Pesanan Anda sedang diproses dan disiapkan',
                'completed' => in_array($this->status->value, ['processing', 'shipped', 'delivered']),
                'active' => $this->status->value === 'processing',
                'timestamp' => $this->status->value !== 'pending' ? $this->updated_at->format('Y-m-d H:i:s') : null
            ],
            [
                'status' => 'shipped',
                'label' => 'Dikirim',
                'description' => 'Pesanan Anda telah dikirim dan dalam perjalanan',
                'completed' => in_array($this->status->value, ['shipped', 'delivered']),
                'active' => $this->status->value === 'shipped',
                'timestamp' => in_array($this->status->value, ['shipped', 'delivered']) ? $this->updated_at->format('Y-m-d H:i:s') : null
            ],
            [
                'status' => 'delivered',
                'label' => 'Diterima',
                'description' => 'Pesanan Anda telah berhasil diterima',
                'completed' => $this->status->value === 'delivered',
                'active' => $this->status->value === 'delivered',
                'timestamp' => $this->status->value === 'delivered' ? $this->updated_at->format('Y-m-d H:i:s') : null
            ]
        ];

        // If cancelled, show different flow
        if ($this->status->value === 'cancelled') {
            return [
                [
                    'status' => 'cancelled',
                    'label' => 'Pesanan Dibatalkan',
                    'description' => 'Pesanan Anda telah dibatalkan',
                    'completed' => true,
                    'active' => true,
                    'timestamp' => $this->updated_at->format('Y-m-d H:i:s')
                ]
            ];
        }

        return $steps;
    }

    private function getEstimatedDelivery(): ?string
    {
        if ($this->status->value === 'delivered') {
            return null; // Already delivered
        }

        if ($this->status->value === 'cancelled') {
            return null; // Cancelled orders don't have delivery estimates
        }

        // Calculate estimated delivery based on current status
        $estimatedDays = match($this->status->value) {
            'pending' => 5,
            'processing' => 3,
            'shipped' => 1,
            default => 5
        };

        return now()->addDays($estimatedDays)->format('Y-m-d');
    }
}