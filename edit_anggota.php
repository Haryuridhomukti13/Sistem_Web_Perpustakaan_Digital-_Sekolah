<?php
session_start();
include 'koneksi.php';

// 1. Proteksi Halaman
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];

// Hanya Admin/Pustakawan yang boleh edit anggota
if ($role == 'anggota') {
    header("Location: dashboard.php");
    exit;
}

// 2. Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: anggota.php");
    exit;
}

$id_edit = mysqli_real_escape_string($conn, $_GET['id']);

// 3. Query ambil data lama pengguna
$query_lama = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_pengguna = '$id_edit'");
$data = mysqli_fetch_assoc($query_lama);

if (!$data) {
    echo "<script>alert('Data anggota tidak ditemukan!'); window.location='anggota.php';</script>";
    exit;
}

// 4. Proses Update Data
if (isset($_POST['update'])) {
    // --- LOGIKA BARU: Ambil No Anggota ---
    $no_agt = mysqli_real_escape_string($conn, $_POST['no_anggota']); 
    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $role_p = mysqli_real_escape_string($conn, $_POST['role_pengguna']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Logika Password
    $pass_query = "";
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $pass_query = ", password = '$password'";
    }

    // Logika Upload Foto Profil
    $foto_nama = $data['foto']; // Default pakai foto lama
    
    if ($_FILES['foto']['error'] === 0) {
        $ekstensi_valid = ['jpg', 'jpeg', 'png'];
        $nama_file = $_FILES['foto']['name'];
        $ukuran_file = $_FILES['foto']['size'];
        $tmp_name = $_FILES['foto']['tmp_name'];
        $ekstensi_file = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        if (in_array($ekstensi_file, $ekstensi_valid)) {
            if ($ukuran_file <= 2000000) {
                $target_dir = "img/foto/";
                
                // Hapus foto lama jika ada
                if (!empty($data['foto']) && file_exists($target_dir . $data['foto'])) {
                    unlink($target_dir . $data['foto']);
                }
                
                $foto_nama = uniqid() . '.' . $ekstensi_file;
                move_uploaded_file($tmp_name, $target_dir . $foto_nama);
            } else {
                echo "<script>alert('Ukuran file terlalu besar! Max 2MB');</script>";
            }
        } else {
            echo "<script>alert('Format file harus JPG/PNG!');</script>";
        }
    }

    // --- LOGIKA BARU: Tambahkan no_anggota di baris pertama UPDATE ---
    $sql_update = "UPDATE pengguna SET 
                    no_anggota = '$no_agt', 
                    nama = '$nama', 
                    email = '$email', 
                    foto = '$foto_nama',
                    role = '$role_p', 
                    status = '$status' 
                    $pass_query
                   WHERE id_pengguna = '$id_edit'";

    if (mysqli_query($conn, $sql_update)) {
        echo "<script>alert('Data anggota berhasil diperbarui!'); window.location='anggota.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota | E-PERPUS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="js/edit_anggota.js"></script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 font-sans text-slate-900 dark:text-slate-100 transition-colors duration-300">

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

        <main class="flex-1 p-4 md:p-8 w-full">
            <header class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-4">
                    <a href="anggota.php" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-800 text-slate-600 dark:text-white hover:text-brand transition-all">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                    <h1 class="text-2xl font-bold tracking-tight">Edit Anggota</h1>
                </div>

                <button onclick="toggleDarkMode()" class="p-2 w-10 h-10 bg-white dark:bg-darkCard border dark:border-slate-700 rounded-xl shadow-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-800">
                    <i id="theme-icon" class="fa fa-moon text-slate-600 dark:text-yellow-400"></i>
                </button>
            </header>

            <div class="max-w-4xl mx-auto">
                <div class="bg-white dark:bg-darkCard rounded-[2.5rem] shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">
                    <div class="p-8 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/30">
                        <div class="flex items-center gap-5">
                            <div class="relative group">
                                <?php if (!empty($data['foto']) && file_exists('img/foto/' . $data['foto'])): ?>
                                    <img src="img/foto/<?php echo $data['foto']; ?>" class="w-20 h-20 rounded-[1.5rem] object-cover ring-4 ring-white dark:ring-slate-700 shadow-md">
                                <?php else: ?>
                                    <div class="w-20 h-20 rounded-[1.5rem] bg-amber-100 dark:bg-amber-500/10 flex items-center justify-center text-amber-500 font-bold text-2xl border border-amber-200 dark:border-amber-500/20">
                                        <?php echo strtoupper(substr($data['nama'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-800 dark:text-white"><?php echo htmlspecialchars($data['nama']); ?></h3>
                                <p class="text-sm text-slate-500">ID Anggota: <span class="font-mono">#<?php echo $data['id_pengguna']; ?></span></p>
                            </div>
                        </div>
                        <div class="hidden md:flex h-12 w-12 rounded-2xl bg-brand/10 flex items-center justify-center text-brand border border-brand/20">
                            <i class="fa fa-user-pen text-xl"></i>
                        </div>
                    </div>

                    <form method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-brand px-1">Nomor Anggota</label>
                                <input type="text" name="no_anggota" value="<?php echo htmlspecialchars($data['no_anggota']); ?>" placeholder="Masukkan nomor identitas..."
                                    class="w-full px-5 py-3.5 bg-indigo-50/30 dark:bg-slate-800/80 border border-brand/20 dark:border-brand/30 rounded-2xl focus:ring-2 focus:ring-brand outline-none transition-all dark:text-white font-semibold">
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400 px-1">Nama Lengkap</label>
                                <input type="text" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required
                                    class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all dark:text-white">
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400 px-1">Alamat Email</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required
                                    class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all dark:text-white">
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400 px-1">Ganti Foto Profil (JPG/PNG)</label>
                                <input type="file" name="foto" 
                                    class="w-full px-5 py-2.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-brand file:text-white hover:file:bg-brand/90 transition-all dark:text-slate-400 text-sm">
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400 px-1">Hak Akses (Role)</label>
                                <select name="role_pengguna" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all dark:text-white appearance-none">
                                    <option value="anggota" <?php if($data['role']=='anggota') echo 'selected'; ?>>Anggota</option>
                                    <option value="pustakawan" <?php if($data['role']=='pustakawan') echo 'selected'; ?>>Pustakawan</option>
                                    <option value="admin" <?php if($data['role']=='admin') echo 'selected'; ?>>Admin</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400 px-1">Ganti Password (Opsional)</label>
                                <div class="relative">
                                    <input type="password" name="password" id="passInput" placeholder="Kosongkan jika tidak diubah"
                                        class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand focus:border-brand outline-none transition-all dark:text-white">
                                    <button type="button" onclick="togglePass()" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-brand">
                                        <i id="eyeIcon" class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400 px-1">Status Keanggotaan</label>
                                <div class="flex gap-4">
                                    <label class="flex-1">
                                        <input type="radio" name="status" value="aktif" <?php if($data['status']=='aktif') echo 'checked'; ?> class="hidden peer">
                                        <div class="text-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 cursor-pointer transition-all dark:text-slate-400 font-bold text-sm">
                                            Aktif
                                        </div>
                                    </label>
                                    <label class="flex-1">
                                        <input type="radio" name="status" value="non-aktif" <?php if($data['status']=='non-aktif') echo 'checked'; ?> class="hidden peer">
                                        <div class="text-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 peer-checked:bg-rose-500 peer-checked:text-white peer-checked:border-rose-500 cursor-pointer transition-all dark:text-slate-400 font-bold text-sm">
                                            Non-Aktif
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row gap-4">
                            <button type="submit" name="update" class="flex-1 px-10 py-4 bg-brand text-white rounded-2xl font-bold shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 transition-all flex items-center justify-center gap-3">
                                <i class="fa fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="anggota.php" class="px-10 py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-2xl font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all text-center">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="js/edit_anggota_2.js"></script>
</body>
</html>