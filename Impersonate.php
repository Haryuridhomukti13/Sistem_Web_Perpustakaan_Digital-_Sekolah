<?php
session_start();
include 'koneksi.php';

// Hanya admin asli yang boleh menjalankan akun orang lain
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo "<script>alert('Akses khusus Admin!'); window.location.href='dashboard.php';</script>";
    exit;
}

$id_target = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id_target <= 0) {
    header("Location: anggota.php");
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_pengguna = '$id_target'");
$target = mysqli_fetch_assoc($query);

if (!$target) {
    echo "<script>alert('Akun tidak ditemukan'); window.location.href='anggota.php';</script>";
    exit;
}

// Simpan sesi admin asli supaya bisa kembali lagi nanti (kalau belum tersimpan,
// misalnya admin loncat dari satu akun yang di-impersonate ke akun lain).
if (!isset($_SESSION['admin_asli'])) {
    $_SESSION['admin_asli'] = $_SESSION['user'];
}

// Ganti sesi aktif jadi akun target
$_SESSION['user'] = [
    'id_pengguna' => $target['id_pengguna'],
    'nama' => $target['nama'],
    'role' => $target['role']
];

header("Location: dashboard.php");
exit;