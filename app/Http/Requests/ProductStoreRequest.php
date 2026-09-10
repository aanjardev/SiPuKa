<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_produk'      => ['required', 'string', 'max:200'],
            'kode_sku'         => ['required', 'string', 'max:20', 'unique:produk,kode_sku'],
            'id_kategori'      => ['required', 'exists:kategori,id'],
            'harga_jual'       => ['nullable', 'integer', 'min:0'],
            'harga_beli'       => ['nullable', 'integer', 'min:0'],
            'harga_servis'     => ['nullable', 'integer', 'min:0'],
            'stok_produk'      => ['nullable', 'integer', 'min:0'],
            'deskripsi_produk' => ['nullable', 'string'],
            'instagram_link'   => ['nullable', 'url', 'max:255'],
            'status'           => ['required', 'in:Second,Baru'],
            'grade'            => ['required', 'in:Unggulan,Standar,Minus'],
            'is_visible'       => ['required', 'boolean'],
            'images'           => ['nullable', 'array'],
            'images.*'         => ['image', 'max:10240'],
            'main_image'       => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_sku.required' => 'Kode SKU wajib diisi.',
            'kode_sku.unique'   => 'Kode SKU sudah digunakan. Silakan gunakan kode SKU yang berbeda.',
        ];
    }
}
