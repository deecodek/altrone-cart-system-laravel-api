<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\VendorRepositoryInterface;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class VendorService
{
    protected VendorRepositoryInterface $vendorRepository;

    public function __construct(VendorRepositoryInterface $vendorRepository)
    {
        $this->vendorRepository = $vendorRepository;
    }

    public function list(int $perPage = 15): Paginator
    {
        try {
            Log::debug('VendorService list called', ['per_page' => $perPage]);

            return Cache::remember("vendors.page.{$perPage}", 120, fn () => $this->vendorRepository->paginate($perPage));
        } catch (Throwable $exception) {
            Log::error('VendorService list failed', ['per_page' => $perPage, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    public function get(int $id): Vendor
    {
        try {
            Log::debug('VendorService get called', ['id' => $id]);

            return Cache::remember("vendors.{$id}", 120, fn () => $this->vendorRepository->findOrFail($id));
        } catch (Throwable $exception) {
            Log::error('VendorService get failed', ['id' => $id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    public function create(array $data): Vendor
    {
        try {
            Log::info('VendorService create called', ['email' => $data['email'] ?? null]);

            $vendor = DB::transaction(fn () => $this->vendorRepository->create($data));

            Cache::forget('vendors.page.15');
            Cache::forget("vendors.{$vendor->id}");

            Log::info('VendorService create succeeded', ['vendor_id' => $vendor->id, 'email' => $vendor->email]);

            return $vendor;
        } catch (Throwable $exception) {
            Log::error('VendorService create failed', ['data' => $data, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    public function update(Vendor $vendor, array $data): Vendor
    {
        try {
            Log::info('VendorService update called', ['vendor_id' => $vendor->id]);

            $updated = DB::transaction(fn () => $this->vendorRepository->update($vendor, $data));

            Cache::forget('vendors.page.15');
            Cache::forget("vendors.{$vendor->id}");

            Log::info('VendorService update succeeded', ['vendor_id' => $vendor->id]);

            return $updated;
        } catch (Throwable $exception) {
            Log::error('VendorService update failed', ['vendor_id' => $vendor->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    public function delete(Vendor $vendor): bool
    {
        try {
            Log::info('VendorService delete called', ['vendor_id' => $vendor->id]);

            $deleted = DB::transaction(fn () => $this->vendorRepository->delete($vendor));

            Cache::forget('vendors.page.15');
            Cache::forget("vendors.{$vendor->id}");

            Log::info('VendorService delete succeeded', ['vendor_id' => $vendor->id]);

            return $deleted;
        } catch (Throwable $exception) {
            Log::error('VendorService delete failed', ['vendor_id' => $vendor->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }
}
