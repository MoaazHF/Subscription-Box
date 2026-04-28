<?php

namespace App\Http\Requests;

use App\Models\Delivery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeliveryStatusUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Delivery $delivery */
        $delivery = $this->route('delivery');

        return $delivery->address?->user_id === $this->user()?->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Delivery $delivery */
        $delivery = $this->route('delivery');

        $allowedNextStatuses = Delivery::STATUS_TRANSITIONS[$delivery->status] ?? [];

        return [
            'status' => [
                'required',
                'string',
                Rule::in($allowedNextStatuses),
            ],
            'delivery_instructions' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.in' => 'The requested status transition is not allowed.',
        ];
    }
}
