<?php
session_start();
include 'koneksi.php';

// Proteksi Login
if (!isset($_SESSION['user'])) {
	header("Location: index.php");
	exit;
}

$user = $_SESSION['user'];
$role = $user['role'] ?? 'anggota';

// Proteksi: anggota tidak punya menu Data Anggota (khusus pustakawan & admin)
if ($role === 'anggota') {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Daftar Anggota | E-Perpus SMANTEN</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=300;400;500;600;700&display=swap" rel="stylesheet">
	<script src="js/anggota.js"></script>
	<style>
		@media (max-width: 768px) {
			.sidebar-hidden { transform: translateX(-100%); }
			.sidebar-active { transform: translateX(0); }
		}
	</style>
</head>
<body class="bg-slate-50 dark:bg-darkBlue text-slate-800 dark:text-slate-100 min-h-screen transition-colors duration-300">

	<div class="flex min-h-screen relative overflow-x-hidden">
		<aside id="sidebar" class="fixed md:sticky top-0 left-0 z-40 w-72 h-screen bg-slate-900 dark:bg-white border-r border-slate-800 dark:border-slate-200 p-6 flex flex-col justify-between transition-transform duration-300 sidebar-hidden md:transform-none text-slate-300 dark:text-slate-600 transition-colors duration-300">
			<div>
				<div class="flex items-center gap-3 mb-8 px-2">
					<div class="w-10 h-10 rounded-xl bg-brand flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
						<i class="fa fa-book-open text-lg"></i>
					</div>
					<div>
						<h1 class="font-bold text-base tracking-tight leading-none mb-1 text-white dark:text-slate-900">SMANTEN</h1>
						<p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Digital Library</p>
					</div>
				</div>

           <!-- Menu Navigasi Berdasarkan Role -->
                <nav class="space-y-1.5">
                    <p class="px-3 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3">Menu Utama</p>
                    
                    <a href="dashboard.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                        <i class="fa fa-th-large text-base w-5 text-center"></i> Dashboard
                    </a>
                    
                    <?php if ($role == 'admin' || $role == 'pustakawan'): ?>
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
                        <?php if ($role == 'admin'): ?>
                        <a href="pengaturan.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                            <i class="fa fa-sliders text-base w-5 text-center"></i> Pengaturan Sistem
                        </a>
                        <!-- Admin memegang semua kontrol sistem, termasuk menu khusus anggota -->
                        <a href="daftar_buku.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                            <i class="fa fa-book text-base w-5 text-center"></i> Katalog Buku
                        </a>
                        <a href="buku_pinjam.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                            <i class="fa fa-book-reader text-base w-5 text-center"></i> Ruang Baca Digital
                        </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Menu khusus Anggota -->
                        <a href="daftar_buku.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                            <i class="fa fa-book text-base w-5 text-center"></i> Katalog Buku
                        </a>
                        <a href="buku_pinjam.php" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 dark:text-slate-500 hover:bg-slate-800/60 dark:hover:bg-slate-100 hover:text-white dark:hover:text-brand transition-all">
                            <i class="fa fa-book-reader text-base w-5 text-center"></i> Ruang Baca Digital
                        </a>
                    <?php endif; ?>
                </nav>
            </div>

            <div class="border-t border-slate-800 dark:border-slate-200 pt-4">
                <a href="logout.php" class="flex items-center gap-3 px-3 py-2 text-rose-400 dark:text-rose-600 hover:bg-rose-500/10 dark:hover:bg-rose-50 rounded-xl transition-all">
                    <i class="fa fa-sign-out w-5"></i> Logout
                </a>
            </div>
		</aside>

		<main class="flex-1 p-4 md:p-8 min-w-0">
			<header class="flex items-center justify-between mb-8 bg-white dark:bg-darkCard p-4 rounded-2xl border border-slate-100 dark:border-slate-800/60 shadow-sm transition-colors duration-300">
				<div class="flex items-center gap-4">
					<button onclick="toggleSidebar()" class="md:hidden w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center">
						<i class="fa fa-bars text-slate-600 dark:text-slate-300"></i>
					</button>
					<div>
						<h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Daftar Anggota</h2>
						<p class="text-xs text-slate-400 hidden sm:block">Kelola data anggota perpustakaan</p>
					</div>
				</div>
				<div class="flex items-center gap-3">
					<button onclick="toggleDarkMode()" class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/50 flex items-center justify-center text-slate-500 dark:text-slate-400 transition-all">
						<i id="theme-icon" class="fa fa-moon text-base"></i>
					</button>
				</div>
			</header>

			<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
				<div>
					<p class="text-sm text-slate-500 dark:text-slate-400">Total anggota tercatat dalam sistem</p>
				</div>
				<div class="flex items-center gap-3">
					<?php if ($role == 'admin' || $role == 'pustakawan'): ?>
						<a href="tambah_anggota.php" class="inline-flex items-center gap-2 bg-brand hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-indigo-500/20 transition-all">
							<i class="fa fa-plus-circle"></i> Tambah Anggota
						</a>
					<?php endif; ?>
				</div>
			</div>

			<div class="bg-white dark:bg-darkCard rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
				<div class="overflow-x-auto">
					<table class="w-full text-left border-collapse">
						<thead>
							<tr class="bg-slate-50/70 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400">
								<th class="py-4 px-6 w-16 text-center">No</th>
								<th class="py-4 px-6">Informasi Anggota</th>
								<th class="py-4 px-6 hidden md:table-cell">Role & Status</th>
								<th class="py-4 px-6 text-center">Terdaftar</th>
								<th class="py-4 px-6 text-center w-40">Aksi</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
							<?php 
							$no = 1;
							// Admin bisa lihat semua role (anggota, pustakawan, admin).
							// Pustakawan hanya boleh lihat data anggota saja
							// (tidak boleh lihat sesama pustakawan maupun admin).
							if ($role === 'pustakawan') {
								$query = mysqli_query($conn, "SELECT * FROM pengguna WHERE role = 'anggota' ORDER BY id_pengguna DESC");
							} else {
								$query = mysqli_query($conn, "SELECT * FROM pengguna ORDER BY id_pengguna DESC");
							}
							while ($row = mysqli_fetch_assoc($query)) {
								$foto_path = 'img/foto/' . $row['foto'];
								if (!empty($row['foto']) && file_exists($foto_path)) {
									$avatar = $foto_path;
								} else {
									$avatar = 'https://ui-avatars.com/api/?name=' . urlencode($row['nama']) . '&background=4F46E5&color=fff&size=256';
								}

								$badge_role = 'text-emerald-600 bg-emerald-50 border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20';
								if($row['role'] == 'admin') $badge_role = 'text-rose-600 bg-rose-50 border-rose-100 dark:bg-rose-900/20 dark:border-rose-500/20';
								if($row['role'] == 'pustakawan') $badge_role = 'text-amber-600 bg-amber-50 border-amber-100 dark:bg-amber-900/20 dark:border-amber-500/20';
							?>
							<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
								<td class="py-4 px-6 font-medium text-slate-400 text-center"><?php echo $no++; ?></td>
								<td class="py-4 px-6">
									<div class="flex items-start gap-4">
										<img src="<?php echo $avatar; ?>" alt="Avatar" class="w-12 h-12 object-cover rounded-full shadow-sm border border-slate-100 dark:border-slate-800 flex-shrink-0">
										<div class="min-w-0">
											<h4 class="font-semibold text-slate-900 dark:text-white truncate max-w-[220px] sm:max-w-xs" title="<?php echo htmlspecialchars($row['nama']); ?>">
												<?php echo htmlspecialchars($row['nama']); ?>
											</h4>
											<p class="text-xs text-slate-400 truncate"><?php echo htmlspecialchars($row['email']); ?></p>
										</div>
									</div>
								</td>
								<td class="py-4 px-6 hidden md:table-cell">
									<div class="flex flex-col gap-1">
										<span class="inline-block px-3 py-1 text-xs font-semibold <?php echo $badge_role; ?> rounded-full w-max"><?php echo htmlspecialchars($row['role']); ?></span>
										<span class="text-sm <?php echo ($row['status']=='aktif') ? 'text-emerald-600' : 'text-rose-600'; ?> font-medium"><?php echo htmlspecialchars($row['status']); ?></span>
									</div>
								</td>
								<td class="py-4 px-6 text-center">
									<div class="text-sm text-slate-700 dark:text-slate-300 font-medium">
										<?php echo date('d M Y', strtotime($row['created_at'] ?? '1970-01-01')); ?>
									</div>
								</td>
								<td class="py-4 px-6">
									<div class="flex items-center justify-center gap-2">
										<a href="view_anggota.php?id=<?php echo $row['id_pengguna']; ?>" class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition-all" title="Lihat Detail">
											<i class="fa fa-eye text-sm"></i>
										</a>

										<?php if ($role == 'admin' && $row['id_pengguna'] != $user['id_pengguna']): ?>
											<a href="impersonate.php?id=<?php echo $row['id_pengguna']; ?>" class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center transition-all" title="Masuk sebagai akun ini" onclick="return confirm('Masuk sebagai akun <?php echo htmlspecialchars(addslashes($row['nama'])); ?>?');">
												<i class="fa fa-right-to-bracket text-sm"></i>
											</a>
										<?php endif; ?>

										<?php if ($role == 'admin' || $role == 'pustakawan'): ?>
											<a href="edit_anggota.php?id=<?php echo $row['id_pengguna']; ?>" class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/30 hover:bg-amber-100 dark:hover:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center transition-all" title="Edit Data">
												<i class="fa fa-edit text-sm"></i>
											</a>
											<a href="hapus_anggota.php?id=<?php echo $row['id_pengguna']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini?')" class="w-9 h-9 rounded-xl bg-rose-50 dark:bg-rose-950/30 hover:bg-rose-100 dark:hover:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center transition-all" title="Hapus Anggota">
												<i class="fa fa-trash text-sm"></i>
											</a>
										<?php endif; ?>
									</div>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</main>
	</div>

	<script src="js/anggota_2.js"></script>
</body>
</html>