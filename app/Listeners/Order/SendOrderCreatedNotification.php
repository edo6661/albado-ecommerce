<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderCreated;
use App\Mail\Order\OrderCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOrderCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderCreated $event): void
    {
        try {
            $order = $event->order->load(['user', 'items.product']);
            
            Mail::to($order->user->email)->send(new OrderCreatedMail($order));
            
            Log::info('Order created notification sent', [
                'order_id' => $order->id,
                'user_email' => $order->user->email
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send order created notification', [
                'order_id' => $event->order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
