@extends('layouts.app')
@section('title', 'Data Barang')
@section('page-title', 'Data Barang')

@section('content')
<div class="space-y-4">

    {{-- Filter --}}
    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari kode atau nama barang..."
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
                <option value="">Semua Stok</option>
                <option value="aman"    {{ request('status_stok') === 'aman'    ? 'selected' : '' }}>Aman</option>
                <option value="menipis" {{ request('status_stok') === 'menipis' ? 'selected' : '' }}>Menipis</option>
                <option value="habis"   {{ request('status_stok') === 'habis'   ? 'selected' : '' }}>Habis</option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search','category_id','status_stok']))
                <a href="{{ route('manajer.items.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-10">#</th>
                        <th>Barang</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center">Min</th>
                        <th class="text-center">Safety Stock</th>
                        <th class="text-center">Status</th>
                        <th class="text-center w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $item)
                        <tr>
                            <td class="text-gray-400">{{ $items->firstItem() + $i }}</td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->foto_url }}"
                                         alt="{{ $item->nama_barang }}"
                                         class="w-9 h-9 rounded-lg object-cover
                                                border flex-shrink-0">
                                    <div>
                                        <p class="font-medium text-gray-800">
                                            {{ $item->nama_barang }}
                                        </p>
                                        <p class="text-xs text-gray-400 font-mono">
                                            {{ $item->kode_barang }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-sm text-gray-500">
                                {{ $item->category->nama_kategori }}
                            </td>
                            <td class="text-sm text-gray-500">
                                {{ $item->supplier->nama_supplier }}
                            </td>
                            <td class="text-center">
                                <span class="font-semibold
                                    {{ $item->status_stok === 'habis'
                                        ? 'text-red-600'
                                        : ($item->status_stok === 'menipis'
                                            ? 'text-yellow-600'
                                            : 'text-green-600') }}">
                                    {{ number_format($item->stok) }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ $item->satuan }}
                                </span>
                            </td>
                            <td class="text-center text-sm text-gray-500">
                                {{ number_format($item->stok_minimum) }}
                            </td>
                            <td class="text-center text-sm text-gray-500">
                                {{ number_format($item->safety_stock) }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $item->status_stok_color }}">
                                    {{ ucfirst($item->status_stok) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('manajer.items.show', $item) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50
                                              rounded-lg" title="Detail">
                                        <svg class="w-4 h-4" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    {{-- Shortcut buat pengadaan jika stok menipis --}}
                                    @if($item->status_stok !== 'aman')
                                        <a href="{{ route('manajer.procurements.create') }}?item_id={{ $item->id }}"
                                           class="p-1.5 text-orange-600 hover:bg-orange-50
                                                  rounded-lg" title="Buat Pengadaan">
                                            <svg class="w-4 h-4" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-gray-400">
                                Belum ada data barang
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
            <div class="px-4 py-3 border-t">{{ $items->links() }}</div>
        @endif
    </div>
</div>
@endsection