<?php

namespace App\Interfaces;

use App\Models\Cart;
use App\Models\CartItem;

interface CartRepositoryInterface
{
    public function findOrCreateForUser(int $userId): Cart;

    public function findForUser(int $userId): ?Cart;

    public function findItemForUser(int $cartItemId, int $userId): CartItem;

    public function saveItem(Cart $cart, int $productId, int $quantity): CartItem;

    public function deleteItem(CartItem $cartItem): bool;

    public function clearItems(Cart $cart): bool;

    public function loadCartWithItems(int $userId): Cart;
}
