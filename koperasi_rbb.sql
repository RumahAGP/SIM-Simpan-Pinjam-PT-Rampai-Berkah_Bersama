-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2025 at 11:30 AM
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
-- Database: `koperasi_rbb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `hashed_password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `hashed_password`) VALUES
(1, 'tes', 'tes'),
(2, 'admin', 'ac9689e2272427085e35b9d3e3e8bed88cb3434828b43b86fc0596cad4c6e270');

-- --------------------------------------------------------

--
-- Table structure for table `angsuran`
--

CREATE TABLE `angsuran` (
  `id_angsuran` int(11) NOT NULL,
  `id_pinjaman` int(11) NOT NULL,
  `id_metode_pembayaran` int(11) DEFAULT NULL,
  `angsuran_ke` int(11) NOT NULL,
  `nominal_angsuran` int(9) NOT NULL,
  `tgl_pembayaran` datetime DEFAULT NULL,
  `status` enum('LUNAS','BELUM LUNAS') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `angsuran`
--

INSERT INTO `angsuran` (`id_angsuran`, `id_pinjaman`, `id_metode_pembayaran`, `angsuran_ke`, `nominal_angsuran`, `tgl_pembayaran`, `status`) VALUES
(41, 1, NULL, 1, 16666, '2025-06-28 01:17:32', 'BELUM LUNAS'),
(42, 1, NULL, 2, 16666, '2025-06-28 01:17:32', 'BELUM LUNAS'),
(43, 1, NULL, 3, 16666, '2025-06-28 01:17:32', 'BELUM LUNAS'),
(44, 1, NULL, 4, 16666, '2025-06-28 01:17:32', 'BELUM LUNAS'),
(45, 1, NULL, 5, 16666, '2025-06-28 01:17:32', 'BELUM LUNAS'),
(46, 1, NULL, 6, 16666, '2025-06-28 01:17:32', 'BELUM LUNAS'),
(47, 1, NULL, 7, 16666, '2025-06-28 01:17:32', 'BELUM LUNAS'),
(48, 1, NULL, 8, 16666, '2025-06-28 01:17:32', 'BELUM LUNAS'),
(49, 1, NULL, 9, 16666, '2025-06-28 01:17:32', 'BELUM LUNAS'),
(50, 1, NULL, 10, 16666, '2025-06-28 01:17:32', 'BELUM LUNAS'),
(51, 1, NULL, 11, 16666, '2025-06-28 01:17:32', 'BELUM LUNAS'),
(52, 1, NULL, 12, 16666, '2025-06-28 01:17:32', 'BELUM LUNAS'),
(53, 4, 4, 1, 16666, '2025-06-30 00:00:00', 'LUNAS'),
(54, 4, 4, 2, 16666, '2025-11-14 00:00:00', 'LUNAS'),
(55, 4, NULL, 3, 16666, NULL, 'BELUM LUNAS'),
(56, 6, NULL, 1, 13000, NULL, 'BELUM LUNAS'),
(57, 6, NULL, 2, 13000, NULL, 'BELUM LUNAS'),
(58, 6, NULL, 3, 13000, NULL, 'BELUM LUNAS'),
(59, 6, NULL, 4, 13000, NULL, 'BELUM LUNAS'),
(60, 6, NULL, 5, 13000, NULL, 'BELUM LUNAS'),
(61, 6, NULL, 6, 13000, NULL, 'BELUM LUNAS'),
(62, 6, NULL, 7, 13000, NULL, 'BELUM LUNAS'),
(63, 6, NULL, 8, 9000, NULL, 'BELUM LUNAS'),
(70, 6, 4, 9, 12500, '2025-11-22 00:00:00', 'LUNAS'),
(71, 6, 4, 10, 12500, '2025-11-22 00:00:00', 'LUNAS'),
(72, 1, 4, 13, 16667, '2025-11-23 00:00:00', 'LUNAS'),
(73, 1, 4, 14, 16667, '2025-11-23 00:00:00', 'LUNAS'),
(74, 10, 4, 1, 89478485, '2025-11-24 00:00:00', 'LUNAS'),
(80, 10, 4, 2, 89478485, '2025-11-26 00:00:00', 'LUNAS'),
(81, 8, 4, 1, 174444, '2025-11-26 00:00:00', 'LUNAS'),
(82, 10, 4, 3, 89478485, '2025-11-26 00:00:00', 'LUNAS');

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `id_jabatan` int(11) NOT NULL,
  `nama_jabatan` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`id_jabatan`, `nama_jabatan`) VALUES
(1, 'HRD'),
(3, 'Karyawan'),
(2, 'Supervisor');

-- --------------------------------------------------------

--
-- Table structure for table `metode_pembayaran`
--

CREATE TABLE `metode_pembayaran` (
  `id_metode_pembayaran` int(11) NOT NULL,
  `nama_metode_pembayaran` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `metode_pembayaran`
--

INSERT INTO `metode_pembayaran` (`id_metode_pembayaran`, `nama_metode_pembayaran`) VALUES
(4, 'tunai');

-- --------------------------------------------------------

--
-- Table structure for table `nasabah`
--

CREATE TABLE `nasabah` (
  `id_nasabah` int(11) NOT NULL,
  `id_jabatan` int(11) DEFAULT NULL,
  `nama_lengkap` varchar(50) NOT NULL,
  `username` varchar(20) NOT NULL,
  `hashed_password` varchar(100) NOT NULL,
  `status` enum('AKTIF','SUSPEND') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `nasabah`
--

INSERT INTO `nasabah` (`id_nasabah`, `id_jabatan`, `nama_lengkap`, `username`, `hashed_password`, `status`) VALUES
(1, 3, 'adrian', 'adrian', '123', 'AKTIF'),
(2, 2, 'adrian lagi', 'adrian2', '65e84be33532fb784c48129675f9eff3a682b27168c0ea744b2cf58ee02337c5', 'AKTIF'),
(3, 3, 'siapa ya', 'siapaya', '52c62ec0d3a933be0e9f8f4fa7f3dae1c383bf05b0d068db8a63ab71a387b886', 'AKTIF'),
(4, 1, 'galih', 'galih', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'SUSPEND'),
(5, 1, 'daffa', 'daffa', '123', 'AKTIF'),
(6, 2, 'aih', 'testing', '123', 'AKTIF'),
(7, 1, 'nasabah', 'nasabah', '123', 'AKTIF');

-- --------------------------------------------------------

--
-- Table structure for table `pinjaman`
--

CREATE TABLE `pinjaman` (
  `id_pinjaman` int(11) NOT NULL,
  `id_nasabah` int(11) NOT NULL,
  `nominal_pinjaman` int(9) NOT NULL,
  `tenor` int(3) NOT NULL,
  `alasan_pengajuan` text DEFAULT NULL,
  `tgl_pengajuan` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `pinjaman`
--

INSERT INTO `pinjaman` (`id_pinjaman`, `id_nasabah`, `nominal_pinjaman`, `tenor`, `alasan_pengajuan`, `tgl_pengajuan`) VALUES
(1, 1, 200000, 12, 'butuh duit', '2025-06-27 09:14:13'),
(2, 2, 10000000, 4, ' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ligula eros, maximus ac vestibulum sed, rutrum rhoncus arcu. Etiam convallis euismod eros. In vulputate nisl felis, sit amet tempus nulla gravida eu. Fusce in purus a justo pretium congue. Sed id enim id justo pharetra mollis. Nullam ullamcorper elit vitae tortor efficitur gravida. Donec sed malesuada lacus. In tincidunt porta scelerisque. Nam ullamcorper ipsum eu urna sodales efficitur.\r\n\r\nInterdum et malesuada fames ac ante ipsum primis in faucibus. Nam sagittis ut urna a faucibus. Aliquam euismod lacus sed rhoncus mollis. Nullam blandit maximus maximus. Suspendisse potenti. Morbi sit amet semper tellus. Sed scelerisque sem id lacus rhoncus, sed facilisis leo egestas. Sed tempor nulla non arcu semper cursus id at lacus.\r\n\r\n', '2025-06-27 10:23:39'),
(3, 1, 9999999, 12, 'tes', '2025-06-27 14:20:48'),
(4, 4, 50000, 3, 'yeyy', '2025-06-30 17:39:43'),
(5, 4, 10000, 6, 'test', '2025-06-30 23:05:35'),
(6, 5, 100000, 8, 'berak', '2025-11-14 16:37:34'),
(7, 5, 6776746, 24, 'hyyh', '2025-11-22 20:40:40'),
(8, 5, 1221111, 7, 'ssdda', '2025-11-22 22:22:31'),
(9, 5, 453400, 12, 'baru', '2025-11-22 22:50:07'),
(10, 1, 2147483647, 24, 'bodoamat', '2025-11-24 11:06:56'),
(11, 5, 2147483647, 24, 'bro\r\n', '2025-11-29 14:21:33');

-- --------------------------------------------------------

--
-- Table structure for table `simpanan`
--

CREATE TABLE `simpanan` (
  `id_simpanan` int(11) NOT NULL,
  `id_nasabah` int(11) NOT NULL,
  `nominal_simpanan` int(9) DEFAULT NULL,
  `tgl_uang_masuk` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `simpanan`
--

INSERT INTO `simpanan` (`id_simpanan`, `id_nasabah`, `nominal_simpanan`, `tgl_uang_masuk`) VALUES
(1, 1, 100000, '2025-06-26 00:00:00'),
(2, 2, 83000, '2025-06-27 14:13:16'),
(3, 1, 100000, '2025-06-28 00:00:00'),
(4, 2, 1000000, '2025-06-11 00:00:00'),
(5, 3, 1000000, '2025-06-28 00:00:00'),
(6, 4, 100000, '2025-06-29 00:00:00'),
(7, 5, 10000, '2025-11-14 00:00:00'),
(8, 6, 10000, '2025-11-22 00:00:00'),
(9, 5, 5000, '2025-11-22 00:00:00'),
(10, 6, 10000, '2025-11-22 00:00:00'),
(11, 5, 10000, '2025-11-22 00:00:00'),
(14, 5, 30203230, '2025-11-23 00:00:00'),
(15, 1, 2147483647, '2025-11-24 00:00:00'),
(16, 3, 454646566, '2025-11-26 00:00:00'),
(17, 5, 45455, '2025-11-26 00:00:00'),
(18, 5, 345453454, '2025-11-26 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `status_pinjaman`
--

CREATE TABLE `status_pinjaman` (
  `id_status_pinjaman` int(11) NOT NULL,
  `id_pinjaman` int(11) NOT NULL,
  `tgl_status` datetime DEFAULT current_timestamp(),
  `status` enum('LUNAS','DISETUJUI','DITOLAK','MENUNGGU') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `status_pinjaman`
--

INSERT INTO `status_pinjaman` (`id_status_pinjaman`, `id_pinjaman`, `tgl_status`, `status`) VALUES
(1, 1, '2025-06-27 09:18:15', 'DISETUJUI'),
(2, 2, '2025-06-27 10:23:53', 'DITOLAK'),
(3, 3, '2025-06-27 14:22:24', 'DITOLAK'),
(4, 4, '2025-06-30 17:39:43', 'DISETUJUI'),
(5, 5, '2025-11-22 19:31:34', 'DISETUJUI'),
(6, 6, '2025-11-14 16:37:34', 'DISETUJUI'),
(7, 7, '2025-11-29 17:03:16', 'DITOLAK'),
(8, 8, '2025-11-26 15:49:30', 'DISETUJUI'),
(9, 9, '2025-11-22 22:50:07', 'MENUNGGU'),
(10, 10, '2025-11-24 11:09:41', 'DISETUJUI'),
(11, 11, '2025-11-29 14:44:39', 'DITOLAK');

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
-- Indexes for table `angsuran`
--
ALTER TABLE `angsuran`
  ADD PRIMARY KEY (`id_angsuran`),
  ADD KEY `id_pinjaman` (`id_pinjaman`),
  ADD KEY `id_metode_pembayaran` (`id_metode_pembayaran`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`id_jabatan`),
  ADD UNIQUE KEY `nama_jabatan` (`nama_jabatan`);

--
-- Indexes for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  ADD PRIMARY KEY (`id_metode_pembayaran`),
  ADD UNIQUE KEY `nama_metode_pembayaran` (`nama_metode_pembayaran`);

--
-- Indexes for table `nasabah`
--
ALTER TABLE `nasabah`
  ADD PRIMARY KEY (`id_nasabah`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_jabatan` (`id_jabatan`);

--
-- Indexes for table `pinjaman`
--
ALTER TABLE `pinjaman`
  ADD PRIMARY KEY (`id_pinjaman`),
  ADD KEY `id_nasabah` (`id_nasabah`);

--
-- Indexes for table `simpanan`
--
ALTER TABLE `simpanan`
  ADD PRIMARY KEY (`id_simpanan`),
  ADD KEY `id_nasabah` (`id_nasabah`);

--
-- Indexes for table `status_pinjaman`
--
ALTER TABLE `status_pinjaman`
  ADD PRIMARY KEY (`id_status_pinjaman`),
  ADD KEY `id_pinjaman` (`id_pinjaman`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `angsuran`
--
ALTER TABLE `angsuran`
  MODIFY `id_angsuran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `id_jabatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  MODIFY `id_metode_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `nasabah`
--
ALTER TABLE `nasabah`
  MODIFY `id_nasabah` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pinjaman`
--
ALTER TABLE `pinjaman`
  MODIFY `id_pinjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `simpanan`
--
ALTER TABLE `simpanan`
  MODIFY `id_simpanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `status_pinjaman`
--
ALTER TABLE `status_pinjaman`
  MODIFY `id_status_pinjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `angsuran`
--
ALTER TABLE `angsuran`
  ADD CONSTRAINT `angsuran_ibfk_1` FOREIGN KEY (`id_pinjaman`) REFERENCES `pinjaman` (`id_pinjaman`),
  ADD CONSTRAINT `angsuran_ibfk_2` FOREIGN KEY (`id_metode_pembayaran`) REFERENCES `metode_pembayaran` (`id_metode_pembayaran`);

--
-- Constraints for table `nasabah`
--
ALTER TABLE `nasabah`
  ADD CONSTRAINT `nasabah_ibfk_1` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id_jabatan`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pinjaman`
--
ALTER TABLE `pinjaman`
  ADD CONSTRAINT `pinjaman_ibfk_1` FOREIGN KEY (`id_nasabah`) REFERENCES `nasabah` (`id_nasabah`);

--
-- Constraints for table `simpanan`
--
ALTER TABLE `simpanan`
  ADD CONSTRAINT `simpanan_ibfk_1` FOREIGN KEY (`id_nasabah`) REFERENCES `nasabah` (`id_nasabah`);

--
-- Constraints for table `status_pinjaman`
--
ALTER TABLE `status_pinjaman`
  ADD CONSTRAINT `status_pinjaman_ibfk_1` FOREIGN KEY (`id_pinjaman`) REFERENCES `pinjaman` (`id_pinjaman`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
