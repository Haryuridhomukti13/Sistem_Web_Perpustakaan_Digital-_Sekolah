<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id_pinjam']) && isset($_GET['id_buku'])) {
    $id_pinjam = mysqli_real_escape_string($conn, $_GET['id_pinjam']);
    $id_buku   = mysqli_real_escape_string($conn, $_GET['id_buku']);

    // Mulai transaksi agar data konsisten
    mysqli_begin_transaction($conn);

    try {
        // 1. Tambah stok buku kembali karena buku telah dikembalikan
        $update_stok = "UPDATE buku SET jumlah_tersedia = jumlah_tersedia + 1 WHERE id_buku = '$id_buku'";
        mysqli_query($conn, $update_stok);

        // 2. Hapus data dari tabel pinjam (sesuai permintaan Anda untuk menghapus dari daftar)
        // Jika ingin tetap ada riwayat, ganti DELETE menjadi UPDATE status='dikembalikan'
        $hapus_pinjam = "DELETE FROM pinjam WHERE id_pinjam = '$id_pinjam'";
        mysqli_query($conn, $hapus_pinjam);

        mysqli_commit($conn);
        echo "<script>alert('Buku berhasil dikembalikan!'); window.location='buku_pinjam.php';</script>";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Gagal mengembalikan buku.'); window.location='buku_pinjam.php';</script>";
    }
} else {
    header("Location: buku_pinjam.php");
}
?>
