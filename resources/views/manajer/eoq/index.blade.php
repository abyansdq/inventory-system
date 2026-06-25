@extends('layouts.app')
@section('title', 'Perhitungan EOQ')
@section('page-title', 'Perhitungan EOQ')

@section('content')
<div class="space-y-4">

    <div class="card bg-blue-50 border-blue-200 py-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-semibold">Economic Order Quantity (EOQ)</p>
                <p class="mt-1">
                    Formula:
                    <code class="bg-blue-100 px-1 rounded">EOQ = √(2DS/H)</code>
                    — Data kalkulasi dari Admin Gudang.
                </p>
            </div>
        </div>
    </div>

    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari barang..." class="form-input flex-1 min-w-48">
            <select name="category_id" class="form-select w-44">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                            {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nama_kategori }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search','category_id']))
                <a href="{{ route('manajer.eoq.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-10">#</th>
                        <th>Barang</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center">EOQ</th>
                        <th class="text-center">ROP</th>
                        <th class="text-center">Safety Stock</th>
                        <th>Terakhir Dihitung</th>
                        <th class="text-center w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $item)
                        @php $latestEoq = $item->eoqCalculations->first(); @endphp
                        <tr>
                            <td class="text-gray-400">{{ $items->firstItem() + $i }}</td>
                            <td>
                                <p class="font-medium">{{ $item->nama_barang }}</p>
                                <p class="text-xs font-mono text-gray-400">
                                    {{ $item->kode_barang }}
                                </p>
                            </td>
                            <td class="text-center">
                                <span class="font-semibold
                                    {{ $item->status_stok === 'menipis'
                                        ? 'text-yellow-600'
                                        : ($item->status_stok === 'habis'
                                            ? 'text-red-600'
                                            : 'text-green-600') }}">
                                    {{ number_format($item->stok) }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ $item->satuan }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($latestEoq)
                                    <span class="font-semibold text-blue-600">
                                        {{ number_format($latestEoq->eoq_result, 0) }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($latestEoq)
                                    <span class="font-semibold text-orange-600">
                                        {{ number_format($latestEoq->rop_result, 0) }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($latestEoq)
                                    <span class="font-semibold text-purple-600">
                                        {{ number_format($latestEoq->safety_stock, 0) }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="text-sm text-gray-500">
                                {{ $latestEoq
                                    ? $latestEoq->tanggal_hitung->format('d/m/Y')
                                    : '—' }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('manajer.eoq.show', $item) }}"
                                   class="btn-secondary text-xs px-3 py-1.5">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400">
                                Belum ada data barang
                            </td>
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