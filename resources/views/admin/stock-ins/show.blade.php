@extends('layouts.app')
@section('title', 'Detail Barang Masuk')
@section('page-title', 'Detail Barang Masuk')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('admin.stock-ins.index') }}" class="hover:text-blue-600">
                Barang Masuk
            </a>
            <span>→</span>
            <span class="text-gray-800">{{ $stockIn->no_dokumen }}</span>
        </div>
        <a href="{{ route('admin.stock-ins.index') }}" class="btn-secondary text-sm">
            ← Kembali
        </a>
    </div>

    {{-- Header --}}
    <div class="card">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                    <span class="text-xs font-medium text-green-700">Barang Masuk</span>
                </div>
                <p class="font-mono text-lg font-bold text-gray-800">
                    {{ $stockIn->no_dokumen }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    Dicatat oleh <strong>{{ $stockIn->user->name }}</strong>
                    pada {{ $stockIn->created_at->format('d F Y, H:i') }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500">Total Nilai</p>
                <p class="text-2xl font-black text-green-600 mt-1">
                    Rp {{ number_format($stockIn->total_harga, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Detail Transaksi --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

        {{-- Info Barang --}}
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">Informasi Barang</h3>
            <div class="flex items-center gap-3 mb-4 p-3 bg-gray-50 rounded-xl">
                <img src="{{ $stockIn->item->foto_url }}"
                     alt="{{ $stockIn->item->nama_barang }}"
                     class="w-14 h-14 rounded-xl object-cover border flex-shrink-0">
                <div>
                    <p class="font-semibold text-gray-800">
                        {{ $stockIn->item->nama_barang }}
                    </p>
                    <p class="text-xs font-mono text-gray-400">
                        {{ $stockIn->item->kode_barang }}
                    </p>
                </div>
            </div>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Jumlah Masuk</dt>
                    <dd class="font-bold text-green-600 text-lg">
                        +{{ number_format($stockIn->qty) }}
                        <span class="text-sm font-normal text-gray-500">
                            {{ $stockIn->item->satuan }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Harga Satuan</dt>
                    <dd class="font-medium">
                        Rp {{ number_format($stockIn->harga_satuan, 0, ',', '.') }}
                    </dd>
                </div>
                <div class="flex justify-between border-t pt-3">
                    <dt class="text-gray-500 font-medium">Total Harga</dt>
                    <dd class="font-bold text-green-700">
                        Rp {{ number_format($stockIn->total_harga, 0, ',', '.') }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Stok Sekarang</dt>
                    <dd class="font-semibold
                        {{ $stockIn->item->status_stok === 'aman'
                            ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ number_format($stockIn->item->stok) }}
                        {{ $stockIn->item->satuan }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Info Supplier & Dokumen --}}
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">Info Supplier & Dokumen</h3>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500 text-xs mb-1">Supplier</dt>
                    <dd class="font-semibold">
                        {{ $stockIn->supplier->nama_supplier }}
                    </dd>
                    <dd class="text-xs text-gray-400">
                        {{ $stockIn->supplier->telepon ?? '' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tanggal</dt>
                    <dd class="font-medium">
                        {{ $stockIn->tanggal->format('d F Y') }}
                    </dd>
                </div>
                @if($stockIn->procurement)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">No. Pengadaan</dt>
                        <dd>
                            <a href="{{ route('admin.procurements.show', $stockIn->procurement) }}"
                               class="font-mono text-xs text-blue-600 hover:underline">
                                {{ $stockIn->procurement->no_pengadaan }}
                            </a>
                        </dd>
                    </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-gray-500">Diinput Oleh</dt>
                    <dd class="font-medium">{{ $stockIn->user->name }}</dd>
                </div>
                @if($stockIn->keterangan)
                    <div class="pt-3 border-t">
                        <dt class="text-gray-500 text-xs mb-1">Keterangan</dt>
                        <dd class="bg-gray-50 rounded-lg p-3 text-sm">
                            {{ $stockIn->keterangan }}
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    {{-- Aksi --}}
    <div class="flex gap-3">
        <a href="{{ route('admin.stock-ins.index') }}" class="btn-secondary">
            ← Kembali ke Daftar
        </a>
        <a href="{{ route('admin.items.show', $stockIn->item) }}"
           class="btn-secondary">
            Lihat Detail Barang
        </a>
    </div>
</div>
@endsection