<?php

namespace App\Http\Controllers\VenueOwner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\TimeSlot;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan ringkasan dashboard operasional pengelola arena / venue owner.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Ambil semua venue milik pengelola arena ini
        $venues = Venue::query()
            ->where('owner_id', $user->id)
            ->with(['courts.sport'])
            ->get();

        $venueIds = $venues->pluck('id');
        $courtIds = Court::query()->whereIn('venue_id', $venueIds)->pluck('id');

        $today = Carbon::today();

        // 1. Statistik Ringkasan Operasional
        $stats = [
            'total_venues' => $venues->count(),
            'approved_venues' => $venues->where('validation_status', 'APPROVED')->count(),
            'pending_venues' => $venues->where('validation_status', 'PENDING')->count(),
            'total_courts' => Court::query()->whereIn('venue_id', $venueIds)->count(),
            'active_courts' => Court::query()->whereIn('venue_id', $venueIds)->where('is_active', true)->count(),
            'today_slots_total' => TimeSlot::query()
                ->whereIn('court_id', $courtIds)
                ->whereDate('start_at', $today)
                ->count(),
            'today_slots_available' => TimeSlot::query()
                ->whereIn('court_id', $courtIds)
                ->whereDate('start_at', $today)
                ->where('status', 'AVAILABLE')
                ->count(),
            'today_slots_booked' => TimeSlot::query()
                ->whereIn('court_id', $courtIds)
                ->whereDate('start_at', $today)
                ->where('status', 'BOOKED')
                ->count(),
            'total_revenue' => (float) Booking::query()
                ->whereHas('slot', function ($q) use ($courtIds) {
                    $q->whereIn('court_id', $courtIds);
                })
                ->whereIn('status', ['PAID', 'COMPLETED'])
                ->sum('total_price'),
        ];

        // 2. Daftar Reservasi Masuk Hari Ini (Prioritas Operasional Lapangan)
        $todayBookings = Booking::query()
            ->whereHas('slot', function ($q) use ($courtIds, $today) {
                $q->whereIn('court_id', $courtIds)
                  ->whereDate('start_at', $today);
            })
            ->with(['user', 'slot.court.venue'])
            ->latest('created_at')
            ->take(10)
            ->get();

        // 3. Slot Hari Ini untuk Pemantauan Cepat Status Real-Time
        $todaySlots = TimeSlot::query()
            ->whereIn('court_id', $courtIds)
            ->whereDate('start_at', $today)
            ->with(['court.venue', 'court.sport'])
            ->orderBy('start_at')
            ->take(8)
            ->get();

        return view('venueowner.dashboard', [
            'user' => $user,
            'venues' => $venues,
            'stats' => $stats,
            'today_bookings' => $todayBookings,
            'today_slots' => $todaySlots,
        ]);
    }
}

