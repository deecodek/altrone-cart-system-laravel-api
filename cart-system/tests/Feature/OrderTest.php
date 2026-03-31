<?php

declare(strict_types=1);

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists orders for authenticated user', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create();

    Order::factory()->count(3)->create([
        'user_id' => $user->id,
        'vendor_id' => $vendor->id,
        'status' => OrderStatus::PENDING->value,
    ]);

    $response = $this->actingAs($user)->getJson('/api/orders');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'status',
                    'total',
                    'vendor' => ['id', 'name', 'email', 'slug', 'created_at', 'updated_at'],
                    'customer' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at'],
                    'payment',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta',
        ]);
});

it('filters orders by vendor', function () {
    $user = User::factory()->create();
    $vendor1 = Vendor::factory()->create();
    $vendor2 = Vendor::factory()->create();

    Order::factory()->create(['user_id' => $user->id, 'vendor_id' => $vendor1->id]);
    Order::factory()->create(['user_id' => $user->id, 'vendor_id' => $vendor2->id]);

    $response = $this->actingAs($user)->getJson("/api/orders?vendor_id={$vendor1->id}");

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.vendor.id'))->toBe($vendor1->id);
});

it('filters orders by status', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create();

    Order::factory()->create(['user_id' => $user->id, 'vendor_id' => $vendor->id, 'status' => 'pending']);
    Order::factory()->create(['user_id' => $user->id, 'vendor_id' => $vendor->id, 'status' => 'paid']);

    $response = $this->actingAs($user)->getJson('/api/orders?status=pending');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.status'))->toBe('pending');
});

it('gets a specific order', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'vendor_id' => $vendor->id,
        'status' => OrderStatus::PENDING->value,
    ]);

    $response = $this->actingAs($user)->getJson("/api/orders/{$order->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'status',
                'total',
                'vendor',
                'customer',
                'payment',
                'items',
                'created_at',
                'updated_at',
            ],
        ])
        ->assertJsonPath('data.id', $order->id);
});
