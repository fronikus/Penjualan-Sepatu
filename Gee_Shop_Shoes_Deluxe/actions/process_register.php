<?php
session_start();
include '../koneksi.php'; // Sesuaikan path jika berbeda

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = escape_string($koneksi, $_POST['username']);
    $email = escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['message'] = "Semua field harus diisi.";
        $koneksi->close(); // Tutup koneksi sebelum redirect
        header("Location: ../register.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Format email tidak valid.";
        $koneksi->close(); // Tutup koneksi sebelum redirect
        header("Location: ../register.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['message'] = "Konfirmasi password tidak cocok.";
        $koneksi->close(); // Tutup koneksi sebelum redirect
        header("Location: ../register.php");
        exit();
    }

    // Periksa apakah email sudah terdaftar
    $stmt_check_email = $koneksi->prepare("SELECT id FROM users WHERE email = ?");
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $stmt_check_email->store_result();

    if ($stmt_check_email->num_rows > 0) {
        $_SESSION['message'] = "Email sudah terdaftar. Silakan gunakan email lain atau login.";
        $stmt_check_email->close();
        $koneksi->close(); // Tutup koneksi sebelum redirect
        header("Location: ../register.php");
        exit();
    }
    $stmt_check_email->close();

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Masukkan data pengguna baru ke database
    $stmt_insert_user = $koneksi->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt_insert_user->bind_param("sss", $username, $email, $password_hash);

    if ($stmt_insert_user->execute()) {
        $_SESSION['message'] = "Registrasi berhasil! Silakan login.";
        $stmt_insert_user->close();
        $koneksi->close(); // Tutup koneksi sebelum redirect
        header("Location: ../login.php");
        exit();
    } else {
        $_SESSION['message'] = "Registrasi gagal. Silakan coba lagi.";
        $stmt_insert_user->close();
        $koneksi->close(); // Tutup koneksi sebelum redirect
        header("Location: ../register.php");
        exit();
    }

} else {
    $_SESSION['message'] = "Akses tidak sah.";
    $koneksi->close(); // Tutup koneksi sebelum redirect
    header("Location: ../register.php");
    exit();
}
?>