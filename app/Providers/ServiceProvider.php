<?php

namespace App\Providers;

use App\Contracts\Services\AddressServiceInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\CartServiceInterface;
use App\Contracts\Services\CategoryServiceInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Services\ProductServiceInterface;
use App\Contracts\Services\ProfileServiceInterface;
use App\Contracts\Services\MidtransServiceInterface;
use App\Contracts\Services\TransactionServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Services\AddressService;
use App\Services\AuthService;
use App\Services\CartService;
use App\Services\CategoryService;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\ProfileService;
use App\Services\TransactionService;
use App\Services\UserService;
use App\Services\MidtransService;
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
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
        $this->app->bind(TransactionServiceInterface::class, TransactionService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(MidtransServiceInterface::class, MidtransService::class);
        $this->app->bind(CartServiceInterface::class, CartService::class);
        $this->app->bind(AddressServiceInterface::class, AddressService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
