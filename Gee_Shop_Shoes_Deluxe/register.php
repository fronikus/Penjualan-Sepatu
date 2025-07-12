<?php
include 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$pageTitle = "Daftar Akun Baru - Gee Shop";
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
        <h1>Daftar Akun Baru</h1>
        
        <form action="actions/process_register.php" method="POST" class="auth-form">
            <?php if (!empty($message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <div class="form-group">
                <label for="username">Nama Pengguna:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn-primary">Daftar</button>
            <p class="message">Sudah punya akun? <a href="login.php">Login di sini</a></p>
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