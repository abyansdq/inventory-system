@extends('layouts.app')
@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')
<div class="space-y-4">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm text-gray-500">Total {{ $users->total() }} user</p>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah User
        </a>
    </div>

    {{-- Filter --}}
    <div class="card py-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama atau email..."
                   class="form-input flex-1 min-w-48">
            <select name="role" class="form-select w-36">
                <option value="">Semua Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}"
                            {{ request('role') === $role->name ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
            <select name="status" class="form-select w-36">
                <option value="">Semua Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search','role','status']))
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="card p-0 overflow-hidden">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-12">#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th class="text-center">Role</th>
                        <th class="text-center">Status</th>
                        <th>Bergabung</th>
                        <th class="text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                        <tr>
                            <td class="text-gray-400">
                                {{ $users->firstItem() + $i }}
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->foto_url }}"
                                         alt="{{ $user->name }}"
                                         class="w-8 h-8 rounded-full object-cover border flex-shrink-0">
                                    <span class="font-medium text-gray-800">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span class="badge badge-blue text-xs ml-1">Anda</span>
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td class="text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="text-sm text-gray-500">
                                {{ $user->phone ?? '-' }}
                            </td>
                            <td class="text-center">
                                @php
                                    $roleColors = [
                                        'admin'   => 'blue',
                                        'manajer' => 'purple',
                                        'user'    => 'gray',
                                    ];
                                    $roleName = $user->getRoleNames()->first() ?? '-';
                                @endphp
                                <span class="badge badge-{{ $roleColors[$roleName] ?? 'gray' }}">
                                    {{ $user->role_label }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.users.toggle-active', $user) }}"
                                      method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1
                                                   rounded-full text-xs font-medium transition
                                                   {{ $user->is_active
                                                        ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                            {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                        <span class="w-1.5 h-1.5 rounded-full
                                            {{ $user->is_active ? 'bg-green-500' : 'bg-gray-400' }}">
                                        </span>
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-sm text-gray-500">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"
                                       title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}"
                                              method="POST" x-data
                                              @submit.prevent="if(confirm('Hapus user {{ $user->name }}?')) $el.submit()">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"
                                                    title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
                                Belum ada user
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-4 py-3 border-t">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection