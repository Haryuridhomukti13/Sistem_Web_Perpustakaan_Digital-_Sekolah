<?php
session_start();
include 'koneksi.php';

// 1. Proteksi Halaman: Pastikan user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];

// 2. Proteksi Role: Hanya Admin yang biasanya punya akses hapus anggota
if ($role == 'anggota') {
    echo "<script>alert('Anda tidak memiliki akses!'); window.location='buku.php';</script>";
    exit;
}

// 3. Cek apakah ada ID anggota yang dikirim melalui URL
if (isset($_GET['id'])) {
    // Ambil ID dan amankan
    $id_pengguna = mysqli_real_escape_string($conn, $_GET['id']);

    // 4. Proteksi Tambahan: Jangan biarkan admin menghapus dirinya sendiri
    if ($id_pengguna == $user['id_pengguna']) {
        echo "<script>
                alert('Gagal! Anda tidak bisa menghapus akun Anda sendiri yang sedang digunakan.');
                window.location='anggota.php';
              </script>";
        exit;
    }

    // 5. Jalankan Query Hapus berdasarkan id_pengguna
    $query = "DELETE FROM pengguna WHERE id_pengguna = '$id_pengguna'";

    if (mysqli_query($conn, $query)) {
        // Jika berhasil
        echo "<script>
                alert('Anggota berhasil dihapus!');
                window.location='anggota.php';
              </script>";
    } else {
        // Jika gagal (biasanya karena ID anggota ini ada di tabel pinjam/transaksi)
        echo "<script>
                alert('Gagal menghapus anggota! Data ini mungkin masih terkait dengan riwayat peminjaman buku.');
                window.location='anggota.php';
              </script>";
    }
} else {
    // Jika tidak ada ID di URL, kembalikan ke halaman anggota
    header("Location: anggota.php");
    exit;
}
?>