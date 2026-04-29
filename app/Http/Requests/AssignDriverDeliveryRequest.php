<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignDriverDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delivery_id' => ['required', 'uuid', 'exists:deliveries,id'],
        ];
    }
}
