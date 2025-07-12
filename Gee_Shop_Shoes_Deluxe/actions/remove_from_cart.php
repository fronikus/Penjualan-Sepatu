<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

    if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]); // Hapus item dari sesi keranjang
        // Redirect kembali ke halaman keranjang
        header('Location: ../keranjang.php');
        exit;
    }
}
// Jika request tidak valid atau item tidak ditemukan, redirect saja
header('Location: ../keranjang.php');
exit;
?>