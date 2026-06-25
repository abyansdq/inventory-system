@extends('layouts.app')

@section('title', 'Dashboard Manajer')
@section('page-title', 'Dashboard Manajer')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card">
            <p class="text-sm text-gray-500">Total Barang</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $total_barang }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Stok Menipis</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stok_menipis }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Pengadaan Pending</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $procurement_pending }}</p>
        </div>
        <div class="card">
            <p class="text-sm text-gray-500">Pengadaan Disetujui</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $procurement_approved }}</p>
        </div>
    </div>

    <div class="card">
        <h3 class="font-semibold text-gray-800 mb-4">Pengadaan Terbaru</h3>
        @if($latest_procurements->isEmpty())
            <p class="text-sm text-gray-400 text-center py-4">Belum ada pengadaan</p>
        @else
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No Pengadaan</th>
                            <th>Barang</th>
                            <th>Qty</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latest_procurements as $proc)
                            <tr>
                                <td class="font-mono text-xs">{{ $proc->no_pengadaan }}</td>
                                <td>{{ $proc->item->nama_barang }}</td>
                                <td>{{ $proc->qty }}</td>
                                <td>
                                    <span class="badge badge-{{ $proc->status_color }}">
                                        {{ $proc->status_label }}
                                    </span>
                                </td>
                                <td>{{ $proc->tanggal->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection