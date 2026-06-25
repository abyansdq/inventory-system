<nav class="bg-white shadow-sm border-b px-6 py-3 flex items-center justify-between">

    {{-- Page Title --}}
    <div>
        <h1 class="text-lg font-semibold text-gray-800">
            @yield('page-title', 'Dashboard')
        </h1>
        @if(View::hasSection('breadcrumb'))
            <div class="text-sm text-gray-500 mt-0.5">
                @yield('breadcrumb')
            </div>
        @endif
    </div>

    {{-- Right Side --}}
    <div class="flex items-center gap-4">

        {{-- Notifikasi --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                    class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                @if($unreadCount > 0)
                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                    </span>
                @endif
            </button>

            {{-- Dropdown Notifikasi --}}
            <div x-show="open"
                 @click.away="open = false"
                 x-transition
                 class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border z-50">
                <div class="px-4 py-3 border-b flex items-center justify-between">
                    <span class="font-semibold text-gray-800">Notifikasi</span>
                    @if($unreadCount > 0)
                        <form action="{{ route('notifications.read-all') }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs text-blue-600 hover:underline">
                                Tandai semua dibaca
                            </button>
                        </form>
                    @endif
                </div>
                <div class="max-h-72 overflow-y-auto divide-y">
                    @forelse(auth()->user()->notifications->take(5) as $notification)
                        <a href="{{ route('notifications.read', $notification->id) }}"
                           class="block px-4 py-3 hover:bg-gray-50 transition {{ $notification->read_at ? 'opacity-60' : '' }}">
                            <div class="flex gap-3">
                                <div class="mt-0.5 flex-shrink-0">
                                    @if($notification->data['type'] === 'low_stock')
                                        <span class="w-2 h-2 rounded-full bg-red-500 block mt-1.5"></span>
                                    @elseif($notification->data['type'] === 'procurement')
                                        <span class="w-2 h-2 rounded-full bg-blue-500 block mt-1.5"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-yellow-500 block mt-1.5"></span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ $notification->data['title'] ?? 'Notifikasi' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $notification->data['message'] ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-6 text-center text-sm text-gray-400">
                            Tidak ada notifikasi
                        </div>
                    @endforelse
                </div>
                <div class="px-4 py-2 border-t">
                    <a href="{{ route('notifications.index') }}"
                       class="text-xs text-blue-600 hover:underline block text-center">
                        Lihat semua notifikasi
                    </a>
                </div>
            </div>
        </div>

        {{-- User Dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                    class="flex items-center gap-2 p-1.5 hover:bg-gray-100 rounded-lg transition">
                <img src="{{ auth()->user()->foto_url }}"
                     alt="{{ auth()->user()->name }}"
                     class="w-8 h-8 rounded-full object-cover border">
                <div class="text-left hidden sm:block">
                    <p class="text-sm font-medium text-gray-800 leading-tight">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ auth()->user()->role_label }}
                    </p>
                </div>
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open"
                 @click.away="open = false"
                 x-transition
                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border z-50">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-t-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profil Saya
                </a>
                <div class="border-t"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-b-xl">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>