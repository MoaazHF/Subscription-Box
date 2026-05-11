<?php

namespace App\Http\Requests;

use App\Models\Delivery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDriverDeliveryStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isDriver() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in([
                Delivery::OUT_FOR_DELIVERY,
                Delivery::DELIVERED,
                Delivery::UNDELIVERABLE,
            ])],
        ];
    }
}
