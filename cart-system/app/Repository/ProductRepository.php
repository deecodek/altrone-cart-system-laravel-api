<?php

declare(strict_types=1);

namespace App\Repository;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    protected Product $model;

    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 15): Paginator
    {
        return $this->model->paginate($perPage);
    }

    public function findOrFail(int $id): Product
    {
        return $this->model->findOrFail($id);
    }

    public function findForUpdate(int $id): Product
    {
        return $this->model->lockForUpdate()->findOrFail($id);
    }

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh();
    }

    public function decrementStock(Product $product, int $quantity): Product
    {
        $product->decrement('stock', $quantity);

        return $product->refresh();
    }

    public function restoreStock(Product $product, int $quantity): Product
    {
        $product->increment('stock', $quantity);

        return $product->refresh();
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }
}
