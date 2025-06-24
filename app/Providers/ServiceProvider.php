<?php

namespace App\Providers;

use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\ProductServiceInterface;
use App\Contracts\Services\ProfileServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Services\AuthService;
use App\Services\ProductService;
use App\Services\ProfileService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider as SV;

class ServiceProvider extends SV
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(ProfileServiceInterface::class, ProfileService::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
