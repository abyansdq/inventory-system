@extends('layouts.app')
@section('title', 'Activity Log')
@section('page-title', 'Activity Log')

@section('content')
<div class="space-y-4">

    {{-- Filter --}}
    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari aktivitas..." class="form-input flex-1 min-w-48">
            <select name="log_name" class="form-select w-40">
                <option value="">Semua Modul</option>
                @foreach($logNames as $name)
                    <option value="{{ $name }}" {{ request('log_name') === $name ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $name)) }}
                    </option>
                @endforeach
            </select>
            <select name="causer_id" class="form-select w-48">
                <option value="">Semua User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('causer_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                   class="form-input w-36">
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                   class="form-input w-36">
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search','log_name','causer_id','tanggal_dari','tanggal_sampai']))
                <a href="{{ route('admin.activity-logs.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-44">Waktu</th>
                        <th class="w-28">Modul</th>
                        <th>Deskripsi</th>
                        <th class="w-36">User</th>
                        <th class="w-24">Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="text-xs text-gray-500">
                                <p>{{ $log->created_at->format('d/m/Y') }}</p>
                                <p>{{ $log->created_at->format('H:i:s') }}</p>
                                <p class="text-gray-300 mt-0.5">
                                    {{ $log->created_at->diffForHumans() }}
                                </p>
                            </td>
                            <td>
                                @php
                                    $logColors = [
                                        'user'         => 'blue',
                                        'item'         => 'green',
                                        'supplier'     => 'purple',
                                        'category'     => 'yellow',
                                        'stock_in'     => 'green',
                                        'stock_out'    => 'red',
                                        'item_request' => 'orange',
                                        'procurement'  => 'indigo',
                                    ];
                                    $logColor = $logColors[$log->log_name] ?? 'gray';
                                @endphp
                                <span class="badge badge-{{ $logColor }} text-xs">
                                    {{ ucfirst(str_replace('_', ' ', $log->log_name ?? 'system')) }}
                                </span>
                            </td>
                            <td class="text-sm text-gray-700">
                                {{ $log->description }}
                            </td>
                            <td>
                                @if($log->causer)
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $log->causer->foto_url }}"
                                             alt="{{ $log->causer->name }}"
                                             class="w-6 h-6 rounded-full object-cover border">
                                        <span class="text-xs text-gray-600 truncate">
                                            {{ $log->causer->name }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-300">System</span>
                                @endif
                            </td>
                            <td>
                                @if($log->properties->isNotEmpty() && $log->properties->has('old'))
                                    <div x-data="{ open: false }">
                                        <button @click="open = !open"
                                                class="text-xs text-blue-600 hover:underline">
                                            Lihat detail
                                        </button>
                                        <div x-show="open" x-transition
                                             class="absolute z-10 mt-1 w-64 bg-white border
                                                    shadow-lg rounded-xl p-3 text-xs">
                                            @foreach($log->properties['old'] as $key => $oldVal)
                                                <div class="mb-1">
                                                    <span class="font-medium text-gray-600">{{ $key }}:</span>
                                                    <span class="line-through text-red-400 ml-1">{{ $oldVal }}</span>
                                                    →
                                                    <span class="text-green-600">
                                                        {{ $log->properties['attributes'][$key] ?? '—' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-gray-400">
                                Belum ada aktivitas tercatat
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-4 py-3 border-t">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
@endsection