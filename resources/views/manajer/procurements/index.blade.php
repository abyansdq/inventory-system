@extends('layouts.app')
@section('title', 'Pengadaan Barang')
@section('page-title', 'Pengadaan Barang')

@section('content')
<div class="space-y-4">

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-gray-600">{{ $stats['draft'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Draft</p>
        </div>
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Menunggu</p>
        </div>
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Disetujui</p>
        </div>
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['received'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Diterima</p>
        </div>
    </div>

    <div class="flex justify-end">
        <a href="{{ route('manajer.procurements.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Pengadaan
        </a>
    </div>

    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="No. pengadaan atau nama barang..."
                   class="form-input flex-1 min-w-48">
            <select name="status" class="form-select w-44">
                <option value="">Semua Status</option>
                <option value="draft"    {{ request('status') === 'draft'    ? 'selected' : '' }}>Draft</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Menunggu</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                <option value="ordered"  {{ request('status') === 'ordered'  ? 'selected' : '' }}>Dipesan</option>
                <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Diterima</option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('manajer.procurements.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

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
                        <th class="text-center">Status</th>
                        <th class="text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($procurements as $proc)
                        <tr>
                            <td class="font-mono text-xs">{{ $proc->no_pengadaan }}</td>
                            <td class="font-medium">{{ $proc->item->nama_barang }}</td>
                            <td class="text-sm text-gray-500">{{ $proc->supplier->nama_supplier }}</td>
                            <td class="text-center font-semibold">{{ number_format($proc->qty) }}</td>
                            <td class="text-right text-sm">
                                Rp {{ number_format($proc->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="text-sm">{{ $proc->tanggal->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $proc->status_color }}">
                                    {{ $proc->status_label }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('manajer.procurements.show', $proc) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if($proc->status === 'pending')
                                        <form action="{{ route('manajer.procurements.approve', $proc) }}"
                                              method="POST" x-data
                                              @submit.prevent="if(confirm('Setujui pengadaan ini?')) $el.submit()">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg"
                                                    title="Setujui">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    @if(in_array($proc->status, ['draft','pending']))
                                        <a href="{{ route('manajer.procurements.edit', $proc) }}"
                                           class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400">
                                Belum ada pengadaan
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