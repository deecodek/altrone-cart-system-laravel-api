<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'vendor_id' => ['required', 'exists:vendors,id'],
            'stock' => ['required', 'integer', 'min:0'],
        ];
    }
}
