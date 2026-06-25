@extends('layouts.app')
@section('title', 'Pengadaan Barang')
@section('page-title', 'Pengadaan Barang')

@section('content')
<div class="space-y-4">

    {{-- Info: Admin hanya bisa melihat --}}
    <div class="card bg-gray-50 border-gray-200 py-3">
        <div class="flex items-center gap-3">
            <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-gray-600">
                Pengadaan dibuat dan disetujui oleh <strong>Manajer</strong>.
                Admin bertugas mencatat penerimaan barang saat pengadaan berstatus
                <span class="badge badge-green text-xs">Disetujui</span>.
            </p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="No. pengadaan atau nama barang..."
                   class="form-input flex-1 min-w-48">
            <select name="status" class="form-select w-44">
                <option value="">Semua Status</option>
                @foreach([
                    'draft'    => 'Draft',
                    'pending'  => 'Menunggu',
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                    'ordered'  => 'Dipesan',
                    'received' => 'Diterima',
                ] as $val => $label)
                    <option value="{{ $val }}"
                            {{ request('status') === $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <input type="date" name="tanggal_dari"
                   value="{{ request('tanggal_dari') }}" class="form-input w-36">
            <input type="date" name="tanggal_sampai"
                   value="{{ request('tanggal_sampai') }}" class="form-input w-36">
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search','status','tanggal_dari','tanggal_sampai']))
                <a href="{{ route('admin.procurements.index') }}" class="btn-secondary">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Tabel --}}
    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Pengadaan</th>
                        <th>Barang</th>
                        <th>Supplier</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Total</th>
                        <th>Tanggal</th>
                        <th>Diajukan Oleh</th>
                        <th class="text-center">Status</th>
                        <th class="text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($procurements as $proc)
                        <tr>
                            <td class="font-mono text-xs">
                                {{ $proc->no_pengadaan }}
                            </td>
                            <td class="font-medium text-sm">
                                {{ $proc->item->nama_barang }}
                            </td>
                            <td class="text-sm text-gray-500">
                                {{ $proc->supplier->nama_supplier }}
                            </td>
                            <td class="text-center font-semibold">
                                {{ number_format($proc->qty) }}
                                <span class="text-xs text-gray-400">
                                    {{ $proc->item->satuan }}
                                </span>
                            </td>
                            <td class="text-right text-sm">
                                Rp {{ number_format($proc->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="text-sm">
                                {{ $proc->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="text-sm text-gray-500">
                                {{ $proc->user->name }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $proc->status_color }}">
                                    {{ $proc->status_label }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.procurements.show', $proc) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50
                                              rounded-lg" title="Detail">
                                        <svg class="w-4 h-4" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    {{-- Tombol terima barang (jika approved) --}}
                                    @if($proc->status === 'approved' && !$proc->stockIn)
                                        <a href="{{ route('admin.stock-ins.create') }}?procurement_id={{ $proc->id }}"
                                           class="p-1.5 text-green-600 hover:bg-green-50
                                                  rounded-lg" title="Terima Barang">
                                            <svg class="w-4 h-4" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-gray-400">
                                Belum ada data pengadaan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($procurements->hasPages())
            <div class="px-4 py-3 border-t">{{ $procurements->links() }}</div>
        @endif
    </div>
</div>
@endsection