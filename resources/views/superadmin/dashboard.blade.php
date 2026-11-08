@extends('layouts.superadmin')

@section('title', 'Super Admin Dashboard')
@section('page_title', 'Dashboard')

@push('styles')
<style>
    .welcome-banner {
        background: linear-gradient(135deg, #15803d, #0f5132);
        color: #ffffff;
        padding: 28px 32px;
        border-radius: 16px;
        margin-bottom: 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 25px -5px rgba(21, 128, 61, 0.2);
    }

    .welcome-text h2 {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 6px;
        letter-spacing: -0.02em;
    }

    .welcome-text p {
        font-size: 0.95rem;
        color: #dcfce7;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
    }

    .stat-info p {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.05em;
        margin-bottom: 6px;
    }

    .stat-info .stat-number {
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.03em;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: grid;
        place-items: center;
    }

    .stat-icon svg {
        width: 24px;
        height: 24px;
    }

    .icon-green { background-color: #dcfce7; color: #15803d; }
    .icon-blue { background-color: #dbeafe; color: #2563eb; }
    .icon-amber { background-color: #fef3c7; color: #d97706; }
    .icon-rose { background-color: #ffe4e6; color: #e11d48; }

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    .panel {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        overflow: hidden;
    }

    .panel-header {
        padding: 18px 22px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .panel-header h3 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .panel-body {
        padding: 22px;
    }

    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.88rem;
    }

    th {
        text-align: left;
        padding: 12px 14px;
        color: var(--text-muted);
        font-weight: 600;
        border-bottom: 1px solid var(--border-color);
        background-color: #f8fafc;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    td {
        padding: 14px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-main);
    }

    tr:last-child td {
        border-bottom: none;
    }

    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-admin { background-color: #fee2e2; color: #dc2626; }
    .badge-owner { background-color: #fef3c7; color: #b45309; }
    .badge-player { background-color: #dcfce7; color: #15803d; }

    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .quick-action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: #ffffff;
        color: var(--text-main);
        text-decoration: none;
        font-size: 0.88rem;
        font-weight: 600;
        transition: background 0.15s, border-color 0.15s;
    }

    .quick-action-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <div class="welcome-banner">
        <div class="welcome-text">
            <h2>Selamat Datang, {{ auth()->user()->name ?? 'Super Admin' }}!</h2>
            <p>Kelola seluruh operasional platform SportHive dari satu pusat kontrol.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <p>Total Pengguna</p>
                <div class="stat-number">{{ $stats['total_users'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <p>Total Venue</p>
                <div class="stat-number">{{ $stats['total_venues'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <p>Cabang Olahraga</p>
                <div class="stat-number">{{ $stats['total_sports'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-amber">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <p>Laporan Baru</p>
                <div class="stat-number">{{ $stats['total_reports'] ?? 0 }}</div>
            </div>
            <div class="stat-icon icon-rose">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <div class="panel">
            <div class="panel-header">
                <h3>Pengguna Terbaru</h3>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Bergabung</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_users ?? [] as $user)
                            <tr>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge {{ $user->role === 'SUPER_ADMIN' ? 'badge-admin' : ($user->role === 'VENUE_OWNER' ? 'badge-owner' : 'badge-player') }}">
                                        {{ str_replace('_', ' ', $user->role) }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d M Y') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 24px;">Belum ada data pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h3>Aksi Cepat</h3>
            </div>
            <div class="panel-body">
                <div class="quick-actions">
                    <a href="{{ route('superadmin.sports.index') }}" class="quick-action-btn">
                        <span>+ Tambah Kategori Olahraga</span>
                    </a>
                    <a href="{{ route('superadmin.users.index') }}" class="quick-action-btn">
                        <span>Kelola Pengguna & Pemilik</span>
                    </a>
                    <a href="{{ route('superadmin.venues.index') }}" class="quick-action-btn">
                        <span>Verifikasi Venue Baru</span>
                    </a>
                    <a href="{{ route('superadmin.logs.index') }}" class="quick-action-btn">
                        <span>Lihat Log Aktivitas Sistem</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
