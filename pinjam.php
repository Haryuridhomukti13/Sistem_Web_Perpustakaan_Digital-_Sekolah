<?php
session_start();
include 'koneksi.php';

// 1. Proteksi Hak Akses Admin/Pustakawan
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'pustakawan')) {
    echo "<script>alert('Akses khusus Admin!'); window.location.href='index.php';</script>";
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];
$hari_ini = date('Y-m-d');
$pesan_sukses = "";
$pesan_error = "";

// 2. LOGIKA PROSES TAMBAH PEMINJAMAN OLEH ADMIN (POST)
if (isset($_POST['tambah_pinjam'])) {
    $id_pengguna = mysqli_real_escape_string($conn, $_POST['id_pengguna']);
    $id_buku = mysqli_real_escape_string($conn, $_POST['id_buku']);
    $tgl_pinjam = $hari_ini;
    $tgl_kembali = date('Y-m-d', strtotime('+' . (int) pengaturan('lama_pinjam_hari', 7) . ' days'));
    
    // Cek ketersediaan stok buku
    $cek_stok = mysqli_query($conn, "SELECT jumlah_tersedia FROM buku WHERE id_buku = '$id_buku'");
    $data_stok = mysqli_fetch_assoc($cek_stok);
    
    if ($data_stok['jumlah_tersedia'] > 0) {
        mysqli_begin_transaction($conn);
        try {
            // Cukup INSERT data. Pengurangan stok dan log otomatis ditangani oleh Trigger Database (`trg_pinjam_after_insert`)
            $insert = mysqli_query($conn, "INSERT INTO pinjam (id_pengguna, id_buku, tanggal_pinjam, tanggal_kembali, status, status_pengajuan) 
                                           VALUES ('$id_pengguna', '$id_buku', '$tgl_pinjam', '$tgl_kembali', 'dipinjam', 'aktif')");
            
            mysqli_commit($conn);
            $pesan_sukses = "Peminjaman baru berhasil dicatat oleh Admin! Batas pengembalian: " . date('d M Y', strtotime($tgl_kembali));
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $pesan_error = "Gagal memproses peminjaman.";
        }
    } else {
        $pesan_error = "Maaf, stok buku ini sedang kosong!";
    }
}

// 3. LOGIKA UPDATE PENGEMBALIAN BUKU (GET)
if (isset($_GET['aksi']) && $_GET['aksi'] == 'kembalikan' && isset($_GET['id'])) {
    $id_pinjam = mysqli_real_escape_string($conn, $_GET['id']);
    
    $cari_buku = mysqli_query($conn, "SELECT id_buku FROM pinjam WHERE id_pinjam = '$id_pinjam' AND status = 'dipinjam'");
    
    if (mysqli_num_rows($cari_buku) > 0) {
        // Cukup ubah status menjadi dikembalikan. Penambahan stok dan log otomatis ditangani oleh Trigger Database (`trg_pinjam_after_update`)
        $update_status = mysqli_query($conn, "UPDATE pinjam SET status = 'dikembalikan', status_pengajuan = 'selesai' WHERE id_pinjam = '$id_pinjam'");
        
        if ($update_status) {
            $pesan_sukses = "Buku berhasil dikembalikan dan stok telah diperbarui oleh sistem!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Buku | E-Perpus SMANTEN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="js/pinjam.js"></script>
    <style>
        .sidebar-active { left: 0 !important; }
        body { transition: background-color 0.3s, color 0.3s; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 font-sans text-slate-900 dark:text-slate-100">

    <div class="flex min-h-screen">
        <!-- SIDEBAR / MAIN MENU -->
        <aside id="sidebar" class="fixed md:sticky top-0 left-0 z-40 w-72 h-screen bg-slate-900 dark:bg-white border-r border-slate-800 dark:border-slate-200 p-6 flex flex-col justify-between transition-transform duration-300 sidebar-hidden md:transform-none text-slate-300 dark:text-slate-600 transition-colors duration-300">
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

           <!-- Menu Navigasi Berdasarkan Role -->
                <nav class="space-y-1.5">
                    <p class="px-3 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3">Menu Utama</p>
                    
                    <a href="dashboard.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                        <i class="fa fa-th-large text-base w-5 text-center"></i> Dashboard
                    </a>
                    
                    <?php if ($role == 'admin' || $role == 'pustakawan'): ?>
                        <a href="anggota.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                            <i class="fa fa-users text-base w-5 text-center"></i> Anggota Perpustakaan
                        </a>
                        <a href="buku.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                            <i class="fa fa-book text-base w-5 text-center"></i> Manajemen Buku
                        </a>
                        <a href="pinjam.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-bold bg-brand text-white transition-all shadow-md shadow-indigo-600/10">
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

        <!-- AREA KONTEN UTAMA -->
        <main class="flex-1 p-4 md:p-8 min-w-0">
            <header class="flex items-center justify-between mb-8 bg-white dark:bg-darkCard p-4 rounded-2xl border border-slate-100 dark:border-slate-800/60 shadow-sm transition-colors duration-300">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
                        <i class="fa fa-bars text-slate-600 dark:text-slate-300"></i>
                    </button>
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Daftar Peminjaman & Notifikasi</h2>
                        <p class="text-xs text-slate-400 hidden sm:block">Kelola transaksi pinjam serta konfirmasi pengembalian buku dari anggota</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="toggleModal('modal-tambah')" class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white text-xs font-semibold rounded-xl hover:bg-indigo-700 shadow-md shadow-indigo-500/10 transition-all">
                        <i class="fa fa-plus"></i> Catat Peminjaman
                    </button>
                    <a href="pinjam.php" class="p-2 w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center" title="Refresh Data">
                        <i class="fa fa-sync text-slate-600 dark:text-slate-300"></i>
                    </a>
                    <button onclick="toggleDarkMode()" class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/50 flex items-center justify-center text-slate-500 dark:text-slate-400 transition-all">
                        <i id="theme-icon" class="fa fa-moon text-base"></i>
                    </button>
                </div>
            </header>

            <!-- NOTIFIKASI PESAN -->
            <?php if(!empty($pesan_sukses)): ?>
                <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 rounded-2xl flex items-center gap-3 text-sm font-medium shadow-sm">
                    <div class="bg-emerald-500 text-white rounded-full p-1.5 w-6 h-6 flex items-center justify-center"><i class="fa-solid fa-check text-xs"></i></div>
                    <span><?= htmlspecialchars($pesan_sukses); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($pesan_error)): ?>
                <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-400 rounded-2xl flex items-center gap-3 text-sm font-medium shadow-sm">
                    <div class="bg-rose-500 text-white rounded-full p-1.5 w-6 h-6 flex items-center justify-center"><i class="fa-solid fa-xmark text-xs"></i></div>
                    <span><?= htmlspecialchars($pesan_error); ?></span>
                </div>
            <?php endif; ?>

            <!-- BAGIAN 1: NOTIFIKASI PERMINTAAN PENGEMBALIAN DARI ANGGOTA -->
            <?php
            $query_notif = "SELECT p.*, b.judul, u.nama 
                            FROM pinjam p 
                            JOIN buku b ON p.id_buku = b.id_buku 
                            JOIN pengguna u ON p.id_pengguna = u.id_pengguna
                            WHERE p.status = 'dipinjam' AND p.status_pengajuan = 'menunggu_konfirmasi'
                            ORDER BY p.tanggal_pinjam DESC";
            $result_notif = mysqli_query($conn, $query_notif);
            if(mysqli_num_rows($result_notif) > 0):
            ?>
            <div class="mb-8 bg-amber-50 dark:bg-amber-950/20 rounded-[2rem] border border-amber-200 dark:border-amber-800/60 p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center text-sm shadow-md">
                        <i class="fa fa-bell"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-amber-900 dark:text-amber-400">Permintaan Pengembalian Buku dari Anggota</h3>
                        <p class="text-xs text-amber-700/80 dark:text-amber-500">Anggota berikut mengajukan pengembalian buku. Silakan periksa kondisi fisik buku dan konfirmasi.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php while($n = mysqli_fetch_assoc($result_notif)): ?>
                    <div class="bg-white dark:bg-darkCard p-4 rounded-2xl border border-amber-100 dark:border-amber-900/40 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-md">Pending Validasi</span>
                                <span class="text-[11px] text-slate-400 font-mono">Tempo: <?= date('d/m/Y', strtotime($n['tanggal_kembali'])); ?></span>
                            </div>
                            <h4 class="font-bold text-sm text-slate-900 dark:text-white line-clamp-1"><?= htmlspecialchars($n['judul']); ?></h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Peminjam: <strong class="text-slate-700 dark:text-slate-300"><?= htmlspecialchars($n['nama']); ?></strong></p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                            <a href="pinjam.php?aksi=kembalikan&id=<?= $n['id_pinjam']; ?>" 
                               onclick="return confirm('Setujui pengembalian buku dari <?= htmlspecialchars($n['nama']); ?>?');" 
                               class="w-full text-center py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                                <i class="fa fa-check mr-1"></i> Setujui & Selesaikan
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- BAGIAN 2: TABEL SEMUA PEMINJAMAN AKTIF -->
            <div class="bg-white dark:bg-darkCard rounded-[2rem] shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="font-bold text-base text-slate-900 dark:text-white">Semua Buku Sedang Dipinjam</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 text-[11px] uppercase tracking-wider font-bold">
                                <th class="px-6 py-4.5">Nama Anggota</th>
                                <th class="px-6 py-4.5">Judul Buku</th>
                                <th class="px-6 py-4.5">Tanggal Pinjam</th>
                                <th class="px-6 py-4.5">Batas Pengembalian</th>
                                <th class="px-6 py-4.5 text-center">Aksi Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-700 dark:text-slate-300">
                            <?php
                            $query = "SELECT p.*, b.judul, u.nama 
                                      FROM pinjam p 
                                      JOIN buku b ON p.id_buku = b.id_buku 
                                      JOIN pengguna u ON p.id_pengguna = u.id_pengguna
                                      WHERE p.status = 'dipinjam'
                                      ORDER BY p.id_pinjam DESC";
                            
                            $result = mysqli_query($conn, $query);

                            if(mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_assoc($result)):
                                    $terlambat = (strtotime($row['tanggal_kembali']) < strtotime($hari_ini));
                            ?>
                            <tr class="hover:bg-indigo-50/20 dark:hover:bg-slate-800/30 transition-all duration-150">
                                <td class="px-6 py-4 font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($row['nama']); ?></td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">
                                    <div class="flex items-center gap-2">
                                        <i class="fa fa-book text-indigo-400"></i>
                                        <span><?= htmlspecialchars($row['judul']); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 font-mono"><?= date('d M Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                <td class="px-6 py-4 text-sm font-mono">
                                    <?php if($terlambat): ?>
                                        <span class="px-2 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 font-bold border border-rose-100 dark:border-rose-900/50 inline-flex items-center gap-1 text-xs">
                                            <i class="fa fa-exclamation-triangle"></i> <?= date('d M Y', strtotime($row['tanggal_kembali'])); ?> (Terlambat)
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-600 dark:text-slate-400 font-medium">
                                            <?= date('d M Y', strtotime($row['tanggal_kembali'])); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="pinjam.php?aksi=kembalikan&id=<?= $row['id_pinjam']; ?>" 
                                       onclick="return confirm('Konfirmasi bahwa buku ini telah dikembalikan oleh anggota?')" 
                                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition-all duration-200 shadow-sm shadow-emerald-600/10 hover:shadow-md">
                                        <i class="fa fa-check-circle"></i> Selesai / Kembalikan
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; 
                            } else { ?>
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center text-slate-400 dark:text-slate-500">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-400 text-xl">
                                            <i class="fa fa-folder-open"></i>
                                        </div>
                                        <p class="text-sm font-medium">Tidak ada peminjaman aktif saat ini.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL POPUP: TAMBAH PEMINJAMAN -->
    <div id="modal-tambah" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden">
        <div class="bg-white dark:bg-darkCard p-6 md:p-8 rounded-[2rem] shadow-xl w-full max-w-md mx-4 border border-slate-100 dark:border-slate-800 transition-all">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-slate-950 dark:text-white flex items-center gap-2">
                    <i class="fa fa-handshake text-brand"></i> Catat Peminjaman Baru
                </h3>
                <button onclick="toggleModal('modal-tambah')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-all">
                    <i class="fa fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="" method="POST" class="space-y-4">
                <!-- PILIH ANGGOTA -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Nama Anggota / Pengguna</label>
                    <select name="id_pengguna" required class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand transition-all">
                        <option value="">-- Pilih Anggota --</option>
                        <?php 
                        $res_user = mysqli_query($conn, "SELECT id_pengguna, nama FROM pengguna ORDER BY nama ASC");
                        while($u = mysqli_fetch_assoc($res_user)):
                        ?>
                            <option value="<?= $u['id_pengguna']; ?>"><?= htmlspecialchars($u['nama']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- PILIH BUKU -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wider">Buku yang Dipinjam</label>
                    <select name="id_buku" required class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand transition-all">
                        <option value="">-- Pilih Buku (Tersedia) --</option>
                        <?php 
                        $res_buku = mysqli_query($conn, "SELECT id_buku, judul, jumlah_tersedia FROM buku WHERE jumlah_tersedia > 0 ORDER BY judul ASC");
                        while($b = mysqli_fetch_assoc($res_buku)):
                        ?>
                            <option value="<?= $b['id_buku']; ?>"><?= htmlspecialchars($b['judul']); ?> (Stok: <?= $b['jumlah_tersedia']; ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- KETERANGAN ATURAN -->
                <div class="p-3 bg-indigo-50/50 dark:bg-indigo-950/20 text-indigo-600 dark:text-indigo-400 rounded-xl text-xs flex items-start gap-2">
                    <i class="fa fa-info-circle mt-0.5"></i>
                    <span>Admin dapat meminjamkan buku meskipun anggota telah mencapai batas kuota mandiri 3 buku. Stok akan berkurang otomatis oleh sistem.</span>
                </div>

                <!-- SUBMIT BUTTONS -->
                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" onclick="toggleModal('modal-tambah')" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-semibold rounded-xl hover:bg-slate-200 transition-all">
                        Batal
                    </button>
                    <button type="submit" name="tambah_pinjam" class="px-5 py-2.5 bg-brand text-white text-xs font-semibold rounded-xl hover:bg-indigo-700 shadow-md shadow-indigo-500/10 transition-all">
                        Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/pinjam_2.js"></script>
</body>
</html>