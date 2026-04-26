<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SwapItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'remove_box_item_id' => ['required', 'uuid', 'exists:box_items,id'],
            'new_item_id' => ['required', 'uuid', 'exists:items,id'],
            'confirm_allergen' => ['nullable', 'boolean'],
        ];
    }
}
