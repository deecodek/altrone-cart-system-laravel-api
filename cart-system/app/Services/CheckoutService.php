<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\OrderPlaced;
use App\Events\PaymentSucceeded;
use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class CheckoutService
{
    protected CartRepositoryInterface $cartRepository;

    protected OrderRepositoryInterface $orderRepository;

    protected PaymentRepositoryInterface $paymentRepository;

    protected ProductRepositoryInterface $productRepository;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        OrderRepositoryInterface $orderRepository,
        PaymentRepositoryInterface $paymentRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->orderRepository = $orderRepository;
        $this->paymentRepository = $paymentRepository;
        $this->productRepository = $productRepository;
    }

    public function checkout(User $user): Collection
    {
        try {
            Log::info('CheckoutService checkout started', ['user_id' => $user->id]);

            $cart = $this->cartRepository->loadCartWithItems($user->id);

            if ($cart->items->isEmpty()) {
                Log::warning('CheckoutService checkout failed - cart empty', ['user_id' => $user->id]);

                throw ValidationException::withMessages([
                    'cart' => ['Cart is empty.'],
                ]);
            }

            $orders = DB::transaction(function () use ($cart, $user) {
                $orders = new Collection;

                foreach ($cart->items->groupBy(fn ($item) => $item->product->vendor_id) as $vendorId => $items) {
                    $order = $this->orderRepository->create([
                        'user_id' => $user->id,
                        'vendor_id' => $vendorId,
                        'status' => Order::STATUS_PAID,
                        'total' => 0,
                    ]);

                    $total = 0;

                    foreach ($items as $item) {
                        $product = $this->productRepository->findForUpdate($item->product_id);

                        if ($item->quantity > $product->stock) {
                            Log::warning('CheckoutService insufficient stock', [
                                'product_id' => $item->product_id,
                                'requested' => $item->quantity,
                                'available' => $product->stock,
                            ]);

                            throw ValidationException::withMessages([
                                'quantity' => [sprintf('Product %s does not have enough stock.', $product->name)],
                            ]);
                        }

                        $this->productRepository->decrementStock($product, $item->quantity);

                        $itemTotal = $item->quantity * $product->price;
                        $total += $itemTotal;

                        $order->items()->create([
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'quantity' => $item->quantity,
                            'unit_price' => $product->price,
                            'total_price' => $itemTotal,
                        ]);
                    }

                    $this->orderRepository->update($order, ['total' => $total]);

                    $payment = $this->paymentRepository->create([
                        'order_id' => $order->id,
                        'amount' => $total,
                        'status' => Payment::STATUS_PAID,
                    ]);

                    event(new OrderPlaced($order));
                    event(new PaymentSucceeded($payment));

                    $orders->push($order->fresh());
                }

                $this->cartRepository->clearItems($cart);

                return $orders;
            });

            Log::info('CheckoutService checkout completed', [
                'user_id' => $user->id,
                'orders_count' => $orders->count(),
                'order_ids' => $orders->pluck('id')->toArray(),
            ]);

            return $orders;
        } catch (Throwable $exception) {
            Log::error('CheckoutService checkout failed', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            throw $exception;
        }
    }
}
