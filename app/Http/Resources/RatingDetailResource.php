<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'review' => $this->review,
            'formatted_rating' => $this->formatted_rating,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->profile?->avatar_url,
            ],
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'slug' => $this->product->slug,
                'price' => (float) $this->product->price,
                'primary_image' => $this->product->images->first() ? 
                    new ProductImageResource($this->product->images->first()) : null,
            ],
            'images' => RatingImageResource::collection($this->whenLoaded('images')),
            'images_count' => $this->images ? $this->images->count() : 0,
            'can_edit' => $request->user() && $request->user()->id === $this->user_id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}