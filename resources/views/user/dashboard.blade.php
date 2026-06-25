@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card">
            <p class="text-sm text-gray-500">Total Permintaan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $total_permintaan }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Menunggu Approval</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $permintaan_pending }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Disetujui</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $permintaan_approved }}</p>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">Permintaan Saya</h3>
            <a href="{{ route('user.item-requests.create') }}" class="btn-primary text-xs">
                + Buat Permintaan
            </a>
        </div>
        @if($latest_requests->isEmpty())
            <p class="text-sm text-gray-400 text-center py-6">Belum ada permintaan barang</p>
        @else
            <div class="space-y-3">
                @foreach($latest_requests as $req)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $req->item->nama_barang }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $req->no_permintaan }} • {{ $req->tanggal->format('d/m/Y') }}
                            </p>
                        </div>
                        <span class="badge badge-{{ $req->status_color }}">
                            {{ $req->status_label }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection