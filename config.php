<?php
/**
 * config.php
 * -----------------------------------------------------------------
 * File pengaturan TERPUSAT untuk seluruh sistem Digilib2.
 * Semua file lain (koneksi.php, index.php, dashboard.php, dst) cukup
 * memanggil file ini untuk mendapatkan koneksi database + pengaturan
 * aplikasi (nama sekolah, logo, batas pinjam, dsb).
 *
 * Cara pakai di file lain:
 *   require_once __DIR__ . '/config.php';
 *   echo pengaturan('nama_sekolah');           // ambil 1 nilai
 *   $semua = $GLOBALS['PENGATURAN'];           // ambil semua nilai (array)
 * -----------------------------------------------------------------
 */

// ============== 1. KONFIGURASI DATABASE ==============
// Ubah bagian ini kalau pindah server / ganti kredensial database.
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'digilib2');
}

// ============== 2. KONEKSI DATABASE ==============
if (empty($conn) || !($conn instanceof mysqli)) {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        die('Koneksi database gagal: ' . mysqli_connect_error());
    }
    mysqli_set_charset($conn, 'utf8mb4');
}

// ============== 3. MUAT PENGATURAN DARI DATABASE ==============
// Nilai default dipakai jika tabel/kolom belum ada, biar sistem tidak error.
$GLOBALS['PENGATURAN'] = [
    'nama_aplikasi'           => 'DIGILIB',
    'nama_sekolah'            => 'SMAN 10 KOTA HARAPAN BANGSA',
    'tagline'                 => 'Digital Library',
    'deskripsi_beranda'       => 'Temukan berbagai koleksi buku terbaik untuk menunjang pembelajaran dan wawasanmu di perpustakaan digital kami.',
    'alamat'                  => '',
    'telepon'                 => '',
    'email_kontak'            => '',
    'logo'                    => 'logo.png',
    'jumlah_buku_per_halaman' => '12',
    'lama_pinjam_hari'        => '7',
    'maks_pinjam_buku'        => '3',
    'maintenance_peminjaman'  => '0',
    'maintenance_berita'      => '0',
    'maintenance_katalog'     => '0',
];

$cek_tabel = @mysqli_query($conn, "SHOW TABLES LIKE 'pengaturan'");
if ($cek_tabel && mysqli_num_rows($cek_tabel) > 0) {
    // Bersihkan data rusak/sampah dari tabel pengaturan:
    // - baris dengan key_pengaturan kosong (bug lama, checkbox maintenance tidak pernah tersimpan)
    // - baris key 'mode_maintenance' lama (digantikan 3 key per-modul di bawah)
    // - baris sampah hasil testing (key 'a')
    mysqli_query($conn, "DELETE FROM pengaturan WHERE key_pengaturan = '' OR key_pengaturan = 'mode_maintenance' OR key_pengaturan = 'a'");

    $hasil = mysqli_query($conn, "SELECT key_pengaturan, value_pengaturan FROM pengaturan");
    if ($hasil) {
        while ($baris = mysqli_fetch_assoc($hasil)) {
            $GLOBALS['PENGATURAN'][$baris['key_pengaturan']] = $baris['value_pengaturan'];
        }
    }
}

/**
 * Ambil satu nilai pengaturan dengan aman.
 * @param string $key
 * @param mixed $default nilai jika key tidak ditemukan
 * @return mixed
 */
if (!function_exists('pengaturan')) {
    function pengaturan($key, $default = '') {
        return $GLOBALS['PENGATURAN'][$key] ?? $default;
    }
}

// ============== 4. MODE MAINTENANCE PER MODUL ==============
// Maintenance sekarang bisa diaktifkan terpisah per modul (bukan seluruh
// situs sekaligus): 'peminjaman', 'berita', 'katalog'. Admin yang sedang
// login selalu tetap bisa akses meski modul terkait sedang maintenance.
//
// Cara pakai di file modul terkait, taruh persis setelah include koneksi.php:
//   if (cek_maintenance('peminjaman')) { tampilkan_maintenance('Peminjaman & Pengembalian'); }
// Untuk halaman khusus staf (mis. pinjam.php, scan_pinjam.php) yang memang
// dipakai admin/pustakawan buat minjemin buku ke anggota yang datang langsung
// ke perpus, staf tetap harus bisa akses walau modul sedang maintenance:
//   if (cek_maintenance('peminjaman', ['admin', 'pustakawan'])) { ... }
if (!function_exists('cek_maintenance')) {
    function cek_maintenance($modul, $peran_dikecualikan = ['admin']) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (pengaturan('maintenance_' . $modul) != '1') {
            return false;
        }
        $sedang_login = $_SESSION['user'] ?? null;
        $peran        = $sedang_login['role'] ?? null;
        $dikecualikan = $sedang_login && in_array($peran, $peran_dikecualikan, true);
        return !$dikecualikan;
    }
}

if (!function_exists('tampilkan_maintenance')) {
    function tampilkan_maintenance($nama_modul = 'Halaman ini') {
        echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'>
              <title>Maintenance</title>
              <style>body{font-family:sans-serif;background:#0F172A;color:#fff;display:flex;
              align-items:center;justify-content:center;height:100vh;margin:0;text-align:center}
              div{max-width:420px;padding:24px}</style></head><body>
              <div><h2>🛠️ Sedang dalam pengembangan</h2>
              <p>" . htmlspecialchars($nama_modul) . " pada Perpustakaan " . htmlspecialchars(pengaturan('nama_sekolah')) . " sedang dalam pemeliharaan.
              Silakan datang keperpustakaan sesuai dengan jam yang sudah tertera</p>
              <p style='margin-top:16px'><a href='dashboard.php' style='color:#93c5fd'>&larr; Kembali ke Beranda</a></p>
              </div></body></html>";
        exit;
    }
}