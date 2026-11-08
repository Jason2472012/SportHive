@extends('layouts.venueowner')

@section('title', 'Daftarkan Arena Baru')
@section('page_title', 'Daftarkan Arena Baru')

@push('styles')
<style>
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 6px;
        color: var(--text-main);
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        font-size: 0.9rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-family: inherit;
        background-color: #ffffff;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
    }

    .form-hint {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 640px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800;">Daftarkan Arena / Venue Baru</h2>
            <p style="font-size: 0.82rem; color: var(--text-muted);">Lengkapi data profil arena dan fasilitas untuk diajukan ke Super Admin</p>
        </div>
        <a href="{{ route('venueowner.venues.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('venueowner.venues.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Arena / Venue <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: GOR Bintang Sport Center" value="{{ old('name') }}" required>
                    @error('name') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Kota Wilayah <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="city" class="form-control" placeholder="Contoh: Bandung, Jakarta Selatan" value="{{ old('city') }}" required>
                    @error('city') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Lengkap Arena <span style="color:#ef4444;">*</span></label>
                <input type="text" name="address" class="form-control" placeholder="Nama jalan, nomor, kelurahan, kecamatan" value="{{ old('address') }}" required>
                @error('address') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Unggah Foto Fasilitas Arena (File)</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    <div class="form-hint">Format JPG/PNG/WEBP, maksimal 3MB.</div>
                    @error('photo') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Atau Masukkan Tautan URL Foto</label>
                    <input type="url" name="photo_url" class="form-control" placeholder="https://..." value="{{ old('photo_url') }}">
                    <div class="form-hint">Alternatif jika menggunakan gambar hosting online.</div>
                    @error('photo_url') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi & Fasilitas Pendukung</label>
                <textarea name="description" rows="4" class="form-control" placeholder="Jelaskan fasilitas yang ada (misal: ruang ganti ber-AC, shower air hangat, kantin, loker gratis, parkir mobil luas)...">{{ old('description') }}</textarea>
                @error('description') <div style="color: #ef4444; font-size: 0.78rem; margin-top: 4px;">{{ $message }}</div> @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Latitude Koordinat (Opsional)</label>
                    <input type="number" step="any" name="latitude" class="form-control" placeholder="-6.917464" value="{{ old('latitude') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Longitude Koordinat (Opsional)</label>
                    <input type="number" step="any" name="longitude" class="form-control" placeholder="107.619123" value="{{ old('longitude') }}">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 12px;">
                <a href="{{ route('venueowner.venues.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan & Ajukan Verifikasi</button>
            </div>
        </form>
    </div>
</div>

@endsection

