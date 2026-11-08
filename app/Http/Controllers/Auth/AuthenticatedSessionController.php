<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:150'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_active' => true,
        ])) {
            return back()
                ->withErrors(['email' => 'Email atau password tidak sesuai (atau akun sedang di-suspend).'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = $request->user();

        if ($user && Schema::hasTable('activity_logs')) {
            ActivityLog::recordLog(
                $user->id,
                'AUTH_LOGIN',
                "Pengguna {$user->name} ({$user->role}) berhasil login.",
                $request->ip()
            );
        }

        if ($user && $user->role === 'SUPER_ADMIN') {
            return redirect()->intended(route('superadmin.dashboard'));
        }

        return redirect()->intended(route('home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user && Schema::hasTable('activity_logs')) {
            ActivityLog::recordLog(
                $user->id,
                'AUTH_LOGOUT',
                "Pengguna {$user->name} ({$user->role}) logout dari sistem.",
                $request->ip()
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
