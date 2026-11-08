@extends('layouts.superadmin')

@section('title', 'Kelola Pengguna (CRUD User)')
@section('page_title', 'Kelola Pengguna')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h2>Kelola Pengguna (CRUD User & Suspend)</h2>
            <p>Lihat daftar pengguna, tambah akun baru, pantau status mitra venue, serta suspend atau aktifkan akun pengguna.</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="openModal('modalCreateUser')">
            + Tambah Pengguna Baru
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <p>Total Pengguna</p>
                <div class="stat-number">{{ $summary['total'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Pemain (Player / Organizer)</p>
                <div class="stat-number">{{ $summary['players'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Mitra Venue Owner</p>
                <div class="stat-number">{{ $summary['venue_owners'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-amber">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Akun Di-suspend</p>
                <div class="stat-number">{{ $summary['suspended'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-rose">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
        </div>
    </div>

    <!-- Filter & User Table -->
    <div class="panel">
        <div class="panel-header">
            <h3>Daftar Pengguna Sistem</h3>
            <form method="GET" action="{{ route('superadmin.users.index') }}" class="filter-bar">
                <input type="text" name="search" class="form-control" style="width: 220px;" placeholder="Cari nama / email / telp..." value="{{ request('search') }}">
                <select name="role" class="form-select" style="width: 165px;">
                    <option value="">Semua Role</option>
                    <option value="PLAYER" {{ request('role') === 'PLAYER' ? 'selected' : '' }}>PLAYER</option>
                    <option value="VENUE_OWNER" {{ request('role') === 'VENUE_OWNER' ? 'selected' : '' }}>VENUE OWNER</option>
                    <option value="SUPER_ADMIN" {{ request('role') === 'SUPER_ADMIN' ? 'selected' : '' }}>SUPER ADMIN</option>
                </select>
                <select name="status" class="form-select" style="width: 150px;">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                <button type="submit" class="btn btn-outline">Filter</button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama & Kontak</th>
                        <th>Role</th>
                        <th>Venue Mitra (Khusus Owner)</th>
                        <th>Rating Pemain</th>
                        <th>Status Akun</th>
                        <th>Bergabung</th>
                        <th>Aksi (CRUD & Suspend)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>#{{ $user->id }}</td>
                            <td>
                                <div><strong>{{ $user->name }}</strong></div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $user->email }}</div>
                                @if($user->phone)
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">Telp: {{ $user->phone }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $user->role === 'SUPER_ADMIN' ? 'badge-admin' : ($user->role === 'VENUE_OWNER' ? 'badge-owner' : 'badge-player') }}">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td>
                                @if($user->role === 'VENUE_OWNER')
                                    @forelse($user->venues as $venue)
                                        <div style="margin-bottom: 4px;">
                                            <a href="{{ route('superadmin.venues.index', ['search' => $venue->name]) }}" style="text-decoration: none; color: var(--text-main); font-weight: 600; font-size: 0.8rem;">
                                                {{ $venue->name }}
                                            </a>
                                            @if($venue->validation_status === 'APPROVED')
                                                <span class="badge badge-success">APPROVED</span>
                                            @elseif($venue->validation_status === 'PENDING')
                                                <span class="badge badge-warning">PENDING</span>
                                            @else
                                                <span class="badge badge-danger">REJECTED</span>
                                            @endif
                                        </div>
                                    @empty
                                        <span style="font-size: 0.78rem; color: var(--text-muted);">Belum mengajukan venue</span>
                                    @endforelse
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.78rem;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($user->rating_count > 0)
                                    <strong>★ {{ number_format((float) $user->rating_avg, 2) }}</strong>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">({{ $user->rating_count }} ulasan)</span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.78rem;">Belum ada rating</span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Suspended</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</td>
                            <td>
                                <div class="action-group">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline"
                                        onclick="openEditUserModal({{ $user->id }}, @js($user->name), @js($user->email), @js($user->phone ?? ''), @js($user->role))"
                                    >
                                        Edit
                                    </button>

                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('superadmin.users.toggle-suspend', $user) }}" onsubmit="return confirm('Ubah status aktif/suspend untuk pengguna ini?');">
                                            @csrf
                                            @method('PATCH')
                                            @if($user->is_active)
                                                <button type="submit" class="btn btn-sm btn-warning">Suspend</button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-primary">Aktifkan</button>
                                            @endif
                                        </form>

                                        <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna ini secara permanen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    @else
                                        <span class="badge badge-neutral">Akun Anda</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 28px; color: var(--text-muted);">
                                Tidak ada data pengguna yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="pagination-wrapper">
                <div>Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} pengguna</div>
                <div class="pagination-links">{{ $users->links() }}</div>
            </div>
        @endif
    </div>

    <!-- Modal Tambah User -->
    <div id="modalCreateUser" class="modal-backdrop">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Tambah Pengguna Baru</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalCreateUser')">&times;</button>
            </div>
            <form method="POST" action="{{ route('superadmin.users.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="name" class="form-control" required maxlength="100" placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat Email *</label>
                        <input type="email" name="email" class="form-control" required maxlength="150" placeholder="budi@sporthive.id">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control" maxlength="20" placeholder="081234567890">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role Pengguna *</label>
                        <select name="role" class="form-select" required>
                            <option value="PLAYER">PLAYER (Pemain / Organizer)</option>
                            <option value="VENUE_OWNER">VENUE_OWNER (Mitra Pengelola Lapangan)</option>
                            <option value="SUPER_ADMIN">SUPER_ADMIN (Administrator Platform)</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Password Awal (Min. 8 karakter) *</label>
                        <input type="password" name="password" class="form-control" required minlength="8" placeholder="Minimal 8 karakter">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('modalCreateUser')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div id="modalEditUser" class="modal-backdrop">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Edit Data Pengguna</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalEditUser')">&times;</button>
            </div>
            <form id="formEditUser" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" id="edit_user_name" name="name" class="form-control" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat Email *</label>
                        <input type="email" id="edit_user_email" name="email" class="form-control" required maxlength="150">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" id="edit_user_phone" name="phone" class="form-control" maxlength="20">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role Pengguna *</label>
                        <select id="edit_user_role" name="role" class="form-select" required>
                            <option value="PLAYER">PLAYER (Pemain / Organizer)</option>
                            <option value="VENUE_OWNER">VENUE_OWNER (Mitra Pengelola Lapangan)</option>
                            <option value="SUPER_ADMIN">SUPER_ADMIN (Administrator Platform)</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" name="password" class="form-control" minlength="8" placeholder="Kosongkan bila tetap">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('modalEditUser')">Batal</button>
                    <button type="submit" class="btn btn-primary">Perbarui Data</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openEditUserModal(id, name, email, phone, role) {
        const form = document.getElementById('formEditUser');
        form.action = `/superadmin/users/${id}`;
        document.getElementById('edit_user_name').value = name;
        document.getElementById('edit_user_email').value = email;
        document.getElementById('edit_user_phone').value = phone;
        document.getElementById('edit_user_role').value = role;
        openModal('modalEditUser');
    }
</script>
@endpush

