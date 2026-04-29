<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseGiftSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
            'recipient_email' => ['required', 'email', 'max:255'],
            'recipient_name' => ['nullable', 'string', 'max:100'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:24'],
            'personal_message' => ['nullable', 'string'],
            'scheduled_send_at' => ['nullable', 'date', 'after_or_equal:now'],
        ];
    }
}
