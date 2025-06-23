<?php


namespace App\Contracts\Services;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\UploadedFile;

interface ProfileServiceInterface
{
    public function getProfileByUserId(int $userId): ?Profile;
    public function createProfile(User $user, array $data): Profile;
    public function updateProfile(int $userId, array $data): ?Profile;
    public function deleteProfile(int $userId): bool;
    public function updateUserProfile(User $user, array $validatedData, ?UploadedFile $avatar = null): array;
}