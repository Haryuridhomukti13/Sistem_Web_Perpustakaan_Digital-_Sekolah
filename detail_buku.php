<?php
// Konfigurasi Koneksi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "digilib2"; // DISESUAIKAN: dari digilib menjadi digilib2 sesuai gambar

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil ID dari URL dan proteksi dari SQL Injection
$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

// Query ambil data buku spesifik
$query = "SELECT * FROM buku WHERE id_buku = '$id'";
$result = mysqli_query($conn, $query);
$buku = mysqli_fetch_assoc($result);

// Jika buku tidak ditemukan, arahkan kembali ke index
if (!$buku) {
    header("Location: index.php");
    exit;
}

// DISESUAIKAN: Mengubah 'cover_buku' menjadi 'sampul' sesuai struktur database
$nama_file = !empty($buku['sampul']) ? $buku['sampul'] : 'default.jpg';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($buku['judul']); ?> | Detail Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f6; color: #333; }
        .navbar { background-color: #1a1a1a; }
        
        /* Kontainer Utama */
        .book-container { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: 20px; }
        
        /* Bagian Cover */
        .book-cover-wrapper { padding: 40px; background: #fdfdfd; display: flex; justify-content: center; align-items: center; border-right: 1px solid #eee; }
        .book-cover { width: 100%; max-width: 320px; border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.15); transition: transform 0.3s ease; }
        .book-cover:hover { transform: translateY(-5px); }
        
        /* Bagian Informasi */
        .book-info { padding: 50px; }
        .badge-category { background-color: #eef2ff; color: #4338ca; font-weight: 600; padding: 8px 16px; border-radius: 50px; font-size: 0.8rem; text-transform: uppercase; }
        .book-title { font-weight: 800; color: #111827; margin-top: 20px; line-height: 1.2; }
        .book-author { font-size: 1.1rem; color: #6b7280; margin-bottom: 30px; }
        
        /* Grid Detail */
        .detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 35px; }
        .detail-item { padding: 15px; border-radius: 12px; border: 1px solid #f3f4f6; }
        .detail-label { display: block; font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; font-weight: 700; margin-bottom: 4px; }
        .detail-value { font-weight: 600; color: #1f2937; display: block; }
        
        /* Tombol */
        .btn-action { padding: 14px 28px; font-weight: 600; border-radius: 12px; transition: all 0.3s; text-decoration: none; }
        .btn-pinjam { background-color: #2563eb; border: none; color: white; }
        .btn-pinjam:hover { background-color: #1d4ed8; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); color: white; }
        .btn-pinjam.disabled { background-color: #d1d5db; cursor: not-allowed; transform: none; color: #9ca3af; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">E-PERPUS</a>
        <a href="index.php" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</nav>

<div class="container py-5">
    <div class="book-container">
        <div class="row g-0">
            <div class="col-md-5 book-cover-wrapper">
                <img src="uploads/cover/<?php echo htmlspecialchars($nama_file); ?>" 
                     class="book-cover" 
                     alt="Cover <?php echo htmlspecialchars($buku['judul']); ?>"
                     onerror="this.onerror=null;this.src='https://via.placeholder.com/400x600?text=No+Cover';">
            </div>

            <div class="col-md-7">
                <div class="book-info">
                    <span class="badge-category"><?php echo !empty($buku['kategori']) ? htmlspecialchars($buku['kategori']) : 'General'; ?></span>
                    <h1 class="book-title"><?php echo htmlspecialchars($buku['judul']); ?></h1>
                    <p class="book-author">Karya <span class="text-dark fw-bold"><?php echo htmlspecialchars($buku['pengarang']); ?></span></p>

                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">ISBN</span>
                            <span class="detail-value"><?php echo !empty($buku['isbn']) ? htmlspecialchars($buku['isbn']) : '-'; ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Penerbit</span>
                            <span class="detail-value"><?php echo htmlspecialchars($buku['penerbit']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tahun Terbit</span>
                            <span class="detail-value"><?php echo htmlspecialchars($buku['tahun_terbit']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Jumlah Halaman</span>
                            <span class="detail-value"><?php echo !empty($buku['jumlah_halaman']) ? htmlspecialchars($buku['jumlah_halaman']) : '-'; ?> Hlm</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Ketersediaan</span>
                            <?php if($buku['jumlah_tersedia'] > 0): ?>
                                <span class="detail-value text-success"><?php echo htmlspecialchars($buku['jumlah_tersedia']); ?> Buku Ready</span>
                            <?php else: ?>
                                <span class="detail-value text-danger">Sedang Dipinjam</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h6 class="fw-bold mb-3 text-uppercase small" style="letter-spacing: 1px;">Tentang Buku</h6>
                        <p class="text-muted lh-lg">
                            <?php 
                            if (!empty($buku['deskripsi_buku'])) {
                                echo nl2br(htmlspecialchars($buku['deskripsi_buku']));
                            } else {
                                echo 'Dapatkan wawasan mendalam dari buku "' . htmlspecialchars($buku['judul']) . '". Koleksi ini diterbitkan oleh ' . htmlspecialchars($buku['penerbit']) . ' dan tersedia bagi seluruh anggota perpustakaan digital.';
                            }
                            ?>
                        </p>
                    </div>

                    <div class="d-grid d-md-flex gap-3">
                        <a href="proses_pinjam.php?id=<?php echo $buku['id_buku']; ?>" class="btn btn-pinjam btn-action <?php echo ($buku['jumlah_tersedia'] <= 0) ? 'disabled' : ''; ?>">
                            <i class="bi bi-bookmark-plus me-2"></i> Pinjam Sekarang
                        </a>                    
                        <button id="btnShare" class="btn btn-outline-secondary btn-action">
                            <i class="bi bi-share me-2"></i> Bagikan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-center py-4 text-muted small">
    &copy; 2026 E-Perpus Digital. Dikembangkan untuk kemudahan membaca.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const shareBtn = document.querySelector('#btnShare');

    shareBtn.addEventListener('click', event => {
        if (navigator.share) {
            navigator.share({
                title: '<?php echo addslashes($buku['judul']); ?>',
                text: 'Cek buku "<?php echo addslashes($buku['judul']); ?>" karya <?php echo addslashes($buku['pengarang']); ?> di E-Perpus!',
                url: window.location.href
            }).then(() => {
                console.log('Berhasil membagikan!');
            })
            .catch((error) => {
                console.log('Gagal membagikan:', error);
            });
        } else {
            const dummy = document.createElement('input');
            const text = window.location.href;

            document.body.appendChild(dummy);
            dummy.value = text;
            dummy.select();
            document.execCommand('copy');
            document.body.removeChild(dummy);

            alert('Link buku berhasil disalin ke clipboard!');
        }
    });
</script>
</body>
</html>