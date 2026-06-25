@extends('layouts.app')
@section('title', 'Ketersediaan Stok')
@section('page-title', 'Ketersediaan Stok')

@section('content')
<div class="space-y-4">

    {{-- Info Banner --}}
    <div class="card bg-blue-50 border-blue-200 py-3">
        <div class="flex gap-3 items-center">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-blue-800">
                Lihat ketersediaan barang sebelum membuat permintaan.
                <a href="{{ route('user.item-requests.create') }}"
                   class="font-semibold underline ml-1">
                    Buat Permintaan →
                </a>
            </p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama atau kode barang..."
                   class="form-input flex-1 min-w-48">
            <select name="category_id" class="form-select w-44">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                            {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nama_kategori }}
                    </option>
                @endforeach
            </select>
            <select name="status_stok" class="form-select w-36">
                <option value="">Semua Status</option>
                <option value="tersedia"
                        {{ request('status_stok') === 'tersedia' ? 'selected' : '' }}>
                    Tersedia
                </option>
                <option value="menipis"
                        {{ request('status_stok') === 'menipis' ? 'selected' : '' }}>
                    Menipis
                </option>
                <option value="habis"
                        {{ request('status_stok') === 'habis' ? 'selected' : '' }}>
                    Habis
                </option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search','category_id','status_stok']))
                <a href="{{ route('user.items.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Grid Barang --}}
    @if($items->isEmpty())
        <div class="card text-center py-16">
            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
            </svg>
            <p class="text-gray-400 font-medium">Tidak ada barang ditemukan</p>
            @if(request()->hasAny(['search','category_id','status_stok']))
                <a href="{{ route('user.items.index') }}"
                   class="text-blue-600 text-sm mt-2 inline-block hover:underline">
                    Reset Filter
                </a>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($items as $item)
                <div class="card p-0 overflow-hidden hover:shadow-md transition group">
                    {{-- Foto Barang --}}
                    <div class="relative">
                        <img src="{{ $item->foto_url }}"
                             alt="{{ $item->nama_barang }}"
                             class="w-full h-36 object-cover group-hover:scale-105
                                    transition duration-300">
                        {{-- Badge Status --}}
                        <div class="absolute top-2 right-2">
                            @if($item->stok == 0)
                                <span class="badge badge-red shadow-sm">Habis</span>
                            @elseif($item->stok <= $item->stok_minimum)
                                <span class="badge badge-yellow shadow-sm">Menipis</span>
                            @else
                                <span class="badge badge-green shadow-sm">Tersedia</span>
                            @endif
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="p-4">
                        <p class="text-xs text-gray-400 font-mono mb-1">
                            {{ $item->kode_barang }}
                        </p>
                        <h3 class="font-semibold text-gray-800 text-sm leading-tight mb-1">
                            {{ $item->nama_barang }}
                        </h3>
                        <p class="text-xs text-gray-500 mb-3">
                            {{ $item->category->nama_kategori }}
                        </p>

                        {{-- Stok Bar --}}
                        <div class="mb-3">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-gray-500">Stok</span>
                                <span class="text-sm font-bold
                                    {{ $item->stok == 0 ? 'text-red-600' :
                                       ($item->stok <= $item->stok_minimum
                                            ? 'text-yellow-600' : 'text-green-600') }}">
                                    {{ number_format($item->stok) }}
                                    <span class="text-xs font-normal text-gray-400">
                                        {{ $item->satuan }}
                                    </span>
                                </span>
                            </div>
                            @php
                                $pct = $item->stok_minimum > 0
                                    ? min(100, ($item->stok / ($item->stok_minimum * 3)) * 100)
                                    : ($item->stok > 0 ? 100 : 0);
                                $barColor = $item->stok == 0 ? 'bg-red-400' :
                                    ($item->stok <= $item->stok_minimum
                                        ? 'bg-yellow-400' : 'bg-green-400');
                            @endphp
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="{{ $barColor }} h-1.5 rounded-full transition-all"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>

                        {{-- Aksi --}}
                        <div class="flex gap-2">
                            <a href="{{ route('user.items.show', $item) }}"
                               class="btn-secondary text-xs flex-1 justify-center py-1.5">
                                Detail
                            </a>
                            @if($item->stok > 0)
                                <a href="{{ route('user.item-requests.create') }}?item_id={{ $item->id }}"
                                   class="btn-primary text-xs flex-1 justify-center py-1.5">
                                    Minta
                                </a>
                            @else
                                <button disabled
                                        class="flex-1 py-1.5 text-xs bg-gray-100 text-gray-400
                                               rounded-lg cursor-not-allowed">
                                    Habis
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($items->hasPages())
            <div class="flex justify-center">
                {{ $items->links() }}
            </div>
        @endif
    @endif
</div>
@endsection