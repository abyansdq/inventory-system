@extends('layouts.app')
@section('title', 'Detail Pengadaan')
@section('page-title', 'Detail Pengadaan')

@section('content')
<div class="max-w-3xl space-y-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('manajer.procurements.index') }}" class="hover:text-blue-600">
                Pengadaan
            </a>
            <span>→</span>
            <span class="text-gray-800">{{ $procurement->no_pengadaan }}</span>
        </div>
        <a href="{{ route('manajer.procurements.index') }}" class="btn-secondary text-sm">
            ← Kembali
        </a>
    </div>

    {{-- Status Header --}}
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
                    Diajukan pada {{ $procurement->tanggal->format('d F Y') }}
                </p>
            </div>
            <span class="badge badge-{{ $procurement->status_color }} text-sm px-3 py-1">
                {{ $procurement->status_label }}
            </span>
        </div>
    </div>

    {{-- Detail --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">Detail Pengadaan</h3>
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
                    <dt class="font-medium">Total Harga</dt>
                    <dd class="font-bold text-purple-700">
                        Rp {{ number_format($procurement->total_harga, 0, ',', '.') }}
                    </dd>
                </div>
            </dl>
        </div>

        <div class="card">
            <h3 class="font-semibold text-gray-800 mb-4">Informasi Lainnya</h3>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500 text-xs mb-1">Supplier</dt>
                    <dd class="font-semibold">
                        {{ $procurement->supplier->nama_supplier }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Tanggal</dt>
                    <dd class="font-medium">
                        {{ $procurement->tanggal->format('d/m/Y') }}
                    </dd>
                </div>
                @if($procurement->tanggal_dibutuhkan)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Dibutuhkan</dt>
                        <dd class="font-medium">
                            {{ $procurement->tanggal_dibutuhkan->format('d/m/Y') }}
                        </dd>
                    </div>
                @endif
                @if($procurement->approvedBy)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Disetujui Oleh</dt>
                        <dd class="font-medium">
                            {{ $procurement->approvedBy->name }}
                        </dd>
                    </div>
                @endif
                @if($procurement->catatan)
                    <div class="pt-3 border-t">
                        <dt class="text-gray-500 text-xs mb-1">Catatan</dt>
                        <dd class="bg-gray-50 rounded-lg p-3">
                            {{ $procurement->catatan }}
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    {{-- Aksi jika masih bisa diubah --}}
    @if(in_array($procurement->status, ['draft', 'pending']))
        <div class="flex gap-3">
            <a href="{{ route('manajer.procurements.edit', $procurement) }}"
               class="btn-secondary">
                Edit Pengadaan
            </a>
            @if($procurement->status === 'draft')
                <form action="{{ route('manajer.procurements.submit', $procurement) }}"
                      method="POST" x-data
                      @submit.prevent="if(confirm('Ajukan pengadaan ini?')) $el.submit()">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-primary">
                        Ajukan ke Persetujuan
                    </button>
                </form>
            @endif
        </div>
    @endif

    {{-- Approve / Reject jika pending --}}
    @if($procurement->status === 'pending')
        <div class="card space-y-4" x-data="{ showReject: false }">
            <h3 class="font-semibold text-gray-800">Tindakan</h3>

            <form action="{{ route('manajer.procurements.approve', $procurement) }}"
                  method="POST" x-data
                  @submit.prevent="if(confirm('Setujui pengadaan ini?')) $el.submit()">
                @csrf @method('PATCH')
                <button type="submit" class="btn-success w-full justify-center">
                    ✅ Setujui Pengadaan
                </button>
            </form>

            <div class="border-t pt-4">
                <button @click="showReject = !showReject"
                        type="button" class="btn-danger w-full">
                    ❌ Tolak Pengadaan
                </button>
                <div x-show="showReject" x-transition class="mt-3">
                    <form action="{{ route('manajer.procurements.reject', $procurement) }}"
                          method="POST" class="space-y-3">
                        @csrf @method('PATCH')
                        <label class="form-label">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="catatan" rows="2"
                                  class="form-input"
                                  placeholder="Wajib diisi..."></textarea>
                        <button type="submit" class="btn-danger">
                            Konfirmasi Tolak
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection