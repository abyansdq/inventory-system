@extends('layouts.app')
@section('title', 'Laporan Pengadaan')
@section('page-title', 'Laporan Pengadaan')

@section('content')
<div class="space-y-4">
    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="form-label text-xs">Status</label>
                <select name="status" class="form-select w-40">
                    <option value="">Semua Status</option>
                    @foreach(['draft','pending','approved','rejected','ordered','received','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
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
            @if(request()->hasAny(['status','tanggal_dari','tanggal_sampai']))
                <a href="{{ route('admin.reports.procurement') }}" class="btn-secondary">Reset</a>
            @endif
            <div class="ml-auto flex gap-2">
                <a href="{{ route('admin.reports.export.pdf', 'procurement') }}?{{ http_build_query(request()->all()) }}"
                   class="btn-danger text-sm" target="_blank">Export PDF</a>
                <a href="{{ route('admin.reports.export.excel', 'procurement') }}?{{ http_build_query(request()->all()) }}"
                   class="btn-success text-sm">Export Excel</a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-gray-700">{{ $summary['total'] }}</p>
            <p class="text-xs text-gray-500">Total</p>
        </div>
        <div class="card py-3 text-center">
            <p class="text-lg font-bold text-purple-600">
                Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500">Total Nilai</p>
        </div>
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $summary['approved'] }}</p>
            <p class="text-xs text-gray-500">Disetujui</p>
        </div>
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $summary['received'] }}</p>
            <p class="text-xs text-gray-500">Diterima</p>
        </div>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No Pengadaan</th>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Supplier</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">Status</th>
                        <th>Diajukan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($procurements as $i => $proc)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="font-mono text-xs">{{ $proc->no_pengadaan }}</td>
                            <td class="text-sm">{{ $proc->tanggal->format('d/m/Y') }}</td>
                            <td class="font-medium text-sm">{{ $proc->item->nama_barang }}</td>
                            <td class="text-sm text-gray-500">{{ $proc->supplier->nama_supplier }}</td>
                            <td class="text-center">{{ number_format($proc->qty) }}</td>
                            <td class="text-right text-sm">
                                Rp {{ number_format($proc->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $proc->status_color }}">
                                    {{ $proc->status_label }}
                                </span>
                            </td>
                            <td class="text-sm text-gray-500">{{ $proc->user->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-gray-400">
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