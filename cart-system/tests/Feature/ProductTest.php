<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists all products', function () {
    $user = User::factory()->create();
    Product::factory()->count(5)->create();

    $response = $this->actingAs($user)->getJson('/api/products');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'description', 'price', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ]);
});

it('creates a new product', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create();

    $payload = [
        'name' => 'New Product',
        'description' => 'Product description',
        'price' => 99.99,
        'vendor_id' => $vendor->id,
        'stock' => 50,
    ];

    $response = $this->actingAs($user)->postJson('/api/products', $payload);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'price', 'created_at', 'updated_at'],
        ]);

    expect(Product::where('name', 'New Product')->exists())->toBeTrue();
});

it('gets a specific product', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($user)->getJson("/api/products/{$product->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'price', 'created_at', 'updated_at'],
        ])
        ->assertJsonPath('data.id', $product->id);
});

it('updates a product', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $payload = [
        'name' => 'Updated Product',
        'price' => 149.99,
        'stock' => 25,
    ];

    $response = $this->actingAs($user)->putJson("/api/products/{$product->id}", $payload);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Updated Product')
        ->assertJsonPath('data.price', 149.99);

    expect(Product::find($product->id)->name)->toBe('Updated Product');
});

it('deletes a product', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $response = $this->actingAs($user)->deleteJson("/api/products/{$product->id}");

    $response->assertNoContent();

    expect(Product::find($product->id))->toBeNull();
});
