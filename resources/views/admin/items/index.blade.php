@extends('layouts.app')
@section('title', 'Data Barang')
@section('page-title', 'Manajemen Barang')

@section('content')
<div class="space-y-4">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm text-gray-500">Total {{ $items->total() }} barang</p>
        <a href="{{ route('admin.items.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Barang
        </a>
    </div>

    {{-- Filter --}}
    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari kode atau nama barang..." class="form-input flex-1 min-w-48">
            <select name="category_id" class="form-select w-48">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nama_kategori }}
                    </option>
                @endforeach
            </select>
            <select name="status_stok" class="form-select w-40">
                <option value="">Status Stok</option>
                <option value="aman"    {{ request('status_stok') === 'aman' ? 'selected' : '' }}>Aman</option>
                <option value="menipis" {{ request('status_stok') === 'menipis' ? 'selected' : '' }}>Menipis</option>
                <option value="habis"   {{ request('status_stok') === 'habis' ? 'selected' : '' }}>Habis</option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search','category_id','status_stok','status']))
                <a href="{{ route('admin.items.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-12">#</th>
                        <th>Barang</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center">Min</th>
                        <th class="text-right">Harga Beli</th>
                        <th class="text-center">Status</th>
                        <th class="text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $item)
                        <tr>
                            <td class="text-gray-400">{{ $items->firstItem() + $i }}</td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->foto_url }}" alt="{{ $item->nama_barang }}"
                                         class="w-9 h-9 rounded-lg object-cover border flex-shrink-0">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $item->nama_barang }}</p>
                                        <p class="text-xs text-gray-400 font-mono">{{ $item->kode_barang }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item->category->nama_kategori }}</td>
                            <td class="text-sm text-gray-600">{{ $item->supplier->nama_supplier }}</td>
                            <td class="text-center">
                                <span class="font-semibold
                                    {{ $item->status_stok === 'habis' ? 'text-red-600' :
                                       ($item->status_stok === 'menipis' ? 'text-yellow-600' : 'text-green-600') }}">
                                    {{ number_format($item->stok) }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $item->satuan }}</span>
                            </td>
                            <td class="text-center text-sm text-gray-500">
                                {{ number_format($item->stok_minimum) }}
                            </td>
                            <td class="text-right text-sm">
                                Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $item->status_stok_color }}">
                                    {{ ucfirst($item->status_stok) }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.items.show', $item) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.items.edit', $item) }}"
                                       class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.items.destroy', $item) }}"
                                          method="POST" x-data
                                          @submit.prevent="if(confirm('Hapus barang ini?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-gray-400">Belum ada barang</td>
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