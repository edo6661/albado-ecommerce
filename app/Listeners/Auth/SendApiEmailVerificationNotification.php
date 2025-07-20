<?php

namespace App\Listeners\Auth;

use App\Events\Auth\EmailVerificationRequested;
use App\Events\Auth\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\ApiVerifyEmailMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class SendApiEmailVerificationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UserRegistered|EmailVerificationRequested $event): void
    {
        $user = $event->user;

        if (!$user || $user->hasVerifiedEmail()) {
            return;
        }

        try {
            $verificationUrl = URL::temporarySignedRoute(
                'api.verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );

            Mail::to($user->email)->send(new ApiVerifyEmailMail($user, $verificationUrl));
        } catch (\Exception $e) {
            Log::error('Failed to send API verification email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}