<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'size:2'],
            'is_serviceable' => ['nullable', 'boolean'],
        ];
    }
}
