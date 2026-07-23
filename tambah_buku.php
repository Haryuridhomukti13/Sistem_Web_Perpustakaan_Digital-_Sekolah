<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];

// Proteksi: Hanya Admin & Pustakawan
if ($role == 'anggota') {
    header("Location: buku.php");
    exit;
}

// Proses Simpan Data Buku
if (isset($_POST['submit'])) {
    // Escaping input untuk keamanan SQL Injection
    $isbn            = mysqli_real_escape_string($conn, $_POST['isbn']);
    $judul           = mysqli_real_escape_string($conn, $_POST['judul']);
    $pengarang       = mysqli_real_escape_string($conn, $_POST['pengarang']);
    $penerbit        = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $tahun_terbit    = mysqli_real_escape_string($conn, $_POST['tahun_terbit']);
    $kategori        = mysqli_real_escape_string($conn, $_POST['kategori']);
    $jumlah_total    = mysqli_real_escape_string($conn, $_POST['jumlah_total']);
    $jumlah_halaman  = mysqli_real_escape_string($conn, $_POST['jumlah_halaman']); 
    $deskripsi_buku  = mysqli_real_escape_string($conn, $_POST['deskripsi_buku']); 
    
    $jumlah_tersedia = $jumlah_total; 
    $sampul          = "default.jpg"; 
    $file_pdf        = "";

    // --- LOGIKA UPLOAD SAMPUL (Folder 'uploads/sampul/') ---
    if (isset($_FILES['sampul']) && $_FILES['sampul']['error'] === 0) {
        $ext_sampul = strtolower(pathinfo($_FILES['sampul']['name'], PATHINFO_EXTENSION));
        $sampul = "cover_" . uniqid() . "." . $ext_sampul;
        
        $target_sampul_dir = __DIR__ . '/uploads/sampul/';
        if (!is_dir($target_sampul_dir)) { 
            mkdir($target_sampul_dir, 0777, true); 
        }
        move_uploaded_file($_FILES['sampul']['tmp_name'], $target_sampul_dir . $sampul);
    }

    // --- LOGIKA UPLOAD PDF / DOKUMEN (Folder 'uploads/pdf/') ---
    if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === 0) {
        $file_pdf = time() . '_' . str_replace(' ', '_', $_FILES['file_pdf']['name']);
        
        $target_pdf_dir = __DIR__ . '/uploads/pdf/';
        if (!is_dir($target_pdf_dir)) { 
            mkdir($target_pdf_dir, 0777, true); 
        }
        move_uploaded_file($_FILES['file_pdf']['tmp_name'], $target_pdf_dir . $file_pdf);
    }

    // QUERY DATABASE
    $query = "INSERT INTO buku (isbn, judul, pengarang, penerbit, tahun_terbit, kategori, jumlah_total, jumlah_tersedia, file_pdf, sampul, jumlah_halaman, deskripsi_buku) 
              VALUES ('$isbn', '$judul', '$pengarang', '$penerbit', '$tahun_terbit', '$kategori', '$jumlah_total', '$jumlah_tersedia', '$file_pdf', '$sampul', '$jumlah_halaman', '$deskripsi_buku')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Buku berhasil ditambahkan!'); window.location='buku.php';</script>";
    } else {
        echo "<script>alert('Gagal Simpan: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku | E-PERPUS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="js/tambah_buku.js"></script>
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
                    <a href="buku.php" class="w-10 h-10 flex items-center justify-center bg-white dark:bg-darkCard rounded-xl shadow-sm border border-slate-100 dark:border-slate-800 text-slate-600 dark:text-white hover:text-brand transition-all">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                    <h1 class="text-2xl font-bold tracking-tight">Tambah Buku Baru</h1>
                </div>
            </header>

            <div class="max-w-5xl mx-auto">
                <div class="bg-white dark:bg-darkCard rounded-[2.5rem] shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">
                    <form action="" method="POST" enctype="multipart/form-data" class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            
                            <div class="space-y-4">
                                <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400 px-1">Upload Cover (Gambar)</label>
                                <div class="relative group">
                                    <div id="preview-container" class="w-full aspect-[3/4] bg-slate-50 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-[2rem] flex flex-col items-center justify-center overflow-hidden transition-all group-hover:border-brand">
                                        <i id="placeholder-icon" class="fa fa-image text-4xl text-slate-300 mb-2"></i>
                                        <img id="image-preview" class="hidden w-full h-full object-cover">
                                        <p id="placeholder-text" class="text-[10px] text-slate-400 font-medium text-center px-2">Klik untuk upload JPG/PNG</p>
                                    </div>
                                    <input type="file" name="sampul" id="sampul-input" accept="image/*" required class="absolute inset-0 opacity-0 cursor-pointer">
                                </div>
                            </div>

                            <div class="md:col-span-2 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400 px-1">ISBN</label>
                                        <input type="text" name="isbn" required placeholder="978-..." class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand outline-none transition-all dark:text-white">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400 px-1">Judul Buku</label>
                                        <input type="text" name="judul" required placeholder="Judul buku" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand outline-none transition-all dark:text-white">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400 px-1">Pengarang</label>
                                        <input type="text" name="pengarang" required placeholder="Nama pengarang" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand outline-none transition-all dark:text-white">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400 px-1">Penerbit</label>
                                        <input type="text" name="penerbit" required placeholder="Nama penerbit" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand outline-none transition-all dark:text-white">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400 px-1">Tahun Terbit</label>
                                        <input type="number" name="tahun_terbit" value="2026" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand outline-none transition-all dark:text-white">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400 px-1">Kategori</label>
                                        <input type="text" name="kategori" required placeholder="Contoh: Sains/Novel" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand outline-none transition-all dark:text-white">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400 px-1">Jumlah Halaman</label>
                                        <input type="number" name="jumlah_halaman" required min="1" placeholder="Jumlah halaman" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand outline-none transition-all dark:text-white">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400 px-1">Jumlah Stok</label>
                                        <input type="number" name="jumlah_total" required min="1" class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand outline-none transition-all dark:text-white">
                                    </div>
                                    <div class="space-y-2 md:col-span-2">
                                        <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400 px-1">Upload File PDF</label>
                                        <input type="file" name="file_pdf" accept=".pdf" class="w-full px-5 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:bg-brand file:text-white">
                                    </div>
                                    <div class="space-y-2 md:col-span-2">
                                        <label class="text-[11px] font-bold uppercase tracking-widest text-slate-400 px-1">Deskripsi Buku</label>
                                        <textarea name="deskripsi_buku" required rows="4" placeholder="Tuliskan sinopsis atau deskripsi buku di sini..." class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-brand outline-none transition-all dark:text-white"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-8 mt-8 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                            <button type="submit" name="submit" class="px-10 py-4 bg-brand text-white rounded-2xl font-bold shadow-lg hover:bg-indigo-700 transition-all flex items-center gap-3">
                                <i class="fa fa-plus-circle"></i> Tambahkan Buku
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="js/tambah_buku_2.js"></script>
    <script src="js/tambah_buku_3.js"></script>
</body>
</html>