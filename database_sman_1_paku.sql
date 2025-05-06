-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 06 Bulan Mei 2025 pada 07.16
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
('G001', 'Budi Santoso', 'budi_guru', 'guru001'),
('G002', 'Naro, S.Pd', 'naro_guru', 'guru002');

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
('MTR015', 'Minggu 1 : Pengenalan dan Aturan (Senam Irama)', '1', 'Senam irama merupakan salah satu cabang olahraga yang memadukan gerakan tubuh dengan irama musik atau ketukan tertentu. Senam ini menekankan pada keluwesan, ketepatan, serta keindahan gerakan yang dilakukan secara berirama, teratur, dan harmonis. Dalam pelaksanaannya, senam irama tidak hanya melatih kebugaran jasmani, tetapi juga melatih keterampilan motorik, koordinasi, kelincahan, serta kepekaan terhadap irama musik. Gerakan-gerakan dalam senam irama dapat dilakukan secara individu maupun kelompok, dengan atau tanpa alat bantu seperti pita, bola, simpai, atau tali. Senam irama sering digunakan sebagai bagian dari kegiatan pendidikan jasmani di sekolah karena mampu memberikan manfaat fisik dan mental secara menyeluruh, serta membentuk kedisiplinan dan rasa percaya diri pada peserta.\n\nDalam melaksanakan senam irama, terdapat beberapa aturan yang harus dipatuhi untuk mencapai hasil yang optimal dan menjaga keselamatan peserta. Pertama, setiap peserta harus mengenakan pakaian yang nyaman dan sesuai agar gerakan dapat dilakukan dengan leluasa. Kedua, pemanasan wajib dilakukan sebelum memulai senam untuk menghindari cedera dan mempersiapkan tubuh terhadap aktivitas fisik. Ketiga, peserta harus mengikuti irama atau ketukan dengan cermat agar setiap gerakan terlihat selaras dan tidak kacau. Selain itu, penting untuk memperhatikan teknik dasar gerakan seperti langkah kaki, ayunan tangan, serta keseimbangan tubuh yang benar. Peserta juga diharapkan menjaga kekompakan dan disiplin, terutama dalam senam kelompok, agar gerakan dapat berjalan serempak. Terakhir, senam irama harus dilakukan di tempat yang aman dan cukup luas agar peserta bisa bergerak bebas tanpa risiko bertabrakan. Dengan memahami dan mengikuti aturan-aturan ini, senam irama dapat menjadi kegiatan yang menyenangkan, menyehatkan, dan membangun karakter positif.', 'https://youtu.be/B6yifBxT5jQ?si=gaAMD-jwZtft-nVG', NULL, 'G001', 'G001', 'guru', '2025-05-03 11:21:22'),
('MTR808', 'Materi Test', '1', 'Konten materi test', '', 'ADM001', NULL, '', 'admin', '2025-05-05 21:50:30');

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
('S001', 'Adibah Kalmasya', 'X A', 'adibah_siswa', 'siswa001'),
('S002', 'Amanda Ayu Lestari', 'X A', 'amanda_siswa', 'siswa002'),
('S003', 'Aura Leluni Putri', 'X A', 'aura_siswa', 'siswa003'),
('S004', 'Celine Christiono Neundroff', 'X A', 'celine_siswa', 'siswa004'),
('S005', 'Chiko Erianto', 'X A', 'chiko_siswa', 'siswa005'),
('S006', 'Daniel Sigit Pratama', 'X A', 'daniel_siswa', 'siswa006'),
('S007', 'Dea Nita', 'X A', 'dea_siswa', 'siswa007'),
('S008', 'Estyara Pangamiany', 'X A', 'estyara_siswa', 'siswa008'),
('S009', 'Ezra Kahanjak', 'X A', 'ezra_siswa', 'siswa009'),
('S010', 'Grescia Natalin', 'X A', 'grescia_siswa', 'siswa010'),
('S011', 'Jessica Maharani', 'X A', 'jessica_siswa', 'siswa011'),
('S012', 'Joshua Okta Viu Tarukallo', 'X A', 'joshua_siswa', 'siswa012'),
('S013', 'Ketrin Winey', 'X A', 'ketrin_siswa', 'siswa013'),
('S014', 'Kristian Arsellino', 'X A', 'kristian_siswa', 'siswa014'),
('S015', 'Lunaya', 'X A', 'lunaya_siswa', 'siswa015'),
('S016', 'Manohara Delin', 'X A', 'manohara_siswa', 'siswa016'),
('S017', 'Mirna Munita', 'X A', 'mirna_siswa', 'siswa017'),
('S018', 'Nayanta Afrilianto', 'X A', 'nayanta_siswa', 'siswa018'),
('S019', 'Nidia Agustin', 'X A', 'nidia_siswa', 'siswa019'),
('S020', 'Ovaldo Benyamin', 'X A', 'ovaldo_siswa', 'siswa020'),
('S021', 'Pasya Saputra', 'X A', 'pasya_siswa', 'siswa021'),
('S022', 'Puspa Sridayanti', 'X A', 'puspa_siswa', 'siswa022'),
('S023', 'Raditia Perdinata', 'X A', 'raditia_siswa', 'siswa023'),
('S024', 'Rafael Orlando', 'X A', 'rafael_siswa', 'siswa024'),
('S025', 'Rahmina Fitri', 'X A', 'rahmina_siswa', 'siswa025'),
('S026', 'Refan Aditya', 'X A', 'refan_siswa', 'siswa026'),
('S027', 'Rezky Aditya', 'X A', 'rezky_siswa', 'siswa027'),
('S028', 'Regina Dameyanti', 'X A', 'regina_siswa', 'siswa028'),
('S029', 'Rimayanti', 'X A', 'rimayanti_siswa', 'siswa029'),
('S030', 'Rivaldo Singal', 'X A', 'rivaldo_siswa', 'siswa030'),
('S031', 'Rosa', 'X A', 'rosa_siswa', 'siswa031'),
('S032', 'Seno Jilianto', 'X A', 'seno_siswa', 'siswa032'),
('S033', 'Siloam Stevonny', 'X A', 'siloam_siswa', 'siswa033'),
('S034', 'Tiara Anastasya Carolinè', 'X A', 'tiara_siswa', 'siswa034'),
('S035', 'Tio Baraja Priwijaya', 'X A', 'tio_siswa', 'siswa035'),
('S036', 'Windi', 'X A', 'windi_siswa', 'siswa036'),
('S037', 'Alya Dove El Blessia', 'X B', 'alya_siswa', 'siswa037'),
('S038', 'Aiysya Putri', 'X B', 'aiysya_siswa', 'siswa038'),
('S039', 'Anggun Wunge Firstalents', 'X B', 'anggun_siswa', 'siswa039'),
('S040', 'Anka Kiara', 'X B', 'anka_siswa', 'siswa040'),
('S041', 'Anwar Kusnaini', 'X B', 'anwar_siswa', 'siswa041'),
('S042', 'Brian Adam Imanuel', 'X B', 'brian_siswa', 'siswa042'),
('S043', 'Chelsy Agustin Enjelina', 'X B', 'chelsy_siswa', 'siswa043'),
('S044', 'Chintia Novrianti. G', 'X B', 'chintia_siswa', 'siswa044'),
('S045', 'Didi Chrisfama', 'X B', 'didi_siswa', 'siswa045'),
('S046', 'Dino', 'X B', 'dino_siswa', 'siswa046'),
('S047', 'Diva Tri Andri Leluni', 'X B', 'diva_siswa', 'siswa047'),
('S048', 'EgiTri Hartawan Ivanka', 'X B', 'egitri_siswa', 'siswa048'),
('S049', 'Evi Dealova', 'X B', 'evi_siswa', 'siswa049'),
('S050', 'Frygel Pamunsu', 'X B', 'frygel_siswa', 'siswa050'),
('S051', 'Ketty Ariani', 'X B', 'ketty_siswa', 'siswa051'),
('S052', 'Luna Ciya', 'X B', 'luna_siswa', 'siswa052'),
('S053', 'Maria Afriani Dua Liter', 'X B', 'maria_siswa', 'siswa053'),
('S054', 'Markus Hariano', 'X B', 'markus_siswa', 'siswa054'),
('S055', 'Matthew Miracle R. Wonok', 'X B', 'matthew_siswa', 'siswa055'),
('S056', 'Meilin Yulianita', 'X B', 'meilin_siswa', 'siswa056'),
('S057', 'Recky Lesmana', 'X B', 'recky_siswa', 'siswa057'),
('S058', 'Resa Margareta', 'X B', 'resa_siswa', 'siswa058'),
('S059', 'Revan Aprilian', 'X B', 'revan_siswa', 'siswa059'),
('S060', 'Riska Enjeliani', 'X B', 'riska_siswa', 'siswa060'),
('S061', 'Rossy Saputra', 'X B', 'rossy_siswa', 'siswa061'),
('S062', 'Seliani Puspita Dewi', 'X B', 'seliani_siswa', 'siswa062'),
('S063', 'Sopyan Mangku Janang', 'X B', 'sopyan_siswa', 'siswa063'),
('S064', 'Tamara Handayani', 'X B', 'tamara_siswa', 'siswa064'),
('S065', 'Yosua Petrianto Sejati', 'X B', 'yosua_siswa', 'siswa065'),
('S066', 'Mikausya Putri M.', 'X B', 'mikausya_siswa', 'siswa066');

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
