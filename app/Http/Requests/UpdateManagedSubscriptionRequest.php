<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateManagedSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
            'address_id' => ['required', 'uuid', 'exists:addresses,id'],
            'status' => ['required', 'in:active,paused,cancelled,suspended,gift'],
            'auto_renew' => ['nullable', 'boolean'],
            'eco_shipping' => ['nullable', 'boolean'],
        ];
    }
}
