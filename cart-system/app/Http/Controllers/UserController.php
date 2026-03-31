<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\User\DeleteUserRequest;
use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\ShowUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(IndexUserRequest $request)
    {
        try {
            $perPage = $request->input('per_page', 15);

            Log::info('Users listed', [
                'per_page' => $perPage,
                'requested_by' => auth()->id(),
            ]);

            return UserResource::collection(
                $this->userService->list((int) $perPage)
            );
        } catch (Throwable $exception) {
            Log::error('UserController index failed', [
                'requested_by' => auth()->id(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function store(StoreUserRequest $request): UserResource
    {
        try {
            $user = $this->userService->create($request->validated());

            Log::info('User created', [
                'user_id' => $user->id,
                'email' => $user->email,
                'created_by' => auth()->id(),
            ]);

            return new UserResource($user);
        } catch (Throwable $exception) {
            Log::error('UserController store failed', [
                'data' => $request->validated(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function show(ShowUserRequest $request, User $user): UserResource
    {
        try {
            Log::info('User retrieved', [
                'user_id' => $user->id,
                'requested_by' => auth()->id(),
            ]);

            return new UserResource($this->userService->get($user->id));
        } catch (Throwable $exception) {
            Log::error('UserController show failed', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        try {
            $updatedUser = $this->userService->update($user, $request->validated());

            Log::info('User updated', [
                'user_id' => $user->id,
                'updated_by' => auth()->id(),
            ]);

            return new UserResource($updatedUser);
        } catch (Throwable $exception) {
            Log::error('UserController update failed', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function destroy(DeleteUserRequest $request, User $user): JsonResponse
    {
        try {
            $this->userService->delete($user);

            Log::info('User deleted', [
                'user_id' => $user->id,
                'deleted_by' => auth()->id(),
            ]);

            return response()->json([], 204);
        } catch (Throwable $exception) {
            Log::error('UserController destroy failed', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
