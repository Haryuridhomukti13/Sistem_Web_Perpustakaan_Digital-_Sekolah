<?php
session_start();
include 'koneksi.php';

// Proteksi Halaman
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user_logged = $_SESSION['user'];
$role_logged = $user_logged['role'];

// Ambil ID Anggota dari URL
if (!isset($_GET['id'])) {
    header("Location: anggota.php");
    exit;
}

$id_pengguna = mysqli_real_escape_string($conn, $_GET['id']);

// Query ambil data anggota berdasarkan ID
$query = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_pengguna = '$id_pengguna'");
$data  = mysqli_fetch_assoc($query);

// Jika data tidak ditemukan
if (!$data) {
    echo "<script>alert('Data anggota tidak ditemukan!'); window.location='anggota.php';</script>";
    exit;
}

// Logika Foto Profil
$path_foto = 'img/foto/' . $data['foto'];
if (!empty($data['foto']) && file_exists($path_foto)) {
    $gambar = $path_foto;
} else {
    $gambar = "https://ui-avatars.com/api/?name=" . urlencode($data['nama']) . "&background=4F46E5&color=fff&size=512";
}

// Styling Role Badge
$role_style = "text-emerald-600 bg-emerald-50 border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20";
if($data['role'] == 'admin') $role_style = "text-rose-600 bg-rose-50 border-rose-100 dark:bg-rose-900/20 dark:border-rose-500/20";
if($data['role'] == 'pustakawan') $role_style = "text-amber-600 bg-amber-50 border-amber-100 dark:bg-amber-900/20 dark:border-amber-500/20";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil: <?php echo htmlspecialchars($data['nama']); ?> | E-PERPUS</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="js/view_anggota.js"></script>
    <style>
        @media (max-width: 768px) {
            .sidebar-hidden { transform: translateX(-100%); }
            .sidebar-active { transform: translateX(0); }
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-darkBlue text-slate-800 dark:text-slate-100 min-h-screen transition-colors duration-300 font-sans">

<div class="flex min-h-screen relative overflow-x-hidden">
    <aside id="sidebar" class="fixed inset-y-0 left-[-260px] md:relative md:left-0 w-72 bg-darkBlue dark:bg-white text-white dark:text-slate-900 border-r border-white/5 dark:border-slate-200 transition-all duration-300 z-50">
        <div class="p-6 flex flex-col h-full">
            <div>
                <div class="flex items-center gap-3 text-white dark:text-brand font-bold text-xl mb-8">
                    <div class="bg-brand text-white p-2 rounded-lg"><i class="fa fa-graduation-cap"></i></div>
                    <span>E-PERPUS SMANTEN</span>
                </div>
                <nav class="space-y-1.5">
                    <p class="px-3 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3">Menu Utama</p>
                    <a href="dashboard.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                        <i class="fa fa-th-large text-base w-5 text-center"></i> Dashboard
                    </a>

                    <a href="anggota.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-bold bg-brand text-white transition-all shadow-md shadow-indigo-600/10">
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
                </nav>
            </div>

            <div class="mt-auto pt-6 border-t border-white/5 dark:border-slate-100">
                <a href="logout.php" class="flex items-center gap-3 px-3 py-2 text-rose-400 dark:text-rose-600 hover:bg-rose-500/10 dark:hover:bg-rose-50 rounded-xl transition-all">
                    <i class="fa fa-sign-out w-5"></i> Logout
                </a>
            </div>
        </div>
    </aside>

    <main class="flex-1 p-4 md:p-8 min-w-0">
        <header class="flex items-center justify-between mb-8 bg-white dark:bg-darkCard p-4 rounded-2xl border border-slate-100 dark:border-slate-800/60 shadow-sm transition-colors duration-300">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
                    <i class="fa fa-bars text-slate-600 dark:text-slate-300"></i>
                </button>
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Detail Pengguna</h2>
                    <p class="text-xs text-slate-400 hidden sm:block">Ringkasan profil dan informasi akun</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="toggleDarkMode()" class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/50 flex items-center justify-center text-slate-500 dark:text-slate-400 transition-all">
                    <i id="theme-icon" class="fa fa-moon text-base"></i>
                </button>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-darkCard p-4 rounded-[2.5rem] shadow-sm border border-slate-100 dark:border-slate-800 text-center">
                    <div class="aspect-square rounded-[2rem] overflow-hidden shadow-2xl bg-slate-100 dark:bg-slate-800 mb-6">
                        <img src="<?php echo $gambar; ?>" class="w-full h-full object-cover" alt="Foto Profil">
                    </div>
                    
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white"><?php echo htmlspecialchars($data['nama']); ?></h3>
                    <p class="text-brand font-bold text-sm mt-1 mb-6 tracking-widest uppercase">ID: <?php echo htmlspecialchars($data['no_anggota'] ?: '-'); ?></p>

                    <div class="space-y-3">
                        <a href="edit_anggota.php?id=<?php echo $data['id_pengguna']; ?>" class="flex items-center justify-center gap-3 w-full py-4 bg-brand hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-indigo-500/20 transition-all">
                            <i class="fa fa-user-gear"></i> Edit Profil
                        </a>
                        <a href="cetak_kartu.php?id=<?php echo $data['id_pengguna']; ?>" target="_blank" class="flex items-center justify-center gap-3 w-full py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-2xl hover:bg-slate-100 transition-all">
                            <i class="fa fa-print"></i> Cetak Kartu Anggota
                        </a>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-darkCard p-8 rounded-[2.5rem] shadow-sm border border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <span class="px-3 py-1 text-[10px] font-bold rounded-lg uppercase tracking-widest border <?php echo $role_style; ?>">
                                <?php echo htmlspecialchars($data['role']); ?>
                            </span>
                            <h2 class="text-3xl font-bold text-slate-800 dark:text-white mt-4">Informasi Akun</h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fa fa-hashtag"></i> Nomor Anggota
                            </label>
                            <p class="text-slate-700 dark:text-slate-200 font-bold text-lg">
                                <?php echo htmlspecialchars($data['no_anggota'] ?: 'Belum diatur'); ?>
                            </p>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fa fa-envelope"></i> Alamat Email
                            </label>
                            <p class="text-slate-700 dark:text-slate-200 font-semibold"><?php echo htmlspecialchars($data['email']); ?></p>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fa fa-toggle-on"></i> Status Akun
                            </label>
                            <div>
                                <?php if($data['status'] == 'aktif'): ?>
                                    <span class="text-emerald-500 font-bold text-sm flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="text-rose-500 font-bold text-sm flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Non-Aktif
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <i class="fa fa-clock-rotate-left"></i> Terdaftar Sejak
                            </label>
                            <p class="text-slate-700 dark:text-slate-200 font-semibold">
                                <?php echo date('d F Y', strtotime($data['created_at'])); ?>
                            </p>
                        </div>
                    </div>

                    <div class="mt-10 pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-wrap gap-4">
                        <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 rounded-2xl border border-slate-100 dark:border-slate-700">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Username Login</p>
                            <p class="text-lg font-bold dark:text-white"><?php echo htmlspecialchars($data['id_pengguna']); ?></p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 rounded-2xl border border-slate-100 dark:border-slate-700">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">DB Index ID</p>
                            <p class="text-lg font-bold dark:text-white">#<?php echo htmlspecialchars($data['id_pengguna']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-darkBlue dark:bg-white p-6 rounded-[2rem] flex items-center justify-between text-white dark:text-slate-800 shadow-xl border dark:border-slate-200">
                    <div>
                        <p class="text-slate-400 dark:text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-1">Catatan Sistem</p>
                        <p class="font-bold italic">"Anggota ini memiliki hak akses sebagai <?php echo $data['role']; ?>."</p>
                    </div>
                    <i class="fa fa-shield-halved text-3xl opacity-20"></i>
                </div>

            </div>
        </div>
    </main>
</div>

<script src="js/view_anggota_2.js"></script>

</body>
</html>