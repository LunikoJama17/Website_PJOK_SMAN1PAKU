<?php
ob_start();
session_start();
require_once "../../../database.php";

// Pastikan hanya admin atau guru yang bisa akses
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','guru'])) {
    header("Location: ../../../auth/login.php");
    exit();
}

// Tangani Hapus via AJAX
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['hapus'])) {
    ob_clean();
    $id = mysqli_real_escape_string($conn, $_POST['hapus']);
    $cek = mysqli_query($conn,"SELECT * FROM materi WHERE id_materi='$id'");
    if (!$cek || mysqli_num_rows($cek)===0) {
        echo 'error'; exit();
    }
    echo (mysqli_query($conn,"DELETE FROM materi WHERE id_materi='$id'") ? 'success' : 'error');
    exit();
}

// Pilih semester (default=1)
$semester = (isset($_GET['semester']) && in_array($_GET['semester'], ['1','2'])) ? $_GET['semester'] : '1';


// Pagination
$perPage    = 5;
$page       = max(1,intval($_GET['page'] ?? 1));
$start      = ($page-1)*$perPage;

// Hitung total & halaman
$totalRes   = mysqli_query($conn,"SELECT COUNT(*) AS tot FROM materi WHERE semester_materi='$semester'");
$totalData  = mysqli_fetch_assoc($totalRes)['tot'];
$totalPages = ceil($totalData/$perPage);

// Ambil data
$sql    = "SELECT * FROM materi WHERE semester_materi='$semester' ORDER BY id_materi DESC LIMIT $start,$perPage";
$res    = mysqli_query($conn,$sql);
$materi = mysqli_fetch_all($res, MYSQLI_ASSOC);

// Tampilkan alert setelah tambah/edit
$alertScript = '';
if (isset($_SESSION['success_tambah'])) {
    $alertScript = "Swal.fire('Berhasil','Materi berhasil ditambahkan','success');";
    unset($_SESSION['success_tambah']);
}
if (isset($_SESSION['success_edit'])) {
    $alertScript = "Swal.fire('Berhasil','Materi berhasil diperbarui','success');";
    unset($_SESSION['success_edit']);
}

include "../../../includes/navbar_guru.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Kelola Materi | <?= ucfirst($_SESSION['role']) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-indigo-100 p-4 flex flex-col min-h-screen">

  <!-- Judul -->
  <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">
    Materi Semester <?= $semester ?>
  </h1>

  <!-- Tab Semester -->
  <div class="flex justify-center space-x-4 mb-6">
    <?php for($s=1;$s<=2;$s++): ?>
      <a href="?semester=<?= $s ?>"
         class="px-5 py-2 rounded-full text-sm font-semibold <?= $semester==$s ? 'bg-indigo-600 text-white' : 'bg-gray-200 hover:bg-gray-300'; ?>">
        Semester <?= $s ?>
      </a>
    <?php endfor; ?>
  </div>

  <div class="flex justify-center mb-6">
    <h2 class="text-center inline-block text-2xl font-bold text-white bg-indigo-600 rounded-full px-6 py-2">Materi Pembelajaran Semester <?= $semester ?></h2>
  </div>

  <!-- Tambah Materi -->
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
        <div class="flex space-x-2 justify-center w-full md:justify-end w-full md:w-auto">
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

  <!-- Pagination Nav -->
  <?php if ($totalPages > 1): ?>
  <nav class="flex justify-center mt-6" aria-label="Pagination">
    <ul class="inline-flex -space-x-px text-sm">
      <?php if ($page > 1): ?>
        <li>
          <a href="?semester=<?= $semester ?>&page=<?= $page - 1 ?>"
             class="px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700">&laquo; Prev</a>
        </li>
      <?php endif; ?>

      <?php
      $startPage = max(1, $page - 2);
      $endPage = min($totalPages, $page + 2);

      if ($startPage > 1) {
        echo '<li><a href="?semester=' . $semester . '&page=1" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">1</a></li>';
        if ($startPage > 2) echo '<li><span class="px-3 py-2 text-gray-500">...</span></li>';
      }

      for ($i = $startPage; $i <= $endPage; $i++): ?>
        <li>
          <a href="?semester=<?= $semester ?>&page=<?= $i ?>"
             class="px-3 py-2 leading-tight border border-gray-300 <?= ($i === $page) ? 'bg-indigo-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700'; ?>">
             <?= $i ?>
          </a>
        </li>
      <?php endfor;

      if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) echo '<li><span class="px-3 py-2 text-gray-500">...</span></li>';
        echo '<li><a href="?semester=' . $semester . '&page=' . $totalPages . '" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">' . $totalPages . '</a></li>';
      }
      ?>

      <?php if ($page < $totalPages): ?>
        <li>
          <a href="?semester=<?= $semester ?>&page=<?= $page + 1 ?>"
             class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700">Next &raquo;</a>
        </li>
      <?php endif; ?>
    </ul>
  </nav>
  <?php endif; ?>

  <!-- SweetAlert delete & post‐add/edit -->
  <script>
    <?= $alertScript ?>

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
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
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
