<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProfileRepositoryInterface;
use App\Models\Profile;
use App\Models\User;

class ProfileRepository implements ProfileRepositoryInterface
{
    public function __construct(
        protected Profile $model
    ) {}

    public function findByUserId(int $userId): ?Profile
    {
        return $this->model->where('user_id', $userId)->first();
    }

    public function create(User $user, array $data): Profile
    {
        $data['user_id'] = $user->id;
        return $this->model->create($data);
    }

    public function update(Profile $profile, array $data): bool
    {
        return $profile->update($data);
    }

    public function delete(Profile $profile): bool
    {
        return $profile->delete();
    }
}