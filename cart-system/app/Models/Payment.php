<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['order_id', 'amount', 'status'])]
class Payment extends Model
{
    const STATUS_PENDING = 'pending';

    const STATUS_PAID = 'paid';

    const STATUS_FAILED = 'failed';

    protected $casts = [
        'amount' => 'float',
        'status' => PaymentStatus::class,
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
