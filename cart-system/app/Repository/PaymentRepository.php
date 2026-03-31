<?php

declare(strict_types=1);

namespace App\Repository;

use App\Interfaces\PaymentRepositoryInterface;
use App\Models\Payment;

class PaymentRepository implements PaymentRepositoryInterface
{
    protected Payment $model;

    public function __construct(Payment $payment)
    {
        $this->model = $payment;
    }

    public function create(array $data): Payment
    {
        return $this->model->create($data);
    }

    public function markPaid(Payment $payment): Payment
    {
        $payment->update(['status' => 'paid']);

        return $payment->fresh();
    }

    public function markCanceled(Payment $payment): Payment
    {
        $payment->update(['status' => 'canceled']);

        return $payment->fresh();
    }
}
