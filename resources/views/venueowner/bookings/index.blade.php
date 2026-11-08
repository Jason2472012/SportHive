@extends('layouts.venueowner')

@section('title', 'Pemantauan Reservasi Masuk')
@section('page_title', 'Daftar Reservasi Masuk')

@push('styles')
<style>
    .grid-stats-booking {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .booking-stat {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 16px;
    }

    .booking-stat-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .booking-stat-value {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--text-main);
    }
</style>
@endpush

@section('content')

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h2 style="font-size: 1.3rem; font-weight: 800;">📋 Pemantauan Reservasi Masuk & Operasional</h2>
        <p style="font-size: 0.85rem; color: var(--text-muted);">Validasi tiket masuk pemain, kelola pembayaran di lokasi, dan pantau status pemesanan</p>
    </div>
</div>

<!-- Stats Bar -->
<div class="grid-stats-booking">
    <div class="booking-stat">
        <div class="booking-stat-label">Jadwal Hari Ini</div>
        <div class="booking-stat-value" style="color: #059669;">{{ $stats['today_count'] }}</div>
    </div>
    <div class="booking-stat">
        <div class="booking-stat-label">Menunggu Bayar (Pending)</div>
        <div class="booking-stat-value" style="color: #d97706;">{{ $stats['pending'] }}</div>
    </div>
    <div class="booking-stat">
        <div class="booking-stat-label">Lunas (Siap Main)</div>
        <div class="booking-stat-value" style="color: #0284c7;">{{ $stats['paid'] }}</div>
    </div>
    <div class="booking-stat">
        <div class="booking-stat-label">Selesai / Checked-In</div>
        <div class="booking-stat-value" style="color: #475569;">{{ $stats['completed'] }}</div>
    </div>
    <div class="booking-stat">
        <div class="booking-stat-label">Total Riwayat</div>
        <div class="booking-stat-value">{{ $stats['total'] }}</div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="padding: 18px 20px; margin-bottom: 24px;">
    <form method="GET" action="{{ route('venueowner.bookings.index') }}" style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 2; min-width: 220px;">
            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">Pencarian (Kode E-Tiket / Nama / No HP)</label>
            <input type="text" name="search" class="btn btn-secondary" style="width: 100%; text-align: left;" placeholder="Contoh: SH-00012, Andi, 0812..." value="{{ $filters['search'] }}">
        </div>

        <div style="min-width: 150px;">
            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">Status Pemesanan</label>
            <select name="status" class="btn btn-secondary" style="width: 100%; text-align: left;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="PAID" {{ $filters['status'] === 'PAID' ? 'selected' : '' }}>PAID (Lunas)</option>
                <option value="PENDING" {{ $filters['status'] === 'PENDING' ? 'selected' : '' }}>PENDING (Menunggu)</option>
                <option value="COMPLETED" {{ $filters['status'] === 'COMPLETED' ? 'selected' : '' }}>COMPLETED (Selesai)</option>
                <option value="CANCELLED" {{ $filters['status'] === 'CANCELLED' ? 'selected' : '' }}>CANCELLED (Batal)</option>
            </select>
        </div>

        <div style="min-width: 150px;">
            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">Pilih Tanggal Main</label>
            <input type="date" name="date" class="btn btn-secondary" style="width: 100%;" value="{{ $filters['date'] }}" onchange="this.form.submit()">
        </div>

        <div style="display: flex; gap: 8px;">
            <a href="{{ route('venueowner.bookings.index', ['period' => 'today']) }}" class="btn btn-secondary {{ $filters['period'] === 'today' ? 'btn-primary' : '' }}" style="font-size: 0.8rem;">
                Hari Ini
            </a>
            @if($filters['search'] || $filters['status'] || $filters['date'] || $filters['period'])
                <a href="{{ route('venueowner.bookings.index') }}" class="btn btn-secondary" style="font-size: 0.8rem;">Reset</a>
            @endif
        </div>
    </form>
</div>

<!-- Bookings Table -->
<div class="card">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #f8fafc; border-bottom: 1px solid var(--border-color);">
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">Kode E-Tiket</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">Pemesan</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">Jadwal & Lapangan</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">Total Biaya</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">Status</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-align: right;">Aksi Lapangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 14px 16px;">
                            <a href="{{ route('venueowner.bookings.show', $booking) }}" style="font-family: monospace; font-weight: 700; color: #0284c7; text-decoration: none;">
                                {{ $booking->e_ticket_code ?? 'SH-'.str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
                            </a>
                            <div style="font-size: 0.7rem; color: var(--text-muted);">
                                Dipesan: {{ $booking->created_at->format('d/m/Y H:i') }}
                            </div>
                        </td>
                        <td style="padding: 14px 16px;">
                            <div style="font-weight: 700;">{{ $booking->user->name ?? '-' }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $booking->user->phone ?? '-' }} · {{ $booking->user->email }}</div>
                        </td>
                        <td style="padding: 14px 16px;">
                            <div style="font-weight: 700;">{{ $booking->slot->court->name ?? '-' }}</div>
                            <div style="font-size: 0.78rem; color: var(--text-muted);">
                                📅 {{ $booking->slot->start_at->format('d M Y') }}
                                ⏱ {{ $booking->slot->start_at->format('H:i') }} - {{ $booking->slot->end_at->format('H:i') }} WIB
                            </div>
                        </td>
                        <td style="padding: 14px 16px; font-weight: 700;">
                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                        </td>
                        <td style="padding: 14px 16px;">
                            @if($booking->status === 'COMPLETED')
                                <span class="badge-pill badge-info">✓ CHECKED-IN</span>
                            @elseif($booking->status === 'PAID')
                                <span class="badge-pill badge-success">LUNAS</span>
                            @elseif($booking->status === 'PENDING')
                                <span class="badge-pill badge-warning">PENDING</span>
                            @elseif($booking->status === 'CANCELLED')
                                <span class="badge-pill badge-danger">BATAL</span>
                            @else
                                <span class="badge-pill">{{ $booking->status }}</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; text-align: right;">
                            <div style="display: inline-flex; gap: 6px; align-items: center;">
                                @if($booking->status === 'PAID')
                                    <form method="POST" action="{{ route('venueowner.bookings.check-in', $booking) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Validasi kehadiran pemain dengan kode tiket ini?')">
                                            ✓ Check-In
                                        </button>
                                    </form>
                                @elseif($booking->status === 'PENDING')
                                    <form method="POST" action="{{ route('venueowner.bookings.confirm-payment', $booking) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Konfirmasi pembayaran telah lunas?')">
                                            Konfirmasi Bayar
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('venueowner.bookings.show', $booking) }}" class="btn btn-secondary btn-sm">
                                    Detail
                                </a>

                                @if($booking->status !== 'COMPLETED' && $booking->status !== 'CANCELLED')
                                    <form method="POST" action="{{ route('venueowner.bookings.cancel', $booking) }}" onsubmit="return confirm('Batalkan reservasi ini dan buka kembali slot jadwal?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 8px;" title="Batalkan">
                                            Batal
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">
                            Tidak ada reservasi masuk yang sesuai dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bookings->hasPages())
        <div style="margin-top: 20px;">
            {{ $bookings->links() }}
        </div>
    @endif
</div>

@endsection

