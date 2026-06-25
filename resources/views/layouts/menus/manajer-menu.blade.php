<a href="{{ route('manajer.dashboard') }}"
   class="sidebar-link {{ request()->routeIs('manajer.dashboard') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
    </svg>
    Dashboard
</a>

<div class="pt-3 pb-1">
    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok & Barang</p>
</div>
<a href="{{ route('manajer.items.index') }}"
   class="sidebar-link {{ request()->routeIs('manajer.items*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
    </svg>
    Data Barang
</a>
<a href="{{ route('manajer.monitoring.index') }}"
   class="sidebar-link {{ request()->routeIs('manajer.monitoring*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
    </svg>
    Monitoring Stok
</a>

<div class="pt-3 pb-1">
    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengadaan</p>
</div>
<a href="{{ route('manajer.procurements.index') }}"
   class="sidebar-link {{ request()->routeIs('manajer.procurements*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
    </svg>
    Pengadaan Barang
</a>
<a href="{{ route('manajer.item-requests.index') }}"
   class="sidebar-link {{ request()->routeIs('manajer.item-requests*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    Permintaan Barang
</a>

<div class="pt-3 pb-1">
    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Analisis</p>
</div>
<a href="{{ route('manajer.eoq.index') }}"
   class="sidebar-link {{ request()->routeIs('manajer.eoq*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
    </svg>
    EOQ
</a>
<a href="{{ route('manajer.forecasts.index') }}"
   class="sidebar-link {{ request()->routeIs('manajer.forecasts*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
    </svg>
    Prediksi Permintaan
</a>

<div class="pt-3 pb-1">
    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Laporan</p>
</div>
<a href="{{ route('manajer.reports.stock') }}"
   class="sidebar-link {{ request()->routeIs('manajer.reports*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    Laporan
</a>