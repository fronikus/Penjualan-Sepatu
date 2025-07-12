<?php
include 'koneksi.php';

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$pageTitle = "Reset Password - Gee Shop";
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
        <h1>Reset Password</h1>
        
        <form action="#" method="POST" class="auth-form">
            <?php if (!empty($message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <p style="text-align: center; color: var(--soft-grey); margin-bottom: 20px;">Masukkan email Anda. Kami akan mengirimkan link untuk mereset password Anda.</p>

            <div class="form-group">
                <label for="email">Email Anda:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" class="btn-primary">Kirim Link Reset</button>
            <p class="message">Kembali ke <a href="login.php">Login</a></p>
        </form>
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