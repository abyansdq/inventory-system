@extends('layouts.app')
@section('title', 'Barang Masuk')
@section('page-title', 'Barang Masuk')

@section('content')
<div class="space-y-4">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm text-gray-500">Total {{ $stockIns->total() }} transaksi</p>
        <a href="{{ route('admin.stock-ins.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Input Barang Masuk
        </a>
    </div>

    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="No. dokumen atau nama barang..." class="form-input flex-1 min-w-48">
            <select name="item_id" class="form-select w-48">
                <option value="">Semua Barang</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_barang }}
                    </option>
                @endforeach
            </select>
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                   class="form-input w-40">
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                   class="form-input w-40">
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search','item_id','tanggal_dari','tanggal_sampai']))
                <a href="{{ route('admin.stock-ins.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>No Dokumen</th>
                        <th>Barang</th>
                        <th>Supplier</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Harga Satuan</th>
                        <th class="text-right">Total</th>
                        <th>Tanggal</th>
                        <th>Input Oleh</th>
                        <th class="text-center w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockIns as $si)
                        <tr>
                            <td>
                                <span class="font-mono text-xs bg-green-50 text-green-700 px-2 py-1 rounded">
                                    {{ $si->no_dokumen }}
                                </span>
                            </td>
                            <td class="font-medium">{{ $si->item->nama_barang }}</td>
                            <td class="text-sm text-gray-500">{{ $si->supplier->nama_supplier }}</td>
                            <td class="text-center font-semibold text-green-600">
                                +{{ number_format($si->qty) }}
                            </td>
                            <td class="text-right text-sm">
                                Rp {{ number_format($si->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td class="text-right text-sm font-medium">
                                Rp {{ number_format($si->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="text-sm">{{ $si->tanggal->format('d/m/Y') }}</td>
                            <td class="text-sm text-gray-500">{{ $si->user->name }}</td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.stock-ins.show', $si) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-gray-400">
                                Belum ada data barang masuk
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($stockIns->hasPages())
            <div class="px-4 py-3 border-t">{{ $stockIns->links() }}</div>
        @endif
    </div>
</div>
@endsection