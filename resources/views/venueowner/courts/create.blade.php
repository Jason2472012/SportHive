@extends('layouts.venueowner')

@section('title', 'Tambah Lapangan Baru')
@section('page_title', 'Tambah Lapangan')

@push('styles')
<style>
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 6px; color: var(--text-main); }
    .form-control { width: 100%; padding: 10px 14px; font-size: 0.9rem; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; background-color: #ffffff; }
    .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15); }
    .form-hint { font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; }
</style>
@endpush

@section('content')

<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800;">Tambah Lapangan Olahraga</h2>
            <p style="font-size: 0.82rem; color: var(--text-muted);">Pilih arena, jenis cabang olahraga, dan nama sarana lapangan</p>
        </div>
        <a href="{{ route('venueowner.courts.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('venueowner.courts.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Pilih Arena / Venue <span style="color:#ef4444;">*</span></label>
                <select name="venue_id" class="form-control" required>
                    <option value="">-- Pilih Arena --</option>
                    @foreach($venues as $venue)
                        <option value="{{ $venue->id }}" {{ (old('venue_id', $selected_venue) == $venue->id) ? 'selected' : '' }}>
                            {{ $venue->name }} ({{ $venue->city }})
                        </option>
                    @endforeach
                </select>
                @error('venue_id') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Cabang Olahraga <span style="color:#ef4444;">*</span></label>
                <select name="sport_id" class="form-control" required>
                    <option value="">-- Pilih Cabang Olahraga --</option>
                    @foreach($sports as $sport)
                        <option value="{{ $sport->id }}" {{ old('sport_id') == $sport->id ? 'selected' : '' }}>
                            ⚽ {{ $sport->name }}
                        </option>
                    @endforeach
                </select>
                @error('sport_id') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nama Lapangan <span style="color:#ef4444;">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Lapangan Futsal Rumput Sintetis A, Lapangan Badminton 2" value="{{ old('name') }}" required>
                <div class="form-hint">Nama lapangan harus unik di dalam satu arena.</div>
                @error('name') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.88rem; font-weight: 600;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 16px; height: 16px;">
                    Aktifkan lapangan ini langsung (Siap menerima reservasi slot)
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <a href="{{ route('venueowner.courts.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Lapangan</button>
            </div>
        </form>
    </div>
</div>

@endsection

