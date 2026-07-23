<?php
session_start();

// Kembalikan sesi ke akun admin asli, kalau ada
if (isset($_SESSION['admin_asli'])) {
    $_SESSION['user'] = $_SESSION['admin_asli'];
    unset($_SESSION['admin_asli']);
}

header("Location: dashboard.php");
exit;