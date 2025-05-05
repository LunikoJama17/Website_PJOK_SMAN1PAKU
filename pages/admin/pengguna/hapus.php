<?php
session_start();
require_once "../../../database.php";

// Aktifkan error reporting mysqli untuk debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Hanya admin yang boleh menghapus
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo 'error';
    exit();
}

// Pastikan data AJAX terkirim
if (empty($_POST['hapus']) || empty($_POST['role'])) {
    http_response_code(400);
    echo 'error';
    exit();
}

// Sanitasi input
$id   = mysqli_real_escape_string($conn, $_POST['hapus']);
$role = $_POST['role'];

// Tentukan tabel dan kolom berdasarkan role
if ($role === 'guru') {
    $table  = 'guru';
    $column = 'id_guru';
} elseif ($role === 'siswa') {
    $table  = 'siswa';
    $column = 'id_siswa';
} else {
    http_response_code(400);
    echo 'error';
    exit();
}

// Siapkan query DELETE
$sql = "DELETE FROM `{$table}` WHERE `{$column}` = '{$id}'";

try {
    // Nonaktifkan cek foreign key sementara jika ada constraints
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

    // Eksekusi DELETE
    $res = mysqli_query($conn, $sql);

    // Aktifkan kembali foreign key checks
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

    // Berikan respon sukses jika query berhasil dijalankan
    echo 'success';
} catch (Exception $e) {
    // Pastikan foreign key checks kembali aktif meski error
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
    error_log('Delete Error: ' . $e->getMessage());
    http_response_code(500);
    echo 'error';
} catch (Error $e) {
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
    error_log('Delete Error: ' . $e->getMessage());
    http_response_code(500);
    echo 'error';
} // end of Error catch

exit();
?>
