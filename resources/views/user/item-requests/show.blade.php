@extends('layouts.app')
@section('title', 'Detail Permintaan')
@section('page-title', 'Detail Permintaan Barang')

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('user.item-requests.index') }}" class="hover:text-blue-600">
            Permintaan Saya
        </a>
        <span>→</span>
        <span class="text-gray-800">{{ $itemRequest->no_permintaan }}</span>
    </div>

    {{-- Status Card --}}
    <div class="card">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-mono text-gray-400">
                    {{ $itemRequest->no_permintaan }}
                </p>
                <h2 class="text-xl font-bold text-gray-800 mt-1">
                    {{ $itemRequest->item->nama_barang }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Diajukan pada {{ $itemRequest->tanggal->format('d F Y') }}
                </p>
            </div>
            <span class="badge badge-{{ $itemRequest->status_color }} text-sm px-3 py-1 flex-shrink-0">
                {{ $itemRequest->status_label }}
            </span>
        </div>

        {{-- Progress Tracker --}}
        <div class="mt-6">
            @php
                $steps = [
                    ['key' => 'pending',   'label' => 'Diajukan',  'icon' => '📋'],
                    ['key' => 'approved',  'label' => 'Disetujui', 'icon' => '✅'],
                    ['key' => 'processed', 'label' => 'Diproses',  'icon' => '📦'],
                ];

                $statusOrder = ['pending' => 0, 'approved' => 1, 'processed' => 2];
                $currentIdx  = $statusOrder[$itemRequest->status] ?? -1;

                // Status khusus
                $isRejected  = $itemRequest->status === 'rejected';
                $isCancelled = $itemRequest->status === 'cancelled';
            @endphp

            @if($isRejected)
                <div class="flex items-center gap-3 p-3 bg-red-50
                            border border-red-200 rounded-xl">
                    <span class="text-2xl">❌</span>
                    <div>
                        <p class="font-semibold text-red-700 text-sm">Permintaan Ditolak</p>
                        @if($itemRequest->catatan_admin)
                            <p class="text-xs text-red-600 mt-0.5">
                                Alasan: {{ $itemRequest->catatan_admin }}
                            </p>
                        @endif
                    </div>
                </div>
            @elseif($isCancelled)
                <div class="flex items-center gap-3 p-3 bg-gray-50
                            border border-gray-200 rounded-xl">
                    <span class="text-2xl">🚫</span>
                    <p class="font-semibold text-gray-600 text-sm">Permintaan Dibatalkan</p>
                </div>
            @else
                <div class="flex items-center gap-0">
                    @foreach($steps as $idx => $step)
                        @php $isDone = $currentIdx >= $idx; @endphp

                        {{-- Step Circle --}}
                        <div class="flex flex-col items-center">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center
                                        text-sm font-bold shadow-sm
                                        {{ $isDone
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-gray-100 text-gray-400 border-2 border-gray-200' }}">
                                {{ $isDone ? '✓' : ($idx + 1) }}
                            </div>
                            <p class="text-xs mt-1 font-medium
                                      {{ $isDone ? 'text-blue-600' : 'text-gray-400' }}">
                                {{ $step['label'] }}
                            </p>
                        </div>

                        {{-- Connector Line --}}
                        @if(!$loop->last)
                            <div class="flex-1 h-0.5 mb-5
                                        {{ $currentIdx > $idx ? 'bg-blue-500' : 'bg-gray-200' }}">
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Detail Permintaan --}}
    <div class="card">
        <h3 class="font-semibold text-gray-800 mb-4">Detail Permintaan</h3>
        <dl class="space-y-4 text-sm">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-gray-500 mb-1">Barang</dt>
                    <dd class="font-semibold text-gray-800">
                        {{ $itemRequest->item->nama_barang }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500 mb-1">Kategori</dt>
                    <dd class="font-medium">
                        {{ $itemRequest->item->category->nama_kategori ?? '-' }}
                    </dd>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-gray-500 mb-1">Jumlah Diminta</dt>
                    <dd class="font-semibold text-blue-700 text-lg">
                        {{ number_format($itemRequest->qty) }}
                        <span class="text-sm font-normal text-gray-500">
                            {{ $itemRequest->item->satuan }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500 mb-1">Stok Tersedia</dt>
                    <dd class="font-semibold
                        {{ $itemRequest->item->stok >= $itemRequest->qty
                            ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format($itemRequest->item->stok) }}
                        <span class="text-sm font-normal text-gray-500">
                            {{ $itemRequest->item->satuan }}
                        </span>
                    </dd>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-gray-500 mb-1">Tanggal Pengajuan</dt>
                    <dd class="font-medium">
                        {{ $itemRequest->tanggal->format('d F Y') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500 mb-1">Dibutuhkan Tanggal</dt>
                    <dd class="font-medium">
                        {{ $itemRequest->tanggal_butuh
                            ? $itemRequest->tanggal_butuh->format('d F Y')
                            : '—' }}
                    </dd>
                </div>
            </div>

            <div>
                <dt class="text-gray-500 mb-1">Keperluan</dt>
                <dd class="font-medium bg-gray-50 rounded-lg p-3">
                    {{ $itemRequest->keperluan ?? '—' }}
                </dd>
            </div>

            {{-- Info Admin --}}
            @if($itemRequest->approvedBy)
                <div class="pt-4 border-t space-y-3">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-gray-500 mb-1">Diproses Oleh</dt>
                            <dd class="font-medium">
                                {{ $itemRequest->approvedBy->name }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 mb-1">Tanggal Proses</dt>
                            <dd class="font-medium">
                                {{ $itemRequest->approved_at?->format('d F Y, H:i') }}
                            </dd>
                        </div>
                    </div>

                    @if($itemRequest->catatan_admin)
                        <div>
                            <dt class="text-gray-500 mb-1">Catatan Admin</dt>
                            <dd class="p-3 rounded-lg text-sm font-medium
                                {{ $itemRequest->status === 'rejected'
                                    ? 'bg-red-50 text-red-700 border border-red-200'
                                    : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                {{ $itemRequest->catatan_admin }}
                            </dd>
                        </div>
                    @endif
                </div>
            @endif
        </dl>
    </div>

    {{-- Aksi --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('user.item-requests.index') }}" class="btn-secondary">
            ← Kembali
        </a>

        @if($itemRequest->status === 'pending')
            <form action="{{ route('user.item-requests.cancel', $itemRequest) }}"
                  method="POST" x-data
                  @submit.prevent="if(confirm('Batalkan permintaan ini?')) $el.submit()">
                @csrf @method('PATCH')
                <button type="submit" class="btn-danger">
                    Batalkan Permintaan
                </button>
            </form>
        @endif

        {{-- Buat permintaan baru untuk item yang sama --}}
        @if(in_array($itemRequest->status, ['rejected','cancelled']))
            <a href="{{ route('user.item-requests.create') }}?item_id={{ $itemRequest->item_id }}"
               class="btn-primary">
                Ajukan Ulang
            </a>
        @endif
    </div>
</div>
@endsection