<?php

namespace App\Listeners\Order;

use App\Events\Order\PaymentFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\Order\PaymentFailedMail;

class SendPaymentFailedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PaymentFailed $event): void
    {
        try {
            $transaction = $event->transaction->load(['order.user', 'order.items.product']);
            $order = $transaction->order;
            
            Mail::to($order->user->email)->send(new PaymentFailedMail($transaction));
            
            Log::info('Payment failed notification sent', [
                'transaction_id' => $transaction->id,
                'order_id' => $order->id,
                'user_email' => $order->user->email
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send payment failed notification', [
                'transaction_id' => $event->transaction->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}