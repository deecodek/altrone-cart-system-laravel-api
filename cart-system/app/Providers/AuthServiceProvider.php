<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Order;
use App\Policies\OrderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Order::class => OrderPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('manage-users', fn ($user) => $user->isAdmin());
        Gate::define('manage-vendors', fn ($user) => $user->isAdmin());
        Gate::define('manage-products', fn ($user) => $user->isAdmin());
    }
}
