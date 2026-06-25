<?php
// app/Http/Requests/User/ItemRequestFormRequest.php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequestFormRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'item_id'       => ['required', 'exists:items,id'],
            'qty'           => ['required', 'integer', 'min:1'],
            'tanggal_butuh' => ['nullable', 'date', 'after_or_equal:today'],
            'keperluan'     => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required'       => 'Barang wajib dipilih.',
            'qty.required'           => 'Jumlah wajib diisi.',
            'qty.min'                => 'Jumlah minimal 1.',
            'keperluan.required'     => 'Keperluan wajib diisi.',
            'tanggal_butuh.after_or_equal' => 'Tanggal dibutuhkan tidak boleh sebelum hari ini.',
        ];
    }
}