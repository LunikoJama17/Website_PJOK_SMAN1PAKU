<?php
ob_start();
session_start();
require_once "../../../database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../auth/login.php");
    exit();
}

// Redirect ke role guru jika tidak ada parameter role
if (!isset($_GET['role'])) {
    header("Location: index.php?role=guru");
    exit();
}

// Setup pagination
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$role = $_GET['role'];

// Hitung total rows
if ($role === 'guru') {
    $countSql = "SELECT COUNT(*) AS cnt FROM guru";
} else {
    $countSql = "SELECT COUNT(*) AS cnt FROM siswa";
}
$countRes = mysqli_query($conn, $countSql);
$totalRows = mysqli_fetch_assoc($countRes)['cnt'];
$totalPages = ceil($totalRows / $limit);

// Hapus pengguna via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'], $_POST['role'])) {
    ob_clean();
    $id = mysqli_real_escape_string($conn, $_POST['hapus']);
    $rolePost = $_POST['role'];

    if ($rolePost === 'guru') {
        $table = 'guru';
        $key = 'id_guru';
    } else {
        $table = 'siswa';
        $key = 'id_siswa';
    }
    $cek = mysqli_query($conn, "SELECT 1 FROM {$table} WHERE {$key}='{$id}'");
    if (mysqli_num_rows($cek) === 0) {
        echo 'error';
        exit();
    }
    $hapus = mysqli_query($conn, "DELETE FROM {$table} WHERE {$key}='{$id}'");
    echo $hapus ? 'success' : 'error';
    exit();
}

include "../../../includes/navbar_admin.php";

// Ambil data sesuai pagination
if ($role === 'guru') {
    $sql = "SELECT * FROM guru ORDER BY id_guru LIMIT {$limit} OFFSET {$offset}";
} else {
    $sql = "SELECT * FROM siswa ORDER BY id_siswa LIMIT {$limit} OFFSET {$offset}";
}
$res = mysqli_query($conn, $sql);
$pengguna = $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : [];
?>

<div class="container mx-auto mt-8 px-4">
    <h1 class="text-3xl font-semibold text-center mb-6">Kelola Pengguna</h1>

    <!-- Navigasi Role -->
    <div class="mb-6 flex justify-center space-x-4 p-2">
        <a href="?role=guru" class="px-6 py-3 text-white <?= ($role === 'guru') ? 'bg-indigo-600' : 'bg-gray-600'; ?> hover:bg-indigo-700 rounded-md">Guru</a>
        <a href="?role=siswa" class="px-6 py-3 text-white <?= ($role === 'siswa') ? 'bg-indigo-600' : 'bg-gray-600'; ?> hover:bg-indigo-700 rounded-md">Siswa</a>
    </div>

    <!-- Tombol Tambah -->
    <div class="mb-6 flex justify-center p-2">
        <a href="tambah.php?role=<?= $role ?>" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-md">+ Tambah <?= ucfirst($role) ?></a>
    </div>

    <?php if (!empty($pengguna)): ?>
        <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
            <table class="min-w-full bg-white border-collapse text-left text-gray-700">
                <thead>
                    <tr class="bg-gray-100 text-sm font-medium text-gray-600">
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Nama</th>
                        <?php if ($role === 'siswa'): ?><th class="py-3 px-4">Kelas</th><?php endif; ?>
                        <th class="py-3 px-4">Username</th>
                        <th class="py-3 px-4">Password</th>
                        <th class="py-3 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pengguna as $row): ?>
                        <?php
                            $id = ($role === 'guru') ? $row['id_guru'] : $row['id_siswa'];
                            $nama = ($role === 'guru') ? $row['nama_guru'] : $row['nama_siswa'];
                            $username = ($role === 'guru') ? $row['username_guru'] : $row['username_siswa'];
                            $password = ($role === 'guru') ? $row['password_guru'] : $row['password_siswa'];
                        ?>
                        <tr class="border-t border-gray-200">
                            <td class="py-3 px-4"><?= htmlspecialchars($id) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($nama) ?></td>
                            <?php if ($role === 'siswa'): ?><td class="py-3 px-4"><?= htmlspecialchars($row['kelas']) ?></td><?php endif; ?>
                            <td class="py-3 px-4"><?= htmlspecialchars($username) ?></td>
                            <td class="py-3 px-4"><?= htmlspecialchars($password) ?></td>
                            <td class="py-3 px-4">
                                <div class="flex flex-col md:flex-row gap-2">
                                    <a href="edit.php?id=<?= $id ?>&role=<?= $role ?>" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm">Edit</a>
                                    <button onclick="hapusPengguna('<?= $id ?>','<?= $role ?>')" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-sm">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="flex justify-center mt-6" aria-label="Pagination">
            <ul class="inline-flex -space-x-px text-sm">
                <?php if ($page > 1): ?>
                    <li>
                        <a href="?role=<?= $role ?>&page=<?= $page - 1 ?>" class="px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700">&laquo; Prev</a>
                    </li>
                <?php endif; ?>

                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);

                if ($start > 1) {
                    echo '<li><a href="?role=' . $role . '&page=1" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">1</a></li>';
                    if ($start > 2) echo '<li><span class="px-3 py-2 text-gray-500">...</span></li>';
                }

                for ($i = $start; $i <= $end; $i++): ?>
                    <li>
                        <a href="?role=<?= $role ?>&page=<?= $i ?>" class="px-3 py-2 leading-tight border border-gray-300 <?= ($i === $page) ? 'bg-indigo-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700'; ?>"><?= $i ?></a>
                    </li>
                <?php endfor;

                if ($end < $totalPages) {
                    if ($end < $totalPages - 1) echo '<li><span class="px-3 py-2 text-gray-500">...</span></li>';
                    echo '<li><a href="?role=' . $role . '&page=' . $totalPages . '" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">' . $totalPages . '</a></li>';
                }
                ?>

                <?php if ($page < $totalPages): ?>
                    <li>
                        <a href="?role=<?= $role ?>&page=<?= $page + 1 ?>" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700">Next &raquo;</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>

    <?php else: ?>
        <p class="text-gray-500 mt-4 text-center">Tidak ada data untuk ditampilkan.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function hapusPengguna(id, role) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Tindakan ini tidak bisa dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "hapus.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const res = xhr.responseText.trim();
                    if (res === 'success') {
                        Swal.fire('Berhasil!','Pengguna berhasil dihapus.','success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!','Pengguna gagal dihapus.','error');
                    }
                }
            };
            xhr.send(`hapus=${encodeURIComponent(id)}&role=${encodeURIComponent(role)}`);
        }
    });
}
</script>

<?php include "../../../includes/footer.php"; ?>
<?php ob_end_flush(); ?>
