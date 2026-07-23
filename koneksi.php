<?php
/**
 * koneksi.php
 * -----------------------------------------------------------------
 * File ini TIDAK LAGI menyimpan kredensial database secara langsung.
 * Semua diarahkan ke config.php supaya pengaturan sistem terpusat
 * di satu tempat dan bisa diubah lewat halaman pengaturan.php.
 *
 * Tidak perlu mengubah file lain yang sudah memakai:
 *   include "koneksi.php";
 * karena variabel $conn tetap tersedia seperti sebelumnya.
 * -----------------------------------------------------------------
 */
require_once __DIR__ . '/config.php';
