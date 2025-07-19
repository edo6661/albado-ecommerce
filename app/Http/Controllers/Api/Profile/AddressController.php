<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Contracts\Services\AddressServiceInterface;
use App\Http\Requests\Api\StoreAddressApiRequest;
use App\Http\Requests\Api\UpdateAddressApiRequest;
use App\Http\Resources\AddressDetailResource;
use App\Http\Resources\AddressResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(
        protected AddressServiceInterface $addressService
    ) {}

    /**
     * Display a listing of authenticated user's addresses
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $addresses = $this->addressService->getUserAddresses($request->user()->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil diambil',
                'data' => AddressResource::collection($addresses)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data alamat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created address
     *
     * @param StoreAddressApiRequest $request
     * @return JsonResponse
     */
    public function store(StoreAddressApiRequest $request): JsonResponse
    {
        try {
            $address = $this->addressService->createAddress(
                $request->user()->id,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil ditambahkan',
                'data' => new AddressDetailResource($address)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan alamat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified address
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $address = $this->addressService->getAddressById($id);
            
            if (!$address || $address->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail alamat berhasil diambil',
                'data' => new AddressDetailResource($address)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail alamat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified address
     *
     * @param UpdateAddressApiRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateAddressApiRequest $request, int $id): JsonResponse
    {
        try {
            $existingAddress = $this->addressService->getAddressById($id);
            
            if (!$existingAddress || $existingAddress->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            $address = $this->addressService->updateAddress($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil diperbarui',
                'data' => new AddressDetailResource($address)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui alamat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified address
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $address = $this->addressService->getAddressById($id);
            
            if (!$address || $address->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            $deleted = $this->addressService->deleteAddress($id);

            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus alamat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set address as default
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function setDefault(Request $request, int $id): JsonResponse
    {
        try {
            $address = $this->addressService->getAddressById($id);
            
            if (!$address || $address->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            $updated = $this->addressService->setDefaultAddress($id);

            return response()->json([
                'success' => true,
                'message' => 'Alamat default berhasil diperbarui',
                'data' => new AddressDetailResource($address->fresh())
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengatur alamat default',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}