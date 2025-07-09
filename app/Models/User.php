<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }
    public function isUser(): bool
    {
        return $this->role === UserRole::USER;
    }
    public function hasSocialLogin(): bool
    {
        return $this->socialAccounts()->exists();
    }

    public function hasPassword(): bool
    {
        return !is_null($this->password);
    }

    public function isSocialOnlyUser(): bool
    {
        return $this->hasSocialLogin() && !$this->hasPassword();
    }

    public function getSocialProvider(): ?string
    {
        $socialAccount = $this->socialAccounts()->first();
        return $socialAccount ? $socialAccount->provider : null;
    }
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getOrCreateCart(): Cart
    {
        return $this->cart ?: $this->cart()->create();
    }
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }
}

