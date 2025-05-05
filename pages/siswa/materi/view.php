<?php
// Memulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) session_start();

// Menyertakan file database
require '../../../database.php'; // sesuaikan dengan strukturmu

// Ambil ID siswa dan ID materi
$id_siswa = $_SESSION['id'] ?? null; // Ubah id_siswa menjadi id sesuai hasil debug
$id_materi = $_GET['id'] ?? null; // Ambil ID materi dari URL

// Cek apakah ID siswa ada di session
if (!$id_siswa) {
    die("Siswa belum login. Silakan login terlebih dahulu.");
}

// Cek apakah ID materi ada
if (!$id_materi) {
    die("ID Materi tidak ditemukan.");
}

include "../../../includes/navbar_siswa.php";  // Mengikutsertakan navbar

// Catat akses materi siswa
$query = "INSERT INTO akses_materi (id_siswa, id_materi, tgl_akses) VALUES ('$id_siswa', '$id_materi', NOW())";
if (!mysqli_query($conn, $query)) {
    die("Gagal mencatat akses materi: " . mysqli_error($conn));
}

// Ambil data materi berdasarkan ID
$query = "SELECT * FROM materi WHERE id_materi = '$id_materi'";
$result = mysqli_query($conn, $query);

// Periksa apakah ada data materi
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

$materi = mysqli_fetch_assoc($result);

if (!$materi) {
    die("Materi tidak ditemukan.");
}

// Cek apakah URL video adalah URL embed yang valid
$video_url = $materi['video_materi'];

// Jika URL adalah URL YouTube pendek, ubah ke format embed
if (strpos($video_url, 'youtu.be/') !== false) {
    // Ambil ID video dari URL pendek
    $video_id = substr(parse_url($video_url, PHP_URL_PATH), 1); // Mengambil bagian setelah "/"

    // Membuat URL embed
    $video_url = "https://www.youtube.com/embed/" . $video_id;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($materi['judul_materi']) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Main Section -->
    <main class="max-w-4xl mx-auto p-6">

<!-- Tombol Kembali -->
<div class="flex py-4">
  <a href="javascript:history.back()" class="text-white font-semibold bg-red-500 px-6 py-3 rounded-lg shadow-lg hover:bg-red-600 transition-all duration-300 transform hover:scale-105">
    Kembali
  </a>
</div>

<!-- Video Embed -->
<div class="mb-6 rounded overflow-hidden">
  <div class="aspect-w-16 aspect-h-9">
    <iframe class="w-full h-64 md:h-96 rounded shadow" src="<?= htmlspecialchars($video_url) ?>" frameborder="0" allowfullscreen></iframe>
  </div>
</div>

<!-- Judul Materi -->
<h1 class="text-2xl md:text-3xl font-semibold text-center bg-gray-200 py-3 rounded mb-6 shadow">
  <?= htmlspecialchars($materi['judul_materi']) ?>
</h1>

<!-- Isi Materi -->
<div class="bg-white p-6 m-4 rounded-lg shadow text-justify leading-relaxed backdrop-blur">
  <?= nl2br(htmlspecialchars($materi['isi_materi'])) ?>
</div>

</main>

  <!-- Footer -->
  <?php include "../../../includes/footer.php"; ?>
</body>
</html>
