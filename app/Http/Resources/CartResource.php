<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'quantity' => $this->quantity,
            'price' => (float) $this->price,
            'total_price' => (float) $this->total_price,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}

