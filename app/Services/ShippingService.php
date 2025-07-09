<?php

namespace App\Services;

use App\Contracts\Services\ShippingServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingService implements ShippingServiceInterface
{
    protected string $apiKey;
    protected string $storeLat;
    protected string $storeLng;
    protected float $costPerKm;

    public function __construct()
    {
        $this->apiKey = config('services.google.backend_maps_api_key');
        $this->storeLat = config('services.shipping.store_lat');
        $this->storeLng = config('services.shipping.store_lng');
        $this->costPerKm = (float) config('services.shipping.cost_per_km');
    }

    public function calculate(float $userLat, float $userLng): array
    {
        if (empty($this->apiKey) || empty($this->storeLat) || empty($this->storeLng)) {
            return ['success' => false, 'message' => 'Konfigurasi pengiriman tidak lengkap.'];
        }

        $url = "https://maps.googleapis.com/maps/api/distancematrix/json";

        try {
            $response = Http::get($url, [
                'origins' => "{$this->storeLat},{$this->storeLng}",
                'destinations' => "{$userLat},{$userLng}",
                'key' => $this->apiKey,
                'units' => 'metric',
            ]);

            $data = $response->json();

            if ($response->successful() && $data['status'] === 'OK' && isset($data['rows'][0]['elements'][0]['distance'])) {
                $distanceInMeters = $data['rows'][0]['elements'][0]['distance']['value'];
                $distanceInKm = $distanceInMeters / 1000;
                $cost = $distanceInKm * $this->costPerKm;

                return [
                    'success' => true,
                    'distance_km' => round($distanceInKm, 2),
                    'cost' => round($cost),
                    'formatted_cost' => 'Rp ' . number_format(round($cost), 0, ',', '.'),
                ];
            }
            
            Log::error('Google Maps API Error', ['response' => $data]);
            return ['success' => false, 'message' => $data['error_message'] ?? 'Gagal menghitung jarak.'];

        } catch (\Exception $e) {
            Log::error('ShippingService Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Terjadi kesalahan pada server saat menghitung ongkir.'];
        }
    }
}