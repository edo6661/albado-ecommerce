<?php

namespace App\Contracts\Repositories;

use App\Models\Profile;
use App\Models\User;

interface ProfileRepositoryInterface
{
    public function findByUserId(int $userId): ?Profile;
    public function create(User $user, array $data): Profile;
    public function update(Profile $profile, array $data): bool;
    public function delete(Profile $profile): bool;
}
