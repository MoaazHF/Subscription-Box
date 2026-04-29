<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSocialPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'box_id' => ['required', 'uuid', 'exists:boxes,id'],
            'caption' => ['nullable', 'string'],
            'photo_url' => ['nullable', 'url', 'max:500'],
            'visibility' => ['nullable', 'string', 'in:public,private'],
        ];
    }
}
