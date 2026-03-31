<?php

declare(strict_types=1);

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('checks out cart and creates orders', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create();
    $product = Product::factory()->create(['vendor_id' => $vendor->id, 'stock' => 100]);

    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $cart->items()->create([
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user)->postJson('/api/checkout');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'status',
                    'total',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

    expect($user->orders()->count())->toBe(1);
    expect($user->cart->items()->count())->toBe(0);
});
