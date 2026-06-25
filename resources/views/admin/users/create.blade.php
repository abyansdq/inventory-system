@extends('layouts.app')
@section('title', 'Tambah User')
@section('page-title', 'Tambah User')

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <form action="{{ route('admin.users.store') }}" method="POST"
              enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div x-data="{ preview: null }" class="flex flex-col items-center gap-3 pb-4 border-b">
                <div class="relative">
                    <img :src="preview ?? '{{ asset('images/default-avatar.png') }}'"
                         alt="Preview"
                         class="w-24 h-24 rounded-full object-cover border-4 border-gray-200">
                    <label for="photo"
                           class="absolute -bottom-1 -right-1 w-8 h-8 bg-blue-600 text-white
                                  rounded-full flex items-center justify-center cursor-pointer
                                  hover:bg-blue-700 transition shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </label>
                </div>
                <input type="file" id="photo" name="photo" accept="image/*"
                       class="hidden"
                       @change="preview = URL.createObjectURL($event.target.files[0])">
                <p class="text-xs text-gray-400">Klik ikon kamera untuk upload foto</p>
                @error('photo') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-input @error('name') border-red-400 @enderror"
                           placeholder="Nama lengkap user">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-input @error('email') border-red-400 @enderror"
                           placeholder="email@example.com">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="form-input" placeholder="08xx-xxxx-xxxx">
                </div>

                <div>
                    <label class="form-label">Password <span class="text-red-500">*</span></label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'"
                               name="password"
                               class="form-input pr-10 @error('password') border-red-400 @enderror"
                               placeholder="Min 8 karakter">
                        <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Role <span class="text-red-500">*</span></label>
                    <select name="role" class="form-select @error('role') border-red-400 @enderror">
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}"
                                    {{ old('role') === $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       class="w-4 h-4 text-blue-600 rounded"
                       {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                <label for="is_active" class="text-sm text-gray-700">User Aktif</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">Simpan User</button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection