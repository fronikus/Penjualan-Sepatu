<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../koneksi.php'; // Path relatif dari folder actions/

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

    if ($product_id > 0 && $quantity >= 1) {
        // Cek stok produk dari database
        $query = "SELECT stok FROM produk WHERE id = ?";
        if ($stmt = $koneksi->prepare($query)) {
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product_db = $result->fetch_assoc(); // Menggunakan nama variabel berbeda
            $stmt->close();

            if ($product_db) {
                $available_stock = $product_db['stok'];
                if ($quantity <= $available_stock) {
                    if (isset($_SESSION['cart'][$product_id])) {
                        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                        $response['success'] = true;
                        $response['message'] = 'Kuantitas berhasil diperbarui.';
                    } else {
                        $response['message'] = 'Produk tidak ditemukan di keranjang.';
                    }
                } else {
                    $response['message'] = 'Stok tidak mencukupi. Stok tersedia: ' . $available_stock;
                }
            } else {
                $response['message'] = 'Produk tidak ditemukan di database.';
            }
        } else {
            $response['message'] = 'Error saat menyiapkan kueri stok: ' . $koneksi->error;
        }
        $koneksi->close();
    } else {
        $response['message'] = 'Input tidak valid.';
    }
} else {
    $response['message'] = 'Permintaan tidak valid.';
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>