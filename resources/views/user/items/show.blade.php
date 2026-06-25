@extends('layouts.app')
@section('title', $item->nama_barang)
@section('page-title', 'Detail Barang')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('user.items.index') }}" class="hover:text-blue-600">
            Ketersediaan Stok
        </a>
        <span>→</span>
        <span class="text-gray-800">{{ $item->nama_barang }}</span>
    </div>

    {{-- Header Card --}}
    <div class="card">
        <div class="flex flex-col sm:flex-row gap-5">
            <img src="{{ $item->foto_url }}"
                 alt="{{ $item->nama_barang }}"
                 class="w-full sm:w-36 h-36 rounded-xl object-cover border flex-shrink-0">
            <div class="flex-1">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-mono text-gray-400">{{ $item->kode_barang }}</p>
                        <h2 class="text-xl font-bold text-gray-800 mt-1">
                            {{ $item->nama_barang }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $item->category->nama_kategori }}
                        </p>
                        @if($item->deskripsi)
                            <p class="text-sm text-gray-600 mt-2">{{ $item->deskripsi }}</p>
                        @endif
                    </div>
                    {{-- Status Badge --}}
                    <span class="badge badge-{{ $item->status_stok_color }} text-sm flex-shrink-0">
                        @if($item->stok == 0) Stok Habis
                        @elseif($item->stok <= $item->stok_minimum) Stok Menipis
                        @else Tersedia
                        @endif
                    </span>
                </div>

                {{-- Stok Besar --}}
                <div class="mt-4 flex items-end gap-2">
                    <span class="text-4xl font-black
                        {{ $item->stok == 0 ? 'text-red-600' :
                           ($item->stok <= $item->stok_minimum
                                ? 'text-yellow-600' : 'text-green-600') }}">
                        {{ number_format($item->stok) }}
                    </span>
                    <span class="text-gray-400 text-lg mb-1">{{ $item->satuan }}</span>
                    <span class="text-sm text-gray-400 mb-1.5">tersedia</span>
                </div>

                {{-- Tombol Minta --}}
                <div class="mt-4">
                    @if($item->stok > 0)
                        <a href="{{ route('user.item-requests.create') }}?item_id={{ $item->id }}"
                           class="btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4v16m8-8H4"/>
                            </svg>
                            Buat Permintaan
                        </a>
                    @else
                        <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                            ⛔ Stok barang ini sedang habis. Silakan tunggu atau hubungi admin.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Info Detail --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">Informasi Barang</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Kategori</dt>
                    <dd class="font-medium">{{ $item->category->nama_kategori }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Satuan</dt>
                    <dd class="font-medium">{{ strtoupper($item->satuan) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Stok Saat Ini</dt>
                    <dd class="font-semibold
                        {{ $item->stok == 0 ? 'text-red-600' :
                           ($item->stok <= $item->stok_minimum
                                ? 'text-yellow-600' : 'text-green-600') }}">
                        {{ number_format($item->stok) }} {{ $item->satuan }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Stok Minimum</dt>
                    <dd class="font-medium">{{ number_format($item->stok_minimum) }} {{ $item->satuan }}</dd>
                </div>
            </dl>
        </div>

        {{-- Prediksi Terbaru --}}
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">Prediksi Permintaan</h3>
            @if($latestForecast)
                <div class="text-center py-3">
                    <p class="text-xs text-gray-500 mb-1">
                        {{ $latestForecast->periode_prediksi }}
                    </p>
                    <p class="text-3xl font-black text-indigo-600">
                        {{ number_format($latestForecast->hasil_prediksi, 0) }}
                    </p>
                    <p class="text-sm text-gray-400 mt-1">{{ $item->satuan }} (prediksi WMA)</p>
                    <a href="{{ route('user.forecasts.show', $item) }}"
                       class="mt-3 inline-block text-xs text-indigo-600 hover:underline">
                        Lihat Detail Prediksi →
                    </a>
                </div>
            @else
                <div class="text-center py-6">
                    <p class="text-sm text-gray-400">Belum ada data prediksi</p>
                    <a href="{{ route('user.forecasts.show', $item) }}"
                       class="mt-2 inline-block text-xs text-blue-600 hover:underline">
                        Lihat Halaman Prediksi →
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Pergerakan Stok Terbaru --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">
                Masuk Terbaru
                <span class="text-xs text-gray-400 font-normal">(30 hari)</span>
            </h3>
            @if($recentStockIns->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4">
                    Tidak ada data masuk
                </p>
            @else
                <div class="space-y-2">
                    @foreach($recentStockIns as $si)
                        <div class="flex justify-between items-center
                                    py-2 border-b border-gray-50 last:border-0 text-sm">
                            <span class="text-gray-500">
                                {{ $si->tanggal->format('d/m/Y') }}
                            </span>
                            <span class="font-semibold text-green-600">
                                +{{ number_format($si->qty) }} {{ $item->satuan }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">
                Keluar Terbaru
                <span class="text-xs text-gray-400 font-normal">(30 hari)</span>
            </h3>
            @if($recentStockOuts->isEmpty())
                <p class="text-sm text-gray-400 text-center py-4">
                    Tidak ada data keluar
                </p>
            @else
                <div class="space-y-2">
                    @foreach($recentStockOuts as $so)
                        <div class="flex justify-between items-center
                                    py-2 border-b border-gray-50 last:border-0 text-sm">
                            <span class="text-gray-500">
                                {{ $so->tanggal->format('d/m/Y') }}
                            </span>
                            <span class="font-semibold text-red-600">
                                -{{ number_format($so->qty) }} {{ $item->satuan }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <a href="{{ route('user.items.index') }}" class="btn-secondary">← Kembali</a>
</div>
@endsection