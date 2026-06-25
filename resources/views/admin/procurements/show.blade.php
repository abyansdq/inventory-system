@extends('layouts.app')
@section('title', 'Detail Pengadaan')
@section('page-title', 'Detail Pengadaan')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('admin.procurements.index') }}" class="hover:text-blue-600">
                Pengadaan
            </a>
            <span>→</span>
            <span class="text-gray-800">{{ $procurement->no_pengadaan }}</span>
        </div>
        <a href="{{ route('admin.procurements.index') }}" class="btn-secondary text-sm">
            ← Kembali
        </a>
    </div>

    {{-- Header Status --}}
    <div class="card">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-mono text-gray-400">
                    {{ $procurement->no_pengadaan }}
                </p>
                <h2 class="text-xl font-bold text-gray-800 mt-1">
                    {{ $procurement->item->nama_barang }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Diajukan oleh <strong>{{ $procurement->user->name }}</strong>
                    pada {{ $procurement->tanggal->format('d F Y') }}
                </p>
            </div>
            <div class="text-right">
                <span class="badge badge-{{ $procurement->status_color }} text-sm px-3 py-1">
                    {{ $procurement->status_label }}
                </span>
                <p class="text-xs text-gray-400 mt-2">Status</p>
            </div>
        </div>

        {{-- Progress --}}
        <div class="mt-6">
            @php
                $steps = [
                    ['key' => 'pending',  'label' => 'Diajukan'],
                    ['key' => 'approved', 'label' => 'Disetujui'],
                    ['key' => 'received', 'label' => 'Diterima'],
                ];
                $order   = ['draft'=>-1,'pending'=>0,'approved'=>1,'ordered'=>1,'received'=>2];
                $current = $order[$procurement->status] ?? -1;
                $isRejected  = $procurement->status === 'rejected';
                $isCancelled = $procurement->status === 'cancelled';
            @endphp

            @if($isRejected || $isCancelled)
                <div class="p-3 bg-red-50 border border-red-200 rounded-xl
                            flex items-center gap-3">
                    <span class="text-xl">❌</span>
                    <div>
                        <p class="font-semibold text-red-700 text-sm">
                            Pengadaan {{ $isRejected ? 'Ditolak' : 'Dibatalkan' }}
                        </p>
                        @if($procurement->catatan)
                            <p class="text-xs text-red-600 mt-0.5">
                                {{ $procurement->catatan }}
                            </p>
                        @endif
                    </div>
                </div>
            @else
                <div class="flex items-center">
                    @foreach($steps as $idx => $step)
                        @php $done = $current >= $idx; @endphp
                        <div class="flex flex-col items-center">
                            <div class="w-9 h-9 rounded-full flex items-center
                                        justify-center text-sm font-bold shadow-sm
                                        {{ $done
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-gray-100 text-gray-400 border-2 border-gray-200' }}">
                                {{ $done ? '✓' : ($idx + 1) }}
                            </div>
                            <p class="text-xs mt-1 font-medium
                                      {{ $done ? 'text-blue-600' : 'text-gray-400' }}">
                                {{ $step['label'] }}
                            </p>
                        </div>
                        @if(!$loop->last)
                            <div class="flex-1 h-0.5 mb-5
                                        {{ $current > $idx ? 'bg-blue-500' : 'bg-gray-200' }}">
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Detail --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

        {{-- Info Barang --}}
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">Detail Pengadaan</h3>
            <div class="flex items-center gap-3 mb-4 p-3 bg-gray-50 rounded-xl">
                <img src="{{ $procurement->item->foto_url }}"
                     alt="{{ $procurement->item->nama_barang }}"
                     class="w-12 h-12 rounded-xl object-cover border flex-shrink-0">
                <div>
                    <p class="font-semibold text-sm">
                        {{ $procurement->item->nama_barang }}
                    </p>
                    <p class="text-xs font-mono text-gray-400">
                        {{ $procurement->item->kode_barang }}
                    </p>
                </div>
            </div>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Jumlah</dt>
                    <dd class="font-bold text-blue-700 text-lg">
                        {{ number_format($procurement->qty) }}
                        <span class="text-sm font-normal text-gray-500">
                            {{ $procurement->item->satuan }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Harga Satuan</dt>
                    <dd class="font-medium">
                        Rp {{ number_format($procurement->harga_satuan, 0, ',', '.') }}
                    </dd>
                </div>
                <div class="flex justify-between border-t pt-3">
                    <dt class="font-medium text-gray-700">Total Harga</dt>
                    <dd class="font-bold text-purple-700 text-lg">
                        Rp {{ number_format($procurement->total_harga, 0, ',', '.') }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Stok Saat Ini</dt>
                    <dd class="font-semibold">
                        {{ number_format($procurement->item->stok) }}
                        {{ $procurement->item->satuan }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Info Lainnya --}}
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">Informasi Lainnya</h3>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500 text-xs mb-1">Supplier</dt>
                    <dd class="font-semibold">
                        {{ $procurement->supplier->nama_supplier }}
                    </dd>
                    <dd class="text-xs text-gray-400">
                        {{ $procurement->supplier->telepon ?? '' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tanggal Pengadaan</dt>
                    <dd class="font-medium">
                        {{ $procurement->tanggal->format('d/m/Y') }}
                    </dd>
                </div>
                @if($procurement->tanggal_dibutuhkan)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tanggal Dibutuhkan</dt>
                        <dd class="font-medium">
                            {{ $procurement->tanggal_dibutuhkan->format('d/m/Y') }}
                        </dd>
                    </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-gray-500">Diajukan Oleh</dt>
                    <dd class="font-medium">{{ $procurement->user->name }}</dd>
                </div>
                @if($procurement->approvedBy)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Disetujui Oleh</dt>
                        <dd class="font-medium">{{ $procurement->approvedBy->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tgl Persetujuan</dt>
                        <dd class="font-medium">
                            {{ $procurement->approved_at?->format('d/m/Y H:i') }}
                        </dd>
                    </div>
                @endif
                @if($procurement->catatan)
                    <div class="pt-3 border-t">
                        <dt class="text-gray-500 text-xs mb-1">Catatan</dt>
                        <dd class="bg-gray-50 rounded-lg p-3 text-sm">
                            {{ $procurement->catatan }}
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    {{-- Penerimaan Barang (jika sudah disetujui) --}}
    @if($procurement->status === 'approved' && !$procurement->stockIn)
        <div class="card border-green-200 bg-green-50">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="font-semibold text-green-800">
                        ✅ Pengadaan Telah Disetujui
                    </p>
                    <p class="text-sm text-green-700 mt-1">
                        Catat penerimaan barang ketika barang dari supplier sudah datang.
                    </p>
                </div>
                <a href="{{ route('admin.stock-ins.create') }}?procurement_id={{ $procurement->id }}"
                   class="btn-success text-sm flex-shrink-0">
                    Terima Barang
                </a>
            </div>
        </div>
    @endif

    {{-- Info jika sudah diterima --}}
    @if($procurement->stockIn)
        <div class="card border-blue-200 bg-blue-50">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="font-semibold text-blue-800">
                        📦 Barang Sudah Diterima
                    </p>
                    <p class="text-sm text-blue-700 mt-1">
                        No. Dokumen:
                        <span class="font-mono font-semibold">
                            {{ $procurement->stockIn->no_dokumen }}
                        </span>
                        — {{ $procurement->stockIn->tanggal->format('d/m/Y') }}
                    </p>
                </div>
                <a href="{{ route('admin.stock-ins.show', $procurement->stockIn) }}"
                   class="btn-secondary text-sm flex-shrink-0">
                    Lihat Detail
                </a>
            </div>
        </div>
    @endif

    {{-- Aksi --}}
    <div class="flex gap-3">
        <a href="{{ route('admin.procurements.index') }}" class="btn-secondary">
            ← Kembali
        </a>
        <a href="{{ route('admin.items.show', $procurement->item) }}"
           class="btn-secondary">
            Lihat Barang
        </a>
    </div>
</div>
@endsection