<?php

namespace App\Http\Controllers\VenueOwner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Sport;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VenueProfileController extends Controller
{
    /**
     * Tampilkan daftar profil venue milik pengelola arena.
     */
    public function index(Request $request): View
    {
        $venues = Venue::query()
            ->where('owner_id', $request->user()->id)
            ->with(['courts.sport'])
            ->withCount('courts')
            ->latest('created_at')
            ->get();

        return view('venueowner.venues.index', [
            'venues' => $venues,
        ]);
    }

    /**
     * Formulir pendaftaran venue baru oleh mitra pengelola.
     */
    public function create(): View
    {
        $sports = Sport::query()->where('is_active', true)->orderBy('name')->get();

        return view('venueowner.venues.create', [
            'sports' => $sports,
        ]);
    }

    /**
     * Simpan data profil venue baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'photo_url' => ['nullable', 'url', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $photoUrl = $validated['photo_url'] ?? null;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('venues', 'public');
            $photoUrl = Storage::url($path);
        }

        $venue = Venue::query()->create([
            'owner_id' => $request->user()->id,
            'name' => $validated['name'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'description' => $validated['description'] ?? null,
            'photo_url' => $photoUrl,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'validation_status' => 'PENDING', // Otomatis berstatus pending untuk diverifikasi Super Admin
        ]);

        if (Schema::hasTable('activity_logs')) {
            ActivityLog::recordLog(
                $request->user()->id,
                'VENUE_CREATE',
                "Mitra mendaftarkan venue baru '{$venue->name}' di kota {$venue->city}.",
                $request->ip()
            );
        }

        return redirect()->route('venueowner.venues.index')
            ->with('success', "Profil arena '{$venue->name}' berhasil didaftarkan dan menunggu verifikasi Super Admin.");
    }

    /**
     * Tampilkan detail profil venue beserta statistik dan fasilitas lapangan.
     */
    public function show(Request $request, Venue $venue): View
    {
        $this->authorizeOwner($request, $venue);

        $venue->load(['courts.sport', 'courts.timeSlots']);

        return view('venueowner.venues.show', [
            'venue' => $venue,
        ]);
    }

    /**
     * Formulir edit profil venue.
     */
    public function edit(Request $request, Venue $venue): View
    {
        $this->authorizeOwner($request, $venue);

        return view('venueowner.venues.edit', [
            'venue' => $venue,
        ]);
    }

    /**
     * Perbarui data profil venue.
     */
    public function update(Request $request, Venue $venue): RedirectResponse
    {
        $this->authorizeOwner($request, $venue);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'photo_url' => ['nullable', 'url', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $photoUrl = $venue->photo_url;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('venues', 'public');
            $photoUrl = Storage::url($path);
        } elseif (! empty($validated['photo_url'])) {
            $photoUrl = $validated['photo_url'];
        }

        $venue->update([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'description' => $validated['description'] ?? null,
            'photo_url' => $photoUrl,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
        ]);

        if (Schema::hasTable('activity_logs')) {
            ActivityLog::recordLog(
                $request->user()->id,
                'VENUE_UPDATE',
                "Mitra memperbarui data profil arena '{$venue->name}'.",
                $request->ip()
            );
        }

        return redirect()->route('venueowner.venues.index')
            ->with('success', "Profil arena '{$venue->name}' berhasil diperbarui.");
    }

    /**
     * Validasi kepemilikan venue agar mitra tidak mengakses venue milik orang lain.
     */
    protected function authorizeOwner(Request $request, Venue $venue): void
    {
        if ($request->user()->role !== 'SUPER_ADMIN' && $venue->owner_id !== $request->user()->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola venue ini.');
        }
    }
}

