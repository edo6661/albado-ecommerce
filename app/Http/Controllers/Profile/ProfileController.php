<?php


namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Contracts\Services\ProfileServiceInterface;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Exceptions\UserNotFoundException;
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
        $userId = auth()->id();
        $profile = $this->profileService->getProfileByUserId($userId);
        
        return view('profile.show', compact('profile'));
    }

    public function edit(Request $request): View
    {
        $userId = auth()->id();
        $profile = $this->profileService->getProfileByUserId($userId);
        
        return view('profile.edit', compact('profile'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        try {
            $userId = auth()->id();
            $profile = $this->profileService->updateProfile($userId, $request->validated());
            
            return redirect()->route('profile.show')
                           ->with('success', 'Profile berhasil diupdate.');
        } catch (UserNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            return back()->withInput()
                        ->withErrors(['error' => 'Gagal mengupdate profile. Silakan coba lagi.']);
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        try {
            $userId = auth()->id();
            $deleted = $this->profileService->deleteProfile($userId);
            
            if ($deleted) {
                return redirect()->route('profile.show')
                               ->with('success', 'Profile berhasil dihapus.');
            }
            
            return back()->withErrors(['error' => 'Gagal menghapus profile.']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus profile. Silakan coba lagi.']);
        }
    }
}