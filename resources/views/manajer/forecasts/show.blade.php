@extends('layouts.app')
@section('title', 'Prediksi — ' . $item->nama_barang)
@section('page-title', 'Detail Prediksi WMA')

@section('content')
<div class="max-w-4xl space-y-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('manajer.forecasts.index') }}" class="hover:text-blue-600">
                Prediksi
            </a>
            <span>→</span>
            <span class="text-gray-800">{{ $item->nama_barang }}</span>
        </div>
        <a href="{{ route('manajer.forecasts.index') }}" class="btn-secondary text-sm">
            ← Kembali
        </a>
    </div>

    @if(!$hasForecast)
        <div class="card bg-yellow-50 border-yellow-200">
            <p class="text-sm text-yellow-800">
                ⚠️ Belum ada data prediksi untuk barang ini.
                Hubungi Admin Gudang untuk generate prediksi.
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            {{-- Chart --}}
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">
                    📈 Grafik Aktual vs Prediksi
                </h3>
                @if($hasForecast || $demandHistories->isNotEmpty())
                    <canvas id="forecastChart" height="110"></canvas>
                @else
                    <p class="text-center text-sm text-gray-400 py-10">
                        Belum ada data untuk ditampilkan
                    </p>
                @endif
            </div>

            {{-- Tabel --}}
            @if($hasForecast)
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">Tabel Hasil Prediksi</h3>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Periode</th>
                                <th class="text-center">n</th>
                                <th class="text-right">Prediksi</th>
                                <th class="text-right">Aktual</th>
                                <th class="text-right">MAPE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($forecasts as $fc)
                                <tr>
                                    <td class="font-medium">
                                        {{ $fc->periode_prediksi }}
                                    </td>
                                    <td class="text-center">
                                        {{ $fc->periode_bulan }}
                                    </td>
                                    <td class="text-right font-semibold text-indigo-600">
                                        {{ number_format($fc->hasil_prediksi, 2) }}
                                        <span class="text-xs text-gray-400 font-normal">
                                            {{ $item->satuan }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        {{ $fc->actual_demand !== null
                                            ? number_format($fc->actual_demand, 2)
                                            : '—' }}
                                    </td>
                                    <td class="text-right text-sm">
                                        @if($fc->error_mape !== null)
                                            <span class="{{ $fc->error_mape < 20
                                                ? 'text-green-600'
                                                : ($fc->error_mape < 50
                                                    ? 'text-yellow-600'
                                                    : 'text-red-600') }}">
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

        {{-- Kanan --}}
        <div class="space-y-6">

            @if($accuracy['jumlah_data'] > 0)
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">📐 Akurasi Model</h3>
                <div class="space-y-3">
                    <div class="text-center p-3 bg-indigo-50 rounded-xl">
                        <p class="text-xs text-indigo-600">MAE</p>
                        <p class="text-2xl font-black text-indigo-700">
                            {{ number_format($accuracy['mae'], 4) }}
                        </p>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-xl">
                        <p class="text-xs text-green-600">MAPE</p>
                        <p class="text-2xl font-black text-green-700">
                            {{ number_format($accuracy['mape'], 2) }}%
                        </p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl text-xs text-center font-medium">
                        {{ $accuracy['interpretasi'] }}
                    </div>
                </div>
            </div>
            @endif

            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">
                    Data Histori ({{ $demandHistories->count() }} bulan)
                </h3>
                <div class="space-y-1 max-h-72 overflow-y-auto">
                    @forelse($demandHistories as $h)
                        <div class="flex justify-between items-center py-1.5
                                    border-b border-gray-50 last:border-0 text-sm">
                            <span class="text-gray-600">{{ $h->periode }}</span>
                            <span class="font-semibold">
                                {{ number_format($h->jumlah_permintaan) }}
                                <span class="text-xs text-gray-400">
                                    {{ $item->satuan }}
                                </span>
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">
                            Belum ada data
                        </p>
                    @endforelse
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
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { intersect: false, mode: 'index' },
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: false } }
        }
    });
}
@endif
</script>
@endpush