<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'discount_price' => $this->discount_price ? (float) $this->discount_price : null,
            'final_price' => (float) ($this->discount_price ?? $this->price),
            'stock' => $this->stock,
            'is_active' => $this->is_active,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'primary_image' => $this->images->first() ? new ProductImageResource($this->images->first()) : null,
            'average_rating' => (float) $this->average_rating,
            'rating_count' => $this->rating_count,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}