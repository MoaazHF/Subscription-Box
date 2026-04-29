<?php

namespace App\Http\Requests;

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
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'weight_g' => ['required', 'integer', 'min:1'],
            'size_category' => ['required', 'in:small,medium,large'],
            'unit_price' => ['required', 'numeric', 'min:0.01'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'is_limited_edition' => ['nullable', 'boolean'],
            'limited_stock' => ['nullable', 'integer', 'min:1', 'required_if:is_limited_edition,1'],
            'supplier' => ['nullable', 'string', 'max:100'],
            'origin_country' => ['nullable', 'string', 'size:2'],
            'sourcing_notes' => ['nullable', 'string'],
            'is_addon' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ];
    }
}
