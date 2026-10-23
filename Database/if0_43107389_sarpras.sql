-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql306.infinityfree.com
-- Generation Time: Oct 23, 2026 at 03:50 AM
-- Server version: 11.4.13-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_43107389_sarpras`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`) VALUES
(1, 'admin', '$argon2id$v=19$m=19456,t=2,p=1$mz7rSKEl8yj6escuUjbyCw$klKNNKdHqZiF4muX8yMH96N/TyF6L9krUdwUaua5Cx4'),
(2, 'malvien', '$argon2id$v=19$m=19456,t=2,p=1$PuQA0NF4MB1xrdTmlj4sLw$jkJRmMgg2Ebj/RfaKhvJCJe+BxTojIK1kInfjqPm4LM'),
(3, 'Rezka', '$argon2id$v=19$m=19456,t=2,p=1$PuQA0NF4MB1xrdTmlj4sLw$jkJRmMgg2Ebj/RfaKhvJCJe+BxTojIK1kInfjqPm4LM'),
(4, 'Resky', '$argon2id$v=19$m=19456,t=2,p=1$PuQA0NF4MB1xrdTmlj4sLw$jkJRmMgg2Ebj/RfaKhvJCJe+BxTojIK1kInfjqPm4LM'),
(5, 'hilman', '$argon2id$v=19$m=19456,t=2,p=1$PuQA0NF4MB1xrdTmlj4sLw$jkJRmMgg2Ebj/RfaKhvJCJe+BxTojIK1kInfjqPm4LM');

-- --------------------------------------------------------

--
-- Table structure for table `aspirasi`
--

CREATE TABLE `aspirasi` (
  `id_aspirasi` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `lokasi` varchar(50) NOT NULL,
  `ket` text NOT NULL,
  `bukti_siswa` varchar(100) DEFAULT NULL,
  `status` enum('Menunggu','Proses','Selesai','Ditolak') DEFAULT 'Menunggu',
  `feedback` text DEFAULT NULL,
  `bukti_admin` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `aspirasi`
--

INSERT INTO `aspirasi` (`id_aspirasi`, `id_siswa`, `id_kategori`, `id_admin`, `lokasi`, `ket`, `bukti_siswa`, `status`, `feedback`, `bukti_admin`, `created_at`) VALUES
(1, 3, 1, 5, 'sfgw', 'sgswgvs', NULL, 'Selesai', 'sudah risky', 'admin_1790131811_6ab33e63e7259.jpg', '2026-08-28 08:31:37'),
(2, 3, 2, 1, 'njkshjfis', 'sshfiwhgows', NULL, 'Selesai', 'beressss', 'admin_1788399497_6a98cf8995132.jpg', '2026-08-28 10:05:02'),
(4, 3, 2, 1, 'hfhy', 'uguip', 'siswa_1788006704_6a92d1302047a.jpg', 'Selesai', 'bbik', 'admin_1788007614_6a92d4be9f075.jpg', '2026-08-29 19:31:44'),
(5, 3, 2, 2, 'di dekat mushola', 'gaada gayung', 'siswa_1788308474_6a976bfa16364.jpg', 'Proses', 'fnasofdjoa', 'admin_1792741048_6adb0eb8c079a.png', '2026-09-02 07:21:14'),
(6, 3, 2, 1, 'diwc', 'bufbqwib cua', 'siswa_1788311489_6a9777c160a98.jpg', 'Selesai', 'berhasillll', 'admin_1788311535_6a9777ef1f495.jpg', '2026-09-02 08:11:29'),
(7, 2, 2, 2, 'di dekat mushola', 'kotor sekali', 'siswa_1788398801_6a98ccd1b62a8.jpg', 'Ditolak', 'Pengaduan ditolak oleh admin karena tidak sesuai dengan ketentuan sarana prasarana sekolah.', NULL, '2026-09-03 08:26:41'),
(11, 2, 2, 2, 'sfsc', 'vsv', NULL, 'Ditolak', 'Pengaduan ditolak oleh admin karena tidak sesuai dengan ketentuan sarana prasarana sekolah.', NULL, '2026-09-04 10:33:58'),
(12, 3, 2, 5, 'dekat kantin', 'disana bang', NULL, 'Selesai', 'sudahhh selesai yaa', 'admin_1790131763_6ab33e33c9eaa.jpg', '2026-09-08 09:28:10'),
(13, 3, 1, 5, 'dekat kantin', 'gatau kenapa', 'siswa_1788870836_6aa000b4f38bc.png', 'Selesai', 'sudah beres ya riski', 'admin_1790127652_6ab32e2463489.jpg', '2026-09-08 05:33:56'),
(14, 3, 3, 2, 'disansa', 'gatau eng kenapa ituteh', NULL, 'Selesai', 'udah beres yaa', 'admin_1788913050_6aa0a59a5b52f.png', '2026-09-08 17:14:07'),
(15, 3, 2, 2, 'DIDEKAT KELAS MALVEIN', 'HARUS DIBANTUIN SELESAIKAN', 'siswa_1789618101_6aab67b55c299.png', 'Selesai', 'sudah kami selesaikan ', 'admin_1789700496_6aaca9908be79.jpg', '2026-09-16 21:08:21'),
(16, 3, 6, 2, 'lab rpl', 'komputernya kurang', 'siswa_1790066612_6ab23fb435579.jpg', 'Selesai', 'ergreef', 'admin_1790066852_6ab240a4902bc.jpg', '2026-09-22 01:43:32'),
(17, 3, 1, 2, 'dikelas', 'ada atap yang roboh', 'siswa_1790208631_6ab46a773fa17.jpg', 'Ditolak', 'Pengaduan ditolak oleh admin karena tidak sesuai dengan ketentuan sarana prasarana sekolah.', NULL, '2026-09-23 17:10:31'),
(18, 3, 2, 5, 'mdjebcjbew', 'e cemc', 'siswa_1790208683_6ab46aab304a7.jpg', 'Selesai', 'beres ya bang', 'admin_1790254147_6ab51c43865d6.jpg', '2026-09-23 17:11:23'),
(19, 4, 1, 2, 'dekat kantin', 'adalah pokonya', 'siswa_1790735752_6abc758896d4e.jpg', 'Ditolak', 'Pengaduan ditolak oleh admin karena tidak sesuai dengan ketentuan sarana prasarana sekolah.', 'admin_1791250664_6ac450e890825.png', '2026-09-30 09:35:52'),
(20, 3, 2, 3, 'di dekat mushola', 'didida', 'siswa_1791170758_6ac318c6e4217.png', 'Ditolak', 'Pengaduan ditolak oleh admin karena tidak sesuai dengan ketentuan sarana prasarana sekolah.', 'admin_1791170800_6ac318f0813ec.png', '2026-10-05 10:25:58'),
(21, 3, 2, NULL, 'fwfwffwf', 'fwfwfw', 'siswa_1792741286_6adb0fa6c4a73.png', 'Menunggu', NULL, NULL, '2026-10-23 00:41:27');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Fasilitas Pembelajaran'),
(2, 'Fasilitas Umum'),
(3, 'Fasilitas Ibadah'),
(6, 'Fasilitas Lab');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id_siswa` int(11) NOT NULL,
  `nis` varchar(10) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `password` varchar(100) NOT NULL DEFAULT '123456'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id_siswa`, `nis`, `nama`, `kelas`, `password`) VALUES
(2, '1515151515', 'Shinta nana', 'xii rpl 2', '$argon2id$v=19$m=65536,t=4,p=1$LjYzRHVFUm5WNUFGN0tqVw$NeitY2PYCRRa8PPr4JrmJW7egEavSq+ZNOKkGw3Ek1I'),
(3, '1212121212', 'Rizky Her', 'parva rpl2', '$argon2id$v=19$m=65536,t=4,p=1$T28zZE5WLlNzcE9zOUFKUQ$xka3131MeOfZ29k9kuSNMW9xDIvCt+Q0PGdFGd29HBs'),
(4, '1122334455', 'Sandi sa', 'xii rpl 2', '$argon2id$v=19$m=65536,t=4,p=1$cGRTa2xlQXRLN21OVTZzUw$1ebhV1ZZu2U/VDCCAmQIrifrACeNpWrqgakhtds45O8'),
(13, '9247777777', 'Nita Santi', 'xii rpl 2', '$argon2id$v=19$m=65536,t=4,p=1$by81bGgzUUVmZE5vUDFEYw$H64unOeq3ugkGro+xM/J3P4RQvM7aUbit/vrLfFjhyg');

-- --------------------------------------------------------

--
-- Table structure for table `tb_history_aspirasi`
--

CREATE TABLE `tb_history_aspirasi` (
  `id_history` int(11) NOT NULL,
  `id_aspirasi` int(11) NOT NULL,
  `status_akhir` varchar(50) NOT NULL,
  `feedback_admin` text NOT NULL,
  `tanggal_arsip` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_history_aspirasi`
--

INSERT INTO `tb_history_aspirasi` (`id_history`, `id_aspirasi`, `status_akhir`, `feedback_admin`, `tanggal_arsip`) VALUES
(1, 4, 'Selesai', 'bbik', '2026-09-01 14:45:09'),
(2, 6, 'Selesai', 'berhasillll', '2026-09-02 08:12:15'),
(3, 2, 'Selesai', 'beressss', '2026-09-03 08:38:17'),
(4, 14, 'Selesai', 'udah beres yaa', '2026-09-08 17:17:38'),
(5, 15, 'Selesai', 'sudah kami selesaikan ', '2026-09-17 20:01:36'),
(6, 12, 'Selesai', 'ghuh', '2026-09-22 01:19:23'),
(7, 16, 'Selesai', 'ergreef', '2026-09-22 01:47:33'),
(8, 13, 'Selesai', 'sudah beres ya riski', '2026-09-22 18:40:52'),
(9, 1, 'Selesai', 'sudah risky', '2026-09-22 19:50:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `aspirasi`
--
ALTER TABLE `aspirasi`
  ADD PRIMARY KEY (`id_aspirasi`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id_siswa`),
  ADD UNIQUE KEY `nis` (`nis`);

--
-- Indexes for table `tb_history_aspirasi`
--
ALTER TABLE `tb_history_aspirasi`
  ADD PRIMARY KEY (`id_history`),
  ADD KEY `fk_history_aspirasi` (`id_aspirasi`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `aspirasi`
--
ALTER TABLE `aspirasi`
  MODIFY `id_aspirasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tb_history_aspirasi`
--
ALTER TABLE `tb_history_aspirasi`
  MODIFY `id_history` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aspirasi`
--
ALTER TABLE `aspirasi`
  ADD CONSTRAINT `aspirasi_ibfk_2` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `aspirasi_ibfk_3` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `aspirasi_ibfk_4` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_history_aspirasi`
--
ALTER TABLE `tb_history_aspirasi`
  ADD CONSTRAINT `fk_history_aspirasi` FOREIGN KEY (`id_aspirasi`) REFERENCES `aspirasi` (`id_aspirasi`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
