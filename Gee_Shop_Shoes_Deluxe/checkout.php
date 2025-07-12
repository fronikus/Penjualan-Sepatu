<?php
session_start(); // Pastikan ini ada di baris pertama
include 'koneksi.php'; // Menggunakan include sesuai permintaan Anda untuk versi original

// Tampilkan pesan error/sukses
if (isset($_SESSION['message'])) {
    echo '<div style="color: red; background-color: #ffe0e0; padding: 10px; border: 1px solid red; margin-bottom: 20px; text-align: center; border-radius: 5px;">' . htmlspecialchars($_SESSION['message']) . '</div>';
    unset($_SESSION['message']);
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Anda harus login untuk melanjutkan checkout.";
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['cart'])) {
    $_SESSION['message'] = "Keranjang belanja Anda kosong. Tidak dapat melakukan checkout.";
    header("Location: keranjang.php");
    exit();
}

$cart_items = [];
$total_price = 0;

$product_ids = implode(",", array_keys($_SESSION['cart']));
// REVISI PENTING DI SINI: Ubah nama kolom di SELECT query
// Menggunakan nama kolom yang benar dari database Anda: id, nama, harga, gambar
$query = "SELECT id, nama, harga, gambar FROM produk WHERE id IN ($product_ids)";
$result = $koneksi->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $product_id = $row['id'];
        $quantity = $_SESSION['cart'][$product_id]['quantity'];
        
        // REVISI PENTING DI SINI: Akses kolom menggunakan nama yang benar dari database
        $harga_produk_db = $row['harga']; // Menggunakan 'harga' bukan 'price'
        if (!is_numeric($harga_produk_db)) {
            $_SESSION['message'] = "Data harga produk tidak valid untuk produk ID: " . htmlspecialchars($product_id);
            header("Location: keranjang.php");
            exit();
        }

        $item_total = $harga_produk_db * $quantity;
        $total_price += $item_total;

        $cart_items[] = [
            'id' => $row['id'],
            'name' => $row['nama'],    // Menggunakan 'nama' bukan 'name'
            'image' => $row['gambar'], // Menggunakan 'gambar' bukan 'image'
            'price' => $harga_produk_db, 
            'quantity' => $quantity,
            'subtotal' => $item_total
        ];
    }
} else {
    $_SESSION['message'] = "Gagal mengambil data produk dari database: " . htmlspecialchars($koneksi->error);
    header("Location: keranjang.php");
    exit();
}

$pageTitle = "Checkout - Gee Shop";
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
                <li><a href="index.php#produk">Produk</a></li> <li><a href="index.php#about">Tentang Kami</a></li>
                <li><a href="index.php#contact">Kontak</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
            <div class="nav-icons">
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
        <h1>Selesaikan Pembelian Anda</h1>

        <div class="checkout-container">
            <div class="shipping-info">
                <h3>Informasi Pengiriman</h3>
                <form action="actions/process_checkout.php" method="POST" class="checkout-form">
                    <div class="form-group">
                        <label for="full_name">Nama Lengkap:</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Alamat Pengiriman:</label>
                        <textarea id="address" name="address" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="phone">Nomor Telepon:</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>

                    <h3>Metode Pembayaran</h3>
                    <div class="form-group payment-method">
                        <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer" checked>
                        <label for="bank_transfer">Transfer Bank</label>
                    </div>
                    <div class="form-group payment-method">
                        <input type="radio" id="credit_card" name="payment_method" value="credit_card"> <label for="credit_card"><i class="fas fa-credit-card"></i> Kartu Kredit</label>
                    </div>
                    <div class="form-group payment-method">
                        <input type="radio" id="e_wallet" name="payment_method" value="e_wallet"> <label for="e_wallet"><i class="fas fa-wallet"></i> E-Wallet (Segera Hadir)</label>
                    </div>

                    <div class="order-summary-small">
                        <h4>Ringkasan Pesanan Anda</h4>
                        <ul>
                            <?php foreach ($cart_items as $item): ?>
                                <li>
                                    <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo htmlspecialchars($item['quantity']); ?></span>
                                    <span>Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></span>
                                </li>
                            <?php endforeach; ?>
                            <li class="total-row">
                                <span>Total Belanja:</span>
                                <span>Rp <?php echo number_format($total_price, 0, ',', '.'); ?></span>
                            </li>
                        </ul>
                    </div>

                    <button type="submit" class="btn-primary">Konfirmasi Pesanan</button>
                </form>
            </div>

            <div class="order-summary-large">
                <h3>Ringkasan Pesanan</h3>
                <div class="summary-details">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="summary-item">
                            <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div class="item-info">
                                <p class="item-name"><?php echo htmlspecialchars($item['name']); ?></p>
                                <p class="item-qty">Jumlah: <?php echo htmlspecialchars($item['quantity']); ?></p>
                            </div>
                            <p class="item-price">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></p>
                        </div>
                    <?php endforeach; ?>
                    <div class="summary-total">
                        <span>Total Pembayaran:</span>
                        <span class="total-amount">Rp <?php echo number_format($total_price, 0, ',', '.'); ?></span>
                    </div>
                </div>
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
            // Hapus atau komentari seluruh blok ini jika ada:
            /*
            const searchLink = document.getElementById('searchLink');
            if (searchLink) {
                searchLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    const searchQuery = prompt("Masukkan kata kunci pencarian:");
                    if (searchQuery !== null && searchQuery.trim() !== "") {
                        window.location.href = "search.php?query=" + encodeURIComponent(searchQuery.trim());
                    }
                });
            }
            */
        });
    </script>
</body>
</html>