@extends('layouts.app')
@section('title', 'Laporan Barang Masuk')
@section('page-title', 'Laporan Barang Masuk')

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
                <label class="form-label text-xs">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                       class="form-input w-36">
            </div>
            <div>
                <label class="form-label text-xs">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                       class="form-input w-36">
            </div>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['item_id','tanggal_dari','tanggal_sampai']))
                <a href="{{ route('admin.reports.stock-in') }}" class="btn-secondary">Reset</a>
            @endif
            <div class="ml-auto flex gap-2">
                <a href="{{ route('admin.reports.export.pdf', 'stock-in') }}?{{ http_build_query(request()->all()) }}"
                   class="btn-danger text-sm" target="_blank">Export PDF</a>
                <a href="{{ route('admin.reports.export.excel', 'stock-in') }}?{{ http_build_query(request()->all()) }}"
                   class="btn-success text-sm">Export Excel</a>
            </div>
        </form>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $summary['total_transaksi'] }}</p>
            <p class="text-xs text-gray-500">Total Transaksi</p>
        </div>
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-green-600">{{ number_format($summary['total_qty']) }}</p>
            <p class="text-xs text-gray-500">Total Qty</p>
        </div>
        <div class="card py-3 text-center">
            <p class="text-lg font-bold text-purple-600">
                Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500">Total Nilai</p>
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
                        <th>Supplier</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Harga Satuan</th>
                        <th class="text-right">Total</th>
                        <th>Input Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockIns as $i => $si)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <span class="font-mono text-xs bg-green-50 text-green-700 px-2 py-0.5 rounded">
                                    {{ $si->no_dokumen }}
                                </span>
                            </td>
                            <td class="text-sm">{{ $si->tanggal->format('d/m/Y') }}</td>
                            <td class="font-medium text-sm">{{ $si->item->nama_barang }}</td>
                            <td class="text-sm text-gray-500">{{ $si->supplier->nama_supplier }}</td>
                            <td class="text-center font-semibold text-green-600">
                                +{{ number_format($si->qty) }}
                                <span class="text-xs text-gray-400">{{ $si->item->satuan }}</span>
                            </td>
                            <td class="text-right text-sm">
                                Rp {{ number_format($si->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td class="text-right text-sm font-medium">
                                Rp {{ number_format($si->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="text-sm text-gray-500">{{ $si->user->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-gray-400">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($stockIns->isNotEmpty())
                <tfoot>
                    <tr class="bg-gray-50 font-semibold">
                        <td colspan="7" class="px-4 py-3 text-right text-sm">Total:</td>
                        <td class="px-4 py-3 text-right text-sm text-green-700">
                            Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection