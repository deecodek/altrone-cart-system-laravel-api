<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendOrderNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderPlaced $event): void
    {
        Log::channel('notifications')->info('Mock email: order placed notification sent', [
            'order_id' => $event->order->id,
            'vendor_id' => $event->order->vendor_id,
            'customer_id' => $event->order->user_id,
            'total' => $event->order->total,
            'status' => $event->order->status,
        ]);
    }
}
