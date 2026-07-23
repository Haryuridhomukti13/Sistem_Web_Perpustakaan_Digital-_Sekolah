<?php
session_start();
include 'koneksi.php';

// 1. Proteksi Hak Akses Admin/Pustakawan (Meniru logika pinjam.php kamu)
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'pustakawan')) {
    echo "<script>alert('Akses khusus Admin/Pustakawan!'); window.location.href='index.php';</script>";
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];
$hari_ini = date('Y-m-d');
$pesan_sukses = "";
$pesan_error = "";

// 2. Logika ketika Kamera HP Berhasil Menangkap Barcode Buku
if (isset($_POST['barcode_scanned'])) {
    $isbn_atau_id = mysqli_real_escape_string($conn, $_POST['barcode_data']);
    $id_pengguna = mysqli_real_escape_string($conn, $_POST['id_pengguna']);
    $tgl_pinjam = $hari_ini;
    $tgl_kembali = date('Y-m-d', strtotime('+' . (int) pengaturan('lama_pinjam_hari', 7) . ' days')); // Batas kembali otomatis sesuai Pengaturan Sistem

    // Mencari buku berdasarkan kolom 'isbn' atau 'id_buku' (Sesuai dengan logika tambah_buku.php)
    $cek_buku = mysqli_query($conn, "SELECT id_buku, judul, jumlah_tersedia FROM buku WHERE isbn = '$isbn_atau_id' OR id_buku = '$isbn_atau_id'");
    
    if (mysqli_num_rows($cek_buku) > 0) {
        $data_buku = mysqli_fetch_assoc($cek_buku);
        $id_buku = $data_buku['id_buku'];
        $judul_buku = $data_buku['judul'];

        if ($data_buku['jumlah_tersedia'] > 0) {
            // Jalankan transaksi insert data peminjaman ke database
            $insert = mysqli_query($conn, "INSERT INTO pinjam (id_pengguna, id_buku, tanggal_pinjam, tanggal_kembali, status) 
                                           VALUES ('$id_pengguna', '$id_buku', '$tgl_pinjam', '$tgl_kembali', 'dipinjam')");
            
            // Kurangi stok jumlah_tersedia buku fisik
            $update_stok = mysqli_query($conn, "UPDATE buku SET jumlah_tersedia = jumlah_tersedia - 1 WHERE id_buku = '$id_buku'");
            
            if ($insert && $update_stok) {
                // 3. INTEGRASI LOG AKTIVITAS (Otomatis tercatat ke halaman log.php kamu)
                $id_operator = $user['id_pengguna'];
                $aksi_log = "Melakukan transaksi pinjam via scan barcode buku '" . mysqli_real_escape_string($conn, $judul_buku) . "'.";
                mysqli_query($conn, "INSERT INTO log_aktivitas (id_pengguna, aksi, created_at) VALUES ('$id_operator', '$aksi_log', NOW())");

                $pesan_sukses = "Buku '" . htmlspecialchars($judul_buku) . "' berhasil dipinjam! Batas pengembalian: " . date('d M Y', strtotime($tgl_kembali));
            } else {
                $pesan_error = "Terjadi kesalahan sistem saat memproses transaksi.";
            }
        } else {
            $pesan_error = "Maaf, stok fisik buku '" . htmlspecialchars($judul_buku) . "' sedang kosong / habis dipinjam.";
        }
    } else {
        $pesan_error = "Kode Barcode / ISBN '" . htmlspecialchars($isbn_atau_id) . "' tidak terdaftar dalam sistem perpustakaan.";
    }
}

// Mengambil daftar anggota aktif untuk pilihan dropdown (Sama seperti komponen pinjam.php kamu)
$query_anggota = mysqli_query($conn, "SELECT id_pengguna, nama, no_anggota FROM pengguna WHERE role='anggota' AND status='aktif' ORDER BY nama ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Barcode Peminjaman | E-Perpus SMANTEN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="js/scan_pinjam.js"></script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 font-sans text-slate-800 dark:text-slate-100 transition-all">

    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        <div class="w-full max-w-md bg-white dark:bg-darkCard rounded-2xl shadow-xl p-6 border border-slate-100 dark:border-slate-800">
            
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-brand">
                        <i class="fa fa-camera text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold">Kamera Barcode Buku</h1>
                        <p class="text-xs text-slate-400">Scan Instan lewat HP Pustakawan</p>
                    </div>
                </div>
                <a href="buku.php" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-all">
                    <i class="fa fa-times text-lg"></i>
                </a>
            </div>

            <?php if (!empty($pesan_sukses)): ?>
                <div class="mb-4 p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 text-emerald-600 dark:text-emerald-400 text-sm rounded-xl flex items-start gap-3">
                    <i class="fa fa-check-circle mt-0.5 text-base"></i>
                    <span><?php echo $pesan_sukses; ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($pesan_error)): ?>
                <div class="mb-4 p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/50 text-rose-600 dark:text-rose-400 text-sm rounded-xl flex items-start gap-3">
                    <i class="fa fa-exclamation-circle mt-0.5 text-base"></i>
                    <span><?php echo $pesan_error; ?></span>
                </div>
            <?php endif; ?>

            <form id="form-scan" method="POST" action="">
                <div class="mb-5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        <i class="fa fa-user mr-1"></i> Pilih Anggota Peminjam
                    </label>
                    <select name="id_pengguna" id="id_pengguna" required class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 border-0 rounded-xl focus:ring-2 focus:ring-brand text-sm outline-none transition-all">
                        <option value="">-- Cari Nama Anggota --</option>
                        <?php while($agt = mysqli_fetch_assoc($query_anggota)): ?>
                            <option value="<?php echo $agt['id_pengguna']; ?>">
                                <?php echo htmlspecialchars($agt['nama']) . " (" . htmlspecialchars($agt['no_anggota']) . ")"; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        <i class="fa fa-qrcode mr-1"></i> Jendela Bidik Kamera
                    </label>
                    
                    <div id="wrapper-kamera" class="overflow-hidden rounded-xl bg-slate-900 border border-slate-200 dark:border-slate-700 relative shadow-inner min-h-[220px] flex items-center justify-center">
                        <div id="reader" class="w-full"></div>
                    </div>
                </div>

                <input type="hidden" name="barcode_data" id="barcode_data">
                <input type="hidden" name="barcode_scanned" value="1">
            </form>

            <div class="text-center">
                <p class="text-xs text-slate-400 italic">Pilih nama anggota terlebih dahulu di atas, kemudian dekatkan kode barcode buku fisik ke area tengah kotak kamera HP.</p>
            </div>

        </div>
    </div>

    <script src="js/scan_pinjam_2.js"></script>
</body>
</html>

