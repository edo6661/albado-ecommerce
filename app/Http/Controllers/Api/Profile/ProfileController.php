<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Contracts\Services\ProfileServiceInterface;
use App\Http\Resources\ProfileResource;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        protected ProfileServiceInterface $profileService
    ) {}

    /**
     * Display the authenticated user's profile
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $profile = $this->profileService->getProfileByUserId($user->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diambil',
                'data' => new ProfileResource($user->load('profile'))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the authenticated user's profile
     *
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $result = $this->profileService->updateUserProfile(
                $request->user(),
                $request->validated(),
                $request->file('avatar')
            );
            
            $user = $request->user()->fresh(['profile']);
            
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => new ProfileResource($user)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the authenticated user's avatar
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAvatar(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user->profile || !$user->profile->avatar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Avatar tidak ditemukan'
                ], 404);
            }

            $this->profileService->updateProfile($user->id, ['avatar' => null]);
            
            return response()->json([
                'success' => true,
                'message' => 'Avatar berhasil dihapus',
                'data' => new ProfileResource($user->fresh(['profile']))
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}