<?php

namespace App\Interfaces;

use App\Models\Payment;

interface PaymentRepositoryInterface
{
    public function create(array $data): Payment;

    public function markPaid(Payment $payment): Payment;

    public function markCanceled(Payment $payment): Payment;
}
