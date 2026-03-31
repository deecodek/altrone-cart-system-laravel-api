<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists all users', function () {
    $user = User::factory()->create();
    User::factory()->count(5)->create();

    $response = $this->actingAs($user)->getJson('/api/users');

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ]);
});

it('creates a new user', function () {
    $user = User::factory()->create();

    $payload = [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'role' => 'customer',
    ];

    $response = $this->actingAs($user)->postJson('/api/users', $payload);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at'],
        ]);

    expect(User::where('email', 'newuser@example.com')->exists())->toBeTrue();
});

it('gets a specific user', function () {
    $authenticatedUser = User::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($authenticatedUser)->getJson("/api/users/{$user->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at'],
        ])
        ->assertJsonPath('data.id', $user->id);
});

it('updates a user', function () {
    $authenticatedUser = User::factory()->create();
    $user = User::factory()->create();

    $payload = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ];

    $response = $this->actingAs($authenticatedUser)->putJson("/api/users/{$user->id}", $payload);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Updated Name')
        ->assertJsonPath('data.email', 'updated@example.com');

    expect(User::find($user->id)->name)->toBe('Updated Name');
});

it('deletes a user', function () {
    $authenticatedUser = User::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($authenticatedUser)->deleteJson("/api/users/{$user->id}");

    $response->assertNoContent();

    expect(User::find($user->id))->toBeNull();
});
