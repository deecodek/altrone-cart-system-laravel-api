<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case CANCELED = 'canceled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
