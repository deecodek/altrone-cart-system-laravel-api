<?php

declare(strict_types=1);

namespace App\Repository;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use DateTimeImmutable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    protected Order $model;

    public function __construct(Order $order)
    {
        $this->model = $order;
    }

    public function paginateForUser(array $filters, int $perPage = 15): Paginator
    {
        $query = $this->model->with(['vendor', 'user', 'payment', 'items.product']);

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->paginate($perPage);
    }

    public function findForUser(int $orderId, int $userId): Order
    {
        return $this->model->with(['vendor', 'user', 'payment', 'items.product'])
            ->where('id', $orderId)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function findOrFail(int $id): Order
    {
        return $this->model->with(['vendor', 'user', 'payment', 'items.product'])->findOrFail($id);
    }

    public function create(array $data): Order
    {
        return $this->model->create($data);
    }

    public function update(Order $order, array $data): Order
    {
        $order->update($data);

        return $order->fresh();
    }

    public function cancel(Order $order): Order
    {
        $order->update(['status' => OrderStatus::CANCELED]);

        return $order->fresh();
    }

    public function getUnpaidOlderThan(DateTimeImmutable $dateTime): Collection
    {
        return $this->model->with(['items.product', 'payment'])
            ->where('status', OrderStatus::PENDING->value)
            ->whereHas('payment', fn ($query) => $query->where('status', PaymentStatus::PENDING->value))
            ->where('created_at', '<=', $dateTime)
            ->get();
    }
}
