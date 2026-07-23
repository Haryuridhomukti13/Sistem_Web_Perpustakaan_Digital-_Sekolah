<?php 
include "koneksi.php"; 
session_start();

// --- 1. LOGIKA PROSES DAFTAR ---
if (isset($_POST['btn_daftar'])) {
    $nama        = mysqli_real_escape_string($conn, $_POST['username']); 
    $no_anggota  = mysqli_real_escape_string($conn, $_POST['no_anggota']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $password    = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role        = 'anggota'; 
    $status      = 'aktif';   

    $cek = mysqli_query($conn, "SELECT * FROM pengguna WHERE no_anggota = '$no_anggota' OR email = '$email'");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Nomor Anggota atau Email sudah terdaftar!'); window.location='index.php';</script>";
    } else {
        $query_daftar = "INSERT INTO pengguna (nama, no_anggota, email, password, role, status) 
                         VALUES ('$nama', '$no_anggota', '$email', '$password', '$role', '$status')";
        
        if (mysqli_query($conn, $query_daftar)) {
            echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Gagal Mendaftar: " . mysqli_error($conn) . "');</script>";
        }
    }
}

// --- 2. LOGIKA AMBIL DATA BUKU ---
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
    </style>
</head>
<body class="bg-gray-50 text-gray-900 overflow-x-hidden">

    <nav class="fixed w-full z-50 bg-white shadow-sm border-b border-gray-100">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <a href="#" class="flex items-center gap-3">
                <img src="img/logo/logo.png" alt="Logo SMAN 10" class="h-14 w-14 object-contain">
                <span class="text-green-800 font-bold leading-tight text-lg md:text-xl tracking-wide">
                    SMAN 10 KOTA<br>HARAPAN BANGSA
                </span>
            </a>
            <div class="hidden lg:flex space-x-7 items-center text-green-800 font-semibold text-sm tracking-wide uppercase">
                <a href="index.php" class="hover:text-blue-600 transition">Beranda</a>
                <a href="index.php#katalog" class="hover:text-blue-600 transition">Katalog</a>
                <a href="index.php#informasi" class="hover:text-blue-600 transition">Informasi</a>
                <a href="berita.php" class="hover:text-blue-600 transition">Berita &amp; Kegiatan</a>
                <a href="index.php#galeri-prestasi" class="hover:text-blue-600 transition">Galeri &amp; Prestasi</a>
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
            <a href="index.php#katalog" class="block hover:text-blue-600">Katalog</a>
            <a href="index.php#informasi" class="block hover:text-blue-600">Informasi</a>
            <a href="berita.php" class="block hover:text-blue-600">Berita &amp; Kegiatan</a>
            <a href="index.php#galeri-prestasi" class="block hover:text-blue-600">Galeri &amp; Prestasi</a>
            <a href="index.php#informasi" class="block hover:text-blue-600">Kontak</a>
        </div>
    </nav>

    <section class="relative h-screen flex items-center pt-20 overflow-hidden" id="login">
        <div id="dynamic-bg" class="bg-slider" style="background-image: url('img/Gambar/Gemini.png');"></div>
            <div class="absolute inset-0 hero-overlay"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-wrap items-center">
                <div class="w-full lg:w-1/2 text-white mb-12 lg:mb-0">
                    <h1 class="text-3xl md:text-5xl font-bold leading-tight mb-4 drop-shadow-md">
                        Perpustakaan SMAN 10 HARAPAN BANGSA Menyediakan Akses Ribuan Buku Pelajaran Dalam Genggaman
                    </h1>
                    <p class="text-gray-200 mt-6 text-lg max-w-xl">
                        Temukan berbagai koleksi buku terbaik untuk menunjang pembelajaran dan wawasanmu di perpustakaan digital kami.
                    </p>
                </div>

                <div class="w-full lg:w-5/12 ml-auto">
                    <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-10 text-gray-800">
                        <div id="form-login-pengguna" class="fade-in">
                            <h3 class="text-2xl font-bold mb-6">Login Anggota</h3>
                            <form action="proses_login.php" method="POST" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Username / Nama</label>
                                    <input type="text" name="username" placeholder="Masukkan nama" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none transition" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Password</label>
                                    <div class="relative">
                                        <input type="password" id="loginPass" name="password" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none transition" required>
                                        <button type="button" onclick="togglePassword('loginPass', 'eyeLogin')" class="absolute right-4 inset-y-0 text-gray-400">
                                            <i id="eyeLogin" class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition shadow-lg">Masuk Sekarang</button>
                            </form>
                            <p class="mt-6 text-center text-sm text-gray-500">Belum punya akun? <a href="javascript:void(0)" onclick="tampilkanDaftar()" class="text-blue-600 font-bold hover:underline">Daftar Baru</a></p>
                        </div>

                        <div id="form-daftar-pengguna" class="hidden fade-in">
                            <h3 class="text-2xl font-bold mb-6 text-green-700">Daftar Pengguna</h3>
                            <form action="" method="POST" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Nama Lengkap</label>
                                    <input type="text" name="username" placeholder="Sesuai KTP" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-500 outline-none transition" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">NIK / Kartu Pelajar</label>
                                    <input type="text" name="no_anggota" placeholder="Contoh: 3216xxxxxxxx" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-500 outline-none transition" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Email</label>
                                    <input type="email" name="email" placeholder="contoh@email.com" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-500 outline-none transition" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Password</label>
                                    <div class="relative">
                                        <input type="password" id="regPass" name="password" placeholder="Min. 8 karakter" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-500 outline-none transition" required>
                                        <button type="button" onclick="togglePassword('regPass', 'eyeReg')" class="absolute right-4 inset-y-0 text-gray-400">
                                            <i id="eyeReg" class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" name="btn_daftar" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition shadow-lg mt-2">Simpan Pendaftaran</button>
                            </form>
                            <p class="mt-6 text-center text-sm text-gray-500">Sudah punya akun? <a href="javascript:void(0)" onclick="tampilkanLogin()" class="text-green-600 font-bold hover:underline">Kembali Login</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION KATALOG -->
    <section class="py-20 bg-white" id="katalog">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Katalog Buku Terbaru</h2>
                    <p class="text-gray-500">Temukan bacaan favoritmu di sini</p>
                </div>
                <div class="relative w-full md:w-96">
                    <input type="text" id="inputCari" onkeyup="cariBuku()" placeholder="Cari judul buku..." 
                        class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none transition shadow-sm">
                    <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Wrapper Katalog dengan Posisi Relative untuk Tombol Melayang Transparan di Tengah -->
            <div class="relative" id="katalog-wrapper">
                <!-- Grid 10 Buku -->
                <div id="katalog-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 transition-all duration-500"></div>

                <!-- Tombol Navigasi Benar-Benar Transparan di Tengah-Tengah Buku -->
                <div id="katalog-nav-buttons" class="absolute inset-x-0 top-1/2 -translate-y-1/2 flex justify-between px-2 md:-mx-6 pointer-events-none z-20">
                    <button onclick="prevKatalogPage()" class="w-12 h-12 bg-transparent hover:bg-black/10 text-gray-800 dark:text-white rounded-full flex items-center justify-center transition pointer-events-auto hover:scale-110" title="Sebelumnya">
                        <i class="fa fa-chevron-left text-lg drop-shadow-md"></i>
                    </button>
                    <button onclick="nextKatalogPage()" class="w-12 h-12 bg-transparent hover:bg-black/10 text-gray-800 dark:text-white rounded-full flex items-center justify-center transition pointer-events-auto hover:scale-110" title="Selanjutnya">
                        <i class="fa fa-chevron-right text-lg drop-shadow-md"></i>
                    </button>
                </div>
            </div>

            <div id="not-found" class="hidden text-center py-20">
                <i class="fa fa-book-open text-5xl text-gray-200 mb-4"></i>
                <p class="text-gray-500 text-lg">Buku tidak ditemukan...</p>
            </div>
        </div>
    </section>

    <!-- SECTION INFORMASI -->
    <section class="py-20 bg-white" id="informasi">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Informasi Perpustakaan</h2>
                    <p class="text-gray-500 mt-2">Panduan layanan, aturan, dan jadwal operasional SMAN 10 Kota Harapan Bangsa</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-100 rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                    <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa fa-clock"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Jam Operasional</h3>
                    <ul class="text-gray-600 text-sm space-y-2">
                        <li class="flex justify-between border-b border-gray-50 pb-2">
                            <span>Senin - Jumat</span> 
                            <span class="font-semibold text-gray-900">07:00 - 15:30 WIB</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Sabtu - Minggu</span> 
                            <span class="font-bold text-red-500">TUTUP</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-gray-100 rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                    <div class="w-14 h-14 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa fa-list-check"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Aturan Peminjaman</h3>
                    <ul class="text-gray-600 text-sm space-y-3">
                        <li class="flex items-start gap-3">
                            <i class="fa fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Maksimal peminjaman <strong>3 buku</strong> dalam satu waktu.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fa fa-check-circle text-green-500 mt-0.5"></i>
                            <span>Durasi peminjaman maksimal <strong>1 Bulan</strong>.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fa fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                            <span>Keterlambatan pengembalian akan dikenakan sanksi.</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-gray-100 rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                    <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa fa-headset"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Pusat Bantuan</h3>
                    <p class="text-gray-600 text-sm mb-4">Mengalami kendala saat login atau butuh rekomendasi buku? Hubungi pustakawan kami.</p>
                    <div class="space-y-3 mt-auto">
                        <a href="#" class="flex items-center gap-3 text-sm font-semibold text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fab fa-whatsapp text-green-500 text-lg"></i> +62 812-3456-7890
                        </a>
                        <a href="#" class="flex items-center gap-3 text-sm font-semibold text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fa fa-envelope text-red-500 text-lg"></i> perpus@sman10khb.sch.id
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-400 py-10 text-center">
        <p>&copy; 2026 E-Perpus SMAN 10 Harapan Bangsa. Menuju Generasi Emas Indonesia.</p>
    </footer>

    <script>
        const allBooks = <?php echo json_encode($buku_list); ?>;
        const container = document.getElementById('katalog-container');
        const notFound = document.getElementById('not-found');
        const navButtons = document.getElementById('katalog-nav-buttons');

        let currentKatalogPage = 0;
        const itemsPerPage = <?php echo (int) pengaturan('jumlah_buku_per_halaman', 10); ?>; // diambil dari Pengaturan Sistem
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
            if (currentKatalogPage >= maxPage) {
                currentKatalogPage = 0;
            }

            if (booksToRender.length > itemsPerPage) {
                if(navButtons) navButtons.classList.remove('hidden');
            } else {
                if(navButtons) navButtons.classList.add('hidden');
            }

            const startIdx = currentKatalogPage * itemsPerPage;
            const paginatedBooks = booksToRender.slice(startIdx, startIdx + itemsPerPage);

            paginatedBooks.forEach(buku => {
                container.innerHTML += `
                    <div class="bg-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all group fade-in flex flex-col justify-between">
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
                            <a href="detail_buku.php?id=${buku.id}" class="block text-center border border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white py-2 rounded-lg text-sm font-semibold transition">Detail Buku</a>
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
            currentKatalogPage = 0; // Reset ke halaman pertama saat mencari
            const filteredBooks = allBooks.filter(buku => {
                return buku.judul.toLowerCase().includes(keyword) || buku.penulis.toLowerCase().includes(keyword);
            });
            renderBooks(filteredBooks);
        }

        // Auto-Slide setiap 4 detik
        function startAutoSlide() {
            autoSlideTimer = setInterval(() => {
                const maxPage = Math.ceil(filteredBooksCache.length / itemsPerPage);
                if (maxPage > 1) {
                    nextKatalogPage();
                }
            }, 4000);
        }

        function stopAutoSlide() {
            clearInterval(autoSlideTimer);
        }

        // Berhenti otomatis saat mouse berada di area katalog, jalan lagi saat keluar
        const katalogWrapper = document.getElementById('katalog-wrapper');
        katalogWrapper.addEventListener('mouseenter', stopAutoSlide);
        katalogWrapper.addEventListener('mouseleave', startAutoSlide);

        // Render awal katalog buku & aktifkan auto-slide
        renderBooks(allBooks);
        startAutoSlide();

        function tampilkanDaftar() {
            document.getElementById('form-login-pengguna').classList.add('hidden');
            document.getElementById('form-daftar-pengguna').classList.remove('hidden');
        }
        function tampilkanLogin() {
            document.getElementById('form-daftar-pengguna').classList.add('hidden');
            document.getElementById('form-login-pengguna').classList.remove('hidden');
        }
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>