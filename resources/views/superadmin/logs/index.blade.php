@extends('layouts.superadmin')

@section('title', 'Pantau Statistik & Log Aktivitas')
@section('page_title', 'Statistik & Log Aktivitas')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h2>Pantau Statistik Penggunaan, Open Room Aktif & Log Aktivitas</h2>
            <p>Monitor rekapitulasi operasional platform secara real-time, aktivitas Open Room matchmaking, serta jejak audit keamanan (Activity Logs).</p>
        </div>
    </div>

    <!-- Platform Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <p>Populasi Pengguna</p>
                <div class="stat-number">{{ $platformStats['users_total'] ?? 0 }}</div>
                <div style="font-size: 0.74rem; color: var(--text-muted); margin-top: 4px;">
                    {{ $platformStats['users_player'] ?? 0 }} Pemain · {{ $platformStats['users_owner'] ?? 0 }} Owner
                </div>
            </div>
            <div class="stat-icon icon-blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Venue Terverifikasi</p>
                <div class="stat-number">{{ $platformStats['venues_approved'] ?? 0 }}</div>
                <div style="font-size: 0.74rem; color: #d97706; margin-top: 4px;">
                    {{ $platformStats['venues_pending'] ?? 0 }} Menunggu Validasi
                </div>
            </div>
            <div class="stat-icon icon-green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Reservasi & Open Room</p>
                <div class="stat-number">{{ $platformStats['open_rooms_active'] ?? 0 }} Room</div>
                <div style="font-size: 0.74rem; color: var(--text-muted); margin-top: 4px;">
                    Dari {{ $platformStats['bookings_total'] ?? 0 }} Total Booking Slot
                </div>
            </div>
            <div class="stat-icon icon-purple">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Total Jejak Log Sistem</p>
                <div class="stat-number">{{ $platformStats['logs_total'] ?? 0 }}</div>
                <div style="font-size: 0.74rem; color: var(--text-muted); margin-top: 4px;">
                    Rekaman Aktivitas Audit
                </div>
            </div>
            <div class="stat-icon icon-amber">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h3>Monitoring Open Room Matchmaking Aktif</h3>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID Room</th>
                        <th>Judul & Tipe Permainan</th>
                        <th>Organizer (Penyewa)</th>
                        <th>Venue & Olahraga</th>
                        <th>Target Skill</th>
                        <th>Kuota & Patungan</th>
                        <th>Status Room</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeRooms as $room)
                        <tr>
                            <td>#{{ $room->id }}</td>
                            <td>
                                <div><strong>{{ $room->title ?? 'Open Room #' . $room->id }}</strong></div>
                                <span class="badge {{ $room->type === 'TOURNAMENT' ? 'badge-warning' : 'badge-info' }}">
                                    {{ $room->type === 'TOURNAMENT' ? 'TOURNAMENT' : 'FUN MATCH' }}
                                </span>
                            </td>
                            <td>{{ $room->booking->user->name ?? '-' }}</td>
                            <td>
                                <div><strong>{{ $room->booking->slot->court->venue->name ?? '-' }}</strong></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">
                                    {{ $room->booking->slot->court->sport->name ?? '-' }} · {{ $room->booking->slot->court->name ?? '-' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ $room->skillTarget->name ?? 'All Level' }}</span>
                            </td>
                            <td>
                                <div><strong>{{ $room->members->count() }} / {{ $room->quota }}</strong> Pemain</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">
                                    Rp {{ number_format((float) $room->fee_per_person, 0, ',', '.') }} / org
                                </div>
                            </td>
                            <td>
                                @if($room->status === 'OPEN')
                                    <span class="badge badge-success">OPEN</span>
                                @else
                                    <span class="badge badge-warning">{{ $room->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 20px; color: var(--text-muted);">
                                Belum ada sesi Open Room aktif saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel Log Aktivitas Sistem -->
    <div class="panel">
        <div class="panel-header">
            <h3>Riwayat Log Aktivitas Sistem (Audit Trail)</h3>
            <form method="GET" action="{{ route('superadmin.logs.index') }}" class="filter-bar">
                <input type="text" name="search" class="form-control" style="width: 220px;" placeholder="Cari aksi / detail / user / IP..." value="{{ request('search') }}">
                <select name="action_filter" class="form-select" style="width: 190px;">
                    <option value="">Semua Jenis Aksi</option>
                    @foreach($availableActions as $act)
                        <option value="{{ $act }}" {{ request('action_filter') === $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-outline">Filter</button>
                @if(request()->hasAny(['search', 'action_filter', 'user_id']))
                    <a href="{{ route('superadmin.logs.index') }}" class="btn btn-outline">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID Log</th>
                        <th>Waktu Kejadian</th>
                        <th>Pengguna (Aktor)</th>
                        <th>Kode Aksi</th>
                        <th>Rincian Aktivitas</th>
                        <th>Alamat IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>#{{ $log->id }}</td>
                            <td style="white-space: nowrap;">
                                {{ $log->created_at ? $log->created_at->format('d M Y H:i:s') : '-' }}
                            </td>
                            <td>
                                @if($log->user)
                                    <div><strong>{{ $log->user->name }}</strong></div>
                                    <div style="font-size: 0.74rem; color: var(--text-muted);">{{ $log->user->role }}</div>
                                @else
                                    <span class="badge badge-neutral">Sistem</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $log->action }}</span>
                            </td>
                            <td>{{ $log->detail ?? '-' }}</td>
                            <td><code>{{ $log->ip_address ?? '-' }}</code></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 28px; color: var(--text-muted);">
                                Belum ada data log aktivitas yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="pagination-wrapper">
                <div>Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} log</div>
                <div class="pagination-links">{{ $logs->links() }}</div>
            </div>
        @endif
    </div>
@endsection

