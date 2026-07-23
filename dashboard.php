<?php
session_start();
include 'koneksi.php';

// Proteksi Login
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];
$id_pengguna = $user['id_pengguna'];

// Fungsi helper untuk URL Pagination (agar state pagination lain tidak hilang saat di-klik)
if (!function_exists('buildUrl')) {
    function buildUrl($key, $value) {
        $params = $_GET;
        $params[$key] = $value;
        return '?' . http_build_query($params);
    }
}

// --- LOGIKA AKSI VALIDASI PENGEMBALIAN DARI DASHBOARD (ADMIN) ---
if ($role == 'admin' && isset($_GET['aksi']) && $_GET['aksi'] == 'kembalikan' && isset($_GET['id'])) {
    $id_pinjam = mysqli_real_escape_string($conn, $_GET['id']);
    
    $cari_buku = mysqli_query($conn, "SELECT id_buku FROM pinjam WHERE id_pinjam = '$id_pinjam' AND status = 'dipinjam'");
    
    if (mysqli_num_rows($cari_buku) > 0) {
        $data_buku = mysqli_fetch_assoc($cari_buku);
        $id_buku = $data_buku['id_buku'];
        
        $update_status = mysqli_query($conn, "UPDATE pinjam SET status = 'dikembalikan', status_pengajuan = 'selesai' WHERE id_pinjam = '$id_pinjam'");
        $update_stok = mysqli_query($conn, "UPDATE buku SET jumlah_tersedia = jumlah_tersedia + 1 WHERE id_buku = '$id_buku'");
        
        if ($update_status && $update_stok) {
            header("Location: dashboard.php");
            exit;
        }
    }
}

// --- LOGIKA PROSES CRUD PENGUMUMAN & GALERI PRESTASI (KHUSUS ADMIN) ---
if ($role == 'admin' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // TAMBAH PENGUMUMAN / BERITA
    if (isset($_POST['tambah_pemberitahuan'])) {
        $judul = mysqli_real_escape_string($conn, $_POST['judul']);
        $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
        $isi = mysqli_real_escape_string($conn, $_POST['isi']);
        
        $sampul = 'default_berita.jpg';
        if (!empty($_FILES['sampul_berita']['name'])) {
            $sampul = time() . '_' . $_FILES['sampul_berita']['name'];
            $target_dir = __DIR__ . '/uploads/sampul_berita/';
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            move_uploaded_file($_FILES['sampul_berita']['tmp_name'], $target_dir . $sampul);
        }

        $berita_file = 'NULL';
        if (!empty($_FILES['berita']['name'])) {
            $nama_file = time() . '_' . $_FILES['berita']['name'];
            $target_berita_dir = __DIR__ . '/uploads/Berita/';
            if (!is_dir($target_berita_dir)) {
                mkdir($target_berita_dir, 0777, true);
            }
            move_uploaded_file($_FILES['berita']['tmp_name'], $target_berita_dir . $nama_file);
            $berita_file = "'$nama_file'";
        }

        mysqli_query($conn, "INSERT INTO pengumuman_dan_berita (judul, kategori, isi, sampul_berita, berita) VALUES ('$judul', '$kategori', '$isi', '$sampul', $berita_file)");
        header("Location: dashboard.php");
        exit;
    }
    
    // EDIT PENGUMUMAN / BERITA
    if (isset($_POST['edit_pemberitahuan'])) {
        $id_pem = mysqli_real_escape_string($conn, $_POST['id_pengumuman']);
        $judul = mysqli_real_escape_string($conn, $_POST['judul']);
        $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
        $isi = mysqli_real_escape_string($conn, $_POST['isi']);

        $lama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT sampul_berita, berita FROM pengumuman_dan_berita WHERE id_pengumuman = '$id_pem'"));
        $sampul = $lama['sampul_berita'];
        $berita_file = $lama['berita'] ? "'" . $lama['berita'] . "'" : 'NULL';

        if (!empty($_FILES['sampul_berita']['name'])) {
            $sampul = time() . '_' . $_FILES['sampul_berita']['name'];
            $target_dir = __DIR__ . '/uploads/sampul_berita/';
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            move_uploaded_file($_FILES['sampul_berita']['tmp_name'], $target_dir . $sampul);
        }

        if (!empty($_FILES['berita']['name'])) {
            $nama_file = time() . '_' . $_FILES['berita']['name'];
            $target_berita_dir = __DIR__ . '/uploads/Berita/';
            if (!is_dir($target_berita_dir)) {
                mkdir($target_berita_dir, 0777, true);
            }
            move_uploaded_file($_FILES['berita']['tmp_name'], $target_berita_dir . $nama_file);
            $berita_file = "'$nama_file'";
        }

        mysqli_query($conn, "UPDATE pengumuman_dan_berita SET judul = '$judul', kategori = '$kategori', isi = '$isi', sampul_berita = '$sampul', berita = $berita_file WHERE id_pengumuman = '$id_pem'");
        header("Location: dashboard.php");
        exit;
    }
    
    // HAPUS PENGUMUMAN
    if (isset($_POST['hapus_pemberitahuan'])) {
        $id_pem = mysqli_real_escape_string($conn, $_POST['id_pengumuman']);
        mysqli_query($conn, "DELETE FROM pengumuman_dan_berita WHERE id_pengumuman = '$id_pem'");
        header("Location: dashboard.php");
        exit;
    }

    // --- TAMBAH GALERI PRESTASI ---
    if (isset($_POST['tambah_prestasi'])) {
        $judul = mysqli_real_escape_string($conn, $_POST['judul']);
        $nama_peraih = mysqli_real_escape_string($conn, $_POST['nama_peraih']);
        $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
        $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
        $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
        
        $gambar = 'default_prestasi.jpg';
        if (!empty($_FILES['gambar']['name'])) {
            $gambar = time() . '_' . $_FILES['gambar']['name'];
            $target_foto_dir = __DIR__ . '/uploads/foto_prestasi/';
            if (!is_dir($target_foto_dir)) {
                mkdir($target_foto_dir, 0777, true);
            }
            move_uploaded_file($_FILES['gambar']['tmp_name'], $target_foto_dir . $gambar);
        }

        mysqli_query($conn, "INSERT INTO galeri_prestasi (judul, nama_peraih, kategori, deskripsi, tanggal, gambar) VALUES ('$judul', '$nama_peraih', '$kategori', '$deskripsi', '$tanggal', '$gambar')");
        header("Location: dashboard.php");
        exit;
    }

    // --- HAPUS GALERI PRESTASI ---
    if (isset($_POST['hapus_prestasi'])) {
        $id_pres = mysqli_real_escape_string($conn, $_POST['id_prestasi']);
        mysqli_query($conn, "DELETE FROM galeri_prestasi WHERE id_prestasi = '$id_pres'");
        header("Location: dashboard.php");
        exit;
    }
}

// --- LOGIKA TAB & PAGINATION PENGUMUMAN/BERITA ---
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'semua';

$limit_s = 5;
$page_s = isset($_GET['page_s']) ? (int)$_GET['page_s'] : 1;
$start_s = ($page_s > 1) ? ($page_s * $limit_s) - $limit_s : 0;
$total_s_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM pengumuman_dan_berita");
$total_s_data = mysqli_fetch_assoc($total_s_q)['total'] ?? 0;
$pages_s = max(1, ceil($total_s_data / $limit_s));

$limit_b = 5;
$page_b = isset($_GET['page_b']) ? (int)$_GET['page_b'] : 1;
$start_b = ($page_b > 1) ? ($page_b * $limit_b) - $limit_b : 0;
$total_b_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM pengumuman_dan_berita WHERE kategori = 'Berita'");
$total_b_data = mysqli_fetch_assoc($total_b_q)['total'] ?? 0;
$pages_b = max(1, ceil($total_b_data / $limit_b));

$limit_p = 10;
$page_p = isset($_GET['page_p']) ? (int)$_GET['page_p'] : 1;
$start_p = ($page_p > 1) ? ($page_p * $limit_p) - $limit_p : 0;
$total_p_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM pengumuman_dan_berita WHERE kategori = 'Pengumuman'");
$total_p_data = mysqli_fetch_assoc($total_p_q)['total'] ?? 0;
$pages_p = max(1, ceil($total_p_data / $limit_p));

if ($tab == 'berita') {
    $query_data = mysqli_query($conn, "SELECT * FROM pengumuman_dan_berita WHERE kategori = 'Berita' ORDER BY tanggal_dibuat DESC LIMIT $start_b, $limit_b");
    $total_pages = $pages_b;
    $current_page = $page_b;
    $page_param = 'page_b';
} elseif ($tab == 'pengumuman') {
    $query_data = mysqli_query($conn, "SELECT * FROM pengumuman_dan_berita WHERE kategori = 'Pengumuman' ORDER BY tanggal_dibuat DESC LIMIT $start_p, $limit_p");
    $total_pages = $pages_p;
    $current_page = $page_p;
    $page_param = 'page_p';
} else {
    $query_data = mysqli_query($conn, "SELECT * FROM pengumuman_dan_berita ORDER BY tanggal_dibuat DESC LIMIT $start_s, $limit_s");
    $total_pages = $pages_s;
    $current_page = $page_s;
    $page_param = 'page_s';
}

// --- LOGIKA PAGINATION GALERI PRESTASI ---
$limit_g = 3; 
$page_g = isset($_GET['page_g']) ? (int)$_GET['page_g'] : 1;
$start_g = ($page_g > 1) ? ($page_g * $limit_g) - $limit_g : 0;
$total_g_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM galeri_prestasi");
$total_g_data = mysqli_fetch_assoc($total_g_q)['total'] ?? 0;
$pages_g = max(1, ceil($total_g_data / $limit_g));
$prestasi_query = mysqli_query($conn, "SELECT * FROM galeri_prestasi ORDER BY tanggal DESC LIMIT $start_g, $limit_g");

// --- LOGIKA PAGINATION BUKU TERPOPULER ---
$limit_bp = 3; 
$page_bp = isset($_GET['page_bp']) ? (int)$_GET['page_bp'] : 1;
$start_bp = ($page_bp > 1) ? ($page_bp * $limit_bp) - $limit_bp : 0;
$total_bp_q = mysqli_query($conn, "SELECT COUNT(DISTINCT p.id_buku) as total FROM pinjam p JOIN buku b ON p.id_buku = b.id_buku");
$total_bp_data = mysqli_fetch_assoc($total_bp_q)['total'] ?? 0;
$pages_bp = max(1, ceil($total_bp_data / $limit_bp));
$populer_query = mysqli_query($conn, "SELECT b.*, COUNT(p.id_buku) as frekuensi 
                                     FROM pinjam p JOIN buku b ON p.id_buku = b.id_buku 
                                     GROUP BY p.id_buku ORDER BY frekuensi DESC LIMIT $start_bp, $limit_bp");


// --- PENGAMBILAN DATA STATISTIK ---
$buku_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM buku");
$total_buku = mysqli_fetch_assoc($buku_res)['total'];

$anggota_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM pengguna WHERE role = 'anggota'");
$total_anggota = mysqli_fetch_assoc($anggota_res)['total'];

if ($role == 'admin') {
    $pinjam_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM pinjam WHERE status != 'dikembalikan'");
} else {
    $pinjam_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM pinjam WHERE id_pengguna = '$id_pengguna' AND status != 'dikembalikan'");
}
$total_pinjam = mysqli_fetch_assoc($pinjam_res)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | E-Perpus SMANTEN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="js/dashboard.js"></script>
    <style>
        .sidebar-active { left: 0 !important; }
        .sidebar-hidden { left: -18rem; }
        body { transition: background-color 0.3s, color 0.3s; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 font-sans text-slate-900 dark:text-slate-100">

    <div class="flex min-h-screen">
        <aside id="sidebar" class="fixed md:sticky top-0 left-[-18rem] md:left-0 z-40 w-72 h-screen bg-slate-900 dark:bg-white border-r border-slate-800 dark:border-slate-200 p-6 flex flex-col justify-between sidebar-hidden md:transform-none text-slate-300 dark:text-slate-600 transition-transform duration-300 transition-colors duration-300">
            <div>
                <div class="flex items-center gap-3 mb-8 px-2">
                    <div class="w-10 h-10 rounded-xl bg-brand flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                        <i class="fa fa-book-open text-lg"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-base tracking-tight leading-none mb-1 text-white dark:text-slate-900"><?= htmlspecialchars(pengaturan('nama_aplikasi')) ?></h1>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider"><?= htmlspecialchars(pengaturan('tagline')) ?></p>
                    </div>
                </div>

                <!-- Menu Navigasi Berdasarkan Role -->
                <nav class="space-y-1.5">
                    <p class="px-3 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3">Menu Utama</p>
                    
                    <a href="dashboard.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-bold bg-brand text-white transition-all shadow-md shadow-indigo-600/10">
                        <i class="fa fa-th-large text-base w-5 text-center"></i> Dashboard
                    </a>
                    
                    <?php if ($role == 'admin' || $role == 'pustakawan'): ?>
                        <a href="anggota.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                            <i class="fa fa-users text-base w-5 text-center"></i> Anggota Perpustakaan
                        </a>
                        <a href="buku.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                            <i class="fa fa-book text-base w-5 text-center"></i> Manajemen Buku
                        </a>
                        <a href="pinjam.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                            <i class="fa fa-handshake text-base w-5 text-center"></i> Transaksi Pinjam
                        </a>
                        <a href="log.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                            <i class="fa fa-history text-base w-5 text-center"></i> Log Aktivitas
                        </a>
                        <?php if ($role == 'admin'): ?>
                        <a href="pengaturan.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                            <i class="fa fa-sliders text-base w-5 text-center"></i> Pengaturan Sistem
                        </a>
                        <!-- Admin memegang semua kontrol sistem, termasuk menu khusus anggota -->
                        <a href="daftar_buku.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                            <i class="fa fa-book text-base w-5 text-center"></i> Katalog Buku
                        </a>
                        <a href="buku_pinjam.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                            <i class="fa fa-book-reader text-base w-5 text-center"></i> Ruang Baca Digital
                        </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Menu khusus Anggota -->
                        <a href="daftar_buku.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                            <i class="fa fa-book text-base w-5 text-center"></i> Katalog Buku
                        </a>
                        <a href="buku_pinjam.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                            <i class="fa fa-book-reader text-base w-5 text-center"></i> Ruang Baca Digital
                        </a>
                    <?php endif; ?>
                </nav>
            </div>

            <div class="border-t border-slate-800 dark:border-slate-200 pt-4">
                <a href="logout.php" class="flex items-center gap-3 px-3 py-2 text-rose-400 dark:text-rose-600 hover:bg-rose-500/10 dark:hover:bg-rose-50 rounded-xl transition-all">
                    <i class="fa fa-sign-out w-5"></i> Logout
                </a>
            </div>
        </aside>

        <main class="flex-1 p-4 md:p-8 w-full overflow-hidden">
            <?php if (isset($_SESSION['admin_asli'])): ?>
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-indigo-600 text-white px-5 py-3.5 rounded-2xl shadow-lg shadow-indigo-500/20">
                    <div class="flex items-center gap-2 text-sm font-medium">
                        <i class="fa fa-user-secret"></i>
                        Anda (admin) sedang masuk sebagai <b><?php echo htmlspecialchars($user['nama']); ?></b> (<?php echo htmlspecialchars($role); ?>)
                    </div>
                    <a href="kembali_admin.php" class="inline-flex items-center gap-2 bg-white text-indigo-700 text-xs font-bold px-4 py-2 rounded-xl hover:bg-indigo-50 transition-all w-max">
                        <i class="fa fa-arrow-left"></i> Kembali ke Akun Admin
                    </a>
                </div>
            <?php endif; ?>
            <header class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 bg-white dark:bg-darkCard border dark:border-slate-700 rounded-lg">
                        <i class="fa fa-bars"></i>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">Selamat Datang, <?php echo htmlspecialchars($user['nama']); ?>!</h1>
                        <p class="text-slate-400 dark:text-slate-500 text-sm mt-0.5">Akses Dashboard Anda sebagai <span class="capitalize font-semibold text-brand"><?php echo $role; ?></span>.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button onclick="toggleDarkMode()" class="p-2 w-10 h-10 bg-white dark:bg-darkCard border dark:border-slate-700 rounded-xl shadow-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                        <i id="theme-icon" class="fa fa-moon text-slate-600 dark:text-yellow-400"></i>
                    </button>
                    <div class="h-10 w-10 rounded-full bg-brand flex items-center justify-center text-white ring-4 ring-indigo-50 dark:ring-indigo-900/40 uppercase font-bold text-sm">
                        <?php echo substr($user['nama'], 0, 1); ?>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-darkCard p-6 rounded-[1.5rem] border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-400 dark:text-slate-500">Total Koleksi Buku</p>
                        <h3 class="text-3xl font-bold mt-1"><?php echo $total_buku; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-950/40 rounded-xl flex items-center justify-center text-brand text-xl"><i class="fa fa-book-open"></i></div>
                </div>

                <?php if ($role == 'admin'): ?>
                <div class="bg-white dark:bg-darkCard p-6 rounded-[1.5rem] border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-400 dark:text-slate-500">Anggota Terdaftar</p>
                        <h3 class="text-3xl font-bold mt-1"><?php echo $total_anggota; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl flex items-center justify-center text-emerald-500 text-xl"><i class="fa fa-users"></i></div>
                </div>
                <?php endif; ?>

                <div class="bg-white dark:bg-darkCard p-6 rounded-[1.5rem] border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between <?php echo ($role != 'admin') ? 'sm:col-span-2 lg:col-span-2' : ''; ?>">
                    <div>
                        <p class="text-sm font-medium text-slate-400 dark:text-slate-500">
                            <?php echo ($role == 'admin') ? 'Peminjaman Aktif' : 'Buku Sedang Anda Pinjam'; ?>
                        </p>
                        <h3 class="text-3xl font-bold mt-1"><?php echo $total_pinjam; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 dark:bg-amber-950/40 rounded-xl flex items-center justify-center text-amber-500 text-xl"><i class="fa fa-handshake"></i></div>
                </div>
            </div>

            <!-- NOTIFIKASI PENGEMBALIAN BUKU KHUSUS ADMIN DI DASHBOARD -->
            <?php if ($role == 'admin'): 
                $query_notif_dash = "SELECT p.*, b.judul, u.nama 
                                     FROM pinjam p 
                                     JOIN buku b ON p.id_buku = b.id_buku 
                                     JOIN pengguna u ON p.id_pengguna = u.id_pengguna
                                     WHERE p.status = 'dipinjam' AND p.status_pengajuan = 'menunggu_konfirmasi'
                                     ORDER BY p.tanggal_pinjam DESC";
                $res_notif_dash = mysqli_query($conn, $query_notif_dash);
                if (mysqli_num_rows($res_notif_dash) > 0):
            ?>
            <div class="mb-8 bg-amber-50 dark:bg-amber-950/20 rounded-[2rem] border border-amber-200 dark:border-amber-800/60 p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center text-sm shadow-md">
                        <i class="fa fa-bell"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-amber-900 dark:text-amber-400">Permintaan Pengembalian Buku Baru</h3>
                        <p class="text-xs text-amber-700/80 dark:text-amber-500">Anggota mengajukan pengembalian buku. Periksa kondisi fisik dan klik tombol setujui di bawah ini.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php while($nd = mysqli_fetch_assoc($res_notif_dash)): ?>
                    <div class="bg-white dark:bg-darkCard p-4 rounded-2xl border border-amber-100 dark:border-amber-900/40 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-md">Pending Validasi</span>
                                <span class="text-[11px] text-slate-400 font-mono">Tempo: <?= date('d/m/Y', strtotime($nd['tanggal_kembali'])); ?></span>
                            </div>
                            <h4 class="font-bold text-sm text-slate-900 dark:text-white line-clamp-1"><?= htmlspecialchars($nd['judul']); ?></h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Peminjam: <strong class="text-slate-700 dark:text-slate-300"><?= htmlspecialchars($nd['nama']); ?></strong></p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                            <a href="dashboard.php?aksi=kembalikan&id=<?= $nd['id_pinjam']; ?>" 
                               onclick="return confirm('Setujui pengembalian buku dari <?= htmlspecialchars($nd['nama']); ?>?');" 
                               class="w-full text-center py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                                <i class="fa fa-check mr-1"></i> Setujui & Selesaikan
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- KOLOM KIRI (BERITA & PENGUMUMAN) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-darkCard p-6 rounded-[1.5rem] border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col justify-between h-full">
                        <div>
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                                <h2 class="text-lg font-bold tracking-tight flex items-center gap-2">
                                    <i class="fa fa-bullhorn text-brand"></i> Informasi & Berita Sekolah
                                </h2>
                                <div class="flex items-center justify-between w-full sm:w-auto gap-3">
                                    <div class="flex bg-slate-100 dark:bg-slate-800 p-1 rounded-xl text-xs font-semibold">
                                        <a href="<?php echo buildUrl('tab', 'semua'); ?>" class="px-3 py-1.5 rounded-lg transition-all <?php echo ($tab == 'semua') ? 'bg-white dark:bg-darkCard text-brand shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900'; ?>">Semua</a>
                                        <a href="<?php echo buildUrl('tab', 'berita'); ?>" class="px-3 py-1.5 rounded-lg transition-all <?php echo ($tab == 'berita') ? 'bg-white dark:bg-darkCard text-brand shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900'; ?>">Berita</a>
                                        <a href="<?php echo buildUrl('tab', 'pengumuman'); ?>" class="px-3 py-1.5 rounded-lg transition-all <?php echo ($tab == 'pengumuman') ? 'bg-white dark:bg-darkCard text-brand shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900'; ?>">Pengumuman</a>
                                    </div>

                                    <?php if ($role == 'admin'): ?>
                                        <button onclick="toggleModal('modal-tambah')" class="bg-brand hover:bg-indigo-700 text-white text-xs font-bold px-3 py-2 rounded-xl transition-all shadow-md shadow-indigo-500/10 shrink-0">
                                            <i class="fa fa-plus mr-1"></i> Buat
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <?php
                                if (mysqli_num_rows($query_data) == 0):
                                ?>
                                    <p class="text-sm text-slate-400 dark:text-slate-500 text-center py-6">Belum ada informasi untuk kategori ini.</p>
                                <?php 
                                else:
                                    while ($pem = mysqli_fetch_assoc($query_data)):
                                        $cover_path = 'uploads/sampul_berita/' . $pem['sampul_berita'];
                                        $isi_bersih = strip_tags($pem['isi']);
                                        $ringkasan_isi = (mb_strlen($isi_bersih) > 150) ? mb_substr($isi_bersih, 0, 150) . '...' : $isi_bersih;
                                        $kategori_item = isset($pem['kategori']) ? $pem['kategori'] : 'Berita';
                                ?>
                                    <div class="p-4 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 rounded-2xl relative group">
                                        <div class="flex justify-between items-start gap-4">
                                            <div class="flex gap-4">
                                                <?php if ($pem['sampul_berita'] != 'default_berita.jpg' && file_exists(__DIR__ . '/' . $cover_path)): ?>
                                                    <img src="<?php echo $cover_path; ?>" alt="Sampul Berita" class="w-20 h-20 object-cover rounded-xl shadow-sm shrink-0">
                                                <?php else: ?>
                                                    <div class="w-20 h-20 rounded-xl bg-slate-200 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                                        <i class="fa fa-newspaper text-slate-400 text-2xl"></i>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div>
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 bg-indigo-500 text-white rounded-md"><?php echo htmlspecialchars($kategori_item); ?></span>
                                                    </div>
                                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-1"><?php echo htmlspecialchars($pem['judul']); ?></h4>
                                                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed"><?php echo htmlspecialchars($ringkasan_isi); ?></p>
                                                    
                                                    <div class="mt-2">
                                                        <a href="detail_berita.php?id=<?php echo $pem['id_pengumuman']; ?>" target="_blank" class="text-[11px] font-bold text-brand hover:underline inline-flex items-center gap-1">
                                                            Baca Selengkapnya <i class="fa fa-arrow-right text-[9px]"></i>
                                                        </a>
                                                    </div>
                                                    
                                                    <div class="flex items-center gap-4 mt-2">
                                                        <span class="text-[10px] text-slate-400"><i class="fa fa-clock mr-1"></i><?php echo date('d M Y H:i', strtotime($pem['tanggal_dibuat'])); ?></span>
                                                        <?php if (!empty($pem['berita'])): ?>
                                                            <a href="uploads/Berita/<?php echo $pem['berita']; ?>" target="_blank" class="text-[10px] font-bold text-brand hover:underline flex items-center gap-1">
                                                                <i class="fa fa-download"></i> Unduh Lampiran
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <?php if ($role == 'admin'): ?>
                                                <div class="flex items-center gap-2 shrink-0">
                                                    <button type="button" onclick="bukaModalEdit(<?php echo $pem['id_pengumuman']; ?>, '<?php echo urlencode($pem['judul']); ?>', '<?php echo urlencode($kategori_item); ?>', '<?php echo urlencode($pem['isi']); ?>')" class="text-slate-400 hover:text-brand p-1 rounded-lg transition-all">
                                                        <i class="fa fa-edit text-xs"></i>
                                                    </button>
                                                    <form action="" method="POST" onsubmit="return confirm('Hapus informasi ini?');" class="inline">
                                                        <input type="hidden" name="id_pengumuman" value="<?php echo $pem['id_pengumuman']; ?>">
                                                        <button type="submit" name="hapus_pemberitahuan" class="text-slate-400 hover:text-rose-500 p-1 rounded-lg transition-all">
                                                            <i class="fa fa-trash text-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php 
                                    endwhile;
                                endif; 
                                ?>
                            </div>
                        </div>

                        <div class="flex justify-center items-center gap-2 mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <?php if ($current_page > 1): ?>
                                <a href="<?php echo buildUrl($page_param, $current_page - 1); ?>" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                                    <i class="fa fa-chevron-left mr-1"></i> Prev
                                </a>
                            <?php endif; ?>

                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="<?php echo buildUrl($page_param, $i); ?>" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition <?php echo ($current_page == $i) ? 'bg-brand text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($current_page < $total_pages): ?>
                                <a href="<?php echo buildUrl($page_param, $current_page + 1); ?>" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                                    Next <i class="fa fa-chevron-right ml-1"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN (NAVIGASI, GALERI, BUKU POPULER, DLL) -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-darkCard p-6 rounded-[1.5rem] border border-slate-100 dark:border-slate-800 shadow-sm">
                        <h2 class="text-lg font-bold tracking-tight mb-4 flex items-center gap-2">
                            <i class="fa fa-bolt text-brand"></i> Navigasi Cepat
                        </h2>
                        <div class="flex flex-col gap-3">
                            <?php if ($role == 'admin'): ?>
                                <a href="buku.php" class="flex items-center gap-4 p-3.5 bg-slate-50 dark:bg-slate-800/40 border dark:border-slate-800 rounded-2xl hover:border-indigo-500 dark:hover:border-indigo-500 transition-all group">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-500 group-hover:scale-105 transition-transform"><i class="fa fa-book-open text-sm"></i></div>
                                    <div class="text-left">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Tambah Buku Baru</p>
                                        <p class="text-[10px] text-slate-400">Entri koleksi baru perpustakaan</p>
                                    </div>
                                </a>
                                <a href="anggota.php" class="flex items-center gap-4 p-3.5 bg-slate-50 dark:bg-slate-800/40 border dark:border-slate-800 rounded-2xl hover:border-emerald-500 dark:hover:border-emerald-500 transition-all group">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 flex items-center justify-center text-emerald-500 group-hover:scale-105 transition-transform"><i class="fa fa-user-plus text-sm"></i></div>
                                    <div class="text-left">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Tambah Anggota</p>
                                        <p class="text-[10px] text-slate-400">Registrasi akun pembaca baru</p>
                                    </div>
                                </a>
                                <a href="pinjam.php" class="flex items-center gap-4 p-3.5 bg-slate-50 dark:bg-slate-800/40 border dark:border-slate-800 rounded-2xl hover:border-amber-500 dark:hover:border-amber-500 transition-all group">
                                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/50 flex items-center justify-center text-amber-500 group-hover:scale-105 transition-transform"><i class="fa fa-handshake text-sm"></i></div>
                                    <div class="text-left">
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200">Kelola Peminjaman</p>
                                        <p class="text-[10px] text-slate-400">Validasi pengajuan/sirkulasi buku</p>
                                    </div>
                                </a>
                            <?php else: ?>
                                <a href="daftar_buku.php" class="flex items-center justify-between p-4 bg-brand text-white rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/10 group">
                                    <div class="flex items-center gap-3">
                                        <i class="fa fa-search text-base"></i>
                                        <div class="text-left">
                                            <p class="text-xs font-bold">Cari Koleksi Buku</p>
                                            <p class="text-[10px] text-indigo-100">Cari buku digital & pinjam langsung</p>
                                        </div>
                                    </div>
                                    <i class="fa fa-chevron-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                                </a>
                                <a href="buku_pinjam.php" class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/40 border dark:border-slate-800 rounded-2xl hover:border-brand dark:hover:border-brand transition-all group">
                                    <div class="flex items-center gap-3">
                                        <i class="fa fa-book-reader text-base text-brand"></i>
                                        <div class="text-left">
                                            <p class="text-xs font-bold">Buku Sedang Dipinjam</p>
                                            <p class="text-[10px] text-slate-400">Buka e-book atau baca file PDF</p>
                                        </div>
                                    </div>
                                    <i class="fa fa-chevron-right text-[10px] text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- GALERI PRESTASI -->
                    <div class="bg-white dark:bg-darkCard p-6 rounded-[1.5rem] border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col h-[400px]">
                        <div class="flex justify-between items-center mb-4 shrink-0">
                            <h2 class="text-lg font-bold tracking-tight flex items-center gap-2">
                                <i class="fa fa-award text-brand"></i> Galeri Prestasi & Karya
                            </h2>
                            <div class="flex gap-2">
                                <?php if ($role == 'admin'): ?>
                                    <button onclick="toggleModal('modal-tambah-prestasi')" class="bg-brand hover:bg-indigo-700 text-white text-xs font-bold px-2.5 py-1.5 rounded-lg transition-all shadow-md shadow-indigo-500/10">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="space-y-3 flex-1 overflow-y-auto pr-1">
                            <?php
                            if (mysqli_num_rows($prestasi_query) == 0):
                            ?>
                                <p class="text-sm text-slate-400 dark:text-slate-500 text-center py-6">Belum ada data prestasi yang diunggah.</p>
                            <?php 
                            else:
                                while ($pres = mysqli_fetch_assoc($prestasi_query)):
                                    $pres_img = 'uploads/foto_prestasi/' . $pres['gambar'];
                            ?>
                                <div class="p-3 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 rounded-2xl flex justify-between items-center gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <?php if (!empty($pres['gambar']) && $pres['gambar'] != 'default_prestasi.jpg' && file_exists(__DIR__ . '/' . $pres_img)): ?>
                                            <img src="<?php echo $pres_img; ?>" alt="Prestasi" class="w-14 h-14 object-cover rounded-xl shadow-sm shrink-0">
                                        <?php else: ?>
                                            <div class="w-14 h-14 rounded-xl bg-slate-200 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                                <i class="fa fa-trophy text-slate-400 text-lg"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="min-w-0">
                                            <span class="inline-block text-[8px] font-bold uppercase tracking-wider px-1.5 py-0.5 bg-indigo-500 text-white rounded-md mb-1"><?php echo htmlspecialchars($pres['kategori']); ?></span>
                                            <h4 class="font-bold text-slate-900 dark:text-white text-xs truncate"><?php echo htmlspecialchars($pres['judul']); ?></h4>
                                            <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">Oleh: <span class="font-semibold"><?php echo htmlspecialchars($pres['nama_peraih']); ?></span></p>
                                        </div>
                                    </div>
                                    
                                    <?php if ($role == 'admin'): ?>
                                        <form action="" method="POST" onsubmit="return confirm('Hapus prestasi ini?');" class="shrink-0">
                                            <input type="hidden" name="id_prestasi" value="<?php echo $pres['id_prestasi']; ?>">
                                            <button type="submit" name="hapus_prestasi" class="text-slate-400 hover:text-rose-500 p-1.5 rounded-lg transition-all">
                                                <i class="fa fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php 
                                endwhile;
                            endif; 
                            ?>
                        </div>

                        <!-- Pagination Galeri Prestasi Muncul Permanen -->
                        <div class="flex justify-center items-center gap-1.5 mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 shrink-0">
                            <?php if ($page_g > 1): ?>
                                <a href="<?php echo buildUrl('page_g', $page_g - 1); ?>" class="px-2 py-1 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                                    <i class="fa fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php for($i = 1; $i <= $pages_g; $i++): ?>
                                <a href="<?php echo buildUrl('page_g', $i); ?>" class="px-2.5 py-1 rounded-md text-[10px] font-bold transition <?php echo ($page_g == $i) ? 'bg-brand text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page_g < $pages_g): ?>
                                <a href="<?php echo buildUrl('page_g', $page_g + 1); ?>" class="px-2 py-1 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                                    <i class="fa fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- BUKU TERPOPULER -->
                    <div class="bg-white dark:bg-darkCard p-6 rounded-[1.5rem] border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col h-[400px]">
                        <h2 class="text-lg font-bold tracking-tight mb-4 flex items-center gap-2 shrink-0">
                            <i class="fa fa-fire text-amber-500"></i> Koleksi Buku Terpopuler
                        </h2>
                        <div class="space-y-3 flex-1 overflow-y-auto pr-1">
                            <?php
                            if(mysqli_num_rows($populer_query) == 0):
                            ?>
                                <p class="text-xs text-slate-400 text-center py-4">Belum ada statistik sirkulasi buku.</p>
                            <?php
                            else:
                                $rank = $start_bp + 1;
                                while($bp = mysqli_fetch_assoc($populer_query)):
                                    $nama_penulis = '-';
                                    if (isset($bp['penulis'])) {
                                        $nama_penulis = $bp['penulis'];
                                    } elseif (isset($bp['pengarang'])) {
                                        $nama_penulis = $bp['pengarang'];
                                    } elseif (isset($bp['author'])) {
                                        $nama_penulis = $bp['author'];
                                    }
                            ?>
                                <div class="p-3 bg-slate-50 dark:bg-slate-800/30 border dark:border-slate-800 rounded-2xl flex items-center gap-3 relative overflow-hidden">
                                    <div class="absolute -right-2 -bottom-4 text-5xl font-black text-slate-200/50 dark:text-slate-700/30 select-none">#<?php echo $rank++; ?></div>
                                    <div class="w-10 h-10 rounded-xl bg-indigo-500 text-white flex items-center justify-center shrink-0 font-bold"><i class="fa fa-bookmark"></i></div>
                                    <div class="min-w-0 flex-1 z-10">
                                        <h4 class="text-xs font-bold truncate pr-6 text-slate-800 dark:text-slate-200"><?php echo htmlspecialchars($bp['judul']); ?></h4>
                                        <p class="text-[10px] text-slate-400 mt-0.5 truncate">Penulis: <?php echo htmlspecialchars($nama_penulis); ?></p>
                                        <span class="inline-block text-[9px] font-bold bg-amber-500/10 text-amber-500 px-2 py-0.5 rounded-md mt-1"><i class="fa fa-star mr-1"></i><?php echo $bp['frekuensi']; ?>x Dipinjam</span>
                                    </div>
                                </div>
                            <?php 
                                endwhile;
                            endif; 
                            ?>
                        </div>

                        <!-- Pagination Buku Populer Muncul Permanen -->
                        <div class="flex justify-center items-center gap-1.5 mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 shrink-0">
                            <?php if ($page_bp > 1): ?>
                                <a href="<?php echo buildUrl('page_bp', $page_bp - 1); ?>" class="px-2 py-1 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                                    <i class="fa fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php for($i = 1; $i <= $pages_bp; $i++): ?>
                                <a href="<?php echo buildUrl('page_bp', $i); ?>" class="px-2.5 py-1 rounded-md text-[10px] font-bold transition <?php echo ($page_bp == $i) ? 'bg-brand text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page_bp < $pages_bp): ?>
                                <a href="<?php echo buildUrl('page_bp', $page_bp + 1); ?>" class="px-2 py-1 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                                    <i class="fa fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($role == 'anggota'): ?>
                    <div class="bg-white dark:bg-darkCard p-6 rounded-[1.5rem] border border-slate-100 dark:border-slate-800 shadow-sm">
                        <h2 class="text-lg font-bold tracking-tight mb-4 flex items-center gap-2">
                            <i class="fa fa-bell text-rose-500"></i> Status Pengembalian
                        </h2>
                        <div class="space-y-3">
                            <?php
                            $tenggat_kanan = mysqli_query($conn, "SELECT p.*, b.judul, DATEDIFF(p.tanggal_kembali, CURDATE()) as sisa_hari 
                                                                  FROM pinjam p JOIN buku b ON p.id_buku = b.id_buku 
                                                                  WHERE p.id_pengguna = '$id_pengguna' AND p.status = 'disetujui' 
                                                                  HAVING sisa_hari <= 2 ORDER BY sisa_hari ASC");
                            
                            if (mysqli_num_rows($tenggat_kanan) == 0):
                            ?>
                                <p class="text-xs text-slate-400 text-center py-4"><i class="fa fa-check-circle text-emerald-500 mr-1"></i> Aman. Tidak ada denda atau tenggat dekat.</p>
                            <?php 
                            else:
                                while($tg = mysqli_fetch_assoc($tenggat_kanan)):
                                    if($tg['sisa_hari'] < 0) {
                                        $teks_sisa = "Terlambat " . abs($tg['sisa_hari']) . " hari!";
                                        $color_box = "bg-rose-50 dark:bg-rose-950/20 border-rose-100 dark:border-rose-900 text-rose-700 dark:text-rose-400";
                                        $icon_box = "fa-exclamation-circle text-rose-500";
                                    } else {
                                        $teks_sisa = "Sisa " . $tg['sisa_hari'] . " hari lagi";
                                        $color_box = "bg-amber-50 dark:bg-amber-950/20 border-amber-100 dark:border-amber-900 text-amber-700 dark:text-amber-400";
                                        $icon_box = "fa-hourglass-half text-amber-500";
                                    }
                            ?>
                                <div class="p-3 border rounded-xl flex items-start gap-3 <?php echo $color_box; ?>">
                                    <i class="fa <?php echo $icon_box; ?> text-sm mt-0.5 shrink-0"></i>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold truncate"><?php echo htmlspecialchars($tg['judul']); ?></p>
                                        <p class="text-[10px] opacity-80 mt-0.5"><?php echo $teks_sisa; ?> (Batas: <?php echo date('d/m/Y', strtotime($tg['tanggal_kembali'])); ?>)</p>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($role == 'admin'): ?>
                    <div class="bg-white dark:bg-darkCard p-6 rounded-[1.5rem] border border-slate-100 dark:border-slate-800 shadow-sm">
                        <h2 class="text-lg font-bold tracking-tight mb-6 flex items-center gap-2">
                            <i class="fa fa-exchange-alt text-brand"></i> Transaksi Terkini
                        </h2>
                        <div class="space-y-4">
                            <?php
                            $log_query = mysqli_query($conn, "SELECT p.*, b.judul, u.nama FROM pinjam p 
                                                                 JOIN buku b ON p.id_buku = b.id_buku 
                                                                 JOIN pengguna u ON p.id_pengguna = u.id_pengguna 
                                                                 ORDER BY p.tanggal_pinjam DESC LIMIT 5");
                            if(mysqli_num_rows($log_query) == 0):
                            ?>
                                <p class="text-xs text-slate-400 text-center py-4">Belum ada riwayat transaksi.</p>
                            <?php 
                            else:
                                while($log = mysqli_fetch_assoc($log_query)): 
                                    $status_color = $log['status'] == 'disetujui' ? 'text-emerald-500 bg-emerald-500/10' : ($log['status'] == 'diajukan' ? 'text-amber-500 bg-amber-500/10' : 'text-slate-400 bg-slate-500/10');
                            ?>
                                <div class="flex items-start gap-3 text-xs border-b border-slate-50 dark:border-slate-800/60 pb-3 last:border-none last:pb-0">
                                    <div class="w-2 h-2 mt-1.5 rounded-full shrink-0 bg-brand"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-slate-800 dark:text-slate-200 truncate"><span class="text-brand"><?php echo htmlspecialchars($log['nama']); ?></span> meminjam <strong><?php echo htmlspecialchars($log['judul']); ?></strong></p>
                                        <div class="flex justify-between items-center mt-1">
                                            <span class="text-[10px] text-slate-400"><?php echo date('d/m/Y', strtotime($log['tanggal_pinjam'])); ?></span>
                                            <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded-md <?php echo $status_color; ?>"><?php echo $log['status']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                endwhile;
                            endif; 
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <?php if ($role == 'admin'): ?>
    <!-- Modal Tambah Berita -->
    <div id="modal-tambah" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white dark:bg-darkCard w-full max-w-md p-6 rounded-3xl shadow-xl transform scale-95 transition-transform duration-300 mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2"><i class="fa fa-bullhorn text-brand"></i> Tambah Informasi Baru</h3>
                <button onclick="toggleModal('modal-tambah')" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times"></i></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Judul</label>
                    <input type="text" name="judul" required class="w-full text-sm px-4 py-2 bg-slate-50 dark:bg-slate-800 border dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Pilih Kategori</label>
                    <select name="kategori" required class="w-full text-sm px-4 py-2 bg-slate-50 dark:bg-slate-800 border dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand dark:text-white">
                        <option value="Berita">Berita</option>
                        <option value="Pengumuman">Pengumuman</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Deskripsi/Isi</label>
                    <textarea name="isi" rows="4" required class="w-full text-sm px-4 py-2 bg-slate-50 dark:bg-slate-800 border dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Gambar Sampul (Opsional)</label>
                    <input type="file" name="sampul_berita" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">File Lampiran PDF/Doc (Opsional)</label>
                    <input type="file" name="berita" accept=".pdf,.doc,.docx" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>
                <button type="submit" name="tambah_pemberitahuan" class="w-full bg-brand hover:bg-indigo-700 text-white font-semibold text-sm py-2.5 rounded-xl transition-all shadow-lg shadow-indigo-500/20 mt-2">
                    <i class="fa fa-paper-plane mr-1"></i> Publikasikan
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Galeri Prestasi -->
    <div id="modal-tambah-prestasi" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white dark:bg-darkCard w-full max-w-lg p-6 rounded-3xl shadow-xl transform scale-95 transition-transform duration-300 mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2"><i class="fa fa-award text-brand"></i> Tambah Galeri Prestasi</h3>
                <button onclick="toggleModal('modal-tambah-prestasi')" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times"></i></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Judul Penghargaan / Lomba / Karya</label>
                    <input type="text" name="judul" required class="w-full text-sm px-4 py-2 bg-slate-50 dark:bg-slate-800 border dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand dark:text-white" placeholder="Contoh: Juara 1 Olimpiade Matematika">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Nama Peraih / Pembuat</label>
                        <input type="text" name="nama_peraih" required class="w-full text-sm px-4 py-2 bg-slate-50 dark:bg-slate-800 border dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand dark:text-white" placeholder="Nama Siswa atau Guru">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Kategori</label>
                        <select name="kategori" required class="w-full text-sm px-4 py-2 bg-slate-50 dark:bg-slate-800 border dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand dark:text-white">
                            <option value="Akademik">Akademik</option>
                            <option value="Non-Akademik">Non-Akademik</option>
                            <option value="Karya Guru">Karya Guru</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" required class="w-full text-sm px-4 py-2 bg-slate-50 dark:bg-slate-800 border dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand dark:text-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Foto Dokumentasi</label>
                        <input type="file" name="gambar" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Deskripsi Singkat</label>
                    <textarea name="deskripsi" rows="3" required class="w-full text-sm px-4 py-2 bg-slate-50 dark:bg-slate-800 border dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand dark:text-white" placeholder="Keterangan singkat pencapaian..."></textarea>
                </div>
                <button type="submit" name="tambah_prestasi" class="w-full bg-brand hover:bg-indigo-700 text-white font-semibold text-sm py-2.5 rounded-xl transition-all shadow-lg shadow-indigo-500/20 mt-2">
                    <i class="fa fa-save mr-1"></i> Simpan & Publikasikan Prestasi
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Edit Berita -->
    <div id="modal-edit" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white dark:bg-darkCard w-full max-w-md p-6 rounded-3xl shadow-xl transform scale-95 transition-transform duration-300 mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2"><i class="fa fa-edit text-brand"></i> Ubah Informasi</h3>
                <button onclick="toggleModal('modal-edit')" class="text-slate-400 hover:text-slate-600"><i class="fa fa-times"></i></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="id_pengumuman" id="edit-id">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Judul</label>
                    <input type="text" name="judul" id="edit-judul" required class="w-full text-sm px-4 py-2 bg-slate-50 dark:bg-slate-800 border dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand dark:text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Pilih Kategori</label>
                    <select name="kategori" id="edit-kategori" required class="w-full text-sm px-4 py-2 bg-slate-50 dark:bg-slate-800 border dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand dark:text-white">
                        <option value="Berita">Berita</option>
                        <option value="Pengumuman">Pengumuman</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Deskripsi/Isi</label>
                    <textarea name="isi" id="edit-isi" rows="4" required class="w-full text-sm px-4 py-2 bg-slate-50 dark:bg-slate-800 border dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Ganti Gambar Sampul (Opsional)</label>
                    <input type="file" name="sampul_berita" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Ganti File Lampiran (Opsional)</label>
                    <input type="file" name="berita" accept=".pdf,.doc,.docx" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>
                <button type="submit" name="edit_pemberitahuan" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm py-2.5 rounded-xl transition-all shadow-lg shadow-emerald-500/20 mt-2">
                    <i class="fa fa-save mr-1"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script src="js/dashboard_2.js"></script>
</body>
</html>