<?php 
include "koneksi.php"; 
session_start();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// --- AMBIL DATA BERITA UTAMA ---
$berita = null;
$res = mysqli_query($conn, "SELECT * FROM pengumuman_dan_berita WHERE id_pengumuman = $id LIMIT 1");
if ($res && mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    $path = 'uploads/sampul_berita/' . $row['sampul_berita'];
    
    $cover = ($row['sampul_berita'] != 'default_berita.jpg' && !empty($row['sampul_berita']) && file_exists(__DIR__ . '/' . $path))
        ? $path
        : "https://picsum.photos/seed/berita" . $row['id_pengumuman'] . "/900/500";

    $berita = [
        'id'        => $row['id_pengumuman'],
        'judul'     => $row['judul'],
        'isi'       => $row['isi'] ?? '',
        'tanggal'   => isset($row['tanggal_dibuat']) ? date('d M Y', strtotime($row['tanggal_dibuat'])) : '',
        'kategori'  => $row['kategori'] ?? 'Informasi',
        'penulis'   => 'Admin Sekolah',
        'cover'     => $cover,
        'berita'    => $row['berita'] ?? null
    ];
}

// Jika berita tidak ditemukan, redirect kembali ke berita.php
if (!$berita) {
    echo "<script>alert('Berita tidak ditemukan.'); window.location='berita.php';</script>";
    exit;
}

// --- AMBIL PRESTASI SISWA (KOLOM KIRI) ---
$prestasi_list = [];
$res_prestasi = mysqli_query($conn, "SELECT * FROM galeri_prestasi ORDER BY tanggal DESC LIMIT 3");
if ($res_prestasi && mysqli_num_rows($res_prestasi) > 0) {
    while ($gp = mysqli_fetch_assoc($res_prestasi)) {
        $p_path = 'uploads/foto_prestasi/' . $gp['gambar'];
        $p_cover = (!empty($gp['gambar']) && $gp['gambar'] != 'default_prestasi.jpg' && file_exists(__DIR__ . '/' . $p_path))
            ? $p_path
            : "https://picsum.photos/seed/prestasi" . $gp['id_prestasi'] . "/400/300";
        
        $prestasi_list[] = [
            'judul' => $gp['judul'],
            'nama'  => $gp['nama_peraih'],
            'cover' => $p_cover,
            'tanggal' => date('d M Y', strtotime($gp['tanggal']))
        ];
    }
}

// --- AMBIL BUKU POPULER (KOLOM KANAN) ---
$buku_populer = [];
$res_buku = mysqli_query($conn, "SELECT * FROM buku ORDER BY id_buku DESC LIMIT 3");
if ($res_buku && mysqli_num_rows($res_buku) > 0) {
    while ($bk = mysqli_fetch_assoc($res_buku)) {
        $b_path = 'uploads/sampul/' . $bk['sampul'];
        $b_cover = (!empty($bk['sampul']) && file_exists(__DIR__ . '/' . $b_path))
            ? $b_path
            : "https://picsum.photos/seed/buku" . $bk['id_buku'] . "/400/500";
        
        $buku_populer[] = [
            'judul'     => $bk['judul'],
            'pengarang' => $bk['pengarang'],
            'cover'     => $b_cover,
            'kategori'  => $bk['kategori']
        ];
    }
}

// --- AMBIL BERITA TERKAIT (BAWAH) ---
$terkait = [];
$res_terkait = mysqli_query($conn, "SELECT * FROM pengumuman_dan_berita WHERE id_pengumuman != $id ORDER BY tanggal_dibuat DESC LIMIT 3");
if ($res_terkait && mysqli_num_rows($res_terkait) > 0) {
    while ($r = mysqli_fetch_assoc($res_terkait)) {
        $p = 'uploads/sampul_berita/' . $r['sampul_berita'];
        $terkait[] = [
            'id'      => $r['id_pengumuman'],
            'judul'   => $r['judul'],
            'tanggal' => isset($r['tanggal_dibuat']) ? date('d M Y', strtotime($r['tanggal_dibuat'])) : '',
            'cover'   => ($r['sampul_berita'] != 'default_berita.jpg' && !empty($r['sampul_berita']) && file_exists(__DIR__ . '/' . $p))
                ? $p
                : "https://picsum.photos/seed/berita" . $r['id_pengumuman'] . "/400/300"
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($berita['judul']); ?> | SMAN 10 Kota Harapan Bangsa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Dibuat text-align: justify agar teks berita rata kiri-kanan */
        .prose-berita p { margin-bottom: 1.25rem; line-height: 1.8; color: #374151; text-align: justify; }
        .bg-slider {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1; background-size: cover; background-position: center;
        }
        .hero-overlay { background: linear-gradient(to bottom, rgba(0,0,0,0.5), rgba(0,0,0,0.8)); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn .5s ease both; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 overflow-x-hidden">

    <!-- NAVBAR DISAMAKAN PERSIS DENGAN BERITA.PHP -->
    <nav class="fixed w-full z-50 bg-white shadow-sm border-b border-gray-100">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3">
                <img src="img/logo/logo.png" alt="Logo SMAN 10" class="h-14 w-14 object-contain">
                <span class="text-green-800 font-bold leading-tight text-lg md:text-xl tracking-wide">
                    SMAN 10 KOTA<br>HARAPAN BANGSA
                </span>
            </a>
            <div class="hidden lg:flex space-x-7 items-center text-green-800 font-semibold text-sm tracking-wide uppercase">
                <a href="index.php" class="hover:text-blue-600 transition">Beranda</a>
                <a href="katalog.php" class="hover:text-blue-600 transition">Katalog</a>
                <a href="index.php#informasi" class="hover:text-blue-600 transition">Informasi</a>
                <a href="berita.php" class="text-blue-600 transition">Berita &amp; Kegiatan</a>
                <a href="index.php#galeri-prestasi" class="hover:text-blue-600 transition">Galeri &amp; Prestasi</a>
                <a href="index.php#informasi" class="hover:text-blue-600 transition">Kontak</a>
                <a href="berita.php" class="text-green-800 hover:text-blue-600 transition">
                    <i class="fa fa-search text-base"></i>
                </a>
            </div>
            <button class="lg:hidden text-green-800 text-2xl" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
                <i class="fa fa-bars"></i>
            </button>
        </div>
        <div id="mobileMenu" class="hidden lg:hidden bg-white border-t border-gray-100 px-6 py-4 space-y-3 text-green-800 font-semibold uppercase text-sm">
            <a href="index.php" class="block hover:text-blue-600">Beranda</a>
            <a href="katalog.php" class="block hover:text-blue-600">Katalog</a>
            <a href="index.php#informasi" class="block hover:text-blue-600">Informasi</a>
            <a href="berita.php" class="block text-blue-600">Berita &amp; Kegiatan</a>
            <a href="index.php#galeri-prestasi" class="block hover:text-blue-600">Galeri &amp; Prestasi</a>
            <a href="index.php#informasi" class="block hover:text-blue-600">Kontak</a>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="relative h-screen flex items-center pt-20 overflow-hidden" id="hero">
        <div id="dynamic-bg" class="bg-slider" style="background-image: url('img/Gambar/Gemini.png');"></div>
        <div class="absolute inset-0 hero-overlay"></div>

        <div class="container mx-auto px-6 relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-bold leading-tight mb-4 text-white drop-shadow-md">
                Berita &amp; Kegiatan Terbaru SMAN 10 KOTA HARAPAN BANGSA
            </h1>
            <p class="text-gray-200 mt-6 text-lg max-w-2xl mx-auto">
                Ikuti perkembangan, prestasi, dan informasi terkini dari seluruh kegiatan sekolah kami dalam genggaman.
            </p>
            <div class="mt-8">
                <button onclick="document.getElementById('detail-section').scrollIntoView({behavior:'smooth'});" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full transition shadow-lg">
                    Jelajahi Informasi
                </button>
            </div>
        </div>
    </section>

    <!-- BREADCRUMB SECTION -->
    <div class="py-4 bg-white border-b border-gray-100" id="detail-section">
        <div class="container mx-auto px-6 text-sm text-gray-500">
            <a href="index.php" class="hover:text-blue-600">Beranda</a>
            <span class="mx-2">/</span>
            <a href="berita.php" class="hover:text-blue-600">Berita &amp; Kegiatan</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($berita['judul']); ?></span>
        </div>
    </div>

    <!-- MAIN CONTAINER DENGAN 3 KOLOM -->
    <div class="container mx-auto px-6 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- SIDEBAR KIRI: PRESTASI SISWA -->
            <aside class="lg:col-span-1 space-y-6">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                        <i class="fa fa-trophy text-amber-500"></i> Prestasi Siswa
                    </h3>
                    <?php if (empty($prestasi_list)): ?>
                        <p class="text-xs text-gray-400">Belum ada prestasi ditampilkan.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($prestasi_list as $prestasi): ?>
                                <div class="flex gap-3 items-center group">
                                    <img src="<?php echo htmlspecialchars($prestasi['cover']); ?>" class="w-16 h-16 rounded-xl object-cover shrink-0 group-hover:scale-105 transition">
                                    <div>
                                        <p class="text-[10px] text-gray-400"><i class="fa fa-calendar-days mr-1"></i><?php echo $prestasi['tanggal']; ?></p>
                                        <h4 class="text-xs font-bold text-gray-900 line-clamp-2 mt-0.5"><?php echo htmlspecialchars($prestasi['judul']); ?></h4>
                                        <p class="text-[11px] text-blue-600 font-medium mt-0.5">Oleh: <?php echo htmlspecialchars($prestasi['nama']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>

            <!-- KONTEN UTAMA ARTIKEL DI TENGAH -->
            <main class="lg:col-span-2 bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-gray-100">
                <article>
                    <span class="inline-block bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-full mb-4">
                        <?php echo htmlspecialchars($berita['kategori']); ?>
                    </span>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 leading-tight mb-4">
                        <?php echo htmlspecialchars($berita['judul']); ?>
                    </h1>
                    <div class="flex items-center gap-4 text-xs text-gray-500 mb-6">
                        <span><i class="fa fa-calendar-days mr-1"></i><?php echo htmlspecialchars($berita['tanggal']); ?></span>
                        <span><i class="fa fa-user mr-1"></i><?php echo htmlspecialchars($berita['penulis']); ?></span>
                    </div>

                    <img src="<?php echo htmlspecialchars($berita['cover']); ?>" alt="<?php echo htmlspecialchars($berita['judul']); ?>"
                        class="w-full h-64 md:h-80 object-cover rounded-2xl shadow-md mb-8">

                    <!-- Diisi dengan kelas text-justify untuk rata kiri-kanan -->
                    <div class="prose-berita text-sm md:text-base text-justify">
                        <?php echo nl2br(htmlspecialchars($berita['isi'])); ?>
                    </div>

                    <!-- Tombol Unduh Lampiran Berita Jika Ada -->
                    <?php if (!empty($berita['berita'])): ?>
                        <div class="mt-8 p-4 bg-blue-50 border border-blue-100 rounded-2xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center">
                                    <i class="fa fa-file-pdf"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Dokumen Lampiran Berita</p>
                                    <p class="text-xs text-gray-500">Unduh file pendukung untuk informasi ini</p>
                                </div>
                            </div>
                            <a href="uploads/berita/<?php echo $berita['berita']; ?>" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow">
                                <i class="fa fa-download mr-1"></i> Unduh File
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-between flex-wrap gap-4">
                        <a href="berita.php" class="inline-flex items-center text-blue-600 font-semibold text-xs hover:underline">
                            <i class="fa fa-arrow-left mr-2"></i> Kembali ke Berita
                        </a>
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <span>Bagikan:</span>
                            <a href="https://wa.me/?text=<?php echo urlencode($berita['judul']); ?>" target="_blank" class="hover:text-green-600"><i class="fa-brands fa-whatsapp text-base"></i></a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=" target="_blank" class="hover:text-blue-600"><i class="fa-brands fa-facebook text-base"></i></a>
                        </div>
                    </div>
                </article>
            </main>

            <!-- SIDEBAR KANAN: BUKU POPULER -->
            <aside class="lg:col-span-1 space-y-6">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                        <i class="fa fa-book text-blue-600"></i> Buku Populer
                    </h3>
                    <?php if (empty($buku_populer)): ?>
                        <p class="text-xs text-gray-400">Belum ada buku populer.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($buku_populer as $buku): ?>
                                <div class="flex gap-3 items-center group">
                                    <img src="<?php echo htmlspecialchars($buku['cover']); ?>" class="w-14 h-20 rounded-lg object-cover shrink-0 group-hover:scale-105 transition shadow-sm">
                                    <div>
                                        <span class="inline-block bg-blue-50 text-blue-600 text-[10px] font-semibold px-2 py-0.5 rounded mb-1"><?php echo htmlspecialchars($buku['kategori']); ?></span>
                                        <h4 class="text-xs font-bold text-gray-900 line-clamp-2"><?php echo htmlspecialchars($buku['judul']); ?></h4>
                                        <p class="text-[11px] text-gray-500 mt-0.5"><?php echo htmlspecialchars($buku['pengarang']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>

        </div>
    </div>

    <?php if (count($terkait) > 0): ?>
    <!-- BERITA TERKAIT -->
    <section class="py-14 bg-gray-50 border-t border-gray-100">
        <div class="container mx-auto px-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Berita Lainnya</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($terkait as $t): ?>
                <a href="detail_berita.php?id=<?php echo $t['id']; ?>" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all group border border-gray-100 flex flex-col justify-between">
                    <div>
                        <div class="overflow-hidden h-44">
                            <img src="<?php echo htmlspecialchars($t['cover']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        </div>
                        <div class="p-5">
                            <p class="text-xs text-gray-400 mb-2"><i class="fa fa-calendar-days mr-1"></i><?php echo htmlspecialchars($t['tanggal']); ?></p>
                            <h6 class="font-bold text-gray-900 line-clamp-2 group-hover:text-blue-600 transition"><?php echo htmlspecialchars($t['judul']); ?></h6>
                        </div>
                    </div>
                    <div class="px-5 pb-5 pt-0">
                        <span class="inline-flex items-center text-blue-600 font-semibold text-xs hover:underline">
                            Baca Selengkapnya <i class="fa fa-arrow-right ml-1"></i>
                        </span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <footer class="bg-gray-900 text-gray-400 py-10 text-center">
        <p>&copy; 2026 E-Perpus SMAN 10 Harapan Bangsa. Generasi Emas Indonesia.</p>
    </footer>

</body>
</html>