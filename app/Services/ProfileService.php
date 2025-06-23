<?php


namespace App\Services;

use App\Contracts\Services\ProfileServiceInterface;
use App\Contracts\Repositories\ProfileRepositoryInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Models\Profile;
use App\Models\User;
use App\Exceptions\UserNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
     public function updateUserProfile(User $user, array $validatedData, ?UploadedFile $avatar = null): array
    {
        $hasPasswordBefore = !is_null($user->password);
        
        $userData = $this->extractUserData($validatedData);
        if (!empty($userData)) {
            $this->userService->updateUser($user->id, $userData);
        }
        
        $profileData = $this->handleAvatarUpload($user, $avatar);
        if (!empty($profileData)) {
            $this->updateProfile($user->id, $profileData);
        }
        
        $message = $this->generateSuccessMessage($hasPasswordBefore, $validatedData);
        
        return ['message' => $message];
    }
     private function extractUserData(array $validatedData): array
    {
        $userData = [];
        $userFields = ['name', 'email', 'password'];
        
        foreach ($userFields as $field) {
            if (isset($validatedData[$field]) && !empty($validatedData[$field])) {
                $userData[$field] = $validatedData[$field];
            }
        }
        
        return $userData;
    }

    private function handleAvatarUpload(User $user, ?UploadedFile $avatar = null): array
    {
        if (!$avatar) {
            return [];
        }
        
        $this->deleteOldAvatar($user);
        
        $imagePath = $avatar->store('default', 's3');
        
        return ['avatar' => $imagePath];
    }

    private function deleteOldAvatar(User $user): void
    {
        if ($user->profile && 
            $user->profile->avatar && 
            Storage::disk('s3')->exists($user->profile->avatar)) {
            Storage::disk('s3')->delete($user->profile->avatar);
        }
    }

    private function generateSuccessMessage(bool $hasPasswordBefore, array $validatedData): string
    {
        $passwordWasSet = !$hasPasswordBefore && 
                         isset($validatedData['password']) && 
                         !empty($validatedData['password']);
        
        return $passwordWasSet 
            ? 'Profil berhasil diperbarui! Password telah berhasil diatur.'
            : 'Profil berhasil diperbarui!';
    }
}