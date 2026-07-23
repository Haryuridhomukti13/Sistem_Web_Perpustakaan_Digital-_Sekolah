<?php
session_start();
include 'koneksi.php';

// Proteksi: hanya admin yang boleh mengakses halaman ini
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: index.php");
    exit;
}
$user = $_SESSION['user'];
$role = $user['role'];

$pesan = '';
$pesan_tipe = ''; // 'sukses' atau 'gagal'

// ============== PROSES SIMPAN PENGATURAN ==============
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Daftar field teks yang boleh diubah lewat form ini
    $field_teks = [
        'nama_aplikasi', 'nama_sekolah', 'tagline', 'deskripsi_beranda',
        'alamat', 'telepon', 'email_kontak',
        'jumlah_buku_per_halaman', 'lama_pinjam_hari', 'maks_pinjam_buku',
    ];

    foreach ($field_teks as $key) {
        if (isset($_POST[$key])) {
            $nilai = mysqli_real_escape_string($conn, $_POST[$key]);
            mysqli_query($conn, "
                INSERT INTO pengaturan (key_pengaturan, value_pengaturan)
                VALUES ('$key', '$nilai')
                ON DUPLICATE KEY UPDATE value_pengaturan = '$nilai'
            ");
        }
    }

    // Mode maintenance per-modul, masing-masing dikirim sebagai checkbox (ada/tidak ada di POST)
    $modul_maintenance = ['maintenance_peminjaman', 'maintenance_berita', 'maintenance_katalog'];
    foreach ($modul_maintenance as $key) {
        $nilai = isset($_POST[$key]) ? '1' : '0';
        mysqli_query($conn, "
            INSERT INTO pengaturan (key_pengaturan, value_pengaturan)
            VALUES ('$key', '$nilai')
            ON DUPLICATE KEY UPDATE value_pengaturan = '$nilai'
        ");
    }

    // Upload logo baru (opsional)
    if (!empty($_FILES['logo']['name'])) {
        $ekstensi_diizinkan = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
        $ekstensi = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));

        if (in_array($ekstensi, $ekstensi_diizinkan)) {
            $nama_file = 'logo_' . time() . '.' . $ekstensi;
            $target_dir = __DIR__ . '/img/logo/';
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_dir . $nama_file)) {
                mysqli_query($conn, "
                    INSERT INTO pengaturan (key_pengaturan, value_pengaturan)
                    VALUES ('logo', '$nama_file')
                    ON DUPLICATE KEY UPDATE value_pengaturan = '$nama_file'
                ");
            } else {
                $pesan = 'Gagal mengunggah file logo.';
                $pesan_tipe = 'gagal';
            }
        } else {
            $pesan = 'Format logo tidak didukung. Gunakan JPG, PNG, SVG, atau WEBP.';
            $pesan_tipe = 'gagal';
        }
    }

    // Catat ke log aktivitas jika tabelnya ada
    $cek_log = @mysqli_query($conn, "SHOW TABLES LIKE 'log_aktivitas'");
    if ($cek_log && mysqli_num_rows($cek_log) > 0) {
        $id_pengguna = (int) $user['id_pengguna'];
        mysqli_query($conn, "
            INSERT INTO log_aktivitas (id_pengguna, aktivitas, ip_address, created_at)
            VALUES ('$id_pengguna', 'UBAH PENGATURAN|Admin memperbarui pengaturan sistem', '{$_SERVER['REMOTE_ADDR']}', UNIX_TIMESTAMP())
        ");
    }

    if ($pesan === '') {
        $pesan = 'Pengaturan berhasil disimpan.';
        $pesan_tipe = 'sukses';
    }

    // Muat ulang pengaturan terbaru dari database
    unset($GLOBALS['PENGATURAN']);
    if (file_exists('config.php')) {
        include 'config.php';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Sistem | <?= htmlspecialchars(function_exists('pengaturan') ? pengaturan('nama_aplikasi') : 'E-Perpus') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="js/pengaturan.js"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-900">

    <div class="max-w-4xl mx-auto px-6 py-10">

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <i class="fa fa-sliders text-brand"></i> Pengaturan Sistem
                </h1>
                <p class="text-sm text-slate-500 mt-1">Ubah identitas dan aturan umum sistem Digilib dari satu tempat.</p>
            </div>
            <a href="dashboard.php" class="text-sm font-semibold text-slate-500 hover:text-brand flex items-center gap-2">
                <i class="fa fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>

        <?php if ($pesan): ?>
            <div class="mb-6 px-4 py-3 rounded-xl text-sm font-medium
                <?= $pesan_tipe == 'sukses' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
                <?= htmlspecialchars($pesan) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6">

            <!-- Identitas Aplikasi -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h2 class="font-bold text-lg mb-4">Identitas Aplikasi</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Nama Aplikasi (sidebar)</label>
                        <input type="text" name="nama_aplikasi" value="<?= htmlspecialchars(function_exists('pengaturan') ? pengaturan('nama_aplikasi') : '') ?>"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Tagline</label>
                        <input type="text" name="tagline" value="<?= htmlspecialchars(function_exists('pengaturan') ? pengaturan('tagline') : '') ?>"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand focus:outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Nama Sekolah / Instansi</label>
                        <input type="text" name="nama_sekolah" value="<?= htmlspecialchars(function_exists('pengaturan') ? pengaturan('nama_sekolah') : '') ?>"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand focus:outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Deskripsi Beranda</label>
                        <textarea name="deskripsi_beranda" rows="3"
                                  class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand focus:outline-none"><?= htmlspecialchars(function_exists('pengaturan') ? pengaturan('deskripsi_beranda') : '') ?></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Logo</label>
                        <div class="flex items-center gap-4">
                            <img src="img/logo/<?= htmlspecialchars(function_exists('pengaturan') ? pengaturan('logo') : '') ?>" onerror="this.style.display='none'"
                                 class="h-14 w-14 object-contain rounded-lg border border-slate-200 bg-white">
                            <input type="file" name="logo" accept=".jpg,.jpeg,.png,.svg,.webp"
                                   class="text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-brand/10 file:text-brand file:font-semibold">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kontak -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h2 class="font-bold text-lg mb-4">Informasi Kontak</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Alamat</label>
                        <input type="text" name="alamat" value="<?= htmlspecialchars(function_exists('pengaturan') ? pengaturan('alamat') : '') ?>"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Telepon</label>
                        <input type="text" name="telepon" value="<?= htmlspecialchars(function_exists('pengaturan') ? pengaturan('telepon') : '') ?>"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Email Kontak</label>
                        <input type="email" name="email_kontak" value="<?= htmlspecialchars(function_exists('pengaturan') ? pengaturan('email_kontak') : '') ?>"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Aturan Peminjaman -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h2 class="font-bold text-lg mb-4">Aturan Sirkulasi &amp; Katalog</h2>
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Buku per halaman (katalog)</label>
                        <input type="number" min="1" name="jumlah_buku_per_halaman" value="<?= htmlspecialchars(function_exists('pengaturan') ? pengaturan('jumlah_buku_per_halaman') : '') ?>"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Lama pinjam (hari)</label>
                        <input type="number" min="1" name="lama_pinjam_hari" value="<?= htmlspecialchars(function_exists('pengaturan') ? pengaturan('lama_pinjam_hari') : '') ?>"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Maks. buku dipinjam/anggota</label>
                        <input type="number" min="1" name="maks_pinjam_buku" value="<?= htmlspecialchars(function_exists('pengaturan') ? pengaturan('maks_pinjam_buku') : '') ?>"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Mode Sistem -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h2 class="font-bold text-lg mb-4">Mode Maintenance per Modul</h2>
                <p class="text-xs text-slate-400 mb-4">Centang modul yang mau dikunci sementara. Admin tetap bisa akses modul yang sedang maintenance.</p>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="maintenance_peminjaman" value="1" <?= (function_exists('pengaturan') && pengaturan('maintenance_peminjaman') == '1') ? 'checked' : '' ?>
                               class="w-5 h-5 rounded text-brand focus:ring-brand">
                        <span class="text-sm font-medium text-slate-700">
                            Peminjaman &amp; Pengembalian (pinjam, buku_pinjam, scan_pinjam)
                        </span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="maintenance_berita" value="1" <?= (function_exists('pengaturan') && pengaturan('maintenance_berita') == '1') ? 'checked' : '' ?>
                               class="w-5 h-5 rounded text-brand focus:ring-brand">
                        <span class="text-sm font-medium text-slate-700">
                            Berita &amp; Pengumuman (berita, detail_berita)
                        </span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="maintenance_katalog" value="1" <?= (function_exists('pengaturan') && pengaturan('maintenance_katalog') == '1') ? 'checked' : '' ?>
                               class="w-5 h-5 rounded text-brand focus:ring-brand">
                        <span class="text-sm font-medium text-slate-700">
                            Katalog Buku (bagian katalog di katalog.php)
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-brand text-white font-bold px-6 py-3 rounded-xl shadow-md hover:bg-indigo-700 transition-all flex items-center gap-2">
                    <i class="fa fa-save"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</body>
</html>