<?php

namespace App\Http\Controllers;

use App\Contracts\Services\AddressServiceInterface;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function __construct(
        protected ShippingService $shippingService,
        protected AddressServiceInterface $addressService
    ) {}

    public function calculateShipping(Request $request): JsonResponse
    {
        $request->validate(['address_id' => 'required|exists:addresses,id,user_id,' . auth()->id()]);
        
        $address = $this->addressService->getAddressById($request->address_id);

        if (!$address || !$address->latitude || !$address->longitude) {
            return response()->json(['success' => false, 'message' => 'Alamat tidak valid.'], 400);
        }
        
        $shipping = $this->shippingService->calculate($address->latitude, $address->longitude);

        return response()->json($shipping);
    }
}