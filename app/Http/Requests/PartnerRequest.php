<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:120'],
            'logo' => [$this->isMethod('POST') ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=5000,max_height=5000'],
            'submission_token' => [$this->isMethod('POST') ? 'required' : 'prohibited', 'uuid'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
