<?php

namespace App\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15): Paginator;

    public function findOrFail(int $id): User;

    public function findByEmail(string $email): ?User;

    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function delete(User $user): bool;
}
