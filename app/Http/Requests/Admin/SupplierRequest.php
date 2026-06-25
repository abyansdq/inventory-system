<?php
// app/Http/Requests/Admin/SupplierRequest.php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('supplier')?->id;

        return [
            'kode_supplier'  => [
                'required', 'string', 'max:20',
                Rule::unique('suppliers', 'kode_supplier')
                    ->ignore($id)->whereNull('deleted_at'),
            ],
            'nama_supplier'  => ['required', 'string', 'max:150'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'email'          => [
                'nullable', 'email', 'max:100',
                Rule::unique('suppliers', 'email')
                    ->ignore($id)->whereNull('deleted_at'),
            ],
            'telepon'        => ['nullable', 'string', 'max:20'],
            'alamat'         => ['nullable', 'string', 'max:500'],
            'kota'           => ['nullable', 'string', 'max:100'],
            'keterangan'     => ['nullable', 'string', 'max:500'],
            'is_active'      => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_supplier.required' => 'Kode supplier wajib diisi.',
            'kode_supplier.unique'   => 'Kode supplier sudah digunakan.',
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'email.unique'           => 'Email sudah digunakan supplier lain.',
        ];
    }
}