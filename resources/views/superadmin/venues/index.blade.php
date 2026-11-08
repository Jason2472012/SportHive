@extends('layouts.superadmin')

@section('title', 'Kelola Venue & Validasi Mitra')
@section('page_title', 'Kelola Venue & Validasi Mitra')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h2>Kelola Venue & Validasi Pendaftaran Mitra</h2>
            <p>Periksa pengajuan kemitraan venue baru, setujui (Approve) atau tolak dengan catatan evaluasi (Reject) disertai notifikasi otomatis ke pemilik venue.</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="openModal('modalCreateVenue')">
            + Tambah Data Venue
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <p>Total Venue</p>
                <div class="stat-number">{{ $summary['total'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Menunggu Validasi (Pending)</p>
                <div class="stat-number">{{ $summary['pending'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-amber">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Disetujui (Approved)</p>
                <div class="stat-number">{{ $summary['approved'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Ditolak (Rejected)</p>
                <div class="stat-number">{{ $summary['rejected'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-rose">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Filter & Table -->
    <div class="panel">
        <div class="panel-header">
            <h3>Daftar Pengajuan & Data Mitra Venue</h3>
            <form method="GET" action="{{ route('superadmin.venues.index') }}" class="filter-bar">
                <input type="text" name="search" class="form-control" style="width: 230px;" placeholder="Cari nama venue / kota / mitra..." value="{{ request('search') }}">
                <select name="validation_status" class="form-select" style="width: 180px;">
                    <option value="">Semua Status Validasi</option>
                    <option value="PENDING" {{ request('validation_status') === 'PENDING' ? 'selected' : '' }}>PENDING (Menunggu)</option>
                    <option value="APPROVED" {{ request('validation_status') === 'APPROVED' ? 'selected' : '' }}>APPROVED (Disetujui)</option>
                    <option value="REJECTED" {{ request('validation_status') === 'REJECTED' ? 'selected' : '' }}>REJECTED (Ditolak)</option>
                </select>
                <button type="submit" class="btn btn-outline">Filter</button>
                @if(request()->hasAny(['search', 'validation_status']))
                    <a href="{{ route('superadmin.venues.index') }}" class="btn btn-outline">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Informasi Venue & Alamat</th>
                        <th>Pemilik Mitra (Owner)</th>
                        <th>Lapangan (Courts)</th>
                        <th>Status Validasi</th>
                        <th>Diverifikasi Oleh</th>
                        <th>Aksi Validasi & Kelola</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($venues as $venue)
                        <tr>
                            <td>#{{ $venue->id }}</td>
                            <td style="max-width: 280px;">
                                <div><strong>{{ $venue->name }}</strong></div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $venue->city ?? '-' }} · {{ $venue->address }}</div>
                                @if($venue->description)
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 3px;">{{ \Illuminate\Support\Str::limit($venue->description, 80) }}</div>
                                @endif
                            </td>
                            <td>
                                <div><strong>{{ $venue->owner->name ?? '-' }}</strong></div>
                                <div style="font-size: 0.76rem; color: var(--text-muted);">{{ $venue->owner->email ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $venue->courts->count() }} Lapangan</span>
                                @if($venue->courts->isNotEmpty())
                                    <div style="font-size: 0.74rem; color: var(--text-muted); margin-top: 4px;">
                                        {{ $venue->courts->pluck('sport.name')->filter()->unique()->implode(', ') }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($venue->validation_status === 'APPROVED')
                                    <span class="badge badge-success">APPROVED</span>
                                @elseif($venue->validation_status === 'PENDING')
                                    <span class="badge badge-warning">PENDING</span>
                                @else
                                    <span class="badge badge-danger">REJECTED</span>
                                @endif
                            </td>
                            <td>
                                @if($venue->validator)
                                    <div style="font-size: 0.8rem; font-weight: 600;">{{ $venue->validator->name }}</div>
                                    <div style="font-size: 0.73rem; color: var(--text-muted);">
                                        {{ $venue->validated_at ? $venue->validated_at->format('d M Y H:i') : '-' }}
                                    </div>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.78rem;">Belum diverifikasi</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-group">
                                    @if($venue->validation_status !== 'APPROVED')
                                        <form method="POST" action="{{ route('superadmin.venues.approve', $venue) }}" onsubmit="return confirm('Setujui pendaftaran mitra untuk venue {{ addslashes($venue->name) }}?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-primary">Setujui</button>
                                        </form>
                                    @endif

                                    @if($venue->validation_status !== 'REJECTED')
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-warning"
                                            onclick="openRejectVenueModal({{ $venue->id }}, @js($venue->name))"
                                        >
                                            Tolak
                                        </button>
                                    @endif

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline"
                                        onclick="openEditVenueModal({{ $venue->id }}, {{ $venue->owner_id }}, @js($venue->name), @js($venue->city ?? ''), @js($venue->address), @js($venue->description ?? ''), @js($venue->photo_url ?? ''))"
                                    >
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('superadmin.venues.destroy', $venue) }}" onsubmit="return confirm('Hapus data venue ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 28px; color: var(--text-muted);">
                                Belum ada data venue atau pengajuan mitra.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($venues->hasPages())
            <div class="pagination-wrapper">
                <div>Menampilkan {{ $venues->firstItem() }}–{{ $venues->lastItem() }} dari {{ $venues->total() }} venue</div>
                <div class="pagination-links">{{ $venues->links() }}</div>
            </div>
        @endif
    </div>

    <!-- Modal Tolak Pendaftaran Mitra dengan Catatan (Sesuai Activity Diagram) -->
    <div id="modalRejectVenue" class="modal-backdrop">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Tolak Pendaftaran Mitra Venue</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalRejectVenue')">&times;</button>
            </div>
            <form id="formRejectVenue" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p style="font-size: 0.86rem; color: var(--text-muted); margin-bottom: 14px;">
                        Anda akan menolak pengajuan mitra untuk venue <strong id="reject_venue_name" style="color: var(--text-main);"></strong>. Catatan penolakan di bawah ini akan dikirimkan langsung ke notifikasi akun Venue Owner.
                    </p>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Catatan Alasan Penolakan *</label>
                        <textarea name="rejection_note" class="form-control" rows="4" required placeholder="Contoh: Berkas legalitas usaha dan foto fasilitas lapangan belum jelas/lengkap. Mohon perbarui dokumen dan ajukan kembali."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('modalRejectVenue')">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak & Kirim Notifikasi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Venue -->
    <div id="modalCreateVenue" class="modal-backdrop">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Tambah Data Venue Baru</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalCreateVenue')">&times;</button>
            </div>
            <form method="POST" action="{{ route('superadmin.venues.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Pemilik Venue (Role: VENUE_OWNER) *</label>
                        <select name="owner_id" class="form-select" required>
                            <option value="">-- Pilih Pemilik Venue --</option>
                            @foreach($venueOwners as $owner)
                                <option value="{{ $owner->id }}">{{ $owner->name }} ({{ $owner->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Venue / Arena *</label>
                        <input type="text" name="name" class="form-control" required maxlength="150" placeholder="Contoh: GOR SportHive Arena Bandung">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kota *</label>
                        <input type="text" name="city" class="form-control" required maxlength="100" placeholder="Bandung">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat Lengkap *</label>
                        <input type="text" name="address" class="form-control" required maxlength="255" placeholder="Jl. Surya Sumantri No. 65">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Deskripsi Fasilitas & Legalitas</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi fasilitas lapangan, parkir, jam operasional, nomor izin usaha..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">URL Foto / Berkas</label>
                        <input type="text" name="photo_url" class="form-control" maxlength="255" placeholder="https://example.com/venue.jpg">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Status Validasi Awal *</label>
                        <select name="validation_status" class="form-select" required>
                            <option value="APPROVED">APPROVED (Langsung Disetujui)</option>
                            <option value="PENDING">PENDING (Menunggu Validasi)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('modalCreateVenue')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Venue</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Venue -->
    <div id="modalEditVenue" class="modal-backdrop">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Edit Profil Venue</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalEditVenue')">&times;</button>
            </div>
            <form id="formEditVenue" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Pemilik Venue *</label>
                        <select id="edit_venue_owner_id" name="owner_id" class="form-select" required>
                            @foreach($venueOwners as $owner)
                                <option value="{{ $owner->id }}">{{ $owner->name }} ({{ $owner->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Venue / Arena *</label>
                        <input type="text" id="edit_venue_name" name="name" class="form-control" required maxlength="150">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kota *</label>
                        <input type="text" id="edit_venue_city" name="city" class="form-control" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat Lengkap *</label>
                        <input type="text" id="edit_venue_address" name="address" class="form-control" required maxlength="255">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea id="edit_venue_description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">URL Foto</label>
                        <input type="text" id="edit_venue_photo_url" name="photo_url" class="form-control" maxlength="255">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('modalEditVenue')">Batal</button>
                    <button type="submit" class="btn btn-primary">Perbarui Venue</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openRejectVenueModal(id, name) {
        const form = document.getElementById('formRejectVenue');
        form.action = `/superadmin/venues/${id}/reject`;
        document.getElementById('reject_venue_name').textContent = name;
        openModal('modalRejectVenue');
    }

    function openEditVenueModal(id, ownerId, name, city, address, description, photoUrl) {
        const form = document.getElementById('formEditVenue');
        form.action = `/superadmin/venues/${id}`;
        document.getElementById('edit_venue_owner_id').value = ownerId;
        document.getElementById('edit_venue_name').value = name;
        document.getElementById('edit_venue_city').value = city;
        document.getElementById('edit_venue_address').value = address;
        document.getElementById('edit_venue_description').value = description;
        document.getElementById('edit_venue_photo_url').value = photoUrl;
        openModal('modalEditVenue');
    }
</script>
@endpush

