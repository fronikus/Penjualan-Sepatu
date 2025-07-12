<?php
// Pastikan sesi dimulai di setiap file yang menggunakan $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Sertakan file koneksi database
include '../koneksi.php'; // Sesuaikan path jika koneksi.php berada di direktori yang berbeda

$response = ['success' => false, 'message' => '', 'cart_count' => 0];

// Pastikan request adalah POST dan ada data product_id
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1; // Default 1

    if ($product_id <= 0 || $quantity < 1) {
        $response['message'] = 'ID produk atau kuantitas tidak valid.';
        echo json_encode($response);
        exit;
    }

    // Ambil detail produk (termasuk harga dan nama) langsung dari database
    $stmt = $koneksi->prepare("SELECT id, nama, harga, gambar FROM produk WHERE id = ?");
    
    // Periksa jika prepare gagal
    if ($stmt === false) {
        $response['message'] = "Database error (prepare): " . $koneksi->error;
        echo json_encode($response);
        $koneksi->close();
        exit;
    }

    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close(); // Tutup statement setelah digunakan

    if ($product) {
        // Ambil harga dan nama dari database, bukan dari POST
        $db_product_name = $product['nama'];
        $db_product_price = $product['harga'];
        $db_product_image = $product['gambar']; // Ambil juga gambar jika ingin ditampilkan di keranjang

        // Inisialisasi keranjang jika belum ada
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Jika produk sudah ada di keranjang, tingkatkan kuantitasnya
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            // Jika produk belum ada, tambahkan ke keranjang
            $_SESSION['cart'][$product_id] = [
                'name' => $db_product_name,
                'price' => $db_product_price,
                'quantity' => $quantity,
                'image' => $db_product_image // Tambahkan gambar
            ];
        }

        $response['success'] = true;
        $response['message'] = 'Produk berhasil ditambahkan ke keranjang!';
        $response['cart_count'] = count($_SESSION['cart']); // Kirim jumlah item unik di keranjang
    } else {
        $response['message'] = 'Produk tidak ditemukan.';
    }
} else {
    $response['message'] = 'Permintaan tidak valid.';
}

$koneksi->close(); // Tutup koneksi database

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>