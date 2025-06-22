<?php

namespace App\Services;

use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        protected UserServiceInterface $userService
    ) {}

    public function register(array $data): User
    {
        $user = $this->userService->createUser($data);
        
        event(new Registered($user));
        
        return $user;
    }

    public function login(array $credentials): ?User
    {
        if (Auth::attempt($credentials)) {
            return Auth::user();
        }
        
        return null;
    }

    public function logout(): void
    {
        Auth::logout();
    }

    public function verifyEmail(User $user): bool
    {
        if ($user->hasVerifiedEmail()) {
            return false;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            return true;
        }

        return false;
    }

    public function sendPasswordResetLink(string $email): bool
    {
        $status = Password::sendResetLink(['email' => $email]);
        
        return $status === Password::RESET_LINK_SENT;
    }

    public function resetPassword(array $data): bool
    {
        $status = Password::reset($data, function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();
        });

        return $status === Password::PASSWORD_RESET;
    }
    public function handleProviderCallback(string $provider, object $socialUser): User
    {
        return DB::transaction(function () use ($provider, $socialUser) {
            $socialAccount = SocialAccount::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();
            
            if ($socialAccount && $socialAccount->user) {
                $socialAccount->update([
                    'provider_token' => $socialUser->token,
                    'provider_refresh_token' => $socialUser->refreshToken,
                ]);
                
                return $socialAccount->user;
            }
            
            $user = $this->getUserByEmail($socialUser->getEmail());
            
            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'avatar' => $socialUser->getAvatar(),
                    'password' => null,
                    'email_verified_at' => now(),
                    'role' => 'seller',
                ]);
            }
            
            $socialAccount = SocialAccount::updateOrCreate(
                [
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                ],
                [
                    'user_id' => $user->id, 
                    'provider_token' => $socialUser->token,
                    'provider_refresh_token' => $socialUser->refreshToken,
                ]
            );
            
            return $user;
        });
    }
    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
     public function getAuthenticatedUser(): ?User
    {
        return Auth::user();
    }
    public function redirectAfterLogin(): string
    {
        $user = $this->getAuthenticatedUser();
        
        if ($user->isAdmin()) {
            return route('admin.dashboard');
        }
        return route('home');
    }
}
