<?php
// app/Http/Controllers/Auth/AuthenticatedSessionController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Cek apakah user aktif
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
            ])->onlyInput('email');
        }

        // Log aktivitas login
        activity()
            ->causedBy($user)
            ->log("User {$user->name} berhasil login.");

        // Redirect berdasarkan role
        return redirect($this->redirectBasedOnRole($user));
    }

    /**
     * Handle logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            activity()
                ->causedBy($user)
                ->log("User {$user->name} logout.");
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda berhasil logout.');
    }

    /**
     * Tentukan redirect URL berdasarkan role.
     */
    private function redirectBasedOnRole($user): string
    {
        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('manajer')) {
            return route('manajer.dashboard');
        }

        if ($user->hasRole('user')) {
            return route('user.dashboard');
        }

        // Fallback
        return route('dashboard');
    }
}