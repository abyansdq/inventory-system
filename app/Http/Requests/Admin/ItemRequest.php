<?php
// app/Http/Requests/Admin/ItemRequest.php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('item')?->id;

        return [
            'kode_barang'   => [
                'required', 'string', 'max:50',
                Rule::unique('items', 'kode_barang')
                    ->ignore($id)->whereNull('deleted_at'),
            ],
            'nama_barang'   => ['required', 'string', 'max:200'],
            'category_id'   => ['required', 'exists:categories,id'],
            'supplier_id'   => ['required', 'exists:suppliers,id'],
            'satuan'        => ['required', 'string', 'max:50'],
            'stok'          => ['required', 'integer', 'min:0'],
            'stok_minimum'  => ['required', 'integer', 'min:0'],
            'safety_stock'  => ['required', 'integer', 'min:0'],
            'harga_beli'    => ['required', 'numeric', 'min:0'],
            'harga_jual'    => ['required', 'numeric', 'min:0'],
            'ordering_cost' => ['required', 'numeric', 'min:0'],
            'holding_cost'  => ['required', 'numeric', 'min:0'],
            'lead_time'     => ['required', 'integer', 'min:1'],
            'foto'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'deskripsi'     => ['nullable', 'string', 'max:1000'],
            'is_active'     => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_barang.required'   => 'Kode barang wajib diisi.',
            'kode_barang.unique'     => 'Kode barang sudah digunakan.',
            'nama_barang.required'   => 'Nama barang wajib diisi.',
            'category_id.required'   => 'Kategori wajib dipilih.',
            'category_id.exists'     => 'Kategori tidak valid.',
            'supplier_id.required'   => 'Supplier wajib dipilih.',
            'satuan.required'        => 'Satuan wajib diisi.',
            'harga_beli.required'    => 'Harga beli wajib diisi.',
            'ordering_cost.required' => 'Biaya pemesanan wajib diisi.',
            'holding_cost.required'  => 'Biaya penyimpanan wajib diisi.',
            'lead_time.required'     => 'Lead time wajib diisi.',
            'lead_time.min'          => 'Lead time minimal 1 hari.',
            'foto.image'             => 'File harus berupa gambar.',
            'foto.max'               => 'Ukuran foto maksimal 2MB.',
        ];
    }
}