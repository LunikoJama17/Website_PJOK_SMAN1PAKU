<?php
// Memulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) session_start();

// Menyertakan file database
require '../../../database.php'; // Sesuaikan dengan strukturmu

// Ambil ID pengguna (guru atau admin) dan ID materi
$id_guru = $_SESSION['id'] ?? null; // ID guru atau admin
$id_materi = $_GET['id'] ?? null; // ID materi dari URL

// Cek apakah ID pengguna ada di session
if (!$id_guru) {
    die("Guru belum login. Silakan login terlebih dahulu.");
}

// Cek apakah ID materi ada
if (!$id_materi) {
    die("ID Materi tidak ditemukan.");
}

// Menghitung role pengguna (guru atau admin) untuk menyesuaikan tampilan atau logika lainnya
$role = $_SESSION['role'] ?? null; // Ambil role pengguna (guru, admin, atau lainnya)

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

include "../../../includes/navbar_guru.php";
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
<body class="bg-gray-100 text-gray-800">
  
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
    <div class="bg-white p-6 rounded-lg shadow text-justify leading-relaxed">
      <?= nl2br(htmlspecialchars($materi['isi_materi'])) ?>
    </div>

  </main>

  <?php include "../../../includes/footer.php"; ?>

</body>
</html>
