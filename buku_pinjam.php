<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$id_pengguna = $user['id_pengguna'];
$role = $user['role'];

// Proteksi: pustakawan tidak punya menu Buku Digital Dipinjam (khusus anggota & admin)
if ($role === 'pustakawan') {
    header("Location: dashboard.php");
    exit;
}

// --- LOGIKA MENANGANI PENGAJUAN PENGEMBALIAN DARI ANGGOTA ---
if (isset($_POST['ajukan_kembali'])) {
    $id_pinjam = mysqli_real_escape_string($conn, $_POST['id_pinjam']);
    
    // Update status pengajuan agar admin tahu anggota ingin mengembalikan buku
    $update_req = mysqli_query($conn, "UPDATE pinjam SET status_pengajuan = 'menunggu_konfirmasi' WHERE id_pinjam = '$id_pinjam' AND id_pengguna = '$id_pengguna'");
    
    if ($update_req) {
        echo "<script>alert('Permintaan pengembalian berhasil dikirim ke Admin/Pustakawan. Mohon serahkan buku fisik/tunggu konfirmasi.'); window.location='buku_pinjam.php';</script>";
    } else {
        echo "<script>alert('Gagal mengirim permintaan: " . mysqli_error($conn) . "'); window.location='buku_pinjam.php';</script>";
    }
}

// Ambil data buku pinjaman aktif milik pengguna terkait
$sql = "SELECT p.*, b.judul, b.pengarang, b.sampul, b.file_pdf 
        FROM pinjam p 
        JOIN buku b ON p.id_buku = b.id_buku 
        WHERE p.id_pengguna = '$id_pengguna' 
        AND p.status != 'dikembalikan'
        ORDER BY p.tanggal_pinjam DESC";

$query = mysqli_query($conn, $sql);

if (!$query) {
    die("Kesalahan Database: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Digital Dipinjam | E-Perpus SMANTEN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="js/buku_pinjam.js"></script>
    <style>
        .sidebar-active { left: 0 !important; }
        .sidebar-hidden { left: -18rem; }
        body { transition: background-color 0.3s, color 0.3s; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 font-sans text-slate-900 dark:text-slate-100">

    <div class="flex min-h-screen relative overflow-x-hidden">
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
                        <a href="buku_pinjam.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-bold bg-brand text-white transition-all shadow-md shadow-indigo-600/10">
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
        </aside>        <main class="flex-1 p-4 md:p-8 w-full overflow-hidden">
            <header class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 bg-white dark:bg-darkCard border dark:border-slate-700 rounded-lg">
                        <i class="fa fa-bars"></i>
                    </button>
                    <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">
                        <i class="fa fa-book-reader text-brand"></i> Ruang Baca Digital Anda
                    </h1>
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

            <?php if(mysqli_num_rows($query) > 0): ?>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <?php while($row = mysqli_fetch_assoc($query)): ?>
                    <div class="bg-white dark:bg-darkCard p-5 rounded-[1.5rem] border border-slate-100 dark:border-slate-800 shadow-sm flex gap-4 hover:-translate-y-0.5 transition-all duration-300">
                        <div class="w-24 h-36 bg-slate-100 dark:bg-slate-800 rounded-xl overflow-hidden shrink-0">
                            <?php if(!empty($row['sampul']) && file_exists('uploads/sampul/'.$row['sampul'])): ?>
                                <img src="uploads/sampul/<?php echo $row['sampul']; ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 text-[10px]">
                                    <i class="fa fa-image text-xl mb-1"></i>
                                    <span>No Cover</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex flex-col justify-between flex-1 min-w-0">
                            <div>
                                <h3 class="font-bold text-base text-slate-900 dark:text-white truncate" title="<?php echo htmlspecialchars($row['judul']); ?>">
                                    <?php echo htmlspecialchars($row['judul']); ?>
                                </h3>
                                <p class="text-slate-400 dark:text-slate-500 text-xs mt-0.5">Penulis: <?php echo htmlspecialchars($row['pengarang']); ?></p>
                                
                                <div class="grid grid-cols-2 gap-2 my-3 text-xs">
                                    <div class="border-r border-slate-100 dark:border-slate-800 pr-2">
                                        <span class="text-slate-400 block text-[10px] font-semibold uppercase tracking-wider">Mulai Pinjam</span>
                                        <span class="font-bold text-slate-700 dark:text-slate-300"><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></span>
                                    </div>
                                    <div class="pl-1">
                                        <span class="text-slate-400 block text-[10px] font-semibold uppercase tracking-wider">Batas Tempo</span>
                                        <span class="font-bold text-rose-500"><?php echo date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- TOMBOL BACA & AJUKAN PENGEMBALIAN -->
                            <div class="flex flex-wrap items-center gap-2">
                                <!-- Tombol Baca PDF -->
                                <?php if(!empty($row['file_pdf'])): ?>
                                    <a href="uploads/pdf/<?php echo $row['file_pdf']; ?>" target="_blank" class="inline-flex items-center justify-center gap-1.5 text-xs font-bold bg-rose-500 hover:bg-rose-600 text-white px-3 py-2 rounded-xl transition-all shadow-md shadow-rose-500/10">
                                        <i class="fa fa-file-pdf"></i> Baca PDF
                                    </a>
                                <?php else: ?>
                                    <button class="inline-flex items-center justify-center gap-1.5 text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-600 px-3 py-2 rounded-xl cursor-not-allowed" disabled>
                                        <i class="fa fa-lock"></i> PDF Kosong
                                    </button>
                                <?php endif; ?>

                                <!-- Tombol Berikan Notifikasi / Ajukan Pengembalian ke Admin -->
                                <?php if(isset($row['status_pengajuan']) && $row['status_pengajuan'] == 'menunggu_konfirmasi'): ?>
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold bg-amber-500/10 text-amber-500 px-3 py-2 rounded-xl border border-amber-500/20">
                                        <i class="fa fa-clock"></i> Menunggu Konfirmasi Admin
                                    </span>
                                <?php else: ?>
                                    <form action="" method="POST" onsubmit="return confirm('Kirim pemberitahuan pengembalian buku ini ke Admin/Pustakawan?');">
                                        <input type="hidden" name="id_pinjam" value="<?php echo $row['id_pinjam']; ?>">
                                        <button type="submit" name="ajukan_kembali" class="inline-flex items-center justify-center gap-1.5 text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white px-3 py-2 rounded-xl transition-all shadow-md shadow-amber-500/10">
                                            <i class="fa fa-paper-plane"></i> Ajukan Pengembalian
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="bg-white dark:bg-darkCard text-center p-10 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm max-w-xl mx-auto mt-6">
                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 dark:text-slate-500">
                        <i class="fa fa-info-circle text-2xl"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Tidak Ada Pinjaman Aktif</h2>
                    <p class="text-slate-400 dark:text-slate-500 text-sm leading-relaxed mb-6">Jika Anda baru saja mengembalikan buku atau masa pinjam telah habis divalidasi admin, buku akan otomatis dihapus dari daftar ruang baca ini.</p>
                    <a href="daftar_buku.php" class="inline-flex items-center gap-2 bg-brand hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-indigo-500/20 transition-all">
                        <i class="fa fa-search"></i> Buka Katalog Buku
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="js/buku_pinjam_2.js"></script>
</body>
</html>