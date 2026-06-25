@extends('layouts.app')
@section('title', 'Detail User')
@section('page-title', 'Detail User')

@section('content')
<div class="max-w-4xl space-y-6">

    {{-- Profile Card --}}
    <div class="card">
        <div class="flex flex-col sm:flex-row gap-6 items-start">
            <img src="{{ $user->foto_url }}" alt="{{ $user->name }}"
                 class="w-24 h-24 rounded-2xl object-cover border-4 border-gray-100 flex-shrink-0">
            <div class="flex-1">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $user->name }}</h2>
                        <p class="text-gray-500 text-sm mt-1">{{ $user->email }}</p>
                        <p class="text-gray-400 text-sm">{{ $user->phone ?? 'Tidak ada telepon' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="badge badge-{{ $user->is_active ? 'green' : 'gray' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                        @php
                            $roleColors = ['admin' => 'blue', 'manajer' => 'purple', 'user' => 'gray'];
                            $roleName   = $user->getRoleNames()->first() ?? '-';
                        @endphp
                        <span class="badge badge-{{ $roleColors[$roleName] ?? 'gray' }}">
                            {{ $user->role_label }}
                        </span>
                    </div>
                </div>
                <div class="flex gap-2 mt-4">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-secondary text-sm">
                        Edit User
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card text-center py-3">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total_requests'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Permintaan</p>
        </div>
        <div class="card text-center py-3">
            <p class="text-2xl font-bold text-green-600">{{ $stats['total_stock_ins'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Barang Masuk</p>
        </div>
        <div class="card text-center py-3">
            <p class="text-2xl font-bold text-red-600">{{ $stats['total_stock_outs'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Barang Keluar</p>
        </div>
        <div class="card text-center py-3">
            <p class="text-2xl font-bold text-purple-600">{{ $stats['total_procurement'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Pengadaan</p>
        </div>
    </div>

    {{-- Activity Log --}}
    <div class="card">
        <h3 class="font-semibold text-gray-800 mb-4">Riwayat Aktivitas Terbaru</h3>
        @if($activityLogs->isEmpty())
            <p class="text-sm text-gray-400 text-center py-6">Belum ada aktivitas</p>
        @else
            <div class="space-y-3">
                @foreach($activityLogs as $log)
                    <div class="flex gap-4 py-2 border-b border-gray-50 last:border-0">
                        <div class="w-2 h-2 rounded-full bg-blue-400 mt-2 flex-shrink-0"></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-700">{{ $log->description }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $log->created_at->diffForHumans() }}
                                • {{ $log->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        @if($log->properties->isNotEmpty())
                            <details class="text-xs text-gray-400 cursor-pointer">
                                <summary>Perubahan</summary>
                                <div class="mt-1 text-xs bg-gray-50 rounded p-2 max-w-xs">
                                    @if($log->properties->has('old'))
                                        @foreach($log->properties['old'] as $key => $val)
                                            <div class="flex gap-2">
                                                <span class="text-gray-500">{{ $key }}:</span>
                                                <span class="line-through text-red-400">{{ $val }}</span>
                                                →
                                                <span class="text-green-600">{{ $log->properties['attributes'][$key] ?? '' }}</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </details>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <a href="{{ route('admin.users.index') }}" class="btn-secondary">← Kembali</a>
</div>
@endsection