<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Http\Resources\RegistrationResource;
use App\Services\AuthService;
use Illuminate\Support\Facades\Log;
use Throwable;

class RegisterationController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function __invoke(RegistrationRequest $request): RegistrationResource
    {
        $requestData = $request->validated();

        try {
            $registeredUser = $this->authService->register($requestData);

            return new RegistrationResource($registeredUser);
        } catch (Throwable $exception) {
            Log::error('RegistrationController failed', [
                'data' => [
                    'email' => $requestData['email'] ?? null,
                    'device_name' => $requestData['device_name'] ?? null,
                ],
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
