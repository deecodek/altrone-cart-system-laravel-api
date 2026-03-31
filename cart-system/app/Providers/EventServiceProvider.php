<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Events\PaymentSucceeded;
use App\Listeners\SendOrderNotification;
use App\Listeners\SendPaymentNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderPlaced::class => [
            SendOrderNotification::class,
        ],
        PaymentSucceeded::class => [
            SendPaymentNotification::class,
        ],
    ];
}
