@extends('layouts.venueowner')

@section('title', 'Edit Lapangan')
@section('page_title', 'Edit Lapangan')

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
            <h2 style="font-size: 1.25rem; font-weight: 800;">Edit Lapangan: {{ $court->name }}</h2>
            <p style="font-size: 0.82rem; color: var(--text-muted);">Arena: {{ $court->venue->name }} ({{ $court->venue->city }})</p>
        </div>
        <a href="{{ route('venueowner.courts.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('venueowner.courts.update', $court) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Cabang Olahraga <span style="color:#ef4444;">*</span></label>
                <select name="sport_id" class="form-control" required>
                    @foreach($sports as $sport)
                        <option value="{{ $sport->id }}" {{ old('sport_id', $court->sport_id) == $sport->id ? 'selected' : '' }}>
                            ⚽ {{ $sport->name }}
                        </option>
                    @endforeach
                </select>
                @error('sport_id') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nama Lapangan <span style="color:#ef4444;">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $court->name) }}" required>
                @error('name') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Status Operasional Lapangan <span style="color:#ef4444;">*</span></label>
                <select name="is_active" class="form-control" required>
                    <option value="1" {{ old('is_active', $court->is_active) ? 'selected' : '' }}>AKTIF (Dapat dijadwalkan dan dipesan)</option>
                    <option value="0" {{ ! old('is_active', $court->is_active) ? 'selected' : '' }}>NONAKTIF (Sedang perbaikan / tutup)</option>
                </select>
                @error('is_active') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <a href="{{ route('venueowner.courts.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@endsection

