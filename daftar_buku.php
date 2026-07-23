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

// Proteksi: pustakawan tidak punya menu Katalog Buku (khusus anggota & admin)
if ($role === 'pustakawan') {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku | E-Perpus SMANTEN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="js/daftar_buku.js"></script>
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
                        <a href="daftar_buku.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-bold bg-brand text-white transition-all shadow-md shadow-indigo-600/10">
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
            <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 bg-white dark:bg-darkCard border dark:border-slate-700 rounded-lg">
                        <i class="fa fa-bars"></i>
                    </button>
                    <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">
                        <i class="fa fa-layer-group text-brand"></i> Katalog Buku Tersedia
                    </h1>
                </div>

                <div class="flex items-center w-full sm:w-auto gap-3 self-stretch sm:self-auto justify-end">
                    <form action="" method="GET" class="flex items-center gap-2 w-full sm:w-64">
                        <div class="relative w-full">
                            <input type="text" name="cari" class="w-full pl-4 pr-10 py-2 text-sm bg-white dark:bg-darkCard border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand dark:text-white" placeholder="Cari judul atau pengarang..." value="<?php echo htmlspecialchars($_GET['cari'] ?? ''); ?>">
                            <button type="submit" class="absolute right-3 top-2.5 text-slate-400 dark:text-slate-500 hover:text-brand">
                                <i class="fa fa-search text-sm"></i>
                            </button>
                        </div>
                    </form>

                    <button onclick="toggleDarkMode()" class="p-2 w-10 h-10 shrink-0 bg-white dark:bg-darkCard border dark:border-slate-700 rounded-xl shadow-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                        <i id="theme-icon" class="fa fa-moon text-slate-600 dark:text-yellow-400"></i>
                    </button>

                    <div class="h-10 w-10 shrink-0 rounded-full bg-brand flex items-center justify-center text-white ring-4 ring-indigo-50 dark:ring-indigo-900/40 uppercase font-bold text-sm">
                        <?php echo substr($user['nama'], 0, 1); ?>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php
                $where = "";
                if(isset($_GET['cari']) && !empty($_GET['cari'])){
                    $cari = mysqli_real_escape_string($conn, $_GET['cari']);
                    $where = "WHERE judul LIKE '%$cari%' OR pengarang LIKE '%$cari%' OR kategori LIKE '%$cari%'";
                }

                $query = mysqli_query($conn, "SELECT * FROM buku $where ORDER BY judul ASC");
                
                if(!$query) {
                    echo "<div class='col-span-full p-4 bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 rounded-2xl'>Error: " . mysqli_error($conn) . "</div>";
                } elseif(mysqli_num_rows($query) == 0){
                    echo "<div class='col-span-full text-center py-12'><p class='text-slate-400 dark:text-slate-500'>Buku tidak ditemukan.</p></div>";
                } else {
                    while($row = mysqli_fetch_assoc($query)):
                ?>
                <div class="bg-white dark:bg-darkCard rounded-[1.5rem] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col hover:-translate-y-1 transition-all duration-300 relative group">
                    <span class="absolute top-3 right-3 z-10 text-[10px] font-bold uppercase tracking-wider px-2 py-1 bg-slate-900/80 text-white dark:bg-white/90 dark:text-slate-900 rounded-lg backdrop-blur-sm">
                        <?php echo htmlspecialchars($row['kategori']); ?>
                    </span>
                    
                    <div class="w-full h-72 bg-slate-100 dark:bg-slate-800 overflow-hidden relative">
                        <?php if(!empty($row['sampul']) && file_exists('uploads/sampul/'.$row['sampul'])): ?>
                            <img src="uploads/sampul/<?php echo $row['sampul']; ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="Sampul Buku">
                        <?php else: ?>
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                                <i class="fa fa-image text-4xl mb-2"></i>
                                <span class="text-xs">Tanpa Sampul</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div class="mb-4">
                            <h3 class="font-bold text-base text-slate-900 dark:text-white line-clamp-1" title="<?php echo htmlspecialchars($row['judul']); ?>">
                                <?php echo htmlspecialchars($row['judul']); ?>
                            </h3>
                            <p class="text-slate-400 dark:text-slate-500 text-xs mt-1">Oleh: <?php echo htmlspecialchars($row['pengarang'] ?: '-'); ?></p>
                        </div>
                        
                        <div>
                            <div class="flex justify-between items-center mb-4 text-xs font-medium">
                                <span class="text-slate-500 dark:text-slate-400">Stok: <strong class="text-slate-800 dark:text-white"><?php echo $row['jumlah_tersedia']; ?></strong></span>
                                <?php if($row['jumlah_tersedia'] > 0): ?>
                                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/30 px-2 py-0.5 rounded-lg">Tersedia</span>
                                <?php else: ?>
                                    <span class="text-[10px] font-bold text-rose-600 bg-rose-50 dark:bg-rose-950/30 px-2 py-0.5 rounded-lg">Habis</span>
                                <?php endif; ?>
                            </div>
                            
                            <a href="view_pinjam.php?id=<?php echo $row['id_buku']; ?>" 
                               class="block text-center text-sm font-semibold w-full py-2.5 rounded-xl transition-all <?php echo ($row['jumlah_tersedia'] > 0) ? 'bg-brand hover:bg-indigo-700 text-white shadow-lg shadow-indigo-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-600 pointer-events-none' ?>">
                                <i class="fa fa-handshake mr-1"></i> <?php echo ($row['jumlah_tersedia'] > 0) ? 'Pinjam Buku' : 'Stok Kosong'; ?>
                            </a>
                        </div>
                    </div>
                </div>
                <?php 
                    endwhile; 
                }
                ?>
            </div>
        </main>
    </div>

    <script src="js/daftar_buku_2.js"></script>
</body>
</html>