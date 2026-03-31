<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\User;
use DateTimeImmutable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderService
{
    protected OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function list(User $user, array $filters, int $perPage = 15): Paginator
    {
        try {
            Log::debug('OrderService list called', [
                'user_id' => $user->id,
                'filters' => $filters,
                'per_page' => $perPage,
            ]);

            if (! $user->isAdmin()) {
                $filters['user_id'] = $user->id;
            }

            return $this->orderRepository->paginateForUser($filters, $perPage);
        } catch (Throwable $exception) {
            Log::error('OrderService list failed', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);
            throw $exception;
        }
    }

    public function get(User $user, int $orderId): Order
    {
        try {
            Log::debug('OrderService get called', [
                'user_id' => $user->id,
                'order_id' => $orderId,
            ]);

            if ($user->isAdmin()) {
                return $this->orderRepository->findOrFail($orderId);
            }

            return $this->orderRepository->findForUser($orderId, $user->id);
        } catch (Throwable $exception) {
            Log::error('OrderService get failed', [
                'user_id' => $user->id,
                'order_id' => $orderId,
                'error' => $exception->getMessage(),
            ]);
            throw $exception;
        }
    }

    public function cancelUnpaidOlderThan(DateTimeImmutable $dateTime): int
    {
        try {
            Log::info('OrderService cancelUnpaidOlderThan called', [
                'cutoff_date' => $dateTime->format('Y-m-d H:i:s'),
            ]);

            $orders = $this->orderRepository->getUnpaidOlderThan($dateTime);
            $count = 0;

            foreach ($orders as $order) {
                try {
                    $this->orderRepository->cancel($order);
                    $count++;

                    Log::info('OrderService canceled order', ['order_id' => $order->id]);
                } catch (Throwable $exception) {
                    Log::error('OrderService cancel failed', [
                        'order_id' => $order->id,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }

            Log::info('OrderService cancelUnpaidOlderThan completed', [
                'total_found' => $orders->count(),
                'canceled' => $count,
            ]);

            return $count;
        } catch (Throwable $exception) {
            Log::error('OrderService cancelUnpaidOlderThan failed', [
                'cutoff_date' => $dateTime->format('Y-m-d H:i:s'),
                'error' => $exception->getMessage(),
            ]);
            throw $exception;
        }
    }
}
