<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
</head>
<body class="bg-gray-100 min-h-screen">
@php
    $u = auth()->user();
    $isAdmin = $u?->isAdmin();
    $isKasir = $u?->isKasir();
    $isGudang = $u?->isGudang();
@endphp
<div class="flex min-h-screen">
    <aside class="w-60 bg-indigo-700 text-white p-4 hidden md:block">
        <a href="{{ route('dashboard') }}" class="text-xl font-bold block mb-6">TOKOPINTAR</a>
        <nav class="space-y-1 text-sm">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded hover:bg-indigo-600">Dashboard</a>
            @if ($isAdmin || $isKasir)
                <div class="text-xs uppercase text-indigo-300 mt-3 px-3">Penjualan</div>
                <a href="{{ route('pos.index') }}" class="block px-3 py-2 rounded hover:bg-indigo-600">POS / Kasir</a>
                <a href="{{ route('penjualan.index') }}" class="block px-3 py-2 rounded hover:bg-indigo-600">Riwayat Penjualan</a>
                <a href="{{ route('pelanggan.index') }}" class="block px-3 py-2 rounded hover:bg-indigo-600">Pelanggan</a>
            @endif
            @if ($isAdmin || $isGudang)
                <div class="text-xs uppercase text-indigo-300 mt-3 px-3">Inventory</div>
                <a href="{{ route('barang.index') }}" class="block px-3 py-2 rounded hover:bg-indigo-600">Barang</a>
                <a href="{{ route('supplier.index') }}" class="block px-3 py-2 rounded hover:bg-indigo-600">Supplier</a>
                <a href="{{ route('pembelian.index') }}" class="block px-3 py-2 rounded hover:bg-indigo-600">Pembelian</a>
                <a href="{{ route('mutasi.index') }}" class="block px-3 py-2 rounded hover:bg-indigo-600">Mutasi Stok</a>
                <a href="{{ route('expiry.index') }}" class="block px-3 py-2 rounded hover:bg-indigo-600">Kadaluarsa</a>
            @endif
            @if ($isAdmin)
                <div class="text-xs uppercase text-indigo-300 mt-3 px-3">Admin</div>
                <a href="{{ route('kategori.index') }}" class="block px-3 py-2 rounded hover:bg-indigo-600">Kategori</a>
                <a href="{{ route('pengeluaran.index') }}" class="block px-3 py-2 rounded hover:bg-indigo-600">Pengeluaran</a>
                <a href="{{ route('insight.index') }}" class="block px-3 py-2 rounded hover:bg-indigo-600">Insight AI</a>
                <a href="{{ route('laporan.laba.index') }}" class="block px-3 py-2 rounded hover:bg-indigo-600">Laporan Laba</a>
            @endif
        </nav>
    </aside>
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow px-4 py-3 flex items-center justify-between">
            <div class="text-sm text-gray-500">@yield('breadcrumb', 'Dashboard')</div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-700">{{ $u->name }} <span class="text-xs text-gray-500">({{ $u->role }})</span></span>
                <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 hover:text-indigo-600">Profil</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">@csrf
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">Logout</button>
                </form>
            </div>
        </header>
        <main class="p-6">
            <x-flash />
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
