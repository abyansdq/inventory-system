<a href="{{ route('user.dashboard') }}"
   class="sidebar-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
    </svg>
    Dashboard
</a>

<div class="pt-3 pb-1">
    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</p>
</div>
<a href="{{ route('user.items.index') }}"
   class="sidebar-link {{ request()->routeIs('user.items*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
    </svg>
    Ketersediaan Stok
</a>

<div class="pt-3 pb-1">
    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Permintaan</p>
</div>
<a href="{{ route('user.item-requests.index') }}"
   class="sidebar-link {{ request()->routeIs('user.item-requests*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    Permintaan Barang
</a>
<a href="{{ route('user.forecasts.index') }}"
   class="sidebar-link {{ request()->routeIs('user.forecasts*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
    </svg>
    Prediksi Permintaan
</a>