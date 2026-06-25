@extends('layouts.app')
@section('title', 'Prediksi Permintaan')
@section('page-title', 'Prediksi Permintaan (WMA)')

@section('content')
<div class="space-y-4">

    <div class="card bg-indigo-50 border-indigo-200 py-3">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18"/>
            </svg>
            <div class="text-sm text-indigo-800">
                <p class="font-semibold">Weighted Moving Average (WMA)</p>
                <p class="mt-0.5">
                    Data prediksi dihasilkan oleh Admin berdasarkan histori permintaan.
                </p>
            </div>
        </div>
    </div>

    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari barang..." class="form-input flex-1">
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
                <a href="{{ route('manajer.forecasts.index') }}" class="btn-secondary">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Barang</th>
                        <th>Kategori</th>
                        <th class="text-center">Data Histori</th>
                        <th class="text-center">Prediksi Terakhir</th>
                        <th>Periode</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $item)
                        @php $latestForecast = $item->forecasts->first(); @endphp
                        <tr>
                            <td class="text-gray-400">{{ $items->firstItem() + $i }}</td>
                            <td>
                                <p class="font-medium">{{ $item->nama_barang }}</p>
                                <p class="text-xs font-mono text-gray-400">
                                    {{ $item->kode_barang }}
                                </p>
                            </td>
                            <td class="text-sm text-gray-500">
                                {{ $item->category->nama_kategori }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-blue">
                                    {{ $item->demandHistories->count() }} bulan
                                </span>
                            </td>
                            <td class="text-center">
                                @if($latestForecast)
                                    <span class="font-semibold text-indigo-600">
                                        {{ number_format($latestForecast->hasil_prediksi, 0) }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        {{ $item->satuan }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="text-sm text-gray-500">
                                {{ $latestForecast ? $latestForecast->periode_prediksi : '—' }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('manajer.forecasts.show', $item) }}"
                                   class="btn-secondary text-xs px-3 py-1.5">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400">
                                Belum ada data
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