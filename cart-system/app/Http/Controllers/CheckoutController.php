<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Checkout\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Log;
use Throwable;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function __invoke(CheckoutRequest $request)
    {
        try {
            Log::info('Checkout initiated', [
                'user_id' => auth()->id(),
            ]);

            $orders = $this->checkoutService->checkout(auth()->user());

            Log::info('Checkout completed successfully', [
                'user_id' => auth()->id(),
                'orders_count' => $orders->count(),
                'order_ids' => $orders->pluck('id')->toArray(),
            ]);

            return OrderResource::collection($orders);
        } catch (Throwable $exception) {
            Log::error('CheckoutController failed', [
                'user_id' => auth()->id(),
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            throw $exception;
        }
    }
}
