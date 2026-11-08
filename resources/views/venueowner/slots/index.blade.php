@extends('layouts.venueowner')

@section('title', 'Slot Waktu & Penetapan Harga Sewa')
@section('page_title', 'Jadwal Slot Waktu & Harga')

@push('styles')
<style>
    .filter-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
    }

    .slots-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }

    .slot-item {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 18px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 12px;
        transition: all 0.15s ease;
    }

    .slot-item.available {
        border-left: 4px solid #10b981;
        background-color: #ffffff;
    }

    .slot-item.booked {
        border-left: 4px solid #0284c7;
        background-color: #f8fafc;
    }

    .slot-item.off {
        border-left: 4px solid #94a3b8;
        background-color: #f1f5f9;
        opacity: 0.85;
    }

    .slot-item.held {
        border-left: 4px solid #f59e0b;
        background-color: #fffbeb;
    }

    .slot-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .slot-time {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--text-main);
    }

    .slot-price {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--primary);
    }

    .slot-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        justify-content: flex-end;
        padding-top: 10px;
        border-top: 1px dashed var(--border-color);
    }
</style>
@endpush

@section('content')

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h2 style="font-size: 1.3rem; font-weight: 800;">⏱ Pengaturan Slot Waktu & Tarif Lapangan</h2>
        <p style="font-size: 0.85rem; color: var(--text-muted);">Buka, tutup, ubah harga, atau nonaktifkan jadwal lapangan secara real-time</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('venueowner.slots.create', ['court_id' => $selected_court_id, 'date' => $selected_date]) }}" class="btn btn-primary">
            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            + Buat / Generate Slot Waktu
        </a>
    </div>
</div>

<!-- Filter Bar: Court & Date -->
<div class="filter-card">
    <form method="GET" action="{{ route('venueowner.slots.index') }}" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 240px;">
            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">Pilih Lapangan</label>
            <select name="court_id" class="btn btn-secondary" style="width: 100%; text-align: left;" onchange="this.form.submit()">
                @foreach($courts as $court)
                    <option value="{{ $court->id }}" {{ $selected_court_id == $court->id ? 'selected' : '' }}>
                        {{ $court->venue->name }} — {{ $court->name }} ({{ $court->sport->name ?? 'Sport' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div style="min-width: 160px;">
            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">Pilih Tanggal</label>
            <input type="date" name="date" value="{{ $selected_date }}" class="btn btn-secondary" style="width: 100%;" onchange="this.form.submit()">
        </div>

        <div style="min-width: 150px;">
            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">Filter Status</label>
            <select name="status" class="btn btn-secondary" style="width: 100%; text-align: left;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="AVAILABLE" {{ $selected_status === 'AVAILABLE' ? 'selected' : '' }}>Hanya Tersedia</option>
                <option value="BOOKED" {{ $selected_status === 'BOOKED' ? 'selected' : '' }}>Hanya Terpesan</option>
                <option value="OFF" {{ $selected_status === 'OFF' ? 'selected' : '' }}>Hanya Dinonaktifkan (OFF)</option>
            </select>
        </div>

        @if($selected_status)
            <div>
                <a href="{{ route('venueowner.slots.index', ['court_id' => $selected_court_id, 'date' => $selected_date]) }}" class="btn btn-secondary" style="font-size: 0.8rem;">Reset</a>
            </div>
        @endif
    </form>

    <!-- Ringkasan Cepat Slot Hari yang Dipilih -->
    <div style="display: flex; gap: 18px; margin-top: 16px; padding-top: 14px; border-top: 1px solid var(--border-color); font-size: 0.82rem;">
        <div>Total: <strong style="color: var(--text-main);">{{ $slot_summary['total'] }} Slot</strong></div>
        <div>Tersedia: <strong style="color: #059669;">{{ $slot_summary['available'] }} Slot</strong></div>
        <div>Terpesan: <strong style="color: #0284c7;">{{ $slot_summary['booked'] }} Slot</strong></div>
        <div>Dinonaktifkan: <strong style="color: #64748b;">{{ $slot_summary['off'] }} Slot</strong></div>
    </div>
</div>

<!-- Slots List Grid -->
@if($slots->count() > 0)
    <div class="slots-container">
        @foreach($slots as $slot)
            <div class="slot-item {{ strtolower($slot->status) }}">
                <div>
                    <div class="slot-header">
                        <span class="slot-time">{{ $slot->start_at->format('H:i') }} - {{ $slot->end_at->format('H:i') }}</span>
                        @if($slot->status === 'AVAILABLE')
                            <span class="badge-pill badge-success">TERSEDIA</span>
                        @elseif($slot->status === 'BOOKED')
                            <span class="badge-pill badge-info">TERPESAN</span>
                        @elseif($slot->status === 'HELD')
                            <span class="badge-pill badge-warning">HELD</span>
                        @else
                            <span class="badge-pill" style="background:#e2e8f0; color:#475569;">OFF (NONAKTIF)</span>
                        @endif
                    </div>

                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 4px;">
                        {{ $slot->court->name }} ({{ $slot->court->venue->name }})
                    </div>

                    <div class="slot-price" style="margin-top: 8px;">
                        Rp {{ number_format($slot->price, 0, ',', '.') }}
                        <span style="font-size: 0.75rem; font-weight: normal; color: var(--text-muted);">/ slot</span>
                    </div>

                    @if($slot->status === 'BOOKED' && $slot->bookings->isNotEmpty())
                        @php $booking = $slot->bookings->first(); @endphp
                        <div style="margin-top: 8px; padding: 6px 10px; background-color: #e0f2fe; border-radius: 6px; font-size: 0.75rem; color: #0369a1;">
                            Pemesan: <strong>{{ $booking->user->name ?? '-' }}</strong> (Tiket: {{ $booking->e_ticket_code ?? '-' }})
                        </div>
                    @endif
                </div>

                <div class="slot-actions">
                    @if($slot->status !== 'BOOKED')
                        <!-- Toggle Real-time status -->
                        <form method="POST" action="{{ route('venueowner.slots.toggle-status', $slot) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-secondary btn-sm" title="Ubah status real-time">
                                @if($slot->status === 'AVAILABLE')
                                    Tutup (OFF)
                                @else
                                    Buka Slot
                                @endif
                            </button>
                        </form>

                        <!-- Quick edit price modal trigger / form -->
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('edit-slot-{{ $slot->id }}').style.display='flex'">
                            Ubah Tarif
                        </button>

                        <!-- Delete Slot -->
                        <form method="POST" action="{{ route('venueowner.slots.destroy', $slot) }}" onsubmit="return confirm('Hapus slot jam ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 8px;">Hapus</button>
                        </form>

                        <!-- Edit Modal -->
                        <div id="edit-slot-{{ $slot->id }}" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center;">
                            <div style="background:#fff; border-radius:12px; width:90%; max-width:400px; padding:24px; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
                                <h4 style="font-size:1rem; font-weight:700; margin-bottom:6px;">Ubah Tarif Slot Jam</h4>
                                <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom:16px;">{{ $slot->start_at->format('H:i') }} - {{ $slot->end_at->format('H:i') }} ({{ $slot->court->name }})</p>
                                
                                <form method="POST" action="{{ route('venueowner.slots.update', $slot) }}">
                                    @csrf
                                    @method('PUT')
                                    <div style="margin-bottom:16px;">
                                        <label style="display:block; font-size:0.8rem; font-weight:700; margin-bottom:4px;">Harga Sewa (Rp)</label>
                                        <input type="number" name="price" class="btn btn-secondary" style="width:100%; text-align:left;" value="{{ (int)$slot->price }}" required>
                                    </div>
                                    <div style="margin-bottom:20px;">
                                        <label style="display:block; font-size:0.8rem; font-weight:700; margin-bottom:4px;">Status Ketersediaan</label>
                                        <select name="status" class="btn btn-secondary" style="width:100%; text-align:left;">
                                            <option value="AVAILABLE" {{ $slot->status === 'AVAILABLE' ? 'selected' : '' }}>AVAILABLE (Bisa dipesan)</option>
                                            <option value="OFF" {{ $slot->status === 'OFF' ? 'selected' : '' }}>OFF (Nonaktifkan)</option>
                                        </select>
                                    </div>
                                    <div style="display:flex; justify-content:flex-end; gap:8px;">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('edit-slot-{{ $slot->id }}').style.display='none'">Batal</button>
                                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <span style="font-size: 0.75rem; color: var(--text-muted); font-style: italic;">
                            Terkunci (Sudah Terpesan)
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="card" style="text-align: center; padding: 48px;">
        <div style="font-size: 2.5rem; margin-bottom: 12px;">📅</div>
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 6px;">Belum Ada Slot Waktu untuk Tanggal Ini</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">
            Gunakan Generator Slot Massal untuk membuat jadwal slot otomatis per jam sekaligus dari jam buka hingga jam tutup.
        </p>
        <a href="{{ route('venueowner.slots.create', ['court_id' => $selected_court_id, 'date' => $selected_date]) }}" class="btn btn-primary">
            + Buka Slot Sekarang dengan Generator Massal
        </a>
    </div>
@endif

@endsection

