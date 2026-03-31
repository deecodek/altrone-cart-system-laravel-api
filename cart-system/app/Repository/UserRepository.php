<?php

declare(strict_types=1);

namespace App\Repository;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    protected User $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 15): Paginator
    {
        return $this->model->paginate($perPage);
    }

    public function findOrFail(int $id): User
    {
        return $this->model->findOrFail($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return $this->model->create([
            ...$data,
            'password' => bcrypt($data['password'] ?? ''),
        ]);
    }

    public function update(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return $user->fresh();
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
