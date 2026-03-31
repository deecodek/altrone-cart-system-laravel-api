<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('checks if registration works', function () {
    $payload = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'device_name' => 'test-device',
    ];

    $response = $this->postJson('/api/register', $payload);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'message',
                'user' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at'],
                'access_token',
                'token_type',
            ],
        ]);

    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
});

it('checks if login works', function () {
    $user = User::factory()->create([
        'email' => 'login@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123',
        'device_name' => 'test-device',
    ]);

    $response->assertOk();

    expect($response->json('data.user.email'))->toBe($user->email);
    expect($response->json('data.access_token'))->not->toBeEmpty();
});

it('checks that login fails with invalid credentials', function () {
    User::factory()->create([
        'email' => 'invalid@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->postJson('/api/login', [
        'email' => 'invalid@example.com',
        'password' => 'wrong-password',
        'device_name' => 'test-device',
    ])
        ->assertUnauthorized()
        ->assertJson(['message' => 'Invalid credentials']);
});
