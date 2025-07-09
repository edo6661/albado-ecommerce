<?php

namespace App\Contracts\Services;

interface ShippingServiceInterface
{
    public function calculate(float $userLat, float $userLng): array;
    
}
