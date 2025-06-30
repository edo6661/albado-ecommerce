<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Exceptions\UserNotFoundException;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function createUser(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->create($data);
    }

    public function updateUser(int $id, array $data): ?User
    {
        $user = $this->getUserById($id);
        
        if (!$user) {
            throw new UserNotFoundException("User with ID {$id} not found");
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $this->userRepository->update($user, $data);
        
        return $user->fresh();
    }

    public function deleteUser(int $id): bool
    {
        $user = $this->getUserById($id);
        
        if (!$user) {
            throw new UserNotFoundException("User with ID {$id} not found");
        }

        return $this->userRepository->delete($user);
    }

    public function getAllUsers(): Collection
    {
        return $this->userRepository->getAll();
    }

    public function getPaginatedUsers(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->getAllPaginated($perPage);
    }
    public function getUserStatistics(): array
    {
        return $this->userRepository->getUserStatistics();
    }

    public function getRecentUsers(int $limit = 10): Collection
    {
        return $this->userRepository->getRecentUsers($limit);
    }
}


