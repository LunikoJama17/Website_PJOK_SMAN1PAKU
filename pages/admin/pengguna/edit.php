<?php
// Mulai Output Buffering
ob_start();

// Mulai session
session_start();
require_once "../../../database.php";

// Cek apakah yang login adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../auth/login.php");
    exit();
}

include "../../../includes/navbar_admin.php";

// Pastikan parameter id dan role ada di URL
if (!isset($_GET['id']) || !isset($_GET['role'])) {
    echo "<div class='alert alert-danger'>ID atau Role tidak ditemukan dalam URL.</div>";
    exit();
}

$id   = mysqli_real_escape_string($conn, $_GET['id']);
$role = $_GET['role'];

// Ambil data pengguna berdasarkan ID dan role
if ($role === 'guru') {
    $res      = mysqli_query($conn, "SELECT * FROM guru WHERE id_guru='{$id}'");
    $pengguna = mysqli_fetch_assoc($res);
} else {
    $res      = mysqli_query($conn, "SELECT * FROM siswa WHERE id_siswa='{$id}'");
    $pengguna = mysqli_fetch_assoc($res);
}

if (!$pengguna) {
    echo "<div class='alert alert-danger'>Pengguna tidak ditemukan</div>";
    exit();
}

// Jika tombol update ditekan
if (isset($_POST['update'])) {
    $nama     = htmlspecialchars($_POST['nama']);
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    if ($role === 'guru') {
        $query = "UPDATE guru SET nama_guru='$nama', username_guru='$username', password_guru='$password' WHERE id_guru='$id'";
    } else {
        $kelas = htmlspecialchars($_POST['kelas']);
        $query = "UPDATE siswa SET nama_siswa='$nama', kelas='$kelas', username_siswa='$username', password_siswa='$password' WHERE id_siswa='$id'";
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>
        Swal.fire({
            title: 'Berhasil!',
            text: 'Data pengguna berhasil diperbarui.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => { window.location.href = 'index.php?role={$role}'; });
        </script>";
    } else {
        echo "<script>
        Swal.fire({
            title: 'Gagal!',
            text: 'Gagal memperbarui data.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        </script>";
    }
}
?>

<div class="container mx-auto mt-8 px-4">
    <h3 class="text-3xl font-semibold text-gray-800 mb-6">Edit Pengguna</h3>
    <form method="POST" class="space-y-6">
        <div>
            <label class="block text-lg font-medium text-gray-700">ID</label>
            <input type="text" name="id" value="<?= htmlspecialchars(
                $pengguna['id_guru'] ?? $pengguna['id_siswa']
            ) ?>" readonly
                class="mt-1 p-3 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600">
        </div>

        <div>
            <label class="block text-lg font-medium text-gray-700">Nama</label>
            <input type="text" name="nama" value="<?= htmlspecialchars(
                $pengguna['nama_guru'] ?? $pengguna['nama_siswa']
            ) ?>" required
                class="mt-1 p-3 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600">
        </div>

        <?php if ($role === 'siswa'): ?>
        <div>
            <label class="block text-lg font-medium text-gray-700">Kelas</label>
            <select name="kelas" required
                    class="mt-1 p-3 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600">
                <option value="X A" <?= ($pengguna['kelas'] === 'X A') ? 'selected' : '' ?>>X A</option>
                <option value="X B" <?= ($pengguna['kelas'] === 'X B') ? 'selected' : '' ?>>X B</option>
            </select>
        </div>
        <?php endif; ?>

        <div>
            <label class="block text-lg font-medium text-gray-700">Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars(
                $pengguna['username_guru'] ?? $pengguna['username_siswa']
            ) ?>" required
                class="mt-1 p-3 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600">
        </div>

        <!-- Password with show/hide -->
        <div class="mb-4">
            <label class="block text-lg font-medium text-gray-700 mb-1">Password</label>
            <div class="relative mt-1">
                <input type="password" name="password" id="password" value="<?= htmlspecialchars(
                    $pengguna['password_guru'] ?? $pengguna['password_siswa']
                ) ?>" required
                    class="w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 pr-10">
                <button type="button" onclick="togglePassword()"
                        class="absolute inset-y-0 right-3 flex items-center justify-center text-gray-600 hover:text-gray-900 focus:outline-none">
                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-.275.884-.67 1.718-1.175 2.478M15.5 16.5L19 20M9 16.5L5 20" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex space-x-4">
            <button type="submit" name="update"
                    class="flex-1 bg-indigo-600 text-white p-3 rounded-md shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                Simpan
            </button>
            <button type="button" onclick="window.location.href='index.php?role=<?= $role ?>'"
                    class="flex-1 bg-red-500 text-white p-3 rounded-md shadow-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-600">
                Batal
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Toggle show/hide password
function togglePassword() {
    const pwd = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    const isHidden = pwd.type === 'password';
    pwd.type = isHidden ? 'text' : 'password';
    icon.innerHTML = isHidden
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.184-3.293m3.186-2.191A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.956 9.956 0 01-1.23 2.592M15 12a3 3 0 11-6 0 3 3 0 016 0zM3 3l18 18" />'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-.275.884-.67 1.718-1.175 2.478M15.5 16.5L19 20M9 16.5L5 20" />';
}
</script>

<?php include "../../../includes/footer.php"; ?>

<?php
// Akhiri Output Buffering
ob_end_flush();
?>
