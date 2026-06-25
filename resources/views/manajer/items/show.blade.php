@extends('layouts.app')
@section('title', $item->nama_barang)
@section('page-title', 'Detail Barang')

@section('content')
<div class="max-w-4xl space-y-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('manajer.items.index') }}" class="hover:text-blue-600">
                Data Barang
            </a>
            <span>→</span>
            <span class="text-gray-800">{{ $item->nama_barang }}</span>
        </div>
        <a href="{{ route('manajer.items.index') }}" class="btn-secondary text-sm">
            ← Kembali
        </a>
    </div>

    {{-- Header --}}
    <div class="card">
        <div class="flex flex-col sm:flex-row gap-5">
            <img src="{{ $item->foto_url }}" alt="{{ $item->nama_barang }}"
                 class="w-32 h-32 rounded-xl object-cover border flex-shrink-0">
            <div class="flex-1">
                <p class="text-xs font-mono text-gray-400">{{ $item->kode_barang }}</p>
                <h2 class="text-xl font-bold text-gray-800 mt-1">
                    {{ $item->nama_barang }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $item->category->nama_kategori }}
                    • {{ $item->supplier->nama_supplier }}
                </p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="badge badge-{{ $item->status_stok_color }}">
                        Stok: {{ ucfirst($item->status_stok) }}
                    </span>
                    <span class="badge badge-{{ $item->is_active ? 'green' : 'gray' }}">
                        {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                {{-- Tombol Buat Pengadaan --}}
                @if($item->status_stok !== 'aman')
                    <div class="mt-4">
                        <a href="{{ route('manajer.procurements.create') }}?item_id={{ $item->id }}"
                           class="btn-primary text-sm">
                            🛒 Buat Pengadaan
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- KPI Stok --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card text-center">
            <p class="text-xs text-gray-500 mb-1">Stok Saat Ini</p>
            <p class="text-2xl font-bold
                {{ $summary['stok_saat_ini'] <= $summary['stok_minimum']
                    ? 'text-red-600' : 'text-green-600' }}">
                {{ number_format($summary['stok_saat_ini']) }}
            </p>
            <p class="text-xs text-gray-400">{{ $item->satuan }}</p>
        </div>
        <div class="card text-center">
            <p class="text-xs text-gray-500 mb-1">Stok Minimum</p>
            <p class="text-2xl font-bold text-gray-700">
                {{ number_format($summary['stok_minimum']) }}
            </p>
            <p class="text-xs text-gray-400">{{ $item->satuan }}</p>
        </div>
        <div class="card text-center">
            <p class="text-xs text-gray-500 mb-1">Safety Stock</p>
            <p class="text-2xl font-bold text-blue-600">
                {{ number_format($summary['safety_stock']) }}
            </p>
            <p class="text-xs text-gray-400">{{ $item->satuan }}</p>
        </div>
        <div class="card text-center">
            <p class="text-xs text-gray-500 mb-1">Nilai Stok</p>
            <p class="text-base font-bold text-purple-600">
                Rp {{ number_format($summary['nilai_stok'], 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- EOQ & Forecast --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- EOQ Terbaru --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">EOQ Terbaru</h3>
                <a href="{{ route('manajer.eoq.show', $item) }}"
                   class="text-xs text-blue-600 hover:underline">
                    Lihat Detail →
                </a>
            </div>
            @if($latestEoq)
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="bg-blue-50 rounded-xl p-3">
                        <p class="text-xs text-blue-500">EOQ</p>
                        <p class="text-xl font-black text-blue-700">
                            {{ number_format($latestEoq->eoq_result, 0) }}
                        </p>
                        <p class="text-xs text-blue-400">{{ $item->satuan }}</p>
                    </div>
                    <div class="bg-orange-50 rounded-xl p-3">
                        <p class="text-xs text-orange-500">ROP</p>
                        <p class="text-xl font-black text-orange-700">
                            {{ number_format($latestEoq->rop_result, 0) }}
                        </p>
                        <p class="text-xs text-orange-400">{{ $item->satuan }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-3">
                        <p class="text-xs text-purple-500">SS</p>
                        <p class="text-xl font-black text-purple-700">
                            {{ number_format($latestEoq->safety_stock, 0) }}
                        </p>
                        <p class="text-xs text-purple-400">{{ $item->satuan }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-400 text-right mt-3">
                    Dihitung: {{ $latestEoq->tanggal_hitung->format('d/m/Y') }}
                </p>
            @else
                <p class="text-sm text-gray-400 text-center py-6">
                    Belum ada kalkulasi EOQ
                </p>
            @endif
        </div>

        {{-- Forecast Terbaru --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Prediksi Terbaru</h3>
                <a href="{{ route('manajer.forecasts.show', $item) }}"
                   class="text-xs text-blue-600 hover:underline">
                    Lihat Detail →
                </a>
            </div>
            @if($latestForecast)
                <div class="text-center py-3">
                    <p class="text-xs text-gray-500 mb-1">
                        {{ $latestForecast->periode_prediksi }}
                    </p>
                    <p class="text-3xl font-black text-indigo-600">
                        {{ number_format($latestForecast->hasil_prediksi, 0) }}
                    </p>
                    <p class="text-sm text-gray-400 mt-1">
                        {{ $item->satuan }} (WMA)
                    </p>
                    @if($latestForecast->error_mape !== null)
                        <p class="text-xs text-gray-400 mt-1">
                            MAPE: {{ number_format($latestForecast->error_mape, 2) }}%
                        </p>
                    @endif
                </div>
            @else
                <p class="text-sm text-gray-400 text-center py-6">
                    Belum ada prediksi
                </p>
            @endif
        </div>
    </div>

    {{-- Info Detail --}}
    <div class="card">
        <h3 class="font-semibold text-gray-800 mb-4">Parameter EOQ</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-gray-500 text-xs">Biaya Pemesanan (S)</p>
                <p class="font-semibold mt-1">
                    Rp {{ number_format($item->ordering_cost, 0, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Biaya Penyimpanan (H)</p>
                <p class="font-semibold mt-1">
                    Rp {{ number_format($item->holding_cost, 0, ',', '.') }}/unit/thn
                </p>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Lead Time</p>
                <p class="font-semibold mt-1">{{ $item->lead_time }} hari</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Masuk Bulan Ini</p>
                <p class="font-semibold text-green-600 mt-1">
                    +{{ number_format($summary['masuk_bulan_ini']) }} {{ $item->satuan }}
                </p>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Keluar Bulan Ini</p>
                <p class="font-semibold text-red-600 mt-1">
                    -{{ number_format($summary['keluar_bulan_ini']) }} {{ $item->satuan }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection