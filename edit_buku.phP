<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];

// Hanya Admin dan Pustakawan yang boleh edit
if ($role == 'anggota') {
    header("Location: buku.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: buku.php");
    exit;
}

$id_buku = mysqli_real_escape_string($conn, $_GET['id']);
$query_lama = mysqli_query($conn, "SELECT * FROM buku WHERE id_buku = '$id_buku'");
$data = mysqli_fetch_assoc($query_lama);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='buku.php';</script>";
    exit;
}

if (isset($_POST['update'])) {
    $isbn            = mysqli_real_escape_string($conn, $_POST['isbn']);
    $judul           = mysqli_real_escape_string($conn, $_POST['judul']);
    $pengarang       = mysqli_real_escape_string($conn, $_POST['pengarang']);
    $penerbit        = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $tahun_terbit    = mysqli_real_escape_string($conn, $_POST['tahun_terbit']);
    $kategori        = mysqli_real_escape_string($conn, $_POST['kategori']);
    $jumlah_total    = mysqli_real_escape_string($conn, $_POST['jumlah_total']);
    $jumlah_tersedia = mysqli_real_escape_string($conn, $_POST['jumlah_tersedia']); // Menangkap input dari form yang benar
    $jumlah_halaman  = mysqli_real_escape_string($conn, $_POST['jumlah_halaman']);
    $deskripsi_buku  = mysqli_real_escape_string($conn, $_POST['deskripsi_buku']);

    // --- UPDATE FILE PDF ---
    // Dipastikan folder 'uploads/' tersedia di server Anda
    $file_pdf = $data['file_pdf']; 
    if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] == 0) {
        $pdf_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $_FILES['file_pdf']['name']);
        if (move_uploaded_file($_FILES['file_pdf']['tmp_name'], 'uploads/' . $pdf_name)) {
            if ($file_pdf && file_exists('uploads/' . $file_pdf)) unlink('uploads/' . $file_pdf);
            $file_pdf = $pdf_name;
        }
    }

    // --- UPDATE SAMPUL BUKU ---
    // Folder disesuaikan dengan kode landing page Anda yaitu 'img/'
    $sampul = $data['sampul']; 
    if (isset($_FILES['sampul']) && $_FILES['sampul']['error'] == 0) {
        $img_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $_FILES['sampul']['name']);
        if (move_uploaded_file($_FILES['sampul']['tmp_name'], 'img/' . $img_name)) {
            if ($sampul && $sampul != 'default.jpg' && file_exists('img/' . $sampul)) unlink('img/' . $sampul);
            $sampul = $img_name;
        }
    }

    // Query update mencakup seluruh kolom yang ada pada database digilib2
    $sql_update = "UPDATE buku SET 
                    isbn = '$isbn', 
                    judul = '$judul', 
                    pengarang = '$pengarang', 
                    penerbit = '$penerbit', 
                    tahun_terbit = '$tahun_terbit', 
                    kategori = '$kategori', 
                    jumlah_total = '$jumlah_total',
                    jumlah_tersedia = '$jumlah_tersedia',
                    file_pdf = '$file_pdf',
                    sampul = '$sampul',
                    jumlah_halaman = '$jumlah_halaman',
                    deskripsi_buku = '$deskripsi_buku'
                   WHERE id_buku = '$id_buku'";

    if (mysqli_query($conn, $sql_update)) {
        echo "<script>alert('Data buku berhasil diperbarui!'); window.location='buku.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Buku | E-PERPUS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { min-height: 100vh; background: #0f172a; color: white; padding: 20px; }
        .sidebar a { display: flex; gap: 10px; padding: 12px; color: #94a3b8; text-decoration: none; border-radius: 8px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #1e293b; color: white; }
        .card { border: none; border-radius: 12px; }
        .preview-img { width: 120px; height: 170px; object-fit: cover; border-radius: 10px; margin-top: 10px; border: 3px solid #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-label { font-weight: 600; color: #475569; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
            <div class="col-md-2 sidebar d-none d-md-block">
            <h4 class="fw-bold mb-4"><i class="fa fa-book-reader text-primary"></i> DIGILIB 2.0</h4>
            <a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>

            <?php if ($role == 'admin' || $role == 'pustakawan'): ?>
                <a href="anggota.php"><i class="fa fa-users"></i> Anggota Perpustakaan</a>
                <a href="buku.php" class="active"><i class="fa fa-book"></i> Manajemen Buku</a>
                <a href="pinjam.php"><i class="fa fa-handshake"></i> Transaksi Pinjam</a>
                <a href="log.php"><i class="fa fa-history"></i> Log Aktivitas</a>
            <?php else: ?>
                <a href="buku.php" class="active"><i class="fa fa-book"></i> Katalog Buku</a>
                <a href="buku_pinjam.php"><i class="fa fa-book-reader"></i> Pinjaman Saya</a>
            <?php endif; ?>

            <hr class="text-secondary">
            <a href="logout.php" class="text-danger"><i class="fa fa-sign-out-alt"></i> Keluar</a>
        </div>

        <div class="col-md-10 p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Edit Buku</h2>
                    <p class="text-muted">Perbarui informasi koleksi buku perpustakaan.</p>
                </div>
                <a href="buku.php" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fa fa-times me-1"></i> Batal
                </a>
            </div>

            <div class="card shadow-sm p-4">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ISBN</label>
                                    <input type="text" name="isbn" class="form-control form-control-lg" value="<?php echo htmlspecialchars($data['isbn']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Judul Buku</label>
                                    <input type="text" name="judul" class="form-control form-control-lg" value="<?php echo htmlspecialchars($data['judul']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pengarang</label>
                                    <input type="text" name="pengarang" class="form-control" value="<?php echo htmlspecialchars($data['pengarang']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Penerbit</label>
                                    <input type="text" name="penerbit" class="form-control" value="<?php echo htmlspecialchars($data['penerbit']); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tahun Terbit</label>
                                    <input type="number" name="tahun_terbit" class="form-control" value="<?php echo htmlspecialchars($data['tahun_terbit']); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Kategori</label>
                                    <input type="text" name="kategori" class="form-control" value="<?php echo htmlspecialchars($data['kategori']); ?>" placeholder="Contoh: Sains/Novel" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Jumlah Halaman</label>
                                    <input type="number" name="jumlah_halaman" class="form-control" value="<?php echo htmlspecialchars($data['jumlah_halaman']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Total Stok</label>
                                    <input type="number" name="jumlah_total" class="form-control" value="<?php echo htmlspecialchars($data['jumlah_total']); ?>" min="0" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Stok Tersedia</label>
                                    <input type="number" name="jumlah_tersedia" class="form-control" value="<?php echo htmlspecialchars($data['jumlah_tersedia']); ?>" min="0" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Deskripsi Buku</label>
                                    <textarea name="deskripsi_buku" class="form-control" rows="4" required><?php echo htmlspecialchars($data['deskripsi_buku']); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 border-start ps-md-4">
                            <div class="mb-4">
                                <label class="form-label text-primary"><i class="fa fa-file-pdf"></i> Dokumen PDF</label>
                                <input type="file" name="file_pdf" class="form-control" accept=".pdf">
                                <?php if(!empty($data['file_pdf'])): ?>
                                    <div class="mt-2 p-2 bg-light rounded border small text-break">
                                        <i class="fa fa-check-circle text-success"></i> <?php echo htmlspecialchars($data['file_pdf']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-primary"><i class="fa fa-image"></i> Sampul Buku</label>
                                <input type="file" name="sampul" class="form-control" accept="image/*">
                                <?php if(!empty($data['sampul'])): ?>
                                    <div class="text-center bg-light p-3 mt-2 rounded">
                                        <p class="small text-muted mb-1">Sampul Saat Ini:</p>
                                        <img src="img/<?php echo htmlspecialchars($data['sampul']); ?>" class="preview-img shadow-sm" onerror="this.src='https://via.placeholder.com/120x170?text=No+Cover'">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-3 border-top d-flex gap-2">
                        <button type="submit" name="update" class="btn btn-warning btn-lg px-5 fw-bold shadow-sm">
                            <i class="fa fa-save me-2"></i> Simpan Perubahan
                        </button>
                        <a href="buku.php" class="btn btn-light btn-lg px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>