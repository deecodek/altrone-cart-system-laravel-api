<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuthService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(array $credentials): array
    {
        try {
            Log::info('AuthService login attempt', [
                'email' => $credentials['email'] ?? null,
                'device_name' => $credentials['device_name'] ?? null,
            ]);

            $user = DB::transaction(fn () => $this->userRepository->findByEmail($credentials['email'] ?? ''));

            if (! $user || ! Hash::check($credentials['password'] ?? '', $user->password)) {
                Log::warning('AuthService login failed - invalid credentials', [
                    'email' => $credentials['email'] ?? null,
                ]);

                throw new AuthenticationException('Invalid credentials');
            }

            $token = $user->createToken($credentials['device_name'] ?? 'api-token')->plainTextToken;

            Log::info('AuthService login succeeded', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return ['user' => $user, 'token' => $token];
        } catch (AuthenticationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('AuthService login failed', [
                'credentials' => [
                    'email' => $credentials['email'] ?? null,
                    'device_name' => $credentials['device_name'] ?? null,
                ],
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            throw $exception;
        }
    }

    public function register(array $data): array
    {
        try {
            Log::info('AuthService register attempt', [
                'email' => $data['email'] ?? null,
                'device_name' => $data['device_name'] ?? null,
            ]);

            $user = DB::transaction(fn () => $this->userRepository->create($data));

            $token = $user->createToken($data['device_name'] ?? 'api-token')->plainTextToken;

            Log::info('AuthService register succeeded', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return ['user' => $user, 'token' => $token];
        } catch (Throwable $exception) {
            Log::error('AuthService register failed', [
                'data' => [
                    'email' => $data['email'] ?? null,
                    'device_name' => $data['device_name'] ?? null,
                ],
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            throw $exception;
        }
    }
}
