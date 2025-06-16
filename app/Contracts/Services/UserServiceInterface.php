<?php

namespace App\Contracts\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserServiceInterface
{
    public function getUserById(int $id): ?User;
    public function getUserByEmail(string $email): ?User;
    public function createUser(array $data): User;
    public function updateUser(int $id, array $data): ?User;
    public function deleteUser(int $id): bool;
    public function getAllUsers(): Collection;
    public function getPaginatedUsers(int $perPage = 15): LengthAwarePaginator;
}
