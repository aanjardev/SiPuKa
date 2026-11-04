<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SellCameraPosterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $image = ['image', 'mimes:png,jpg,jpeg,webp', 'max:2048', 'dimensions:max_width=5000,max_height=5000'];
        if ($this->isMethod('POST')) {
            return [
                'images' => ['required', 'array', 'min:1', 'max:5'],
                'images.*' => array_merge(['required'], $image),
                'alt_texts' => ['nullable', 'array', 'max:5'],
                'alt_texts.*' => ['nullable', 'string', 'max:255'],
                'submission_token' => ['required', 'uuid'],
            ];
        }

        return ['image' => array_merge(['nullable'], $image), 'alt_text' => ['nullable', 'string', 'max:255']];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $files = $this->file('images', []);
            if (is_array($files) && array_sum(array_map(fn ($file) => $file instanceof \Illuminate\Http\UploadedFile && $file->isValid() ? $file->getSize() : 0, $files)) > 6 * 1024 * 1024) {
                $validator->errors()->add('images', 'Total ukuran poster maksimal 6 MB per unggahan.');
            }
        });
    }
}
