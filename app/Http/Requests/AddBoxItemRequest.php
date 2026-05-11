<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddBoxItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'new_item_id' => ['required', 'uuid', 'exists:items,id'],
            'confirm_allergen' => ['nullable', 'boolean'],
        ];
    }
}
