@extends('layouts.app')
@section('title', 'EOQ — ' . $item->nama_barang)
@section('page-title', 'Detail EOQ')

@section('content')
<div class="max-w-4xl space-y-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('manajer.eoq.index') }}" class="hover:text-blue-600">EOQ</a>
            <span>→</span>
            <span class="text-gray-800">{{ $item->nama_barang }}</span>
        </div>
        <a href="{{ route('manajer.eoq.index') }}" class="btn-secondary text-sm">
            ← Kembali
        </a>
    </div>

    @if(!$demandData)
        <div class="card bg-yellow-50 border-yellow-200">
            <p class="text-sm text-yellow-800">
                ⚠️ Data histori permintaan belum mencukupi untuk kalkulasi EOQ.
                Minimal 3 bulan data diperlukan.
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kiri: Hasil EOQ --}}
        <div class="lg:col-span-2 space-y-6">
            @if($summary && $latest)
            <div class="card border-2 border-blue-100">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-semibold text-gray-800">
                        📊 Hasil Kalkulasi EOQ Terbaru
                    </h3>
                    <span class="text-xs text-gray-400">
                        {{ $latest->tanggal_hitung->format('d F Y, H:i') }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="text-center p-4 bg-blue-50 rounded-xl">
                        <p class="text-xs text-blue-600 font-medium mb-1">EOQ</p>
                        <p class="text-3xl font-black text-blue-700">
                            {{ number_format($summary['eoq'], 0) }}
                        </p>
                        <p class="text-xs text-blue-500 mt-1">{{ $item->satuan }}</p>
                    </div>
                    <div class="text-center p-4 bg-orange-50 rounded-xl">
                        <p class="text-xs text-orange-600 font-medium mb-1">ROP</p>
                        <p class="text-3xl font-black text-orange-700">
                            {{ number_format($summary['rop'], 0) }}
                        </p>
                        <p class="text-xs text-orange-500 mt-1">{{ $item->satuan }}</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-xl">
                        <p class="text-xs text-purple-600 font-medium mb-1">
                            Safety Stock
                        </p>
                        <p class="text-3xl font-black text-purple-700">
                            {{ number_format($summary['safety_stock'], 0) }}
                        </p>
                        <p class="text-xs text-purple-500 mt-1">{{ $item->satuan }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 text-gray-500 font-medium">
                                    Parameter
                                </th>
                                <th class="text-left py-2 text-gray-500 font-medium">
                                    Notasi
                                </th>
                                <th class="text-right py-2 text-gray-500 font-medium">
                                    Nilai
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr>
                                <td class="py-2">Demand Tahunan</td>
                                <td class="py-2 text-gray-400 font-mono text-xs">D</td>
                                <td class="py-2 text-right font-medium">
                                    {{ number_format($summary['demand_tahunan'], 2) }}
                                    {{ $item->satuan }}/tahun
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2">Biaya Pemesanan</td>
                                <td class="py-2 text-gray-400 font-mono text-xs">S</td>
                                <td class="py-2 text-right font-medium">
                                    Rp {{ number_format($summary['ordering_cost'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2">Biaya Penyimpanan</td>
                                <td class="py-2 text-gray-400 font-mono text-xs">H</td>
                                <td class="py-2 text-right font-medium">
                                    Rp {{ number_format($summary['holding_cost'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="bg-blue-50">
                                <td class="py-2 font-semibold text-blue-800">
                                    EOQ = √(2DS/H)
                                </td>
                                <td class="py-2 text-blue-400 font-mono text-xs">Q*</td>
                                <td class="py-2 text-right font-bold text-blue-800">
                                    {{ number_format($summary['eoq'], 2) }} {{ $item->satuan }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2">Lead Time</td>
                                <td class="py-2 text-gray-400 font-mono text-xs">L</td>
                                <td class="py-2 text-right">
                                    {{ $summary['lead_time'] }} hari
                                </td>
                            </tr>
                            <tr class="bg-purple-50">
                                <td class="py-2 font-semibold text-purple-800">
                                    Safety Stock = (d_max - d) × L
                                </td>
                                <td class="py-2 text-purple-400 font-mono text-xs">SS</td>
                                <td class="py-2 text-right font-bold text-purple-800">
                                    {{ number_format($summary['safety_stock'], 2) }}
                                    {{ $item->satuan }}
                                </td>
                            </tr>
                            <tr class="bg-orange-50">
                                <td class="py-2 font-semibold text-orange-800">
                                    ROP = (d × L) + SS
                                </td>
                                <td class="py-2 text-orange-400 font-mono text-xs">ROP</td>
                                <td class="py-2 text-right font-bold text-orange-800">
                                    {{ number_format($summary['rop'], 2) }} {{ $item->satuan }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2">Frekuensi Pemesanan</td>
                                <td></td>
                                <td class="py-2 text-right">
                                    {{ number_format($summary['frekuensi_pesan'], 2) }}x/tahun
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2">Interval Pemesanan</td>
                                <td></td>
                                <td class="py-2 text-right">
                                    ±{{ number_format($summary['interval_pesan'], 0) }} hari sekali
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 p-4 bg-gray-50 rounded-xl text-sm text-gray-700 leading-relaxed">
                    <p class="font-semibold text-gray-800 mb-2">📝 Interpretasi:</p>
                    <p>{!! $summary['interpretasi'] !!}</p>
                </div>
            </div>
            @else
                <div class="card text-center py-12">
                    <p class="text-gray-400">Belum ada hasil kalkulasi EOQ untuk barang ini.</p>
                    <p class="text-sm text-gray-400 mt-1">
                        Hubungi Admin Gudang untuk melakukan kalkulasi.
                    </p>
                </div>
            @endif

            {{-- Riwayat --}}
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">Riwayat Kalkulasi</h3>
                @if($calculations->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-4">
                        Belum ada riwayat
                    </p>
                @else
                    <div class="table-container">
                        <table class="table text-xs">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th class="text-right">EOQ</th>
                                    <th class="text-right">ROP</th>
                                    <th class="text-right">SS</th>
                                    <th>Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($calculations as $calc)
                                    <tr>
                                        <td>{{ $calc->tanggal_hitung->format('d/m/Y') }}</td>
                                        <td class="text-right font-bold text-blue-600">
                                            {{ number_format($calc->eoq_result, 2) }}
                                        </td>
                                        <td class="text-right text-orange-600">
                                            {{ number_format($calc->rop_result, 2) }}
                                        </td>
                                        <td class="text-right text-purple-600">
                                            {{ number_format($calc->safety_stock, 2) }}
                                        </td>
                                        <td class="text-gray-500">
                                            {{ $calc->calculatedBy->name }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kanan: Info Barang + Histori --}}
        <div class="space-y-6">
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">Info Barang</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Stok Saat Ini</dt>
                        <dd class="font-semibold
                            {{ $item->status_stok === 'aman' ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($item->stok) }} {{ $item->satuan }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Stok Minimum (ROP)</dt>
                        <dd class="font-medium">{{ number_format($item->stok_minimum) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Safety Stock</dt>
                        <dd class="font-medium">{{ number_format($item->safety_stock) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Lead Time</dt>
                        <dd class="font-medium">{{ $item->lead_time }} hari</dd>
                    </div>
                </dl>
                @if($item->status_stok !== 'aman')
                    <div class="mt-4 pt-4 border-t">
                        <a href="{{ route('manajer.procurements.create') }}?item_id={{ $item->id }}"
                           class="btn-primary w-full justify-center text-sm">
                            🛒 Buat Pengadaan
                        </a>
                    </div>
                @endif
            </div>

            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">
                    Histori Permintaan
                    <span class="text-xs text-gray-400 font-normal">
                        ({{ $demandHistories->count() }} bulan)
                    </span>
                </h3>
                <div class="space-y-1 max-h-64 overflow-y-auto">
                    @forelse($demandHistories as $h)
                        <div class="flex justify-between items-center py-1.5
                                    border-b border-gray-50 last:border-0 text-sm">
                            <span class="text-gray-600">{{ $h->periode }}</span>
                            <span class="font-semibold">
                                {{ number_format($h->jumlah_permintaan) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">
                            Belum ada histori
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection