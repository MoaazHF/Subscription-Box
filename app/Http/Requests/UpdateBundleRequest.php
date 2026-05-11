<?php

namespace App\Http\Requests;

use App\Models\Bundle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBundleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        /** @var Bundle $bundle */
        $bundle = $this->route('bundle');

        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('bundles', 'name')->ignore($bundle)],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['required', 'uuid', 'exists:items,id', 'distinct'],
            'quantities' => ['required', 'array'],
            'quantities.*' => ['required', 'integer', 'min:1', 'max:25'],
        ];
    }
}
