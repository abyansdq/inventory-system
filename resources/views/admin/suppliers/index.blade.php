@extends('layouts.app')
@section('title', 'Supplier')
@section('page-title', 'Manajemen Supplier')

@section('content')
<div class="space-y-4">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm text-gray-500">Total {{ $suppliers->total() }} supplier</p>
        <a href="{{ route('admin.suppliers.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Supplier
        </a>
    </div>

    <div class="card py-4">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama, kode, atau kota..." class="form-input flex-1">
            <select name="status" class="form-select w-full sm:w-40">
                <option value="">Semua Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('admin.suppliers.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-12">#</th>
                        <th>Kode</th>
                        <th>Nama Supplier</th>
                        <th>Contact Person</th>
                        <th>Telepon</th>
                        <th>Kota</th>
                        <th class="text-center">Barang</th>
                        <th class="text-center">Status</th>
                        <th class="text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $i => $supplier)
                        <tr>
                            <td class="text-gray-400">{{ $suppliers->firstItem() + $i }}</td>
                            <td>
                                <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                                    {{ $supplier->kode_supplier }}
                                </span>
                            </td>
                            <td class="font-medium">{{ $supplier->nama_supplier }}</td>
                            <td>{{ $supplier->contact_person ?? '-' }}</td>
                            <td>{{ $supplier->telepon ?? '-' }}</td>
                            <td>{{ $supplier->kota ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge badge-blue">{{ $supplier->items_count }}</span>
                            </td>
                            <td class="text-center">
                                @if($supplier->is_active)
                                    <span class="badge badge-green">Aktif</span>
                                @else
                                    <span class="badge badge-gray">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.suppliers.show', $supplier) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.suppliers.edit', $supplier) }}"
                                       class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.suppliers.destroy', $supplier) }}"
                                          method="POST" x-data
                                          @submit.prevent="if(confirm('Hapus supplier ini?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg" title="Hapus">
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
                            <td colspan="9" class="text-center py-12 text-gray-400">
                                Belum ada supplier
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($suppliers->hasPages())
            <div class="px-4 py-3 border-t">{{ $suppliers->links() }}</div>
        @endif
    </div>
</div>
@endsection