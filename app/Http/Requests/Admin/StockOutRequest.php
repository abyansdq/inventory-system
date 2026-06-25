<?php
// app/Http/Requests/Admin/StockOutRequest.php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StockOutRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'item_id'         => ['required', 'exists:items,id'],
            'item_request_id' => ['nullable', 'exists:item_requests,id'],
            'qty'             => ['required', 'integer', 'min:1'],
            'tanggal'         => ['required', 'date', 'before_or_equal:today'],
            'keterangan'      => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'Barang wajib dipilih.',
            'qty.required'     => 'Jumlah wajib diisi.',
            'qty.min'          => 'Jumlah minimal 1.',
            'tanggal.required' => 'Tanggal wajib diisi.',
        ];
    }
}