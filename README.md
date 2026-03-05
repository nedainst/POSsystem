# 🧾 NedaPOS — Point of Sale Web Application

Aplikasi Point of Sale (POS) berbasis web yang dibangun dengan Laravel 12. Dirancang untuk membantu usaha kecil hingga menengah (warung, kafe, toko) dalam mengelola transaksi penjualan, stok barang, dan pelanggan secara efisien melalui antarmuka yang modern dan responsif.

## ✨ Fitur

### Kasir (POS)
- Antarmuka kasir real-time dengan pencarian produk
- Filter produk berdasarkan kategori
- Keranjang belanja interaktif dengan kontrol jumlah
- Metode pembayaran: Cash, Kartu, E-Wallet, Transfer
- Diskon (nominal & persentase) dan pajak
- Kalkulasi kembalian otomatis
- Cetak struk/receipt

### Manajemen Produk & Kategori
- CRUD produk lengkap dengan gambar, SKU, dan barcode
- Harga beli & harga jual dengan kalkulasi profit
- Kategori produk dengan ikon dan warna kustom
- Toggle aktif/nonaktif produk

### Inventaris & Stok
- Monitoring stok real-time
- Peringatan stok rendah dan stok habis
- Penyesuaian stok manual (masuk, keluar, adjustment)
- Riwayat pergerakan stok lengkap

### Pesanan & Pelanggan
- Riwayat pesanan dengan filter status dan tanggal
- Detail pesanan dan pembatalan dengan pengembalian stok
- Manajemen data pelanggan
- Tracking total pembelian pelanggan

### Laporan
- Laporan penjualan harian/periodik
- Laporan produk terlaris
- Dashboard dengan ringkasan statistik

### Pengaturan
- Kustomisasi nama & tampilan aplikasi
- Tema warna dinamis (primary, accent, sidebar)
- Pengaturan struk dan POS

## 🛠️ Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 12, PHP 8.2 |
| Frontend | Tailwind CSS 4, Blade Templates |
| Database | SQLite |
| Bundler | Vite 7 |
| Icons | Lucide Icons |
| Charts | Chart.js |
| Alerts | SweetAlert2 |

## 🚀 Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM

### Langkah-langkah

```bash
# Clone repository
git clone https://github.com/username/POSsystem.git
cd POSsystem

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database (SQLite)
touch database/database.sqlite
php artisan migrate --seed

# Build assets
npm run build

# Jalankan server
php artisan serve
```

Buka browser dan akses `http://localhost:8000`

## 🔐 Akun Default

| Role | Email | Password |
|---|---|---|
| Admin | `admin@pos.com` | `password` |
| Kasir | `kasir@pos.com` | `password` |

## 📸 Screenshots

> _Coming soon_

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).