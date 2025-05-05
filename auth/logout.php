
<?php
session_start();
include "../includes/alerts.php";
session_destroy();
showSweetAlert('success', 'Logout Berhasil', 'Sampai jumpa lagi!', 'login.php');
?>
