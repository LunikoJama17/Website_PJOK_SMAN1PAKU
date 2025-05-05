<?php
session_start();
require_once "../../../database.php";
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'guru')) {
    header("Location: ../../../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_POST['hapus']);
    $cek = mysqli_query($conn, "SELECT * FROM materi WHERE id_materi='$id'");
    if (!$cek || mysqli_num_rows($cek) === 0) {
        echo 'error';
        exit();
    }
    echo (mysqli_query($conn, "DELETE FROM materi WHERE id_materi='$id'") ? 'success' : 'error');
    exit();
}

header("Location: index.php");
?>
