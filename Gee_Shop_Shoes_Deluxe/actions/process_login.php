<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    $stmt = $koneksi->prepare("SELECT id, username, email, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['message'] = "Selamat datang, " . htmlspecialchars($user['username']) . "!";
        
        $koneksi->close(); // Pindahkan di sini
        header("Location: ../index.php");
        exit();
    } else {
        $_SESSION['message'] = "Email atau password salah.";
        
        $koneksi->close(); // Pindahkan di sini
        header("Location: ../login.php");
        exit();
    }
} else {
    $_SESSION['message'] = "Akses tidak sah.";
    
    $koneksi->close(); // Pindahkan di sini
    header("Location: ../login.php");
    exit();
}
// Tidak perlu ada $koneksi->close(); di sini lagi
?>