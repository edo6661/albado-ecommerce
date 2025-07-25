<?php
namespace App\Services;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Enums\UserRole;
use App\Events\Auth\PasswordResetRequested;
use App\Events\Auth\UserRegistered;
use App\Models\Profile;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
class AuthService implements AuthServiceInterface
{
    public function __construct(
        protected UserServiceInterface $userService
    ) {}
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            try {
                if (!isset($data['role'])) {
                    $data['role'] = UserRole::USER;
                }
                $user = $this->userService->createUser($data);
                event(new UserRegistered($user));
                return $user;
            } catch (\Exception $e) {
                Log::error('Error in AuthService::register', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                throw $e;
            }
        });
    }
    public function login(array $credentials): ?User
    {
        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);
        if (Auth::attempt($credentials, $remember)) {
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
            $user->save();
            return true;
        }
        return false;
    }
    public function sendPasswordResetLink(string $email): bool
    {
        $user = $this->getUserByEmail($email);
        if (!$user) {
            return false;
        }
        $status = Password::sendResetLink(
            ['email' => $email],
            function (User $user, string $token) {
                event(new PasswordResetRequested($user, $token));
            }
        );
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
                    'password' => null,
                    'email_verified_at' => now(), 
                    'role' => UserRole::USER,
                ]);
                Profile::create([
                    'user_id' => $user->id,
                    'avatar' => $socialUser->getAvatar(),
                ]);
            } 
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
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