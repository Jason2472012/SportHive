<?php

namespace App\Http\Controllers\VenueOwner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Tampilkan daftar reservasi masuk untuk kebutuhan pemantauan operasional lapangan.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $venueIds = Venue::query()->where('owner_id', $user->id)->pluck('id');
        $courtIds = Court::query()->whereIn('venue_id', $venueIds)->pluck('id');

        $query = Booking::query()
            ->whereHas('slot', function ($q) use ($courtIds) {
                $q->whereIn('court_id', $courtIds);
            })
            ->with(['user', 'slot.court.venue', 'slot.court.sport']);

        // Filter berdasarkan tanggal
        if ($request->filled('date')) {
            $query->whereHas('slot', function ($q) use ($request) {
                $q->whereDate('start_at', $request->query('date'));
            });
        } elseif ($request->query('period') === 'today') {
            $query->whereHas('slot', function ($q) {
                $q->whereDate('start_at', Carbon::today());
            });
        }

        // Filter status booking
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        // Pencarian berdasarkan kode e-tiket atau nama pemesan
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('e_ticket_code', 'like', "%{$search}%")
                  ->orWhere('payment_ref', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->latest('created_at')->paginate(15)->withQueryString();

        // Statistik Cepat Operasional
        $allBookingsQuery = Booking::query()->whereHas('slot', fn ($q) => $q->whereIn('court_id', $courtIds));
        $stats = [
            'total' => (clone $allBookingsQuery)->count(),
            'pending' => (clone $allBookingsQuery)->where('status', 'PENDING')->count(),
            'paid' => (clone $allBookingsQuery)->where('status', 'PAID')->count(),
            'completed' => (clone $allBookingsQuery)->where('status', 'COMPLETED')->count(),
            'today_count' => (clone $allBookingsQuery)->whereHas('slot', fn ($q) => $q->whereDate('start_at', Carbon::today()))->count(),
        ];

        return view('venueowner.bookings.index', [
            'bookings' => $bookings,
            'stats' => $stats,
            'filters' => [
                'date' => $request->query('date'),
                'period' => $request->query('period'),
                'status' => $request->query('status'),
                'search' => $request->query('search'),
            ],
        ]);
    }

    /**
     * Tampilkan detail reservasi pemesanan dan validasi E-Tiket.
     */
    public function show(Request $request, Booking $booking): View
    {
        $this->authorizeBooking($request, $booking);

        $booking->load(['user', 'slot.court.venue', 'slot.court.sport']);

        return view('venueowner.bookings.show', [
            'booking' => $booking,
        ]);
    }

    /**
     * Check-in kehadiran pemain di lapangan menggunakan validasi E-Tiket.
     * Mengubah status reservasi menjadi COMPLETED.
     */
    public function checkIn(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($request, $booking);

        if ($booking->status === 'COMPLETED') {
            return back()->with('info', 'Pemesanan ini sudah pernah di-check in sebelumnya.');
        }

        if ($booking->status === 'CANCELLED') {
            return back()->with('error', 'Pemesanan ini telah dibatalkan dan tidak valid untuk check-in.');
        }

        $booking->update([
            'status' => 'COMPLETED',
        ]);

        if (Schema::hasTable('activity_logs')) {
            ActivityLog::recordLog(
                $request->user()->id,
                'BOOKING_CHECKIN',
                "Staf lapangan melakukan check-in e-tiket {$booking->e_ticket_code} (Pemesan: {$booking->user->name}).",
                $request->ip()
            );
        }

        return back()->with('success', "Check-in berhasil! E-Tiket {$booking->e_ticket_code} terverifikasi valid dan pemain siap menggunakan lapangan.");
    }

    /**
     * Konfirmasi pembayaran lunas di lokasi (On-site / Cash / Manual transfer).
     */
    public function confirmPayment(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($request, $booking);

        if ($booking->status !== 'PENDING') {
            return back()->with('error', 'Hanya pemesanan berstatus PENDING yang dapat dikonfirmasi pembayarannya.');
        }

        DB::transaction(function () use ($booking) {
            $booking->update([
                'status' => 'PAID',
                'paid_at' => now(),
            ]);

            // Pastikan status slot terkunci sebagai BOOKED
            $booking->slot->update(['status' => 'BOOKED']);
        });

        if (Schema::hasTable('activity_logs')) {
            ActivityLog::recordLog(
                $request->user()->id,
                'BOOKING_CONFIRM_PAYMENT',
                "Staf mengonfirmasi pembayaran pemesanan {$booking->e_ticket_code} sebesar Rp ".number_format($booking->total_price, 0, ',', '.'),
                $request->ip()
            );
        }

        return back()->with('success', "Pembayaran untuk tiket {$booking->e_ticket_code} berhasil dikonfirmasi LUNAS.");
    }

    /**
     * Batalkan pemesanan jika terjadi kendala teknis di lapangan.
     */
    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeBooking($request, $booking);

        if ($booking->status === 'COMPLETED') {
            return back()->with('error', 'Pemesanan yang telah selesai tidak dapat dibatalkan.');
        }

        DB::transaction(function () use ($booking) {
            $booking->update([
                'status' => 'CANCELLED',
            ]);

            // Buka kembali status slot jadwal agar dapat dipesan ulang
            $booking->slot->update(['status' => 'AVAILABLE']);
        });

        if (Schema::hasTable('activity_logs')) {
            ActivityLog::recordLog(
                $request->user()->id,
                'BOOKING_CANCEL',
                "Mitra membatalkan pemesanan {$booking->e_ticket_code}.",
                $request->ip()
            );
        }

        return back()->with('success', "Pemesanan {$booking->e_ticket_code} berhasil dibatalkan dan slot jadwal telah dibuka kembali.");
    }

    /**
     * Validasi bahwa booking adalah untuk lapangan milik pengelola arena ini.
     */
    protected function authorizeBooking(Request $request, Booking $booking): void
    {
        $venue = $booking->slot->court->venue;
        if ($request->user()->role !== 'SUPER_ADMIN' && $venue->owner_id !== $request->user()->id) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang atas pemesanan ini.');
        }
    }
}

