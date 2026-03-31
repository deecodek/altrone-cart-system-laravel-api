<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Product\DeleteProductRequest;
use App\Http\Requests\Product\IndexProductRequest;
use App\Http\Requests\Product\ShowProductRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(IndexProductRequest $request)
    {
        try {
            $perPage = $request->input('per_page', 15);

            Log::info('Products listed', [
                'per_page' => $perPage,
                'requested_by' => auth()->id(),
            ]);

            return ProductResource::collection(
                $this->productService->list((int) $perPage)
            );
        } catch (Throwable $exception) {
            Log::error('ProductController index failed', [
                'requested_by' => auth()->id(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function store(StoreProductRequest $request): ProductResource
    {
        try {
            $product = $this->productService->create($request->validated());

            Log::info('Product created', [
                'product_id' => $product->id,
                'name' => $product->name,
                'created_by' => auth()->id(),
            ]);

            return new ProductResource($product);
        } catch (Throwable $exception) {
            Log::error('ProductController store failed', [
                'data' => $request->validated(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function show(ShowProductRequest $request, Product $product): ProductResource
    {
        try {
            Log::info('Product retrieved', [
                'product_id' => $product->id,
                'requested_by' => auth()->id(),
            ]);

            return new ProductResource($this->productService->get($product->id));
        } catch (Throwable $exception) {
            Log::error('ProductController show failed', [
                'product_id' => $product->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        try {
            $updatedProduct = $this->productService->update($product, $request->validated());

            Log::info('Product updated', [
                'product_id' => $product->id,
                'updated_by' => auth()->id(),
            ]);

            return new ProductResource($updatedProduct);
        } catch (Throwable $exception) {
            Log::error('ProductController update failed', [
                'product_id' => $product->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function destroy(DeleteProductRequest $request, Product $product): JsonResponse
    {
        try {
            $this->productService->delete($product);

            Log::info('Product deleted', [
                'product_id' => $product->id,
                'deleted_by' => auth()->id(),
            ]);

            return response()->json([], 204);
        } catch (Throwable $exception) {
            Log::error('ProductController destroy failed', [
                'product_id' => $product->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
