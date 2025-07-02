<?php

namespace App\Mail\Order;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Transaction $transaction
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pembayaran Gagal - ' . $this->transaction->order->order_number . ' - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order.payment-failed',
            with: [
                'transaction' => $this->transaction,
                'order' => $this->transaction->order,
                'user' => $this->transaction->order->user,
                'items' => $this->transaction->order->items,
            ]
        );
    }
}

