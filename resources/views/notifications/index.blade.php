@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')
<div class="card">
    <div class="flex items-center justify-between mb-6">
        <h3 class="font-semibold text-gray-800">Semua Notifikasi</h3>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf @method('PATCH')
                <button class="btn-secondary text-sm">Tandai Semua Dibaca</button>
            </form>
        @endif
    </div>

    @if($notifications->isEmpty())
        <div class="text-center py-12">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-gray-400">Tidak ada notifikasi</p>
        </div>
    @else
        <div class="divide-y">
            @foreach($notifications as $notification)
                <div class="py-4 flex gap-4 {{ $notification->read_at ? 'opacity-60' : '' }}">
                    <div class="flex-shrink-0 mt-1">
                        @php $type = $notification->data['type'] ?? 'info'; @endphp
                        <span class="w-3 h-3 rounded-full block
                            {{ $type === 'low_stock' ? 'bg-red-500' :
                               ($type === 'procurement' ? 'bg-blue-500' : 'bg-yellow-500') }}">
                        </span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">
                            {{ $notification->data['title'] ?? 'Notifikasi' }}
                        </p>
                        <p class="text-sm text-gray-500 mt-0.5">
                            {{ $notification->data['message'] ?? '' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                    @if(!$notification->read_at)
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="text-xs text-blue-600 hover:underline flex-shrink-0">Tandai dibaca</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection