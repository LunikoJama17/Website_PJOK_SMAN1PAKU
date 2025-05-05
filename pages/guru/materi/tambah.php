<?php
ob_start();
session_start();
require_once "../../../database.php";

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','guru'])) {
    header("Location: ../../../auth/login.php");
    exit();
}

// Tangani AJAX simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // bersihkan output sebelumnya
    ob_clean();

    $id     = mysqli_real_escape_string($conn, $_POST['id_materi']);
    $sem    = in_array($_POST['semester'], ['1','2']) ? $_POST['semester'] : '1';
    $judul  = mysqli_real_escape_string($conn, $_POST['judul_materi']);
    $isi    = mysqli_real_escape_string($conn, $_POST['isi_materi']);
    $video  = mysqli_real_escape_string($conn, $_POST['video_materi']);
    $guru   = $_SESSION['id'];

    if (!preg_match("/^MTR\d+$/", $id)) {
        echo 'format_error';
        exit();
    }

    $sql = "INSERT INTO materi
        (id_materi, semester_materi, judul_materi, isi_materi, video_materi, id_guru)
        VALUES
        ('$id','$sem','$judul','$isi','$video','$guru')";

    echo mysqli_query($conn, $sql) ? 'success' : 'error';
    exit();
}

include "../../../includes/navbar_guru.php";
?>
<div class="max-w-2xl mx-auto mt-8 bg-white p-6 rounded-lg shadow">
  <h2 class="text-2xl font-semibold mb-4">Tambah Materi</h2>
  <form id="form-tambah">
    <div class="mb-4">
      <label class="block mb-1">ID Materi</label>
      <input type="text" name="id_materi" required class="w-full border px-3 py-2 rounded" placeholder="MTR001">
    </div>
    <div class="mb-4">
      <label class="block mb-1">Judul Materi</label>
      <input type="text" name="judul_materi" required class="w-full border px-3 py-2 rounded">
    </div>
    <div class="mb-4">
      <label class="block mb-1">Semester</label>
      <select name="semester" required class="w-full border px-3 py-2 rounded">
        <option value="1">Semester 1</option>
        <option value="2">Semester 2</option>
      </select>
    </div>
    <div class="mb-4">
      <label class="block mb-1">Isi Materi</label>
      <textarea name="isi_materi" rows="4" required class="w-full border px-3 py-2 rounded"></textarea>
    </div>
    <div class="mb-4">
      <label class="block mb-1">URL Video Materi</label>
      <input type="url" name="video_materi" class="w-full border px-3 py-2 rounded" placeholder="https://youtu.be/...">
    </div>

    <!-- Tombol Simpan & Batal -->
    <div class="flex space-x-2">
      <button type="submit"
              class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded">
        Simpan
      </button>
      <button type="button" id="btn-batal"
              class="flex-1 bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded">
        Batal
      </button>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelector('#form-tambah').addEventListener('submit', e => {
  e.preventDefault();
  const data = new URLSearchParams(new FormData(e.target));
  fetch('tambah.php', {
    method: 'POST',
    body: data
  })
  .then(r => r.text())
  .then(txt => {
    switch(txt.trim()) {
      case 'success':
        Swal.fire('Berhasil!','Materi ditambahkan.','success')
          .then(()=> location.href='index.php?semester='+data.get('semester'));
        break;
      case 'format_error':
        Swal.fire('Format Salah','ID harus MTR diikuti angka.','warning');
        break;
      default:
        Swal.fire('Gagal!','Terjadi kesalahan.','error');
    }
  });
});

// Tombol Batal: kembali ke daftar materi dengan filter semester
document.querySelector('#btn-batal').addEventListener('click', () => {
  const sem = document.querySelector('select[name="semester"]').value;
  location.href = 'index.php?semester=' + sem;
});
</script>

<?php
include "../../../includes/footer.php";
ob_end_flush();
?>
