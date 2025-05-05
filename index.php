<?php
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: pages/admin/dashboard.php");
    } elseif ($_SESSION['role'] == 'guru') {
        header("Location: pages/guru/dashboard.php");
    } elseif ($_SESSION['role'] == 'siswa') {
        header("Location: pages/siswa/dashboard.php");
    }
    exit();
}
header("Location: auth/login.php");
exit();
?>
