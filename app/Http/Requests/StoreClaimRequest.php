<?php

namespace App\Http\Requests;

use App\Models\Delivery;
use Illuminate\Foundation\Http\FormRequest;

class StoreClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Delivery|null $delivery */
        $delivery = $this->route('delivery');

        return $this->user() !== null
            && $delivery !== null
            && ($this->user()->isAdmin() || $delivery->belongsToUser($this->user()));
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:damaged,missing'],
            'item_id' => ['nullable', 'exists:items,id'],
            'description' => ['required', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
