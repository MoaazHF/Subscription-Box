<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
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
            'start_date' => ['required', 'date'],
            'auto_renew' => ['nullable', 'boolean'],
            'eco_shipping' => ['nullable', 'boolean'],
            'payment_gateway_status' => ['required', 'in:success,failed'],
            'payment_gateway_ref' => ['required', 'string', 'max:100'],
            'payment_card_last4' => ['required', 'digits:4'],
            'payment_gateway_reason' => ['required', 'string', 'max:50'],
        ];
    }
}
