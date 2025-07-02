<?php

namespace App\Listeners\Order;

use App\Events\Order\PaymentSuccess;
use App\Mail\Order\PaymentSuccessMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
class SendPaymentSuccessNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PaymentSuccess $event): void
    {
        try {
            $transaction = $event->transaction->load(['order.user', 'order.items.product']);
            $order = $transaction->order;
            
            Mail::to($order->user->email)->send(new PaymentSuccessMail($transaction));
            
            Log::info('Payment success notification sent', [
                'transaction_id' => $transaction->id,
                'order_id' => $order->id,
                'user_email' => $order->user->email
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send payment success notification', [
                'transaction_id' => $event->transaction->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
