<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Cart\DeleteCartItemRequest;
use App\Http\Requests\Cart\StoreCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(): CartResource
    {
        try {
            $cart = $this->cartService->getCart(auth()->user());

            return new CartResource($cart);
        } catch (Throwable $exception) {
            Log::error('CartController index failed', [
                'user_id' => auth()->id(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function store(StoreCartItemRequest $request): CartItemResource
    {
        try {
            $item = $this->cartService->addItem(
                auth()->user(),
                $request->input('product_id'),
                $request->input('quantity')
            );

            Log::info('Cart item added', [
                'user_id' => auth()->id(),
                'product_id' => $request->input('product_id'),
                'quantity' => $request->input('quantity'),
                'cart_item_id' => $item->id,
            ]);

            return new CartItemResource($item);
        } catch (Throwable $exception) {
            Log::error('CartController store failed', [
                'user_id' => auth()->id(),
                'product_id' => $request->input('product_id'),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function update(UpdateCartItemRequest $request, int $cartItem): CartItemResource
    {
        try {
            $item = $this->cartService->updateItem(
                auth()->user(),
                $cartItem,
                $request->input('quantity')
            );

            Log::info('Cart item updated', [
                'user_id' => auth()->id(),
                'cart_item_id' => $cartItem,
                'quantity' => $request->input('quantity'),
            ]);

            return new CartItemResource($item);
        } catch (Throwable $exception) {
            Log::error('CartController update failed', [
                'user_id' => auth()->id(),
                'cart_item_id' => $cartItem,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function destroy(DeleteCartItemRequest $request, int $cartItem): JsonResponse
    {
        try {
            $this->cartService->removeItem(auth()->user(), $cartItem);

            Log::info('Cart item removed', [
                'user_id' => auth()->id(),
                'cart_item_id' => $cartItem,
            ]);

            return response()->json([], 204);
        } catch (Throwable $exception) {
            Log::error('CartController destroy failed', [
                'user_id' => auth()->id(),
                'cart_item_id' => $cartItem,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
