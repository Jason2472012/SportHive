<?php

namespace App\Http\Controllers\VenueOwner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Court;
use App\Models\Sport;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourtController extends Controller
{
    /**
     * Tampilkan daftar seluruh lapangan dari venue milik pengelola arena.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $venueIds = Venue::query()->where('owner_id', $user->id)->pluck('id');

        $query = Court::query()
            ->whereIn('venue_id', $venueIds)
            ->with(['venue', 'sport'])
            ->withCount('timeSlots');

        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->query('venue_id'));
        }

        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->query('sport_id'));
        }

        $courts = $query->latest('created_at')->get();
        $venues = Venue::query()->where('owner_id', $user->id)->get();
        $sports = Sport::query()->where('is_active', true)->orderBy('name')->get();

        return view('venueowner.courts.index', [
            'courts' => $courts,
            'venues' => $venues,
            'sports' => $sports,
            'selected_venue' => $request->query('venue_id'),
            'selected_sport' => $request->query('sport_id'),
        ]);
    }

    /**
     * Formulir penambahan lapangan baru.
     */
    public function create(Request $request): View
    {
        $venues = Venue::query()
            ->where('owner_id', $request->user()->id)
            ->get();

        $sports = Sport::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('venueowner.courts.create', [
            'venues' => $venues,
            'sports' => $sports,
            'selected_venue' => $request->query('venue_id'),
        ]);
    }

    /**
     * Simpan lapangan baru ke dalam database.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $myVenueIds = Venue::query()->where('owner_id', $user->id)->pluck('id')->toArray();

        $validated = $request->validate([
            'venue_id' => ['required', 'integer', Rule::in($myVenueIds)],
            'sport_id' => ['required', 'integer', 'exists:sports,id'],
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('courts', 'name')->where(function ($query) use ($request) {
                    return $query->where('venue_id', $request->venue_id);
                }),
            ],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.unique' => 'Nama lapangan ini sudah digunakan di venue yang sama.',
        ]);

        $court = Court::query()->create([
            'venue_id' => $validated['venue_id'],
            'sport_id' => $validated['sport_id'],
            'name' => $validated['name'],
            'is_active' => $request->boolean('is_active', true),
            'created_at' => now(),
        ]);

        if (Schema::hasTable('activity_logs')) {
            ActivityLog::recordLog(
                $user->id,
                'COURT_CREATE',
                "Mitra menambahkan lapangan baru '{$court->name}' di venue ID #{$court->venue_id}.",
                $request->ip()
            );
        }

        return redirect()->route('venueowner.courts.index')
            ->with('success', "Lapangan '{$court->name}' berhasil ditambahkan.");
    }

    /**
     * Formulir edit lapangan.
     */
    public function edit(Request $request, Court $court): View
    {
        $this->authorizeCourt($request, $court);

        $venues = Venue::query()->where('owner_id', $request->user()->id)->get();
        $sports = Sport::query()->where('is_active', true)->orderBy('name')->get();

        return view('venueowner.courts.edit', [
            'court' => $court,
            'venues' => $venues,
            'sports' => $sports,
        ]);
    }

    /**
     * Perbarui data lapangan.
     */
    public function update(Request $request, Court $court): RedirectResponse
    {
        $this->authorizeCourt($request, $court);

        $validated = $request->validate([
            'sport_id' => ['required', 'integer', 'exists:sports,id'],
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('courts', 'name')
                    ->where(function ($query) use ($court) {
                        return $query->where('venue_id', $court->venue_id);
                    })
                    ->ignore($court->id),
            ],
            'is_active' => ['required', 'boolean'],
        ]);

        $court->update([
            'sport_id' => $validated['sport_id'],
            'name' => $validated['name'],
            'is_active' => $validated['is_active'],
        ]);

        if (Schema::hasTable('activity_logs')) {
            ActivityLog::recordLog(
                $request->user()->id,
                'COURT_UPDATE',
                "Mitra memperbarui data lapangan '{$court->name}'.",
                $request->ip()
            );
        }

        return redirect()->route('venueowner.courts.index')
            ->with('success', "Data lapangan '{$court->name}' berhasil diperbarui.");
    }

    /**
     * Buka / Tutup (Aktifkan / Nonaktifkan) lapangan secara cepat.
     */
    public function toggleStatus(Request $request, Court $court): RedirectResponse
    {
        $this->authorizeCourt($request, $court);

        $newStatus = ! $court->is_active;
        $court->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan kembali' : 'dinonaktifkan sementara';

        if (Schema::hasTable('activity_logs')) {
            ActivityLog::recordLog(
                $request->user()->id,
                'COURT_TOGGLE',
                "Status operasional lapangan '{$court->name}' {$statusText}.",
                $request->ip()
            );
        }

        return back()->with('success', "Lapangan '{$court->name}' berhasil {$statusText}.");
    }

    /**
     * Hapus lapangan jika belum memiliki relasi jadwal dan pemesanan aktif.
     */
    public function destroy(Request $request, Court $court): RedirectResponse
    {
        $this->authorizeCourt($request, $court);

        // Periksa apakah sudah ada time slot yang pernah di-booking
        $hasBookings = $court->timeSlots()->whereHas('bookings')->exists();

        if ($hasBookings) {
            return back()->with('error', "Lapangan '{$court->name}' tidak dapat dihapus karena memiliki riwayat pemesanan. Anda dapat menonaktifkan statusnya.");
        }

        $courtName = $court->name;
        $court->timeSlots()->delete(); // Hapus slot yang belum dibooking
        $court->delete();

        if (Schema::hasTable('activity_logs')) {
            ActivityLog::recordLog(
                $request->user()->id,
                'COURT_DELETE',
                "Mitra menghapus lapangan '{$courtName}'.",
                $request->ip()
            );
        }

        return redirect()->route('venueowner.courts.index')
            ->with('success', "Lapangan '{$courtName}' berhasil dihapus.");
    }

    /**
     * Proteksi keamanan agar mitra hanya dapat mengelola lapangan miliknya.
     */
    protected function authorizeCourt(Request $request, Court $court): void
    {
        if ($request->user()->role !== 'SUPER_ADMIN' && $court->venue->owner_id !== $request->user()->id) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang atas lapangan ini.');
        }
    }
}

