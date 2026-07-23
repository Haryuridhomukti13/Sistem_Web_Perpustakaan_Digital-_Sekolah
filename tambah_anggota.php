<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];

// Proteksi: Hanya Admin & Pustakawan yang bisa akses
if ($role == 'anggota') {
    header("Location: buku.php");
    exit;
}

// Proses Simpan Data Anggota
if (isset($_POST['submit'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_p   = mysqli_real_escape_string($conn, $_POST['role_pengguna']);
    $status   = mysqli_real_escape_string($conn, $_POST['status']);

    // --- LOGIKA UNGGAH FOTO ---
    $foto = ""; // Default kosong
    if ($_FILES['foto']['name'] != "") {
        $nama_file = $_FILES['foto']['name'];
        $ukuran_file = $_FILES['foto']['size'];
        $error = $_FILES['foto']['error'];
        $tmp_name = $_FILES['foto']['tmp_name'];

        // Cek ekstensi
        $ekstensi_valid = ['jpg', 'jpeg', 'png'];
        $ekstensi_file = explode('.', $nama_file);
        $ekstensi_file = strtolower(end($ekstensi_file));

        if (in_array($ekstensi_file, $ekstensi_valid)) {
            if ($ukuran_file < 2000000) { // Maks 2MB
                // Generate nama baru agar tidak bentrok
                $foto = uniqid() . '.' . $ekstensi_file;
                move_uploaded_file($tmp_name, 'img/' . $foto);
            } else {
                echo "<script>alert('Ukuran file terlalu besar! Maks 2MB');</script>";
            }
        } else {
            echo "<script>alert('Format file tidak didukung! Gunakan JPG/PNG');</script>";
        }
    }
    // ---------------------------

    // Query Menggunakan kolom 'foto' sesuai database
    $query = "INSERT INTO pengguna (nama, email, password, role, status, foto) 
              VALUES ('$nama', '$email', '$password', '$role_p', '$status', '$foto')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Anggota berhasil ditambahkan!'); window.location='anggota.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Anggota | E-PERPUS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="js/tambah_anggota.js"></script>
    <style>
        @media (max-width: 768px) {
            .sidebar-hidden { transform: translateX(-100%); }
            .sidebar-active { transform: translateX(0); }
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-darkBlue text-slate-800 dark:text-slate-100 min-h-screen transition-colors duration-300">

    <div class="flex min-h-screen relative overflow-x-hidden">
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
                    <a href="buku.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                        <i class="fa fa-book text-base w-5 text-center"></i> Manajemen Buku
                    </a>
                    <a href="pinjam.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                        <i class="fa fa-handshake text-base w-5 text-center"></i> Transaksi Pinjam
                    </a>
                    <?php if ($role == 'admin' || $role == 'pustakawan'): ?>
                    <a href="log.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                        <i class="fa fa-history text-base w-5 text-center"></i> Log Aktivitas
                    </a>
                    <?php endif; ?>
                </nav>
            </div>

            <div class="border-t border-slate-800 dark:border-slate-200 pt-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-800 dark:bg-slate-100 flex items-center justify-center font-bold text-brand border border-slate-700 dark:border-slate-200">
                        <?php echo strtoupper(substr($user['nama'], 0, 1)); ?>
                    </div>
                    <div>
                        <p class="text-sm font-semibold max-w-[120px] truncate text-white dark:text-slate-900"><?php echo htmlspecialchars($user['nama']); ?></p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 capitalize"><?php echo htmlspecialchars($role); ?></p>
                    </div>
                </div>
                <a href="logout.php" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-rose-400 dark:hover:text-rose-500 hover:bg-rose-950/30 dark:hover:bg-rose-50 transition-all" title="Keluar">
                    <i class="fa fa-sign-out-alt"></i>
                </a>
            </div>
        </aside>

        <main class="flex-1 p-4 md:p-8 min-w-0">
            <header class="flex items-center justify-between mb-8 bg-white dark:bg-darkCard p-4 rounded-2xl border border-slate-100 dark:border-slate-800/60 shadow-sm transition-colors duration-300">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
                        <i class="fa fa-bars text-slate-600 dark:text-slate-300"></i>
                    </button>
                    <a href="anggota.php" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-800 text-slate-600 dark:text-white hover:text-brand transition-all"><i class="fa fa-arrow-left"></i></a>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">Tambah Anggota</h1>
                        <p class="text-xs text-slate-400 hidden sm:block">Form tambahkan anggota baru ke sistem</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="toggleDarkMode()" class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/50 flex items-center justify-center text-slate-500 dark:text-slate-400 transition-all">
                        <i id="theme-icon" class="fa fa-moon text-base"></i>
                    </button>
                </div>
            </header>

            <div class="max-w-4xl mx-auto">
                <form method="POST" enctype="multipart/form-data" class="bg-white dark:bg-darkCard rounded-[2.5rem] shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">
                    <div class="p-8 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-lg font-bold italic text-brand">Formulir Pengguna Baru</h3>
                        <p class="text-sm text-slate-500">Pastikan alamat email yang dimasukkan aktif.</p>
                    </div>

                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400 px-1">Foto Profil</label>
                                <div class="flex items-center justify-center w-full">
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl cursor-pointer bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 transition-all">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <i class="fa fa-cloud-upload text-2xl text-slate-400 mb-2"></i>
                                            <p class="text-sm text-slate-500"><span class="font-bold text-brand">Klik untuk unggah</span> atau seret file</p>
                                            <p class="text-xs text-slate-400">JPG atau PNG (Maks. 2MB)</p>
                                        </div>
                                        <input type="file" name="foto" class="hidden" accept="image/*" />
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400 px-1">Nama Lengkap</label>
                                <input type="text" name="nama" required placeholder="Contoh: Budi Santoso" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand outline-none transition-all dark:text-white">
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400 px-1">Alamat Email</label>
                                <input type="email" name="email" required placeholder="budi@email.com" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand outline-none transition-all dark:text-white">
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400 px-1">Password</label>
                                <input type="password" name="password" required placeholder="••••••••" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand outline-none transition-all dark:text-white">
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400 px-1">Hak Akses (Role)</label>
                                <select name="role_pengguna" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand outline-none transition-all dark:text-white">
                                    <option value="anggota">Anggota</option>
                                    <option value="pustakawan">Pustakawan</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400 px-1">Status Keanggotaan</label>
                                <div class="flex gap-4">
                                    <label class="flex-1">
                                        <input type="radio" name="status" value="aktif" checked class="hidden peer">
                                        <div class="text-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 peer-checked:bg-emerald-500 peer-checked:text-white cursor-pointer transition-all dark:text-slate-400">Aktif</div>
                                    </label>
                                    <label class="flex-1">
                                        <input type="radio" name="status" value="non-aktif" class="hidden peer">
                                        <div class="text-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 peer-checked:bg-rose-500 peer-checked:text-white cursor-pointer transition-all dark:text-slate-400">Non-Aktif</div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-100 dark:border-slate-800">
                            <button type="submit" name="submit" class="w-full md:w-auto px-10 py-4 bg-brand text-white rounded-2xl font-bold shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 transition-all flex items-center justify-center gap-3">
                                <i class="fa fa-save"></i> Simpan Anggota Baru
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="js/tambah_anggota_2.js"></script>
</body>
</html>