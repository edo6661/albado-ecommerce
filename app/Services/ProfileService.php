<?php


namespace App\Services;

use App\Contracts\Services\ProfileServiceInterface;
use App\Contracts\Repositories\ProfileRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Models\Profile;
use App\Models\User;
use App\Exceptions\UserNotFoundException;

class ProfileService implements ProfileServiceInterface
{
    public function __construct(
        protected ProfileRepositoryInterface $profileRepository,
        protected UserServiceInterface $userService
    ) {}

    public function getProfileByUserId(int $userId): ?Profile
    {
        return $this->profileRepository->findByUserId($userId);
    }

    public function createProfile(User $user, array $data): Profile
    {
        return $this->profileRepository->create($user, $data);
    }

    public function updateProfile(int $userId, array $data): ?Profile
    {
        $user = $this->userService->getUserById($userId);
        
        if (!$user) {
            throw new UserNotFoundException("User with ID {$userId} not found");
        }

        $profile = $this->getProfileByUserId($userId);
        
        if (!$profile) {
            return $this->createProfile($user, $data);
        }

        $this->profileRepository->update($profile, $data);
        
        return $profile->fresh();
    }

    public function deleteProfile(int $userId): bool
    {
        $profile = $this->getProfileByUserId($userId);
        
        if (!$profile) {
            return false;
        }

        return $this->profileRepository->delete($profile);
    }
}