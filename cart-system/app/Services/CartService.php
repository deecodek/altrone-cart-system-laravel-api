<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class CartService
{
    protected CartRepositoryInterface $cartRepository;

    protected ProductRepositoryInterface $productRepository;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
    }

    public function getCart(User $user): Cart
    {
        try {
            Log::debug('CartService getCart called', ['user_id' => $user->id]);

            return Cache::remember("user:{$user->id}:cart", 60, fn () => $this->cartRepository->findOrCreateForUser($user->id));
        } catch (Throwable $exception) {
            Log::error('CartService getCart failed', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    public function addItem(User $user, int $productId, int $quantity): CartItem
    {
        try {
            Log::info('CartService addItem called', [
                'user_id' => $user->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);

            $item = DB::transaction(function () use ($user, $productId, $quantity) {
                $product = $this->productRepository->findForUpdate($productId);

                if ($quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'quantity' => ['Quantity cannot exceed available stock.'],
                    ]);
                }

                $cart = $this->cartRepository->findOrCreateForUser($user->id);
                $item = $cart->items()->firstOrNew(['product_id' => $productId]);
                $item->quantity = $item->exists
                    ? min($item->quantity + $quantity, $product->stock)
                    : $quantity;
                $item->save();

                Cache::forget("user:{$user->id}:cart");

                return $item->refresh();
            });

            Log::info('CartService addItem succeeded', [
                'user_id' => $user->id,
                'product_id' => $productId,
                'cart_item_id' => $item->id,
            ]);

            return $item;
        } catch (Throwable $exception) {
            Log::error('CartService addItem failed', [
                'user_id' => $user->id,
                'product_id' => $productId,
                'error' => $exception->getMessage(),
            ]);
            throw $exception;
        }
    }

    public function updateItem(User $user, int $cartItemId, int $quantity): CartItem
    {
        try {
            Log::info('CartService updateItem called', [
                'user_id' => $user->id,
                'cart_item_id' => $cartItemId,
                'quantity' => $quantity,
            ]);

            $item = DB::transaction(function () use ($user, $cartItemId, $quantity) {
                $item = $this->cartRepository->findItemForUser($cartItemId, $user->id);
                $product = $this->productRepository->findForUpdate($item->product_id);

                if ($quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'quantity' => ['Quantity cannot exceed available stock.'],
                    ]);
                }

                $item->quantity = $quantity;
                $item->save();
                Cache::forget("user:{$user->id}:cart");

                return $item->refresh();
            });

            Log::info('CartService updateItem succeeded', [
                'user_id' => $user->id,
                'cart_item_id' => $cartItemId,
            ]);

            return $item;
        } catch (Throwable $exception) {
            Log::error('CartService updateItem failed', [
                'user_id' => $user->id,
                'cart_item_id' => $cartItemId,
                'error' => $exception->getMessage(),
            ]);
            throw $exception;
        }
    }

    public function removeItem(User $user, int $cartItemId): bool
    {
        try {
            Log::info('CartService removeItem called', [
                'user_id' => $user->id,
                'cart_item_id' => $cartItemId,
            ]);

            $item = $this->cartRepository->findItemForUser($cartItemId, $user->id);
            $deleted = $this->cartRepository->deleteItem($item);
            Cache::forget("user:{$user->id}:cart");

            Log::info('CartService removeItem succeeded', [
                'user_id' => $user->id,
                'cart_item_id' => $cartItemId,
            ]);

            return $deleted;
        } catch (Throwable $exception) {
            Log::error('CartService removeItem failed', [
                'user_id' => $user->id,
                'cart_item_id' => $cartItemId,
                'error' => $exception->getMessage(),
            ]);
            throw $exception;
        }
    }

    public function clearCart(User $user): bool
    {
        try {
            Log::info('CartService clearCart called', ['user_id' => $user->id]);

            $cart = $this->cartRepository->findOrCreateForUser($user->id);
            $cleared = $this->cartRepository->clearItems($cart);
            Cache::forget("user:{$user->id}:cart");

            Log::info('CartService clearCart succeeded', ['user_id' => $user->id]);

            return $cleared;
        } catch (Throwable $exception) {
            Log::error('CartService clearCart failed', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }
}
