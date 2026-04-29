<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRetentionOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'offer_type' => ['required', 'string', 'in:discount,frequency_change,plan_downgrade'],
            'offer_value' => ['required', 'string', 'max:100'],
            'cancellation_reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}
