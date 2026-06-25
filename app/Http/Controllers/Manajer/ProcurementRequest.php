<?php
// app/Http/Requests/Manajer/ProcurementRequest.php

namespace App\Http\Requests\Manajer;

use Illuminate\Foundation\Http\FormRequest;

class ProcurementRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'item_id'            => ['required', 'exists:items,id'],
            'supplier_id'        => ['required', 'exists:suppliers,id'],
            'qty'                => ['required', 'integer', 'min:1'],
            'harga_satuan'       => ['required', 'numeric', 'min:0'],
            'tanggal'            => ['required', 'date'],
            'tanggal_dibutuhkan' => ['nullable', 'date', 'after_or_equal:tanggal'],
            'catatan'            => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required'      => 'Barang wajib dipilih.',
            'supplier_id.required'  => 'Supplier wajib dipilih.',
            'qty.required'          => 'Jumlah wajib diisi.',
            'harga_satuan.required' => 'Harga satuan wajib diisi.',
            'tanggal.required'      => 'Tanggal wajib diisi.',
            'tanggal_dibutuhkan.after_or_equal' =>
                'Tanggal dibutuhkan harus setelah tanggal pengadaan.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Cast tipe data saja, JANGAN tambah total_harga di sini
        $this->merge([
            'qty'          => (int) $this->qty,
            'harga_satuan' => (float) str_replace(
                [',', '.'], ['', '.'],
                $this->harga_satuan ?? 0
            ),
        ]);
    }
}