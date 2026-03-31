<?php

namespace App\Interfaces;

use App\Models\Order;
use DateTimeImmutable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function paginateForUser(array $filters, int $perPage = 15): Paginator;

    public function findForUser(int $orderId, int $userId): Order;

    public function findOrFail(int $id): Order;

    public function create(array $data): Order;

    public function update(Order $order, array $data): Order;

    public function cancel(Order $order): Order;

    public function getUnpaidOlderThan(DateTimeImmutable $dateTime): Collection;
}
