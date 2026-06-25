@extends('layouts.app')

@section('title', 'Kategori')
@section('page-title', 'Manajemen Kategori')

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-sm text-gray-500">
                Total {{ $categories->total() }} kategori
            </p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kategori
        </a>
    </div>

    {{-- Filter --}}
    <div class="card py-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama atau kode kategori..."
                   class="form-input flex-1">
            <select name="status" class="form-select w-full sm:w-40">
                <option value="">Semua Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Reset</a>
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
                        <th>Kode</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Jumlah Barang</th>
                        <th class="text-center">Status</th>
                        <th class="text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $i => $category)
                        <tr>
                            <td class="text-gray-400">
                                {{ $categories->firstItem() + $i }}
                            </td>
                            <td>
                                <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                                    {{ $category->kode_kategori ?? '-' }}
                                </span>
                            </td>
                            <td class="font-medium">{{ $category->nama_kategori }}</td>
                            <td class="text-gray-500 max-w-xs truncate">
                                {{ $category->deskripsi ?? '-' }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-blue">
                                    {{ $category->items_count }} barang
                                </span>
                            </td>
                            <td class="text-center">
                                @if($category->is_active)
                                    <span class="badge badge-green">Aktif</span>
                                @else
                                    <span class="badge badge-gray">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.categories.show', $category) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"
                                       title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                       class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}"
                                          method="POST"
                                          x-data
                                          @submit.prevent="if(confirm('Hapus kategori ini?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                Belum ada kategori
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div class="px-4 py-3 border-t">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection