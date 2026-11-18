<?php

namespace App\Http\Controllers\VenueOwner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecapController extends Controller
{
    /**
     * Tampilkan rekapitulasi pendapatan dan jumlah pelanggan per venue dan gabungan.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Ambil semua venue yang dimiliki oleh Venue Owner
        $venues = Venue::query()
            ->where('owner_id', $user->id)
            ->with(['courts.sport'])
            ->get();

        $selectedVenueId = $request->query('venue_id', 'all');
        $dateFilter = $request->query('date_range', 'this_month'); // today, this_week, this_month, this_year, all, custom
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Tentukan batas tanggal
        $now = Carbon::now();
        $dateLabel = 'Bulan Ini';

        if ($dateFilter === 'today') {
            $start = Carbon::today()->startOfDay();
            $end = Carbon::today()->endOfDay();
            $dateLabel = 'Hari Ini (' . $now->translatedFormat('d M Y') . ')';
        } elseif ($dateFilter === 'this_week') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
            $dateLabel = 'Minggu Ini (' . $start->format('d M') . ' - ' . $end->format('d M Y') . ')';
        } elseif ($dateFilter === 'this_year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
            $dateLabel = 'Tahun Ini (' . $now->year . ')';
        } elseif ($dateFilter === 'custom' && $startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $dateLabel = 'Periode ' . $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
        } elseif ($dateFilter === 'all') {
            $start = null;
            $end = null;
            $dateLabel = 'Semua Waktu';
        } else {
            // Default: this_month
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            $dateLabel = 'Bulan Ini (' . $now->translatedFormat('F Y') . ')';
        }

        // Query dasar transaksi yang lunas / selesai
        $baseBookingsQuery = Booking::query()
            ->whereIn('status', ['PAID', 'COMPLETED'])
            ->whereHas('slot.court.venue', function ($q) use ($user, $selectedVenueId) {
                $q->where('owner_id', $user->id);
                if ($selectedVenueId !== 'all') {
                    $q->where('id', (int) $selectedVenueId);
                }
            });

        if ($start && $end) {
            $baseBookingsQuery->whereBetween('created_at', [$start, $end]);
        }

        // 1. Ringkasan Gabungan (Overall / Filtered Total)
        $totalRevenue = (float) (clone $baseBookingsQuery)->sum('total_price');
        $totalBookings = (clone $baseBookingsQuery)->count();
        $uniqueCustomersCount = (clone $baseBookingsQuery)->distinct('user_id')->count('user_id');
        $completedBookings = (clone $baseBookingsQuery)->where('status', 'COMPLETED')->count();

        // 2. Rincian Rekapitulasi per Venue (Per Venue Breakdown)
        $venueBreakdown = [];
        foreach ($venues as $v) {
            $vCourtIds = $v->courts->pluck('id');

            $vQuery = Booking::query()
                ->whereIn('status', ['PAID', 'COMPLETED'])
                ->whereHas('slot', function ($q) use ($vCourtIds) {
                    $q->whereIn('court_id', $vCourtIds);
                });

            if ($start && $end) {
                $vQuery->whereBetween('created_at', [$start, $end]);
            }

            $vRevenue = (float) (clone $vQuery)->sum('total_price');
            $vBookingsCount = (clone $vQuery)->count();
            $vUniqueCustomers = (clone $vQuery)->distinct('user_id')->count('user_id');

            $venueBreakdown[] = [
                'venue' => $v,
                'revenue' => $vRevenue,
                'bookings_count' => $vBookingsCount,
                'unique_customers' => $vUniqueCustomers,
                'active_courts_count' => $v->courts->where('is_active', true)->count(),
                'total_courts_count' => $v->courts->count(),
            ];
        }

        // 3. Rekap Transaksi Terbaru dalam Periode
        $recentTransactions = (clone $baseBookingsQuery)
            ->with(['user', 'slot.court.venue'])
            ->latest('created_at')
            ->take(15)
            ->get();

        return view('venueowner.recap.index', [
            'venues' => $venues,
            'selectedVenueId' => $selectedVenueId,
            'dateFilter' => $dateFilter,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateLabel' => $dateLabel,
            'totalRevenue' => $totalRevenue,
            'totalBookings' => $totalBookings,
            'uniqueCustomersCount' => $uniqueCustomersCount,
            'completedBookings' => $completedBookings,
            'venueBreakdown' => $venueBreakdown,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
