<?php
$host = "localhost";
$user = "root";
$pass = "";
$db_name = "geeshop_db";

$koneksi = new mysqli($host, $user, $pass, $db_name);

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$koneksi->set_charset("utf8mb4");

function escape_string($koneksi, $string) {
    return $koneksi->real_escape_string($string);
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>