<?php

namespace App\Repositories;

use App\Contracts\Repositories\AddressRepositoryInterface;
use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AddressRepository implements AddressRepositoryInterface
{
    public function __construct(
        protected Address $model
    ) {}

    public function findByUserId(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findById(int $id): ?Address
    {
        return $this->model->find($id);
    }

    public function create(array $data): Address
    {
        return $this->model->create($data);
    }

    public function update(Address $address, array $data): bool
    {
        return $address->update($data);
    }

    public function delete(Address $address): bool
    {
        return $address->delete();
    }

    public function setAsDefault(Address $address): bool
    {
        $this->model->where('user_id', $address->user_id)
            ->update(['is_default' => false]);

        return $address->update(['is_default' => true]);
    }
}