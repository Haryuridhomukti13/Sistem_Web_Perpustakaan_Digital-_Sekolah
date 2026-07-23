<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'] ?? 'anggota';

if (!isset($_GET['id'])) {
    header("Location: buku.php");
    exit;
}

$id_buku = mysqli_real_escape_string($conn, $_GET['id']);
// Mengambil data buku
$query = mysqli_query($conn, "SELECT * FROM buku WHERE id_buku = '$id_buku'");
$data  = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data buku tidak ditemukan!'); window.location='buku.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Buku | <?php echo htmlspecialchars($data['judul']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f1f5f9; font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar { min-height: 100vh; background: linear-gradient(180deg,#1e293b,#0f172a); color: white; padding: 20px; }
        .sidebar a { display: flex; gap: 10px; padding: 12px; color: #cbd5e1; text-decoration: none; border-radius: 10px; transition: 0.3s; }
        .sidebar a:hover { background:#334155; color:white; }
        .sidebar a.active { background: #6366f1; color: white; }
        .card { border-radius:20px; border: none; }
        .info-label { color: #94a3b8; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px; }
        .info-value { color: #1e293b; font-size: 1rem; font-weight: 500; margin-bottom: 15px; }
        .book-cover-detail { width: 100%; max-width: 280px; border-radius: 15px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
        .no-cover { height: 380px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; border-radius: 15px; color: #94a3b8; border: 2px dashed #cbd5e1; }
        @media(max-width:768px){ .sidebar { position:fixed; left:-250px; width:250px; transition:0.3s; z-index: 1000; } .sidebar.active { left:0; } }
    </style>
</head>
<body>
<div class="container-fluid">
<div class="row">

<!-- Sidebar -->
<div class="col-md-2 sidebar shadow" id="sidebar">
    <div class="d-flex align-items-center gap-3 mb-4 mt-2">
        <div class="bg-primary p-2 rounded-3"><i class="fa fa-book text-white"></i></div>
        <h5 class="mb-0 fw-bold">DIGILIB 2.0</h5>
    </div>
    <hr class="opacity-10">

    <a href="dashboard.php"><i class="fa fa-home w-20"></i> Dashboard</a>

    <?php if ($role=='admin' || $role=='pustakawan'): ?>
        <a href="anggota.php"><i class="fa fa-users w-20"></i> Anggota Perpustakaan</a>
        <a href="buku.php" class="active"><i class="fa fa-book-open w-20"></i> Manajemen Buku</a>
        <a href="pinjam.php"><i class="fa fa-handshake w-20"></i> Transaksi Pinjam</a>
        <a href="log.php"><i class="fa fa-history w-20"></i> Log Aktivitas</a>
    <?php else: ?>
        <a href="buku.php" class="active"><i class="fa fa-book w-20"></i> Katalog Buku</a>
        <a href="buku_pinjam.php"><i class="fa fa-book-reader w-20"></i> Pinjaman Saya</a>
    <?php endif; ?>

    <div class="mt-5">
        <a href="logout.php" class="text-danger"><i class="fa fa-power-off w-20"></i> Keluar</a>
    </div>
</div>

<!-- Content -->
<div class="col-md-10 p-4 p-md-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="buku.php" class="text-decoration-none">Koleksi</a></li>
                    <li class="breadcrumb-item active">Detail Buku</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark">Informasi Lengkap</h3>
        </div>
        <button class="btn btn-white shadow-sm d-md-none" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
    </div>

    <div class="row g-5">
        <!-- Kolom Sampul -->
        <div class="col-md-4 col-lg-3 text-center">
            <?php 
            // Sesuai dengan folder yang kita buat: uploads/sampul/
            $path_sampul = 'uploads/sampul/' . $data['sampul'];
            if (!empty($data['sampul']) && file_exists($path_sampul)): 
            ?>
                <img src="<?php echo $path_sampul; ?>" class="book-cover-detail" alt="Sampul">
            <?php else: ?>
                <div class="no-cover">
                    <div class="text-center">
                        <i class="fa fa-image fa-3x mb-3 opacity-25"></i><br>
                        <span class="fw-bold">TIDAK ADA SAMPUL</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Kolom Detail -->
        <div class="col-md-8 col-lg-6">
            <div class="card p-4 p-md-5 shadow-sm">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <span class="badge bg-indigo-100 text-primary mb-2 px-3 py-2 rounded-pill uppercase tracking-wider" style="background: #e0e7ff; font-size: 10px; font-weight: 800;">
                            <?php echo htmlspecialchars($data['kategori']); ?>
                        </span>
                        <h2 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($data['judul']); ?></h2>
                        <p class="text-muted small mb-0">Kode ISBN: <span class="font-monospace text-dark"><?php echo htmlspecialchars($data['isbn'] ?: 'N/A'); ?></span></p>
                    </div>
                    <?php if ($role == 'admin' || $role == 'pustakawan'): ?>
                        <a href="edit_buku.php?id=<?php echo $data['id_buku']; ?>" class="btn btn-light border btn-sm rounded-3">
                            <i class="fa fa-edit me-1 text-warning"></i> Edit
                        </a>
                    <?php endif; ?>
                </div>
                
                <hr class="my-4 opacity-5">

                <div class="row">
                    <div class="col-sm-6">
                        <p class="info-label">Penulis / Pengarang</p>
                        <p class="info-value"><?php echo htmlspecialchars($data['pengarang'] ?: '-'); ?></p>

                        <p class="info-label">Penerbit</p>
                        <p class="info-value"><?php echo htmlspecialchars($data['penerbit'] ?: '-'); ?></p>
                        
                        <p class="info-label">Tahun Terbit</p>
                        <p class="info-value"><?php echo htmlspecialchars($data['tahun_terbit'] ?: '-'); ?></p>
                    </div>
                    <div class="col-sm-6">
                        <p class="info-label">Status Ketersediaan</p>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="display-6 fw-bold text-dark"><?php echo $data['jumlah_tersedia']; ?></span>
                            <div class="text-muted leading-tight">
                                <small class="d-block">Tersedia dari</small>
                                <small class="fw-bold"><?php echo $data['jumlah_total']; ?> total koleksi</small>
                            </div>
                        </div>

                        <p class="info-label">Ditambahkan Pada</p>
                        <p class="info-value text-muted small">
                            <i class="fa fa-calendar-alt me-1"></i> 
                            <?php echo date('d M Y, H:i', strtotime($data['created_at'] ?? date('Y-m-d H:i:s'))); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Aksi Cepat -->
        <div class="col-md-12 col-lg-3">
            <div class="card p-4 shadow-sm border-0 bg-white">
                <h6 class="fw-bold mb-4 text-dark"><i class="fa fa-bolt me-2 text-primary"></i>Aksi Cepat</h6>
                
                <?php 
                // Sesuai dengan folder yang kita buat: uploads/pdf/
                $path_pdf = 'uploads/pdf/' . $data['file_pdf'];
                if (!empty($data['file_pdf']) && file_exists($path_pdf)): 
                ?>
                    <a href="<?php echo $path_pdf; ?>" target="_blank" class="btn btn-danger w-100 mb-3 py-3 rounded-4 shadow-sm fw-bold">
                        <i class="fa fa-file-pdf me-2"></i> Baca E-Book
                    </a>
                <?php else: ?>
                    <div class="alert alert-warning border-0 small mb-3 rounded-4">
                        <i class="fa fa-exclamation-triangle me-2"></i> File PDF (Digital) belum tersedia untuk buku ini.
                    </div>
                <?php endif; ?>

                <?php if ($role == 'anggota' && $data['jumlah_tersedia'] > 0): ?>
                    <a href="proses_pinjam.php?id=<?php echo $data['id_buku']; ?>" class="btn btn-success w-100 mb-3 py-3 rounded-4 fw-bold shadow-sm">
                        <i class="fa fa-handshake me-2"></i> Pinjam Fisik
                    </a>
                <?php elseif ($role == 'anggota'): ?>
                    <button class="btn btn-secondary w-100 mb-3 py-3 rounded-4 fw-bold" disabled>Stok Habis</button>
                <?php endif; ?>

                <button class="btn btn-outline-dark w-100 py-2 rounded-4 border-dashed" onclick="window.print()">
                    <i class="fa fa-print me-2"></i> Cetak Detail
                </button>
                
                <a href="buku.php" class="btn btn-link w-100 text-muted text-decoration-none mt-3 small">
                    <i class="fa fa-arrow-left me-1"></i> Kembali ke Katalog
                </a>
            </div>
        </div>
    </div>
</div>

</div>
</div>

<script src="js/view_buku.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>