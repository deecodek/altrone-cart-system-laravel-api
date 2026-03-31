<?php

namespace App\Providers;

use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\VendorRepositoryInterface;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\VendorRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            VendorRepositoryInterface::class,
            VendorRepository::class
        );
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );
        $this->app->bind(
            CartRepositoryInterface::class,
            CartRepository::class
        );
        $this->app->bind(
            OrderRepositoryInterface::class,
            OrderRepository::class
        );
        $this->app->bind(
            PaymentRepositoryInterface::class,
            PaymentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
