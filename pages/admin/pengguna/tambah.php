<?php
// Mulai Output Buffering
ob_start();

// Mulai session
session_start();
require_once "../../../database.php";
include "../../../includes/alerts.php";  // Menyertakan file SweetAlert function

// Cek apakah yang login adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../auth/login.php");
    exit();
}

// Ambil role dari parameter GET, default ke 'guru'
$role = isset($_GET['role']) && in_array($_GET['role'], ['guru', 'siswa']) ? $_GET['role'] : 'guru';

include "../../../includes/navbar_admin.php";

// Jika tombol simpan ditekan
if (isset($_POST['simpan'])) {
    $id       = htmlspecialchars($_POST['id']);
    $nama     = htmlspecialchars($_POST['nama']);
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $rolePost = htmlspecialchars($_POST['role']);

    if ($rolePost === 'guru') {
        $query  = "INSERT INTO guru (id_guru, nama_guru, username_guru, password_guru) 
                   VALUES ('$id', '$nama', '$username', '$password')";
        $result = mysqli_query($conn, $query);
    } elseif ($rolePost === 'siswa') {
        $kelas  = htmlspecialchars($_POST['kelas']);
        $query  = "INSERT INTO siswa (id_siswa, nama_siswa, kelas, username_siswa, password_siswa) 
                   VALUES ('$id', '$nama', '$kelas', '$username', '$password')";
        $result = mysqli_query($conn, $query);
    }

    if ($result) {
        showSweetAlert('success', 'Tambah Pengguna Berhasil', 'Pengguna baru telah berhasil ditambahkan.', 'index.php?role=' . $rolePost);
    } else {
        showSweetAlert('error', 'Tambah Pengguna Gagal', 'Terjadi kesalahan saat menambah pengguna.', 'tambah.php?role=' . $role);
    }
}
?>

<div class="container mx-auto mt-8 px-4">
    <h3 class="text-3xl font-semibold text-gray-800 mb-6">Tambah Pengguna</h3>
    <form method="POST" id="form-tambah" class="space-y-6">
        <div>
            <label for="id" class="block text-lg font-medium text-gray-700">ID Pengguna</label>
            <input type="text" name="id" id="id" placeholder="ID Pengguna (Misal: G001 atau S001)" required
                   class="mt-1 p-3 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600">
        </div>

        <div>
            <label for="nama" class="block text-lg font-medium text-gray-700">Nama</label>
            <input type="text" name="nama" id="nama" placeholder="Nama" required
                   class="mt-1 p-3 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600">
        </div>

        <div>
            <label for="username" class="block text-lg font-medium text-gray-700">Username</label>
            <input type="text" name="username" id="username" placeholder="Username" required
                   class="mt-1 p-3 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600">
        </div>

        <!-- Input Password dengan toggle show/hide -->
        <div class="mb-4">
            <label for="password" class="block text-lg font-medium text-gray-700 mb-1">Password</label>
            <div class="relative mt-1">
                <input type="password" name="password" id="password" placeholder="Password" required
                       class="w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 pr-10">
                <button type="button" onclick="togglePassword()"
                        class="absolute inset-y-0 right-3 flex items-center justify-center text-gray-600 hover:text-gray-900 focus:outline-none">
                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-.275.884-.67 1.718-1.175 2.478M15.5 16.5L19 20M9 16.5L5 20" />
                    </svg>
                </button>
            </div>
        </div>

        <div>
            <label for="role" class="block text-lg font-medium text-gray-700">Role</label>
            <select name="role" id="role" required
                    class="mt-1 p-3 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600">
                <option value="guru" <?php echo ($role === 'guru') ? 'selected' : ''; ?>>Guru</option>
                <option value="siswa" <?php echo ($role === 'siswa') ? 'selected' : ''; ?>>Siswa</option>
            </select>
        </div>

        <div id="kelas-container" class="mt-3" style="display:<?php echo ($role === 'siswa') ? 'block' : 'none'; ?>;">
            <label for="kelas" class="block text-lg font-medium text-gray-700">Kelas</label>
            <select name="kelas" id="kelas"
                    class="mt-1 p-3 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-600">
                <option value="X A">X A</option>
                <option value="X B">X B</option>
            </select>
        </div>

        <div class="flex space-x-4">
            <button type="submit" name="simpan"
                    class="flex-1 bg-indigo-600 text-white p-3 rounded-md shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                Simpan
            </button>
            <button type="button" onclick="window.location.href='index.php?role=' + document.getElementById('role').value"
                    class="flex-1 bg-red-500 text-white p-3 rounded-md shadow-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-600">
                Batal
            </button>
        </div>
    </form>
</div>

<script>
// Toggle kelas untuk siswa
const roleSelect = document.getElementById('role');
const kelasContainer = document.getElementById('kelas-container');

roleSelect.addEventListener('change', function() {
    kelasContainer.style.display = this.value === 'siswa' ? 'block' : 'none';
});

// Toggle show/hide password
function togglePassword() {
    const pwd = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    const isHidden = pwd.type === 'password';
    pwd.type = isHidden ? 'text' : 'password';
    if (isHidden) {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.184-3.293m3.186-2.191A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.956 9.956 0 01-1.23 2.592M15 12a3 3 0 11-6 0 3 3 0 016 0zM3 3l18 18" />';
    } else {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-.275.884-.67 1.718-1.175 2.478M15.5 16.5L19 20M9 16.5L5 20" />';
    }
}
</script>

<?php include "../../../includes/footer.php"; ?>

<?php
// Akhiri Output Buffering
ob_end_flush();
?>
