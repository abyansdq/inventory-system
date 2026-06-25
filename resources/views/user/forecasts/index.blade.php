@extends('layouts.app')
@section('title', 'Prediksi Permintaan')
@section('page-title', 'Prediksi Permintaan')

@section('content')
<div class="space-y-4">

    {{-- Info Banner --}}
    <div class="card bg-indigo-50 border-indigo-200 py-3">
        <div class="flex gap-3 items-start">
            <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18"/>
            </svg>
            <div class="text-sm text-indigo-800">
                <p class="font-semibold">Prediksi Permintaan Barang</p>
                <p class="mt-0.5 text-indigo-600">
                    Data prediksi menggunakan metode
                    <strong>Weighted Moving Average (WMA)</strong>
                    berdasarkan histori permintaan.
                </p>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama barang..." class="form-input flex-1 min-w-48">
            <select name="category_id" class="form-select w-44">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                            {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nama_kategori }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search','category_id']))
                <a href="{{ route('user.forecasts.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Grid Barang --}}
    @if($items->isEmpty())
        <div class="card text-center py-16">
            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18"/>
            </svg>
            <p class="text-gray-400">Tidak ada data barang</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($items as $item)
                @php $latestForecast = $item->forecasts->first(); @endphp
                <div class="card hover:shadow-md transition cursor-pointer"
                     onclick="window.location='{{ route('user.forecasts.show', $item) }}'">

                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div>
                            <p class="text-xs font-mono text-gray-400">{{ $item->kode_barang }}</p>
                            <h3 class="font-semibold text-gray-800 mt-0.5 leading-tight">
                                {{ $item->nama_barang }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $item->category->nama_kategori }}
                            </p>
                        </div>
                        <div class="w-10 h-10 bg-indigo-100 rounded-xl
                                    flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-indigo-600" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18"/>
                            </svg>
                        </div>
                    </div>

                    @if($latestForecast)
                        <div class="bg-indigo-50 rounded-xl p-3 mb-3">
                            <p class="text-xs text-indigo-500 mb-1">
                                Prediksi {{ $latestForecast->periode_prediksi }}
                            </p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-black text-indigo-700">
                                    {{ number_format($latestForecast->hasil_prediksi, 0) }}
                                </span>
                                <span class="text-sm text-indigo-500">{{ $item->satuan }}</span>
                            </div>
                            @if($latestForecast->error_mape !== null)
                                <p class="text-xs text-indigo-400 mt-1">
                                    Akurasi: MAPE {{ number_format($latestForecast->error_mape, 2) }}%
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-xl p-3 mb-3 text-center">
                            <p class="text-xs text-gray-400">Belum ada prediksi</p>
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>
                            {{ $item->demandHistories->count() }} bulan data histori
                        </span>
                        <span class="text-indigo-600 font-medium">
                            Lihat Detail →
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        @if($items->hasPages())
            <div class="flex justify-center">
                {{ $items->links() }}
            </div>
        @endif
    @endif
</div>
@endsection