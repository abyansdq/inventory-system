{{-- Dashboard --}}
<a href="{{ route('admin.dashboard') }}"
   class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 7h18M3 12h18M3 17h18"/>
    </svg>
    Dashboard
</a>

{{-- Master Data --}}
<div class="pt-3 pb-1">
    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Master Data</p>
</div>
<a href="{{ route('admin.items.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.items*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
    </svg>
    Data Barang
</a>
<a href="{{ route('admin.categories.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
    </svg>
    Kategori
</a>
<a href="{{ route('admin.suppliers.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.suppliers*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
    Supplier
</a>
<a href="{{ route('admin.users.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>
    Pengguna
</a>

{{-- Transaksi --}}
<div class="pt-3 pb-1">
    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Transaksi</p>
</div>
<a href="{{ route('admin.stock-ins.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.stock-ins*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/>
    </svg>
    Barang Masuk
</a>
<a href="{{ route('admin.stock-outs.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.stock-outs*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M17 8V4m0 0l4 4m-4-4l-4 4M7 16v4m0 0l-4-4m4 4l4-4"/>
    </svg>
    Barang Keluar
</a>
<a href="{{ route('admin.item-requests.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.item-requests*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    Permintaan Barang
    @php $pendingRequests = \App\Models\ItemRequest::pending()->count(); @endphp
    @if($pendingRequests > 0)
        <span class="ml-auto bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full">
            {{ $pendingRequests }}
        </span>
    @endif
</a>
<a href="{{ route('admin.procurements.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.procurements*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
    </svg>
    Pengadaan
</a>

{{-- Analisis --}}
<div class="pt-3 pb-1">
    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Analisis</p>
</div>
<a href="{{ route('admin.eoq.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.eoq*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
    </svg>
    Perhitungan EOQ
</a>
<a href="{{ route('admin.forecasts.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.forecasts*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
    </svg>
    Prediksi Permintaan
</a>
<a href="{{ route('admin.monitoring.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.monitoring*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
    </svg>
    Monitoring Stok
</a>

{{-- Laporan --}}
<div class="pt-3 pb-1">
    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Laporan</p>
</div>
<a href="{{ route('admin.reports.stock') }}"
   class="sidebar-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    Laporan
</a>

{{-- Tambahkan di bagian bawah menu admin, setelah Laporan --}}
<a href="{{ route('admin.activity-logs.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.activity-logs*') ? 'active' : '' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
    </svg>
    Activity Log
</a>