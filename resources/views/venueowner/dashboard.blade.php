@extends('layouts.venueowner')

@section('title', 'Dashboard Operasional Pengelola Arena')
@section('page_title', 'Dashboard Operasional')

@push('styles')
<style>
    .grid-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .stat-icon.emerald { background-color: #d1fae5; color: #059669; }
    .stat-icon.blue { background-color: #e0f2fe; color: #0284c7; }
    .stat-icon.amber { background-color: #fef3c7; color: #d97706; }
    .stat-icon.purple { background-color: #f3e8ff; color: #9333ea; }

    .stat-icon svg { width: 24px; height: 24px; }

    .stat-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        font-weight: 600;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .stat-value {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--text-main);
    }

    .stat-sub {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .banner-pending {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .banner-pending-title {
        font-weight: 700;
        color: #92400e;
        font-size: 0.95rem;
        margin-bottom: 4px;
    }

    .banner-pending-desc {
        font-size: 0.84rem;
        color: #b45309;
    }

    .table-container {
        overflow-x: auto;
    }

    table.data-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    table.data-table th {
        background-color: #f8fafc;
        padding: 12px 16px;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--border-color);
    }

    table.data-table td {
        padding: 14px 16px;
        font-size: 0.88rem;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-main);
    }

    table.data-table tr:hover {
        background-color: #fbfcfe;
    }

    .slots-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
        margin-top: 14px;
    }

    .slot-card {
        padding: 12px 14px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .slot-card.available {
        background-color: #f0fdf4;
        border-color: #bbf7d0;
    }

    .slot-card.booked {
        background-color: #eff6ff;
        border-color: #bfdbfe;
    }

    .slot-card.off {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        opacity: 0.75;
    }

    .slot-time {
        font-size: 0.95rem;
        font-weight: 800;
    }

    .slot-court {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 600;
    }
</style>
@endpush

@section('content')

@if($stats['pending_venues'] > 0)
    <div class="banner-pending">
        <div>
            <div class="banner-pending-title">⏳ Menunggu Verifikasi Super Admin</div>
            <div class="banner-pending-desc">
                Anda memiliki {{ $stats['pending_venues'] }} arena yang masih dalam proses peninjauan berkas legalitas dan survei oleh tim Super Admin SportHive. Lapangan akan dapat dipesan publik setelah status menjadi APPROVED.
            </div>
        </div>
        <a href="{{ route('venueowner.venues.index') }}" class="btn btn-secondary btn-sm" style="white-space: nowrap;">Lihat Status Arena</a>
    </div>
@endif

<!-- Quick Stats -->
<div class="grid-stats">
    <div class="stat-card">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <div>
            <div class="stat-label">Total Arena / Venue</div>
            <div class="stat-value">{{ $stats['total_venues'] }}</div>
            <div class="stat-sub">{{ $stats['approved_venues'] }} Disetujui · {{ $stats['pending_venues'] }} Pending</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-label">Lapangan Aktif</div>
            <div class="stat-value">{{ $stats['active_courts'] }} <span style="font-size:0.85rem; font-weight:500; color:var(--text-muted);">/ {{ $stats['total_courts'] }}</span></div>
            <div class="stat-sub">Siap dijadwalkan</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-label">Slot Waktu Hari Ini</div>
            <div class="stat-value">{{ $stats['today_slots_total'] }}</div>
            <div class="stat-sub">{{ $stats['today_slots_available'] }} Tersedia · {{ $stats['today_slots_booked'] }} Terisi</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon purple">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-label">Total Omset Reservasi</div>
            <div class="stat-value">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
            <div class="stat-sub">Dari transaksi valid/selesai</div>
        </div>
    </div>
</div>

<!-- Operational Section: Today's Bookings & Slot Monitor -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
    <!-- Left: Today's Incoming Bookings -->
    <div class="card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <div>
                <h2 style="font-size: 1.1rem; font-weight: 700;">📋 Reservasi Masuk Hari Ini</h2>
                <p style="font-size: 0.8rem; color: var(--text-muted);">Pantau kedatangan pemain dan verifikasi tiket di lapangan</p>
            </div>
            <a href="{{ route('venueowner.bookings.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode Tiket</th>
                        <th>Pemesan</th>
                        <th>Lapangan & Jam</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi Lapangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($today_bookings as $booking)
                        <tr>
                            <td>
                                <strong style="font-family: monospace; color: #0284c7;">{{ $booking->e_ticket_code ?? 'SH-'.str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</strong>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $booking->user->name ?? '-' }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $booking->user->phone ?? $booking->user->email }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ $booking->slot->court->name ?? '-' }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">
                                    {{ $booking->slot->start_at->format('H:i') }} - {{ $booking->slot->end_at->format('H:i') }} WIB
                                </div>
                            </td>
                            <td>
                                <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                @if($booking->status === 'COMPLETED')
                                    <span class="badge-pill badge-info">✓ CHECKED-IN</span>
                                @elseif($booking->status === 'PAID')
                                    <span class="badge-pill badge-success">LUNAS</span>
                                @elseif($booking->status === 'PENDING')
                                    <span class="badge-pill badge-warning">PENDING</span>
                                @else
                                    <span class="badge-pill badge-danger">{{ $booking->status }}</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    @if($booking->status === 'PAID')
                                        <form method="POST" action="{{ route('venueowner.bookings.check-in', $booking) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Konfirmasi check-in pemain di lapangan?')">Check-In</button>
                                        </form>
                                    @elseif($booking->status === 'PENDING')
                                        <form method="POST" action="{{ route('venueowner.bookings.confirm-payment', $booking) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Konfirmasi pelunasan tiket ini?')">Lunas</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('venueowner.bookings.show', $booking) }}" class="btn btn-secondary btn-sm">Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 32px;">
                                Belum ada reservasi masuk untuk jadwal hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: Quick Actions & Slot Monitor -->
    <div>
        <div class="card" style="margin-bottom: 24px;">
            <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 14px;">⚡ Aksi Cepat Operasional</h3>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a href="{{ route('venueowner.slots.create') }}" class="btn btn-primary" style="justify-content: center;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Generator Slot Jadwal Massal
                </a>
                <a href="{{ route('venueowner.courts.create') }}" class="btn btn-secondary" style="justify-content: center;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Lapangan Baru
                </a>
                <a href="{{ route('venueowner.venues.index') }}" class="btn btn-secondary" style="justify-content: center;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Kelola Profil Arena
                </a>
            </div>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 0.95rem; font-weight: 700;">⏱ Pemantauan Slot Hari Ini</h3>
                <a href="{{ route('venueowner.slots.index') }}" style="font-size: 0.78rem; color: var(--primary); font-weight: 600; text-decoration: none;">Semua Slot</a>
            </div>

            <div class="slots-grid" style="grid-template-columns: 1fr; margin-top: 12px;">
                @forelse($today_slots as $slot)
                    <div class="slot-card {{ strtolower($slot->status) }}">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="slot-time">{{ $slot->start_at->format('H:i') }} - {{ $slot->end_at->format('H:i') }}</span>
                            @if($slot->status === 'AVAILABLE')
                                <span class="badge-pill badge-success" style="font-size: 0.68rem;">TERSEDIA</span>
                            @elseif($slot->status === 'BOOKED')
                                <span class="badge-pill badge-info" style="font-size: 0.68rem;">BOOKED</span>
                            @else
                                <span class="badge-pill" style="background:#e2e8f0; font-size: 0.68rem;">OFF</span>
                            @endif
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="slot-court">{{ $slot->court->name }} · Rp {{ number_format($slot->price, 0, ',', '.') }}</span>
                            @if($slot->status !== 'BOOKED')
                                <form method="POST" action="{{ route('venueowner.slots.toggle-status', $slot) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-secondary btn-sm" style="padding: 2px 6px; font-size: 0.7rem;">
                                        {{ $slot->status === 'AVAILABLE' ? 'Nonaktifkan' : 'Buka' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="font-size: 0.82rem; color: var(--text-muted); text-align: center; padding: 20px;">
                        Belum ada jadwal slot waktu yang dibuat untuk hari ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

