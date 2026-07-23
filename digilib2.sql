-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2026 at 10:22 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `digilib2`
--

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id_buku` int(11) NOT NULL,
  `sampul` varchar(255) DEFAULT 'default.jpg',
  `isbn` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `judul` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `pengarang` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `penerbit` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `tahun_terbit` year(4) NOT NULL,
  `kategori` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `jumlah_total` int(11) NOT NULL,
  `jumlah_tersedia` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `file_pdf` varchar(255) DEFAULT NULL,
  `jumlah_halaman` varchar(255) NOT NULL,
  `deskripsi_buku` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id_buku`, `sampul`, `isbn`, `judul`, `pengarang`, `penerbit`, `tahun_terbit`, `kategori`, `jumlah_total`, `jumlah_tersedia`, `created_at`, `file_pdf`, `jumlah_halaman`, `deskripsi_buku`) VALUES
(7, 'Screenshot 2026-07-20 195524.png', '673-2321-232', 'Diatas Langit Masih ada Langit', 'Sujatminto', 'Gramedia', '2026', 'Umum', 15, 10, '2026-07-21 11:37:59', '1780584750_472-Article_Text-1684-1-10-20221223.pdf', '100', 'dibuat dengan sepenuh cinta\r\n\r\n'),
(8, 'cover_6a5f4ab7f2094.jpg', '123-456-7890', 'Pendidikan Jasmani, Olahraga dan Kesehatan', 'Kementrian Pendidikan', 'Kementrian Pendidikan', '2026', 'Buku Pelajaran', 2, 3, '2026-07-21 12:01:57', '1784629943_Book_review_of_Lloyd_A_2021_The_qualitative_landsc.pdf', '150', 'Buku Pelajaran'),
(9, 'cover_6a5f4bff4b82c.jpg', '123-456-789-01', 'Buku Tata Tertib & Tata Krama Siswa', 'Kementrian Pendidikan', 'Gramedia', '2026', 'Buku Pelajaran', 2, 2, '2026-07-21 11:27:37', '1784630271__Bilson+Simamora-CR.pdf', '150', 'Buku Tata Krama'),
(10, 'cover_6a5f4c5ccdff1.jpg', '123-456-789-02', 'Modul Ajar Matematika', 'Olivia Wilson', 'Gramedia', '2026', 'Buku Pelajaran', 2, 2, '2026-07-21 10:39:24', '1784630364_6.+Asnawi+IJLIS+120-128.pdf', '150', 'Buku Ajar kurikulum merdeka'),
(11, 'cover_6a5f4caf5b14e.jpg', '123-456-789-03', 'Kewarganegaraan: Kewajiban dan Hakku', 'Kementrian Pendidikan', 'Gramedia', '2026', 'Buku Pelajaran', 2, 2, '2026-07-21 10:40:47', '1784630447_103-106+Skala+Literasi+AI+terhadap+Prestasi+Belajar+Mahasiswa+dalam+Konteks+Pendidikan+Level+Perguruan+Tinggi+di+Era+Digital.pdf', '150', 'Buku pelajaran'),
(12, 'cover_6a5f4d040e287.jpg', '123-456-789-04', 'Bahasa Indonesia', 'Kementrian Pendidikan', 'Gramedia', '2026', 'Buku Pelajaran', 2, 4, '2026-07-22 04:42:53', '1784630532_UjiValiditasdanUjiReliabilitasPadaDataPenelitian.pdf', '150', 'Buku Pelajaran'),
(13, 'cover_6a5f4d63cc40e.jpg', '123-456-789-05', 'Buku Panduan Guru : Ilmu Pengetahuan Sosial', 'Kementrian Pendidikan', 'Kementrian Pendidikan', '2026', 'Buku Pelajaran', 2, 2, '2026-07-21 12:01:55', '1784630627_Symantic_Literature_Review_Manfaat_Artificial.pdf', '150', 'Buku Pelajaran'),
(14, 'cover_6a5f4db58590a.jpg', '123-456-789-06', 'Pendidikan Agama Islam dan Budi Perkerti', 'Kementrian Pendidikan', 'Kementrian Pendidikan', '2026', 'Buku Pelajaran', 2, 2, '2026-07-21 11:55:26', '1784630709_Students_as_AI_literate_designers_a_pedagogical_framework_for_learning_and_teaching_AI_literacy_in_elementary_education.pdf', '150', 'Buku Pelajaran'),
(15, 'cover_6a5f4e14763cb.jpg', '123-456-789-07', 'Sains : Ilmu Pengetahuan Alam SD/MI ', 'Sutami dan M.D Wijayanti', 'Kementrian Pendidikan', '2026', 'Buku Pelajaran', 2, 1, '2026-07-21 12:04:22', '1784630804_SkalaPengukuranInstrumenPenelitian-1.pdf', '150', 'Buku Pelajaran'),
(16, 'cover_6a5f4e7674dd3.jpg', '123-456-789-08', 'Pendidikan Kewarganegaraan', 'Sri Sadiman dan Mahfud', 'Gramedia', '2026', 'Buku Pelajaran', 2, 2, '2026-07-21 11:21:34', '1784630902_Refleksi_Mahasiswa_dalam_Berkeadaban_Digital_melalui_ChatGPT.pdf', '150', 'Buku Pelajaran');

--
-- Triggers `buku`
--
DELIMITER $$
CREATE TRIGGER `trg_buku_after_delete` AFTER DELETE ON `buku` FOR EACH ROW BEGIN
    INSERT INTO log_aktivitas (id_pengguna, aktivitas, ip_address, created_at)
    VALUES (0, CONCAT('HAPUS BUKU|Sistem menghapus data buku ID ', OLD.id_buku, ': ', OLD.judul), IFNULL(@ip_address, '127.0.0.1'), UNIX_TIMESTAMP());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_buku_after_insert` AFTER INSERT ON `buku` FOR EACH ROW BEGIN
    INSERT INTO log_aktivitas (id_pengguna, aktivitas, ip_address, created_at)
    VALUES (0, CONCAT('TAMBAH BUKU|Sistem menambahkan buku baru: ', NEW.judul, ' (ISBN: ', NEW.isbn, ')'), IFNULL(@ip_address, '127.0.0.1'), UNIX_TIMESTAMP());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_buku_after_update` AFTER UPDATE ON `buku` FOR EACH ROW BEGIN
    INSERT INTO log_aktivitas (id_pengguna, aktivitas, ip_address, created_at)
    VALUES (0, CONCAT('EDIT BUKU|Sistem mengubah data buku ID ', NEW.id_buku, ': ', OLD.judul, ' menjadi ', NEW.judul), IFNULL(@ip_address, '127.0.0.1'), UNIX_TIMESTAMP());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `galeri_prestasi`
--

CREATE TABLE `galeri_prestasi` (
  `id_prestasi` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `nama_peraih` varchar(255) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `deskripsi` text NOT NULL,
  `tanggal` date NOT NULL,
  `gambar` varchar(255) DEFAULT 'default_prestasi.jpg',
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `galeri_prestasi`
--

INSERT INTO `galeri_prestasi` (`id_prestasi`, `judul`, `nama_peraih`, `kategori`, `deskripsi`, `tanggal`, `gambar`, `tanggal_dibuat`) VALUES
(3, 'Juara 2 Lomba Badminton Tunggal Putra', 'Ginting', 'Non-Akademik', 'Peraih Juara Lomba o2sn  cabang badminton ', '2026-07-21', '1784628952_ginting.jpg', '2026-07-21 10:15:52'),
(4, 'Juara 1 Lomba Sepak Bola', 'Mesi', 'Non-Akademik', 'Peraih Juara 1 O2SN cabang Sepak Bola', '2026-07-22', '1784629129_messi.jpg', '2026-07-21 10:18:49'),
(5, 'Juara 1 Lomba Cerdas Cermat', 'Ilham', 'Akademik', 'Meraih Juara 1 OSN kategori Matematika', '2026-07-21', '1784629436_mtk.jpg', '2026-07-21 10:23:56'),
(6, 'Juara 2 Lomba OSN Bahasa Inggris', 'Salsa', 'Akademik', 'Meraih juara 2 lomba OSN bahasa Inggris', '2026-07-21', '1784629541_lomba.jpg', '2026-07-21 10:25:41');

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id_log` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `aktivitas` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `ip_address` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id_log`, `id_pengguna`, `aktivitas`, `ip_address`, `created_at`) VALUES
(1, 2, 'PEMINJAMAN|Meminjam buku ID: 6', '127.0.0.1', 1779014019),
(2, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak', '127.0.0.1', 1779014019),
(3, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak', '127.0.0.1', 1779014048),
(4, 2, 'PEMINJAMAN|Meminjam buku ID: 6 Jatuh tempo: 2026-05-24', '127.0.0.1', 1779016075),
(5, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', '127.0.0.1', 1779016075),
(6, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', '127.0.0.1', 1779016089),
(7, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', '127.0.0.1', 1779018958),
(8, 2, 'PEMINJAMAN|Meminjam buku ID: 6 Jatuh tempo: 2026-05-25', '127.0.0.1', 1779063639),
(9, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', '127.0.0.1', 1779063639),
(10, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', '127.0.0.1', 1779063657),
(11, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', '127.0.0.1', 1779669713),
(12, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', '127.0.0.1', 1779669828),
(13, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', '127.0.0.1', 1779670188),
(14, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', '127.0.0.1', 1780584167),
(15, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', '127.0.0.1', 1780584205),
(16, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', '127.0.0.1', 1780584536),
(17, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', '127.0.0.1', 1780584628),
(18, 1, 'TAMBAH BUKU|Menambahkan buku baru: edq (ISBN: 673-2321-232)', '127.0.0.1', 1780584750),
(19, 2, 'PEMINJAMAN|Meminjam buku ID: 6 Jatuh tempo: 2026-06-11', '127.0.0.1', 1780584853),
(20, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', '127.0.0.1', 1780584854),
(21, 2, 'PEMINJAMAN|Meminjam buku ID: 7 Jatuh tempo: 2026-06-11', '127.0.0.1', 1780587217),
(22, 1, 'EDIT BUKU|Mengubah data buku ID 7: edq menjadi edq', '127.0.0.1', 1780587217),
(23, 2, 'STATUS PINJAM|Status pinjam ID 9 berubah menjadi dikembalikan', '127.0.0.1', 1780587451),
(24, 1, 'EDIT BUKU|Mengubah data buku ID 7: edq menjadi edq', '127.0.0.1', 1780587451),
(25, 2, 'PEMINJAMAN|Meminjam buku ID: 7 Jatuh tempo: 2026-06-11', '127.0.0.1', 1780587943),
(26, 1, 'EDIT BUKU|Mengubah data buku ID 7: edq menjadi edq', '127.0.0.1', 1780587943),
(27, 2, 'PEMINJAMAN|Meminjam buku ID: 7 Jatuh tempo: 2026-06-11', '127.0.0.1', 1780588493),
(28, 1, 'EDIT BUKU|Mengubah data buku ID 7: edq menjadi edq', '127.0.0.1', 1780588493),
(29, 2, 'PEMINJAMAN|Meminjam buku ID: 7 Jatuh tempo: 2026-06-11', '127.0.0.1', 1780588621),
(30, 1, 'EDIT BUKU|Mengubah data buku ID 7: edq menjadi edq', '127.0.0.1', 1780588621),
(31, 2, 'STATUS PINJAM|Status pinjam ID 12 berubah menjadi dikembalikan', '127.0.0.1', 1780588626),
(32, 1, 'EDIT BUKU|Mengubah data buku ID 7: edq menjadi edq', '127.0.0.1', 1780588626),
(33, 2, 'STATUS PINJAM|Status pinjam ID 11 berubah menjadi dikembalikan', '127.0.0.1', 1780588628),
(34, 1, 'EDIT BUKU|Mengubah data buku ID 7: edq menjadi edq', '127.0.0.1', 1780588628),
(35, 2, 'STATUS PINJAM|Status pinjam ID 10 berubah menjadi dikembalikan', '127.0.0.1', 1780588631),
(36, 1, 'EDIT BUKU|Mengubah data buku ID 7: edq menjadi edq', '127.0.0.1', 1780588631),
(37, 2, 'PEMINJAMAN|Meminjam buku ID: 7 Jatuh tempo: 2026-06-11', '127.0.0.1', 1780588640),
(38, 1, 'EDIT BUKU|Mengubah data buku ID 7: edq menjadi edq', '127.0.0.1', 1780588640),
(39, 13, 'UPDATE PROFIL|Perubahan data pada pengguna: Kharisma Shofiana Azizah', '127.0.0.1', 1780588757),
(40, 13, 'PEMINJAMAN|Meminjam buku ID: 7 Jatuh tempo: 2026-06-11', '127.0.0.1', 1780588789),
(41, 1, 'EDIT BUKU|Mengubah data buku ID 7: edq menjadi edq', '127.0.0.1', 1780588789),
(42, 13, 'PEMINJAMAN|Meminjam buku ID: 7 Jatuh tempo: 2026-06-11', '127.0.0.1', 1780589150),
(43, 1, 'EDIT BUKU|Mengubah data buku ID 7: edq menjadi edq', '127.0.0.1', 1780589150),
(44, 1, 'UPDATE PROFIL|Perubahan data pada pengguna: admin', '127.0.0.1', 1780590769),
(45, 13, 'STATUS PINJAM|Status pinjam ID 15 berubah menjadi dikembalikan', '127.0.0.1', 1780591458),
(46, 1, 'EDIT BUKU|Mengubah data buku ID 7: edq menjadi edq', '127.0.0.1', 1780591458),
(47, 13, 'STATUS PINJAM|Status pinjam ID 14 berubah menjadi dikembalikan', '127.0.0.1', 1780591461),
(48, 1, 'EDIT BUKU|Mengubah data buku ID 7: edq menjadi edq', '127.0.0.1', 1780591461),
(49, 2, 'STATUS PINJAM|Status pinjam ID 13 berubah menjadi dikembalikan', '127.0.0.1', 1780591463),
(50, 1, 'EDIT BUKU|Mengubah data buku ID 7: edq menjadi edq', '127.0.0.1', 1780591463),
(51, 2, 'PEMINJAMAN|Meminjam buku ID: 6 Jatuh tempo: 2026-06-11', '127.0.0.1', 1780591470),
(52, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', '127.0.0.1', 1780591470),
(53, 2, 'PEMINJAMAN|Meminjam buku ID: 6 Jatuh tempo: 2026-06-11', '127.0.0.1', 1780591478),
(54, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', '127.0.0.1', 1780591478),
(55, 2, 'PEMINJAMAN|Meminjam buku ID: 6 Jatuh tempo: 2026-06-11', '127.0.0.1', 1780591485),
(56, 1, 'EDIT BUKU|Mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', '127.0.0.1', 1780591485),
(57, 0, 'EDIT BUKU|Sistem mengubah data buku ID 6: Buku Cerita Anak menjadi Buku Cerita Anak', 'Sistem', 1784596824),
(58, 0, 'EDIT BUKU|Sistem mengubah data buku ID 7: Diatas Langit Masih ada Langit menjadi Diatas Langit Masih', '127.0.0.1', 1784596984),
(59, 0, 'TAMBAH BUKU|Sistem menambahkan buku baru: Pendidikan Jasmani, Olahraga dan Kesehatan (ISBN: 123-456-', '127.0.0.1', 1784629943),
(60, 0, 'TAMBAH BUKU|Sistem menambahkan buku baru: Buku Tata Tertib & Tata Krama Siswa (ISBN: 123-456-789-01)', '127.0.0.1', 1784630271),
(61, 0, 'TAMBAH BUKU|Sistem menambahkan buku baru: Modul Ajar Matematika (ISBN: 123-456-789-02)', '127.0.0.1', 1784630364),
(62, 0, 'TAMBAH BUKU|Sistem menambahkan buku baru: Kewarganegaraan: Kewajiban dan Hakku (ISBN: 123-456-789-03', '127.0.0.1', 1784630447),
(63, 0, 'TAMBAH BUKU|Sistem menambahkan buku baru: Bahasa Indonesia (ISBN: 123-456-789-04)', '127.0.0.1', 1784630532),
(64, 0, 'TAMBAH BUKU|Sistem menambahkan buku baru: Buku Panduan Guru : Ilmu Pengetahuan Sosial (ISBN: 123-456', '127.0.0.1', 1784630627),
(65, 0, 'TAMBAH BUKU|Sistem menambahkan buku baru: Pendidikan Agama Islam dan Budi Perkerti (ISBN: 123-456-78', '127.0.0.1', 1784630709),
(66, 0, 'TAMBAH BUKU|Sistem menambahkan buku baru: Sains : Ilmu Pengetahuan Alam SD/MI  (ISBN: 123-456-789-07', '127.0.0.1', 1784630804),
(67, 0, 'TAMBAH BUKU|Sistem menambahkan buku baru: Pendidikan Kewarganegaraan (ISBN: 123-456-789-08)', '127.0.0.1', 1784630902),
(68, 0, 'HAPUS BUKU|Sistem menghapus data buku ID 6: Buku Cerita Anak', '127.0.0.1', 1784631171),
(69, 0, 'EDIT BUKU|Sistem mengubah data buku ID 7: Diatas Langit Masih ada Langit menjadi Diatas Langit Masih', '127.0.0.1', 1784631931),
(70, 0, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 7 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784631931),
(71, 0, 'EDIT BUKU|Sistem mengubah data buku ID 7: Diatas Langit Masih ada Langit menjadi Diatas Langit Masih', '127.0.0.1', 1784631931),
(72, 0, 'EDIT BUKU|Sistem mengubah data buku ID 7: Diatas Langit Masih ada Langit menjadi Diatas Langit Masih', '127.0.0.1', 1784632294),
(73, 0, 'STATUS PINJAM|Sistem mengubah status pinjam ID 20 milik fafa menjadi dikembalikan', '127.0.0.1', 1784632294),
(74, 0, 'EDIT BUKU|Sistem mengubah data buku ID 7: Diatas Langit Masih ada Langit menjadi Diatas Langit Masih', '127.0.0.1', 1784632294),
(75, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784632332),
(76, 0, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 12 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784632332),
(77, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784632332),
(78, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784632385),
(79, 0, 'STATUS PINJAM|Sistem mengubah status pinjam ID 21 milik fafa menjadi dikembalikan', '127.0.0.1', 1784632385),
(80, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784632385),
(81, 0, 'EDIT BUKU|Sistem mengubah data buku ID 16: Pendidikan Kewarganegaraan menjadi Pendidikan Kewarganega', '127.0.0.1', 1784632646),
(82, 0, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 16 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784632646),
(83, 0, 'EDIT BUKU|Sistem mengubah data buku ID 16: Pendidikan Kewarganegaraan menjadi Pendidikan Kewarganega', '127.0.0.1', 1784632646),
(84, 0, 'EDIT BUKU|Sistem mengubah data buku ID 8: Pendidikan Jasmani, Olahraga dan Kesehatan menjadi Pendidi', '127.0.0.1', 1784632655),
(85, 0, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 8 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784632655),
(86, 0, 'EDIT BUKU|Sistem mengubah data buku ID 8: Pendidikan Jasmani, Olahraga dan Kesehatan menjadi Pendidi', '127.0.0.1', 1784632655),
(87, 0, 'EDIT BUKU|Sistem mengubah data buku ID 14: Pendidikan Agama Islam dan Budi Perkerti menjadi Pendidik', '127.0.0.1', 1784632663),
(88, 0, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 14 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784632663),
(89, 0, 'EDIT BUKU|Sistem mengubah data buku ID 14: Pendidikan Agama Islam dan Budi Perkerti menjadi Pendidik', '127.0.0.1', 1784632663),
(90, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784632860),
(91, 0, 'PEMINJAMAN|Sistem mencatat admin meminjam buku ID: 12 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784632860),
(92, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784632860),
(93, 0, 'EDIT BUKU|Sistem mengubah data buku ID 16: Pendidikan Kewarganegaraan menjadi Pendidikan Kewarganega', '127.0.0.1', 1784632894),
(94, 0, 'STATUS PINJAM|Sistem mengubah status pinjam ID 22 milik fafa menjadi dikembalikan', '127.0.0.1', 1784632894),
(95, 0, 'EDIT BUKU|Sistem mengubah data buku ID 16: Pendidikan Kewarganegaraan menjadi Pendidikan Kewarganega', '127.0.0.1', 1784632894),
(96, 0, 'EDIT BUKU|Sistem mengubah data buku ID 8: Pendidikan Jasmani, Olahraga dan Kesehatan menjadi Pendidi', '127.0.0.1', 1784632896),
(97, 0, 'STATUS PINJAM|Sistem mengubah status pinjam ID 23 milik fafa menjadi dikembalikan', '127.0.0.1', 1784632896),
(98, 0, 'EDIT BUKU|Sistem mengubah data buku ID 8: Pendidikan Jasmani, Olahraga dan Kesehatan menjadi Pendidi', '127.0.0.1', 1784632896),
(99, 0, 'EDIT BUKU|Sistem mengubah data buku ID 14: Pendidikan Agama Islam dan Budi Perkerti menjadi Pendidik', '127.0.0.1', 1784632897),
(100, 0, 'STATUS PINJAM|Sistem mengubah status pinjam ID 24 milik fafa menjadi dikembalikan', '127.0.0.1', 1784632897),
(101, 0, 'EDIT BUKU|Sistem mengubah data buku ID 14: Pendidikan Agama Islam dan Budi Perkerti menjadi Pendidik', '127.0.0.1', 1784632897),
(102, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784632898),
(103, 0, 'STATUS PINJAM|Sistem mengubah status pinjam ID 25 milik admin menjadi dikembalikan', '127.0.0.1', 1784632898),
(104, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784632898),
(105, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784632919),
(106, 0, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 12 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784632919),
(107, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784632919),
(108, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784632944),
(109, 0, 'STATUS PINJAM|Sistem mengubah status pinjam ID 26 milik fafa menjadi dikembalikan', '127.0.0.1', 1784632944),
(110, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784632944),
(111, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784633144),
(112, 0, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 12 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784633144),
(113, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784633144),
(114, 0, 'EDIT BUKU|Sistem mengubah data buku ID 14: Pendidikan Agama Islam dan Budi Perkerti menjadi Pendidik', '127.0.0.1', 1784633185),
(115, 0, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 14 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784633185),
(116, 0, 'EDIT BUKU|Sistem mengubah data buku ID 14: Pendidikan Agama Islam dan Budi Perkerti menjadi Pendidik', '127.0.0.1', 1784633185),
(117, 0, 'EDIT BUKU|Sistem mengubah data buku ID 9: Buku Tata Tertib & Tata Krama Siswa menjadi Buku Tata Tert', '127.0.0.1', 1784633201),
(118, 0, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 9 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784633201),
(119, 0, 'EDIT BUKU|Sistem mengubah data buku ID 9: Buku Tata Tertib & Tata Krama Siswa menjadi Buku Tata Tert', '127.0.0.1', 1784633201),
(120, 0, 'EDIT BUKU|Sistem mengubah data buku ID 14: Pendidikan Agama Islam dan Budi Perkerti menjadi Pendidik', '127.0.0.1', 1784633254),
(121, 0, 'STATUS PINJAM|Sistem mengubah status pinjam ID 28 milik fafa menjadi dikembalikan', '127.0.0.1', 1784633254),
(122, 0, 'EDIT BUKU|Sistem mengubah data buku ID 14: Pendidikan Agama Islam dan Budi Perkerti menjadi Pendidik', '127.0.0.1', 1784633255),
(123, 0, 'EDIT BUKU|Sistem mengubah data buku ID 9: Buku Tata Tertib & Tata Krama Siswa menjadi Buku Tata Tert', '127.0.0.1', 1784633257),
(124, 0, 'STATUS PINJAM|Sistem mengubah status pinjam ID 29 milik fafa menjadi dikembalikan', '127.0.0.1', 1784633257),
(125, 0, 'EDIT BUKU|Sistem mengubah data buku ID 9: Buku Tata Tertib & Tata Krama Siswa menjadi Buku Tata Tert', '127.0.0.1', 1784633257),
(126, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784633258),
(127, 0, 'STATUS PINJAM|Sistem mengubah status pinjam ID 27 milik fafa menjadi dikembalikan', '127.0.0.1', 1784633258),
(128, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784633258),
(129, 0, 'EDIT BUKU|Sistem mengubah data buku ID 13: Buku Panduan Guru : Ilmu Pengetahuan Sosial menjadi Buku ', '127.0.0.1', 1784633406),
(130, 0, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 13 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784633406),
(131, 0, 'EDIT BUKU|Sistem mengubah data buku ID 13: Buku Panduan Guru : Ilmu Pengetahuan Sosial menjadi Buku ', '127.0.0.1', 1784633406),
(132, 0, 'EDIT BUKU|Sistem mengubah data buku ID 13: Buku Panduan Guru : Ilmu Pengetahuan Sosial menjadi Buku ', '127.0.0.1', 1784633444),
(133, 0, 'STATUS PINJAM|Sistem mengubah status pinjam ID 30 milik fafa menjadi dikembalikan', '127.0.0.1', 1784633444),
(134, 0, 'EDIT BUKU|Sistem mengubah data buku ID 13: Buku Panduan Guru : Ilmu Pengetahuan Sosial menjadi Buku ', '127.0.0.1', 1784633444),
(135, 0, 'EDIT BUKU|Sistem mengubah data buku ID 7: Diatas Langit Masih ada Langit menjadi Diatas Langit Masih', '127.0.0.1', 1784633474),
(136, 0, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 7 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784633474),
(137, 0, 'EDIT BUKU|Sistem mengubah data buku ID 7: Diatas Langit Masih ada Langit menjadi Diatas Langit Masih', '127.0.0.1', 1784633475),
(138, 0, 'EDIT BUKU|Sistem mengubah data buku ID 7: Diatas Langit Masih ada Langit menjadi Diatas Langit Masih', '127.0.0.1', 1784633879),
(139, 0, 'STATUS PINJAM|Sistem mengubah status pinjam ID 31 milik fafa menjadi dikembalikan', '127.0.0.1', 1784633879),
(140, 0, 'EDIT BUKU|Sistem mengubah data buku ID 7: Diatas Langit Masih ada Langit menjadi Diatas Langit Masih', '127.0.0.1', 1784633879),
(141, 0, 'EDIT BUKU|Sistem mengubah data buku ID 14: Pendidikan Agama Islam dan Budi Perkerti menjadi Pendidik', '127.0.0.1', 1784633906),
(142, 0, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 14 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784633906),
(143, 0, 'EDIT BUKU|Sistem mengubah data buku ID 14: Pendidikan Agama Islam dan Budi Perkerti menjadi Pendidik', '127.0.0.1', 1784633906),
(144, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784634566),
(145, 2, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 12 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784634566),
(146, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784634566),
(147, 0, 'EDIT BUKU|Sistem mengubah data buku ID 8: Pendidikan Jasmani, Olahraga dan Kesehatan menjadi Pendidi', '127.0.0.1', 1784634893),
(148, 2, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 8 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784634893),
(149, 0, 'EDIT BUKU|Sistem mengubah data buku ID 14: Pendidikan Agama Islam dan Budi Perkerti menjadi Pendidik', '127.0.0.1', 1784634926),
(150, 2, 'STATUS PINJAM|Sistem mengubah status pinjam ID 32 milik fafa menjadi dikembalikan', '127.0.0.1', 1784634926),
(151, 0, 'EDIT BUKU|Sistem mengubah data buku ID 14: Pendidikan Agama Islam dan Budi Perkerti menjadi Pendidik', '127.0.0.1', 1784634926),
(152, 0, 'EDIT BUKU|Sistem mengubah data buku ID 13: Buku Panduan Guru : Ilmu Pengetahuan Sosial menjadi Buku ', '127.0.0.1', 1784635286),
(153, 2, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 13 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784635286),
(154, 0, 'EDIT BUKU|Sistem mengubah data buku ID 13: Buku Panduan Guru : Ilmu Pengetahuan Sosial menjadi Buku ', '127.0.0.1', 1784635286),
(155, 0, 'EDIT BUKU|Sistem mengubah data buku ID 13: Buku Panduan Guru : Ilmu Pengetahuan Sosial menjadi Buku ', '127.0.0.1', 1784635315),
(156, 2, 'STATUS PINJAM|Sistem mengubah status pinjam ID 35 milik fafa menjadi dikembalikan', '127.0.0.1', 1784635315),
(157, 0, 'EDIT BUKU|Sistem mengubah data buku ID 13: Buku Panduan Guru : Ilmu Pengetahuan Sosial menjadi Buku ', '127.0.0.1', 1784635315),
(158, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784635316),
(159, 2, 'STATUS PINJAM|Sistem mengubah status pinjam ID 33 milik fafa menjadi dikembalikan', '127.0.0.1', 1784635316),
(160, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784635316),
(161, 0, 'EDIT BUKU|Sistem mengubah data buku ID 8: Pendidikan Jasmani, Olahraga dan Kesehatan menjadi Pendidi', '127.0.0.1', 1784635317),
(162, 2, 'STATUS PINJAM|Sistem mengubah status pinjam ID 34 milik fafa menjadi dikembalikan', '127.0.0.1', 1784635317),
(163, 0, 'EDIT BUKU|Sistem mengubah data buku ID 8: Pendidikan Jasmani, Olahraga dan Kesehatan menjadi Pendidi', '127.0.0.1', 1784635317),
(164, 0, 'EDIT BUKU|Sistem mengubah data buku ID 15: Sains : Ilmu Pengetahuan Alam SD/MI  menjadi Sains : Ilmu', '127.0.0.1', 1784635330),
(165, 2, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 15 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784635330),
(166, 0, 'EDIT BUKU|Sistem mengubah data buku ID 15: Sains : Ilmu Pengetahuan Alam SD/MI  menjadi Sains : Ilmu', '127.0.0.1', 1784635330),
(167, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784635439),
(168, 2, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 12 (Jatuh tempo: 2026-07-28)', '127.0.0.1', 1784635439),
(169, 0, 'EDIT BUKU|Sistem mengubah data buku ID 15: Sains : Ilmu Pengetahuan Alam SD/MI  menjadi Sains : Ilmu', '127.0.0.1', 1784635462),
(170, 2, 'STATUS PINJAM|Sistem mengubah status pinjam ID 36 milik fafa menjadi dikembalikan', '127.0.0.1', 1784635462),
(171, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784635464),
(172, 2, 'STATUS PINJAM|Sistem mengubah status pinjam ID 37 milik fafa menjadi dikembalikan', '127.0.0.1', 1784635464),
(173, 1, 'UBAH PENGATURAN|Admin memperbarui pengaturan sistem', '127.0.0.1', 1784685862),
(174, 1, 'UBAH PENGATURAN|Admin memperbarui pengaturan sistem', '127.0.0.1', 1784685965),
(175, 1, 'UBAH PENGATURAN|Admin memperbarui pengaturan sistem', '127.0.0.1', 1784685996),
(176, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784686064),
(177, 2, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 12 (Jatuh tempo: 2026-07-29)', '127.0.0.1', 1784686064),
(178, 1, 'UBAH PENGATURAN|Admin memperbarui pengaturan sistem', '127.0.0.1', 1784686688),
(179, 1, 'UBAH PENGATURAN|Admin memperbarui pengaturan sistem', '127.0.0.1', 1784687009),
(180, 1, 'UBAH PENGATURAN|Admin memperbarui pengaturan sistem', '127.0.0.1', 1784690213),
(181, 1, 'UBAH PENGATURAN|Admin memperbarui pengaturan sistem', '127.0.0.1', 1784690735),
(182, 1, 'UBAH PENGATURAN|Admin memperbarui pengaturan sistem', '127.0.0.1', 1784694791),
(183, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784695336),
(184, 2, 'PEMINJAMAN|Sistem mencatat fafa meminjam buku ID: 12 (Jatuh tempo: 2026-08-21)', '127.0.0.1', 1784695336),
(185, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784695371),
(186, 2, 'STATUS PINJAM|Sistem mengubah status pinjam ID 38 milik fafa menjadi dikembalikan', '127.0.0.1', 1784695371),
(187, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784695371),
(188, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784695373),
(189, 2, 'STATUS PINJAM|Sistem mengubah status pinjam ID 39 milik fafa menjadi dikembalikan', '127.0.0.1', 1784695373),
(190, 0, 'EDIT BUKU|Sistem mengubah data buku ID 12: Bahasa Indonesia menjadi Bahasa Indonesia', '127.0.0.1', 1784695373),
(191, 1, 'UBAH PENGATURAN|Admin memperbarui pengaturan sistem', '127.0.0.1', 1784695859),
(192, 1, 'UBAH PENGATURAN|Admin memperbarui pengaturan sistem', '127.0.0.1', 1784699663),
(193, 1, 'UBAH PENGATURAN|Admin memperbarui pengaturan sistem', '127.0.0.1', 1784722121),
(194, 0, 'UPDATE PROFIL|Sistem mencatat perubahan data pada pengguna ID 13: Kharisma Shofiana Azizah', '127.0.0.1', 1784736943),
(195, 0, 'UPDATE PROFIL|Sistem mencatat perubahan data pada pengguna ID 13: Kharisma Shofiana Azizah', '127.0.0.1', 1784738108),
(196, 0, 'UPDATE PROFIL|Sistem mencatat perubahan data pada pengguna ID 13: pustakawan', '127.0.0.1', 1784738474),
(197, 0, 'UPDATE PROFIL|Sistem mencatat perubahan data pada pengguna ID 2: anggota', '127.0.0.1', 1784738498),
(198, 0, 'UPDATE PROFIL|Sistem mencatat perubahan data pada pengguna ID 13: pustakawan', '127.0.0.1', 1784738510);

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id_pengaturan` int(11) NOT NULL,
  `key_pengaturan` varchar(100) NOT NULL,
  `value_pengaturan` text DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id_pengaturan`, `key_pengaturan`, `value_pengaturan`, `keterangan`, `updated_at`) VALUES
(1, 'nama_aplikasi', 'SMANTEN', 'Nama singkat aplikasi (dipakai di sidebar dashboard)', '2026-07-22 02:06:36'),
(2, 'nama_sekolah', 'SMAN 10 KOTA HARAPAN BANGSA', 'Nama sekolah/instansi (tampil di navbar & footer)', '2026-07-22 01:57:57'),
(3, 'tagline', 'Digital Library', 'Tagline di bawah nama aplikasi (sidebar dashboard)', '2026-07-22 01:57:57'),
(4, 'deskripsi_beranda', 'Temukan berbagai koleksi buku terbaik untuk menunjang pembelajaran dan wawasanmu di perpustakaan digital kami.', 'Deskripsi di halaman beranda', '2026-07-22 01:57:57'),
(5, 'alamat', 'JL.Harapan Bangsa no 10 Kota Tensura Sherita', 'Alamat sekolah/perpustakaan', '2026-07-22 02:04:22'),
(6, 'telepon', '081234567890', 'Nomor telepon kontak', '2026-07-22 02:04:22'),
(7, 'email_kontak', 'perpus@sman10khb.sch.id', 'Email kontak perpustakaan', '2026-07-22 02:04:22'),
(8, 'logo', 'logo.png', 'Nama file logo di folder img/logo/', '2026-07-22 01:57:57'),
(9, 'jumlah_buku_per_halaman', '10', 'Jumlah buku ditampilkan per halaman di katalog', '2026-07-22 02:23:29'),
(10, 'lama_pinjam_hari', '30', 'Batas lama peminjaman buku (hari) sebelum jatuh tempo', '2026-07-22 02:04:22'),
(11, 'maks_pinjam_buku', '3', 'Maksimal jumlah buku yang boleh dipinjam bersamaan per anggota', '2026-07-22 01:57:57'),
(100, 'maintenance_peminjaman', '0', NULL, '2026-07-22 12:08:41'),
(101, 'maintenance_berita', '0', NULL, '2026-07-22 05:54:23'),
(102, 'maintenance_katalog', '0', NULL, '2026-07-22 12:08:41');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `no_anggota` varchar(25) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `nama` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `email` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `role` enum('admin','anggota','pustakawan','') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `status` enum('aktif','nonaktif','','') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `no_anggota`, `foto`, `nama`, `email`, `password`, `role`, `status`, `created_at`) VALUES
(1, '123', '', 'admin', 'admin@gmail.com', 'admin', 'admin', 'aktif', '2026-07-22 17:00:30'),
(2, '1234', NULL, 'anggota', 'anggota@gmail.com', 'anggota', 'anggota', 'aktif', '2026-07-22 17:00:07'),
(3, '12345', '', 'pustakawan', 'pustakawan@gmail.com', 'pustakawan', 'pustakawan', 'aktif', '2026-07-22 17:00:21');

--
-- Triggers `pengguna`
--
DELIMITER $$
CREATE TRIGGER `trg_pengguna_after_update` AFTER UPDATE ON `pengguna` FOR EACH ROW BEGIN
    -- Mencatat log hanya jika ada perubahan pada nama, email, status, atau password
    IF OLD.nama != NEW.nama OR OLD.email != NEW.email OR OLD.status != NEW.status OR OLD.password != NEW.password THEN
        INSERT INTO log_aktivitas (id_pengguna, aktivitas, ip_address, created_at)
        VALUES (0, CONCAT('UPDATE PROFIL|Sistem mencatat perubahan data pada pengguna ID ', NEW.id_pengguna, ': ', NEW.nama), IFNULL(@ip_address, '127.0.0.1'), UNIX_TIMESTAMP());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman_dan_berita`
--

CREATE TABLE `pengumuman_dan_berita` (
  `id_pengumuman` int(11) NOT NULL,
  `sampul_berita` varchar(255) DEFAULT 'default_berita.jpg',
  `judul` varchar(255) NOT NULL,
  `kategori` enum('Berita','Pengumuman') NOT NULL DEFAULT 'Berita',
  `isi` text NOT NULL,
  `berita` varchar(255) DEFAULT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengumuman_dan_berita`
--

INSERT INTO `pengumuman_dan_berita` (`id_pengumuman`, `sampul_berita`, `judul`, `kategori`, `isi`, `berita`, `tanggal_dibuat`) VALUES
(2, 'default_berita.jpg', 'Jadwal Maintenece', 'Pengumuman', 'Sistem peminjaman pengembalian E-Perpus tidak bisa digunakan dalam maktu yang akan di beritahukan kembali, untuk meminjam dan mengembalikan langsung datang ke perpustakan secara langsung\r\n\r\n', NULL, '2026-07-21 02:10:09'),
(3, '1784556381_Screenshot 2026-07-20 210553.png', 'Megahnya Penutupan Asrama UIN Malang Tahun 2022/2023', 'Berita', 'Kota Malang – 1 Mei 2023 Universitas Islam Negeri Maulana Malik Ibrahim Malang (UIN Malang) mengadakan acara penutupan asrama yang spektakuler di Gedung Sport Center.\r\n\r\nSebanyak 11 asrama yang berada di Pusat Mahad Sunan Ampel Al-Aly bersatu mengikuti gelaran penutupan asrama yang diberi nama “Muwadaah 2022-2023”. Festival penutupan ini diadakan mulai dari jam 19.00 hingga jam 03.00 pagi.\r\n\r\nMenciptakan suasana yang begitu memukau, tahun ini menjadi tahun di mana Festival Muwadaah 2022-2023 diselenggarakan secara offline dan memberikan kesempatan bagi seluruh mahasiswa untuk merasakan atmosfer yang berbeda dari tahun-tahun sebelumnya.\r\n\r\nPenampilan pertama pada festival Muwadaah 2022-2023 diisi oleh penampilan banjari sholawat di iringi alat musik tradisional yang memukau hati penonton. \r\n\r\nKemudian, penampilan kedua menghadirkan qiro’ah pembacaan ayat suci Al-Quran, dan penampilan ketiga dibawakan oleh paduan suara yang membawakan lagu Indonesia Raya dan mars MSAA (Mahad Sunan Ampel Al-Aly), untuk menyatukan jiwa seluruh mahasantri yang hadir di Gedung Sport Center.\r\n\r\nSebelum penampilan yang memukau di festival Muwadaah 2022-2023, acara diisi dengan pengumuman mahasantri teladan dan sambutan dari ketua panitia Muwadaah 2022-2023 Dr. Ahmad Izzanudin M.Hi, serta ketua Mahad Sunan Ampel Al-Aly yaitu Dr. KH Badruddin Muhammad M.Hi.\r\n\r\nSelain itu, rektor UIN Malang Prof. Dr. HM. Zainuddin MA, juga memberikan sambutannya yang menginspirasi bagi mahasantri, serta tak luput dari pembacaan doa sebelum memulai acara muwadaah 2022-2023 yang di bacakan oleh Drs. KH Chamzawi, M.Hi.\r\n\r\nSetelah sambutan dari Rektor UIN Malang dan pengurus MSAA serta ketua panitia, penampilan selanjutnya tidak kalah memukau dari penampilan seperti, pertunjukan tari saman, band melayu, drama dan masih banyak lagi pertunjukan lainnya yang tidak kalah begitu keren.\r\n\r\nEuforia pada festival muwadaah kali ini sangat mengkagumkan dibandingkan dengan muadaah tahun lalu karena muadaah kali ini di selengaraakan secara offline setelah pandemi melanda Indonesia dua tahun lalu.\r\n\r\nMelalui dari penutupan mahad Sunan Ampel Al-Aly ini, UIN Malang telah menciptakan platform yang memungkinkan mahasiswa untuk menunjukkan bakat dan potensi terbaik mereka. Acara ini juga menjadi ajang silaturahmi dan mempererat hubungan.\r\n\r\nPenutupan yang meriah dan penuh semangat, muwadaah UIN Malang tahun 2022/2023 berhasil meninggalkan kesan yang tak terlupakan.\r\n\r\nSemoga prestasi dan semangat mahasiswa UIN Malang terus berkembang di masa depan dan tidak pernah pudar.', NULL, '2026-07-20 14:06:21'),
(4, 'default_berita.jpg', 'Agenda Seminal cara meminjam di Perpustakaan SMANTEN', 'Pengumuman', 'Bagi para seluruh siswa ya kelas 10 sampai kelas 12 diwajibkan datang pukul 07.00 di ruang aula SMANTEN karena ada ABSEN ketika datang masuk', NULL, '2026-07-21 02:24:16'),
(5, 'default_berita.jpg', 'Pengumumann Agenda Siminar Literasi Siswa', 'Pengumuman', 'diharapkan bagi mahasiswa untuk datang tepat waktu\r\nHari : Selasa\r\nHari/TGL : 25 Juli 2026\r\nJam : 07.00', NULL, '2026-07-21 02:36:25'),
(6, 'default_berita.jpg', 'Pengumuman peruahan jadwal pengengembalian ', 'Pengumuman', 'Bagi para siswa ataupun staf dan guru SMAN 10 Harapan Bangsa untuk pengembalian buku perpustakaan akan bertambah menjadi sampai kenaikan kelas', NULL, '2026-07-21 02:38:30'),
(7, 'default_berita.jpg', 'Pengumuman Pengembangan sistem Perpustakaan', 'Pengumuman', 'Bagi Siswa dan guru ataupun Staf SMAN 10 Harapan Bangsa akan ada pengembengan Sistem E-Perpus bagi guru, staf maupun siswa bila ada terkendala harap melapor ke ruangan perpustakaan atau kenomer yang tertera pada sistem', NULL, '2026-07-21 02:41:00'),
(8, 'default_berita.jpg', 'Pengumuman peberitahuan Mobil Literasi', 'Pengumuman', 'Pada tanggal 15 oktober 2026 Mobil Literasi Akan ber kunjung ke SMAN 10 Harapan Bangsa bagi siswa yang akan membaca dimobil literasi diharap tertib dan mendengarkan arahan dengan baik', NULL, '2026-07-21 02:43:57'),
(9, '1784602657_Gemini_Generated_Image_i0gd61i0gd61i0gd.png', 'Perpustakaan Sekolah SMA Negeri 1 Cikarang Utara Sebagai Pusat Sumber Belajar ', 'Berita', 'Perpustakaan sekolah adalah perpustakaan yang ada atau diselenggarakan di sekolah, baik itu sekolah dasar, sekolah menengah pertama, sekolah menengah atas, sampai sekolah lanjutan, seperti perguruan tinggi. Perpustakaan sekolah berguna untuk menunjang proses belajar, baik itu siswa yang berada di sekolah dasar atau sekolah lanjutan. Buku yang ada di dalam perpustakaan sekolah sebagian besar adalah buku yang terdiri dari berbagai koleksi buku pelajaran atau buku bacaan yang mana dapat menunjang proses pembelajaran, salah satu nya adalah di SMA NEGERI 1 CIKARANG UTARA.   \r\n\r\nPerpustakaan sekolah di SMA NEGERI CIKARANG UTARA yang sebelumnya terletak di samping kelas 12 mipa satu,kini perpustakaan dibangun baru pada tahun 2023 yang sekarang berada di tengah gedung kelas 12. Perpustakaan sekolah SMAN 1 CIKARANG UTARA juga sebagai Tempat kumpulan berbagai macam buku ilmu pengetahuan sebagai sumber belajar bagi semua warga sekolah, Tempat siswa mencari berbagai macam informasi untuk melengkapi, memperjelas, untuk mengingatkan kembali, dan memperkaya ilmu pengetahuan dan wawasannya.      Adanya perpustakaan sekolah di SMA NEGERI 1 CIKARANG UTARA bukan tanpa alasan.\r\n\r\nAda tujuan dan juga manfaat dari adanya perpustakaan mengacu pada maksud dibuatnya perpustakaan sekolah. penyelenggaraa perpustakaan sekolah di SMA NEGERI 1 CIKARAMG UTARA bukan hanya untuk mengumpulkan dan menyimpan bahan-bahan pustaka, tetapi dengan adanya perpustakaan sekolah, diharapkan dapat membantu siswa dan guru menyelesaikan tugas-tugas dalam proses belajar mengajar.\r\nDidirikannya atau penyelenggaraan perpustakaan sekolah bukan hanya untuk Mengumpulkan dan juga menyimpan berbagai bahan pustaka, akan tetapi diharapkan perpustakaan tersebut dapat membantu siswa dan guru menyelesaikan berbagai tugas dalam proses belajar mengajar.\r\n\r\nOleh sebab itu, berbagai bahan pustaka yang dimiliki di perpustakaan sekolah harus lengkap untuk menunjang proses belajar mengajar, agar tujuannya tercapai, yaitu menunjang proses belajar mengajar. Artinya, selain menyediakan buku yang menunjang proses belajar mengajar,harus menyediakan buku yang memang sesuai dengan selera siswa dan guru agar perpustakaan menjadi tempat yang nyaman untuk mencari informasi dan pengetahuan lainnya.      \r\n\r\nPerpustakaan sekolah di SMA NEGERI 1 CIKARANG UTARA juga memiliki tata tertib yng berlaku yaitu dilarang makan dan minum di perpustakaan, setiap buku yang dipinjam harus sesuai izin, mengembalikan buku pada tempat yang sudah di tentukan. Perpustakaan sekolah di SMAN 1 Cikarang Utara juga menyediakan berbagai jenis sumber daya, termasuk buku fiksi dan nonfiksi, majalah, koran, dan sumber daya digital. Perpustakaan sekolah di SMAN 1 Cikarang Utara merupakan sumber daya yang sangat berharga bagi siswa dan guru. Dengan menyediakan akses ke berbagai jenis sumber daya dan mendukung proses belajar mengajar, perpustakaan sekolah dapat membantu siswa mencapai potensi mereka secara maksimal.', NULL, '2026-07-21 09:21:52'),
(10, '1784603103_Screenshot 2026-07-21 100429.png', 'Perpustakaan SMAN 10 Harapan Bangsa', 'Berita', 'Setiap sekolah pasti memiliki banyak fasilitas yang disediakan oleh sekolah untuk para siswa dan siswi untuk mendukung kegiatan belajar dan mengajar di sekolah. Salah satunya adalah perpustakaan. \r\n\r\nKini perpustakaan SMAN 1 Cikarang Utara sudah jauh lebih baik daripada sebelumnya, mulai dari bangunannya sudah memiliki 2 lantai, tempatnya yang luas, dan sudah di pasangkan pendingin ruangan seperti AC. Di lantai 1, di khususkan untuk meja registrasi bagi siswa/i yang ingin meminjam buku/ ruang perpustakaan, lalu tersedia toilet, koran dan majalah, sofa tamu, dan komputer.\r\n\r\nSelanjutnya, di lantai 2 di khususkan untuk rak-rak yang berisikan buku-buku. Mulai dari buku paket pelajaran, novel, komik, buku ensiklopedia, kamus dan juga sudaj menyediakan buku untuk belajar UTBK yang bisa di pinjam oleh siswa/i SMAN 1 Cikarang Utara. ', NULL, '2026-07-21 03:05:03'),
(11, '1784623868_Gemini_Generated_Image_gjso4ugjso4ugjso.png', 'Transformasi Digital dan Modernisasi: Perpustakaan SMAN 10 Harapan Bangsa Jadi Pusat belajar Terpadu', 'Berita', 'HARAPAN BANGSA — Perpustakaan SMAN 10 Harapan Bangsa kini resmi hadir dengan wajah baru yang jauh lebih modern, estetik, dan berorientasi pada teknologi. Langkah peremajaan fasilitas ini dilakukan sebagai upaya nyata sekolah dalam meningkatkan minat baca serta mendukung kegiatan belajar-mengajar yang lebih interaktif bagi seluruh warga sekolah. Kesan kaku dan monoton kini digantikan oleh desain interior bernuansa kayu hangat, pencahayaan yang terang, serta tata ruang fleksibel yang langsung menarik perhatian dan menjadi magnet baru bagi para siswa.\r\n\r\nUntuk menunjang kenyamanan secara maksimal, perpustakaan ini telah dilengkapi dengan berbagai fasilitas unggulan. Para siswa dapat memanfaatkan zona baca yang adaptif, mulai dari area meja diskusi kelompok hingga sudut lesehan santai yang dilengkapi bean bag. Ribuan koleksi literatur yang mencakup ilmu sains, sejarah, fiksi remaja, hingga ensiklopedia kini tertata rapi sesuai standar literasi modern. Tidak hanya itu, hadir pula pojok literasi digital berkecepatan tinggi untuk mengakses jurnal daring dan e-book, serta penerapan sistem peminjaman mandiri berbasis barcode yang makin mempermudah proses administrasi.\r\n\r\nPerubahan konsep ini terbukti memberikan dampak yang sangat positif terhadap antusiasme belajar para siswa di sekolah. Kepala Perpustakaan SMAN 10 Harapan Bangsa menyampaikan bahwa perombakan ini bertujuan menjadikan perpustakaan sebagai pusat lahirnya gagasan kreatif sekaligus jantung aktivitas akademis sekolah. Sejak ruang baru ini difungsikan, angka kunjungan siswa melonjak drastis, baik pada jam istirahat maupun usai jam pelajaran. Kehadiran fasilitas yang ramah dan edukatif ini diharapkan dapat terus memupuk budaya literasi serta kemampuan berpikir kritis bagi seluruh siswa SMAN 10 Harapan Bangsa.', NULL, '2026-07-21 08:51:08');

-- --------------------------------------------------------

--
-- Table structure for table `pinjam`
--

CREATE TABLE `pinjam` (
  `id_pinjam` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_buku` int(11) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `status` enum('dipinjam','dikembalikan','','') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `status_pengajuan` varchar(50) DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pinjam`
--

INSERT INTO `pinjam` (`id_pinjam`, `id_pengguna`, `id_buku`, `tanggal_pinjam`, `tanggal_kembali`, `status`, `status_pengajuan`) VALUES
(19, 2, 7, '2026-07-20', '2026-07-27', 'dikembalikan', 'aktif'),
(20, 2, 7, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(21, 2, 12, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(22, 2, 16, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(23, 2, 8, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(24, 2, 14, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(25, 1, 12, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(26, 2, 12, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(27, 2, 12, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(28, 2, 14, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(29, 2, 9, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(30, 2, 13, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(31, 2, 7, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(32, 2, 14, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(33, 2, 12, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(34, 2, 8, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(35, 2, 13, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(36, 2, 15, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(37, 2, 12, '2026-07-21', '2026-07-28', 'dikembalikan', 'selesai'),
(38, 2, 12, '2026-07-22', '2026-07-29', 'dikembalikan', 'selesai'),
(39, 2, 12, '2026-07-22', '2026-08-21', 'dikembalikan', 'selesai');

--
-- Triggers `pinjam`
--
DELIMITER $$
CREATE TRIGGER `trg_pinjam_after_insert` AFTER INSERT ON `pinjam` FOR EACH ROW BEGIN
    DECLARE nama_user VARCHAR(100);

    -- A. Mengurangi stok buku jika status langsung 'dipinjam'
    IF NEW.status = 'dipinjam' THEN
        UPDATE buku SET jumlah_tersedia = jumlah_tersedia - 1 WHERE id_buku = NEW.id_buku;
    END IF;

    -- B. Mencatat log aktivitas dengan mengambil nama pengguna
    SELECT nama INTO nama_user FROM pengguna WHERE id_pengguna = NEW.id_pengguna;
    
    INSERT INTO log_aktivitas (id_pengguna, aktivitas, ip_address, created_at)
    VALUES (NEW.id_pengguna, CONCAT('PEMINJAMAN|Sistem mencatat ', IFNULL(nama_user, 'Pengguna'), ' meminjam buku ID: ', NEW.id_buku, ' (Jatuh tempo: ', NEW.tanggal_kembali, ')'), IFNULL(@ip_address, '127.0.0.1'), UNIX_TIMESTAMP());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_pinjam_after_update` AFTER UPDATE ON `pinjam` FOR EACH ROW BEGIN
    DECLARE nama_user VARCHAR(100);

    -- A. Menambah stok buku kembali jika status diubah jadi 'dikembalikan'
    IF OLD.status = 'dipinjam' AND NEW.status = 'dikembalikan' THEN
        UPDATE buku SET jumlah_tersedia = jumlah_tersedia + 1 WHERE id_buku = NEW.id_buku;
    END IF;

    -- B. Mencatat log aktivitas jika status berubah
    IF OLD.status != NEW.status THEN
        SELECT nama INTO nama_user FROM pengguna WHERE id_pengguna = NEW.id_pengguna;
        
        INSERT INTO log_aktivitas (id_pengguna, aktivitas, ip_address, created_at)
        VALUES (NEW.id_pengguna, CONCAT('STATUS PINJAM|Sistem mengubah status pinjam ID ', NEW.id_pinjam, ' milik ', IFNULL(nama_user, 'Pengguna'), ' menjadi ', NEW.status), IFNULL(@ip_address, '127.0.0.1'), UNIX_TIMESTAMP());
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id_buku`);

--
-- Indexes for table `galeri_prestasi`
--
ALTER TABLE `galeri_prestasi`
  ADD PRIMARY KEY (`id_prestasi`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id_log`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id_pengaturan`),
  ADD UNIQUE KEY `key_pengaturan` (`key_pengaturan`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`);

--
-- Indexes for table `pengumuman_dan_berita`
--
ALTER TABLE `pengumuman_dan_berita`
  ADD PRIMARY KEY (`id_pengumuman`);

--
-- Indexes for table `pinjam`
--
ALTER TABLE `pinjam`
  ADD PRIMARY KEY (`id_pinjam`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id_buku` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `galeri_prestasi`
--
ALTER TABLE `galeri_prestasi`
  MODIFY `id_prestasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id_pengaturan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pengumuman_dan_berita`
--
ALTER TABLE `pengumuman_dan_berita`
  MODIFY `id_pengumuman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pinjam`
--
ALTER TABLE `pinjam`
  MODIFY `id_pinjam` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
