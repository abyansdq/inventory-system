@extends('layouts.app')
@section('title', 'Prediksi — ' . $item->nama_barang)
@section('page-title', 'Prediksi Permintaan')

@section('content')
<div class="max-w-4xl space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('user.forecasts.index') }}" class="hover:text-blue-600">
                Prediksi
            </a>
            <span>→</span>
            <span class="text-gray-800">{{ $item->nama_barang }}</span>
        </div>
        <a href="{{ route('user.forecasts.index') }}" class="btn-secondary text-sm">
            ← Kembali
        </a>
    </div>

    @if(!$hasForecast)
        <div class="card bg-yellow-50 border-yellow-200">
            <div class="flex gap-3 text-sm text-yellow-800">
                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="font-semibold">Belum ada data prediksi</p>
                    <p class="mt-1 text-yellow-700">
                        Data prediksi untuk barang ini belum di-generate oleh admin.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KIRI: Chart + Tabel --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Info Barang --}}
            <div class="card">
                <div class="flex items-center gap-4">
                    <img src="{{ $item->foto_url }}" alt="{{ $item->nama_barang }}"
                         class="w-14 h-14 rounded-xl object-cover border flex-shrink-0">
                    <div>
                        <p class="text-xs font-mono text-gray-400">{{ $item->kode_barang }}</p>
                        <h2 class="font-bold text-gray-800">{{ $item->nama_barang }}</h2>
                        <p class="text-sm text-gray-500">{{ $item->category->nama_kategori }}</p>
                    </div>
                    <div class="ml-auto text-right">
                        <p class="text-xs text-gray-500">Stok Saat Ini</p>
                        <p class="text-2xl font-black
                            {{ $item->stok == 0 ? 'text-red-600' :
                               ($item->stok <= $item->stok_minimum
                                    ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ number_format($item->stok) }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $item->satuan }}</p>
                    </div>
                </div>
            </div>

            {{-- Chart Aktual vs Prediksi --}}
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">
                    📈 Grafik Aktual vs Prediksi
                </h3>
                @if($hasForecast || $demandHistories->isNotEmpty())
                    <canvas id="forecastChart" height="110"></canvas>
                @else
                    <div class="text-center py-10">
                        <p class="text-gray-400 text-sm">Belum ada data untuk ditampilkan</p>
                    </div>
                @endif
            </div>

            {{-- Tabel Prediksi --}}
            @if($hasForecast)
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">Tabel Hasil Prediksi</h3>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Periode</th>
                                <th class="text-center">Periode (n)</th>
                                <th class="text-right">Prediksi</th>
                                <th class="text-right">Aktual</th>
                                <th class="text-right">MAPE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($forecasts as $fc)
                                <tr>
                                    <td class="font-medium">{{ $fc->periode_prediksi }}</td>
                                    <td class="text-center">{{ $fc->periode_bulan }} bulan</td>
                                    <td class="text-right font-semibold text-indigo-600">
                                        {{ number_format($fc->hasil_prediksi, 2) }}
                                        <span class="text-xs text-gray-400 font-normal">
                                            {{ $item->satuan }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        @if($fc->actual_demand !== null)
                                            {{ number_format($fc->actual_demand, 2) }}
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="text-right text-sm">
                                        @if($fc->error_mape !== null)
                                            <span class="{{ $fc->error_mape < 20 ? 'text-green-600' : ($fc->error_mape < 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ number_format($fc->error_mape, 2) }}%
                                            </span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        {{-- KANAN: Akurasi + Histori + Penjelasan --}}
        <div class="space-y-6">

            {{-- Akurasi --}}
            @if($accuracy['jumlah_data'] > 0)
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">📐 Akurasi Model</h3>
                <div class="space-y-3">
                    <div class="text-center p-3 bg-indigo-50 rounded-xl">
                        <p class="text-xs text-indigo-600 font-medium">MAE</p>
                        <p class="text-2xl font-black text-indigo-700">
                            {{ number_format($accuracy['mae'], 4) }}
                        </p>
                        <p class="text-xs text-indigo-400">Mean Absolute Error</p>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-xl">
                        <p class="text-xs text-green-600 font-medium">MAPE</p>
                        <p class="text-2xl font-black text-green-700">
                            {{ number_format($accuracy['mape'], 2) }}%
                        </p>
                        <p class="text-xs text-green-400">Mean Absolute Percentage Error</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl text-xs text-center font-medium text-gray-700">
                        {{ $accuracy['interpretasi'] }}
                    </div>
                </div>
            </div>
            @endif

            {{-- Histori Demand --}}
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">
                    Data Histori
                    <span class="text-xs text-gray-400 font-normal">
                        ({{ $demandHistories->count() }} bulan)
                    </span>
                </h3>
                @if($demandHistories->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-4">
                        Belum ada data histori
                    </p>
                @else
                    <div class="space-y-1 max-h-72 overflow-y-auto">
                        @foreach($demandHistories as $h)
                            <div class="flex justify-between items-center py-1.5
                                        border-b border-gray-50 last:border-0 text-sm">
                                <span class="text-gray-600">{{ $h->periode }}</span>
                                <span class="font-semibold">
                                    {{ number_format($h->jumlah_permintaan) }}
                                    <span class="text-xs text-gray-400">{{ $item->satuan }}</span>
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Penjelasan Singkat --}}
            <div class="card bg-gray-50">
                <h3 class="font-semibold text-gray-800 mb-3 text-sm">ℹ️ Tentang WMA</h3>
                <div class="text-xs text-gray-600 space-y-2">
                    <p>
                        <strong>Weighted Moving Average</strong> adalah metode prediksi yang
                        memberikan bobot lebih besar pada data terbaru.
                    </p>
                    <p>
                        Semakin akurat data historis, semakin baik kualitas prediksi yang dihasilkan.
                    </p>
                    <div class="mt-2 p-2 bg-white border rounded-lg font-mono text-xs">
                        MAPE &lt; 10% = Sangat Akurat<br>
                        MAPE 10–20% = Akurat<br>
                        MAPE 20–50% = Cukup Akurat<br>
                        MAPE &gt; 50% = Kurang Akurat
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    @if($hasForecast || $demandHistories->isNotEmpty())
    const ctx = document.getElementById('forecastChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [
                    {
                        label: 'Aktual',
                        data: @json($chartData['aktual']),
                        borderColor: 'rgb(99,102,241)',
                        backgroundColor: 'rgba(99,102,241,0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgb(99,102,241)',
                    },
                    {
                        label: 'Prediksi WMA',
                        data: @json($chartData['prediksi']),
                        borderColor: 'rgb(251,146,60)',
                        backgroundColor: 'rgba(251,146,60,0.05)',
                        borderWidth: 2,
                        borderDash: [6, 3],
                        tension: 0.3,
                        fill: false,
                        pointRadius: 5,
                        pointStyle: 'rectRot',
                        pointBackgroundColor: 'rgb(251,146,60)',
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                if (ctx.raw === null) return ctx.dataset.label + ': —';
                                return ctx.dataset.label + ': '
                                    + new Intl.NumberFormat('id-ID').format(ctx.raw)
                                    + ' {{ $item->satuan }}';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: val =>
                                new Intl.NumberFormat('id-ID').format(val)
                        }
                    }
                }
            }
        });
    }
    @endif
</script>
@endpush