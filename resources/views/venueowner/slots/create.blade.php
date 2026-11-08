@extends('layouts.venueowner')

@section('title', 'Buat & Generate Slot Waktu')
@section('page_title', 'Buat Slot Waktu')

@push('styles')
<style>
    .tabs-nav {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 2px;
    }

    .tab-btn {
        padding: 10px 18px;
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-muted);
        background: none;
        border: none;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .tab-btn.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }

    .form-group { margin-bottom: 18px; }
    .form-label { display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 6px; color: var(--text-main); }
    .form-control { width: 100%; padding: 10px 14px; font-size: 0.9rem; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; background-color: #ffffff; }
    .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15); }
    .form-hint { font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
    @media (max-width: 640px) { .form-row { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<div style="max-width: 750px; margin: 0 auto;">
    <div style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800;">Atur Ketersediaan Waktu & Tarif Lapangan</h2>
            <p style="font-size: 0.82rem; color: var(--text-muted);">Pilih metode otomatis generator massal harian atau tambah slot tunggal</p>
        </div>
        <a href="{{ route('venueowner.slots.index', ['court_id' => $selected_court_id, 'date' => $selected_date]) }}" class="btn btn-secondary btn-sm">Kembali ke Daftar</a>
    </div>

    <!-- Tab navigation -->
    <div class="tabs-nav">
        <button type="button" class="tab-btn active" id="btn-tab-bulk" onclick="switchTab('bulk')">
            ⚡ Generator Massal Harian (Direkomendasikan)
        </button>
        <button type="button" class="tab-btn" id="btn-tab-single" onclick="switchTab('single')">
            ➕ Tambah Slot Tunggal
        </button>
    </div>

    <!-- FORM 1: Generator Massal (Bulk) -->
    <div id="tab-bulk" class="card">
        <div style="margin-bottom: 16px; padding: 12px 16px; background-color: #ecfdf5; border-radius: 8px; border: 1px solid #a7f3d0; font-size: 0.82rem; color: #065f46;">
            💡 <strong>Generator Slot Cepat:</strong> Masukkan jam buka dan jam tutup arena Anda (misal 08:00 - 22:00) dengan durasi 60 menit per slot. Sistem secara otomatis akan membagi dan membuat seluruh slot waktu harian tanpa perlu input manual satu per satu!
        </div>

        <form method="POST" action="{{ route('venueowner.slots.bulk') }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Pilih Lapangan Olahraga <span style="color:#ef4444;">*</span></label>
                    <select name="court_id" class="form-control" required>
                        <option value="">-- Pilih Lapangan --</option>
                        @foreach($courts as $court)
                            <option value="{{ $court->id }}" {{ (old('court_id', $selected_court_id) == $court->id) ? 'selected' : '' }}>
                                {{ $court->venue->name }} — {{ $court->name }} ({{ $court->sport->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Pilih Tanggal Jadwal <span style="color:#ef4444;">*</span></label>
                    <input type="date" name="date" class="form-control" value="{{ old('date', $selected_date) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Jam Buka Arena <span style="color:#ef4444;">*</span></label>
                    <input type="time" name="open_time" class="form-control" value="{{ old('open_time', '08:00') }}" required>
                    <div class="form-hint">Awal sesi pertama dimulai.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Jam Tutup Arena <span style="color:#ef4444;">*</span></label>
                    <input type="time" name="close_time" class="form-control" value="{{ old('close_time', '22:00') }}" required>
                    <div class="form-hint">Batas akhir sesi malam.</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Durasi Setiap Slot (Menit) <span style="color:#ef4444;">*</span></label>
                    <select name="slot_duration_minutes" class="form-control" required>
                        <option value="60" {{ old('slot_duration_minutes') == '60' ? 'selected' : '' }}>60 Menit (1 Jam / Standar)</option>
                        <option value="120" {{ old('slot_duration_minutes') == '120' ? 'selected' : '' }}>120 Menit (2 Jam)</option>
                        <option value="90" {{ old('slot_duration_minutes') == '90' ? 'selected' : '' }}>90 Menit (1.5 Jam)</option>
                        <option value="30" {{ old('slot_duration_minutes') == '30' ? 'selected' : '' }}>30 Menit</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Tarif Sewa per Slot (Rp) <span style="color:#ef4444;">*</span></label>
                    <input type="number" step="1000" name="price" class="form-control" placeholder="Contoh: 150000" value="{{ old('price', 150000) }}" required>
                    <div class="form-hint">Harga sewa standar untuk slot yang dibuat.</div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 16px;">
                <a href="{{ route('venueowner.slots.index', ['court_id' => $selected_court_id, 'date' => $selected_date]) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">⚡ Generate Slot Waktu Sekarang</button>
            </div>
        </form>
    </div>

    <!-- FORM 2: Single Slot Creation -->
    <div id="tab-single" class="card" style="display: none;">
        <form method="POST" action="{{ route('venueowner.slots.store') }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Pilih Lapangan Olahraga <span style="color:#ef4444;">*</span></label>
                    <select name="court_id" class="form-control" required>
                        <option value="">-- Pilih Lapangan --</option>
                        @foreach($courts as $court)
                            <option value="{{ $court->id }}" {{ (old('court_id', $selected_court_id) == $court->id) ? 'selected' : '' }}>
                                {{ $court->venue->name }} — {{ $court->name }} ({{ $court->sport->name ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Jadwal <span style="color:#ef4444;">*</span></label>
                    <input type="date" name="date" class="form-control" value="{{ old('date', $selected_date) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Jam Mulai <span style="color:#ef4444;">*</span></label>
                    <input type="time" name="start_time" class="form-control" value="{{ old('start_time', '18:00') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Jam Selesai <span style="color:#ef4444;">*</span></label>
                    <input type="time" name="end_time" class="form-control" value="{{ old('end_time', '19:00') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tarif Sewa (Rp) <span style="color:#ef4444;">*</span></label>
                    <input type="number" step="1000" name="price" class="form-control" placeholder="150000" value="{{ old('price', 150000) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Status Awal Slot <span style="color:#ef4444;">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="AVAILABLE" selected>AVAILABLE (Bisa langsung dipesan)</option>
                        <option value="OFF">OFF (Tutup sementara / maintenance)</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 16px;">
                <a href="{{ route('venueowner.slots.index', ['court_id' => $selected_court_id, 'date' => $selected_date]) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Slot Tunggal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchTab(type) {
        const tabBulk = document.getElementById('tab-bulk');
        const tabSingle = document.getElementById('tab-single');
        const btnBulk = document.getElementById('btn-tab-bulk');
        const btnSingle = document.getElementById('btn-tab-single');

        if (type === 'bulk') {
            tabBulk.style.display = 'block';
            tabSingle.style.display = 'none';
            btnBulk.classList.add('active');
            btnSingle.classList.remove('active');
        } else {
            tabBulk.style.display = 'none';
            tabSingle.style.display = 'block';
            btnBulk.classList.remove('active');
            btnSingle.classList.add('active');
        }
    }
</script>

@endsection

