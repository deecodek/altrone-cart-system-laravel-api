<?php

declare(strict_types=1);

namespace App\Repository;

use App\Interfaces\CartRepositoryInterface;
use App\Models\Cart;
use App\Models\CartItem;

class CartRepository implements CartRepositoryInterface
{
    protected Cart $model;

    public function __construct(Cart $cart)
    {
        $this->model = $cart;
    }

    public function findOrCreateForUser(int $userId): Cart
    {
        return $this->model->firstOrCreate(['user_id' => $userId]);
    }

    public function findForUser(int $userId): ?Cart
    {
        return $this->model->where('user_id', $userId)->first();
    }

    public function findItemForUser(int $cartItemId, int $userId): CartItem
    {
        $item = CartItem::where('id', $cartItemId)
            ->whereHas('cart', fn ($query) => $query->where('user_id', $userId))
            ->first();

        if (! $item) {
            abort(404);
        }

        return $item;
    }

    public function saveItem(Cart $cart, int $productId, int $quantity): CartItem
    {
        $item = $cart->items()->firstOrNew(['product_id' => $productId]);
        $item->quantity = $quantity;
        $item->save();

        return $item->refresh();
    }

    public function deleteItem(CartItem $cartItem): bool
    {
        return $cartItem->delete();
    }

    public function clearItems(Cart $cart): bool
    {
        return $cart->items()->delete() >= 0;
    }

    public function loadCartWithItems(int $userId): Cart
    {
        return $this->model->with('items.product.vendor')
            ->where('user_id', $userId)
            ->firstOrFail();
    }
}
