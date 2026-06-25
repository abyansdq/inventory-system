<?php
// app/Http/Requests/Admin/StockInRequest.php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StockInRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'item_id'        => ['required', 'exists:items,id'],
            'supplier_id'    => ['required', 'exists:suppliers,id'],
            'procurement_id' => ['nullable', 'exists:procurements,id'],
            'qty'            => ['required', 'integer', 'min:1'],
            'harga_satuan'   => ['required', 'numeric', 'min:0'],
            'tanggal'        => ['required', 'date', 'before_or_equal:today'],
            'keterangan'     => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required'      => 'Barang wajib dipilih.',
            'supplier_id.required'  => 'Supplier wajib dipilih.',
            'qty.required'          => 'Jumlah wajib diisi.',
            'qty.min'               => 'Jumlah minimal 1.',
            'harga_satuan.required' => 'Harga satuan wajib diisi.',
            'tanggal.required'      => 'Tanggal wajib diisi.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'qty'          => (int) $this->qty,
            'harga_satuan' => (float) str_replace([',', '.'], ['', '.'], $this->harga_satuan ?? 0),
        ]);
    }
}