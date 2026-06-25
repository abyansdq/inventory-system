@extends('layouts.app')
@section('title', $item->nama_barang)
@section('page-title', 'Detail Barang')

@section('content')
<div class="space-y-6">

    {{-- Header Card --}}
    <div class="card">
        <div class="flex flex-col sm:flex-row gap-6">
            <img src="{{ $item->foto_url }}" alt="{{ $item->nama_barang }}"
                 class="w-32 h-32 rounded-xl object-cover border flex-shrink-0">
            <div class="flex-1">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-mono text-gray-400">{{ $item->kode_barang }}</p>
                        <h2 class="text-xl font-bold text-gray-800 mt-1">{{ $item->nama_barang }}</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $item->category->nama_kategori }} •
                            {{ $item->supplier->nama_supplier }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.items.edit', $item) }}" class="btn-secondary text-sm">
                            Edit
                        </a>
                    </div>
                </div>

                {{-- Status Badges --}}
                <div class="flex flex-wrap gap-2 mt-4">
                    <span class="badge badge-{{ $item->status_stok_color }}">
                        Stok: {{ ucfirst($item->status_stok) }}
                    </span>
                    @if($item->is_active)
                        <span class="badge badge-green">Aktif</span>
                    @else
                        <span class="badge badge-gray">Nonaktif</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Stok --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card text-center">
            <p class="text-xs text-gray-500 mb-1">Stok Saat Ini</p>
            <p class="text-2xl font-bold {{ $summary['stok_saat_ini'] <= $summary['stok_minimum'] ? 'text-red-600' : 'text-green-600' }}">
                {{ number_format($summary['stok_saat_ini']) }}
            </p>
            <p class="text-xs text-gray-400">{{ $item->satuan }}</p>
        </div>
        <div class="card text-center">
            <p class="text-xs text-gray-500 mb-1">Stok Minimum</p>
            <p class="text-2xl font-bold text-gray-700">{{ number_format($summary['stok_minimum']) }}</p>
            <p class="text-xs text-gray-400">{{ $item->satuan }}</p>
        </div>
        <div class="card text-center">
            <p class="text-xs text-gray-500 mb-1">Safety Stock</p>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($summary['safety_stock']) }}</p>
            <p class="text-xs text-gray-400">{{ $item->satuan }}</p>
        </div>
        <div class="card text-center">
            <p class="text-xs text-gray-500 mb-1">Nilai Stok</p>
            <p class="text-lg font-bold text-purple-600">
                Rp {{ number_format($summary['nilai_stok'], 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Info & Parameter --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">Informasi Barang</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Satuan</dt>
                    <dd class="font-medium">{{ strtoupper($item->satuan) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Harga Beli</dt>
                    <dd class="font-medium">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Harga Jual</dt>
                    <dd class="font-medium">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Lead Time</dt>
                    <dd class="font-medium">{{ $item->lead_time }} hari</dd>
                </div>
            </dl>
        </div>
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">Parameter EOQ</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Biaya Pemesanan (S)</dt>
                    <dd class="font-medium">Rp {{ number_format($item->ordering_cost, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Biaya Penyimpanan (H)</dt>
                    <dd class="font-medium">Rp {{ number_format($item->holding_cost, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between border-t pt-3">
                    <dt class="text-gray-500">Masuk Bulan Ini</dt>
                    <dd class="font-medium text-green-600">+{{ number_format($summary['masuk_bulan_ini']) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Keluar Bulan Ini</dt>
                    <dd class="font-medium text-red-600">-{{ number_format($summary['keluar_bulan_ini']) }}</dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Grafik Pergerakan --}}
    <div class="card">
        <h3 class="font-semibold text-gray-800 mb-4">Pergerakan Stok 30 Hari Terakhir</h3>
        <canvas id="stockChart" height="80"></canvas>
    </div>

    {{-- Riwayat Masuk / Keluar --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">Barang Masuk Terakhir</h3>
            @forelse($latestStockIn as $si)
                <div class="flex justify-between items-center py-2 border-b last:border-0 text-sm">
                    <div>
                        <p class="font-medium">{{ $si->no_dokumen }}</p>
                        <p class="text-xs text-gray-400">{{ $si->tanggal->format('d/m/Y') }} • {{ $si->user->name }}</p>
                    </div>
                    <span class="text-green-600 font-semibold">+{{ number_format($si->qty) }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
            @endforelse
        </div>
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">Barang Keluar Terakhir</h3>
            @forelse($latestStockOut as $so)
                <div class="flex justify-between items-center py-2 border-b last:border-0 text-sm">
                    <div>
                        <p class="font-medium">{{ $so->no_dokumen }}</p>
                        <p class="text-xs text-gray-400">{{ $so->tanggal->format('d/m/Y') }} • {{ $so->user->name }}</p>
                    </div>
                    <span class="text-red-600 font-semibold">-{{ number_format($so->qty) }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('stockChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartData['labels']),
            datasets: [
                {
                    label: 'Masuk',
                    data: @json($chartData['masuk']),
                    backgroundColor: 'rgba(34,197,94,0.6)',
                    borderColor: 'rgb(34,197,94)',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Keluar',
                    data: @json($chartData['keluar']),
                    backgroundColor: 'rgba(239,68,68,0.6)',
                    borderColor: 'rgb(239,68,68)',
                    borderWidth: 1,
                    borderRadius: 4,
                },
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 10 } }
            }
        }
    });
</script>
@endpush