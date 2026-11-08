<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\OpenRoom;
use App\Models\Report;
use App\Models\Sport;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Pantau Statistik & Log Aktivitas (serta Open Room Aktif sesuai Activity Diagram).
     */
    public function index(Request $request): View
    {
        $query = ActivityLog::query()->with('user');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('detail', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('action_filter')) {
            $query->where('action', $request->input('action_filter'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->input('user_id'));
        }

        $logs = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $availableActions = ActivityLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $activeRooms = OpenRoom::query()
            ->with(['booking.user', 'booking.slot.court.venue', 'booking.slot.court.sport', 'skillTarget', 'members'])
            ->whereIn('status', ['OPEN', 'FULL'])
            ->latest('created_at')
            ->take(10)
            ->get();

        $platformStats = [
            'users_total' => User::count(),
            'users_player' => User::where('role', 'PLAYER')->count(),
            'users_owner' => User::where('role', 'VENUE_OWNER')->count(),
            'venues_approved' => Venue::where('validation_status', 'APPROVED')->count(),
            'venues_pending' => Venue::where('validation_status', 'PENDING')->count(),
            'sports_active' => Sport::where('is_active', true)->count(),
            'bookings_total' => Booking::count(),
            'open_rooms_active' => OpenRoom::whereIn('status', ['OPEN', 'FULL'])->count(),
            'reports_open' => Report::whereIn('status', ['OPEN', 'IN_REVIEW'])->count(),
            'logs_total' => ActivityLog::count(),
        ];

        return view('superadmin.logs.index', [
            'logs' => $logs,
            'availableActions' => $availableActions,
            'activeRooms' => $activeRooms,
            'platformStats' => $platformStats,
        ]);
    }
}

