<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    /** Determine if the user is authorized to make this request. */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'weight_g' => ['required', 'integer', 'min:1'],
            'size_category' => ['required', 'string', 'in:small,medium,large'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'is_limited_edition' => ['boolean'],
            'limited_stock' => ['nullable', 'integer', 'min:0'],
            'supplier' => ['nullable', 'string', 'max:100'],
            'origin_country' => ['nullable', 'string', 'size:2'],
            'sourcing_notes' => ['nullable', 'string'],
            'is_addon' => ['boolean'],
            'allergen_tag_ids' => ['nullable', 'array'],
            'allergen_tag_ids.*' => ['integer', 'exists:allergen_tags,id'],
        ];
    }
}
