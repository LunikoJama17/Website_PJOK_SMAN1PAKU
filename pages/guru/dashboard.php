<?php
session_start();
require_once "../../database.php";

// Pastikan user yang login adalah guru
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    header("Location: ../../auth/login.php");
    exit();
}

include "../../includes/navbar_guru.php";

$guru_name = $_SESSION['nama'] ?? 'Guru';

$jumlah_materi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM materi"));
?>

<div class="container mx-auto px-4 mt-8">
    <h2 class="text-center text-3xl font-bold text-gray-800 mb-6">Selamat Datang, <span class="text-indigo-600"><?= htmlspecialchars($guru_name); ?></span>!</h2>
    <p class="text-center mb-8 text-gray-600 text-lg">Jumlah materi dapat dilihat di bawah sini. Untuk mengelola, silakan lewat navigasi yang ada</p>

    <div class="flex justify-center mb-16">
        <!-- Card Materi -->
        <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white shadow-xl rounded-xl p-6 w-full max-w-sm relative overflow-hidden transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl">
            <div class="absolute right-4 top-4 text-5xl opacity-30">📘</div>
            <h3 class="text-lg font-semibold mb-2">Jumlah Materi</h3>
            <p class="text-4xl font-bold"><?= $jumlah_materi ?></p>
        </div>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
