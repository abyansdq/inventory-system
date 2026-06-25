@extends('layouts.app')
@section('title', 'Permintaan Saya')
@section('page-title', 'Permintaan Barang Saya')

@section('content')
<div class="space-y-4">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm text-gray-500">Total {{ $requests->total() }} permintaan</p>
        <a href="{{ route('user.item-requests.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Permintaan Baru
        </a>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Permintaan</th>
                        <th>Barang</th>
                        <th class="text-center">Qty</th>
                        <th>Tanggal</th>
                        <th>Dibutuhkan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td class="font-mono text-xs">{{ $req->no_permintaan }}</td>
                            <td class="font-medium">{{ $req->item->nama_barang }}</td>
                            <td class="text-center">{{ number_format($req->qty) }}</td>
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
                                    <a href="{{ route('user.item-requests.show', $req) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if($req->status === 'pending')
                                        <form action="{{ route('user.item-requests.cancel', $req) }}"
                                              method="POST" x-data
                                              @submit.prevent="if(confirm('Batalkan permintaan ini?')) $el.submit()">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"
                                                    title="Batalkan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
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