@extends('layouts.app')
@section('title', 'Detail Permintaan')
@section('page-title', 'Detail Permintaan Barang')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- Header Status --}}
    <div class="card">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs text-gray-400 font-mono">{{ $itemRequest->no_permintaan }}</p>
                <h2 class="text-xl font-bold text-gray-800 mt-1">
                    {{ $itemRequest->item->nama_barang }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Diminta oleh <strong>{{ $itemRequest->user->name }}</strong>
                    pada {{ $itemRequest->tanggal->format('d F Y') }}
                </p>
            </div>
            <span class="badge badge-{{ $itemRequest->status_color }} text-sm px-3 py-1">
                {{ $itemRequest->status_label }}
            </span>
        </div>
    </div>

    {{-- Detail --}}
    <div class="card">
        <h3 class="font-semibold text-gray-800 mb-4">Detail Permintaan</h3>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Barang</dt>
                <dd class="font-medium mt-1">{{ $itemRequest->item->nama_barang }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Jumlah</dt>
                <dd class="font-medium mt-1">
                    {{ number_format($itemRequest->qty) }} {{ $itemRequest->item->satuan }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">Stok Tersedia</dt>
                <dd class="font-medium mt-1 {{ $itemRequest->item->stok < $itemRequest->qty ? 'text-red-600' : 'text-green-600' }}">
                    {{ number_format($itemRequest->item->stok) }} {{ $itemRequest->item->satuan }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">Dibutuhkan Tanggal</dt>
                <dd class="font-medium mt-1">
                    {{ $itemRequest->tanggal_butuh ? $itemRequest->tanggal_butuh->format('d F Y') : '-' }}
                </dd>
            </div>
            <div class="col-span-2">
                <dt class="text-gray-500">Keperluan</dt>
                <dd class="font-medium mt-1">{{ $itemRequest->keperluan ?? '-' }}</dd>
            </div>
            @if($itemRequest->catatan_admin)
            <div class="col-span-2">
                <dt class="text-gray-500">Catatan Admin</dt>
                <dd class="font-medium mt-1 text-red-600">{{ $itemRequest->catatan_admin }}</dd>
            </div>
            @endif
            @if($itemRequest->approvedBy)
            <div>
                <dt class="text-gray-500">Diproses Oleh</dt>
                <dd class="font-medium mt-1">{{ $itemRequest->approvedBy->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Tanggal Proses</dt>
                <dd class="font-medium mt-1">{{ $itemRequest->approved_at?->format('d/m/Y H:i') ?? '-' }}</dd>
            </div>
            @endif
        </dl>
    </div>

    {{-- Action Buttons (hanya jika pending) --}}
    @if($itemRequest->status === 'pending')
    <div class="card space-y-4">
        <h3 class="font-semibold text-gray-800">Tindakan</h3>

        {{-- Approve --}}
        <form action="{{ route('admin.item-requests.approve', $itemRequest) }}"
              method="POST"
              x-data
              @submit.prevent="if(confirm('Setujui dan proses barang keluar?')) $el.submit()">
            @csrf @method('PATCH')
            <div class="mb-3">
                <label class="form-label">Catatan (Opsional)</label>
                <textarea name="catatan_admin" rows="2" class="form-input"
                          placeholder="Catatan untuk pemohon"></textarea>
            </div>
            <button type="submit" class="btn-success w-full">
                ✅ Setujui & Proses Barang Keluar
            </button>
        </form>

        <div class="border-t pt-4">
            {{-- Reject --}}
            <form action="{{ route('admin.item-requests.reject', $itemRequest) }}"
                  method="POST" x-data="{ show: false }">
                @csrf @method('PATCH')
                <button type="button" @click="show = !show"
                        class="btn-danger w-full mb-3">
                    ❌ Tolak Permintaan
                </button>
                <div x-show="show" x-transition>
                    <label class="form-label">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="catatan_admin" rows="2"
                              class="form-input @error('catatan_admin') border-red-400 @enderror"
                              placeholder="Wajib diisi..."></textarea>
                    @error('catatan_admin') <p class="form-error">{{ $message }}</p> @enderror
                    <button type="submit" class="btn-danger mt-2">Konfirmasi Tolak</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <a href="{{ route('admin.item-requests.index') }}" class="btn-secondary">← Kembali</a>
</div>
@endsection