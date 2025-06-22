<?php

namespace App\Providers;

use App\Events\Auth\EmailVerificationRequested;
use App\Events\Auth\PasswordResetRequested;
use App\Events\Auth\UserRegistered;
use App\Listeners\Auth\SendEmailVerificationNotification;
use App\Listeners\Auth\SendPasswordResetNotification;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
      protected $listen = [
        

        
        UserRegistered::class => [
            SendEmailVerificationNotification::class,
        ],

        EmailVerificationRequested::class => [
            SendEmailVerificationNotification::class,
        ],

        
        PasswordResetRequested::class => [
            SendPasswordResetNotification::class,
        ],
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        
    }
}
