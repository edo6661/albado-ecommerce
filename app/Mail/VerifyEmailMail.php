<?php

namespace App\Mail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable
{
use Queueable, SerializesModels;

public function __construct(
    public User $user,
    public string $verificationUrl
) {}

public function envelope(): Envelope
{
    return new Envelope(
        subject: 'Verifikasi Email Anda - ' . config('app.name'),
    );
}

public function content(): Content
{
    return new Content(
        view: 'emails.verify-email',
        with: [
            'user' => $this->user,
            'verificationUrl' => $this->verificationUrl,
        ]
    );
}
}
