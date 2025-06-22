<?php

namespace App\Listeners\Auth;

use App\Events\Auth\PasswordResetRequested;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
class SendPasswordResetNotification
{
    use InteractsWithQueue;

    public function handle(PasswordResetRequested $event): void
    {
        $resetUrl = url(route('password.reset', [
            'token' => $event->token,
            'email' => $event->user->email,
        ], false));

        Mail::to($event->user->email)->send(
            new ResetPasswordMail($event->user, $resetUrl, $event->token)
        );
    }
}
