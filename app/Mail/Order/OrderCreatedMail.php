<?php

namespace App\Mail\Order;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;   
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pesanan Baru - ' . $this->order->order_number . ' - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order.created',
            with: [
                'order' => $this->order,
                'user' => $this->order->user,
                'items' => $this->order->items,
            ]
        );
    }
}

