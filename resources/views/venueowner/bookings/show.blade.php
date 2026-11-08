@extends('layouts.venueowner')

@section('title', 'Detail Reservasi: ' . ($booking->e_ticket_code ?? 'SH-'.$booking->id))
@section('page_title', 'Detail Reservasi & E-Tiket')

@push('styles')
<style>
    .ticket-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .ticket-header {
        background: linear-gradient(135deg, #065f46, #059669);
        color: #ffffff;
        padding: 24px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ticket-body {
        padding: 32px;
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 32px;
    }

    .ticket-info-group {
        margin-bottom: 20px;
    }

    .ticket-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }

    .ticket-val {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .qr-stub {
        background-color: #f8fafc;
        border: 2px dashed var(--border-color);
        border-radius: 12px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    @media (max-width: 768px) {
        .ticket-body {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<div style="max-width: 860px; margin: 0 auto;">
    <div style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800;">Validasi Tiket Pemesanan</h2>
            <p style="font-size: 0.82rem; color: var(--text-muted);">Periksa kelengkapan data sebelum mengizinkan pemain masuk ke lapangan</p>
        </div>
        <a href="{{ route('venueowner.bookings.index') }}" class="btn btn-secondary btn-sm">Kembali ke Daftar</a>
    </div>

    <div class="ticket-card">
        <div class="ticket-header">
            <div>
                <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.85;">SportHive E-Ticket Operasional</span>
                <h1 style="font-size: 1.6rem; font-weight: 800; font-family: monospace; letter-spacing: 0.05em;">
                    {{ $booking->e_ticket_code ?? 'SH-'.str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
                </h1>
            </div>
            <div>
                @if($booking->status === 'COMPLETED')
                    <span style="background: rgba(255,255,255,0.25); color: #ffffff; padding: 6px 14px; border-radius: 9999px; font-weight: 700; font-size: 0.85rem;">
                        ✓ CHECKED-IN (SELESAI)
                    </span>
                @elseif($booking->status === 'PAID')
                    <span style="background: #ffffff; color: #065f46; padding: 6px 14px; border-radius: 9999px; font-weight: 700; font-size: 0.85rem;">
                        ✓ LUNAS (SIAP MAIN)
                    </span>
                @elseif($booking->status === 'PENDING')
                    <span style="background: #fef3c7; color: #92400e; padding: 6px 14px; border-radius: 9999px; font-weight: 700; font-size: 0.85rem;">
                        ⏳ MENUNGGU BAYAR
                    </span>
                @else
                    <span style="background: #fee2e2; color: #991b1b; padding: 6px 14px; border-radius: 9999px; font-weight: 700; font-size: 0.85rem;">
                        ✕ BATAL
                    </span>
                @endif
            </div>
        </div>

        <div class="ticket-body">
            <div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="ticket-info-group">
                        <div class="ticket-label">Nama Pemesan (Pemain)</div>
                        <div class="ticket-val">{{ $booking->user->name ?? '-' }}</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">{{ $booking->user->email }}</div>
                    </div>

                    <div class="ticket-info-group">
                        <div class="ticket-label">Nomor Kontak WhatsApp / Telp</div>
                        <div class="ticket-val">{{ $booking->user->phone ?? 'Tidak tercantum' }}</div>
                    </div>

                    <div class="ticket-info-group">
                        <div class="ticket-label">Arena / Venue</div>
                        <div class="ticket-val">{{ $booking->slot->court->venue->name ?? '-' }}</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">{{ $booking->slot->court->venue->address }}</div>
                    </div>

                    <div class="ticket-info-group">
                        <div class="ticket-label">Lapangan & Cabor</div>
                        <div class="ticket-val">{{ $booking->slot->court->name ?? '-' }}</div>
                        <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">⚽ {{ $booking->slot->court->sport->name ?? '-' }}</div>
                    </div>

                    <div class="ticket-info-group">
                        <div class="ticket-label">Hari & Tanggal Main</div>
                        <div class="ticket-val" style="color: #0284c7;">
                            {{ $booking->slot->start_at->translatedFormat('l, d F Y') }}
                        </div>
                    </div>

                    <div class="ticket-info-group">
                        <div class="ticket-label">Sesi Jam Pemakaian</div>
                        <div class="ticket-val" style="color: #0284c7;">
                            {{ $booking->slot->start_at->format('H:i') }} - {{ $booking->slot->end_at->format('H:i') }} WIB
                        </div>
                    </div>
                </div>

                <div style="border-top: 1px solid var(--border-color); padding-top: 20px; margin-top: 10px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="ticket-info-group">
                        <div class="ticket-label">Total Pembayaran</div>
                        <div class="ticket-val" style="font-size: 1.3rem; color: #059669;">
                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="ticket-info-group">
                        <div class="ticket-label">Waktu Pembayaran</div>
                        <div class="ticket-val">
                            {{ $booking->paid_at ? $booking->paid_at->format('d/m/Y H:i') : '-' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Check-in / Stub Section -->
            <div class="qr-stub">
                <div style="width: 80px; height: 80px; background: #0f172a; border-radius: 10px; display: grid; place-items: center; color: #fff; font-weight: 800; font-size: 1.8rem; margin-bottom: 12px;">
                    SH
                </div>
                <div style="font-family: monospace; font-weight: 800; font-size: 1rem; color: var(--text-main); margin-bottom: 4px;">
                    {{ $booking->e_ticket_code ?? 'SH-'.str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
                </div>
                <div style="font-size: 0.72rem; color: var(--text-muted); margin-bottom: 20px;">
                    Verifikasi Staf Lapangan
                </div>

                <div style="display: flex; flex-direction: column; gap: 10px; width: 100%;">
                    @if($booking->status === 'PAID')
                        <form method="POST" action="{{ route('venueowner.bookings.check-in', $booking) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;" onclick="return confirm('Konfirmasi check-in pemain di lapangan sekarang?')">
                                ✓ Lakukan Check-In
                            </button>
                        </form>
                    @elseif($booking->status === 'PENDING')
                        <form method="POST" action="{{ route('venueowner.bookings.confirm-payment', $booking) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;" onclick="return confirm('Konfirmasi pembayaran tunai/lunas sekarang?')">
                                Konfirmasi Bayar Lunas
                            </button>
                        </form>
                    @endif

                    @if($booking->status !== 'COMPLETED' && $booking->status !== 'CANCELLED')
                        <form method="POST" action="{{ route('venueowner.bookings.cancel', $booking) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pemesanan ini?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger" style="width: 100%; justify-content: center; font-size: 0.8rem;">
                                Batalkan Reservasi
                            </button>
                        </form>
                    @endif

                    <button type="button" onclick="window.print()" class="btn btn-secondary" style="width: 100%; justify-content: center; font-size: 0.8rem;">
                        Cetak E-Tiket
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

