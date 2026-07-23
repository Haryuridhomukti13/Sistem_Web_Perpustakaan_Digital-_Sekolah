<?php 
include "koneksi.php"; 
session_start();

// --- LOGIKA AMBIL DATA BUKU ---
$buku_list = [];
$res_buku = mysqli_query($conn, "SELECT * FROM buku ORDER BY id_buku DESC");
if ($res_buku && mysqli_num_rows($res_buku) > 0) {
    while($row = mysqli_fetch_assoc($res_buku)) {
        $path = 'uploads/sampul/' . $row['sampul'];
        $row['cover'] = (!empty($row['sampul']) && file_exists(__DIR__ . '/' . $path)) ? $path : "https://picsum.photos/seed/book/300/400";
        
        $buku_list[] = [
            'id' => $row['id_buku'],
            'judul' => $row['judul'],
            'penulis' => $row['pengarang'],
            'status' => 'Tersedia',
            'cover' => $row['cover']
        ];
    }
}

// --- LOGIKA PAGINATION & DATA PENGUMUMAN (KOLOM KIRI - 5 per halaman) ---
$limit_p = 5;
$page_p = isset($_GET['page_p']) ? (int)$_GET['page_p'] : 1;
$start_p = ($page_p > 1) ? ($page_p * $limit_p) - $limit_p : 0;

$total_p_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM pengumuman_dan_berita WHERE kategori = 'Pengumuman'");
$total_p_data = mysqli_fetch_assoc($total_p_query)['total'] ?? 0;
$pages_p = ceil($total_p_data / $limit_p);

$pengumuman_list = [];
$res_pengumuman = mysqli_query($conn, "SELECT * FROM pengumuman_dan_berita WHERE kategori = 'Pengumuman' ORDER BY tanggal_dibuat DESC LIMIT $start_p, $limit_p");
if ($res_pengumuman && mysqli_num_rows($res_pengumuman) > 0) {
    while($row_p = mysqli_fetch_assoc($res_pengumuman)) {
        $pengumuman_list[] = [
            'id' => $row_p['id_pengumuman'],
            'judul' => $row_p['judul'],
            'isi' => $row_p['isi'],
            'tanggal' => date('d M Y', strtotime($row_p['tanggal_dibuat']))
        ];
    }
}

// --- LOGIKA PAGINATION & DATA BERITA (KOLOM KANAN - 2 per halaman) ---
$limit_b = 2;
$page_b = isset($_GET['page_b']) ? (int)$_GET['page_b'] : 1;
$start_b = ($page_b > 1) ? ($page_b * $limit_b) - $limit_b : 0;

$total_b_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM pengumuman_dan_berita WHERE kategori = 'Berita'");
$total_b_data = mysqli_fetch_assoc($total_b_query)['total'] ?? 0;
$pages_b = ceil($total_b_data / $limit_b);

$berita_list = [];
$res_berita = mysqli_query($conn, "SELECT * FROM pengumuman_dan_berita WHERE kategori = 'Berita' ORDER BY tanggal_dibuat DESC LIMIT $start_b, $limit_b");
if ($res_berita && mysqli_num_rows($res_berita) > 0) {
    while($row_b = mysqli_fetch_assoc($res_berita)) {
        $path_berita = 'uploads/sampul_berita/' . $row_b['sampul_berita'];
        $cover_berita = (!empty($row_b['sampul_berita']) && file_exists(__DIR__ . '/' . $path_berita)) ? $path_berita : "https://picsum.photos/seed/berita{$row_b['id_pengumuman']}/600/400";
        
        $berita_list[] = [
            'id' => $row_b['id_pengumuman'],
            'judul' => $row_b['judul'],
            'isi' => $row_b['isi'],
            'tanggal' => date('d M Y', strtotime($row_b['tanggal_dibuat'])),
            'cover' => $cover_berita
        ];
    }
}

// --- LOGIKA DATA GALERI & PRESTASI (Semua data diambil untuk JS Slider/Pagination) ---
$galeri_list_all = [];
$res_galeri = mysqli_query($conn, "SELECT * FROM galeri_prestasi ORDER BY tanggal DESC");
if ($res_galeri && mysqli_num_rows($res_galeri) > 0) {
    while($row_g = mysqli_fetch_assoc($res_galeri)) {
        $path_galeri = 'uploads/foto_prestasi/' . $row_g['gambar'];
        $cover_galeri = (!empty($row_g['gambar']) && $row_g['gambar'] != 'default_prestasi.jpg' && file_exists(__DIR__ . '/' . $path_galeri)) ? $path_galeri : "https://picsum.photos/seed/prestasi{$row_g['id_prestasi']}/600/400";
        
        $galeri_list_all[] = [
            'id' => $row_g['id_prestasi'],
            'judul' => $row_g['judul'],
            'nama_peraih' => $row_g['nama_peraih'],
            'kategori' => $row_g['kategori'],
            // Hapus tag HTML agar aman saat dirender di JS
            'deskripsi' => strip_tags($row_g['deskripsi']), 
            'tanggal' => date('d M Y', strtotime($row_g['tanggal'])),
            'cover' => $cover_galeri
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Perpus | SMAN 10 Kota Harapan Bangsa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-slider {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1; background-size: cover; background-position: center;
        }
        @keyframes bgZoom { from { transform: scale(1); } to { transform: scale(1.1); } }
        .hero-overlay { background: linear-gradient(to bottom, rgba(0,0,0,0.5), rgba(0,0,0,0.8)); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn .5s ease both; }
        
        /* Glassmorphism khusus untuk tombol navigasi JS */
        .glass-btn {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .glass-btn:hover {
            background: #2563eb; /* blue-600 */
            color: white !important;
            border-color: transparent;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 overflow-x-hidden">

    <nav class="fixed w-full z-50 bg-white shadow-sm border-b border-gray-100">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <a href="#" class="flex items-center gap-3">
                <img src="img/logo/<?= htmlspecialchars(pengaturan('logo') ?? 'logo.png') ?>" alt="Logo Sekolah" class="h-14 w-14 object-contain">
                <span class="text-green-800 font-bold leading-tight text-lg md:text-xl tracking-wide">
                    <?= nl2br(htmlspecialchars(str_replace(' KOTA', " KOTA\n", pengaturan('nama_sekolah') ?? 'SMAN 10 KOTA HARAPAN BANGSA'))) ?>
                </span>
            </a>
            <div class="hidden lg:flex space-x-7 items-center text-green-800 font-semibold text-sm tracking-wide uppercase">
                <a href="index.php" class="hover:text-blue-600 transition">Beranda</a>
                <a href="katalog.php" class="hover:text-blue-600 transition">Katalog</a>
                <a href="index.php#informasi" class="hover:text-blue-600 transition">Informasi</a>
                <a href="berita.php" class="hover:text-blue-600 transition">Berita &amp; Kegiatan</a>
                <a href="#galeri-prestasi" class="hover:text-blue-600 transition">Galeri &amp; Prestasi</a>
                <a href="index.php#informasi" class="hover:text-blue-600 transition">Kontak</a>
                <button type="button" onclick="document.getElementById('inputCari').focus(); document.getElementById('katalog').scrollIntoView({behavior:'smooth'});" class="text-green-800 hover:text-blue-600 transition">
                    <i class="fa fa-search text-base"></i>
                </button>
            </div>
            <button class="lg:hidden text-green-800 text-2xl" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
                <i class="fa fa-bars"></i>
            </button>
        </div>
        <div id="mobileMenu" class="hidden lg:hidden bg-white border-t border-gray-100 px-6 py-4 space-y-3 text-green-800 font-semibold uppercase text-sm">
            <a href="index.php" class="block hover:text-blue-600">Beranda</a>
            <a href="katalog.php" class="block hover:text-blue-600">Katalog</a>
            <a href="index.php#informasi" class="block hover:text-blue-600">Informasi</a>
            <a href="berita.php" class="block hover:text-blue-600">Berita &amp; Kegiatan</a>
            <a href="#galeri-prestasi" class="block hover:text-blue-600">Galeri &amp; Prestasi</a>
            <a href="index.php#informasi" class="block hover:text-blue-600">Kontak</a>
        </div>
    </nav>

    <section class="relative h-screen flex items-center pt-20 overflow-hidden" id="hero">
        <div id="dynamic-bg" class="bg-slider" style="background-image: url('img/Gambar/Gemini.png');"></div>
            <div class="absolute inset-0 hero-overlay"></div>

        <div class="container mx-auto px-6 relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-bold leading-tight mb-4 text-white drop-shadow-md">
                Perpustakaan SMAN 10 HARAPAN BANGSA Menyediakan Akses Ribuan Buku Pelajaran Dalam Genggaman
            </h1>
            <p class="text-gray-200 mt-6 text-lg max-w-2xl mx-auto">
                <?= htmlspecialchars(pengaturan('deskripsi_beranda') ?? 'Selamat Datang di E-Perpus') ?>
            </p>
            <div class="mt-8">
                <button onclick="document.getElementById('katalog').scrollIntoView({behavior:'smooth'});" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full transition shadow-lg">
                    Jelajahi Katalog Buku
                </button>
            </div>
        </div>
    </section>

    <!-- SECTION KATALOG -->
    <section class="py-20 bg-white" id="katalog">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Koleksi Buku Terbaru</h2>
                    <p class="text-gray-500">Temukan bacaan favoritmu di sini</p>
                </div>
                <div class="relative w-full md:w-96">
                    <input type="text" id="inputCari" onkeyup="cariBuku()" placeholder="Cari judul buku atau penulis..." 
                        class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none transition shadow-sm">
                    <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Wrapper Katalog (JS Slider) -->
            <div class="relative" id="katalog-wrapper">
                <!-- Grid 5 Buku -->
                <div id="katalog-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 transition-all duration-500"></div>

                <!-- Tombol Navigasi Katalog Transparan di Tengah -->
                <div id="katalog-nav-buttons" class="absolute inset-x-0 top-1/2 -translate-y-1/2 flex justify-between px-2 md:-mx-5 pointer-events-none z-20">
                    <button onclick="prevKatalogPage()" class="glass-btn w-12 h-12 text-gray-800 rounded-full flex items-center justify-center transition pointer-events-auto hover:scale-110 shadow-md" title="Sebelumnya">
                        <i class="fa fa-chevron-left text-lg"></i>
                    </button>
                    <button onclick="nextKatalogPage()" class="glass-btn w-12 h-12 text-gray-800 rounded-full flex items-center justify-center transition pointer-events-auto hover:scale-110 shadow-md" title="Selanjutnya">
                        <i class="fa fa-chevron-right text-lg"></i>
                    </button>
                </div>
            </div>

            <div id="not-found" class="hidden text-center py-20">
                <i class="fa fa-book-open text-5xl text-gray-200 mb-4"></i>
                <p class="text-gray-500 text-lg">Buku tidak ditemukan...</p>
            </div>
        </div>
    </section>

    <!-- SECTION DUA KOLOM: PENGUMUMAN (KIRI) & BERITA (KANAN) -->
    <section class="py-20 bg-gray-50 border-t border-gray-100" id="informasi-utama">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                
                <!-- KOLOM KIRI: PENGUMUMAN (5 item per halaman + Pagination PHP) -->
                <div class="bg-white p-8 md:p-10 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-6">Pengumuman</h2>

                        <div class="space-y-8 divide-y divide-gray-100">
                            <?php if (empty($pengumuman_list)): ?>
                                <p class="text-gray-500 text-sm pt-4">Belum ada pengumuman saat ini.</p>
                            <?php else: foreach($pengumuman_list as $index => $p): ?>
                                <div class="<?php echo $index > 0 ? 'pt-8' : ''; ?>">
                                    <p class="text-xs text-amber-600 font-semibold mb-2">
                                        <i class="fa fa-calendar-days mr-1"></i><?php echo $p['tanggal']; ?>
                                    </p>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">
                                        <?php echo htmlspecialchars($p['judul']); ?>
                                    </h3>
                                    <div class="text-sm text-gray-600 leading-relaxed">
                                        <?php echo nl2br(htmlspecialchars($p['isi'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>

                    <!-- Pagination Pengumuman -->
                    <?php if ($pages_p > 1): ?>
                        <div class="flex justify-center items-center gap-2 mt-8 pt-6 border-t border-gray-100">
                            <?php for($i = 1; $i <= $pages_p; $i++): ?>
                                <a href="?page_p=<?php echo $i; ?>#informasi-utama" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition <?php echo ($page_p == $i) ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- KOLOM KANAN: BERITA (2 item per halaman + Pagination PHP) -->
                <div class="bg-white p-8 md:p-10 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-6">Berita</h2>

                        <div class="space-y-8 divide-y divide-gray-100">
                            <?php if (empty($berita_list)): ?>
                                <p class="text-gray-500 text-sm pt-4">Belum ada berita saat ini.</p>
                            <?php else: foreach($berita_list as $index => $b): ?>
                                <div class="<?php echo $index > 0 ? 'pt-8' : ''; ?> flex flex-col gap-4">
                                    
                                    <div class="w-full h-48 sm:h-56 rounded-2xl overflow-hidden shadow-sm bg-gray-100">
                                        <img src="<?php echo $b['cover']; ?>" alt="Sampul Berita" class="w-full h-full object-cover">
                                    </div>

                                    <div class="w-full">
                                        <p class="text-xs text-amber-600 font-semibold mb-2">
                                            <i class="fa fa-calendar-days mr-1"></i><?php echo $b['tanggal']; ?>
                                        </p>
                                        <h3 class="text-xl font-bold text-gray-900 leading-snug mb-2">
                                            <a href="detail_berita.php?id=<?php echo $b['id']; ?>" class="hover:text-blue-600 transition">
                                                <?php echo htmlspecialchars($b['judul']); ?>
                                            </a>
                                        </h3>
                                        
                                        <p class="text-sm text-gray-600 text-justify mb-3 leading-relaxed">
                                            <?php 
                                                $isi_berita = strip_tags($b['isi']);
                                                echo strlen($isi_berita) > 150 ? substr($isi_berita, 0, 150) . '...' : $isi_berita; 
                                            ?>
                                        </p>

                                        <a href="detail_berita.php?id=<?php echo $b['id']; ?>" class="text-blue-600 font-semibold text-xs hover:underline inline-flex items-center gap-1">
                                            Baca Selengkapnya <i class="fa fa-arrow-right text-[10px]"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>

                    <!-- Pagination Berita -->
                    <?php if ($pages_b > 1): ?>
                        <div class="flex justify-center items-center gap-2 mt-8 pt-6 border-t border-gray-100">
                            <?php for($i = 1; $i <= $pages_b; $i++): ?>
                                <a href="?page_b=<?php echo $i; ?>#informasi-utama" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition <?php echo ($page_b == $i) ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

    <!-- SECTION GALERI & PRESTASI -->
    <section class="py-20 bg-white border-t border-gray-100" id="galeri-prestasi">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Galeri &amp; Prestasi Sekolah</h2>
                    <p class="text-gray-500">Dokumentasi pencapaian dan kegiatan membanggakan</p>
                </div>
            </div>

            <!-- Wrapper Galeri (JS Slider) -->
            <div class="relative" id="galeri-wrapper">
                <!-- Grid Galeri -->
                <div id="galeri-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 transition-all duration-500"></div>

                <!-- Tombol Navigasi Galeri Transparan di Tengah -->
                <div id="galeri-nav-buttons" class="absolute inset-x-0 top-1/2 -translate-y-1/2 flex justify-between px-2 md:-mx-5 pointer-events-none z-20 hidden">
                    <button onclick="prevGaleriPage()" class="glass-btn w-12 h-12 text-gray-800 rounded-full flex items-center justify-center transition pointer-events-auto hover:scale-110 shadow-md" title="Sebelumnya">
                        <i class="fa fa-chevron-left text-lg"></i>
                    </button>
                    <button onclick="nextGaleriPage()" class="glass-btn w-12 h-12 text-gray-800 rounded-full flex items-center justify-center transition pointer-events-auto hover:scale-110 shadow-md" title="Selanjutnya">
                        <i class="fa fa-chevron-right text-lg"></i>
                    </button>
                </div>
            </div>

            <div id="galeri-not-found" class="hidden text-center py-12 bg-gray-50 rounded-3xl border border-gray-100">
                <i class="fa fa-trophy text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 text-sm">Belum ada data galeri atau prestasi yang diunggah.</p>
            </div>

        </div>
    </section>

    <!-- SECTION INFORMASI TAMBAHAN (Jam Operasional & Aturan) -->
    <section class="py-20 bg-gray-50 border-t border-gray-100" id="informasi">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Informasi Perpustakaan</h2>
                    <p class="text-gray-500 mt-2">Panduan layanan, aturan, dan jadwal operasional SMAN 10 Kota Harapan Bangsa</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Kartu 1: Jam Operasional -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                    <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa fa-clock"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Jam Operasional</h3>
                    <ul class="text-gray-600 text-sm space-y-2">
                        <li class="flex justify-between border-b border-gray-200 pb-2">
                            <span>Senin - Jumat</span> 
                            <span class="font-semibold text-gray-900">07:00 - 15:30 WIB</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Sabtu - Minggu</span> 
                            <span class="font-bold text-red-500">TUTUP</span>
                        </li>
                    </ul>
                </div>

                <!-- Kartu 2: Syarat Peminjaman -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                    <div class="w-14 h-14 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa fa-list-check"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Aturan Peminjaman</h3>
                    <ul class="text-gray-600 text-sm space-y-3">
                        <li class="flex items-start gap-3">
                            <i class="fa fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Maksimal peminjaman <strong><?= (int) pengaturan('maks_pinjam_buku', 3) ?> buku</strong> dalam satu waktu.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fa fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Durasi peminjaman maksimal <strong><?= (int) pengaturan('lama_pinjam_hari', 7) ?> hari</strong>.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fa fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                            <span>Keterlambatan pengembalian akan dikenakan sanksi.</span>
                        </li>
                    </ul>
                </div>

                <!-- Kartu 3: Bantuan & Kontak -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                    <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa fa-headset"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Pusat Bantuan</h3>
                    <p class="text-gray-600 text-sm mb-4">Mengalami kendala saat mengakses atau butuh rekomendasi buku? Hubungi pustakawan kami.</p>
                    <div class="space-y-3 mt-auto">
                        <a href="tel:<?= htmlspecialchars(pengaturan('telepon')) ?>" class="flex items-center gap-3 text-sm font-semibold text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fab fa-whatsapp text-green-500 text-lg"></i> <?= htmlspecialchars(pengaturan('telepon') ?: '-') ?>
                        </a>
                        <a href="mailto:<?= htmlspecialchars(pengaturan('email_kontak')) ?>" class="flex items-center gap-3 text-sm font-semibold text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fa fa-envelope text-red-500 text-lg"></i> <?= htmlspecialchars(pengaturan('email_kontak') ?: '-') ?>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-400 py-10 text-center">
        <p>&copy; 2026 E-Perpus SMAN 10 Harapan Bangsa. Menuju Generasi Emas Indonesia.</p>
    </footer>

    <!-- SCRIPT UNTUK KATALOG DAN GALERI PRESTASI -->
    <script>
        // ==========================================
        // 1. SCRIPT UNTUK SLIDER KATALOG BUKU
        // ==========================================
        const allBooks = <?php echo json_encode($buku_list); ?>;
        const container = document.getElementById('katalog-container');
        const notFound = document.getElementById('not-found');
        const navButtons = document.getElementById('katalog-nav-buttons');

        let currentKatalogPage = 0;
        const itemsPerPage = 5;
        let autoSlideTimer = null;
        let filteredBooksCache = allBooks;

        function renderBooks(booksToRender) {
            container.innerHTML = '';
            filteredBooksCache = booksToRender;

            if (booksToRender.length === 0) {
                notFound.classList.remove('hidden');
                container.classList.add('hidden');
                if(navButtons) navButtons.classList.add('hidden');
                return;
            }

            notFound.classList.add('hidden');
            container.classList.remove('hidden');
            
            const maxPage = Math.ceil(booksToRender.length / itemsPerPage);
            if (currentKatalogPage >= maxPage) currentKatalogPage = 0;

            if (booksToRender.length > itemsPerPage) {
                if(navButtons) navButtons.classList.remove('hidden');
            } else {
                if(navButtons) navButtons.classList.add('hidden');
            }

            const startIdx = currentKatalogPage * itemsPerPage;
            const paginatedBooks = booksToRender.slice(startIdx, startIdx + itemsPerPage);

            paginatedBooks.forEach(buku => {
                container.innerHTML += `
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all group fade-in flex flex-col justify-between border border-gray-100 relative z-10">
                        <div>
                            <div class="relative overflow-hidden">
                                <img src="${buku.cover}" class="w-full h-72 object-cover group-hover:scale-110 transition duration-500">
                            </div>
                            <div class="p-4">
                                <h6 class="font-bold text-gray-900 truncate">${buku.judul}</h6>
                                <p class="text-xs text-gray-500 mb-4">${buku.penulis}</p>
                            </div>
                        </div>
                        <div class="p-4 pt-0">
                            <a href="katalog.php" class="block text-center bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-semibold transition shadow-sm">
                                <i class="fa fa-handshake mr-1"></i> Pinjam Buku
                            </a>
                        </div>
                    </div>`;
            });
        }

        function nextKatalogPage() {
            const maxPage = Math.ceil(filteredBooksCache.length / itemsPerPage);
            if(maxPage <= 1) return;
            currentKatalogPage = (currentKatalogPage + 1) % maxPage;
            renderBooks(filteredBooksCache);
        }

        function prevKatalogPage() {
            const maxPage = Math.ceil(filteredBooksCache.length / itemsPerPage);
            if(maxPage <= 1) return;
            currentKatalogPage = (currentKatalogPage - 1 + maxPage) % maxPage;
            renderBooks(filteredBooksCache);
        }

        function cariBuku() {
            const keyword = document.getElementById('inputCari').value.toLowerCase();
            currentKatalogPage = 0;
            const filteredBooks = allBooks.filter(buku => {
                return buku.judul.toLowerCase().includes(keyword) || buku.penulis.toLowerCase().includes(keyword);
            });
            renderBooks(filteredBooks);
        }

        function startAutoSlide() {
            autoSlideTimer = setInterval(() => {
                const maxPage = Math.ceil(filteredBooksCache.length / itemsPerPage);
                if (maxPage > 1) nextKatalogPage();
            }, 4000);
        }

        function stopAutoSlide() {
            clearInterval(autoSlideTimer);
        }

        const katalogWrapper = document.getElementById('katalog-wrapper');
        katalogWrapper.addEventListener('mouseenter', stopAutoSlide);
        katalogWrapper.addEventListener('mouseleave', startAutoSlide);

        // ==========================================
        // 2. SCRIPT UNTUK SLIDER GALERI PRESTASI
        // ==========================================
        const allGaleri = <?php echo json_encode($galeri_list_all); ?>;
        const galeriContainer = document.getElementById('galeri-container');
        const galeriNotFound = document.getElementById('galeri-not-found');
        const galeriNavButtons = document.getElementById('galeri-nav-buttons');

        let currentGaleriPage = 0;
        const itemsPerGaleriPage = 3;
        let autoSlideGaleriTimer = null;

        function renderGaleri() {
            galeriContainer.innerHTML = '';
            
            if (allGaleri.length === 0) {
                galeriNotFound.classList.remove('hidden');
                galeriContainer.classList.add('hidden');
                if(galeriNavButtons) galeriNavButtons.classList.add('hidden');
                return;
            }

            galeriNotFound.classList.add('hidden');
            galeriContainer.classList.remove('hidden');

            const maxPage = Math.ceil(allGaleri.length / itemsPerGaleriPage);
            if (currentGaleriPage >= maxPage) currentGaleriPage = 0;

            if (allGaleri.length > itemsPerGaleriPage) {
                if(galeriNavButtons) galeriNavButtons.classList.remove('hidden');
            } else {
                if(galeriNavButtons) galeriNavButtons.classList.add('hidden');
            }

            const startIdx = currentGaleriPage * itemsPerGaleriPage;
            const paginatedGaleri = allGaleri.slice(startIdx, startIdx + itemsPerGaleriPage);

            paginatedGaleri.forEach(g => {
                let deskripsiSingkat = g.deskripsi.length > 120 ? g.deskripsi.substring(0, 120) + '...' : g.deskripsi;
                galeriContainer.innerHTML += `
                    <div class="bg-gray-50 rounded-3xl overflow-hidden shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-xl transition-all duration-300 group fade-in relative z-10">
                        <div>
                            <div class="relative h-60 overflow-hidden bg-gray-100">
                                <img src="${g.cover}" alt="Foto Prestasi" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                <span class="absolute top-4 left-4 bg-blue-600 text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full shadow-sm">
                                    ${g.kategori}
                                </span>
                            </div>
                            <div class="p-6">
                                <p class="text-xs text-amber-600 font-semibold mb-1">
                                    <i class="fa fa-calendar-days mr-1"></i>${g.tanggal}
                                </p>
                                <p class="text-sm font-bold text-gray-900 mb-2">
                                    Oleh: ${g.nama_peraih}
                                </p>
                                <h3 class="text-lg font-bold text-gray-900 mb-3 leading-snug">
                                    ${g.judul}
                                </h3>
                                <p class="text-sm text-gray-600 text-justify leading-relaxed">
                                    ${deskripsiSingkat}
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        function nextGaleriPage() {
            const maxPage = Math.ceil(allGaleri.length / itemsPerGaleriPage);
            if(maxPage <= 1) return;
            currentGaleriPage = (currentGaleriPage + 1) % maxPage;
            renderGaleri();
        }

        function prevGaleriPage() {
            const maxPage = Math.ceil(allGaleri.length / itemsPerGaleriPage);
            if(maxPage <= 1) return;
            currentGaleriPage = (currentGaleriPage - 1 + maxPage) % maxPage;
            renderGaleri();
        }

        function startAutoSlideGaleri() {
            autoSlideGaleriTimer = setInterval(() => {
                const maxPage = Math.ceil(allGaleri.length / itemsPerGaleriPage);
                if (maxPage > 1) nextGaleriPage();
            }, 5000); // 5 detik untuk galeri
        }

        function stopAutoSlideGaleri() {
            clearInterval(autoSlideGaleriTimer);
        }

        const galeriWrapper = document.getElementById('galeri-wrapper');
        galeriWrapper.addEventListener('mouseenter', stopAutoSlideGaleri);
        galeriWrapper.addEventListener('mouseleave', startAutoSlideGaleri);


        // ==========================================
        // 3. JALANKAN SAAT HALAMAN DIMUAT
        // ==========================================
        renderBooks(allBooks);
        startAutoSlide();

        renderGaleri();
        startAutoSlideGaleri();
    </script>
</body>
</html>