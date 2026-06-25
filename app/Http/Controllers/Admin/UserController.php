<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        $role = $data['role'];
        unset($data['role']);

        $user = User::create($data);
        $user->assignRole($role);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil ditambahkan.");
    }

    public function show(User $user)
    {
        $user->load('roles');

        $activityLogs = \Spatie\Activitylog\Models\Activity::causedBy($user)
            ->latest()
            ->limit(20)
            ->get();

        $stats = [
            'total_requests'    => $user->itemRequests()->count(),
            'total_stock_ins'   => $user->stockIns()->count(),
            'total_stock_outs'  => $user->stockOuts()->count(),
            'total_procurement' => $user->procurements()->count(),
        ];

        return view('admin.users.show', compact('user', 'activityLogs', 'stats'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        // Update password hanya jika diisi
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Handle foto
        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        $role = $data['role'];
        unset($data['role']);

        $user->update($data);
        $user->syncRoles([$role]);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        // Cegah hapus diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        // Cegah hapus user yang masih punya transaksi
        if ($user->stockIns()->exists() || $user->stockOuts()->exists()) {
            return back()->with('error',
                'User tidak dapat dihapus karena masih memiliki riwayat transaksi.'
            );
        }

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    // Toggle aktif/nonaktif
    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User {$user->name} berhasil {$status}.");
    }

    private function uploadPhoto($file): string
    {
        $filename = 'users/' . uniqid() . '.' . $file->getClientOriginalExtension();

        $image = Image::read($file)
            ->cover(200, 200);

        Storage::disk('public')->put($filename, $image->encode());

        return $filename;
    }
}