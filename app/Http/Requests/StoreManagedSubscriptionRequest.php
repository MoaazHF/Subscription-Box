<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManagedSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'uuid', 'exists:users,id'],
            'plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
            'address_id' => ['required', 'uuid', 'exists:addresses,id'],
            'start_date' => ['required', 'date'],
            'auto_renew' => ['nullable', 'boolean'],
            'eco_shipping' => ['nullable', 'boolean'],
        ];
    }
}
