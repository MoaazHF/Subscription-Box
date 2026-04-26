<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'street' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'size:2'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }
}
