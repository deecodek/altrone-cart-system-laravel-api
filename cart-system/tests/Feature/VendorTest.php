<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists all vendors', function () {
    $user = User::factory()->create();
    Vendor::factory()->count(5)->create();

    $response = $this->actingAs($user)->getJson('/api/vendors');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'email', 'slug', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ]);
});

it('creates a new vendor', function () {
    $user = User::factory()->create();

    $payload = [
        'name' => 'New Vendor',
        'email' => 'newvendor@example.com',
        'slug' => 'new-vendor',
    ];

    $response = $this->actingAs($user)->postJson('/api/vendors', $payload);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'slug', 'created_at', 'updated_at'],
        ]);

    expect(Vendor::where('email', 'newvendor@example.com')->exists())->toBeTrue();
});

it('gets a specific vendor', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create();

    $response = $this->actingAs($user)->getJson("/api/vendors/{$vendor->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'slug', 'created_at', 'updated_at'],
        ])
        ->assertJsonPath('data.id', $vendor->id);
});

it('updates a vendor', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create();

    $payload = [
        'name' => 'Updated Vendor',
        'email' => 'updated@example.com',
        'slug' => 'updated-vendor',
    ];

    $response = $this->actingAs($user)->putJson("/api/vendors/{$vendor->id}", $payload);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Updated Vendor')
        ->assertJsonPath('data.email', 'updated@example.com');

    expect(Vendor::find($vendor->id)->name)->toBe('Updated Vendor');
});

it('deletes a vendor', function () {
    $user = User::factory()->create();
    $vendor = Vendor::factory()->create();

    $response = $this->actingAs($user)->deleteJson("/api/vendors/{$vendor->id}");

    $response->assertNoContent();

    expect(Vendor::find($vendor->id))->toBeNull();
});
