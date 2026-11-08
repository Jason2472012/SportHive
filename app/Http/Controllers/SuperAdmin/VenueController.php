<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VenueController extends Controller
{
    /**
     * Menampilkan daftar Venue & Pengajuan Validasi Mitra.
     */
    public function index(Request $request): View
    {
        $query = Venue::query()->with(['owner', 'validator', 'courts.sport']);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('owner', function ($oq) use ($search) {
                        $oq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('validation_status') && in_array($request->input('validation_status'), ['PENDING', 'APPROVED', 'REJECTED'], true)) {
            $query->where('validation_status', $request->input('validation_status'));
        }

        $venues = $query->orderByRaw("FIELD(validation_status, 'PENDING', 'APPROVED', 'REJECTED')")
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $summary = [
            'total' => Venue::count(),
            'pending' => Venue::where('validation_status', 'PENDING')->count(),
            'approved' => Venue::where('validation_status', 'APPROVED')->count(),
            'rejected' => Venue::where('validation_status', 'REJECTED')->count(),
        ];

        $venueOwners = User::where('role', 'VENUE_OWNER')->orderBy('name')->get();

        return view('superadmin.venues.index', [
            'venues' => $venues,
            'summary' => $summary,
            'venueOwners' => $venueOwners,
        ]);
    }

    /**
     * Tambah Venue baru oleh Super Admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'owner_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'name' => ['required', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photo_url' => ['nullable', 'string', 'max:255'],
            'validation_status' => ['required', Rule::in(['PENDING', 'APPROVED', 'REJECTED'])],
        ]);

        $isValidated = in_array($validated['validation_status'], ['APPROVED', 'REJECTED'], true);

        $venue = Venue::create([
            'owner_id' => $validated['owner_id'],
            'name' => $validated['name'],
            'city' => $validated['city'],
            'address' => $validated['address'],
            'description' => $validated['description'] ?? null,
            'photo_url' => $validated['photo_url'] ?? null,
            'validation_status' => $validated['validation_status'],
            'validated_by' => $isValidated ? $request->user()->id : null,
            'validated_at' => $isValidated ? now() : null,
        ]);

        ActivityLog::recordLog(
            $request->user()->id,
            'VENUE_CREATE',
            "Menambahkan data venue: {$venue->name} ({$venue->city}) dengan status {$venue->validation_status}.",
            $request->ip()
        );

        return redirect()
            ->route('superadmin.venues.index')
            ->with('success', "Venue {$venue->name} berhasil ditambahkan.");
    }

    /**
     * Update data Venue.
     */
    public function update(Request $request, Venue $venue): RedirectResponse
    {
        $validated = $request->validate([
            'owner_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'name' => ['required', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photo_url' => ['nullable', 'string', 'max:255'],
        ]);

        $venue->update($validated);

        ActivityLog::recordLog(
            $request->user()->id,
            'VENUE_UPDATE',
            "Memperbarui profil venue: {$venue->name} ({$venue->city}).",
            $request->ip()
        );

        return redirect()
            ->route('superadmin.venues.index')
            ->with('success', "Data venue {$venue->name} berhasil diperbarui.");
    }

    /**
     * Setujui Pendaftaran Mitra Venue (Approve) sesuai Activity Diagram Validasi Mitra.
     */
    public function approve(Request $request, Venue $venue): RedirectResponse
    {
        $venue->verifyVenue('APPROVED', $request->user()->id);

        ActivityLog::recordLog(
            $request->user()->id,
            'VENUE_APPROVE',
            "Menyetujui validasi mitra untuk venue: {$venue->name} (Owner ID: {$venue->owner_id}).",
            $request->ip()
        );

        Notification::sendNotification(
            $venue->owner_id,
            'SYSTEM',
            'Pendaftaran Mitra Venue Disetujui',
            "Selamat! Pengajuan mitra untuk venue \"{$venue->name}\" telah diverifikasi dan disetujui oleh Super Admin. Anda kini dapat mengelola lapangan dan jadwal slot.",
            'VENUE',
            $venue->id
        );

        return redirect()
            ->back()
            ->with('success', "Pengajuan mitra untuk venue {$venue->name} telah disetujui (APPROVED). Notifikasi telah dikirim ke pemilik venue.");
    }

    /**
     * Tolak Pendaftaran Mitra Venue dengan Catatan (Reject) sesuai Activity Diagram Validasi Mitra.
     */
    public function reject(Request $request, Venue $venue): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_note' => ['nullable', 'string', 'max:500'],
        ]);

        $note = trim((string) ($validated['rejection_note'] ?? 'Data usaha atau berkas legalitas belum lengkap/sesuai standar verifikasi.'));
        if ($note === '') {
            $note = 'Data usaha atau berkas legalitas belum lengkap/sesuai standar verifikasi.';
        }

        $venue->verifyVenue('REJECTED', $request->user()->id);

        ActivityLog::recordLog(
            $request->user()->id,
            'VENUE_REJECT',
            "Menolak validasi mitra untuk venue: {$venue->name}. Catatan: {$note}",
            $request->ip()
        );

        Notification::sendNotification(
            $venue->owner_id,
            'SYSTEM',
            'Pendaftaran Mitra Venue Ditolak',
            "Pengajuan mitra untuk venue \"{$venue->name}\" ditolak oleh Super Admin. Catatan evaluasi: {$note}",
            'VENUE',
            $venue->id
        );

        return redirect()
            ->back()
            ->with('success', "Pengajuan mitra untuk venue {$venue->name} telah ditolak (REJECTED) beserta catatan evaluasi.");
    }

    /**
     * Hapus Venue.
     */
    public function destroy(Request $request, Venue $venue): RedirectResponse
    {
        $name = $venue->name;
        $venue->delete();

        ActivityLog::recordLog(
            $request->user()->id,
            'VENUE_DELETE',
            "Menghapus venue: {$name}.",
            $request->ip()
        );

        return redirect()
            ->route('superadmin.venues.index')
            ->with('success', "Venue {$name} berhasil dihapus.");
    }
}

