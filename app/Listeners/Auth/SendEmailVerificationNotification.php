<?php

namespace App\Listeners\Auth;

use App\Events\Auth\EmailVerificationRequested;
use App\Events\Auth\UserRegistered;
use App\Mail\ApiVerifyEmailMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\VerifyEmailMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class SendEmailVerificationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UserRegistered|EmailVerificationRequested $event): void
    {
        $user = $event->user;
       

        if (!$user || $user->hasVerifiedEmail()) {
            return;
        }

        try {
            $isApiRequest = request()->is('api/*') || request()->expectsJson();
            $routeName = $isApiRequest ? 'api.verification.verify' : 'verification.verify';
          
            $verificationUrl = URL::temporarySignedRoute(
                $routeName,
                now()->addMinutes(60),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );

            if ($isApiRequest) {
                Mail::to($user->email)->send(new ApiVerifyEmailMail($user, $verificationUrl));
            } else {
                Mail::to($user->email)->send(new VerifyEmailMail($user, $verificationUrl));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send verification email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'is_api_request' => $isApiRequest ?? false
            ]);
            
            // throw $e;
        }
    }
}