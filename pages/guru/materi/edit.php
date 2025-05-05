<?php
ob_start();
session_start();
require_once "../../../database.php";

// Pastikan hanya guru yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../../../auth/login.php");
    exit();
}

// validasi ID
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$id = mysqli_real_escape_string($conn, $_GET['id']);

// AJAX update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_clean();

    $sem    = in_array($_POST['semester'], ['1','2']) ? $_POST['semester'] : '1';
    $judul  = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi    = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $video  = mysqli_real_escape_string($conn, $_POST['video_materi']);
    $guru   = $_SESSION['id'];
    $now    = date('Y-m-d H:i:s');
    
    $sql = "UPDATE materi SET
        semester_materi = '$sem',
        judul_materi = '$judul',
        isi_materi = '$isi',
        video_materi = '$video',
        id_guru = '$guru',
        id_admin = NULL,
        pengubah_terakhir = '$guru',
        role_pengubah = 'guru',
        waktu_diubah = '$now'
    WHERE id_materi = '$id'";
    
    echo mysqli_query($conn, $sql) ? 'success' : 'error';
    exit();
}

// ambil data lama
$res = mysqli_query($conn, "SELECT * FROM materi WHERE id_materi='$id'");
if (!$res || mysqli_num_rows($res) === 0) {
    echo "<script>alert('Materi tidak ditemukan');location='index.php'</script>";
    exit();
}
$materi = mysqli_fetch_assoc($res);

include "../../../includes/navbar_guru.php";
?>

<div class="max-w-2xl mx-auto mt-8 bg-white p-6 rounded-lg shadow">
  <h3 class="text-3xl font-semibold mb-4">Edit Materi</h3>
  <form id="form-edit" class="space-y-4">
    <input type="hidden" name="id_materi" value="<?= htmlspecialchars($materi['id_materi']) ?>">
    <div>
      <label class="block mb-1">Judul Materi</label>
      <input type="text" name="judul" required
             value="<?= htmlspecialchars($materi['judul_materi']) ?>"
             class="w-full border px-3 py-2 rounded">
    </div>
    <div>
      <label class="block mb-1">Semester</label>
      <select name="semester" required class="w-full border px-3 py-2 rounded">
        <option value="1" <?= $materi['semester_materi']=='1'?'selected':'' ?>>Semester 1</option>
        <option value="2" <?= $materi['semester_materi']=='2'?'selected':'' ?>>Semester 2</option>
      </select>
    </div>
    <div>
      <label class="block mb-1">Isi Materi</label>
      <textarea name="deskripsi" rows="4" required
                class="w-full border px-3 py-2 rounded"><?= htmlspecialchars($materi['isi_materi']) ?></textarea>
    </div>
    <div>
      <label class="block mb-1">URL Video Materi</label>
      <input type="url" name="video_materi"
             value="<?= htmlspecialchars($materi['video_materi']) ?>"
             class="w-full border px-3 py-2 rounded">
    </div>

    <!-- Tombol Update & Batal -->
    <div class="flex space-x-2">
      <button type="submit"
              class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
        Update Materi
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
  // AJAX submit form
  document.querySelector('#form-edit').addEventListener('submit', e => {
    e.preventDefault();
    const data = new URLSearchParams(new FormData(e.target));
    fetch('edit.php?id=' + encodeURIComponent(data.get('id_materi')), {
      method: 'POST',
      body: data
    })
    .then(r => r.text())
    .then(txt => {
      if (txt.trim() === 'success') {
        Swal.fire('Berhasil!', 'Materi diperbarui.', 'success')
          .then(() => location.href = 'index.php?semester=' + data.get('semester'));
      } else {
        Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
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
