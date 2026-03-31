<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PaymentSucceeded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPaymentNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PaymentSucceeded $event): void
    {
        Log::channel('notifications')->info('Mock email: payment succeeded notification sent', [
            'payment_id' => $event->payment->id,
            'order_id' => $event->payment->order_id,
            'amount' => $event->payment->amount,
            'status' => $event->payment->status,
        ]);
    }
}
