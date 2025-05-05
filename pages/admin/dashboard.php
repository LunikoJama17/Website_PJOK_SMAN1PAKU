<?php
session_start();
require_once "../../database.php";

// Pastikan user yang login adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include "../../includes/navbar_admin.php";

$admin_name = $_SESSION['nama'] ?? 'Admin';

$jumlah_materi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM materi"));
$jumlah_guru = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM guru"));
$jumlah_siswa = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM siswa"));
?>

<div class="container mx-auto px-4 mt-8">
    <h2 class="text-center text-3xl font-bold text-gray-800 mb-6">Selamat Datang, <span class="text-indigo-600"><?= htmlspecialchars($admin_name); ?></span>!</h2>
    <p class="text-center mb-8 text-gray-600 text-lg">Jumlah pengguna dan materi dapat dilihat di bawah sini. Untuk mengelola, silakan melalui navigasi yang ada</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Card Materi -->
        <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white shadow-lg rounded-xl p-6 relative overflow-hidden transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl">
            <div class="absolute right-4 top-4 text-5xl opacity-30">📘</div>
            <h3 class="text-lg font-semibold mb-2">Jumlah Materi</h3>
            <p class="text-4xl font-bold"><?= $jumlah_materi ?></p>
        </div>

        <!-- Card Guru -->
        <div class="bg-gradient-to-r from-green-400 to-emerald-600 text-white shadow-lg rounded-xl p-6 relative overflow-hidden transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl">
            <div class="absolute right-4 top-4 text-5xl opacity-30">👨‍🏫</div>
            <h3 class="text-lg font-semibold mb-2">Jumlah Guru</h3>
            <p class="text-4xl font-bold"><?= $jumlah_guru ?></p>
        </div>

        <!-- Card Siswa -->
        <div class="bg-gradient-to-r from-blue-400 to-sky-600 text-white shadow-lg rounded-xl p-6 relative overflow-hidden transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl">
            <div class="absolute right-4 top-4 text-5xl opacity-30">🎓</div>
            <h3 class="text-lg font-semibold mb-2">Jumlah Siswa</h3>
            <p class="text-4xl font-bold"><?= $jumlah_siswa ?></p>
        </div>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
