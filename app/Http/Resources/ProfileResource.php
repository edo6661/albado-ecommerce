<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'role' => $this->role,
            'has_password' => $this->hasPassword(),
            'has_social_login' => $this->hasSocialLogin(),
            'social_provider' => $this->getSocialProvider(),
            'profile' => $this->whenLoaded('profile', function () {
                return [
                    'avatar' => $this->profile?->avatar,
                    'avatar_url' => $this->profile?->avatar_url,
                ];
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}