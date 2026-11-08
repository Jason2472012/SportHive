@extends('layouts.venueowner')

@section('title', 'Kelola Lapangan & Olahraga')
@section('page_title', 'Kelola Lapangan Olahraga')

@section('content')

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h2 style="font-size: 1.3rem; font-weight: 800;">🏸 Lapangan & Cabang Olahraga</h2>
        <p style="font-size: 0.85rem; color: var(--text-muted);">Atur sarana lapangan pada setiap arena, jenis cabang olahraga, dan status operasional</p>
    </div>
    <a href="{{ route('venueowner.courts.create') }}" class="btn btn-primary">
        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Lapangan Baru
    </a>
</div>

<!-- Filters -->
<div class="card" style="padding: 16px 20px; margin-bottom: 20px;">
    <form method="GET" action="{{ route('venueowner.courts.index') }}" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">Filter Berdasarkan Arena</label>
            <select name="venue_id" class="btn btn-secondary" style="width: 100%; text-align: left;" onchange="this.form.submit()">
                <option value="">Semua Arena / Venue</option>
                @foreach($venues as $venue)
                    <option value="{{ $venue->id }}" {{ $selected_venue == $venue->id ? 'selected' : '' }}>{{ $venue->name }} ({{ $venue->city }})</option>
                @endforeach
            </select>
        </div>

        <div style="flex: 1; min-width: 180px;">
            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">Filter Cabang Olahraga</label>
            <select name="sport_id" class="btn btn-secondary" style="width: 100%; text-align: left;" onchange="this.form.submit()">
                <option value="">Semua Cabang Olahraga</option>
                @foreach($sports as $sport)
                    <option value="{{ $sport->id }}" {{ $selected_sport == $sport->id ? 'selected' : '' }}>{{ $sport->name }}</option>
                @endforeach
            </select>
        </div>

        @if($selected_venue || $selected_sport)
            <div>
                <a href="{{ route('venueowner.courts.index') }}" class="btn btn-secondary" style="font-size: 0.8rem;">Reset Filter</a>
            </div>
        @endif
    </form>
</div>

<!-- Court Table -->
<div class="card">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background-color: #f8fafc; border-bottom: 1px solid var(--border-color);">
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">Nama Lapangan</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">Arena / Venue</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">Cabang Olahraga</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">Status Lapangan</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">Slot Waktu</th>
                    <th style="padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courts as $court)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 14px 16px; font-weight: 700; color: var(--text-main);">
                            {{ $court->name }}
                        </td>
                        <td style="padding: 14px 16px; font-size: 0.85rem;">
                            <div style="font-weight: 600;">{{ $court->venue->name ?? '-' }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $court->venue->city ?? '-' }}</div>
                        </td>
                        <td style="padding: 14px 16px;">
                            <span class="badge-pill badge-info">⚽ {{ $court->sport->name ?? '-' }}</span>
                        </td>
                        <td style="padding: 14px 16px;">
                            @if($court->is_active)
                                <span class="badge-pill badge-success">AKTIF</span>
                            @else
                                <span class="badge-pill badge-danger">NONAKTIF</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; font-size: 0.85rem; font-weight: 600;">
                            {{ $court->time_slots_count }} Slot Terdaftar
                        </td>
                        <td style="padding: 14px 16px; text-align: right;">
                            <div style="display: inline-flex; gap: 6px; align-items: center;">
                                <a href="{{ route('venueowner.slots.index', ['court_id' => $court->id]) }}" class="btn btn-primary btn-sm" title="Atur Jadwal & Harga">
                                    Atur Slot
                                </a>

                                <form method="POST" action="{{ route('venueowner.courts.toggle-status', $court) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-secondary btn-sm" title="Ubah status">
                                        {{ $court->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>

                                <a href="{{ route('venueowner.courts.edit', $court) }}" class="btn btn-secondary btn-sm">Edit</a>

                                <form method="POST" action="{{ route('venueowner.courts.destroy', $court) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lapangan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 8px;">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">
                            Belum ada sarana lapangan yang sesuai dengan kriteria filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

