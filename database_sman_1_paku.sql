-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 03 Bulan Mei 2025 pada 09.25
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `database_sman_1_paku`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` varchar(10) NOT NULL,
  `nama_admin` varchar(255) NOT NULL,
  `username_admin` varchar(50) NOT NULL,
  `password_admin` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`, `username_admin`, `password_admin`) VALUES
('ADM001', 'Admin Utama', 'admin1', 'admin123'),
('ADM002', 'Admin Kedua', 'admin2', 'admin456');

-- --------------------------------------------------------

--
-- Struktur dari tabel `akses_materi`
--

CREATE TABLE `akses_materi` (
  `id_siswa` varchar(10) NOT NULL,
  `id_materi` varchar(10) NOT NULL,
  `tgl_akses` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `akses_materi`
--

INSERT INTO `akses_materi` (`id_siswa`, `id_materi`, `tgl_akses`) VALUES
('S001', 'MTR001', '2025-03-17 18:28:13'),
('S001', 'MTR003', '2025-05-03 04:12:37'),
('S001', 'MTR003', '2025-05-03 04:14:02'),
('S001', 'MTR002', '2025-05-03 04:14:22'),
('S001', 'MTR001', '2025-05-03 04:14:26'),
('S001', 'MTR001', '2025-05-03 04:14:48'),
('S001', 'MTR001', '2025-05-03 04:15:55'),
('S001', 'MTR001', '2025-05-03 04:16:40'),
('S001', 'MTR002', '2025-05-03 04:16:48'),
('S001', 'MTR002', '2025-05-03 04:17:48'),
('S001', 'MTR002', '2025-05-03 04:18:32'),
('S001', 'MTR001', '2025-05-03 04:19:30'),
('S001', 'MTR001', '2025-05-03 04:20:26'),
('S001', 'MTR001', '2025-05-03 04:21:24'),
('S001', 'MTR002', '2025-05-03 04:21:29'),
('S001', 'MTR002', '2025-05-03 04:22:38'),
('S001', 'MTR001', '2025-05-03 04:23:01'),
('S001', 'MTR001', '2025-05-03 04:23:13'),
('S001', 'MTR001', '2025-05-03 04:24:23'),
('S001', 'MTR001', '2025-05-03 04:25:29'),
('S001', 'MTR001', '2025-05-03 04:27:43'),
('S001', 'MTR001', '2025-05-03 04:28:22'),
('S001', 'MTR001', '2025-05-03 04:30:41'),
('S001', 'MTR013', '2025-05-03 05:11:28'),
('S002', 'MTR005', '2025-05-03 13:23:02'),
('S002', 'MTR013', '2025-05-03 13:24:03'),
('S001', 'MTR014', '2025-05-03 13:46:31'),
('S001', 'MTR014', '2025-05-03 14:18:33'),
('S001', 'MTR015', '2025-05-03 14:18:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `guru`
--

CREATE TABLE `guru` (
  `id_guru` varchar(10) NOT NULL CHECK (`id_guru` like 'G%'),
  `nama_guru` varchar(255) NOT NULL,
  `username_guru` varchar(50) NOT NULL,
  `password_guru` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `guru`
--

INSERT INTO `guru` (`id_guru`, `nama_guru`, `username_guru`, `password_guru`) VALUES
('G001', 'Budi Santoso', 'budi_guru', 'guru123'),
('G002', 'Diky Pambudi', 'diky_guru', 'guru456'),
('G003', 'Santi Dwi Cahya', 'santi_guru', 'guru789');

-- --------------------------------------------------------

--
-- Struktur dari tabel `materi`
--

CREATE TABLE `materi` (
  `id_materi` varchar(10) NOT NULL CHECK (`id_materi` like 'MTR%'),
  `judul_materi` varchar(255) NOT NULL,
  `semester_materi` enum('1','2') NOT NULL,
  `isi_materi` longtext DEFAULT NULL,
  `video_materi` varchar(255) DEFAULT NULL,
  `id_admin` varchar(10) DEFAULT NULL,
  `id_guru` varchar(10) DEFAULT NULL,
  `pengubah_terakhir` varchar(10) NOT NULL,
  `role_pengubah` enum('admin','guru','','') NOT NULL,
  `waktu_diubah` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `materi`
--

INSERT INTO `materi` (`id_materi`, `judul_materi`, `semester_materi`, `isi_materi`, `video_materi`, `id_admin`, `id_guru`, `pengubah_terakhir`, `role_pengubah`, `waktu_diubah`) VALUES
('MTR001', 'Materi 1', '1', 'Deskripsi materi pertama', 'https://youtu.be/B6yifBxT5jQ?si=gaAMD-jwZtft-nVG', NULL, 'G001', 'G001', 'guru', '2025-05-02 20:17:53'),
('MTR002', 'Bulu Tangkis', '1', 'Bulu tangkis, atau dikenal juga sebagai badminton, adalah olahraga raket yang dimainkan dengan tujuan memukul kok melewati net dan mendaratkannya di area lawan. Olahraga ini melibatkan penggunaan raket untuk memukul kok. Indonesia sering mengikuti kompetisi bulu tangkis seperti Piala Sudirman. Pemahaman dasar bulu tangkis meliputi teknik memukul, aturan permainan, dan strategi.', 'https://youtu.be/B6yifBxT5jQ?si=gaAMD-jwZtft-nVG', NULL, 'G001', 'G001', 'guru', '2025-05-02 20:18:20'),
('MTR003', 'Bulu Tangkis', '1', '|', 'https://youtu.be/B6yifBxT5jQ?si=gaAMD-jwZtft-nVG', NULL, 'G001', 'G001', 'guru', '2025-05-02 20:20:07'),
('MTR004', 'Bulu Tangkis', '1', '|', 'https://youtu.be/1oorTNK_qw4?si=ljlbZtl2Yv-QfqtG', NULL, 'G001', 'G001', 'guru', '2025-05-02 20:20:03'),
('MTR005', 'Senam Irama', '1', '|', 'https://youtu.be/1oorTNK_qw4?si=ljlbZtl2Yv-QfqtG', NULL, 'G001', 'G001', 'guru', '2025-05-02 20:19:58'),
('MTR006', 'Bulu Tangkis', '2', '|', 'https://youtu.be/B6yifBxT5jQ?si=gaAMD-jwZtft-nVG', 'ADM002', NULL, 'ADM002', 'admin', '2025-05-02 20:22:06'),
('MTR007', 'Senam Irama', '2', '|', 'https://youtu.be/B6yifBxT5jQ?si=gaAMD-jwZtft-nVG', 'ADM002', NULL, 'ADM002', 'admin', '2025-05-02 20:22:01'),
('MTR008', 'Senam Irama', '2', '|', 'https://youtu.be/1oorTNK_qw4?si=ztOGCXEf6R7wquO_', 'ADM002', NULL, 'ADM002', 'admin', '2025-05-02 20:21:56'),
('MTR009', 'Bulu Tangkis', '2', '|', 'https://youtu.be/1oorTNK_qw4?si=ljlbZtl2Yv-QfqtG', 'ADM001', NULL, 'ADM001', 'admin', '2025-05-02 20:21:21'),
('MTR010', '|', '2', '|', 'https://youtu.be/B6yifBxT5jQ?si=gaAMD-jwZtft-nVG', 'ADM001', NULL, 'ADM001', 'admin', '2025-05-02 20:21:17'),
('MTR011', '|', '2', '|', 'https://youtu.be/1oorTNK_qw4?si=ztOGCXEf6R7wquO_', 'ADM001', NULL, 'ADM001', 'admin', '2025-05-02 20:21:13'),
('MTR012', 'Y', '1', 'y', 'https://youtu.be/B6yifBxT5jQ?si=gaAMD-jwZtft-nVG', NULL, 'G001', 'G001', 'guru', '2025-05-02 20:19:43'),
('MTR013', 'Y', '1', 'y', 'https://youtu.be/B6yifBxT5jQ?si=gaAMD-jwZtft-nVG', 'ADM001', NULL, 'ADM001', 'admin', '2025-05-03 06:33:06'),
('MTR014', 'Y', '1', '-', 'https://youtu.be/B6yifBxT5jQ?si=gaAMD-jwZtft-nVG', 'ADM001', NULL, '', 'admin', '2025-05-03 13:34:29'),
('MTR015', 'Minggu 1 : Pengenalan dan Aturan (Senam Irama)', '1', 'BLALALALAL', 'https://youtu.be/B6yifBxT5jQ?si=gaAMD-jwZtft-nVG', NULL, 'G001', '', 'admin', '2025-05-03 13:50:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `id_siswa` varchar(10) NOT NULL CHECK (`id_siswa` like 'S%'),
  `nama_siswa` varchar(255) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `username_siswa` varchar(50) NOT NULL,
  `password_siswa` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`id_siswa`, `nama_siswa`, `kelas`, `username_siswa`, `password_siswa`) VALUES
('S001', 'Dina Rahmawatini', 'X IPA 1', 'dina_siswa', 'siswa123'),
('S002', 'Rizky Maulana', 'XI IPS 2', 'rizky_siswa', 'siswa456'),
('S003', 'Fajar Prasetya', 'X IPS 1', 'fajar_siswa', 'siswa789'),
('S004', 'Rudi Anggara', 'X IPS 2', 'rudi_siswa', 'siswa004'),
('S005', 'Irwansyah', 'X IPA 2', 'irwansyah_siswa', 'siswa003'),
('S006', 'Ananta Guna', 'X IPS 2', 'ananta_siswa', 'siswa006');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username_admin` (`username_admin`);

--
-- Indeks untuk tabel `akses_materi`
--
ALTER TABLE `akses_materi`
  ADD KEY `id_siswa` (`id_siswa`),
  ADD KEY `id_materi` (`id_materi`);

--
-- Indeks untuk tabel `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id_guru`),
  ADD UNIQUE KEY `username_guru` (`username_guru`);

--
-- Indeks untuk tabel `materi`
--
ALTER TABLE `materi`
  ADD PRIMARY KEY (`id_materi`),
  ADD UNIQUE KEY `id_pengunggah` (`id_admin`,`id_guru`) USING BTREE;

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id_siswa`),
  ADD UNIQUE KEY `username_siswa` (`username_siswa`);

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `akses_materi`
--
ALTER TABLE `akses_materi`
  ADD CONSTRAINT `akses_materi_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE,
  ADD CONSTRAINT `akses_materi_ibfk_2` FOREIGN KEY (`id_materi`) REFERENCES `materi` (`id_materi`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `materi`
--
ALTER TABLE `materi`
  ADD CONSTRAINT `materi_ibfk_1` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
