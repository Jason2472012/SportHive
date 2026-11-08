@extends('layouts.venueowner')

@section('title', 'Kelola Profil Arena / Venue')
@section('page_title', 'Profil Arena Olahraga')

@push('styles')
<style>
    .venue-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 24px;
    }

    .venue-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .venue-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    }

    .venue-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        background-color: #f1f5f9;
    }

    .venue-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .venue-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 6px;
    }

    .venue-location {
        font-size: 0.82rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 12px;
    }

    .venue-desc {
        font-size: 0.85rem;
        color: #475569;
        line-height: 1.5;
        margin-bottom: 16px;
        flex: 1;
    }

    .venue-footer {
        padding-top: 14px;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .sports-tag-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 14px;
    }

    .sport-tag {
        font-size: 0.72rem;
        font-weight: 600;
        background-color: #f1f5f9;
        color: #334155;
        padding: 3px 8px;
        border-radius: 6px;
    }
</style>
@endpush

@section('content')

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h2 style="font-size: 1.3rem; font-weight: 800;">🏢 Arena & Fasilitas Olahraga Saya</h2>
        <p style="font-size: 0.85rem; color: var(--text-muted);">Kelola profil, foto fasilitas, alamat, dan legalitas arena yang Anda miliki</p>
    </div>
    <a href="{{ route('venueowner.venues.create') }}" class="btn btn-primary">
        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Daftarkan Arena Baru
    </a>
</div>

<div class="venue-grid">
    @forelse($venues as $venue)
        <div class="venue-card">
            @if($venue->photo_url)
                <img src="{{ $venue->photo_url }}" alt="{{ $venue->name }}" class="venue-img" onerror="this.src='https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&auto=format&fit=crop&q=60'">
            @else
                <div class="venue-img" style="display: grid; place-items: center; color: var(--text-muted); font-size: 0.85rem;">
                    Foto Fasilitas Belum Diunggah
                </div>
            @endif

            <div class="venue-body">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                    <h3 class="venue-title">{{ $venue->name }}</h3>
                    @if($venue->validation_status === 'APPROVED')
                        <span class="badge-pill badge-success">✓ APPROVED</span>
                    @elseif($venue->validation_status === 'PENDING')
                        <span class="badge-pill badge-warning">⏳ PENDING</span>
                    @else
                        <span class="badge-pill badge-danger">✕ REJECTED</span>
                    @endif
                </div>

                <div class="venue-location">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>{{ $venue->city }} · {{ Str::limit($venue->address, 35) }}</span>
                </div>

                <!-- Cabang Olahraga Tersedia -->
                <div class="sports-tag-list">
                    @forelse($venue->courts->pluck('sport.name')->filter()->unique() as $sportName)
                        <span class="sport-tag">⚽ {{ $sportName }}</span>
                    @empty
                        <span style="font-size: 0.75rem; color: var(--text-muted);">Belum ada lapangan ditambahkan</span>
                    @endforelse
                </div>

                <p class="venue-desc">{{ Str::limit($venue->description ?? 'Tidak ada deskripsi arena.', 90) }}</p>

                <div class="venue-footer">
                    <div style="font-size: 0.82rem; font-weight: 700; color: #0284c7;">
                        {{ $venue->courts_count }} Lapangan
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('venueowner.venues.show', $venue) }}" class="btn btn-secondary btn-sm">Detail</a>
                        <a href="{{ route('venueowner.venues.edit', $venue) }}" class="btn btn-secondary btn-sm">Edit Profil</a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 48px;">
            <p style="color: var(--text-muted); margin-bottom: 16px;">Anda belum mendaftarkan arena / venue olahraga.</p>
            <a href="{{ route('venueowner.venues.create') }}" class="btn btn-primary">Daftarkan Arena Sekarang</a>
        </div>
    @endforelse
</div>

@endsection

