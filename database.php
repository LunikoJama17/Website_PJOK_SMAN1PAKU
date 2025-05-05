<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "database_sman_1_paku"; // Sesuaikan dengan database kamu

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}
?>
