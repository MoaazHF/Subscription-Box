<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreClaimRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $delivery = $this->route('delivery');

        return $delivery?->address?->user_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:damaged,missing'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photo.mimes' => 'Photo must be a JPG, PNG, or WebP image.',
            'photo.max' => 'Photo may not be larger than 5MB.',
        ];
    }
}
