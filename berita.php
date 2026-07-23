<?php 
include "koneksi.php"; 
session_start();

// --- AMBIL DATA BERITA & PENGUMUMAN ---
$berita_list = [];
$res_berita = mysqli_query($conn, "SELECT * FROM pengumuman_dan_berita ORDER BY tanggal_dibuat DESC");

if ($res_berita && mysqli_num_rows($res_berita) > 0) {
    while ($row = mysqli_fetch_assoc($res_berita)) {
        // Mengarahkan path ke folder sampul_berita
        $path = 'uploads/sampul_berita/' . $row['sampul_berita'];
        
        // Mengecek ketersediaan gambar (mengabaikan default_berita.jpg jika file tidak ada secara fisik)
        $row['cover'] = ($row['sampul_berita'] != 'default_berita.jpg' && !empty($row['sampul_berita']) && file_exists(__DIR__ . '/' . $path))
            ? $path
            : "https://picsum.photos/seed/berita" . $row['id_pengumuman'] . "/500/350";

        $berita_list[] = [
            'id'        => $row['id_pengumuman'],
            'judul'     => $row['judul'],
            'ringkasan' => mb_substr(strip_tags($row['isi']), 0, 120) . '...',
            'tanggal'   => date('d M Y', strtotime($row['tanggal_dibuat'])),
            'kategori'  => 'Informasi',
            'cover'     => $row['cover']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita & Kegiatan | SMAN 10 Kota Harapan Bangsa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
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
                <button type="button" onclick="document.getElementById('inputCariBerita').focus(); window.scrollTo({top: 500, behavior: 'smooth'});" class="text-green-800 hover:text-blue-600 transition">
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
            <a href="berita.php" class="block text-blue-600">Berita &amp; Kegiatan</a>
            <a href="index.php#galeri-prestasi" class="block hover:text-blue-600">Galeri &amp; Prestasi</a>
            <a href="index.php#informasi" class="block hover:text-blue-600">Kontak</a>
        </div>
    </nav>
    
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
                <button onclick="document.getElementById('berita-section').scrollIntoView({behavior:'smooth'});" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full transition shadow-lg">
                    Jelajahi Informasi
                </button>
            </div>
        </div>
    </section>

    <section class="py-20 bg-white" id="berita-section">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Semua Informasi</h2>
                    <p class="text-gray-500">Total <span class="font-semibold text-gray-900"><?php echo count($berita_list); ?></span> informasi ditemukan</p>
                </div>
                <div class="relative w-full md:w-96">
                    <input type="text" id="inputCariBerita" onkeyup="cariBerita()" placeholder="Cari informasi atau kegiatan..."
                        class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none transition shadow-sm">
                    <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <div id="berita-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"></div>
            
            <div id="not-found" class="hidden text-center py-20">
                <i class="fa fa-newspaper text-5xl text-gray-200 mb-4"></i>
                <p class="text-gray-500 text-lg">Informasi tidak ditemukan...</p>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-400 py-10 text-center">
        <p>&copy; 2026 E-Perpus SMAN 10 Harapan Bangsa. Generasi Emas Indonesia.</p>
    </footer>

    <script>
        const allBerita = <?php echo json_encode($berita_list); ?>;
        const container = document.getElementById('berita-container');
        const notFound = document.getElementById('not-found');

        function renderBerita(list) {
            container.innerHTML = '';
            if (list.length === 0) {
                notFound.classList.remove('hidden');
                container.classList.add('hidden');
                return;
            }
            notFound.classList.add('hidden');
            container.classList.remove('hidden');
            list.forEach(b => {
                container.innerHTML += `
                    <div class="bg-gray-50 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all group fade-in flex flex-col justify-between border border-gray-100">
                        <div>
                            <a href="detail_berita.php?id=${b.id}" class="relative overflow-hidden h-52 block">
                                <img src="${b.cover}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                <span class="absolute top-3 left-3 bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-full">${b.kategori}</span>
                            </a>
                            <div class="p-5">
                                <p class="text-xs text-gray-400 mb-2"><i class="fa fa-calendar-days mr-1"></i>${b.tanggal}</p>
                                <h6 class="font-bold text-gray-900 mb-2 line-clamp-2">
                                    <a href="detail_berita.php?id=${b.id}" class="hover:text-blue-600 transition">${b.judul}</a>
                                </h6>
                                <p class="text-sm text-gray-500 mb-4 line-clamp-3">${b.ringkasan}</p>
                            </div>
                        </div>
                        <div class="px-5 pb-5 pt-0">
                            <a href="detail_berita.php?id=${b.id}" class="inline-flex items-center text-blue-600 font-semibold text-sm hover:underline">
                                Baca Selengkapnya <i class="fa fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>`;
            });
        }

        function cariBerita() {
            const keyword = document.getElementById('inputCariBerita').value.toLowerCase();
            const filtered = allBerita.filter(b =>
                b.judul.toLowerCase().includes(keyword) ||
                b.ringkasan.toLowerCase().includes(keyword) ||
                b.kategori.toLowerCase().includes(keyword)
            );
            renderBerita(filtered);
        }

        renderBerita(allBerita);
    </script>
</body>
</html>