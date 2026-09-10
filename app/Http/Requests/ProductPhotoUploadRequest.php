<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductPhotoUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'images'     => ['sometimes', 'array', 'min:1'],
            'images.*'   => ['image', 'max:10240'],
            'main_image' => ['nullable', 'string'],
        ];
    }
}
