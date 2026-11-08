<?php

namespace App\Http\Controllers\VenueOwner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Court;
use App\Models\TimeSlot;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TimeSlotController extends Controller
{
    /**
     * Tampilkan jadwal ketersediaan slot waktu dan harga per lapangan.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $venueIds = Venue::query()->where('owner_id', $user->id)->pluck('id');
        $courts = Court::query()->whereIn('venue_id', $venueIds)->with(['venue', 'sport'])->get();
        $courtIds = $courts->pluck('id');

        $selectedCourtId = $request->query('court_id', $courtIds->first());
        $selectedDate = $request->query('date', Carbon::today()->toDateString());
        $selectedStatus = $request->query('status');

        $query = TimeSlot::query()
            ->whereIn('court_id', $courtIds)
            ->with(['court.venue', 'court.sport', 'bookings.user']);

        if ($selectedCourtId) {
            $query->where('court_id', $selectedCourtId);
        }

        if ($selectedDate) {
            $query->whereDate('start_at', $selectedDate);
        }

        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        $slots = $query->orderBy('start_at')->get();

        // Ringkasan slot pada hari & filter yang dipilih
        $slotSummary = [
            'total' => $slots->count(),
            'available' => $slots->where('status', 'AVAILABLE')->count(),
            'booked' => $slots->where('status', 'BOOKED')->count(),
            'off' => $slots->where('status', 'OFF')->count(),
        ];

        return view('venueowner.slots.index', [
            'slots' => $slots,
            'courts' => $courts,
            'selected_court_id' => $selectedCourtId,
            'selected_date' => $selectedDate,
            'selected_status' => $selectedStatus,
            'slot_summary' => $slotSummary,
        ]);
    }

    /**
     * Formulir penambahan slot waktu tunggal atau generator massal.
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $venueIds = Venue::query()->where('owner_id', $user->id)->pluck('id');
        $courts = Court::query()->whereIn('venue_id', $venueIds)->with(['venue', 'sport'])->where('is_active', true)->get();

        return view('venueowner.slots.create', [
            'courts' => $courts,
            'selected_court_id' => $request->query('court_id'),
            'selected_date' => $request->query('date', Carbon::today()->toDateString()),
        ]);
    }

    /**
     * Simpan slot waktu tunggal.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $myCourtIds = Court::query()
            ->whereHas('venue', fn ($q) => $q->where('owner_id', $user->id))
            ->pluck('id')
            ->toArray();

        $validated = $request->validate([
            'court_id' => ['required', 'integer', Rule::in($myCourtIds)],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:AVAILABLE,OFF'],
        ]);

        $startAt = Carbon::parse("{$validated['date']} {$validated['start_time']}");
        $endAt = Carbon::parse("{$validated['date']} {$validated['end_time']}");

        // Cek bentrok jam jadwal di lapangan yang sama
        $isOverlapping = TimeSlot::query()
            ->where('court_id', $validated['court_id'])
            ->where(function ($query) use ($startAt, $endAt) {
                $query->whereBetween('start_at', [$startAt, $endAt])
                      ->orWhereBetween('end_at', [$startAt, $endAt])
                      ->orWhere(function ($q) use ($startAt, $endAt) {
                          $q->where('start_at', '<=', $startAt)
                            ->where('end_at', '>=', $endAt);
                      });
            })
            ->exists();

        if ($isOverlapping) {
            return back()->withInput()->with('error', 'Jadwal slot bentrok dengan slot yang sudah ada sebelumnya pada lapangan tersebut.');
        }

        $slot = TimeSlot::query()->create([
            'court_id' => $validated['court_id'],
            'start_at' => $startAt,
            'end_at' => $endAt,
            'price' => $validated['price'],
            'status' => $validated['status'],
        ]);

        if (Schema::hasTable('activity_logs')) {
            ActivityLog::recordLog(
                $user->id,
                'SLOT_CREATE',
                "Mitra menambahkan slot jam {$validated['start_time']} - {$validated['end_time']} di lapangan ID #{$slot->court_id} dengan harga Rp ".number_format($slot->price, 0, ',', '.'),
                $request->ip()
            );
        }

        return redirect()->route('venueowner.slots.index', [
            'court_id' => $slot->court_id,
            'date' => $validated['date'],
        ])->with('success', 'Slot jadwal berhasil ditambahkan.');
    }

    /**
     * Generator Massal (Batch) Slot Jadwal Harian.
     * Fitur ini memudahkan pemilik lapangan membuka slot per jam secara otomatis dari jam buka sampai jam tutup.
     */
    public function storeBulk(Request $request): RedirectResponse
    {
        $user = $request->user();
        $myCourtIds = Court::query()
            ->whereHas('venue', fn ($q) => $q->where('owner_id', $user->id))
            ->pluck('id')
            ->toArray();

        $validated = $request->validate([
            'court_id' => ['required', 'integer', Rule::in($myCourtIds)],
            'date' => ['required', 'date'],
            'open_time' => ['required', 'date_format:H:i'],
            'close_time' => ['required', 'date_format:H:i', 'after:open_time'],
            'slot_duration_minutes' => ['required', 'integer', 'in:30,60,90,120'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $start = Carbon::parse("{$validated['date']} {$validated['open_time']}");
        $endLimit = Carbon::parse("{$validated['date']} {$validated['close_time']}");
        $duration = (int) $validated['slot_duration_minutes'];
        $createdCount = 0;
        $skippedCount = 0;

        while ($start->copy()->addMinutes($duration)->lte($endLimit)) {
            $slotEnd = $start->copy()->addMinutes($duration);

            // Cek apakah slot sudah ada
            $exists = TimeSlot::query()
                ->where('court_id', $validated['court_id'])
                ->where(function ($q) use ($start, $slotEnd) {
                    $q->whereBetween('start_at', [$start, $slotEnd])
                      ->orWhereBetween('end_at', [$start, $slotEnd])
                      ->orWhere(function ($sub) use ($start, $slotEnd) {
                          $sub->where('start_at', '<=', $start)
                              ->where('end_at', '>=', $slotEnd);
                      });
                })
                ->exists();

            if (! $exists) {
                TimeSlot::query()->create([
                    'court_id' => $validated['court_id'],
                    'start_at' => $start,
                    'end_at' => $slotEnd,
                    'price' => $validated['price'],
                    'status' => 'AVAILABLE',
                ]);
                $createdCount++;
            } else {
                $skippedCount++;
            }

            $start = $slotEnd;
        }

        if (Schema::hasTable('activity_logs')) {
            ActivityLog::recordLog(
                $user->id,
                'SLOT_BULK_CREATE',
                "Mitra menghasilkan {$createdCount} slot otomatis pada tanggal {$validated['date']} di lapangan ID #{$validated['court_id']}.",
                $request->ip()
            );
        }

        return redirect()->route('venueowner.slots.index', [
            'court_id' => $validated['court_id'],
            'date' => $validated['date'],
        ])->with('success', "Berhasil menambahkan {$createdCount} slot waktu baru. ({$skippedCount} slot dilewati karena sudah ada).");
    }

    /**
     * Perbarui data harga atau rentang waktu slot jadwal.
     */
    public function update(Request $request, TimeSlot $slot): RedirectResponse
    {
        $this->authorizeSlot($request, $slot);

        if ($slot->status === 'BOOKED') {
            return back()->with('error', 'Slot yang sudah dipesan tidak dapat diubah.');
        }

        $validated = $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:AVAILABLE,OFF'],
        ]);

        $slot->update([
            'price' => $validated['price'],
            'status' => $validated['status'],
        ]);

        if (Schema::hasTable('activity_logs')) {
            ActivityLog::recordLog(
                $request->user()->id,
                'SLOT_UPDATE',
                "Mitra memperbarui harga/status slot ID #{$slot->id}.",
                $request->ip()
            );
        }

        return back()->with('success', 'Data slot jadwal berhasil diperbarui.');
    }

    /**
     * Buka, tutup, atau nonaktifkan slot secara real-time (AVAILABLE <-> OFF).
     */
    public function toggleStatus(Request $request, TimeSlot $slot): RedirectResponse
    {
        $this->authorizeSlot($request, $slot);

        if ($slot->status === 'BOOKED') {
            return back()->with('error', 'Slot jadwal yang sudah dipesan pelanggan tidak dapat dinonaktifkan.');
        }

        $newStatus = ($slot->status === 'AVAILABLE') ? 'OFF' : 'AVAILABLE';
        $slot->update(['status' => $newStatus]);

        $statusLabel = ($newStatus === 'AVAILABLE') ? 'dibuka kembali (Tersedia)' : 'dinonaktifkan sementara (OFF)';

        if (Schema::hasTable('activity_logs')) {
            ActivityLog::recordLog(
                $request->user()->id,
                'SLOT_TOGGLE',
                "Mitra mengubah status slot ID #{$slot->id} menjadi {$newStatus} secara real-time.",
                $request->ip()
            );
        }

        return back()->with('success', "Slot jam {$slot->start_at->format('H:i')} - {$slot->end_at->format('H:i')} berhasil {$statusLabel}.");
    }

    /**
     * Hapus slot jadwal jika belum pernah dipesan.
     */
    public function destroy(Request $request, TimeSlot $slot): RedirectResponse
    {
        $this->authorizeSlot($request, $slot);

        if ($slot->status === 'BOOKED' || $slot->bookings()->exists()) {
            return back()->with('error', 'Slot tidak dapat dihapus karena memiliki data pemesanan terkait.');
        }

        $slotTime = "{$slot->start_at->format('H:i')} - {$slot->end_at->format('H:i')}";
        $slot->delete();

        if (Schema::hasTable('activity_logs')) {
            ActivityLog::recordLog(
                $request->user()->id,
                'SLOT_DELETE',
                "Mitra menghapus slot waktu {$slotTime}.",
                $request->ip()
            );
        }

        return back()->with('success', "Slot waktu {$slotTime} berhasil dihapus.");
    }

    /**
     * Pastikan mitra hanya dapat mengakses slot milik lapangan di venue miliknya.
     */
    protected function authorizeSlot(Request $request, TimeSlot $slot): void
    {
        $venue = $slot->court->venue;
        if ($request->user()->role !== 'SUPER_ADMIN' && $venue->owner_id !== $request->user()->id) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang atas slot waktu ini.');
        }
    }
}

