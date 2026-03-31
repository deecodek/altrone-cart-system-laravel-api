<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vendorId = $this->route('vendor')?->id;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:vendors,email,'.($vendorId ?? 'NULL')],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:vendors,slug,'.($vendorId ?? 'NULL')],
        ];
    }
}
