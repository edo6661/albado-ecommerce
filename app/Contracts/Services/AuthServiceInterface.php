<?php


namespace App\Contracts\Services;

use App\Models\User;

interface AuthServiceInterface
{
    public function register(array $data): User;
    public function login(array $credentials): ?User;
    public function logout(): void;
    public function verifyEmail(User $user): bool;
    public function sendPasswordResetLink(string $email): bool;
    public function resetPassword(array $data): bool;
    public function redirectAfterLogin():string;
    public function handleProviderCallback(string $provider, object $socialUser): User;
}
