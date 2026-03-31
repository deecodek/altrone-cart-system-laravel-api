<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'vendor_id', 'status', 'total'])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    const STATUS_PENDING = 'pending';

    const STATUS_PAID = 'paid';

    const STATUS_CANCELED = 'canceled';

    protected $casts = [
        'total' => 'float',
        'status' => OrderStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
