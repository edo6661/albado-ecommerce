<?php

namespace App\Contracts\Repositories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Collection;

interface AddressRepositoryInterface
{
    public function findByUserId(int $userId): Collection;
    public function findById(int $id): ?Address;
    public function create(array $data): Address;
    public function update(Address $address, array $data): bool;
    public function delete(Address $address): bool;
    public function setAsDefault(Address $address): bool;
    public function unsetAsDefault(Address $address): bool;
}