<?php

namespace App\Http\Requests;

use App\Models\Delivery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeliveryStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(Delivery::STATUSES)],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'estimated_delivery' => ['nullable', 'date'],
            'delivery_instructions' => ['nullable', 'string'],
            'eco_dispatch' => ['nullable', 'boolean'],
        ];
    }
}
