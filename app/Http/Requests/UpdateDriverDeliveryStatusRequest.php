<?php

namespace App\Http\Requests;

use App\Models\Delivery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateDriverDeliveryStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isDriver() ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', Rule::in([
                Delivery::PICKING,
                Delivery::PACKED,
                Delivery::SHIPPED,
                Delivery::OUT_FOR_DELIVERY,
                Delivery::DELIVERED,
                Delivery::UNDELIVERABLE,
            ])],
            'progress_step' => ['nullable', 'integer', 'between:0,'.Delivery::MAX_DRIVER_PROGRESS_STEP],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $status = $this->input('status');
                $progressStep = $this->input('progress_step');

                if ($status === null && $progressStep === null) {
                    $validator->errors()->add('progress_step', 'A status or progress step is required.');
                }
            },
        ];
    }
}
