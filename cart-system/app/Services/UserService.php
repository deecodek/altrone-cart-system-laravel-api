<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function list(int $perPage = 15): Paginator
    {
        try {
            Log::debug('UserService list called', ['per_page' => $perPage]);

            return Cache::remember("users.page.{$perPage}", 120, fn () => $this->userRepository->paginate($perPage));
        } catch (Throwable $exception) {
            Log::error('UserService list failed', ['per_page' => $perPage, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    public function get(int $id): User
    {
        try {
            Log::debug('UserService get called', ['id' => $id]);

            return Cache::remember("users.{$id}", 120, fn () => $this->userRepository->findOrFail($id));
        } catch (Throwable $exception) {
            Log::error('UserService get failed', ['id' => $id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    public function create(array $data): User
    {
        try {
            Log::info('UserService create called', ['email' => $data['email'] ?? null]);

            $user = DB::transaction(fn () => $this->userRepository->create($data));

            Cache::forget('users.page.15');
            Cache::forget("users.{$user->id}");

            Log::info('UserService create succeeded', ['user_id' => $user->id, 'email' => $user->email]);

            return $user;
        } catch (Throwable $exception) {
            Log::error('UserService create failed', ['data' => $data, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    public function update(User $user, array $data): User
    {
        try {
            Log::info('UserService update called', ['user_id' => $user->id]);

            $updated = DB::transaction(fn () => $this->userRepository->update($user, $data));

            Cache::forget('users.page.15');
            Cache::forget("users.{$user->id}");

            Log::info('UserService update succeeded', ['user_id' => $user->id]);

            return $updated;
        } catch (Throwable $exception) {
            Log::error('UserService update failed', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    public function delete(User $user): bool
    {
        try {
            Log::info('UserService delete called', ['user_id' => $user->id]);

            $deleted = DB::transaction(fn () => $this->userRepository->delete($user));

            Cache::forget('users.page.15');
            Cache::forget("users.{$user->id}");

            Log::info('UserService delete succeeded', ['user_id' => $user->id]);

            return $deleted;
        } catch (Throwable $exception) {
            Log::error('UserService delete failed', ['user_id' => $user->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }
}
