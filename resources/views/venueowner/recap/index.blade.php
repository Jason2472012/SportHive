@extends('layouts.venueowner')

@section('title', 'Rekapitulasi Pendapatan & Pelanggan')
@section('page_title', 'Rekapitulasi Usaha')

@push('styles')
<style>
    .recap-filter-card {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    .filter-grid {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .form-group-filter {
        flex: 1;
        min-width: 190px;
    }

    .filter-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 6px;
    }

    .form-select, .form-input-date {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 0.85rem;
        background-color: #ffffff;
        color: var(--text-main);
        outline: none;
        transition: border-color 0.15s ease;
    }

    .form-select:focus, .form-input-date:focus {
        border-color: var(--primary);
    }

    /* Grid Kartu Metrik */
    .recap-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    .recap-stat-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .recap-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    .recap-stat-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .recap-stat-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .recap-stat-value {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.2;
    }

    .recap-stat-sub {
        font-size: 0.72rem;
        color: var(--text-muted);
    }

    /* Icon Containers */
    .recap-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .recap-stat-icon svg {
        width: 20px;
        height: 20px;
    }

    .icon-emerald { background-color: #d1fae5; color: #059669; }
    .icon-blue { background-color: #e0f2fe; color: #0284c7; }
    .icon-amber { background-color: #fef3c7; color: #d97706; }
    .icon-purple { background-color: #f3e8ff; color: #9333ea; }

    /* Tables */
    .data-table-wrapper {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        margin-bottom: 28px;
        overflow: hidden;
    }

    .data-table-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .data-table-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 3px;
    }

    .data-table-subtitle {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin: 0;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .recap-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .recap-table th {
        background-color: #f8fafc;
        padding: 12px 18px;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }

    .recap-table td {
        padding: 14px 18px;
        font-size: 0.86rem;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-main);
        vertical-align: middle;
    }

    .recap-table tr:hover td {
        background-color: #fbfcfe;
    }

    .recap-table tr:last-child td {
        border-bottom: none;
    }

    /* Progress bar */
    .progress-bar-bg {
        background-color: #e2e8f0;
        height: 6px;
        border-radius: 999px;
        width: 110px;
        margin-top: 6px;
        overflow: hidden;
    }

    .progress-bar-fill {
        background-color: var(--primary);
        height: 100%;
        border-radius: 999px;
    }
</style>
@endpush

@section('content')
<!-- Header Halaman -->
<div style="margin-bottom: 24px;">
    <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-main); margin-bottom: 4px;">Rekapitulasi Pendapatan & Pelanggan</h2>
    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">Laporan komparasi pendapatan sewa, total pelanggan, dan performa per arena maupun gabungan.</p>
</div>

<!-- Card Filter -->
<div class="recap-filter-card">
    <form method="GET" action="{{ route('venueowner.recap.index') }}" class="filter-grid">
        <div class="form-group-filter" style="flex: 1.2;">
            <label class="filter-label">Pilih Venue / Arena</label>
            <select name="venue_id" class="form-select">
                <option value="all" {{ $selectedVenueId === 'all' ? 'selected' : '' }}>Semua Arena (Gabungan Total)</option>
                @foreach($venues as $v)
                    <option value="{{ $v->id }}" {{ (string)$selectedVenueId === (string)$v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group-filter">
            <label class="filter-label">Rentang Waktu</label>
            <select name="date_range" id="date_range_select" class="form-select" onchange="toggleCustomDates(this.value)">
                <option value="today" {{ $dateFilter === 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="this_week" {{ $dateFilter === 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                <option value="this_month" {{ $dateFilter === 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                <option value="this_year" {{ $dateFilter === 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                <option value="custom" {{ $dateFilter === 'custom' ? 'selected' : '' }}>Kustom Rentang Tanggal</option>
                <option value="all" {{ $dateFilter === 'all' ? 'selected' : '' }}>Semua Periode</option>
            </select>
        </div>

        <div id="custom_dates_wrapper" style="display: {{ $dateFilter === 'custom' ? 'flex' : 'none' }}; gap: 10px; flex: 2; min-width: 260px;">
            <div style="flex: 1;">
                <label class="filter-label">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-input-date">
            </div>
            <div style="flex: 1;">
                <label class="filter-label">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-input-date">
            </div>
        </div>

        <div>
            <button type="submit" class="btn btn-primary" style="padding: 9px 20px;">Terapkan Filter</button>
        </div>
    </form>
</div>

<!-- Penanda Periode -->
<div style="margin-bottom: 14px; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em;">
    Periode: <span style="color: var(--primary);">{{ $dateLabel }}</span>
</div>

<!-- Kartu Ringkasan Metrik (Proporsional & Rapi) -->
<div class="recap-stats-grid">
    <!-- Total Pendapatan Bersih -->
    <div class="recap-stat-card">
        <div class="recap-stat-info">
            <span class="recap-stat-label">Total Pendapatan</span>
            <div class="recap-stat-value" style="color: #047857;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <span class="recap-stat-sub">Status Lunas & Selesai</span>
        </div>
        <div class="recap-stat-icon icon-emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
    </div>

    <!-- Pelanggan Berbeda -->
    <div class="recap-stat-card">
        <div class="recap-stat-info">
            <span class="recap-stat-label">Pelanggan Unik</span>
            <div class="recap-stat-value">{{ $uniqueCustomersCount }} Orang</div>
            <span class="recap-stat-sub">Individu penyewa berbeda</span>
        </div>
        <div class="recap-stat-icon icon-blue">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
    </div>

    <!-- Total Sesi Sewa -->
    <div class="recap-stat-card">
        <div class="recap-stat-info">
            <span class="recap-stat-label">Total Transaksi Sewa</span>
            <div class="recap-stat-value">{{ $totalBookings }} Sesi</div>
            <span class="recap-stat-sub">{{ $completedBookings }} sesi telah check-in</span>
        </div>
        <div class="recap-stat-icon icon-amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
        </div>
    </div>

    <!-- Rata-rata per Transaksi -->
    <div class="recap-stat-card">
        <div class="recap-stat-info">
            <span class="recap-stat-label">Rata-rata per Transaksi</span>
            <div class="recap-stat-value" style="font-size: 1.25rem;">
                Rp {{ $totalBookings > 0 ? number_format($totalRevenue / $totalBookings, 0, ',', '.') : '0' }}
            </div>
            <span class="recap-stat-sub">Rata-rata nilai per sesi sewa</span>
        </div>
        <div class="recap-stat-icon icon-purple">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
    </div>
</div>

<!-- Tabel Komparasi Antar-Venue -->
<div class="data-table-wrapper">
    <div class="data-table-header">
        <div>
            <h3 class="data-table-title">Rincian Komparasi Antar-Venue</h3>
            <p class="data-table-subtitle">Perbandingan performa omset dan jumlah penyewa masing-masing arena dalam periode ini</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="recap-table">
            <thead>
                <tr>
                    <th>Nama Arena / Venue</th>
                    <th>Lokasi</th>
                    <th>Fasilitas Lapangan</th>
                    <th>Pelanggan Berbeda</th>
                    <th>Total Sesi Sewa</th>
                    <th>Kontribusi Pendapatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($venueBreakdown as $item)
                    @php
                        $percentage = $totalRevenue > 0 ? round(($item['revenue'] / $totalRevenue) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main); margin-bottom: 3px;">{{ $item['venue']->name }}</div>
                            <span class="badge-pill {{ $item['venue']->validation_status === 'APPROVED' ? 'badge-success' : 'badge-warning' }}" style="font-size: 0.68rem; padding: 2px 8px;">
                                {{ $item['venue']->validation_status }}
                            </span>
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.82rem;">
                            {{ $item['venue']->city ?? $item['venue']->address ?? '-' }}
                        </td>
                        <td>
                            <strong>{{ $item['active_courts_count'] }}</strong> <span style="color: var(--text-muted); font-size: 0.8rem;">/ {{ $item['total_courts_count'] }} Lapangan Aktif</span>
                        </td>
                        <td>
                            <strong style="color: #0284c7;">{{ $item['unique_customers'] }}</strong> <span style="font-size: 0.8rem; color: var(--text-muted);">Penyewa</span>
                        </td>
                        <td>
                            <strong>{{ $item['bookings_count'] }}</strong> <span style="font-size: 0.8rem; color: var(--text-muted);">Sesi</span>
                        </td>
                        <td>
                            <div style="font-weight: 800; color: #047857; font-size: 0.92rem;">
                                Rp {{ number_format($item['revenue'], 0, ',', '.') }}
                            </div>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: {{ $percentage }}%;"></div>
                            </div>
                            <span style="font-size: 0.7rem; color: var(--text-muted);">{{ $percentage }}% kontribusi</span>
                        </td>
                        <td>
                            <a href="{{ route('venueowner.recap.index', ['venue_id' => $item['venue']->id, 'date_range' => $dateFilter, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-secondary btn-sm">
                                Filter Venue Ini
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            Belum ada arena yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Tabel Transaksi Terakhir Selesai / Lunas -->
<div class="data-table-wrapper">
    <div class="data-table-header">
        <div>
            <h3 class="data-table-title">Daftar Transaksi Selesai & Lunas</h3>
            <p class="data-table-subtitle">Menampilkan hingga 15 pemesanan lunas terbaru yang tercatat pada periode ini</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="recap-table">
            <thead>
                <tr>
                    <th>Kode Tiket / ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Venue & Lapangan</th>
                    <th>Jadwal Bermain</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $b)
                    <tr>
                        <td style="font-family: monospace; font-weight: 700; font-size: 0.85rem; color: #0f172a;">
                            {{ $b->e_ticket_code ?? ('#SH-' . str_pad($b->id, 5, '0', STR_PAD_LEFT)) }}
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main);">{{ $b->user->name ?? 'Pemain Anonim' }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $b->user->phone ?? $b->user->email ?? '-' }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-main);">{{ $b->slot->court->venue->name ?? '-' }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $b->slot->court->name ?? '-' }}</div>
                        </td>
                        <td style="font-size: 0.82rem;">
                            <div style="font-weight: 600;">{{ $b->slot && $b->slot->start_at ? $b->slot->start_at->format('d M Y') : '-' }}</div>
                            <div style="color: var(--text-muted); font-size: 0.76rem;">
                                {{ $b->slot && $b->slot->start_at ? $b->slot->start_at->format('H:i') : '' }} - {{ $b->slot && $b->slot->end_at ? $b->slot->end_at->format('H:i') : '' }} WIB
                            </div>
                        </td>
                        <td style="font-weight: 800; color: #047857; font-size: 0.92rem;">
                            Rp {{ number_format($b->total_price, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($b->status === 'COMPLETED')
                                <span class="badge-pill badge-info">CHECKED-IN</span>
                            @else
                                <span class="badge-pill badge-success">LUNAS</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            Tidak ada transaksi lunas pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleCustomDates(val) {
        const wrapper = document.getElementById('custom_dates_wrapper');
        if (val === 'custom') {
            wrapper.style.display = 'flex';
        } else {
            wrapper.style.display = 'none';
        }
    }
</script>
@endsection
