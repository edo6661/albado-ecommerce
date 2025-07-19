<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total_items' => $this['total_items'],
            'total_quantity' => $this['total_quantity'],
            'total_price' => (float) $this['total_price'],
            'items' => CartResource::collection($this['items']),
        ];
    }
}
