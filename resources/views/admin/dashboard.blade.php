@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Barang --}}
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Barang</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $total_barang }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Supplier --}}
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Supplier</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $total_supplier }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Barang Masuk Hari Ini --}}
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Masuk Hari Ini</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $barang_masuk_hari_ini }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 16V4m0 0L3 8m4-4l4 4"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Stok Menipis --}}
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Stok Menipis</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $stok_menipis }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 2: Tabel Stok Menipis + Permintaan Terbaru --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Barang Stok Menipis --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Barang Stok Menipis</h3>
                <a href="{{ route('admin.monitoring.index') }}" class="text-sm text-blue-600 hover:underline">
                    Lihat semua
                </a>
            </div>
            @if($items_low_stock->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">Semua stok aman ✅</p>
            @else
                <div class="space-y-3">
                    @foreach($items_low_stock as $item)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $item->nama_barang }}</p>
                                <p class="text-xs text-gray-500">Min: {{ $item->stok_minimum }} {{ $item->satuan }}</p>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-{{ $item->status_stok_color }}">
                                    {{ $item->stok }} {{ $item->satuan }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Permintaan Terbaru --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Permintaan Terbaru</h3>
                <a href="{{ route('admin.item-requests.index') }}" class="text-sm text-blue-600 hover:underline">
                    Lihat semua
                </a>
            </div>
            @if($latest_requests->isEmpty())
                <p class="text-sm text-gray-400 text-center py-6">Belum ada permintaan</p>
            @else
                <div class="space-y-3">
                    @foreach($latest_requests as $req)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $req->item->nama_barang }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $req->user->name }} • {{ $req->tanggal->format('d/m/Y') }}
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

</div>
@endsection