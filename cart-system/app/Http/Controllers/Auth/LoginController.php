<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class LoginController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function __invoke(LoginRequest $request): LoginResource|JsonResponse
    {
        $credentials = $request->validated();

        try {
            $auth = $this->authService->login($credentials);

            return new LoginResource($auth);
        } catch (AuthenticationException $exception) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        } catch (Throwable $exception) {
            Log::error('LoginController failed', [
                'credentials' => [
                    'email' => $credentials['email'] ?? null,
                    'device_name' => $credentials['device_name'] ?? null,
                ],
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
