<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $vendorGroups = $this->items->groupBy(fn ($item) => $item->product->vendor_id)->map(function ($items) {
            $vendor = $items->first()->product->vendor;

            return [
                'vendor' => new VendorResource($vendor),
                'items' => CartItemResource::collection($items),
                'subtotal' => $items->sum(fn ($item) => $item->quantity * $item->product->price),
            ];
        })->values();

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'vendors' => $vendorGroups,
            'total' => $this->items->sum(fn ($item) => $item->quantity * $item->product->price),
        ];
    }
}
