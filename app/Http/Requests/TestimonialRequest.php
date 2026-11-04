<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class TestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:120'],
            'review_text' => ['required', 'string', 'max:2000'],
            'source_name' => ['required', 'string', 'max:80'],
            'source_url' => ['required', 'url:http,https', 'max:2048'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'reviewed_at' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'screenshot' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048', 'dimensions:max_width=5000,max_height=5000'],
            'remove_screenshot' => ['nullable', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'submission_token' => [$this->isMethod('POST') ? 'required' : 'prohibited', 'uuid'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->boolean('remove_screenshot') && $this->hasFile('screenshot')) {
                $validator->errors()->add('screenshot', 'Pilih ganti screenshot atau hapus screenshot, bukan keduanya.');
            }
        });
    }

    public function attributes(): array
    {
        return ['customer_name' => 'nama pelanggan', 'review_text' => 'isi ulasan', 'source_name' => 'nama sumber', 'source_url' => 'tautan sumber', 'reviewed_at' => 'tanggal ulasan'];
    }
}
