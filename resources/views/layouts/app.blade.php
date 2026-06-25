<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inventory System') — {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js sudah include via Breeze --}}
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="min-h-screen flex">

        {{-- Sidebar --}}
        @include('layouts.sidebar')

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Top Navbar --}}
            @include('layouts.navbar')

            {{-- Flash Messages --}}
            @include('components.flash-message')

            {{-- Page Content --}}
            <main class="flex-1 p-6">
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="bg-white border-t px-6 py-3 text-sm text-gray-500 text-center">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>