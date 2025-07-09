<?php

namespace App\Contracts\Services;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface AddressServiceInterface
{
    public function getUserAddresses(int $userId): Collection;
    public function getAddressById(int $id): ?Address;
    public function createAddress(int $userId, array $data): Address;
    public function updateAddress(int $id, array $data): ?Address;
    public function deleteAddress(int $id): bool;
    public function setDefaultAddress(int $id): bool;
}