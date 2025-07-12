<?php
session_start();

$_SESSION = array();

session_destroy();

$_SESSION['message'] = "Anda telah berhasil logout.";
header("Location: login.php");
exit;
?>