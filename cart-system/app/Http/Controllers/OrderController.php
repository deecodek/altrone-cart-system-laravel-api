<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Order\OrderIndexRequest;
use App\Http\Requests\Order\ShowOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(OrderIndexRequest $request)
    {
        try {
            $filters = $request->only(['vendor_id', 'customer_id', 'status']);
            $perPage = $request->input('per_page', 15);

            Log::info('Orders listed', [
                'requested_by' => auth()->id(),
                'filters' => $filters,
                'per_page' => $perPage,
            ]);

            return OrderResource::collection(
                $this->orderService->list(
                    auth()->user(),
                    $filters,
                    (int) $perPage
                )
            );
        } catch (Throwable $exception) {
            Log::error('OrderController index failed', [
                'requested_by' => auth()->id(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function show(ShowOrderRequest $request, Order $order)
    {
        try {
            Log::info('Order retrieved', [
                'order_id' => $order->id,
                'requested_by' => auth()->id(),
            ]);

            $order = $this->orderService->get(auth()->user(), $order->id);

            return new OrderResource($order);
        } catch (Throwable $exception) {
            Log::error('OrderController show failed', [
                'order_id' => $order->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
