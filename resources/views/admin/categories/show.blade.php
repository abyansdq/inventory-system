@extends('layouts.app')
@section('title', 'Detail Kategori')
@section('page-title', 'Detail Kategori')

@section('content')
<div class="max-w-4xl space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('admin.categories.index') }}" class="hover:text-blue-600">
                Kategori
            </a>
            <span>→</span>
            <span class="text-gray-800">{{ $category->nama_kategori }}</span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.categories.edit', $category) }}"
               class="btn-secondary text-sm">Edit</a>
            <a href="{{ route('admin.categories.index') }}"
               class="btn-secondary text-sm">← Kembali</a>
        </div>
    </div>

    {{-- Info Kategori --}}
    <div class="card">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl
                                flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">
                            {{ $category->nama_kategori }}
                        </h2>
                        @if($category->kode_kategori)
                            <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">
                                {{ $category->kode_kategori }}
                            </span>
                        @endif
                    </div>
                </div>

                @if($category->deskripsi)
                    <p class="text-sm text-gray-600 mt-2">{{ $category->deskripsi }}</p>
                @endif
            </div>

            <div class="flex flex-col items-end gap-2">
                @if($category->is_active)
                    <span class="badge badge-green">Aktif</span>
                @else
                    <span class="badge badge-gray">Nonaktif</span>
                @endif
                <span class="badge badge-blue">{{ $category->items_count }} barang</span>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Dibuat</p>
                <p class="font-medium mt-1">
                    {{ $category->created_at->format('d F Y') }}
                </p>
            </div>
            <div>
                <p class="text-gray-500">Terakhir Diperbarui</p>
                <p class="font-medium mt-1">
                    {{ $category->updated_at->format('d F Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Daftar Barang dalam Kategori --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">
                Barang dalam Kategori Ini
            </h3>
            <span class="badge badge-blue">{{ $items->total() }} barang</span>
        </div>

        @if($items->isEmpty())
            <div class="text-center py-10">
                <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
                <p class="text-gray-400 text-sm">
                    Belum ada barang dalam kategori ini
                </p>
            </div>
        @else
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Supplier</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $i => $item)
                            <tr>
                                <td class="text-gray-400">
                                    {{ $items->firstItem() + $i }}
                                </td>
                                <td>
                                    <span class="font-mono text-xs bg-gray-100
                                                 px-2 py-1 rounded">
                                        {{ $item->kode_barang }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $item->foto_url }}"
                                             alt="{{ $item->nama_barang }}"
                                             class="w-8 h-8 rounded-lg object-cover
                                                    border flex-shrink-0">
                                        <span class="font-medium text-sm">
                                            {{ $item->nama_barang }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-sm text-gray-500">
                                    {{ $item->supplier->nama_supplier }}
                                </td>
                                <td class="text-center">
                                    <span class="font-semibold
                                        {{ $item->status_stok === 'habis' ? 'text-red-600' :
                                           ($item->status_stok === 'menipis'
                                                ? 'text-yellow-600' : 'text-green-600') }}">
                                        {{ number_format($item->stok) }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        {{ $item->satuan }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $item->status_stok_color }}">
                                        {{ ucfirst($item->status_stok) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.items.show', $item) }}"
                                       class="text-blue-600 hover:underline text-sm">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($items->hasPages())
                <div class="mt-4">{{ $items->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection