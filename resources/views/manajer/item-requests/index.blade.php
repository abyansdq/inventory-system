@extends('layouts.app')
@section('title', 'Permintaan Barang')
@section('page-title', 'Permintaan Barang')

@section('content')
<div class="space-y-4">

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Menunggu</p>
        </div>
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Disetujui</p>
        </div>
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['processed'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Diproses</p>
        </div>
        <div class="card py-3 text-center">
            <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Ditolak</p>
        </div>
    </div>

    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="No. permintaan, user, atau barang..."
                   class="form-input flex-1 min-w-48">
            <select name="status" class="form-select w-40">
                <option value="">Semua Status</option>
                <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Menunggu</option>
                <option value="approved"  {{ request('status') === 'approved'  ? 'selected' : '' }}>Disetujui</option>
                <option value="processed" {{ request('status') === 'processed' ? 'selected' : '' }}>Diproses</option>
                <option value="rejected"  {{ request('status') === 'rejected'  ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('manajer.item-requests.index') }}"
                   class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Permintaan</th>
                        <th>Pemohon</th>
                        <th>Barang</th>
                        <th class="text-center">Qty</th>
                        <th>Tanggal</th>
                        <th class="text-center">Status</th>
                        <th class="text-center w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td class="font-mono text-xs">
                                {{ $req->no_permintaan }}
                            </td>
                            <td>
                                <p class="font-medium text-sm">{{ $req->user->name }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $req->user->role_label }}
                                </p>
                            </td>
                            <td class="font-medium text-sm">
                                {{ $req->item->nama_barang }}
                            </td>
                            <td class="text-center font-semibold">
                                {{ number_format($req->qty) }}
                                <span class="text-xs text-gray-400">
                                    {{ $req->item->satuan }}
                                </span>
                            </td>
                            <td class="text-sm">
                                {{ $req->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $req->status_color }}">
                                    {{ $req->status_label }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('manajer.item-requests.show', $req) }}"
                                   class="p-1.5 text-blue-600 hover:bg-blue-50
                                          rounded-lg inline-block">
                                    <svg class="w-4 h-4" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400">
                                Belum ada permintaan barang
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="px-4 py-3 border-t">{{ $requests->links() }}</div>
        @endif
    </div>
</div>
@endsection