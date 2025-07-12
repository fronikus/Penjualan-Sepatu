<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$cart_count = 0;
if (isset($_SESSION['cart'])) {
    $cart_count = count($_SESSION['cart']); // Menghitung jumlah item unik
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'cart_count' => $cart_count]);
exit;
?>