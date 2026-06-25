@extends('layouts.app')
@section('title', 'EOQ — ' . $item->nama_barang)
@section('page-title', 'Perhitungan EOQ')

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb Info --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">
                <a href="{{ route('admin.eoq.index') }}" class="hover:text-blue-600">
                    EOQ
                </a>
                → {{ $item->nama_barang }}
            </p>
        </div>
        <a href="{{ route('admin.eoq.index') }}" class="btn-secondary text-sm">← Kembali</a>
    </div>

    {{-- Alert: Data tidak cukup --}}
    @if(!$demandData)
        <div class="card bg-yellow-50 border-yellow-200">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="text-sm text-yellow-800">
                    <p class="font-semibold">Data histori permintaan belum mencukupi</p>
                    <p class="mt-1">
                        Dibutuhkan minimal <strong>3 bulan</strong> data histori untuk menghitung EOQ.
                        Tambahkan histori permintaan terlebih dahulu.
                    </p>
                    <a href="{{ route('admin.eoq.show', $item) }}#histori"
                       class="mt-2 inline-block text-yellow-700 underline">
                        Input Histori Permintaan →
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KIRI: Form Kalkulasi + Hasil --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Form Hitung EOQ --}}
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">
                    🔢 Input Parameter EOQ
                </h3>
                <form action="{{ route('admin.eoq.calculate', $item) }}"
                      method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">
                                Demand Tahunan (D)
                                <span class="text-xs text-gray-400 font-normal">
                                    — unit/tahun
                                </span>
                            </label>
                            <input type="number" name="demand_tahunan" step="0.01"
                                   value="{{ old('demand_tahunan', $demandData ? number_format($demandData['demand_tahunan'], 2, '.', '') : '') }}"
                                   placeholder="{{ $demandData ? 'Auto: ' . number_format($demandData['demand_tahunan'], 2) : 'Dari histori...' }}"
                                   class="form-input">
                            <p class="text-xs text-gray-400 mt-1">
                                Kosongkan untuk otomatis dari histori
                            </p>
                        </div>

                        <div>
                            <label class="form-label">
                                Biaya Pemesanan (S)
                                <span class="text-xs text-gray-400 font-normal">
                                    — Rp/pesan
                                </span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                <input type="number" name="ordering_cost" step="100"
                                       value="{{ old('ordering_cost', $item->ordering_cost) }}"
                                       class="form-input pl-10">
                            </div>
                        </div>

                        <div>
                            <label class="form-label">
                                Biaya Penyimpanan (H)
                                <span class="text-xs text-gray-400 font-normal">
                                    — Rp/unit/tahun
                                </span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                <input type="number" name="holding_cost" step="100"
                                       value="{{ old('holding_cost', $item->holding_cost) }}"
                                       class="form-input pl-10">
                            </div>
                        </div>

                        <div>
                            <label class="form-label">
                                Lead Time (L)
                                <span class="text-xs text-gray-400 font-normal">— hari</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="lead_time" min="1"
                                       value="{{ old('lead_time', $item->lead_time) }}"
                                       class="form-input pr-12">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">hari</span>
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="keterangan"
                                   value="{{ old('keterangan') }}"
                                   placeholder="Opsional..."
                                   class="form-input">
                        </div>
                    </div>

                    <button type="submit"
                            class="btn-primary w-full justify-center"
                            {{ !$demandData ? 'disabled' : '' }}>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Hitung EOQ Sekarang
                    </button>
                </form>
            </div>

            {{-- Hasil Kalkulasi Terbaru --}}
            @if($summary && $latest)
            <div class="card border-2 border-blue-100">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-semibold text-gray-800">📊 Hasil Kalkulasi Terbaru</h3>
                    <span class="text-xs text-gray-400">
                        {{ $latest->tanggal_hitung->format('d F Y, H:i') }}
                    </span>
                </div>

                {{-- Hasil Utama --}}
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
                        <p class="text-xs text-purple-600 font-medium mb-1">Safety Stock</p>
                        <p class="text-3xl font-black text-purple-700">
                            {{ number_format($summary['safety_stock'], 0) }}
                        </p>
                        <p class="text-xs text-purple-500 mt-1">{{ $item->satuan }}</p>
                    </div>
                </div>

                {{-- Tabel Detail --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 text-gray-500 font-medium">Parameter</th>
                                <th class="text-left py-2 text-gray-500 font-medium">Notasi</th>
                                <th class="text-right py-2 text-gray-500 font-medium">Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr>
                                <td class="py-2">Demand Tahunan</td>
                                <td class="py-2 text-gray-400 font-mono text-xs">D</td>
                                <td class="py-2 text-right font-medium">
                                    {{ number_format($summary['demand_tahunan'], 2) }} {{ $item->satuan }}/tahun
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
                                    Rp {{ number_format($summary['holding_cost'], 0, ',', '.') }}/unit/tahun
                                </td>
                            </tr>
                            <tr class="bg-blue-50">
                                <td class="py-2 font-semibold text-blue-800">EOQ = √(2DS/H)</td>
                                <td class="py-2 text-blue-400 font-mono text-xs">Q*</td>
                                <td class="py-2 text-right font-bold text-blue-800">
                                    {{ number_format($summary['eoq'], 2) }} {{ $item->satuan }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2">Lead Time</td>
                                <td class="py-2 text-gray-400 font-mono text-xs">L</td>
                                <td class="py-2 text-right">{{ $summary['lead_time'] }} hari</td>
                            </tr>
                            <tr>
                                <td class="py-2">Demand Harian Rata-rata</td>
                                <td class="py-2 text-gray-400 font-mono text-xs">d</td>
                                <td class="py-2 text-right">
                                    {{ number_format($latest->demand_harian_avg, 4) }} {{ $item->satuan }}/hari
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2">Demand Harian Maksimum</td>
                                <td class="py-2 text-gray-400 font-mono text-xs">d_max</td>
                                <td class="py-2 text-right">
                                    {{ number_format($latest->demand_harian_max, 4) }} {{ $item->satuan }}/hari
                                </td>
                            </tr>
                            <tr class="bg-purple-50">
                                <td class="py-2 font-semibold text-purple-800">
                                    Safety Stock = (d_max - d) × L
                                </td>
                                <td class="py-2 text-purple-400 font-mono text-xs">SS</td>
                                <td class="py-2 text-right font-bold text-purple-800">
                                    {{ number_format($summary['safety_stock'], 2) }} {{ $item->satuan }}
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
                            <tr class="border-t">
                                <td class="py-2">Frekuensi Pemesanan</td>
                                <td class="py-2 text-gray-400 font-mono text-xs">f</td>
                                <td class="py-2 text-right">
                                    {{ number_format($summary['frekuensi_pesan'], 2) }}x/tahun
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2">Interval Pemesanan</td>
                                <td></td>
                                <td class="py-2 text-right">
                                    ± {{ number_format($summary['interval_pesan'], 0) }} hari sekali
                                </td>
                            </tr>
                            <tr class="border-t">
                                <td class="py-2 text-gray-500">Total Biaya Pesan/Tahun</td>
                                <td></td>
                                <td class="py-2 text-right">
                                    Rp {{ number_format($summary['total_biaya_pesan'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 text-gray-500">Total Biaya Simpan/Tahun</td>
                                <td></td>
                                <td class="py-2 text-right">
                                    Rp {{ number_format($summary['total_biaya_simpan'], 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="bg-green-50 font-semibold">
                                <td class="py-2 text-green-800">Total Biaya Inventory</td>
                                <td class="py-2 text-green-400 font-mono text-xs">TIC</td>
                                <td class="py-2 text-right text-green-800">
                                    Rp {{ number_format($summary['total_biaya'], 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Interpretasi --}}
                <div class="mt-5 p-4 bg-gray-50 rounded-xl text-sm text-gray-700 leading-relaxed">
                    <p class="font-semibold text-gray-800 mb-2">📝 Interpretasi Hasil:</p>
                    <p>{!! $summary['interpretasi'] !!}</p>
                </div>
            </div>
            @endif

            {{-- Riwayat Kalkulasi --}}
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">Riwayat Kalkulasi EOQ</h3>
                @if($calculations->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-6">
                        Belum ada riwayat kalkulasi
                    </p>
                @else
                    <div class="table-container">
                        <table class="table text-xs">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th class="text-right">D</th>
                                    <th class="text-right">S</th>
                                    <th class="text-right">H</th>
                                    <th class="text-center">LT</th>
                                    <th class="text-right font-semibold">EOQ</th>
                                    <th class="text-right">ROP</th>
                                    <th class="text-right">SS</th>
                                    <th>Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($calculations as $calc)
                                    <tr>
                                        <td>{{ $calc->tanggal_hitung->format('d/m/Y') }}</td>
                                        <td class="text-right">{{ number_format($calc->demand_tahunan, 0) }}</td>
                                        <td class="text-right">{{ number_format($calc->ordering_cost, 0) }}</td>
                                        <td class="text-right">{{ number_format($calc->holding_cost, 0) }}</td>
                                        <td class="text-center">{{ $calc->lead_time }}h</td>
                                        <td class="text-right font-bold text-blue-600">
                                            {{ number_format($calc->eoq_result, 2) }}
                                        </td>
                                        <td class="text-right text-orange-600">
                                            {{ number_format($calc->rop_result, 2) }}
                                        </td>
                                        <td class="text-right text-purple-600">
                                            {{ number_format($calc->safety_stock, 2) }}
                                        </td>
                                        <td class="text-gray-500">{{ $calc->calculatedBy->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($calculations->hasPages())
                        <div class="mt-3">{{ $calculations->links() }}</div>
                    @endif
                @endif
            </div>
        </div>

        {{-- KANAN: Info Barang + Histori Demand --}}
        <div class="space-y-6">

            {{-- Info Barang --}}
            <div class="card">
                <h3 class="font-semibold text-gray-800 mb-4">Info Barang</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Kode</span>
                        <span class="font-mono font-medium">{{ $item->kode_barang }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Satuan</span>
                        <span class="font-medium">{{ strtoupper($item->satuan) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Stok Saat Ini</span>
                        <span class="font-medium {{ $item->status_stok === 'aman' ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($item->stok) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Stok Minimum</span>
                        <span class="font-medium">{{ number_format($item->stok_minimum) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Safety Stock</span>
                        <span class="font-medium">{{ number_format($item->safety_stock) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Lead Time</span>
                        <span class="font-medium">{{ $item->lead_time }} hari</span>
                    </div>
                </div>
            </div>

            {{-- Histori Demand --}}
            <div class="card" id="histori">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">Histori Permintaan</h3>
                    <span class="badge badge-blue">{{ $demandHistories->count() }} bulan</span>
                </div>

                {{-- Form tambah demand --}}
                <form action="{{ route('admin.demand-histories.store') }}"
                      method="POST" class="mb-4 space-y-3"
                      x-data="{ open: false }">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $item->id }}">

                    <button type="button" @click="open = !open"
                            class="w-full text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah / Update Histori
                    </button>

                    <div x-show="open" x-transition class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="form-label text-xs">Tahun</label>
                            <input type="number" name="tahun" value="{{ date('Y') }}"
                                   min="2020" max="{{ date('Y') }}"
                                   class="form-input text-sm">
                        </div>
                        <div>
                            <label class="form-label text-xs">Bulan</label>
                            <select name="bulan" class="form-select text-sm">
                                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $idx => $bln)
                                    <option value="{{ $idx + 1 }}" {{ date('n') == ($idx + 1) ? 'selected' : '' }}>
                                        {{ $bln }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label text-xs">Jumlah</label>
                            <input type="number" name="jumlah_permintaan" min="0"
                                   class="form-input text-sm" placeholder="0">
                        </div>
                        <div class="col-span-3">
                            <button type="submit" class="btn-primary w-full text-sm py-1.5">
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>

                {{-- List histori --}}
                <div class="space-y-1 max-h-80 overflow-y-auto">
                    @forelse($demandHistories as $hist)
                        <div class="flex items-center justify-between py-1.5 border-b
                                    border-gray-50 last:border-0 text-sm">
                            <span class="text-gray-600">{{ $hist->periode }}</span>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold">
                                    {{ number_format($hist->jumlah_permintaan) }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $item->satuan }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">
                            Belum ada histori permintaan
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection