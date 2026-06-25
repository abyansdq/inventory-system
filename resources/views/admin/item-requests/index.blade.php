@extends('layouts.app')
@section('title', 'Permintaan Barang')
@section('page-title', 'Permintaan Barang')

@section('content')
<div class="space-y-4">

    {{-- Stats --}}
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

    {{-- Filter --}}
    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="No. permintaan, nama user, atau barang..."
                   class="form-input flex-1 min-w-48">
            <select name="status" class="form-select w-44">
                <option value="">Semua Status</option>
                <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Menunggu</option>
                <option value="approved"  {{ request('status') === 'approved'  ? 'selected' : '' }}>Disetujui</option>
                <option value="processed" {{ request('status') === 'processed' ? 'selected' : '' }}>Diproses</option>
                <option value="rejected"  {{ request('status') === 'rejected'  ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('admin.item-requests.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
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
                        <th>Dibutuhkan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td>
                                <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">
                                    {{ $req->no_permintaan }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <p class="font-medium text-sm">{{ $req->user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $req->user->role_label }}</p>
                                </div>
                            </td>
                            <td class="font-medium">{{ $req->item->nama_barang }}</td>
                            <td class="text-center font-semibold">
                                {{ number_format($req->qty) }}
                                <span class="text-xs text-gray-400">{{ $req->item->satuan }}</span>
                            </td>
                            <td class="text-sm">{{ $req->tanggal->format('d/m/Y') }}</td>
                            <td class="text-sm">
                                {{ $req->tanggal_butuh ? $req->tanggal_butuh->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $req->status_color }}">
                                    {{ $req->status_label }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.item-requests.show', $req) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if($req->status === 'pending')
                                        <form action="{{ route('admin.item-requests.approve', $req) }}"
                                              method="POST" x-data
                                              @submit.prevent="if(confirm('Setujui permintaan ini?')) $el.submit()">
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
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400">
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