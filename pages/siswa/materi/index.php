<?php
ob_start();
session_start();
require_once "../../../database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header("Location: login.php");
    exit();
}

$semester = isset($_GET['semester']) && in_array($_GET['semester'], ['1','2']) ? $_GET['semester'] : '1';
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter = "semester_materi='$semester'";
if ($search !== '') {
    $safeSearch = mysqli_real_escape_string($conn, $search);
    $filter .= " AND judul_materi LIKE '%$safeSearch%'";
}

$perPage = 5;
$page = max(1, intval($_GET['page'] ?? 1));
$start = ($page - 1) * $perPage;

$totalRes = mysqli_query($conn, "SELECT COUNT(*) AS tot FROM materi WHERE $filter");
$totalData = mysqli_fetch_assoc($totalRes)['tot'];
$totalPages = ceil($totalData / $perPage);

$sql = "SELECT * FROM materi WHERE $filter ORDER BY id_materi DESC LIMIT $start,$perPage";
$res = mysqli_query($conn, $sql);
$materi = mysqli_fetch_all($res, MYSQLI_ASSOC);

include "../../../includes/navbar_siswa.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Materi Pembelajaran</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body { 
      font-family: 'Poppins', sans-serif;
      -webkit-text-size-adjust: 100%;
    }
    .materi-card {
      transition: transform 0.2s, box-shadow 0.2s;
    }
  </style>
</head>
<body class="bg-indigo-50 p-2 sm:p-4">

<div class="max-w-3xl mx-auto">
  <!-- Header -->
  <div class="mb-4 px-2">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 text-center mb-2">
      Materi Pembelajaran Semester <?= $semester ?>
    </h1>
    
    <!-- Tab Semester -->
    <div class="flex justify-center space-x-2 mb-4">
      <?php for ($s = 1; $s <= 2; $s++): ?>
        <a href="?semester=<?= $s ?>"
           class="px-4 py-1.5 rounded-full text-xs sm:text-sm font-medium
           <?= $semester == $s ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-200 hover:bg-gray-300'; ?>">
          Semester <?= $s ?>
        </a>
      <?php endfor; ?>
    </div>
    
    <!-- Search Box -->
    <div class="relative">
      <input type="text" id="search-box" name="q"
             value="<?= htmlspecialchars($search) ?>"
             placeholder="Cari materi..."
             class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 text-sm sm:text-base"
             data-semester="<?= $semester ?>">
      <svg class="absolute right-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
      </svg>
    </div>
  </div>

  <!-- Materi List -->
  <div id="materi-list" class="space-y-3 sm:space-y-4 px-2">
    <?php if (count($materi) === 0): ?>
      <div class="text-center py-8">
        <p class="text-gray-600">Tidak ada materi ditemukan.</p>
      </div>
    <?php else: ?>
      <?php foreach ($materi as $row): ?>
        <a href="view.php?id=<?= htmlspecialchars($row['id_materi']) ?>"
   class="block materi-card bg-white rounded-lg shadow-sm 
          transition-all duration-500 ease-[cubic-bezier(0.25,0.1,0.25,1)]
          hover:bg-gradient-to-r hover:from-indigo-600 hover:to-indigo-500 hover:text-white
          hover:shadow-xl hover:-translate-y-1
          active:bg-indigo-700 active:translate-y-0
          border border-gray-200 transform group">
  <div class="p-3 sm:p-4">
    <h2 class="text-center font-semibold text-gray-800 group-hover:text-white text-sm sm:text-base line-clamp-2 transition-colors duration-300">
      <?= htmlspecialchars($row['judul_materi']) ?>
    </h2>
    <p class="text-center text-xs sm:text-sm text-gray-500 group-hover:text-indigo-100 mt-1 transition-colors duration-500">
      Klik untuk melihat materi
    </p>
  </div>
</a>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
    <?php $queryStr = "semester=$semester" . ($search !== '' ? "&q=" . urlencode($search) : ''); ?>
    <div class="mt-6 flex justify-center items-center space-x-1 sm:space-x-2 px-2">
      <?php if ($page > 1): ?>
        <a href="?<?= $queryStr ?>&page=<?= $page-1 ?>"
           class="px-2.5 py-1.5 text-xs sm:text-sm bg-white text-indigo-800 rounded-md hover:bg-gray-100 shadow-sm">
          &laquo; Prev
        </a>
      <?php endif; ?>
      
      <?php 
      // Tampilkan maksimal 5 halaman di sekitar halaman aktif
      $startPage = max(1, $page - 2);
      $endPage = min($totalPages, $page + 2);
      
      if ($startPage > 1) echo '<span class="px-1">...</span>';
      for ($i = $startPage; $i <= $endPage; $i++): ?>
        <a href="?<?= $queryStr ?>&page=<?= $i ?>"
           class="px-3 py-1.5 text-xs sm:text-sm rounded-md <?= $i == $page ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-100 hover:bg-gray-200'; ?>">
          <?= $i ?>
        </a>
      <?php endfor; 
      if ($endPage < $totalPages) echo '<span class="px-1">...</span>'; ?>
      
      <?php if ($page < $totalPages): ?>
        <a href="?<?= $queryStr ?>&page=<?= $page+1 ?>"
           class="px-2.5 py-1.5 text-xs sm:text-sm bg-white text-indigo-800 rounded-md hover:bg-gray-100 shadow-sm">
          Next &raquo;
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<script>
// Optimized search with debounce
const searchBox = document.getElementById('search-box');
const materiList = document.getElementById('materi-list');
let searchTimer;

searchBox.addEventListener('input', function() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    const keyword = this.value.trim();
    const semester = this.dataset.semester;
    
    if (keyword.length > 0 || keyword.length === 0) {
      fetch(`<?= basename($_SERVER['PHP_SELF']) ?>?semester=${semester}&q=${encodeURIComponent(keyword)}`)
        .then(res => res.text())
        .then(html => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');
          const newContent = doc.getElementById('materi-list');
          materiList.innerHTML = newContent.innerHTML;
        });
    }
  }, 300);
});
</script>

<?php include "../../../includes/footer.php"; ?>
</body>
</html>
<?php ob_end_flush(); ?>