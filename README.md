# Digilib2 – Sistem Perpustakaan Digital Sekolah

Digilib2 adalah aplikasi web untuk mengelola perpustakaan sekolah secara digital,
dibangun menggunakan PHP native dan MySQL. Sistem ini mencakup sisi publik
(katalog buku, berita/pengumuman) dan sisi admin (dashboard, manajemen buku,
anggota, peminjaman, hingga cetak kartu anggota).

## ✨ Fitur Utama
- **Katalog Buku Publik** – Menampilkan daftar buku dengan sampul, judul, dan status ketersediaan
- **Manajemen Buku** – Tambah, edit, hapus, dan lihat detail buku
- **Manajemen Anggota** – Tambah, edit, hapus, dan lihat data anggota
- **Peminjaman & Pengembalian** – Termasuk fitur scan (barcode/QR) untuk transaksi cepat
- **Cetak Kartu Anggota**
- **Berita & Pengumuman** – Dengan halaman detail dan pagination
- **Log Aktivitas** – Pencatatan aktivitas sistem
- **Pengaturan Terpusat** – Nama sekolah, logo, batas pinjam, dsb dapat diatur lewat panel admin
- **Mode Maintenance per Modul** – Modul peminjaman, berita, dan katalog bisa
  dinonaktifkan sementara secara independen, dengan pengecualian untuk role
  tertentu (admin/pustakawan)
- **Autentikasi & Sesi** – Login, logout, dan proteksi sesi

## 🛠️ Teknologi
- PHP (native, mysqli)
- MySQL/MariaDB
- HTML, CSS, JavaScript (vanilla)

## 📁 Struktur Proyek
digilib2/
├── config.php # Konfigurasi DB & pengaturan aplikasi terpusat
├── koneksi.php # Koneksi database (mengarah ke config.php)
├── index.php # Halaman utama/beranda
├── dashboard.php # Dashboard admin
├── buku.php, tambah_buku.php, edit_buku.php, hapus_buku.php, detail_buku.php
├── anggota.php, tambah_anggota.php, edit_anggota.php, hapus_anggota.php
├── pinjam.php, scan_pinjam.php, kembali_admin.php, proses_kembali.php
├── berita.php, detail_berita.php
├── cetak_kartu.php
├── pengaturan.php
├── 📁css/, 📁js/code jsnya, 📁img/

## ⚙️ Instalasi
1. Clone repository ini ke folder server lokal (misalnya `htdocs` untuk XAMPP)
2. Buat database MySQL bernama `digilib2`
3. Import struktur database (jika file `.sql` disertakan)
4. Sesuaikan kredensial database di `config.php` bila diperlukan
5. Jalankan melalui `http://localhost/digilib2`
