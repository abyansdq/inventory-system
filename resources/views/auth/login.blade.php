<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        {{-- Logo & Title --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">{{ config('app.name') }}</h1>
            <p class="text-gray-400 text-sm mt-1">Sistem Manajemen Stok Gudang</p>
        </div>

        {{-- Card Login --}}
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Masuk ke Akun</h2>

            {{-- Error Message --}}
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm text-red-700">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="form-label">Email</label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="email"
                           placeholder="nama@example.com"
                           class="form-input @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="form-label">Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <input id="password"
                               :type="show ? 'text' : 'password'"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="••••••••"
                               class="form-input pr-10 @error('password') border-red-400 @enderror">
                        <button type="button"
                                @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember"
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 text-sm text-gray-600">Ingat saya</label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Masuk
                </button>
            </form>

            {{-- Demo Accounts --}}
            <div class="mt-6 pt-6 border-t border-gray-100">
                <p class="text-xs text-gray-500 text-center mb-3">Demo Akun:</p>
                <div class="grid grid-cols-3 gap-2 text-xs text-center">
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="font-semibold text-gray-700">Admin</p>
                        <p class="text-gray-500">admin@inventory.com</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="font-semibold text-gray-700">Manajer</p>
                        <p class="text-gray-500">manajer@inventory.com</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="font-semibold text-gray-700">User</p>
                        <p class="text-gray-500">user@inventory.com</p>
                    </div>
                </div>
                <p class="text-xs text-gray-400 text-center mt-2">Password semua: <strong>password</strong></p>
            </div>
        </div>
    </div>

</body>
</html>