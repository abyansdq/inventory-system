@extends('layouts.app')
@section('title', 'Laporan Prediksi')
@section('page-title', 'Laporan Prediksi Permintaan')

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
                <label class="form-label text-xs">Tahun</label>
                <select name="tahun" class="form-select w-32">
                    <option value="">Semua Tahun</option>
                    @foreach(range(date('Y'), date('Y') + 2) as $yr)
                        <option value="{{ $yr }}" {{ request('tahun') == $yr ? 'selected' : '' }}>
                            {{ $yr }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['item_id','tahun']))
                <a href="{{ route('admin.reports.forecast') }}" class="btn-secondary">Reset</a>
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
                        <th>Metode</th>
                        <th class="text-center">Periode (n)</th>
                        <th>Periode Prediksi</th>
                        <th class="text-right">Hasil Prediksi</th>
                        <th class="text-right">Aktual</th>
                        <th class="text-right">MAPE</th>
                        <th>Generate Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($forecasts as $i => $fc)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="font-medium text-sm">{{ $fc->item->nama_barang }}</td>
                            <td>
                                <span class="badge badge-indigo text-xs">{{ $fc->metode_label }}</span>
                            </td>
                            <td class="text-center">{{ $fc->periode_bulan }} bulan</td>
                            <td class="font-medium">{{ $fc->periode_prediksi }}</td>
                            <td class="text-right font-semibold text-indigo-600">
                                {{ number_format($fc->hasil_prediksi, 2) }}
                            </td>
                            <td class="text-right">
                                {{ $fc->actual_demand !== null
                                    ? number_format($fc->actual_demand, 2)
                                    : '—' }}
                            </td>
                            <td class="text-right text-sm">
                                {{ $fc->error_mape !== null
                                    ? number_format($fc->error_mape, 2) . '%'
                                    : '—' }}
                            </td>
                            <td class="text-sm text-gray-500">
                                {{ $fc->generatedBy?->name ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-gray-400">
                                Belum ada data prediksi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection