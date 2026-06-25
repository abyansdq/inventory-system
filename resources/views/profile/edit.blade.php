@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- Informasi Profil --}}
    <div class="card">
        <div class="mb-6">
            <h3 class="text-base font-semibold text-gray-800">Informasi Profil</h3>
            <p class="text-sm text-gray-500 mt-1">
                Perbarui nama, email, dan foto profil Anda.
            </p>
        </div>

        <form action="{{ route('profile.update') }}" method="POST"
              enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PATCH')

            {{-- Foto --}}
            <div x-data="{ preview: null }" class="flex items-center gap-6">
                <div class="relative flex-shrink-0">
                    <img :src="preview ?? '{{ $user->foto_url }}'"
                         alt="{{ $user->name }}"
                         class="w-20 h-20 rounded-full object-cover border-4 border-gray-200">
                    <label for="photo"
                           class="absolute -bottom-1 -right-1 w-7 h-7 bg-blue-600 text-white
                                  rounded-full flex items-center justify-center cursor-pointer
                                  hover:bg-blue-700 shadow transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </label>
                    <input type="file" id="photo" name="photo" accept="image/*"
                           class="hidden"
                           @change="preview = URL.createObjectURL($event.target.files[0])">
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">{{ $user->name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $user->role_label }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        Bergabung {{ $user->created_at->format('d F Y') }}
                    </p>
                </div>
            </div>

            @error('photo') <p class="form-error">{{ $message }}</p> @enderror

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name"
                           value="{{ old('name', $user->name) }}"
                           class="form-input @error('name') border-red-400 @enderror">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email"
                           value="{{ old('email', $user->email) }}"
                           class="form-input @error('email') border-red-400 @enderror">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone"
                           value="{{ old('phone', $user->phone) }}"
                           class="form-input" placeholder="08xx-xxxx-xxxx">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>

    {{-- Ganti Password --}}
    <div class="card">
        <div class="mb-6">
            <h3 class="text-base font-semibold text-gray-800">Keamanan Akun</h3>
            <p class="text-sm text-gray-500 mt-1">
                Pastikan akun Anda menggunakan password yang kuat dan unik.
            </p>
        </div>

        <form action="{{ route('profile.password') }}" method="POST" class="space-y-5">
            @csrf @method('PATCH')

            <div>
                <label class="form-label">Password Saat Ini <span class="text-red-500">*</span></label>
                <div class="relative" x-data="{ show: false }">
                    <input :type="show ? 'text' : 'password'"
                           name="current_password"
                           class="form-input pr-10 @error('current_password') border-red-400 @enderror"
                           placeholder="••••••••">
                    <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('current_password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="form-label">Password Baru <span class="text-red-500">*</span></label>
                    <div class="relative" x-data="{ show: false }">
                        <input :type="show ? 'text' : 'password'"
                               name="password"
                               class="form-input pr-10 @error('password') border-red-400 @enderror"
                               placeholder="Min 8 karakter">
                        <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation"
                           class="form-input" placeholder="Ulangi password baru">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Update Password</button>
            </div>
        </form>
    </div>

    {{-- Hapus Akun --}}
    <div class="card border-red-200" x-data="{ confirm: false }">
        <div class="mb-4">
            <h3 class="text-base font-semibold text-red-700">Hapus Akun</h3>
            <p class="text-sm text-gray-500 mt-1">
                Setelah akun dihapus, semua data dan resource akan dihapus secara permanen.
            </p>
        </div>

        <button @click="confirm = true" type="button" class="btn-danger text-sm">
            Hapus Akun Saya
        </button>

        <div x-show="confirm" x-transition
             class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
            <p class="text-sm text-red-700 font-medium mb-3">
                ⚠️ Konfirmasi hapus akun. Masukkan password Anda:
            </p>
            <form action="{{ route('profile.destroy') }}" method="POST" class="space-y-3">
                @csrf @method('DELETE')
                <input type="password" name="password"
                       class="form-input @error('password', 'userDeletion') border-red-400 @enderror"
                       placeholder="Password Anda">
                @error('password', 'userDeletion')
                    <p class="form-error">{{ $message }}</p>
                @enderror
                <div class="flex gap-2">
                    <button type="submit" class="btn-danger text-sm">
                        Ya, Hapus Akun
                    </button>
                    <button @click="confirm = false" type="button" class="btn-secondary text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection