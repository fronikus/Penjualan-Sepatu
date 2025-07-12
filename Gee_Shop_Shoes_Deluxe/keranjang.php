<?php
// Pastikan sesi dimulai di awal file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'koneksi.php'; // Memanggil file koneksi database Anda

$cart_items = []; // Inisialisasi array untuk menampung item keranjang

// Memeriksa apakah ada item di keranjang dalam sesi
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']); // Mengambil ID produk dari sesi keranjang
    
    // Membuat placeholder untuk kueri SQL
    // Contoh: (?), (?), (?) untuk 3 ID produk
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));

    // Kueri SQL untuk mengambil detail produk dari database berdasarkan ID
    $query = "SELECT id, nama, harga, gambar, stok FROM produk WHERE id IN ($placeholders)";
    
    // Menyiapkan statement untuk menghindari SQL injection
    if ($stmt = $koneksi->prepare($query)) {
        // Membuat string tipe data untuk bind_param (semua ID adalah integer 'i')
        $types = str_repeat('i', count($product_ids));
        
        // Mengikat parameter ID produk ke statement
        $stmt->bind_param($types, ...$product_ids);
        
        // Menjalankan kueri
        $stmt->execute();
        
        // Mendapatkan hasil kueri
        $result = $stmt->get_result();
        
        // Mengambil data produk dan menggabungkannya dengan kuantitas dari sesi
        while ($product = $result->fetch_assoc()) {
            $product_id = $product['id'];
            $quantity = $_SESSION['cart'][$product_id]['quantity'];
            
            // Tambahkan item ke array $cart_items
            $cart_items[$product_id] = [
                'id' => $product_id,
                'nama' => $product['nama'],
                'harga' => $product['harga'],
                'gambar' => $product['gambar'],
                'stok' => $product['stok'], // Stok dari database
                'quantity' => $quantity // Kuantitas dari sesi keranjang
            ];
        }
        $stmt->close();
    } else {
        // Menampilkan error jika persiapan statement gagal
        echo "<p style='color: red; text-align: center;'>Error persiapan kueri: " . $koneksi->error . "</p>";
    }
}

$pageTitle = "Keranjang Belanja - Gee Shop";
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
                <li><a href="index.php#produk">Produk</a></li>
                <li><a href="index.php#about">Tentang Kami</a></li>
                <li><a href="index.php#contact">Kontak</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
            <div class="nav-icons">
                <a href="keranjang.php" id="cartLink" aria-label="Keranjang Belanja">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">
                        <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                    </span>
                </a>
            </div>
            <div class="burger" aria-label="Menu Navigasi Mobile">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </div>
        </nav>
    </header>

    <main class="cart-page-main">
        <section class="cart-section">
            <h2>Keranjang Belanja Anda</h2>
            <?php if (empty($cart_items)): ?>
                <p class="empty-cart-message">Keranjang Anda kosong. Yuk, jelajahi produk kami!</p>
                <div class="empty-cart-actions">
                    <a href="index.php#produk" class="btn-primary">Mulai Belanja</a>
                </div>
            <?php else: ?>
                <div class="cart-container">
                    <div class="cart-items-list">
                        <?php
                        $total_harga = 0;
                        foreach ($cart_items as $item):
                            $subtotal = $item['harga'] * $item['quantity'];
                            $total_harga += $subtotal;
                        ?>
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <img src="images/<?php echo htmlspecialchars($item['gambar']); ?>" alt="<?php echo htmlspecialchars($item['nama']); ?>">
                            </div>
                            <div class="cart-item-details">
                                <h3><?php echo htmlspecialchars($item['nama']); ?></h3>
                                <p class="price">Harga: Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></p>
                                <form action="actions/update_cart_quantity.php" method="POST" class="quantity-form">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                    <label for="quantity-<?php echo htmlspecialchars($item['id']); ?>">Kuantitas:</label>
                                    <div class="quantity-control">
                                        <button type="button" class="quantity-btn decrease-quantity" data-id="<?php echo htmlspecialchars($item['id']); ?>">-</button>
                                        <input type="number"
                                            id="quantity-<?php echo htmlspecialchars($item['id']); ?>"
                                            name="quantity"
                                            value="<?php echo htmlspecialchars($item['quantity']); ?>"
                                            min="1"
                                            max="<?php echo htmlspecialchars($item['stok']); ?>" class="quantity-input">
                                        <button type="button" class="quantity-btn increase-quantity" data-id="<?php echo htmlspecialchars($item['id']); ?>">+</button>
                                    </div>
                                    <p class="subtotal">Subtotal: Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></p>
                                    </form>
                                <form action="actions/remove_from_cart.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                    <button type="submit" name="remove_item" class="btn-remove">Hapus</button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="cart-summary">
                        <h3>Ringkasan Belanja</h3>
                        <div class="summary-details">
                            <p>Total Item: <span><?php echo count($cart_items); ?></span></p>
                            <p>Total Harga: <span>Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></span></p>
                        </div>
                        <a href="checkout.php" class="btn-primary checkout-btn">Lanjutkan ke Checkout</a>
                        <a href="index.php#produk" class="btn-secondary continue-shopping-btn">Lanjut Belanja</a>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Gee Shop. Hak Cipta Dilindungi.</p>
        <div class="social-icons">
            <a href="#" aria-label="Instagram Gee Shop"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="Facebook Gee Shop"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="Twitter Gee Shop"><i class="fab fa-twitter"></i></a>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
        // Logika JavaScript untuk tombol tambah/kurang kuantitas dan update otomatis
        document.addEventListener('DOMContentLoaded', function() {
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
            
            const quantityInputs = document.querySelectorAll('.quantity-input');
            const decreaseBtns = document.querySelectorAll('.decrease-quantity');
            const increaseBtns = document.querySelectorAll('.increase-quantity');

            decreaseBtns.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    const input = document.getElementById('quantity-' + productId);
                    let quantity = parseInt(input.value);
                    if (quantity > 1) {
                        input.value = quantity - 1;
                        updateCartItem(productId, input.value);
                    }
                });
            });

            increaseBtns.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    const input = document.getElementById('quantity-' + productId);
                    let quantity = parseInt(input.value);
                    const maxStock = parseInt(input.max); // Ambil nilai max dari atribut
                    if (quantity < maxStock) { // Batasi penambahan sesuai stok
                        input.value = quantity + 1;
                        updateCartItem(productId, input.value);
                    } else {
                        alert('Stok produk tidak mencukupi.');
                    }
                });
            });

            quantityInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const productId = this.dataset.id || this.id.split('-')[1];
                    let quantity = parseInt(this.value);
                    const maxStock = parseInt(this.max);

                    // Validasi input manual
                    if (isNaN(quantity) || quantity < 1) {
                        this.value = 1;
                        quantity = 1;
                    } else if (quantity > maxStock) {
                        alert('Stok produk tidak mencukupi.');
                        this.value = maxStock;
                        quantity = maxStock;
                    }
                    updateCartItem(productId, quantity);
                });
            });

            function updateCartItem(productId, quantity) {
                // Kirim request AJAX ke actions/update_cart_quantity.php
                fetch('actions/update_cart_quantity.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&quantity=${quantity}&update_quantity=1`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Refresh halaman atau perbarui subtotal dan total secara dinamis
                        window.location.reload(); // Untuk kemudahan, reload halaman
                        // Alternatif yang lebih canggih: perbarui elemen HTML tanpa reload
                    } else {
                        alert('Gagal memperbarui kuantitas: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memperbarui keranjang.');
                });
            }
        });
    </script>
</body>
</html>