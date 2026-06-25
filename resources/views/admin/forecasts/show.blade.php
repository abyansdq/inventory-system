@extends('layouts.app')
@section('title', 'Prediksi — ' . $item->nama_barang)
@section('page-title', 'Prediksi Permintaan WMA')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">
            <a href="{{ route('admin.forecasts.index') }}" class="hover:text-blue-600">Prediksi</a>
            → {{ $item->nama_barang }}
        </p>
        <a href="{{ route('admin.forecasts.index') }}" class="btn-secondary text-sm">← Kembali</a>
    </div>

    @if(!$cukupData)
    <div class="card bg-yellow-50 border-yellow-200">
        <div class="flex gap-3 text-sm text-yellow-800">
            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="font-semibold">Data histori belum cukup</p>
                <p class="mt-1">
                    Tersedia <strong>{{ $dataCount }} bulan</strong> data.
                    Dibutuhkan minimal <strong>3 bulan</strong> untuk generate prediksi.
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KIRI: Form + Chart + Tabel Prediksi --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Form Generate Prediksi --}}
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">
                    🎯 Generate Prediksi WMA
                </h3>
                <form action="{{ route('admin.forecasts.generate', $item) }}"
                      method="POST" class="space-y-4" x-data="wmaForm()">
                    @csrf

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="form-label">
                                Periode (n)
                                <span class="text-xs text-gray-400 font-normal">bulan</span>
                            </label>
                            <select name="n" x-model="n" @change="updateBobot"
                                    class="form-select">
                                <option value="2">2 Periode</option>
                                <option value="3" selected>3 Periode</option>
                                <option value="4">4 Periode</option>
                                <option value="5">5 Periode</option>
                                <option value="6">6 Periode</option>
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Jumlah bulan yang digunakan</p>
                        </div>

                        <div>
                            <label class="form-label">
                                Prediksi ke depan
                                <span class="text-xs text-gray-400 font-normal">bulan</span>
                            </label>
                            <select name="bulan_kedepan" class="form-select">
                                <option value="1">1 Bulan</option>
                                <option value="2">2 Bulan</option>
                                <option value="3" selected>3 Bulan</option>
                                <option value="6">6 Bulan</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">
                                Bobot (otomatis)
                            </label>
                            <div class="form-input bg-gray-50 text-sm text-gray-600 font-mono">
                                <span x-text="bobotDisplay">1, 2, 3</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">
                                Data terbaru = bobot terbesar
                            </p>
                        </div>
                    </div>

                    {{-- Preview Rumus --}}
                    <div class="p-3 bg-indigo-50 border border-indigo-200 rounded-xl text-sm">
                        <p class="font-medium text-indigo-800 mb-2">Preview Rumus WMA:</p>
                        <p class="font-mono text-xs text-indigo-700" x-html="rumusPreview"></p>
                    </div>

                    <button type="submit"
                            class="btn-primary w-full justify-center"
                            {{ !$cukupData ? 'disabled' : '' }}>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18"/>
                        </svg>
                        Generate Prediksi
                    </button>
                </form>
            </div>

            {{-- Chart Aktual vs Prediksi --}}
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">
                    📈 Grafik Aktual vs Prediksi
                </h3>
                <canvas id="forecastChart" height="100"></canvas>
            </div>

            {{-- Tabel Hasil Prediksi --}}
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">Hasil Prediksi</h3>
                @if($forecasts->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-6">
                        Belum ada data prediksi
                    </p>
                @else
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Periode Prediksi</th>
                                    <th>Metode</th>
                                    <th class="text-center">n</th>
                                    <th class="text-right">Hasil Prediksi</th>
                                    <th class="text-right">Aktual</th>
                                    <th class="text-right">MAE</th>
                                    <th class="text-right">MAPE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($forecasts as $fc)
                                    <tr>
                                        <td class="font-medium">{{ $fc->periode_prediksi }}</td>
                                        <td>
                                            <span class="badge badge-indigo text-xs">
                                                {{ $fc->metode_label }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $fc->periode_bulan }}</td>
                                        <td class="text-right font-semibold text-indigo-600">
                                            {{ number_format($fc->hasil_prediksi, 2) }}
                                        </td>
                                        <td class="text-right">
                                            @if($fc->actual_demand !== null)
                                                {{ number_format($fc->actual_demand, 2) }}
                                            @else
                                                <span class="text-gray-300 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="text-right text-sm">
                                            @if($fc->error_mae !== null)
                                                {{ number_format($fc->error_mae, 4) }}
                                            @else
                                                <span class="text-gray-300 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="text-right text-sm">
                                            @if($fc->error_mape !== null)
                                                {{ number_format($fc->error_mape, 2) }}%
                                            @else
                                                <span class="text-gray-300 text-xs">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- KANAN: Akurasi + Histori --}}
        <div class="space-y-6">

            {{-- Akurasi Model --}}
            @if($accuracy['jumlah_data'] > 0)
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">📐 Akurasi Model</h3>
                <div class="space-y-4">
                    <div class="text-center p-4 bg-indigo-50 rounded-xl">
                        <p class="text-xs text-indigo-600 font-medium">MAE</p>
                        <p class="text-2xl font-black text-indigo-700 mt-1">
                            {{ number_format($accuracy['mae'], 4) }}
                        </p>
                        <p class="text-xs text-indigo-500">Mean Absolute Error</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-xl">
                        <p class="text-xs text-green-600 font-medium">MAPE</p>
                        <p class="text-2xl font-black text-green-700 mt-1">
                            {{ number_format($accuracy['mape'], 2) }}%
                        </p>
                        <p class="text-xs text-green-500">Mean Absolute Percentage Error</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl text-sm text-center font-medium">
                        {{ $accuracy['interpretasi'] }}
                    </div>
                    <p class="text-xs text-gray-400 text-center">
                        Berdasarkan {{ $accuracy['jumlah_data'] }} data aktual
                    </p>
                </div>
            </div>
            @endif

            {{-- Data Histori --}}
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">
                    Data Histori ({{ $demandHistories->count() }} bulan)
                </h3>
                <div class="space-y-1 max-h-96 overflow-y-auto">
                    @forelse($demandHistories as $h)
                        <div class="flex justify-between items-center py-1.5 border-b
                                    border-gray-50 last:border-0 text-sm">
                            <span class="text-gray-600">{{ $h->periode }}</span>
                            <span class="font-semibold">
                                {{ number_format($h->jumlah_permintaan) }}
                                <span class="text-xs text-gray-400">{{ $item->satuan }}</span>
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">
                            Belum ada data
                        </p>
                    @endforelse
                </div>
            </div>

            {{-- Penjelasan WMA --}}
            <div class="card bg-gray-50">
                <h3 class="font-semibold text-gray-800 mb-3 text-sm">
                    ℹ️ Cara Kerja WMA
                </h3>
                <div class="text-xs text-gray-600 space-y-2">
                    <p>
                        <strong>Weighted Moving Average</strong> memberikan bobot berbeda
                        pada setiap periode — data terbaru mendapat bobot lebih besar.
                    </p>
                    <p class="font-mono bg-white border rounded p-2">
                        WMA = Σ(W × D) / ΣW
                    </p>
                    <p>Contoh WMA 3 periode dengan bobot [1, 2, 3]:</p>
                    <p class="font-mono bg-white border rounded p-2 text-xs">
                        Data: Jan=100, Feb=120, Mar=110<br>
                        WMA = (100×1 + 120×2 + 110×3) / 6<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;= (100+240+330) / 6<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;= 670 / 6 = <strong>111.67</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
// ---- Chart ----
const ctx = document.getElementById('forecastChart').getContext('2d');
new Chart(ctx, {
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
                backgroundColor: 'rgba(251,146,60,0.1)',
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
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.dataset.label + ': ' +
                           (ctx.raw !== null
                               ? new Intl.NumberFormat('id-ID').format(ctx.raw)
                               : '—')
                }
            }
        },
        scales: {
            y: { beginAtZero: false }
        }
    }
});

// ---- Alpine WMA Form ----
function wmaForm() {
    return {
        n: 3,
        get bobotDisplay() {
            return Array.from({length: this.n}, (_, i) => i + 1).join(', ');
        },
        get rumusPreview() {
            const bobot = Array.from({length: this.n}, (_, i) => i + 1);
            const labels = Array.from({length: this.n}, (_, i) => `D_${i+1}`);
            const sum    = bobot.reduce((a, b) => a + b, 0);
            const num    = labels.map((l, i) => `${bobot[i]}×${l}`).join(' + ');
            return `WMA = (${num}) / ${sum}`;
        },
        updateBobot() { /* auto reactive */ }
    }
}
</script>
@endpush