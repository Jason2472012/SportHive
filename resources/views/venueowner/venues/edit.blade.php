@extends('layouts.venueowner')

@section('title', 'Edit Profil Arena')
@section('page_title', 'Edit Profil Arena')

@push('styles')
<style>
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 6px; color: var(--text-main); }
    .form-control { width: 100%; padding: 10px 14px; font-size: 0.9rem; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; background-color: #ffffff; }
    .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15); }
    .form-hint { font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 640px) { .form-row { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800;">Edit Profil: {{ $venue->name }}</h2>
            <p style="font-size: 0.82rem; color: var(--text-muted);">Perbarui informasi arena dan fasilitas operasional</p>
        </div>
        <a href="{{ route('venueowner.venues.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('venueowner.venues.update', $venue) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Arena / Venue <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $venue->name) }}" required>
                    @error('name') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Kota Wilayah <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $venue->city) }}" required>
                    @error('city') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Lengkap Arena <span style="color:#ef4444;">*</span></label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $venue->address) }}" required>
                @error('address') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
            </div>

            @if($venue->photo_url)
                <div style="margin-bottom: 16px;">
                    <label class="form-label">Foto Fasilitas Saat Ini:</label>
                    <img src="{{ $venue->photo_url }}" alt="{{ $venue->name }}" style="width: 140px; height: 90px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
                </div>
            @endif

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Ganti Foto Fasilitas (File)</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    <div class="form-hint">Biarkan kosong jika tidak ingin mengubah foto.</div>
                    @error('photo') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Atau Tautan URL Foto</label>
                    <input type="url" name="photo_url" class="form-control" value="{{ old('photo_url', $venue->photo_url) }}">
                    @error('photo_url') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi Fasilitas</label>
                <textarea name="description" rows="4" class="form-control">{{ old('description', $venue->description) }}</textarea>
                @error('description') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Latitude Koordinat</label>
                    <input type="number" step="any" name="latitude" class="form-control" value="{{ old('latitude', $venue->latitude) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Longitude Koordinat</label>
                    <input type="number" step="any" name="longitude" class="form-control" value="{{ old('longitude', $venue->longitude) }}">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 12px;">
                <a href="{{ route('venueowner.venues.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@endsection

