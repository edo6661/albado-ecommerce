<?php

namespace App\Http\Controllers\Profile;

use App\Contracts\Services\AddressServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function __construct(
        protected AddressServiceInterface $addressService
    ) {}

    public function index(Request $request): View
    {
        $addresses = $this->addressService->getUserAddresses($request->user()->id);
        
        return view('profile.addresses.index', compact('addresses'));
    }

    public function create(): View
    {
        return view('profile.addresses.create');
    }

    public function store(StoreAddressRequest $request): RedirectResponse
    {
        try {
            $this->addressService->createAddress(
                $request->user()->id,
                $request->validated()
            );

            return redirect()
                ->route('profile.addresses.index')
                ->with('success', 'Alamat berhasil ditambahkan');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan alamat: ' . $e->getMessage());
        }
    }

    public function edit(int $id): View
    {
        $address = $this->addressService->getAddressById($id);
        
        if (!$address) {
            abort(404);
        }

        return view('profile.addresses.edit', compact('address'));
    }

    public function update(UpdateAddressRequest $request, int $id): RedirectResponse
    {
        try {
            $address = $this->addressService->updateAddress($id, $request->validated());
            
            if (!$address) {
                return redirect()
                    ->route('profile.addresses.index')
                    ->with('error', 'Alamat tidak ditemukan');
            }

            return redirect()
                ->route('profile.addresses.index')
                ->with('success', 'Alamat berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui alamat: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $deleted = $this->addressService->deleteAddress($id);
            
            if (!$deleted) {
                return redirect()
                    ->route('profile.addresses.index')
                    ->with('error', 'Alamat tidak ditemukan');
            }

            return redirect()
                ->route('profile.addresses.index')
                ->with('success', 'Alamat berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus alamat: ' . $e->getMessage());
        }
    }

    public function setDefault(int $id): RedirectResponse
    {
        try {
            $updated = $this->addressService->setDefaultAddress($id);
            
            if (!$updated) {
                return redirect()
                    ->route('profile.addresses.index')
                    ->with('error', 'Alamat tidak ditemukan');
            }

            return redirect()
                ->route('profile.addresses.index')
                ->with('success', 'Alamat default berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat mengatur alamat default: ' . $e->getMessage());
        }
    }
}