<?php

namespace App\Interfaces;

use App\Models\Product;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15): Paginator;

    public function findOrFail(int $id): Product;

    public function findForUpdate(int $id): Product;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;

    public function decrementStock(Product $product, int $quantity): Product;

    public function restoreStock(Product $product, int $quantity): Product;

    public function delete(Product $product): bool;
}
