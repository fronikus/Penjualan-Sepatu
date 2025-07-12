<?php
include 'koneksi.php';

if (!isset($_GET['order_id']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

$order_details = null;
$order_items = [];

$stmt_order = $koneksi->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt_order->bind_param("ii", $order_id, $user_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();
$order_details = $result_order->fetch_assoc();

if (!$order_details) {
    $_SESSION['message'] = "Pesanan tidak ditemukan atau Anda tidak memiliki akses.";
    header("Location: index.php");
    exit();
}

$stmt_items = $koneksi->prepare("SELECT product_name, quantity, price_per_unit FROM order_items WHERE order_id = ?");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
while ($row_item = $result_items->fetch_assoc()) {
    $order_items[] = $row_item;
}

$pageTitle = "Konfirmasi Pesanan - Gee Shop";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <a href="index.php">Gee <span class="highlight">Shop</span></a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php#home">Beranda</a></li>
                <li><a href="index.php#products">Produk</a></li>
                <li><a href="index.php#about">Tentang Kami</a></li>
                <li><a href="index.php#contact">Kontak</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
            <div class="nav-icons">
                <a href="#" id="searchLink" aria-label="Cari Produk"><i class="fas fa-search"></i></a>
                <a href="keranjang.php" id="cartLink" aria-label="Keranjang Belanja"><i class="fas fa-shopping-cart"></i><span class="cart-count">0</span></a>
            </div>
            <div class="burger" aria-label="Menu Navigasi Mobile">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </div>
        </nav>
    </header>

    <main class="page-wrapper">
        <h1>Pesanan Anda Berhasil!</h1>
        <p class="success-message">Terima kasih atas pesanan Anda. Detail pesanan Anda adalah sebagai berikut:</p>

        <div class="order-confirmation-details">
            <div class="detail-block">
                <h3>Detail Pesanan #<?php echo htmlspecialchars($order_details['id']); ?></h3>
                <p><strong>Tanggal Pesanan:</strong> <?php echo date('d M Y H:i', strtotime($order_details['order_date'])); ?></p>
                <p><strong>Status:</strong> <span style="color: <?php echo ($order_details['order_status'] == 'Pending' ? '#f39c12' : ($order_details['order_status'] == 'Completed' ? '#2ecc71' : '#e74c3c')); ?>; font-weight: bold;"><?php echo htmlspecialchars($order_details['order_status']); ?></span></p>
                <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $order_details['payment_method']))); ?></p>
                <p><strong>Total Pembayaran:</strong> Rp <?php echo number_format($order_details['total_amount'], 0, ',', '.'); ?></p>
            </div>

            <div class="detail-block">
                <h3>Informasi Pengiriman</h3>
                <p><strong>Nama Penerima:</strong> <?php echo htmlspecialchars($order_details['full_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order_details['email']); ?></p>
                <p><strong>Telepon:</strong> <?php echo htmlspecialchars($order_details['phone']); ?></p>
                <p><strong>Alamat:</strong> <?php echo nl2br(htmlspecialchars($order_details['address'])); ?></p>
            </div>

            <div class="detail-block full-width">
                <h3>Item Pesanan</h3>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td>Rp <?php echo number_format($item['price_per_unit'], 0, ',', '.'); ?></td>
                                <td>Rp <?php echo number_format($item['quantity'] * $item['price_per_unit'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p style="text-align: center; margin-top: 40px;">
                Kami akan segera memproses pesanan Anda. Silakan cek email Anda untuk detail lebih lanjut dan instruksi pembayaran.
            </p>
            <div style="text-align: center; margin-top: 30px;">
                <a href="index.php" class="btn-primary">Kembali ke Beranda</a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Gee Shop. Hak Cipta Dilindungi.</p>
        <div class="social-icons">
            <a href="#" aria-label="Instagram Gee Shop"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="Facebook Gee Shop"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="Twitter Gee Shop"><i class="fab fa-twitter"></i></a>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }
        });
    </script>
</body>
</html>