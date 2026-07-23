<?php
session_start();
include 'koneksi.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = $_POST['password'];

$query = mysqli_query($conn, "SELECT * FROM pengguna WHERE nama='$username'");
$data = mysqli_fetch_assoc($query);

if ($data) {
    if ($data['status'] !== 'aktif') {
        echo "<script>alert('Akun Anda sudah non-aktif. Silahkan hubungi admin.'); window.location='index.php';</script>";
        exit;
    }

    if (password_verify($password, $data['password']) || $password == $data['password']) {

        // Set session
        $_SESSION['user'] = [
            'id_pengguna' => $data['id_pengguna'],
            'nama' => $data['nama'],
            'role' => $data['role']
        ];

        // --- LOGIKA REDIRECT KE DASHBOARD UTAMA ---
        // Semua role (admin, pustakawan, maupun anggota) langsung diarahkan ke dashboard.php
        header("Location: dashboard.php");
        exit;

    } else {
        echo "<script>alert('Password salah'); window.location='index.php#login';</script>";
    }
} else {
    echo "<script>alert('User tidak ditemukan atau belum terdaftar'); window.location='index.php#login';</script>";
}
?>