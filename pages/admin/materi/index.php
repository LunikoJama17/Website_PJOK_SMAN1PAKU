<?php
ob_start();
session_start();
require_once "../../../database.php";

// Pastikan admin sudah login
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Tangani Hapus via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    ob_clean();
    $id = mysqli_real_escape_string($conn, $_POST['hapus']);
    $cek = mysqli_query($conn, "SELECT * FROM materi WHERE id_materi='$id'");
    if (!$cek || mysqli_num_rows($cek) === 0) {
        echo 'error';
        exit();
    }
    echo (mysqli_query($conn, "DELETE FROM materi WHERE id_materi='$id'") ? 'success' : 'error');
    exit();
}

// Pilihan semester
$semester = isset($_GET['semester']) && in_array($_GET['semester'], ['1','2'])
    ? $_GET['semester'] : '1';

// Pagination
$perPage    = 5;
$page       = max(1, intval($_GET['page'] ?? 1));
$start      = ($page - 1) * $perPage;

// Hitung total data di semester terpilih
$totalRes   = mysqli_query($conn, "SELECT COUNT(*) AS tot FROM materi WHERE semester_materi='$semester'");
$totalData  = mysqli_fetch_assoc($totalRes)['tot'];
$totalPages = ceil($totalData / $perPage);

// Ambil data materi terfilter & ter-paginate
$sql    = "SELECT * FROM materi WHERE semester_materi='$semester' ORDER BY id_materi DESC LIMIT $start,$perPage";
$res    = mysqli_query($conn, $sql);
$materi = mysqli_fetch_all($res, MYSQLI_ASSOC);

include "../../../includes/navbar_admin.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Kelola Materi | Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-indigo-100 p-4">

  <!-- Judul -->
  <h1 class="text-3xl font-bold text-black mb-6 text-center">Kelola Materi Semester <?= $semester ?></h1>

  <!-- Tab Semester -->
  <div class="flex justify-center space-x-4 mb-6">
    <?php for ($s = 1; $s <= 2; $s++): ?>
      <a href="?semester=<?= $s ?>"
         class="px-5 py-2 rounded-full text-sm font-semibold
           <?= $semester == $s
              ? 'bg-indigo-600 text-white'
              : 'bg-gray-200 hover:bg-gray-300'; ?>">
        Semester <?= $s ?>
      </a>
    <?php endfor; ?>
  </div>

  <div class="flex justify-center mb-6">
    <h2 class="text-center inline-block text-2xl font-bold text-white bg-indigo-600 rounded-full px-6 py-2">Materi Pembelajaran Semester <?= $semester ?></h2>
  </div>

  <!-- Tombol Tambah Materi -->
  <div class="flex justify-center mb-6">
    <a href="tambah.php?semester=<?= $semester ?>"
       class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold text-sm">
      + Tambah Materi
    </a>
  </div>

  <!-- Daftar Materi sebagai Card -->
  <div class="space-y-4">
    <?php foreach ($materi as $row): ?>
      <div class="bg-white p-4 rounded-lg shadow-md max-w-2xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between">
        <!-- Judul (link edit) -->
        <a href="edit.php?id=<?= htmlspecialchars($row['id_materi']) ?>"
           class="text-center flex-1 bg-gray-100 hover:bg-gray-200 rounded-lg p-3 font-semibold text-gray-800 truncate mb-3 md:mb-0 md:mr-4">
          <?= htmlspecialchars($row['judul_materi']) ?>
        </a>
        <div class="flex space-x-2 justify-center md:justify-end w-full md:w-auto">
          <!-- Tombol Lihat -->
          <a href="view.php?id=<?= htmlspecialchars($row['id_materi']) ?>"
            class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-sm whitespace-nowrap">
            Lihat
          </a>
          <!-- Tombol Hapus -->
          <button onclick="confirmDelete('<?= htmlspecialchars($row['id_materi']) ?>')"
            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-sm whitespace-nowrap">
            Hapus
          </button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
    <div class="mt-8 flex justify-center space-x-2">
      <?php if ($page > 1): ?>
        <a href="?semester=<?= $semester ?>&page=<?= $page-1 ?>" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">&laquo; Prev</a>
      <?php endif; ?>
      <?php for ($i=1; $i<=$totalPages; $i++): ?>
        <a href="?semester=<?= $semester ?>&page=<?= $i ?>"
           class="px-3 py-1 rounded
             <?= $i==$page
                ? 'bg-indigo-600 text-white'
                : 'bg-gray-200 hover:bg-gray-300'; ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
      <?php if ($page < $totalPages): ?>
        <a href="?semester=<?= $semester ?>&page=<?= $page+1 ?>"class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Next &raquo;</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- SweetAlert Delete -->
  <script>
  function confirmDelete(id) {
    Swal.fire({
      title: 'Hapus materi?',
      text: "Data tidak dapat dikembalikan!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, hapus',
      cancelButtonText: 'Batal'
    }).then(res => {
      if (res.isConfirmed) {
        fetch('index.php?semester=<?= $semester ?>&page=<?= $page ?>', {
          method: 'POST',
          headers: {'Content-Type':'application/x-www-form-urlencoded'},
          body: 'hapus='+encodeURIComponent(id)
        })
        .then(r=>r.text())
        .then(txt=>{
          if (txt.trim()==='success') {
            Swal.fire('Dihapus!','Materi berhasil dihapus.','success')
              .then(()=>location.reload());
          } else {
            Swal.fire('Gagal!','Terjadi kesalahan.','error');
          }
        });
      }
    });
  }
  </script>

<?php include "../../../includes/footer.php"; ?>
</body>
</html>

<?php ob_end_flush(); ?>
