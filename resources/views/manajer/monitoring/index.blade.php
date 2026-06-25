@extends('layouts.app')
@section('title', 'Monitoring Stok')
@section('page-title', 'Monitoring Stok')

@section('content')
<div class="space-y-6">

    {{-- KPI --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center
                        justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-blue-600" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
            <p class="text-2xl font-black text-gray-800">{{ $summary['total_barang'] }}</p>
            <p class="text-sm text-gray-500">Total Barang</p>
        </div>
        <div class="card text-center">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center
                        justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-green-600" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="text-2xl font-black text-green-600">{{ $summary['stok_aman'] }}</p>
            <p class="text-sm text-gray-500">Stok Aman</p>
        </div>
        <div class="card text-center">
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center
                        justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-yellow-600" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <p class="text-2xl font-black text-yellow-600">
                {{ $summary['stok_menipis'] }}
            </p>
            <p class="text-sm text-gray-500">Stok Menipis</p>
        </div>
        <div class="card text-center">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center
                        justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-red-600" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/>
                </svg>
            </div>
            <p class="text-2xl font-black text-red-600">{{ $summary['stok_habis'] }}</p>
            <p class="text-sm text-gray-500">Stok Habis</p>
        </div>
    </div>

    {{-- Chart --}}
    <div class="card">
        <h3 class="font-semibold text-gray-800 mb-4">
            Pergerakan Stok 30 Hari Terakhir
        </h3>
        <canvas id="movementChart" height="80"></canvas>
    </div>

    {{-- Stok Menipis & Habis --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">
                    ⚠️ Stok Menipis ({{ $lowStockItems->count() }})
                </h3>
                @if($lowStockItems->isNotEmpty())
                    <a href="{{ route('manajer.procurements.create') }}"
                       class="text-xs text-blue-600 hover:underline">
                        + Buat Pengadaan
                    </a>
                @endif
            </div>
            @if($lowStockItems->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">
                    ✅ Semua stok aman
                </p>
            @else
                <div class="space-y-3">
                    @foreach($lowStockItems as $item)
                        <div class="flex items-center justify-between p-3
                                    bg-yellow-50 rounded-xl border border-yellow-100">
                            <div class="flex-1">
                                <p class="font-medium text-sm text-gray-800">
                                    {{ $item->nama_barang }}
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $item->category->nama_kategori }}
                                    • Min: {{ $item->stok_minimum }} {{ $item->satuan }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 ml-3">
                                <div class="text-right">
                                    <p class="font-bold text-yellow-700">
                                        {{ number_format($item->stok) }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $item->satuan }}</p>
                                </div>
                                <a href="{{ route('manajer.procurements.create') }}?item_id={{ $item->id }}"
                                   class="p-1.5 bg-orange-100 text-orange-600
                                          rounded-lg hover:bg-orange-200 transition"
                                   title="Buat Pengadaan">
                                    <svg class="w-4 h-4" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M12 4v16m8-8H4"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">
                ⛔ Stok Habis ({{ $outOfStockItems->count() }})
            </h3>
            @if($outOfStockItems->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">
                    ✅ Tidak ada barang yang habis
                </p>
            @else
                <div class="space-y-3">
                    @foreach($outOfStockItems as $item)
                        <div class="flex items-center justify-between p-3
                                    bg-red-50 rounded-xl border border-red-100">
                            <div>
                                <p class="font-medium text-sm text-gray-800">
                                    {{ $item->nama_barang }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $item->category->nama_kategori }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="badge badge-red">Habis</span>
                                <a href="{{ route('manajer.procurements.create') }}?item_id={{ $item->id }}"
                                   class="p-1.5 bg-red-100 text-red-600
                                          rounded-lg hover:bg-red-200 transition"
                                   title="Buat Pengadaan">
                                    <svg class="w-4 h-4" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M12 4v16m8-8H4"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Top 5 --}}
    <div class="card">
        <h3 class="font-semibold text-gray-800 mb-4">
            🔝 Top 5 Barang Paling Sering Keluar (30 hari)
        </h3>
        @if($topItems->isEmpty())
            <p class="text-sm text-gray-400 text-center py-6">
                Belum ada data pergerakan
            </p>
        @else
            <div class="space-y-3">
                @foreach($topItems as $idx => $top)
                    <div class="flex items-center gap-4">
                        <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-600
                                    flex items-center justify-center
                                    text-xs font-bold flex-shrink-0">
                            {{ $idx + 1 }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">
                                {{ $top->item->nama_barang }}
                            </p>
                            @php
                                $max = $topItems->max('total_keluar');
                                $pct = $max > 0
                                    ? ($top->total_keluar / $max * 100)
                                    : 0;
                            @endphp
                            <div class="mt-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-2 bg-blue-500 rounded-full"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-blue-600">
                                {{ number_format($top->total_keluar) }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $top->item->satuan }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('movementChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: @json($movementData['labels']),
        datasets: [
            {
                label: 'Barang Masuk',
                data: @json($movementData['masuk']),
                borderColor: 'rgb(34,197,94)',
                backgroundColor: 'rgba(34,197,94,0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true,
            },
            {
                label: 'Barang Keluar',
                data: @json($movementData['keluar']),
                borderColor: 'rgb(239,68,68)',
                backgroundColor: 'rgba(239,68,68,0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true,
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { intersect: false, mode: 'index' },
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endpush