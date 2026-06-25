<?php
// app/Http/Requests/Admin/CategoryRequest.php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('category')?->id;

        return [
            'nama_kategori' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'nama_kategori')
                    ->ignore($id)
                    ->whereNull('deleted_at'),
            ],
            'kode_kategori' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('categories', 'kode_kategori')
                    ->ignore($id)
                    ->whereNull('deleted_at'),
            ],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'is_active'  => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique'   => 'Nama kategori sudah ada.',
            'kode_kategori.unique'   => 'Kode kategori sudah ada.',
        ];
    }
}