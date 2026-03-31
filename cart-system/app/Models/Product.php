<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'price', 'vendor_id', 'stock'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (float) $value,
            set: fn ($value) => number_format((float) $value, 2, '.', ''),
        );
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
