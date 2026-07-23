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

// Proteksi: Hanya Admin dan Pustakawan yang bisa melihat Log Aktivitas
if ($role == 'anggota') {
    header("Location: dashboard.php");
    exit;
}

// Pagination log
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$limit = 10;
$offset = ($page - 1) * $limit;
$total_count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM log_aktivitas");
$total_count_row = mysqli_fetch_assoc($total_count_result);
$total_log = (int) $total_count_row['total'];
$total_pages = max(1, (int) ceil($total_log / $limit));

// Query untuk mengambil data log aktivitas dan menggabungkannya dengan nama pengguna
$query_log = mysqli_query($conn, "SELECT l.*, p.nama 
                                  FROM log_aktivitas l 
                                  LEFT JOIN pengguna p ON l.id_pengguna = p.id_pengguna 
                                  ORDER BY l.created_at DESC 
                                  LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas | E-Perpus SMANTEN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="js/log.js"></script>
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
                        <a href="log.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-bold bg-brand text-white transition-all shadow-md shadow-indigo-600/10">
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
            <header class="flex items-center justify-between mb-8 bg-white dark:bg-darkCard p-4 rounded-2xl border border-slate-100 dark:border-slate-800/60 shadow-sm transition-colors duration-300">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
                        <i class="fa fa-bars text-slate-600 dark:text-slate-300"></i>
                    </button>
                    <div>
                        <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Log Aktivitas</h2>
                        <p class="text-xs text-slate-400 hidden sm:block">Pantau aktivitas sistem dan pengguna perpustakaan.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="window.location.reload()" class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all" title="Refresh">
                        <i class="fa fa-sync"></i>
                    </button>
                    <button onclick="toggleDarkMode()" class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                        <i id="theme-icon" class="fa fa-moon text-base"></i>
                    </button>
                </div>
            </header>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Rekam jejak aktivitas pengguna dan sistem perpustakaan.</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 px-5 py-4 shadow-sm">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 dark:text-slate-500">Total Log</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white"><?php echo number_format($total_log); ?></p>
                </div>
            </div>

            <div class="bg-white dark:bg-darkCard rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm p-6 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400 dark:text-slate-500 uppercase text-xs tracking-wider font-semibold">
                                <th class="pb-4 pt-2 px-4 w-1/6">Waktu</th>
                        <th class="pb-4 pt-2 px-4 w-1/5">Pengguna</th>
                        <th class="pb-4 pt-2 px-4">Aktivitas</th>
                        <th class="pb-4 pt-2 px-4 w-1/6">IP Address</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-800/50">
                    <?php if (mysqli_num_rows($query_log) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($query_log)): ?>
                        <?php 
                            $log_raw = $row['aktivitas']; 
                            if (strpos($log_raw, '|') !== false) {
                                list($aktivitas_badge, $aktivitas_detail) = explode('|', $log_raw, 2);
                            } else {
                                $aktivitas_badge = $log_raw;
                                $aktivitas_detail = "";
                            }
                        ?>
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-4">
                                <span class="text-slate-400 dark:text-slate-500 block text-xs"><?php echo date('d/m/Y', (int)$row['created_at']); ?></span>
                                <span class="font-bold text-slate-700 dark:text-slate-300"><?php echo date('H:i:s', (int)$row['created_at']); ?></span>
                            </td>
                            <td class="py-4 px-4 font-medium text-slate-800 dark:text-slate-200">
                                <div class="flex items-center gap-2">
                                    <i class="fa fa-user-circle text-slate-400"></i>
                                    <span>
                                        <?php 
                                            if (!empty($row['nama'])) {
                                                echo htmlspecialchars($row['nama']);
                                            } else {
                                                if ($aktivitas_badge == "TAMBAH BUKU") echo "System/Tambah";
                                                elseif ($aktivitas_badge == "EDIT BUKU") echo "System/Edit";
                                                elseif ($aktivitas_badge == "HAPUS BUKU") echo "System/Hapus";
                                                else echo "System/Sistem";
                                            }
                                        ?>
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-4 space-y-2">
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/50">
                                        <?php echo htmlspecialchars($aktivitas_badge); ?>
                                    </span>
                                </div>
                                <?php if (!empty(trim($aktivitas_detail))): ?>
                                    <p class="text-sm text-slate-500 dark:text-slate-400"><?php echo htmlspecialchars($aktivitas_detail); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-mono text-xs px-2 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-md border dark:border-slate-700">
                                    <?php echo htmlspecialchars($row['ip_address'] ?: '-'); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-12 text-slate-400 dark:text-slate-500">
                                <i class="fa fa-inbox text-4xl mb-3 block opacity-50"></i>
                                Belum ada rekaman aktivitas.
                            </td>
                        </tr>
                    <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Menampilkan <?php echo ($total_log > 0) ? ($offset + 1) : 0; ?> sampai <?php echo min($offset + $limit, $total_log); ?> dari <?php echo number_format($total_log); ?> aktivitas</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">Sebelumnya</a>
                        <?php endif; ?>

                        <?php 
                            // ==== LOGIKA SLIDING WINDOW PAGINATION ====
                            $max_links = 10; // Maksimal tombol angka yang muncul
                            $start_page = max(1, $page - floor($max_links / 2));
                            $end_page = $start_page + $max_links - 1;

                            if ($end_page > $total_pages) {
                                $end_page = $total_pages;
                                $start_page = max(1, $end_page - $max_links + 1);
                            }
                        ?>

                        <?php if ($start_page > 1): ?>
                            <span class="px-2 text-slate-400">...</span>
                        <?php endif; ?>

                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="px-3 py-2 rounded-xl transition-all <?php echo ($i == $page) ? 'bg-brand text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>

                        <?php if ($end_page < $total_pages): ?>
                            <span class="px-2 text-slate-400">...</span>
                        <?php endif; ?>

                        <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">Selanjutnya</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/log_2.js"></script>
</body>
</html>