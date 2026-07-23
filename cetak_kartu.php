<?php
session_start();
include 'koneksi.php';

// Proteksi Halaman
if (!isset($_SESSION['user'])) { 
    header("Location: index.php"); 
    exit; 
}

// Cek parameter ID
if (!isset($_GET['id'])) { 
    die("ID Anggota tidak ditemukan."); 
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_pengguna = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data anggota tidak ditemukan di database.");
}

// Pengaturan Foto
$path_foto = 'img/foto/' . $data['foto'];
$gambar = (!empty($data['foto']) && file_exists($path_foto)) 
          ? $path_foto 
          : "https://ui-avatars.com/api/?name=" . urlencode($data['nama']) . "&background=4F46E5&color=fff";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu - <?php echo htmlspecialchars($data['nama']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            body { background: none !important; }
            /* Menghilangkan header/footer bawaan browser saat print */
            @page { margin: 0; }
            .no-print { display: none; }
            .card-bg {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                background: linear-gradient(135deg, #4F46E5 0%, #312E81 100%) !important;
            }
        }
        
        .card-bg {
            background: linear-gradient(135deg, #4F46E5 0%, #312E81 100%);
            width: 85.6mm;
            height: 54mm;
        }

        /* Memastikan ukuran presisi ID-1 Card */
        #kartu-container {
            width: 85.6mm;
            height: 54mm;
        }
    </style>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">

    <div id="kartu-container">
        <div class="card-bg rounded-[15px] shadow-2xl relative text-white p-5 overflow-hidden">
            
            <div class="absolute -right-8 -top-8 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
            <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-indigo-400/10 rounded-full blur-2xl"></div>

            <div class="relative flex justify-between items-start border-b border-white/20 pb-2 mb-3">
                <div>
                    <h1 class="text-[14px] font-black tracking-tight uppercase leading-none">Perpustakaan Digital</h1>
                    <p class="text-[7px] tracking-[2px] text-indigo-200 font-bold uppercase mt-1">Kartu Anggota Resmi</p>
                </div>
                <div class="bg-white/20 p-1.5 rounded-lg">
                    <i class="fa fa-graduation-cap text-lg"></i>
                </div>
            </div>

            <div class="relative flex gap-4 items-center">
                <div class="w-[20mm] h-[25mm] rounded-lg overflow-hidden border-2 border-white/30 bg-slate-800 shrink-0 shadow-lg">
                    <img src="<?php echo $gambar; ?>" class="w-full h-full object-cover" alt="Foto Profil">
                </div>

                <div class="flex-1 min-w-0">
                    <div class="mb-2">
                        <p class="text-[6px] text-indigo-300 font-bold uppercase leading-none mb-1">Nama Lengkap</p>
                        <h3 class="text-[12px] font-bold truncate uppercase tracking-wide">
                            <?php echo htmlspecialchars($data['nama']); ?>
                        </h3>
                    </div>
                    <div class="mb-2">
                        <p class="text-[6px] text-indigo-300 font-bold uppercase leading-none mb-1">Nomor Anggota</p>
                        <p class="text-[11px] font-mono font-black tracking-wider">
                            <?php echo htmlspecialchars($data['no_anggota'] ?: 'BELUM ADA ID'); ?>
                        </p>
                    </div>
                    
                    <div class="bg-white/90 p-1 rounded-sm w-fit flex items-end gap-[1px] h-5">
                        <?php for($i=0; $i<35; $i++): 
                            $w = rand(1, 2);
                            $h = rand(70, 100);
                        ?>
                            <div class="bg-black w-[<?php echo $w; ?>px] h-[<?php echo $h; ?>%]"></div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-3 left-5 right-5 flex justify-between items-end">
                <p class="text-[7px] italic font-medium text-indigo-100/70">Berlaku selama menjadi anggota aktif</p>
                <div class="text-right">
                    <p class="text-[8px] font-black tracking-tighter opacity-40">E-PERPUS SMANTEN</p>
                </div>
            </div>
        </div>
    </div>

    <script src="js/cetak_kartu.js"></script>

</body>
</html>