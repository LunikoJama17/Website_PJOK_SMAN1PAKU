<?php
session_start();
require_once "../../database.php";

// Pastikan user yang login adalah siswa
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../../auth/login.php");
    exit();
}

include "../../includes/navbar_siswa.php";

$siswa_name = $_SESSION['nama'] ?? 'Siswa';
$jumlah_materi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM materi"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navbar sudah include di navbar_siswa.php -->

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Welcome Section -->
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">
                Selamat Datang, <span class="text-indigo-600"><?= htmlspecialchars($siswa_name); ?></span>!
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Jumlah materi dapat dilihat di bawah sini. Untuk mengakses materi, silakan lewat navigasi yang ada.
            </p>
        </div>

        <!-- Stats Card -->
        <div class="flex justify-center mb-16">
            <div class="bg-gradient-to-r from-purple-500 to-indigo-400 text-white rounded-xl shadow-xl p-6 w-full max-w-sm relative overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                <div class="absolute right-4 top-4 text-5xl opacity-50">📚</div>
                <h3 class="text-lg font-semibold mb-2">Jumlah Materi</h3>
                <p class="text-4xl font-bold"><?= $jumlah_materi ?></p>
                <div class="mt-4 text-sm text-purple-100">
                    Materi tersedia untuk dipelajari
                </div>
            </div>
        </div>

        <!-- About School Section -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-12">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
                Tentang Sekolah
            </h2>
            
            <div class="prose prose-indigo max-w-none text-gray-600 mb-8">
                <p class="text-justify mb-4">
                SMA Negeri 1 Paku (SMAN 1 PAKU) merupakan sekolah menengah atas negeri yang terletak di Jl. Ampah–Tamiang Layang KM.21, Desa Tampa, Kecamatan Paku, Kabupaten Barito Timur, Kalimantan Tengah, dengan kode pos 73652. Sekolah ini memiliki NPSN 30202438 dan berada di bawah naungan Pemerintah Daerah. Saat ini dipimpin oleh Bapak Wayan Sila Putra sebagai kepala sekolah. Berdasarkan data resmi, SMAN 1 Paku telah meraih akreditasi B dan menerapkan Kurikulum Merdeka dalam kegiatan pembelajarannya. Dengan luas lahan mencapai 22.000 m², sekolah ini menyediakan lingkungan belajar yang kondusif dan mendukung proses pembelajaran. Meski beberapa data fasilitas dan jumlah siswa masih dalam proses pemutakhiran, SMAN 1 Paku terus berkomitmen untuk meningkatkan mutu pendidikan dan memberikan pelayanan terbaik bagi seluruh warga sekolah.
                </p>
            </div>

            <!-- School Location -->
            <div class="mt-8">
                <h3 class="text-xl font-semibold text-center text-gray-800 mb-4">
                    <span class="inline-block mr-2">📍</span> Lokasi Sekolah
                </h3>
                
                <!-- Map Container -->
                <div class="rounded-xl overflow-hidden shadow-lg border border-gray-200">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7975.061153640254!2d115.11448060000001!3d-1.9403742000000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dfae81e543cc367%3A0x3bc6e75f470d1cb9!2sSMA%20Negri%201%20Paku!5e0!3m2!1sen!2sid!4v1746268206753!5m2!1sen!2sid" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>

                <!-- Map Link -->
                <div class="text-center mt-4">
                    <a href="https://maps.app.goo.gl/aJatuJW52eAzAiDP7?g_st=ac" 
                       target="_blank"
                       class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        Buka di Google Maps
                    </a>
                </div>
            </div>
        </div>

        <!-- About Website Section -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-12">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
                Tentang Website Ini
            </h2>
            
            <div class="prose prose-indigo max-w-none text-gray-600">
                <p class="text-justify mb-4">
                    Website ini merupakan platform pembelajaran digital SMA Negeri 1 Paku yang dikhususkan untuk mendukung pembelajaran Pendidikan Jasmani, Olahraga, dan Kesehatan (PJOK). Dibangun dengan tujuan mempermudah akses materi pembelajaran dan meningkatkan kualitas pendidikan jasmani di sekolah ini.
                </p>
                
                <h3 class="text-xl font-semibold text-gray-700 mt-6 mb-3">Fitur Utama:</h3>
                <ul class="list-disc pl-5 space-y-2 mb-4">
                    <li>Penyediaan materi pembelajaran PJOK secara digital</li>
                    <li>Materi fleksibel dalam bentuk teks dan video</li>
                    <li>Sumber referensi latihan fisik dan kesehatan</li>
                </ul>
                
                <p class="text-justify">
                    Website ini dikembangkan sebagai bagian dari inovasi digital SMAN 1 Paku untuk menunjang proses belajar mengajar di era teknologi, khususnya untuk mata pelajaran yang membutuhkan praktik seperti PJOK.
                </p>
            </div>
        </div>
    </div>

    <?php include "../../includes/footer.php"; ?>
</body>
</html>