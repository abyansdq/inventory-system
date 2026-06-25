@extends('layouts.app')
@section('title', 'Detail Barang Keluar')
@section('page-title', 'Detail Barang Keluar')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('admin.stock-outs.index') }}" class="hover:text-blue-600">
                Barang Keluar
            </a>
            <span>→</span>
            <span class="text-gray-800">{{ $stockOut->no_dokumen }}</span>
        </div>
        <a href="{{ route('admin.stock-outs.index') }}" class="btn-secondary text-sm">
            ← Kembali
        </a>
    </div>

    {{-- Header --}}
    <div class="card">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                    <span class="text-xs font-medium text-red-700">Barang Keluar</span>
                </div>
                <p class="font-mono text-lg font-bold text-gray-800">
                    {{ $stockOut->no_dokumen }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    Dicatat oleh <strong>{{ $stockOut->user->name }}</strong>
                    pada {{ $stockOut->created_at->format('d F Y, H:i') }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500">Jumlah Keluar</p>
                <p class="text-3xl font-black text-red-600 mt-1">
                    -{{ number_format($stockOut->qty) }}
                </p>
                <p class="text-sm text-gray-400">
                    {{ $stockOut->item->satuan }}
                </p>
            </div>
        </div>
    </div>

    {{-- Detail --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

        {{-- Info Barang --}}
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">Informasi Barang</h3>
            <div class="flex items-center gap-3 mb-4 p-3 bg-gray-50 rounded-xl">
                <img src="{{ $stockOut->item->foto_url }}"
                     alt="{{ $stockOut->item->nama_barang }}"
                     class="w-14 h-14 rounded-xl object-cover border flex-shrink-0">
                <div>
                    <p class="font-semibold text-gray-800">
                        {{ $stockOut->item->nama_barang }}
                    </p>
                    <p class="text-xs font-mono text-gray-400">
                        {{ $stockOut->item->kode_barang }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $stockOut->item->category->nama_kategori }}
                    </p>
                </div>
            </div>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Jumlah Keluar</dt>
                    <dd class="font-bold text-red-600 text-lg">
                        -{{ number_format($stockOut->qty) }}
                        <span class="text-sm font-normal text-gray-500">
                            {{ $stockOut->item->satuan }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Stok Sekarang</dt>
                    <dd class="font-semibold
                        {{ $stockOut->item->status_stok === 'aman'
                            ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ number_format($stockOut->item->stok) }}
                        {{ $stockOut->item->satuan }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Info Dokumen --}}
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">Informasi Dokumen</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tanggal</dt>
                    <dd class="font-medium">
                        {{ $stockOut->tanggal->format('d F Y') }}
                    </dd>
                </div>

                <div class="flex justify-between">
                    <dt class="text-gray-500">Diinput Oleh</dt>
                    <dd class="font-medium">{{ $stockOut->user->name }}</dd>
                </div>

                @if($stockOut->itemRequest)
                    <div class="pt-3 border-t">
                        <dt class="text-gray-500 text-xs mb-2">
                            Dari Permintaan Barang
                        </dt>
                        <div class="bg-blue-50 rounded-xl p-3 space-y-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-blue-600">No. Permintaan</span>
                                <a href="{{ route('admin.item-requests.show', $stockOut->itemRequest) }}"
                                   class="font-mono font-semibold text-blue-700 hover:underline">
                                    {{ $stockOut->itemRequest->no_permintaan }}
                                </a>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-blue-600">Pemohon</span>
                                <span class="font-medium">
                                    {{ $stockOut->itemRequest->user->name }}
                                </span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-blue-600">Keperluan</span>
                                <span class="font-medium text-right max-w-32 truncate">
                                    {{ $stockOut->itemRequest->keperluan }}
                                </span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Jenis</dt>
                        <dd>
                            <span class="badge badge-gray text-xs">Input Manual</span>
                        </dd>
                    </div>
                @endif

                @if($stockOut->keterangan)
                    <div class="pt-3 border-t">
                        <dt class="text-gray-500 text-xs mb-1">Keterangan</dt>
                        <dd class="bg-gray-50 rounded-lg p-3 text-sm">
                            {{ $stockOut->keterangan }}
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    {{-- Aksi --}}
    <div class="flex gap-3">
        <a href="{{ route('admin.stock-outs.index') }}" class="btn-secondary">
            ← Kembali ke Daftar
        </a>
        <a href="{{ route('admin.items.show', $stockOut->item) }}"
           class="btn-secondary">
            Lihat Detail Barang
        </a>
    </div>
</div>
@endsection