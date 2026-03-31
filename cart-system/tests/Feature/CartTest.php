<?php

declare(strict_types=1);

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('gets the cart for authenticated user', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create();
    $product = Product::factory()->create(['vendor_id' => $vendor->id]);

    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $cart->items()->create([
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user)->getJson('/api/cart');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'vendors' => [
                    '*' => [
                        'vendor' => ['id', 'name', 'email', 'slug', 'created_at', 'updated_at'],
                        'items' => [
                            '*' => ['id', 'product', 'quantity', 'unit_price', 'total_price'],
                        ],
                        'subtotal',
                    ],
                ],
                'total',
            ],
        ]);
});

it('adds item to cart', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create();
    $product = Product::factory()->create(['vendor_id' => $vendor->id]);

    $payload = [
        'product_id' => $product->id,
        'quantity' => 1,
    ];

    $response = $this->actingAs($user)->postJson('/api/cart/items', $payload);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => ['id', 'quantity', 'unit_price', 'total_price'],
        ]);

    expect($user->cart->items()->count())->toBe(1);
});

it('updates cart item quantity', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create();
    $product = Product::factory()->create(['vendor_id' => $vendor->id]);

    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $cartItem = $cart->items()->create([
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $payload = [
        'quantity' => 1,
    ];

    $response = $this->actingAs($user)->patchJson("/api/cart/items/{$cartItem->id}", $payload);

    $response->assertOk()
        ->assertJsonPath('data.quantity', 1);

    expect($cartItem->fresh()->quantity)->toBe(1);
});

it('removes item from cart', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create();
    $product = Product::factory()->create(['vendor_id' => $vendor->id]);

    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $cartItem = $cart->items()->create([
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user)->deleteJson("/api/cart/items/{$cartItem->id}");

    $response->assertNoContent();

    expect($user->cart->items()->count())->toBe(0);
});
