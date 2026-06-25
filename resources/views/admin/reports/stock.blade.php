@extends('layouts.app')
@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Stok Barang')

@section('content')
<div class="space-y-4">

    {{-- Filter & Export --}}
    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="form-label text-xs">Kategori</label>
                <select name="category_id" class="form-select w-44">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
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
                <a href="{{ route('admin.reports.stock') }}" class="btn-secondary">Reset</a>
            @endif

            <div class="ml-auto flex gap-2">
                <a href="{{ route('admin.reports.export.pdf', 'stock') }}?{{ http_build_query(request()->all()) }}"
                   class="btn-danger text-sm" target="_blank">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export PDF
                </a>
                <a href="{{ route('admin.reports.export.excel', 'stock') }}?{{ http_build_query(request()->all()) }}"
                   class="btn-success text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </a>
            </div>
        </form>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $summary['total_item'] }}</p>
            <p class="text-xs text-gray-500">Total Item</p>
        </div>
        <div class="card py-3 text-center">
            <p class="text-lg font-bold text-green-600">
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

    {{-- Tabel --}}
    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center">Min</th>
                        <th class="text-right">Harga Beli</th>
                        <th class="text-right">Nilai Stok</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="font-mono text-xs">{{ $item->kode_barang }}</td>
                            <td class="font-medium">{{ $item->nama_barang }}</td>
                            <td class="text-sm text-gray-500">{{ $item->category->nama_kategori }}</td>
                            <td class="text-sm text-gray-500">{{ $item->supplier->nama_supplier }}</td>
                            <td class="text-center font-semibold">
                                {{ number_format($item->stok) }}
                                <span class="text-xs text-gray-400">{{ $item->satuan }}</span>
                            </td>
                            <td class="text-center text-sm">{{ number_format($item->stok_minimum) }}</td>
                            <td class="text-right text-sm">
                                Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                            </td>
                            <td class="text-right text-sm font-medium">
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
                            <td colspan="10" class="text-center py-12 text-gray-400">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($items->isNotEmpty())
                <tfoot>
                    <tr class="bg-gray-50 font-semibold">
                        <td colspan="8" class="px-4 py-3 text-right text-sm">
                            Total Nilai Stok:
                        </td>
                        <td class="px-4 py-3 text-right text-sm text-green-700">
                            Rp {{ number_format($summary['nilai_total'], 0, ',', '.') }}
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