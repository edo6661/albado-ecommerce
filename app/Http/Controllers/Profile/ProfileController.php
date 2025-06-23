<?php

namespace App\Http\Controllers\Profile;

use App\Contracts\Services\ProfileServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        protected ProfileServiceInterface $profileService
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
            $result = $this->profileService->updateUserProfile(
                $request->user(),
                $request->validated(),
                $request->file('avatar')
            );
            
            return redirect()
                ->route('profile.show')
                ->with('success', $result['message']);
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage());
        }
    }
}