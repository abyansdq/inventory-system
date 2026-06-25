@extends('layouts.app')
@section('title', 'Laporan Barang Keluar')
@section('page-title', 'Laporan Barang Keluar')

@section('content')
<div class="space-y-4">
    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="form-label text-xs">Barang</label>
                <select name="item_id" class="form-select w-48">
                    <option value="">Semua Barang</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->nama_barang }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label text-xs">Dari</label>
                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                       class="form-input w-36">
            </div>
            <div>
                <label class="form-label text-xs">Sampai</label>
                <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                       class="form-input w-36">
            </div>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['item_id','tanggal_dari','tanggal_sampai']))
                <a href="{{ route('admin.reports.stock-out') }}" class="btn-secondary">Reset</a>
            @endif
            <div class="ml-auto flex gap-2">
                <a href="{{ route('admin.reports.export.pdf', 'stock-out') }}?{{ http_build_query(request()->all()) }}"
                   class="btn-danger text-sm" target="_blank">Export PDF</a>
                <a href="{{ route('admin.reports.export.excel', 'stock-out') }}?{{ http_build_query(request()->all()) }}"
                   class="btn-success text-sm">Export Excel</a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $summary['total_transaksi'] }}</p>
            <p class="text-xs text-gray-500">Total Transaksi</p>
        </div>
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-red-600">{{ number_format($summary['total_qty']) }}</p>
            <p class="text-xs text-gray-500">Total Qty Keluar</p>
        </div>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No Dokumen</th>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th class="text-center">Qty</th>
                        <th>No Permintaan</th>
                        <th>Input Oleh</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockOuts as $i => $so)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <span class="font-mono text-xs bg-red-50 text-red-700 px-2 py-0.5 rounded">
                                    {{ $so->no_dokumen }}
                                </span>
                            </td>
                            <td class="text-sm">{{ $so->tanggal->format('d/m/Y') }}</td>
                            <td class="font-medium text-sm">{{ $so->item->nama_barang }}</td>
                            <td class="text-center font-semibold text-red-600">
                                -{{ number_format($so->qty) }}
                                <span class="text-xs text-gray-400">{{ $so->item->satuan }}</span>
                            </td>
                            <td class="text-xs font-mono text-gray-500">
                                {{ $so->itemRequest?->no_permintaan ?? '-' }}
                            </td>
                            <td class="text-sm text-gray-500">{{ $so->user->name }}</td>
                            <td class="text-sm text-gray-400 max-w-xs truncate">
                                {{ $so->keterangan ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection