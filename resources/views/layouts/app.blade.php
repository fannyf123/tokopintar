<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'TOKOPINTAR'))</title>
    <script>
        (function () {
            const t = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', t);
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        body { background:#f4f7f9; font-family:'Inter',sans-serif; font-size:13.5px; color:#4a5568; overflow-x:hidden; }
        .sidebar { height:100vh; background:#1e222d; color:#fff; padding-top:20px; position:fixed; top:0; left:0; width:260px; box-shadow:2px 0 10px rgba(0,0,0,.05); z-index:1040; transition:transform .3s; overflow-y:auto; }
        .sidebar .brand { font-size:1.2rem; font-weight:700; letter-spacing:1.5px; }
        .sidebar .menu-label { font-size:.7rem; padding-left:20px; color:#6b7280; font-weight:600; letter-spacing:1px; margin-bottom:10px; }
        .sidebar a { color:#a0aec0; text-decoration:none; display:flex; align-items:center; padding:12px 20px; margin-bottom:4px; border-radius:8px; transition:.3s; font-weight:500; }
        .sidebar a:hover { background:rgba(255,255,255,.05); color:#fff; transform:translateX(4px); }
        .sidebar a.active { background:#4361ee; color:#fff; box-shadow:0 4px 12px rgba(67,97,238,.3); font-weight:600; }
        .sidebar a i { width:25px; font-size:16px; }
        .main-content { margin-left:260px; padding:25px 35px; min-height:100vh; transition:margin .3s; }
        .navbar { border-radius:12px; background:#fff !important; box-shadow:0 2px 10px rgba(0,0,0,.02) !important; margin-bottom:30px !important; }
        .card { border:none; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,.03); }
        .card-body { padding:25px; }
        .btn { font-size:13px; font-weight:500; border-radius:8px; padding:8px 16px; }
        .btn-sm { padding:5px 10px; font-size:12px; border-radius:6px; }
        .btn-primary { background:#4361ee; border-color:#4361ee; }
        .btn-primary:hover { background:#3f37c9; border-color:#3f37c9; }
        table.dataTable thead th { border:none !important; border-bottom:2px solid #e2e8f0 !important; background:#fff !important; color:#64748b; font-size:12px; text-transform:uppercase; letter-spacing:.5px; padding:15px 10px !important; }
        table.dataTable tbody td { border:none !important; border-bottom:1px solid #f1f5f9 !important; padding:15px 10px !important; vertical-align:middle; color:#475569; }
        .table thead th { border-bottom:2px solid #e2e8f0; background:#fff; color:#64748b; font-size:12px; text-transform:uppercase; letter-spacing:.5px; padding:15px 10px; }
        .table tbody td { border-bottom:1px solid #f1f5f9; padding:14px 10px; vertical-align:middle; color:#475569; }
        .stat-card { border-radius:12px; padding:20px; color:#fff; }
        .stat-card .stat-label { font-size:11px; text-transform:uppercase; letter-spacing:1px; opacity:.85; }
        .stat-card .stat-value { font-size:1.5rem; font-weight:700; margin-top:6px; }
        .sidebar-overlay { position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,.5); z-index:1030; display:none; opacity:0; transition:opacity .3s; }

        .sidebar.collapsed { width:70px; padding-left:.5rem !important; padding-right:.5rem !important; }
        .sidebar.collapsed .menu-label, .sidebar.collapsed #themeLabel, .sidebar.collapsed .sidebar-brand-full { display:none !important; }
        .sidebar.collapsed .sidebar-brand-mini { display:inline !important; font-size:1.5rem; }
        .sidebar.collapsed .sidebar-header { justify-content:center; flex-direction:column; gap:8px; padding:0 !important; }
        .sidebar.collapsed #collapseSidebar { margin-top:4px; }
        .sidebar.collapsed a { justify-content:center; padding:12px 6px; margin-bottom:6px; border-radius:10px; font-size:0 !important; }
        .sidebar.collapsed a i { width:auto; margin-right:0; font-size:18px !important; }
        .sidebar.collapsed a:hover { transform:none; }
        .sidebar.collapsed a.active { transform:none; }
        .sidebar.collapsed ~ .main-content { margin-left:70px; }
        .sidebar.collapsed hr { margin:.5rem .25rem !important; }
        @media (max-width:991.98px) {
            .sidebar { transform:translateX(-100%); width:80%; max-width:300px; }
            .sidebar.show { transform:translateX(0); }
            .main-content { margin-left:0; padding:12px; }
            .sidebar-overlay.show { display:block; opacity:1; }
            .navbar { margin-bottom:14px !important; padding:10px 14px !important; border-radius:10px; }
            .card-body { padding:14px; }
            body { font-size:13px; }
            h1, h2, h3, h4, h5 { font-size:1.15rem !important; }
            .table { font-size:12.5px; }
            .table thead th, table.dataTable thead th { padding:10px 6px !important; font-size:10.5px; letter-spacing:.3px; }
            .table tbody td, table.dataTable tbody td { padding:10px 6px !important; }
            .btn { padding:8px 12px; font-size:12.5px; }
            .btn-sm { padding:6px 10px; font-size:11.5px; }
            .stat-card .stat-value { font-size:1.15rem; }
            .stat-card .stat-label { font-size:10px; }
            .form-control, .form-select { font-size:14px; }
            .form-control-sm, .form-select-sm { font-size:13px; }
            .row.g-3 > [class*="col-"], .row.g-2 > [class*="col-"] { padding-top:.25rem; padding-bottom:.25rem; }
            .text-end { text-align:right !important; }
            .form-label { margin-bottom:.25rem; font-size:12px; }
            .input-group .btn { padding:6px 10px; }
            .card-body > h6, .card-body > h1, .card-body > h2 { word-break:break-word; }
            .table-responsive { -webkit-overflow-scrolling: touch; }
        }
        @media (max-width:575.98px) {
            .main-content { padding:8px; }
            .card { border-radius:10px; }
            .card-body { padding:12px; }
            .navbar-brand { font-size:.95rem; }
            .stat-card .card-body { padding:14px; }
        }
        .alert { border-radius:10px; }

        [data-bs-theme="dark"] body { background:#0f1419; color:#cbd5e1; }
        [data-bs-theme="dark"] .navbar { background:#1a1f2e !important; }
        [data-bs-theme="dark"] .navbar-brand, [data-bs-theme="dark"] .navbar .text-dark { color:#e2e8f0 !important; }
        [data-bs-theme="dark"] .card { background:#1a1f2e; color:#cbd5e1; box-shadow:0 4px 15px rgba(0,0,0,.2); }
        [data-bs-theme="dark"] .table { color:#cbd5e1; --bs-table-bg:transparent; --bs-table-striped-bg:#243144; --bs-table-striped-color:#e2e8f0; }
        [data-bs-theme="dark"] .table thead th, [data-bs-theme="dark"] table.dataTable thead th { background:#1a1f2e !important; color:#94a3b8; border-bottom-color:#334155 !important; }
        [data-bs-theme="dark"] .table tbody td, [data-bs-theme="dark"] table.dataTable tbody td { border-bottom-color:#334155 !important; color:#cbd5e1; }
        [data-bs-theme="dark"] .form-control, [data-bs-theme="dark"] .form-select { background:#0f1419; color:#e2e8f0; border-color:#334155; }
        [data-bs-theme="dark"] .form-control:focus, [data-bs-theme="dark"] .form-select:focus { background:#0f1419; color:#e2e8f0; }
        [data-bs-theme="dark"] .form-control::placeholder { color:#64748b; }
        [data-bs-theme="dark"] .input-group-text { background:#243144; color:#cbd5e1; border-color:#334155; }
        [data-bs-theme="dark"] .table-light { --bs-table-bg:#243144; --bs-table-color:#e2e8f0; }
        [data-bs-theme="dark"] .btn-light { background:#243144; color:#e2e8f0; border-color:#334155; }
        [data-bs-theme="dark"] .btn-light:hover { background:#334155; color:#fff; }
        [data-bs-theme="dark"] .text-muted { color:#94a3b8 !important; }
        [data-bs-theme="dark"] code { color:#fbbf24; background:#1e293b; padding:2px 6px; border-radius:4px; }
        [data-bs-theme="dark"] .alert-success { background:#14532d; color:#86efac; border-color:#166534; }
        [data-bs-theme="dark"] .alert-danger { background:#7f1d1d; color:#fca5a5; border-color:#991b1b; }
        [data-bs-theme="dark"] .alert-info { background:#1e3a8a; color:#93c5fd; border-color:#1e40af; }
        [data-bs-theme="dark"] .alert-light { background:#243144; color:#cbd5e1; border-color:#334155; }
        [data-bs-theme="dark"] hr { border-color:#334155; }
        [data-bs-theme="dark"] .page-link { background:#1a1f2e; border-color:#334155; color:#cbd5e1; }
        [data-bs-theme="dark"] .page-link:hover { background:#243144; color:#fff; }
        [data-bs-theme="dark"] .page-item.active .page-link { background:#4361ee; border-color:#4361ee; color:#fff; }
        [data-bs-theme="dark"] .page-item.disabled .page-link { background:#1a1f2e; color:#64748b; }
        [data-bs-theme="dark"] .border, [data-bs-theme="dark"] .border-top, [data-bs-theme="dark"] .border-bottom { border-color:#334155 !important; }
        [data-bs-theme="dark"] .badge.bg-light { background:#334155 !important; color:#e2e8f0 !important; }
        [data-bs-theme="dark"] .badge.bg-secondary { background:#475569 !important; }
        [data-bs-theme="dark"] .modal-content { background:#1a1f2e; color:#cbd5e1; }
        [data-bs-theme="dark"] .img-thumbnail { background:#1a1f2e; border-color:#334155; }
        [data-bs-theme="dark"] .pos-results { background:#1a1f2e; }
        [data-bs-theme="dark"] .pos-results .item { border-color:#334155; }
        [data-bs-theme="dark"] .pos-results .item:hover { background:#243144; }
        [data-bs-theme="dark"] #pelangganList { background:#1a1f2e !important; color:#cbd5e1; border-color:#334155 !important; }
        [data-bs-theme="dark"] #pelangganList > div { color:#cbd5e1 !important; border-color:#334155 !important; }
        [data-bs-theme="dark"] #pelangganList > div:hover { background:#243144; }
        [data-bs-theme="dark"] #lookupResults { background:#1a1f2e !important; color:#cbd5e1; border-color:#334155 !important; }
        [data-bs-theme="dark"] #lookupResults > div { color:#cbd5e1 !important; border-color:#334155 !important; }
        [data-bs-theme="dark"] #lookupResults > div:hover { background:#243144; }
        [data-bs-theme="dark"] #searchResults { background:#1a1f2e !important; color:#cbd5e1; border-color:#334155 !important; }
    </style>
    @stack('styles')
</head>
<body>
@php
    $u = auth()->user();
    $isAdmin = $u?->isAdmin();
    $isKasir = $u?->isKasir();
    $isGudang = $u?->isGudang();
@endphp
<div class="d-flex">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="sidebar px-3" id="sidebar">
        <div class="sidebar-header text-center mb-3 mt-2 d-flex justify-content-between align-items-center px-2">
            <span class="brand text-white sidebar-brand-full"><i class="fas fa-store text-primary me-2"></i> TOKOPINTAR</span>
            <span class="brand text-white sidebar-brand-mini d-none"><i class="fas fa-store text-primary"></i></span>
            <i class="fas fa-times d-lg-none text-secondary" id="closeSidebar" style="cursor:pointer;font-size:1.2rem;"></i>
            <button type="button" class="btn btn-sm p-0 d-none d-lg-inline-flex text-secondary border-0" id="collapseSidebar" style="cursor:pointer;background:transparent;font-size:1.1rem;" title="Sembunyikan menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <p class="menu-label text-uppercase">Main</p>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-border-all"></i> Dashboard
        </a>
        <a href="{{ route('panduan.index') }}" class="{{ request()->routeIs('panduan.*') ? 'active' : '' }}">
            <i class="fas fa-book-open"></i> Panduan
        </a>

        @if ($isAdmin || $isKasir)
            <p class="menu-label text-uppercase mt-3">Penjualan</p>
            <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.*') ? 'active' : '' }}">
                <i class="fas fa-cash-register"></i> Kasir
            </a>
            <a href="{{ route('penjualan.index') }}" class="{{ request()->routeIs('penjualan.*') ? 'active' : '' }}">
                <i class="fas fa-receipt"></i> Riwayat Jualan
            </a>
            <a href="{{ route('pelanggan.index') }}" class="{{ request()->routeIs('pelanggan.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Pelanggan
            </a>
        @endif

        @if ($isAdmin || $isGudang)
            <p class="menu-label text-uppercase mt-3">Stok Barang</p>
            <a href="{{ route('barang.index') }}" class="{{ request()->routeIs('barang.*') ? 'active' : '' }}">
                <i class="fas fa-box"></i> Daftar Barang
            </a>
            <a href="{{ route('supplier.index') }}" class="{{ request()->routeIs('supplier.*') ? 'active' : '' }}">
                <i class="fas fa-truck"></i> Pemasok
            </a>
            <a href="{{ route('pembelian.index') }}" class="{{ request()->routeIs('pembelian.*') ? 'active' : '' }}">
                <i class="fas fa-truck-loading"></i> Barang Masuk
            </a>
            <a href="{{ route('mutasi.index') }}" class="{{ request()->routeIs('mutasi.*') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt"></i> Penyesuaian Stok
            </a>
            <a href="{{ route('expiry.index') }}" class="{{ request()->routeIs('expiry.*') ? 'active' : '' }}">
                <i class="fas fa-clock"></i> Cek Kadaluarsa
            </a>
        @endif

        @if ($isAdmin)
            <p class="menu-label text-uppercase mt-3">Pengaturan</p>
            <a href="{{ route('kategori.index') }}" class="{{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                <i class="fas fa-tags"></i> Kategori Barang
            </a>
            <a href="{{ route('pengeluaran.index') }}" class="{{ request()->routeIs('pengeluaran.*') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave"></i> Biaya Operasional
            </a>
            <a href="{{ route('insight.index') }}" class="{{ request()->routeIs('insight.*') ? 'active' : '' }}">
                <i class="fas fa-brain"></i> Saran Toko
            </a>
            <a href="{{ route('customer-insight.index') }}" class="{{ request()->routeIs('customer-insight.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Analisa Pelanggan
            </a>
            <a href="{{ route('anomaly.index') }}" class="{{ request()->routeIs('anomaly.*') ? 'active' : '' }}">
                <i class="fas fa-shield-alt"></i> Deteksi Anomali
            </a>
            <a href="{{ route('advanced.rules.index') }}" class="{{ request()->routeIs('advanced.rules.*') ? 'active' : '' }}">
                <i class="fas fa-link"></i> Aturan Asosiasi
            </a>
            <a href="{{ route('advanced.stock') }}" class="{{ request()->routeIs('advanced.stock') ? 'active' : '' }}">
                <i class="fas fa-magic-wand-sparkles"></i> Optimal Stock
            </a>
            <a href="{{ route('advanced.cannibal') }}" class="{{ request()->routeIs('advanced.cannibal') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt"></i> Kanibalisasi
            </a>
            <a href="{{ route('advanced.pareto') }}" class="{{ request()->routeIs('advanced.pareto') ? 'active' : '' }}">
                <i class="fas fa-trophy"></i> Pareto Monitor
            </a>
            <a href="{{ route('pricing.simulator') }}" class="{{ request()->routeIs('pricing.*') ? 'active' : '' }}">
                <i class="fas fa-calculator"></i> Simulator Harga
            </a>
            <a href="{{ route('competitor.index') }}" class="{{ request()->routeIs('competitor.*') ? 'active' : '' }}">
                <i class="fas fa-balance-scale"></i> Harga Kompetitor
            </a>
            <a href="{{ route('bundle.index') }}" class="{{ request()->routeIs('bundle.*') ? 'active' : '' }}">
                <i class="fas fa-box-open"></i> Bundle Generator
            </a>
            <a href="{{ route('laporan.laba.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Laporan Untung
            </a>
        @endif

        <hr class="text-secondary mt-2 mx-3" style="opacity:.2;">

        <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="fas fa-user"></i> Profil
        </a>
        <a href="#" id="themeToggle" role="button">
            <i class="fas fa-moon" id="themeIcon"></i> <span id="themeLabel">Mode Gelap</span>
        </a>
        <a href="#" class="text-danger mt-1" id="logoutBtn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>

    <div class="main-content flex-grow-1">
        <nav class="navbar navbar-expand-lg px-4 py-3 d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <button class="btn btn-light d-lg-none me-3 shadow-sm border-0" id="sidebarToggle" aria-label="Toggle menu" style="border-radius:8px;">
                    <i class="fas fa-bars text-dark"></i>
                </button>
                <span class="navbar-brand mb-0 fw-bold d-none d-sm-block">@yield('page_title', 'Dashboard')</span>
            </div>
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center text-end">
                    <div class="me-2 d-none d-md-block">
                        <span class="d-block fw-bold text-dark" style="font-size:13px;line-height:1;">{{ $u->name }}</span>
                        <span class="text-muted text-capitalize" style="font-size:11px;">{{ $u->role }}</span>
                    </div>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&background=4361ee&color=fff" class="rounded-circle shadow-sm" width="38" alt="Avatar {{ $u->name }}">
                </div>
            </div>
        </nav>

        <x-flash />

        @yield('content')
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    $(function () {
        $('#sidebarToggle').on('click', () => { $('#sidebar').addClass('show'); $('#sidebarOverlay').addClass('show'); });
        $('#closeSidebar, #sidebarOverlay').on('click', () => { $('#sidebar').removeClass('show'); $('#sidebarOverlay').removeClass('show'); });
        $('#logoutBtn').on('click', function (e) {
            e.preventDefault();
            if (confirm('Yakin keluar dari TOKOPINTAR?')) document.getElementById('logout-form').submit();
        });

        if (localStorage.getItem('sidebarCollapsed') === '1') {
            $('#sidebar').addClass('collapsed');
        }
        $('#collapseSidebar').on('click', function () {
            $('#sidebar').toggleClass('collapsed');
            localStorage.setItem('sidebarCollapsed', $('#sidebar').hasClass('collapsed') ? '1' : '0');
        });
        function applyThemeIcon() {
            const t = document.documentElement.getAttribute('data-bs-theme');
            $('#themeIcon').attr('class', t === 'dark' ? 'fas fa-sun' : 'fas fa-moon');
            $('#themeLabel').text(t === 'dark' ? 'Mode Terang' : 'Mode Gelap');
        }
        applyThemeIcon();
        $('#themeToggle').on('click', function (e) {
            e.preventDefault();
            const cur = document.documentElement.getAttribute('data-bs-theme') || 'light';
            const next = cur === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', next);
            localStorage.setItem('theme', next);
            applyThemeIcon();
        });
    });
</script>
@stack('scripts')
</body>
</html>
