<?php
session_start();
require_once "../../../database.php";
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'guru')) {
    header("Location: ../../../auth/login.php");
    exit();
}

$id = $_GET['id'];
$materi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM materi WHERE id=$id"));
unlink("../../../uploads/" . $materi['file']);
mysqli_query($conn, "DELETE FROM materi WHERE id=$id");
header("Location: index.php");
?>
