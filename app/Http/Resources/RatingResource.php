<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'review' => $this->review,
            'user_name' => $this->user->name,
            'images' => RatingImageResource::collection($this->whenLoaded('images')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}