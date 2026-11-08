<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Lihat Daftar User (beserta filter role, status, dan pencarian).
     */
    public function index(Request $request): View
    {
        $query = User::query()->with('venues');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && in_array($request->input('role'), ['SUPER_ADMIN', 'VENUE_OWNER', 'PLAYER'], true)) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->input('status') === 'suspended') {
                $query->where('is_active', false);
            }
        }

        $users = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        $summary = [
            'total' => User::count(),
            'super_admins' => User::where('role', 'SUPER_ADMIN')->count(),
            'venue_owners' => User::where('role', 'VENUE_OWNER')->count(),
            'players' => User::where('role', 'PLAYER')->count(),
            'suspended' => User::where('is_active', false)->count(),
        ];

        return view('superadmin.users.index', [
            'users' => $users,
            'summary' => $summary,
        ]);
    }

    /**
     * Tambah User baru oleh Super Admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['SUPER_ADMIN', 'VENUE_OWNER', 'PLAYER'])],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password_hash' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        ActivityLog::recordLog(
            $request->user()->id,
            'USER_CREATE',
            "Menambahkan user baru: {$user->name} ({$user->email}) dengan role {$user->role}.",
            $request->ip()
        );

        Notification::sendNotification(
            $user->id,
            'SYSTEM',
            'Selamat Datang di SportHive',
            "Akun Anda ({$user->role}) telah dibuat oleh Super Admin.",
            'USER',
            $user->id
        );

        return redirect()
            ->route('superadmin.users.index')
            ->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    /**
     * Update data User oleh Super Admin.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['SUPER_ADMIN', 'VENUE_OWNER', 'PLAYER'])],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        if ($user->id !== $request->user()->id) {
            $user->role = $validated['role'];
        }

        if (! empty($validated['password'])) {
            $user->password_hash = Hash::make($validated['password']);
        }

        $user->save();

        ActivityLog::recordLog(
            $request->user()->id,
            'USER_UPDATE',
            "Memperbarui data user: {$user->name} ({$user->email}) [Role: {$user->role}].",
            $request->ip()
        );

        return redirect()
            ->route('superadmin.users.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Suspend / Aktifkan kembali akun User (sesuai Use Case & Activity Diagram).
     */
    public function toggleSuspend(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()
                ->route('superadmin.users.index')
                ->with('error', 'Anda tidak dapat men-suspend akun Super Admin Anda sendiri.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $actionType = $user->is_active ? 'USER_ACTIVATE' : 'USER_SUSPEND';
        $statusLabel = $user->is_active ? 'diaktifkan kembali' : 'di-suspend (dinonaktifkan)';

        ActivityLog::recordLog(
            $request->user()->id,
            $actionType,
            "Status akun {$user->name} ({$user->email}) telah {$statusLabel}.",
            $request->ip()
        );

        Notification::sendNotification(
            $user->id,
            'SYSTEM',
            $user->is_active ? 'Akun Diaktifkan Kembali' : 'Akun Anda Di-suspend',
            $user->is_active
                ? 'Akun SportHive Anda telah diaktifkan kembali oleh Super Admin.'
                : 'Akun SportHive Anda telah di-suspend oleh Super Admin karena evaluasi kebijakan platform.',
            'USER',
            $user->id
        );

        return redirect()
            ->route('superadmin.users.index')
            ->with('success', "Akun {$user->name} berhasil {$statusLabel}.");
    }

    /**
     * Hapus User.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()
                ->route('superadmin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Super Admin yang sedang digunakan.');
        }

        if ($user->venues()->exists() || $user->bookings()->exists()) {
            return redirect()
                ->route('superadmin.users.index')
                ->with('error', "Pengguna {$user->name} memiliki data Venue/Booking terkait. Gunakan fitur Suspend untuk menonaktifkan akun ini.");
        }

        $name = $user->name;
        $email = $user->email;
        $user->delete();

        ActivityLog::recordLog(
            $request->user()->id,
            'USER_DELETE',
            "Menghapus user: {$name} ({$email}).",
            $request->ip()
        );

        return redirect()
            ->route('superadmin.users.index')
            ->with('success', "Pengguna {$name} berhasil dihapus.");
    }
}

