<?php
session_start();
include 'koneksi.php';

if (cek_maintenance('peminjaman')) { tampilkan_maintenance('Peminjaman & Pengembalian'); }

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$id_pengguna = $user['id_pengguna'];
$role = $user['role'];

if (!isset($_GET['id'])) {
    header("Location: daftar_buku.php");
    exit;
}

$id_buku = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data buku
$query_buku = mysqli_query($conn, "SELECT * FROM buku WHERE id_buku = '$id_buku'");
$data = mysqli_fetch_assoc($query_buku);

if (!$data) {
    echo "<script>alert('Buku tidak ditemukan!'); window.location='daftar_buku.php';</script>";
    exit;
}

// --- VALIDASI BATASAN: ANGGOTA HANYA BOLEH MEMINJAM MAKSIMAL 3 BUKU SECARA MANDIRI ---
$cek_pinjaman_aktif = mysqli_query($conn, "SELECT * FROM pinjam WHERE id_pengguna = '$id_pengguna' AND status != 'dikembalikan'");
$jumlah_dipinjam = mysqli_num_rows($cek_pinjaman_aktif);
$batas_tercapai = ($jumlah_dipinjam >= (int) pengaturan('maks_pinjam_buku', 3));

if (isset($_POST['konfirmasi_pinjam'])) {
    // Cek ulang jumlah pinjaman aktif saat form disubmit
    $cek_ulang_aktif = mysqli_query($conn, "SELECT * FROM pinjam WHERE id_pengguna = '$id_pengguna' AND status != 'dikembalikan'");
    $jumlah_aktif_sekarang = mysqli_num_rows($cek_ulang_aktif);

    if ($jumlah_aktif_sekarang >= (int) pengaturan('maks_pinjam_buku', 3)) {
        echo "<script>alert('Maaf, Anda sudah mencapai batas maksimal " . (int) pengaturan('maks_pinjam_buku', 3) . " buku secara mandiri. Silahkan hubungi admin perpustakaan jika ingin meminjam buku tambahan.'); window.location='buku_pinjam.php';</script>";
        exit;
    }

    if ($data['jumlah_tersedia'] > 0) {
        $tgl_pinjam = date('Y-m-d');
        $tgl_kembali = date('Y-m-d', strtotime('+' . (int) pengaturan('lama_pinjam_hari', 7) . ' days'));

        mysqli_begin_transaction($conn);
        try {
            // Lakukan INSERT saja. Pengurangan stok sudah dihandle otomatis oleh Trigger Database.
            $sql_pinjam = "INSERT INTO pinjam (id_pengguna, id_buku, tanggal_pinjam, tanggal_kembali, status, status_pengajuan) 
                           VALUES ('$id_pengguna', '$id_buku', '$tgl_pinjam', '$tgl_kembali', 'dipinjam', 'aktif')";
            mysqli_query($conn, $sql_pinjam);

            mysqli_commit($conn);
            echo "<script>alert('Peminjaman berhasil!'); window.location='buku_pinjam.php';</script>";
            exit;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "<script>alert('Gagal memproses peminjaman.');</script>";
        }
    } else {
        echo "<script>alert('Maaf, stok sudah habis.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pinjam | E-Perpus SMANTEN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="js/view_pinjam.js"></script>
    <style>
        .sidebar-active { left: 0 !important; }
        .sidebar-hidden { left: -18rem; }
        body { transition: background-color 0.3s, color 0.3s; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 font-sans text-slate-900 dark:text-slate-100">

    <div class="flex min-h-screen relative overflow-x-hidden">
        <!-- SIDEBAR -->
        <aside id="sidebar" class="fixed md:sticky top-0 left-[-18rem] md:left-0 z-40 w-72 h-screen bg-slate-900 dark:bg-white border-r border-slate-800 dark:border-slate-200 p-6 flex flex-col justify-between sidebar-hidden md:transform-none text-slate-300 dark:text-slate-600 transition-transform duration-300 transition-colors duration-300">
            <div>
                <div class="flex items-center gap-3 mb-8 px-2">
                    <div class="w-10 h-10 rounded-xl bg-brand flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                        <i class="fa fa-book-open text-lg"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-base tracking-tight leading-none mb-1 text-white dark:text-slate-900">SMANTEN</h1>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Digital Library</p>
                    </div>
                </div>

                <nav class="space-y-1.5">
                    <p class="px-3 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3">Menu Utama</p>
                    <a href="dashboard.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                        <i class="fa fa-th-large text-base w-5 text-center"></i> Dashboard
                    </a>
                    
                    <?php if ($role == 'admin' || $role == 'pustakawan'): ?>
                    <a href="anggota.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                        <i class="fa fa-users text-base w-5 text-center"></i> Anggota Perpustakaan
                    </a>
                    <?php endif; ?>

                    <?php 
                        $url_buku = ($role == 'anggota') ? 'daftar_buku.php' : 'buku.php';
                        $label_buku = ($role == 'anggota') ? 'Katalog Buku' : 'Manajemen Buku';
                        $active_buku = ($role == 'anggota') ? 'bg-brand text-white font-bold shadow-md shadow-indigo-600/10' : '';
                    ?>
                    <a href="<?php echo $url_buku; ?>" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium <?php echo $active_buku ?: 'text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand'; ?> transition-all">
                        <i class="fa fa-book text-base w-5 text-center"></i> <?php echo $label_buku; ?>
                    </a>

                    <?php 
                        $url_pinjam = ($role == 'anggota') ? 'buku_pinjam.php' : 'pinjam.php';
                        $label_pinjam = ($role == 'anggota') ? 'Buku Dipinjam' : 'Transaksi Pinjam';
                    ?>
                    <a href="<?php echo $url_pinjam; ?>" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                        <i class="fa fa-handshake text-base w-5 text-center"></i> <?php echo $label_pinjam; ?>
                    </a>

                    <?php if ($role == 'admin' || $role == 'pustakawan'): ?>
                    <a href="log.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                        <i class="fa fa-history text-base w-5 text-center"></i> Log Aktivitas
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

        <!-- MAIN CONTENT -->
        <main class="flex-1 p-4 md:p-8 w-full overflow-hidden">
            <header class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 bg-white dark:bg-darkCard border dark:border-slate-700 rounded-lg">
                        <i class="fa fa-bars"></i>
                    </button>
                    <nav aria-label="breadcrumb">
                        <ol class="flex items-center gap-2 text-sm">
                            <li><a href="daftar_buku.php" class="text-slate-400 hover:text-brand">Katalog</a></li>
                            <li class="text-slate-400">/</li>
                            <li class="font-semibold text-slate-700 dark:text-slate-200">Konfirmasi Pinjam</li>
                        </ol>
                    </nav>
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

            <div class="bg-white dark:bg-darkCard rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm p-6 md:p-8">
                
                <?php if ($batas_tercapai): ?>
                <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 rounded-2xl flex items-start gap-3">
                    <i class="fa fa-info-circle text-amber-500 text-lg mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-bold text-amber-800 dark:text-amber-400">Batas Kuota Mandiri Tercapai (3 Buku)</h4>
                        <p class="text-xs text-amber-700 dark:text-amber-300 mt-0.5">Anda saat ini sudah meminjam <strong>3 buku</strong> secara mandiri. Jika ingin meminjam buku tambahan lebih dari 3, silakan <strong>menghubungi atau temui admin/pustakawan</strong> agar admin yang membantu meminjamkan lewat sistem.</p>
                        <div class="mt-3 flex items-center gap-2">
                            <a href="buku_pinjam.php" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-600 text-white text-xs font-bold rounded-xl hover:bg-amber-700 transition">
                                <i class="fa fa-book-reader"></i> Lihat Buku yang Dipinjam
                            </a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="mb-6 px-4 py-3 bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50 rounded-2xl flex items-center justify-between text-xs text-indigo-700 dark:text-indigo-300">
                    <span><i class="fa fa-info-circle mr-1"></i> Status peminjaman aktif Anda saat ini: <strong><?php echo $jumlah_dipinjam; ?> dari <?php echo (int) pengaturan('maks_pinjam_buku', 3); ?> kuota mandiri</strong>.</span>
                    <a href="buku_pinjam.php" class="font-bold underline hover:text-indigo-800">Cek daftar pinjaman</a>
                </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                    
                    <div class="md:col-span-4 text-center md:border-r border-slate-100 dark:border-slate-800 md:pr-6">
                        <div class="w-full max-w-[240px] h-[340px] bg-slate-100 dark:bg-slate-800 rounded-2xl overflow-hidden mx-auto shadow-md mb-4">
                            <?php if(!empty($data['sampul']) && file_exists('uploads/sampul/'.$data['sampul'])): ?>
                                <img src="uploads/sampul/<?php echo $data['sampul']; ?>" class="w-full h-full object-cover" alt="Sampul">
                            <?php else: ?>
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                                    <i class="fa fa-image fa-3x mb-2"></i>
                                    <span class="text-xs">Tanpa Sampul</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h4 class="font-bold text-lg text-slate-900 dark:text-white mb-2"><?php echo htmlspecialchars($data['judul']); ?></h4>
                        <span class="inline-block text-xs font-bold uppercase tracking-wider px-3 py-1 bg-slate-900 text-white dark:bg-white dark:text-slate-900 rounded-lg">
                            <?php echo htmlspecialchars($data['kategori']); ?>
                        </span>
                    </div>

                    <div class="md:col-span-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-950/50 rounded-xl flex items-center justify-center text-brand">
                                <i class="fa fa-info-circle text-base"></i>
                            </div>
                            <h3 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Konfirmasi Peminjaman</h3>
                        </div>

                        <div class="space-y-4 text-sm mb-6">
                            <div class="flex border-b border-slate-100 dark:border-slate-800/60 pb-3">
                                <span class="w-40 text-slate-400 font-semibold uppercase text-xs tracking-wider">Pengarang</span>
                                <span class="flex-1 font-semibold text-slate-800 dark:text-slate-200">: <?php echo htmlspecialchars($data['pengarang'] ?: '-'); ?></span>
                            </div>
                            <div class="flex border-b border-slate-100 dark:border-slate-800/60 pb-3">
                                <span class="w-40 text-slate-400 font-semibold uppercase text-xs tracking-wider">Penerbit</span>
                                <span class="flex-1 font-semibold text-slate-800 dark:text-slate-200">: <?php echo htmlspecialchars($data['penerbit'] ?: '-'); ?></span>
                            </div>
                            <div class="flex pb-1">
                                <span class="w-40 text-slate-400 font-semibold uppercase text-xs tracking-wider">Status Stok</span>
                                <span class="flex-1 font-semibold">
                                    : <span class="inline-block px-2.5 py-0.5 rounded-lg text-xs font-bold <?php echo ($data['jumlah_tersedia'] > 0) ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600' : 'bg-rose-50 dark:bg-rose-950/30 text-rose-600'; ?>">
                                        <?php echo $data['jumlah_tersedia']; ?> Tersedia
                                    </span>
                                </span>
                            </div>
                        </div>
                        
                        <div class="bg-indigo-50/50 dark:bg-slate-800/50 border border-indigo-100 dark:border-slate-700 rounded-2xl p-4 mb-6">
                            <div class="flex gap-3 items-start text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                                <i class="fa fa-calendar-check text-brand text-lg mt-0.5"></i>
                                <div>
                                    <strong class="text-slate-900 dark:text-white block mb-0.5 font-bold">Ketentuan Peminjaman:</strong>
                                    Durasi pinjam maksimal adalah <strong class="text-brand"><?php echo (int) pengaturan('lama_pinjam_hari', 7); ?> hari</strong>. Batas peminjaman mandiri secara online adalah <strong class="text-brand"><?php echo (int) pengaturan('maks_pinjam_buku', 3); ?> buku</strong>. Untuk penambahan pinjaman melebihi batas tersebut, silakan hubungi admin perpustakaan.
                                </div>
                            </div>
                        </div>

                        <form action="" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled = true;" class="flex flex-wrap items-center gap-3">
                            <input type="hidden" name="konfirmasi_pinjam" value="1">
                            <button type="submit"
                                class="bg-brand hover:bg-indigo-700 text-white font-bold text-sm px-6 py-3 rounded-xl transition-all shadow-lg shadow-indigo-500/20 flex items-center gap-2 <?php echo ($data['jumlah_tersedia'] <= 0 || $batas_tercapai) ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''; ?>">
                                <i class="fa fa-hand-holding-medical"></i> Setujui & Pinjam Sekarang
                            </button>
                            <a href="daftar_buku.php" class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold text-sm px-5 py-3 rounded-xl transition-all">
                                Batal
                            </a>
                        </form>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <script src="js/view_pinjam_2.js"></script>
</body>
</html>