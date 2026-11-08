@extends('layouts.venueowner')

@section('title', 'Detail Arena: ' . $venue->name)
@section('page_title', 'Detail Arena')

@push('styles')
<style>
    .venue-header-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        overflow: hidden;
        margin-bottom: 24px;
        display: flex;
        gap: 24px;
    }

    .venue-cover {
        width: 320px;
        min-height: 220px;
        object-fit: cover;
        background-color: #f1f5f9;
    }

    .venue-info {
        padding: 24px 24px 24px 0;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    @media (max-width: 768px) {
        .venue-header-card {
            flex-direction: column;
        }
        .venue-cover {
            width: 100%;
            height: 200px;
        }
        .venue-info {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')

<div style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 800;">{{ $venue->name }}</h2>
        <p style="font-size: 0.82rem; color: var(--text-muted);">{{ $venue->city }} · {{ $venue->address }}</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('venueowner.venues.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
        <a href="{{ route('venueowner.venues.edit', $venue) }}" class="btn btn-secondary btn-sm">Edit Profil</a>
        <a href="{{ route('venueowner.courts.create', ['venue_id' => $venue->id]) }}" class="btn btn-primary btn-sm">+ Tambah Lapangan</a>
    </div>
</div>

<div class="venue-header-card">
    @if($venue->photo_url)
        <img src="{{ $venue->photo_url }}" alt="{{ $venue->name }}" class="venue-cover" onerror="this.src='https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&auto=format&fit=crop&q=60'">
    @else
        <div class="venue-cover" style="display: grid; place-items: center; color: var(--text-muted);">
            Foto Fasilitas Belum Diunggah
        </div>
    @endif

    <div class="venue-info">
        <div>
            <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 8px;">
                @if($venue->validation_status === 'APPROVED')
                    <span class="badge-pill badge-success">✓ STATUS: APPROVED (Resmi Beroperasi)</span>
                @elseif($venue->validation_status === 'PENDING')
                    <span class="badge-pill badge-warning">⏳ STATUS: MENUNGGU VERIFIKASI SUPER ADMIN</span>
                @else
                    <span class="badge-pill badge-danger">✕ STATUS: REJECTED</span>
                @endif
            </div>

            <p style="font-size: 0.9rem; color: #334155; line-height: 1.6; margin-bottom: 16px;">
                {{ $venue->description ?? 'Belum ada catatan deskripsi fasilitas untuk arena ini.' }}
            </p>
        </div>

        <div style="display: flex; gap: 24px; font-size: 0.85rem; color: var(--text-muted); border-top: 1px solid var(--border-color); padding-top: 14px;">
            <div>Total Lapangan: <strong style="color: var(--text-main);">{{ $venue->courts->count() }}</strong></div>
            <div>Cabang Olahraga: <strong style="color: var(--text-main);">{{ $venue->courts->pluck('sport.name')->filter()->unique()->implode(', ') ?: '-' }}</strong></div>
            <div>Terdaftar Sejak: <strong style="color: var(--text-main);">{{ $venue->created_at->format('d M Y') }}</strong></div>
        </div>
    </div>
</div>

<!-- Courts belonging to this venue -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
        <div>
            <h3 style="font-size: 1.05rem; font-weight: 700;">Daftar Lapangan Olahraga di Arena Ini</h3>
            <p style="font-size: 0.78rem; color: var(--text-muted);">Kelola lapangan, jenis olahraga, dan ketersediaan waktu sewa</p>
        </div>
        <a href="{{ route('venueowner.courts.create', ['venue_id' => $venue->id]) }}" class="btn btn-primary btn-sm">+ Tambah Lapangan</a>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #f8fafc; border-bottom: 1px solid var(--border-color);">
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">Nama Lapangan</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">Cabang Olahraga</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">Status Lapangan</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">Total Slot Waktu</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($venue->courts as $court)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 14px 16px; font-weight: 700;">{{ $court->name }}</td>
                        <td style="padding: 14px 16px;">
                            <span class="badge-pill badge-info">⚽ {{ $court->sport->name ?? '-' }}</span>
                        </td>
                        <td style="padding: 14px 16px;">
                            @if($court->is_active)
                                <span class="badge-pill badge-success">AKTIF</span>
                            @else
                                <span class="badge-pill badge-danger">NONAKTIF</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; font-weight: 600;">
                            {{ $court->timeSlots->count() }} Slot Terdaftar
                        </td>
                        <td style="padding: 14px 16px; text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="{{ route('venueowner.slots.index', ['court_id' => $court->id]) }}" class="btn btn-secondary btn-sm">Atur Slot Waktu</a>
                                <a href="{{ route('venueowner.courts.edit', $court) }}" class="btn btn-secondary btn-sm">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 32px;">
                            Belum ada lapangan yang didaftarkan untuk arena ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

