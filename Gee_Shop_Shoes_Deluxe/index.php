<?php
// Pastikan sesi dimulai di awal setiap file yang akan menggunakan $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Mengimpor file koneksi database
include 'koneksi.php';

// Inisialisasi array untuk menampung data produk
$produk = [];

// Kueri SQL untuk mengambil 9 produk terbaru, diurutkan berdasarkan ID secara menurun
// Menggunakan 'produk' (huruf kecil) dan 'id' sebagai kolom ORDER BY
$query = "SELECT * FROM produk ORDER BY id DESC LIMIT 9";
$result = $koneksi->query($query);

// Memeriksa apakah kueri berhasil dijalankan
if ($result) {
    // Mengambil setiap baris hasil kueri dan menambahkannya ke array $produk
    while ($row = $result->fetch_assoc()) {
        $produk[] = $row;
    }
} else {
    // Menampilkan pesan error jika kueri gagal
    echo "<p style='color: red; text-align: center;'>Error mengambil produk dari database: " . $koneksi->error . "</p>";
}

// Judul halaman
$pageTitle = "Gee Shop - Kemewahan di Setiap Langkah";
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
                <a href="#">Gee <span class="highlight">Shop</span></a>
            </div>
            <ul class="nav-links">
                <li><a href="#home">Beranda</a></li>
                <li><a href="#produk">Produk</a></li>
                <li><a href="#about">Tentang Kami</a></li>
                <li><a href="#contact">Kontak</a></li>
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

    <main>
        <section id="home" class="hero-section">
            <div class="hero-content">
                <p class="subtitle">Koleksi Eksklusif Terbaru</p>
                <h1>Definisikan Gaya Anda.<br>Dengan Gee Shop.</h1>
                <p class="description">Temukan sepatu premium yang menggabungkan desain inovatif, kenyamanan tak tertandingi, dan sentuhan kemewahan di setiap pasangnya.</p>
                <a href="#produk" class="btn-primary">Jelajahi Koleksi</a>
            </div>
            <div class="hero-image">
                <img src="images/Logo.png" alt="Logo">
            </div>
        </section>

        <section id="produk" class="produk-section">
            <h2>Koleksi Sepatu Unggulan</h2>
            <div class="product-categories">
                <button class="category-btn active" data-category="all">Semua</button>
            </div>

            <div class="product-grid">
                <?php if (empty($produk)): ?>
                    <p style="text-align: center; width: 100%; font-size: 1.2em; color: var(--soft-grey);">Tidak ada produk yang tersedia saat ini.</p>
                <?php else: ?>
                    <?php foreach ($produk as $product): ?>
                        <div class="product-card" data-category="<?php
                            // Menggunakan kolom 'kategori' jika ada, jika tidak, gunakan 'unknown'
                            // Sesuaikan 'kategori' dengan nama kolom kategori di DB Anda jika ada
                            echo isset($product['kategori']) ? htmlspecialchars($product['kategori']) : 'unknown';
                        ?>" data-product-id="<?php echo htmlspecialchars($product['id']); ?>">
                            <div class="product-image-container">
                                <img src="images/<?php echo htmlspecialchars($product['gambar']); ?>" alt="<?php echo htmlspecialchars($product['nama']); ?>">
                            </div>
                            <h3><?php echo htmlspecialchars($product['nama']); ?></h3>
                            <p class="price">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>
                            <button class="btn-secondary add-to-cart-btn"
                                    data-product-id="<?php echo htmlspecialchars($product['id']); ?>"
                                    data-product-name="<?php echo htmlspecialchars($product['nama']); ?>"
                                    data-product-price="<?php echo htmlspecialchars($product['harga']); ?>">
                                Tambah Keranjang
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section id="about" class="about-section">
            <div class="about-content">
                <h2>Tentang Gee Shop</h2>
                <p>Didirikan dengan gairah untuk sepatu dan dedikasi pada kualitas, <strong>Gee Shop</strong> adalah destinasi utama Anda untuk koleksi sepatu premium. Kami mengkurasi setiap pasang dengan cermat, memastikan setiap produk menawarkan perpaduan sempurna antara **desain inovatif, kenyamanan superior, dan daya tahan yang luar biasa**.</p>
                <p>Kami percaya bahwa sepatu lebih dari sekadar alas kaki; mereka adalah perpanjangan dari kepribadian Anda. Oleh karena itu, kami berkomitmen untuk menyediakan pilihan yang tidak hanya melengkapi gaya Anda, tetapi juga meningkatkan kepercayaan diri di setiap langkah.</p>
            </div>
        </section>

        <section id="contact" class="contact-section">
            <h2>Hubungi Kami</h2>
            <form class="contact-form" action="#" method="POST">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Nama Lengkap Anda" required aria-label="Nama Lengkap">
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Alamat Email Anda" required aria-label="Alamat Email">
                </div>
                <div class="form-group">
                    <textarea name="message" placeholder="Tulis Pesan Anda Di Sini..." rows="8" required aria-label="Pesan Anda"></textarea>
                </div>
                <button type="submit" class="btn-primary">Kirim Pesan</button>
            </form>
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
        document.addEventListener('DOMContentLoaded', () => {
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

            // Pastikan fungsi updateCartCount tetap ada jika digunakan di bagian lain
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }

            // JavaScript untuk filter kategori (jika ada)
            document.querySelectorAll('.category-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const category = this.dataset.category;
                    // Logika untuk memuat produk berdasarkan kategori atau filter
                    // Untuk saat ini, ini hanya contoh. Anda mungkin perlu AJAX di sini.
                    console.log('Filter by category:', category);
                    // Jika Anda ingin mengimplementasikan filter berdasarkan kategori di sisi server,
                    // Anda mungkin akan mengarahkan ke halaman dengan parameter kategori
                    // window.location.href = "index.php?category=" + encodeURIComponent(category);
                });
            });
        });
    </script>
</body>
</html>