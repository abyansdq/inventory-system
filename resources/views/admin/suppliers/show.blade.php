@extends('layouts.app')
@section('title', 'Detail Supplier')
@section('page-title', 'Detail Supplier')

@section('content')
<div class="max-w-4xl space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('admin.suppliers.index') }}" class="hover:text-blue-600">
                Supplier
            </a>
            <span>→</span>
            <span class="text-gray-800">{{ $supplier->nama_supplier }}</span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.suppliers.edit', $supplier) }}"
               class="btn-secondary text-sm">Edit</a>
            <a href="{{ route('admin.suppliers.index') }}"
               class="btn-secondary text-sm">← Kembali</a>
        </div>
    </div>

    {{-- Info Utama Supplier --}}
    <div class="card">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-purple-100 rounded-2xl
                            flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-purple-600" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-mono text-gray-400">
                        {{ $supplier->kode_supplier }}
                    </p>
                    <h2 class="text-xl font-bold text-gray-800 mt-0.5">
                        {{ $supplier->nama_supplier }}
                    </h2>
                    @if($supplier->kota)
                        <p class="text-sm text-gray-500 mt-1">
                            📍 {{ $supplier->kota }}
                        </p>
                    @endif
                </div>
            </div>
            @if($supplier->is_active)
                <span class="badge badge-green">Aktif</span>
            @else
                <span class="badge badge-gray">Nonaktif</span>
            @endif
        </div>

        {{-- Grid Info --}}
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div class="space-y-3">
                <div>
                    <p class="text-gray-500 text-xs mb-0.5">Contact Person</p>
                    <p class="font-medium">
                        {{ $supplier->contact_person ?? '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs mb-0.5">Telepon</p>
                    <p class="font-medium">{{ $supplier->telepon ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs mb-0.5">Email</p>
                    <p class="font-medium">
                        @if($supplier->email)
                            <a href="mailto:{{ $supplier->email }}"
                               class="text-blue-600 hover:underline">
                                {{ $supplier->email }}
                            </a>
                        @else
                            —
                        @endif
                    </p>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-gray-500 text-xs mb-0.5">Alamat</p>
                    <p class="font-medium">{{ $supplier->alamat ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs mb-0.5">Kota</p>
                    <p class="font-medium">{{ $supplier->kota ?? '—' }}</p>
                </div>
                @if($supplier->keterangan)
                    <div>
                        <p class="text-gray-500 text-xs mb-0.5">Keterangan</p>
                        <p class="font-medium">{{ $supplier->keterangan }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="card text-center py-3">
            <p class="text-2xl font-bold text-blue-600">
                {{ $supplier->items->count() }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Barang Terkait</p>
        </div>
        <div class="card text-center py-3">
            <p class="text-2xl font-bold text-green-600">
                {{ $stockIns->total() }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Total Transaksi Masuk</p>
        </div>
        <div class="card text-center py-3">
            <p class="text-2xl font-bold text-purple-600">
                {{ $procurements->count() }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Pengadaan</p>
        </div>
    </div>

    {{-- Barang dari Supplier ini --}}
    <div class="card">
        <h3 class="font-semibold text-gray-800 mb-4">
            Barang dari Supplier Ini
        </h3>
        @if($supplier->items->isEmpty())
            <p class="text-sm text-gray-400 text-center py-6">
                Belum ada barang dari supplier ini
            </p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($supplier->items->take(6) as $item)
                    <div class="flex items-center gap-3 p-3
                                bg-gray-50 rounded-xl border border-gray-100">
                        <img src="{{ $item->foto_url }}"
                             alt="{{ $item->nama_barang }}"
                             class="w-10 h-10 rounded-lg object-cover border flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm text-gray-800 truncate">
                                {{ $item->nama_barang }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Stok: {{ number_format($item->stok) }} {{ $item->satuan }}
                            </p>
                        </div>
                        <span class="badge badge-{{ $item->status_stok_color }} text-xs">
                            {{ ucfirst($item->status_stok) }}
                        </span>
                    </div>
                @endforeach
            </div>
            @if($supplier->items->count() > 6)
                <p class="text-xs text-gray-400 text-center mt-3">
                    Dan {{ $supplier->items->count() - 6 }} barang lainnya...
                </p>
            @endif
        @endif
    </div>

    {{-- Riwayat Barang Masuk --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">Riwayat Barang Masuk</h3>
            <span class="badge badge-blue">{{ $stockIns->total() }} transaksi</span>
        </div>

        @if($stockIns->isEmpty())
            <p class="text-sm text-gray-400 text-center py-6">
                Belum ada transaksi barang masuk
            </p>
        @else
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No Dokumen</th>
                            <th>Barang</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Total</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockIns as $si)
                            <tr>
                                <td>
                                    <span class="font-mono text-xs bg-green-50
                                                 text-green-700 px-2 py-1 rounded">
                                        {{ $si->no_dokumen }}
                                    </span>
                                </td>
                                <td class="font-medium text-sm">
                                    {{ $si->item->nama_barang }}
                                </td>
                                <td class="text-center font-semibold text-green-600">
                                    +{{ number_format($si->qty) }}
                                </td>
                                <td class="text-right text-sm">
                                    Rp {{ number_format($si->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="text-sm">
                                    {{ $si->tanggal->format('d/m/Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($stockIns->hasPages())
                <div class="mt-4">{{ $stockIns->links() }}</div>
            @endif
        @endif
    </div>
</div>
@endsection