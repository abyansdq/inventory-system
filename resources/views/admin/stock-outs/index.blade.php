@extends('layouts.app')
@section('title', 'Barang Keluar')
@section('page-title', 'Barang Keluar')

@section('content')
<div class="space-y-4">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm text-gray-500">Total {{ $stockOuts->total() }} transaksi</p>
        <a href="{{ route('admin.stock-outs.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>
            Input Barang Keluar
        </a>
    </div>

    {{-- Filter --}}
    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="No. dokumen atau nama barang..."
                   class="form-input flex-1 min-w-48">
            <select name="item_id" class="form-select w-48">
                <option value="">Semua Barang</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}"
                            {{ request('item_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_barang }}
                    </option>
                @endforeach
            </select>
            <input type="date" name="tanggal_dari"
                   value="{{ request('tanggal_dari') }}" class="form-input w-40">
            <input type="date" name="tanggal_sampai"
                   value="{{ request('tanggal_sampai') }}" class="form-input w-40">
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search','item_id','tanggal_dari','tanggal_sampai']))
                <a href="{{ route('admin.stock-outs.index') }}" class="btn-secondary">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Tabel --}}
    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>No Dokumen</th>
                        <th>Barang</th>
                        <th class="text-center">Qty</th>
                        <th>No Permintaan</th>
                        <th>Tanggal</th>
                        <th>Input Oleh</th>
                        <th class="text-center w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockOuts as $so)
                        <tr>
                            <td>
                                <span class="font-mono text-xs bg-red-50
                                             text-red-700 px-2 py-1 rounded">
                                    {{ $so->no_dokumen }}
                                </span>
                            </td>
                            <td class="font-medium">
                                {{ $so->item->nama_barang }}
                            </td>
                            <td class="text-center font-semibold text-red-600">
                                -{{ number_format($so->qty) }}
                                <span class="text-xs text-gray-400">
                                    {{ $so->item->satuan }}
                                </span>
                            </td>
                            <td>
                                @if($so->itemRequest)
                                    <span class="font-mono text-xs bg-gray-100
                                                 px-2 py-1 rounded">
                                        {{ $so->itemRequest->no_permintaan }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">Manual</span>
                                @endif
                            </td>
                            <td class="text-sm">
                                {{ $so->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="text-sm text-gray-500">
                                {{ $so->user->name }}
                            </td>
                            <td>
                                <div class="flex items-center justify-center">
                                    <a href="{{ route('admin.stock-outs.show', $so) }}"
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
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400">
                                Belum ada data barang keluar
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($stockOuts->hasPages())
            <div class="px-4 py-3 border-t">{{ $stockOuts->links() }}</div>
        @endif
    </div>
</div>
@endsection