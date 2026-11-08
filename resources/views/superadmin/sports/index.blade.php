@extends('layouts.superadmin')

@section('title', 'Master Cabang Olahraga & Skill Level')
@section('page_title', 'Master Olahraga & Skill Level')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h2>Master Data Cabang Olahraga & Daftar Skill Level</h2>
            <p>Kelola daftar cabang olahraga (Futsal, Badminton, Basket, Tenis, Mini Soccer, dll.) beserta kualifikasi Skill Level (Beginner, Intermediate, Advanced) di setiap olahraga.</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="openModal('modalCreateSport')">
            + Tambah Cabang Olahraga
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <p>Total Cabang Olahraga</p>
                <div class="stat-number">{{ $summary['total_sports'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-amber">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Olahraga Aktif</p>
                <div class="stat-number">{{ $summary['active_sports'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Olahraga Nonaktif</p>
                <div class="stat-number">{{ $summary['inactive_sports'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-slate">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Total Daftar Skill Level</p>
                <div class="stat-number">{{ $summary['total_skill_levels'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-purple">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="panel">
        <div class="panel-header">
            <h3>Daftar Cabang Olahraga & Pengaturan Skill Level</h3>
            <form method="GET" action="{{ route('superadmin.sports.index') }}" class="filter-bar">
                <input type="text" name="search" class="form-control" style="width: 220px;" placeholder="Cari nama olahraga..." value="{{ request('search') }}">
                <select name="status" class="form-select" style="width: 155px;">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <button type="submit" class="btn btn-outline">Filter</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('superadmin.sports.index') }}" class="btn btn-outline">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cabang Olahraga</th>
                        <th>Status</th>
                        <th>Penggunaan</th>
                        <th>Daftar Skill Level (Kelola Skill Level)</th>
                        <th>Aksi Olahraga</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sports as $sport)
                        <tr>
                            <td>#{{ $sport->id }}</td>
                            <td>
                                <div style="font-size: 0.95rem; font-weight: 700;">{{ $sport->name }}</div>
                                <div style="font-size: 0.74rem; color: var(--text-muted);">
                                    Dibuat: {{ $sport->created_at ? $sport->created_at->format('d M Y') : '-' }}
                                </div>
                            </td>
                            <td>
                                @if($sport->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-neutral">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size: 0.8rem;">
                                    <strong>{{ $sport->courts_count }}</strong> Lapangan Venue
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">
                                    <strong>{{ $sport->user_sport_profiles_count }}</strong> Preferensi Pemain
                                </div>
                            </td>
                            <td style="min-width: 340px;">
                                <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px;">
                                    @forelse($sport->skillLevels as $skill)
                                        <div style="display: inline-flex; align-items: center; gap: 6px; background: #f8fafc; border: 1px solid var(--border-color); border-radius: 8px; padding: 4px 8px; font-size: 0.77rem;">
                                            <span class="badge badge-info" style="padding: 2px 6px;">Lv.{{ $skill->level_order }}</span>
                                            <strong>{{ $skill->name }}</strong>
                                            <button
                                                type="button"
                                                style="background: none; border: none; color: #2563eb; cursor: pointer; font-size: 0.72rem; font-weight: 600;"
                                                onclick="openEditSkillModal({{ $skill->id }}, @js($sport->name), @js($skill->name), {{ $skill->level_order }})"
                                                title="Edit Skill Level"
                                            >
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('superadmin.skill-levels.destroy', $skill) }}" style="display: inline;" onsubmit="return confirm('Hapus skill level {{ addslashes($skill->name) }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background: none; border: none; color: #dc2626; cursor: pointer; font-size: 0.75rem; font-weight: 700;" title="Hapus Skill Level">&times;</button>
                                            </form>
                                        </div>
                                    @empty
                                        <span style="font-size: 0.78rem; color: var(--text-muted);">Belum ada skill level</span>
                                    @endforelse
                                </div>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline"
                                    onclick="openAddSkillModal({{ $sport->id }}, @js($sport->name), {{ $sport->skillLevels->count() + 1 }})"
                                >
                                    + Tambah Skill Level
                                </button>
                            </td>
                            <td>
                                <div class="action-group">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline"
                                        onclick="openEditSportModal({{ $sport->id }}, @js($sport->name), {{ $sport->is_active ? 1 : 0 }})"
                                    >
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('superadmin.sports.toggle-status', $sport) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $sport->is_active ? 'btn-warning' : 'btn-primary' }}">
                                            {{ $sport->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('superadmin.sports.destroy', $sport) }}" onsubmit="return confirm('Hapus cabang olahraga {{ addslashes($sport->name) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 28px; color: var(--text-muted);">
                                Belum ada master data cabang olahraga.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Cabang Olahraga -->
    <div id="modalCreateSport" class="modal-backdrop">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Tambah Cabang Olahraga Baru</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalCreateSport')">&times;</button>
            </div>
            <form method="POST" action="{{ route('superadmin.sports.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Cabang Olahraga *</label>
                        <input type="text" name="name" class="form-control" required maxlength="50" placeholder="Contoh: Padel / Voli / Biliar">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status Awal</label>
                        <select name="is_active" class="form-select">
                            <option value="1" selected>Aktif (Ditampilkan di Katalog & Profil)</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.84rem; cursor: pointer;">
                            <input type="checkbox" name="create_default_skills" value="1" checked>
                            <span>Otomatis buat 3 Skill Level standar (<strong>Beginner, Intermediate, Advanced</strong>)</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('modalCreateSport')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Olahraga</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Cabang Olahraga -->
    <div id="modalEditSport" class="modal-backdrop">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Edit Cabang Olahraga</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalEditSport')">&times;</button>
            </div>
            <form id="formEditSport" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Cabang Olahraga *</label>
                        <input type="text" id="edit_sport_name" name="name" class="form-control" required maxlength="50">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Status</label>
                        <select id="edit_sport_active" name="is_active" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('modalEditSport')">Batal</button>
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Skill Level -->
    <div id="modalAddSkill" class="modal-backdrop">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Tambah Skill Level (<span id="add_skill_sport_name"></span>)</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalAddSkill')">&times;</button>
            </div>
            <form id="formAddSkill" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Kualifikasi Skill Level *</label>
                        <input type="text" name="name" class="form-control" required maxlength="50" placeholder="Contoh: Beginner / Intermediate / Advanced / Pro">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Urutan Tingkatan (Level Order) *</label>
                        <input type="number" id="add_skill_order" name="level_order" class="form-control" required min="1" max="20" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('modalAddSkill')">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah Skill Level</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Skill Level -->
    <div id="modalEditSkill" class="modal-backdrop">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Edit Skill Level (<span id="edit_skill_sport_name"></span>)</h3>
                <button type="button" class="modal-close" onclick="closeModal('modalEditSkill')">&times;</button>
            </div>
            <form id="formEditSkill" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Kualifikasi Skill Level *</label>
                        <input type="text" id="edit_skill_name" name="name" class="form-control" required maxlength="50">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Urutan Tingkatan (Level Order) *</label>
                        <input type="number" id="edit_skill_order" name="level_order" class="form-control" required min="1" max="20">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('modalEditSkill')">Batal</button>
                    <button type="submit" class="btn btn-primary">Perbarui Skill Level</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openEditSportModal(id, name, isActive) {
        const form = document.getElementById('formEditSport');
        form.action = `/superadmin/sports/${id}`;
        document.getElementById('edit_sport_name').value = name;
        document.getElementById('edit_sport_active').value = isActive;
        openModal('modalEditSport');
    }

    function openAddSkillModal(sportId, sportName, nextOrder) {
        const form = document.getElementById('formAddSkill');
        form.action = `/superadmin/sports/${sportId}/skill-levels`;
        document.getElementById('add_skill_sport_name').textContent = sportName;
        document.getElementById('add_skill_order').value = nextOrder;
        openModal('modalAddSkill');
    }

    function openEditSkillModal(skillId, sportName, name, levelOrder) {
        const form = document.getElementById('formEditSkill');
        form.action = `/superadmin/skill-levels/${skillId}`;
        document.getElementById('edit_skill_sport_name').textContent = sportName;
        document.getElementById('edit_skill_name').value = name;
        document.getElementById('edit_skill_order').value = levelOrder;
        openModal('modalEditSkill');
    }
</script>
@endpush

