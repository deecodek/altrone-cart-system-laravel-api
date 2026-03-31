<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendor_id' => ['sometimes', 'integer', 'exists:vendors,id'],
            'customer_id' => ['sometimes', 'integer', 'exists:users,id'],
            'status' => ['sometimes', 'string', 'in:pending,paid,canceled'],
            'per_page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
