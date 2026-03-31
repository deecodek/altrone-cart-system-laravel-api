<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => 'Login successful!',
            'user' => new UserResource($this->resource['user']),
            'access_token' => $this->resource['token'],
            'token_type' => 'Bearer',
        ];
    }
}
