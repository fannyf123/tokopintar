# TOKOPINTAR

Sistem manajemen toko UMKM pintar — POS, inventory, FEFO, AI insight lokal, laporan laba.

## Fitur

- **POS Kasir** dengan scan barcode pakai kamera HP (html5-qrcode), hotkey F2/F4/ESC, struk 80mm
- **Inventory FEFO** — first-expired-first-out otomatis saat penjualan, audit trail tiap mutasi
- **Pembelian** dengan batch tracking + tanggal kadaluarsa
- **Mutasi stok** — adjustment, retur, rusak, hilang, expired (semua via service + audit)
- **Manajemen kadaluarsa** — highlight merah/kuning/hijau, buang otomatis ke mutasi expired
- **AI Insight Lokal Deterministik (no LLM, no API):**
  - Velocity score per barang (window 30 hari)
  - Days-of-supply
  - Klasifikasi FAST/NORMAL/SLOW/DEAD/NEW
  - ABC analysis (kontribusi omzet 90 hari)
  - Forecast 7 hari (Simple Exponential Smoothing α=0.3)
  - **Subsidi silang / cross-subsidization:** klasifikasi LOSS_LEADER / BALANCED / PROFIT_DRIVER + basket co-occurrence untuk rekomendasi bundling
- **Laporan laba** — harian/mingguan/bulanan/tahunan, chart, ekspor PDF & CSV
- **Dashboard** — kartu omzet, stok rendah, near-expiry, top produk, fast/dead mover

## Role

| Role | Akses |
|---|---|
| admin | semua modul |
| kasir | POS, riwayat penjualan, pelanggan |
| gudang | barang, supplier, pembelian, mutasi, expiry |

## Akun Demo (setelah seed)

| Username | Password |
|---|---|
| admin | admin123 |
| kasir | kasir123 |
| gudang | gudang123 |

## Tech Stack

- PHP 8.3 + Laravel 13
- SQLite (lokal) / PostgreSQL (production)
- Tailwind CSS v4 + Vite 8
- Chart.js (CDN)
- html5-qrcode (CDN, untuk barcode scanner HP)
- DomPDF + Sanctum + DataTables paket terinstall
- PHPUnit 12 — 25 tests, 119 assertions

## Setup Lokal

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
npm install && npm run build
php artisan serve --port=8080
```

Buka `http://127.0.0.1:8080`.

## Subsidi Silang — Cara Kerja

Konsep di retail bernama **loss leader pricing** atau cross-subsidization. TOKOPINTAR mengklasifikasi barang berdasarkan margin:

- **LOSS_LEADER** (margin ≤ 5%) — barang pemikat, dijual mendekati modal untuk narik traffic
- **PROFIT_DRIVER** (margin ≥ 25%) — penyumbang profit utama
- **BALANCED** (di antaranya)

Lalu menghitung *basket co-occurrence* (mana yang sering dibeli bareng dalam transaksi yang sama) selama 90 hari. Setiap loss-leader dipasangkan dengan profit-driver yang paling sering muncul di basket-nya — keluar sebagai rekomendasi bundling.

Hasilnya di `/insight`: kolom strategy + saran spesifik (misal: "Loss leader 3.5%. Sering dibeli bareng: Aqua 600ml, Roti Sari Roti. Tampilkan dekat barang ini & buat bundling.").

Semua perhitungan deterministik, no random, no LLM.

## Scheduled Commands

```bash
php artisan tokopintar:recompute-insight   # daily 02:00
php artisan tokopintar:check-expiry        # daily 07:00
```

## Deploy ke Render

Project sudah berisi `Dockerfile` + `render.yaml`. Setting:
- Free tier, region Singapore
- Shared postgres dengan project lain via `DB_SCHEMA=tokopintar` (Postgres schema isolation)
- Auto migrate + seed saat startup

## Test

```bash
php artisan test
# 25 tests, 119 assertions, all passing
```

Termasuk smoke test yang mengetes setiap halaman GET, POS full-flow + FEFO, pembelian → terima → batch, mutasi audit trail, insight regeneration, laporan PDF & CSV, role-based access.
