<?php

namespace App\Http\Controllers\Profile;

use App\Contracts\Services\ProfileServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        protected ProfileServiceInterface $profileService,
        protected UserServiceInterface $userService
    ) {}


    public function show(Request $request): View
    {
        $user = $request->user();
        $profile = $this->profileService->getProfileByUserId($user->id);
        return view('profile.show', compact('user', 'profile'));
    }

    public function edit(Request $request): View
    {
        $user = $request->user();
        $profile = $this->profileService->getProfileByUserId($user->id);
        
        return view('profile.edit', compact('user', 'profile'));
    }


    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $validatedData = $request->validated();
            
            $userData = [];
            $profileData = [];
            
            if (isset($validatedData['name'])) {
                $userData['name'] = $validatedData['name'];
            }
            
            if (isset($validatedData['email'])) {
                $userData['email'] = $validatedData['email'];
            }
            
            $hasPassword = !is_null($user->password);
            
            if (isset($validatedData['password']) && !empty($validatedData['password'])) {
                $userData['password'] = $validatedData['password'];
            }
            
            if (isset($validatedData['avatar'])) {
                $profileData['avatar'] = $validatedData['avatar'];
            }
            
            if (!empty($userData)) {
                $this->userService->updateUser($user->id, $userData);
            }
            
            if (!empty($profileData)) {
                $this->profileService->updateProfile($user->id, $profileData);
            }
            
            $message = 'Profil berhasil diperbarui!';
            if (!$hasPassword && isset($validatedData['password']) && !empty($validatedData['password'])) {
                $message = 'Profil berhasil diperbarui! Password telah berhasil diatur.';
            }
            
            return redirect()
                ->route('profile.show')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage());
        }
    }
}