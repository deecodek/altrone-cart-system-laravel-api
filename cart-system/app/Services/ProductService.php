<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductService
{
    protected ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function list(int $perPage = 15): Paginator
    {
        try {
            Log::debug('ProductService list called', ['per_page' => $perPage]);

            return Cache::remember("products.page.{$perPage}", 120, fn () => $this->productRepository->paginate($perPage));
        } catch (Throwable $exception) {
            Log::error('ProductService list failed', ['per_page' => $perPage, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    public function get(int $id): Product
    {
        try {
            Log::debug('ProductService get called', ['id' => $id]);

            return Cache::remember("products.{$id}", 120, fn () => $this->productRepository->findOrFail($id));
        } catch (Throwable $exception) {
            Log::error('ProductService get failed', ['id' => $id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    public function create(array $data): Product
    {
        try {
            Log::info('ProductService create called', ['name' => $data['name'] ?? null, 'vendor_id' => $data['vendor_id'] ?? null]);

            $product = DB::transaction(fn () => $this->productRepository->create($data));

            Cache::forget('products.page.15');
            Cache::forget("products.{$product->id}");

            Log::info('ProductService create succeeded', ['product_id' => $product->id, 'name' => $product->name]);

            return $product;
        } catch (Throwable $exception) {
            Log::error('ProductService create failed', ['data' => $data, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    public function update(Product $product, array $data): Product
    {
        try {
            Log::info('ProductService update called', ['product_id' => $product->id]);

            $updated = DB::transaction(fn () => $this->productRepository->update($product, $data));

            Cache::forget('products.page.15');
            Cache::forget("products.{$product->id}");

            Log::info('ProductService update succeeded', ['product_id' => $product->id]);

            return $updated;
        } catch (Throwable $exception) {
            Log::error('ProductService update failed', ['product_id' => $product->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    public function delete(Product $product): bool
    {
        try {
            Log::info('ProductService delete called', ['product_id' => $product->id]);

            $deleted = DB::transaction(fn () => $this->productRepository->delete($product));

            Cache::forget('products.page.15');
            Cache::forget("products.{$product->id}");

            Log::info('ProductService delete succeeded', ['product_id' => $product->id]);

            return $deleted;
        } catch (Throwable $exception) {
            Log::error('ProductService delete failed', ['product_id' => $product->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }
}
