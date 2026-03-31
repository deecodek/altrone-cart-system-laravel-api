<?php

declare(strict_types=1);

namespace App\Repository;

use App\Interfaces\VendorRepositoryInterface;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

class VendorRepository implements VendorRepositoryInterface
{
    protected Vendor $model;

    public function __construct(Vendor $vendor)
    {
        $this->model = $vendor;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 15): Paginator
    {
        return $this->model->paginate($perPage);
    }

    public function findOrFail(int $id): Vendor
    {
        return $this->model->findOrFail($id);
    }

    public function findByEmail(string $email): ?Vendor
    {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data): Vendor
    {
        return $this->model->create($data);
    }

    public function update(Vendor $vendor, array $data): Vendor
    {
        $vendor->update($data);

        return $vendor->fresh();
    }

    public function delete(Vendor $vendor): bool
    {
        return $vendor->delete();
    }
}
