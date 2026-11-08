@extends('layouts.superadmin')

@section('title', 'Handle Laporan & Feedback')
@section('page_title', 'Laporan & Feedback')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h2>Handle Laporan Masalah & Feedback Pengguna</h2>
            <p>Tinjau laporan pelanggaran pengguna, venue, atau room permainan, perbarui status penyelesaian, dan kirimkan notifikasi tindak lanjut ke pelapor.</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <p>Total Laporan</p>
                <div class="stat-number">{{ $summary['total'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Laporan Baru (OPEN)</p>
                <div class="stat-number">{{ $summary['open'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-rose">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Sedang Ditinjau (IN REVIEW)</p>
                <div class="stat-number">{{ $summary['in_review'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-amber">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Selesai (RESOLVED)</p>
                <div class="stat-number">{{ $summary['resolved'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Filter & Reports Table -->
    <div class="panel">
        <div class="panel-header">
            <h3>Daftar Laporan & Feedback Masuk</h3>
            <form method="GET" action="{{ route('superadmin.reports.index') }}" class="filter-bar">
                <input type="text" name="search" class="form-control" style="width: 220px;" placeholder="Cari isi laporan / pelapor..." value="{{ request('search') }}">
                <select name="target_type" class="form-select" style="width: 155px;">
                    <option value="">Semua Kategori</option>
                    <option value="USER" {{ request('target_type') === 'USER' ? 'selected' : '' }}>USER</option>
                    <option value="VENUE" {{ request('target_type') === 'VENUE' ? 'selected' : '' }}>VENUE</option>
                    <option value="ROOM" {{ request('target_type') === 'ROOM' ? 'selected' : '' }}>OPEN ROOM</option>
                    <option value="OTHER" {{ request('target_type') === 'OTHER' ? 'selected' : '' }}>OTHER / FEEDBACK</option>
                </select>
                <select name="status" class="form-select" style="width: 160px;">
                    <option value="">Semua Status</option>
                    <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>OPEN</option>
                    <option value="IN_REVIEW" {{ request('status') === 'IN_REVIEW' ? 'selected' : '' }}>IN_REVIEW</option>
                    <option value="RESOLVED" {{ request('status') === 'RESOLVED' ? 'selected' : '' }}>RESOLVED</option>
                    <option value="DISMISSED" {{ request('status') === 'DISMISSED' ? 'selected' : '' }}>DISMISSED</option>
                </select>
                <button type="submit" class="btn btn-outline">Filter</button>
                @if(request()->hasAny(['search', 'target_type', 'status']))
                    <a href="{{ route('superadmin.reports.index') }}" class="btn btn-outline">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pelapor</th>
                        <th>Target Laporan</th>
                        <th>Alasan / Isi Feedback</th>
                        <th>Status</th>
                        <th>Ditangani Oleh</th>
                        <th>Waktu Lapor</th>
                        <th>Aksi Tindak Lanjut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td>#{{ $report->id }}</td>
                            <td>
                                <div><strong>{{ $report->reporter->name ?? 'Pengguna Terhapus' }}</strong></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $report->reporter->email ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $report->target_type }}</span>
                                @if($report->target_id)
                                    <span style="font-size: 0.78rem; color: var(--text-muted);">ID #{{ $report->target_id }}</span>
                                @endif
                            </td>
                            <td style="max-width: 320px;">
                                <div style="font-size: 0.85rem; line-height: 1.45;">{{ $report->reason }}</div>
                            </td>
                            <td>
                                @if($report->status === 'OPEN')
                                    <span class="badge badge-danger">OPEN</span>
                                @elseif($report->status === 'IN_REVIEW')
                                    <span class="badge badge-warning">IN REVIEW</span>
                                @elseif($report->status === 'RESOLVED')
                                    <span class="badge badge-success">RESOLVED</span>
                                @else
                                    <span class="badge badge-neutral">DISMISSED</span>
                                @endif
                            </td>
                            <td>
                                @if($report->handler)
                                    <div style="font-size: 0.8rem; font-weight: 600;">{{ $report->handler->name }}</div>
                                    @if($report->resolved_at)
                                        <div style="font-size: 0.73rem; color: var(--text-muted);">{{ $report->resolved_at->format('d M Y H:i') }}</div>
                                    @endif
                                @else
                                    <span style="font-size: 0.78rem; color: var(--text-muted);">Belum ditangani</span>
                                @endif
                            </td>
                            <td>{{ $report->created_at ? $report->created_at->format('d M Y H:i') : '-' }}</td>
                            <td>
                                <div class="action-group">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-primary"
                                        onclick="openHandleReportModal({{ $report->id }}, @js($report->target_type), {{ $report->target_id ?? 'null' }}, @js($report->status), @js($report->reason))"
                                    >
                                        Proses
                                    </button>
                                    <form method="POST" action="{{ route('superadmin.reports.destroy', $report) }}" onsubmit="return confirm('Hapus laporan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 28px; color: var(--text-muted);">
                                Tidak ada data laporan atau feedback yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="pagination-wrapper">
                <div>Menampilkan {{ $reports->firstItem() }}–{{ $reports->lastItem() }} dari {{ $reports->total() }} laporan</div>
                <div class="pagination-links">{{ $reports->links() }}</div>
            </div>
        @endif
    </div>

    <!-- Modal Proses Laporan -->
    <div id="modalHandleReport" class="modal-backdrop">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Tindak Lanjut Laporan #<span id="handle_report_id"></span></h3>
                <button type="button" class="modal-close" onclick="closeModal('modalHandleReport')">&times;</button>
            </div>
            <form id="formHandleReport" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; margin-bottom: 14px; font-size: 0.84rem;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px;">Isi Laporan / Feedback:</div>
                        <div id="handle_report_reason" style="font-weight: 500;"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ubah Status Penanganan *</label>
                        <select id="handle_report_status" name="status" class="form-select" required>
                            <option value="OPEN">OPEN (Belum Ditangani)</option>
                            <option value="IN_REVIEW">IN_REVIEW (Sedang Diinvestigasi)</option>
                            <option value="RESOLVED">RESOLVED (Selesai Ditindaklanjuti)</option>
                            <option value="DISMISSED">DISMISSED (Ditolak / Tidak Terbukti)</option>
                        </select>
                    </div>

                    <div class="form-group" id="suspend_user_option_wrap" style="display: none;">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.84rem; color: #dc2626; font-weight: 600; cursor: pointer;">
                            <input type="checkbox" name="suspend_target_user" value="1">
                            <span>Langsung Suspend (Nonaktifkan) akun pengguna yang dilaporkan ini</span>
                        </label>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Catatan Tanggapan ke Pelapor (Dikirim ke Notifikasi)</label>
                        <textarea name="resolution_note" class="form-control" rows="3" placeholder="Contoh: Terima kasih atas laporan Anda. Tim kami telah memeriksa dan memberikan tindakan tegas pada pihak terkait."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeModal('modalHandleReport')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Kirim Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openHandleReportModal(id, targetType, targetId, currentStatus, reason) {
        const form = document.getElementById('formHandleReport');
        form.action = `/superadmin/reports/${id}/status`;
        document.getElementById('handle_report_id').textContent = id;
        document.getElementById('handle_report_reason').textContent = reason;
        document.getElementById('handle_report_status').value = currentStatus === 'OPEN' ? 'RESOLVED' : currentStatus;

        const suspendWrap = document.getElementById('suspend_user_option_wrap');
        if (targetType === 'USER' && targetId) {
            suspendWrap.style.display = 'block';
        } else {
            suspendWrap.style.display = 'none';
        }

        openModal('modalHandleReport');
    }
</script>
@endpush

