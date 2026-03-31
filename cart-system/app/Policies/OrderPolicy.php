<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->role === UserRole::CUSTOMER->value;
    }

    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() || $order->user_id === $user->id;
    }
}
