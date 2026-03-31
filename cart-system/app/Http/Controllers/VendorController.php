<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Vendor\DeleteVendorRequest;
use App\Http\Requests\Vendor\IndexVendorRequest;
use App\Http\Requests\Vendor\ShowVendorRequest;
use App\Http\Requests\Vendor\StoreVendorRequest;
use App\Http\Requests\Vendor\UpdateVendorRequest;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use App\Services\VendorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class VendorController extends Controller
{
    protected VendorService $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    public function index(IndexVendorRequest $request)
    {
        try {
            $perPage = $request->input('per_page', 15);

            Log::info('Vendors listed', [
                'per_page' => $perPage,
                'requested_by' => auth()->id(),
            ]);

            return VendorResource::collection(
                $this->vendorService->list((int) $perPage)
            );
        } catch (Throwable $exception) {
            Log::error('VendorController index failed', [
                'requested_by' => auth()->id(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function store(StoreVendorRequest $request): VendorResource
    {
        try {
            $vendor = $this->vendorService->create($request->validated());

            Log::info('Vendor created', [
                'vendor_id' => $vendor->id,
                'email' => $vendor->email,
                'created_by' => auth()->id(),
            ]);

            return new VendorResource($vendor);
        } catch (Throwable $exception) {
            Log::error('VendorController store failed', [
                'data' => $request->validated(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function show(ShowVendorRequest $request, Vendor $vendor): VendorResource
    {
        try {
            Log::info('Vendor retrieved', [
                'vendor_id' => $vendor->id,
                'requested_by' => auth()->id(),
            ]);

            return new VendorResource($this->vendorService->get($vendor->id));
        } catch (Throwable $exception) {
            Log::error('VendorController show failed', [
                'vendor_id' => $vendor->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function update(UpdateVendorRequest $request, Vendor $vendor): VendorResource
    {
        try {
            $updatedVendor = $this->vendorService->update($vendor, $request->validated());

            Log::info('Vendor updated', [
                'vendor_id' => $vendor->id,
                'updated_by' => auth()->id(),
            ]);

            return new VendorResource($updatedVendor);
        } catch (Throwable $exception) {
            Log::error('VendorController update failed', [
                'vendor_id' => $vendor->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function destroy(DeleteVendorRequest $request, Vendor $vendor): JsonResponse
    {
        try {
            $this->vendorService->delete($vendor);

            Log::info('Vendor deleted', [
                'vendor_id' => $vendor->id,
                'deleted_by' => auth()->id(),
            ]);

            return response()->json([], 204);
        } catch (Throwable $exception) {
            Log::error('VendorController destroy failed', [
                'vendor_id' => $vendor->id,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
