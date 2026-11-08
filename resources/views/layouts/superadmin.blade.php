<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') · {{ config('app.name', 'SportHive') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #15803d;
            --primary-hover: #166534;
            --primary-light: #dcfce7;
            --sidebar-bg: #0f172a;
            --sidebar-border: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #ffffff;
            --sidebar-active-bg: #1e293b;
            --bg-body: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
            --font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        aside.sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 50;
            border-right: 1px solid var(--sidebar-border);
            transition: transform 0.2s ease-in-out;
        }

        .sidebar-header {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--sidebar-border);
        }

        .logo-box {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #22c55e, #15803d);
            border-radius: 10px;
            display: grid;
            place-items: center;
            color: #ffffff;
            font-weight: 800;
            font-size: 1.15rem;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }

        .brand-text h1 {
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .brand-text span {
            color: #22c55e;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .sidebar-menu {
            flex: 1;
            padding: 20px 12px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .menu-heading {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #475569;
            padding: 12px 12px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.15s ease;
        }

        .nav-item:hover {
            color: var(--sidebar-text-active);
            background-color: rgba(255, 255, 255, 0.05);
        }

        .nav-item.active {
            color: #ffffff;
            background-color: var(--sidebar-active-bg);
            border-left: 3px solid #22c55e;
        }

        .nav-item svg {
            width: 18px;
            height: 18px;
            stroke-width: 2;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            overflow: hidden;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #334155;
            color: #f8fafc;
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .user-details {
            overflow: hidden;
        }

        .user-name {
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 0.72rem;
            color: #94a3b8;
        }

        /* Main Wrapper */
        .main-wrapper {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Top Header */
        header.topbar {
            height: 64px;
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        .breadcrumb span.current {
            color: var(--text-main);
            font-weight: 600;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-logout {
            background: none;
            border: 1px solid var(--border-color);
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            color: #ef4444;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-logout:hover {
            background-color: #fef2f2;
            border-color: #fecaca;
        }

        /* Page Content */
        main.content {
            padding: 32px;
            flex: 1;
        }

        /* Shared Component Styles for Subpages */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .page-title h2 {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .page-title p {
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.88rem;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .alert-success {
            background-color: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
        }

        .alert-error {
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
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
        .icon-purple { background-color: #f3e8ff; color: #7e22ce; }
        .icon-slate { background-color: #f1f5f9; color: #475569; }

        .panel {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .panel-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
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
        .badge-success { background-color: #dcfce7; color: #15803d; }
        .badge-warning { background-color: #fef3c7; color: #b45309; }
        .badge-danger { background-color: #fee2e2; color: #dc2626; }
        .badge-info { background-color: #dbeafe; color: #1d4ed8; }
        .badge-neutral { background-color: #f1f5f9; color: #475569; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s ease;
        }

        .btn-sm { padding: 5px 10px; font-size: 0.76rem; border-radius: 6px; }
        .btn-primary { background-color: var(--primary); color: #ffffff; }
        .btn-primary:hover { background-color: var(--primary-hover); }
        .btn-outline { background-color: #ffffff; border-color: var(--border-color); color: var(--text-main); }
        .btn-outline:hover { background-color: #f8fafc; border-color: #cbd5e1; }
        .btn-warning { background-color: #f59e0b; color: #ffffff; }
        .btn-warning:hover { background-color: #d97706; }
        .btn-danger { background-color: #ef4444; color: #ffffff; }
        .btn-danger:hover { background-color: #dc2626; }

        .form-control, .form-select {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.86rem;
            color: var(--text-main);
            background-color: #ffffff;
            outline: none;
        }

        .form-group { margin-bottom: 14px; }
        .form-label { display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-main); margin-bottom: 6px; }
        .filter-bar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .action-group { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }

        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            z-index: 100;
            place-items: center;
            padding: 20px;
            overflow-y: auto;
        }
        .modal-backdrop.show { display: grid; }
        .modal-card {
            background: #ffffff;
            border-radius: 14px;
            width: 100%;
            max-width: 520px;
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        .modal-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-header h3 { font-size: 1.05rem; font-weight: 700; }
        .modal-close { background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer; }
        .modal-body { padding: 22px; }
        .modal-footer {
            padding: 14px 22px;
            background-color: #f8fafc;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .pagination-wrapper {
            padding: 14px 22px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.84rem;
            color: var(--text-muted);
            flex-wrap: wrap;
            gap: 10px;
        }
        .pagination-links nav svg { width: 18px; height: 18px; }

        /* Responsive */
        @media (max-width: 768px) {
            aside.sidebar {
                transform: translateX(-100%);
            }
            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-box">S</div>
            <div class="brand-text">
                <h1>SportHive</h1>
                <span>Super Admin</span>
            </div>
        </div>

        <nav class="sidebar-menu">
            <div class="menu-heading">Menu Utama</div>
            <a href="{{ route('superadmin.dashboard') }}" class="nav-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <div class="menu-heading">Manajemen Platform</div>
            <a href="{{ route('superadmin.users.index') }}" class="nav-item {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Kelola Pengguna
            </a>
            <a href="{{ route('superadmin.venues.index') }}" class="nav-item {{ request()->routeIs('superadmin.venues.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Kelola Venue
            </a>
            <a href="{{ route('superadmin.sports.index') }}" class="nav-item {{ request()->routeIs('superadmin.sports.*') || request()->routeIs('superadmin.skill-levels.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Kategori Olahraga
            </a>

            <div class="menu-heading">Monitoring</div>
            <a href="{{ route('superadmin.reports.index') }}" class="nav-item {{ request()->routeIs('superadmin.reports.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Laporan & Pelanggaran
            </a>
            <a href="{{ route('superadmin.logs.index') }}" class="nav-item {{ request()->routeIs('superadmin.logs.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Log Aktivitas
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
                <div class="user-details">
                    <div class="user-name">{{ auth()->user()->name ?? 'Super Admin' }}</div>
                    <div class="user-role">SUPER ADMIN</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout" title="Keluar">Logout</button>
            </form>
        </div>
    </aside>

    <div class="main-wrapper">
        <header class="topbar">
            <div class="breadcrumb">
                <span>Admin</span>
                <span>/</span>
                <span class="current">@yield('page_title', 'Dashboard')</span>
            </div>
            <div class="topbar-actions">
                <a href="{{ route('home') }}" target="_blank" style="font-size: 0.85rem; color: var(--text-muted); text-decoration: none; display: flex; align-items: center; gap: 5px;">
                    <span>Lihat Website</span>
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </header>

        <main class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    <div><strong>Berhasil!</strong> {{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <div><strong>Perhatian!</strong> {{ session('error') }}</div>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="alert alert-error">
                    <div>
                        <strong>Terdapat kesalahan input:</strong>
                        <ul style="margin-top: 6px; padding-left: 18px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function openModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.add('show');
        }
        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('show');
        }
    </script>
    @stack('scripts')
</body>
</html>
