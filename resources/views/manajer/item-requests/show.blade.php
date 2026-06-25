@extends('layouts.app')
@section('title', 'Detail Permintaan')
@section('page-title', 'Detail Permintaan Barang')

@section('content')
<div class="max-w-2xl space-y-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('manajer.item-requests.index') }}" class="hover:text-blue-600">
                Permintaan
            </a>
            <span>→</span>
            <span class="text-gray-800">{{ $itemRequest->no_permintaan }}</span>
        </div>
        <a href="{{ route('manajer.item-requests.index') }}" class="btn-secondary text-sm">
            ← Kembali
        </a>
    </div>

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
                    Diajukan oleh <strong>{{ $itemRequest->user->name }}</strong>
                    — {{ $itemRequest->tanggal->format('d F Y') }}
                </p>
            </div>
            <span class="badge badge-{{ $itemRequest->status_color }} text-sm px-3 py-1">
                {{ $itemRequest->status_label }}
            </span>
        </div>
    </div>

    <div class="card">
        <h3 class="font-semibold text-gray-800 mb-4">Detail Permintaan</h3>
        <dl class="space-y-3 text-sm">
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
                        {{ $itemRequest->item->satuan }}
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
                    <dt class="text-gray-500 mb-1">Dibutuhkan</dt>
                    <dd class="font-medium">
                        {{ $itemRequest->tanggal_butuh
                            ? $itemRequest->tanggal_butuh->format('d F Y')
                            : '—' }}
                    </dd>
                </div>
            </div>
            <div>
                <dt class="text-gray-500 mb-1">Keperluan</dt>
                <dd class="bg-gray-50 rounded-lg p-3">
                    {{ $itemRequest->keperluan ?? '—' }}
                </dd>
            </div>
            @if($itemRequest->catatan_admin)
                <div>
                    <dt class="text-gray-500 mb-1">Catatan Admin</dt>
                    <dd class="bg-blue-50 rounded-lg p-3 text-blue-800">
                        {{ $itemRequest->catatan_admin }}
                    </dd>
                </div>
            @endif
            @if($itemRequest->approvedBy)
                <div class="pt-3 border-t grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-gray-500 mb-1">Diproses Oleh</dt>
                        <dd class="font-medium">{{ $itemRequest->approvedBy->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 mb-1">Tgl Proses</dt>
                        <dd class="font-medium">
                            {{ $itemRequest->approved_at?->format('d/m/Y H:i') }}
                        </dd>
                    </div>
                </div>
            @endif
        </dl>
    </div>

    {{-- Jika stok menipis & pending, sarankan pengadaan --}}
    @if($itemRequest->status === 'pending' && $itemRequest->item->stok < $itemRequest->qty)
        <div class="card bg-orange-50 border-orange-200">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="font-semibold text-orange-800 text-sm">
                        ⚠️ Stok Tidak Mencukupi
                    </p>
                    <p class="text-xs text-orange-700 mt-1">
                        Stok tersedia ({{ $itemRequest->item->stok }})
                        kurang dari yang diminta ({{ $itemRequest->qty }}).
                        Pertimbangkan untuk membuat pengadaan.
                    </p>
                </div>
                <a href="{{ route('manajer.procurements.create') }}?item_id={{ $itemRequest->item_id }}"
                   class="btn-primary text-sm flex-shrink-0">
                    Buat Pengadaan
                </a>
            </div>
        </div>
    @endif
</div>
@endsection