@extends('layouts.app')
@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Stok Barang')

@section('content')
<div class="space-y-4">
    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="form-label text-xs">Kategori</label>
                <select name="category_id" class="form-select w-44">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                                {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label text-xs">Status Stok</label>
                <select name="status_stok" class="form-select w-36">
                    <option value="">Semua</option>
                    <option value="aman"    {{ request('status_stok') === 'aman'    ? 'selected' : '' }}>Aman</option>
                    <option value="menipis" {{ request('status_stok') === 'menipis' ? 'selected' : '' }}>Menipis</option>
                    <option value="habis"   {{ request('status_stok') === 'habis'   ? 'selected' : '' }}>Habis</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['category_id','status_stok']))
                <a href="{{ route('manajer.reports.stock') }}" class="btn-secondary">Reset</a>
            @endif
            <div class="ml-auto flex gap-2">
                <a href="{{ route('manajer.reports.export.pdf', 'stock') }}?{{ http_build_query(request()->all()) }}"
                   class="btn-danger text-sm" target="_blank">Export PDF</a>
                <a href="{{ route('manajer.reports.export.excel', 'stock') }}?{{ http_build_query(request()->all()) }}"
                   class="btn-success text-sm">Export Excel</a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $summary['total_item'] }}</p>
            <p class="text-xs text-gray-500">Total Item</p>
        </div>
        <div class="card py-3 text-center">
            <p class="text-base font-bold text-green-600">
                Rp {{ number_format($summary['nilai_total'], 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500">Nilai Total Stok</p>
        </div>
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ $summary['stok_menipis'] }}</p>
            <p class="text-xs text-gray-500">Stok Menipis</p>
        </div>
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-red-600">{{ $summary['stok_habis'] }}</p>
            <p class="text-xs text-gray-500">Stok Habis</p>
        </div>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Barang</th>
                        <th>Kategori</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center">Min</th>
                        <th class="text-right">Nilai Stok</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <p class="font-medium">{{ $item->nama_barang }}</p>
                                <p class="text-xs font-mono text-gray-400">
                                    {{ $item->kode_barang }}
                                </p>
                            </td>
                            <td class="text-sm text-gray-500">
                                {{ $item->category->nama_kategori }}
                            </td>
                            <td class="text-center font-semibold">
                                {{ number_format($item->stok) }}
                                <span class="text-xs text-gray-400">{{ $item->satuan }}</span>
                            </td>
                            <td class="text-center text-sm">
                                {{ number_format($item->stok_minimum) }}
                            </td>
                            <td class="text-right text-sm">
                                Rp {{ number_format($item->stok * $item->harga_beli, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $item->status_stok_color }}">
                                    {{ ucfirst($item->status_stok) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400">
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