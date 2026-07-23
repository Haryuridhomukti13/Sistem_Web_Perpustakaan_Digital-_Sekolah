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

// 2. Proteksi Role: Hanya Admin dan Pustakawan yang boleh menghapus
if ($role == 'anggota') {
    echo "<script>alert('Anda tidak memiliki akses!'); window.location='buku.php';</script>";
    exit;
}

// 3. Cek apakah ada ID yang dikirim melalui URL
if (isset($_GET['id'])) {
    // Ambil ID dan amankan dari SQL Injection
    $id_buku = mysqli_real_escape_string($conn, $_GET['id']);

    // 4. Jalankan Query Hapus berdasarkan id_buku
    $query = "DELETE FROM buku WHERE id_buku = '$id_buku'";

    if (mysqli_query($conn, $query)) {
        // Jika berhasil, tampilkan notifikasi dan kembali ke dashboard
        echo "<script>
                alert('Buku berhasil dihapus!');
                window.location='buku.php';
              </script>";
    } else {
        // Jika gagal (misalnya karena buku sedang dipinjam/foreign key constraint)
        echo "<script>
                alert('Gagal menghapus buku! Kemungkinan buku masih terkait dengan data peminjaman.');
                window.location='buku.php';
              </script>";
    }
} else {
    // Jika tidak ada ID di URL, kembalikan ke dashboard
    header("Location: buku.php");
    exit;
}
?>
