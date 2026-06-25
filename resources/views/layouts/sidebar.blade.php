<aside class="w-64 bg-gray-900 text-white flex flex-col min-h-screen flex-shrink-0"
       x-data="{ collapsed: false }">

    {{-- Logo --}}
    <div class="px-6 py-5 border-b border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-sm text-white leading-tight">Inventory System</p>
                <p class="text-xs text-gray-400">Manajemen Gudang</p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

        @auth
            {{-- =============== ADMIN MENU =============== --}}
            @role('admin')
                @include('layouts.menus.admin-menu')
            @endrole

            {{-- =============== MANAJER MENU =============== --}}
            @role('manajer')
                @include('layouts.menus.manajer-menu')
            @endrole

            {{-- =============== USER MENU =============== --}}
            @role('user')
                @include('layouts.menus.user-menu')
            @endrole
        @endauth
    </nav>

    {{-- User Info Bottom --}}
    <div class="px-4 py-3 border-t border-gray-700">
        <div class="flex items-center gap-3">
            <img src="{{ auth()->user()->foto_url }}"
                 alt="{{ auth()->user()->name }}"
                 class="w-8 h-8 rounded-full object-cover border border-gray-600">
            <div class="min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400">{{ auth()->user()->role_label }}</p>
            </div>
        </div>
    </div>
</aside>