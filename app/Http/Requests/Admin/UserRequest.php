<?php
// app/Http/Requests/Admin/UserRequest.php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id       = $this->route('user')?->id;
        $isCreate = $this->isMethod('POST');

        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'password'  => $isCreate
                ? ['required', Password::min(8)->letters()->numbers()]
                : ['nullable', Password::min(8)->letters()->numbers()],
            'role'      => ['required', Rule::in(['admin', 'manajer', 'user'])],
            'phone'     => ['nullable', 'string', 'max:20'],
            'photo'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'role.required'     => 'Role wajib dipilih.',
            'role.in'           => 'Role tidak valid.',
        ];
    }
}