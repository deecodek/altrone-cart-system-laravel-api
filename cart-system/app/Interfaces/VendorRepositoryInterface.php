<?php

namespace App\Interfaces;

use App\Models\Vendor;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

interface VendorRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15): Paginator;

    public function findOrFail(int $id): Vendor;

    public function findByEmail(string $email): ?Vendor;

    public function create(array $data): Vendor;

    public function update(Vendor $vendor, array $data): Vendor;

    public function delete(Vendor $vendor): bool;
}
