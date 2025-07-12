<?php
session_start(); // Pastikan session dimulai untuk mengakses data sesi

$pageTitle = "Terima Kasih - Gee Shop";
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
    <style>
        .thank-you-container {
            text-align: center;
            padding: 80px 20px;
            background-color: var(--soft-grey);
            margin: 50px auto;
            border-radius: 10px;
            box-shadow: var(--shadow-light);
            max-width: 800px;
        }
        .thank-you-container h1 {
            color: var(--accent-red);
            font-size: 3em;
            margin-bottom: 20px;
        }
        .thank-you-container p {
            font-size: 1.2em;
            color: var(--dark-navy);
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .thank-you-container .btn-primary {
            display: inline-block;
            padding: 12px 25px;
            font-size: 1em;
            text-decoration: none;
            background-color: var(--primary-color);
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .thank-you-container .btn-primary:hover {
            background-color: var(--primary-dark);
        }
    </style>
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
        <div class="thank-you-container">
            <h1>🎉 Terima Kasih Atas Pesanan Anda! 🎉</h1>
            <p>Pesanan Anda telah berhasil ditempatkan. Kami akan segera memprosesnya dan mengirimkannya ke alamat Anda.</p>
            <p>Anda akan menerima konfirmasi pesanan melalui email dalam beberapa menit.</p>
            <a href="index.php" class="btn-primary">Kembali ke Beranda</a>
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