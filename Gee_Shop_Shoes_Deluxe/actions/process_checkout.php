<?php
session_start();
require_once '../koneksi.php'; // Gunakan require_once untuk robustness

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['message'] = "Silakan login untuk melanjutkan checkout.";
        header("Location: ../login.php");
        exit();
    }

    if (empty($_SESSION['cart'])) {
        $_SESSION['message'] = "Keranjang Anda kosong.";
        header("Location: ../keranjang.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $total_harga = 0; // Akan dihitung dari item keranjang

    // Ambil informasi pengiriman dari form dan lakukan sanitasi
    $nama_penerima = isset($_POST['full_name']) ? htmlspecialchars(trim($_POST['full_name'])) : '';
    $email_penerima = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $alamat_pengiriman = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : '';
    $telepon_penerima = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $metode_pembayaran = isset($_POST['payment_method']) ? htmlspecialchars(trim($_POST['payment_method'])) : '';

    // Validasi sederhana untuk input form
    if (empty($nama_penerima) || empty($email_penerima) || empty($alamat_pengiriman) || empty($telepon_penerima) || empty($metode_pembayaran)) {
        $_SESSION['message'] = "Semua kolom informasi pengiriman harus diisi.";
        header("Location: ../checkout.php");
        exit();
    }

    $koneksi->begin_transaction();

    try {
        // PERHATIAN: PASTIKAN KOLOM-KOLOM INI ADA DI TABEL `transaksi` ANDA!
        // (nama_penerima, email_penerima, alamat_pengiriman, telepon_penerima, metode_pembayaran)
        $stmt_transaksi = $koneksi->prepare(
            "INSERT INTO transaksi (user_id, total_harga, tanggal_transaksi, status, nama_penerima, email_penerima, alamat_pengiriman, telepon_penerima, metode_pembayaran) 
             VALUES (?, ?, NOW(), 'pending', ?, ?, ?, ?, ?)"
        );
        
        if (!$stmt_transaksi) {
             throw new Exception("Gagal menyiapkan statement transaksi: " . $koneksi->error);
        }

        $initial_total_harga = 0.0; // Nilai awal untuk total_harga, akan diupdate
        $stmt_transaksi->bind_param("idsssss", $user_id, $initial_total_harga, $nama_penerima, $email_penerima, $alamat_pengiriman, $telepon_penerima, $metode_pembayaran);
        
        if (!$stmt_transaksi->execute()) {
            $stmt_transaksi->close();
            throw new Exception("Gagal membuat transaksi baru: " . $stmt_transaksi->error);
        }
        $order_id = $koneksi->insert_id;
        $stmt_transaksi->close();

        foreach ($_SESSION['cart'] as $product_id => $item) {
            // Ambil harga terbaru dan stok dari database
            // MENGGUNAKAN NAMA KOLOM YANG BENAR: 'nama', 'harga', 'gambar', 'stok'
            $stmt_get_product = $koneksi->prepare("SELECT nama, harga, gambar, stok FROM produk WHERE id = ?");
            if (!$stmt_get_product) {
                throw new Exception("Gagal menyiapkan statement produk: " . $koneksi->error);
            }
            $stmt_get_product->bind_param("i", $product_id);
            $stmt_get_product->execute();
            $result_product = $stmt_get_product->get_result();
            
            if ($result_product->num_rows === 0) {
                $stmt_get_product->close();
                throw new Exception("Produk dengan ID " . htmlspecialchars($product_id) . " tidak ditemukan di database.");
            }
            $product_data = $result_product->fetch_assoc();
            $harga_satuan = $product_data['harga']; // Menggunakan 'harga'
            $stok_saat_ini = $product_data['stok'];
            $stmt_get_product->close();

            $kuantitas = $item['quantity'];

            // Validasi: Pastikan harga_satuan bukan NULL dan numerik
            if (!isset($harga_satuan) || !is_numeric($harga_satuan)) {
                throw new Exception("Harga produk tidak valid atau tidak ditemukan untuk produk ID: " . htmlspecialchars($product_id));
            }

            // Cek ketersediaan stok
            if ($kuantitas > $stok_saat_ini) {
                throw new Exception("Stok tidak cukup untuk produk: " . htmlspecialchars($product_data['nama']) . ". Tersedia: " . $stok_saat_ini . ", Diminta: " . $kuantitas);
            }
            
            $subtotal_item = $harga_satuan * $kuantitas;
            $total_harga += $subtotal_item;

            $stmt_detail = $koneksi->prepare("INSERT INTO detail_transaksi (transaksi_id, produk_id, kuantitas, harga_satuan) VALUES (?, ?, ?, ?)");
            if (!$stmt_detail) {
                throw new Exception("Gagal menyiapkan statement detail transaksi: " . $koneksi->error);
            }
            $stmt_detail->bind_param("iiid", $order_id, $product_id, $kuantitas, $harga_satuan);
            if (!$stmt_detail->execute()) {
                $stmt_detail->close();
                throw new Exception("Gagal menyimpan detail transaksi untuk produk ID: " . htmlspecialchars($product_id) . ". Error: " . $stmt_detail->error);
            }
            $stmt_detail->close();

            // Update stok produk
            $stmt_update_stock = $koneksi->prepare("UPDATE produk SET stok = stok - ? WHERE id = ?");
            if (!$stmt_update_stock) {
                throw new Exception("Gagal menyiapkan statement update stok: " . $koneksi->error);
            }
            $stmt_update_stock->bind_param("ii", $kuantitas, $product_id);
            if (!$stmt_update_stock->execute()) {
                $stmt_update_stock->close();
                throw new Exception("Gagal memperbarui stok untuk produk ID: " . htmlspecialchars($product_id) . ". Error: " . $stmt_update_stock->error);
            }
            $stmt_update_stock->close();
        }

        // Update total harga transaksi setelah semua item diproses
        $stmt_update_total = $koneksi->prepare("UPDATE transaksi SET total_harga = ? WHERE id = ?");
        if (!$stmt_update_total) {
            throw new Exception("Gagal menyiapkan statement update total harga: " . $koneksi->error);
        }
        $stmt_update_total->bind_param("di", $total_harga, $order_id);
        if (!$stmt_update_total->execute()) {
            $stmt_update_total->close();
            throw new Exception("Gagal memperbarui total harga transaksi.");
        }
        $stmt_update_total->close();

        $koneksi->commit(); // Komit transaksi jika semua berhasil
        unset($_SESSION['cart']); // Hapus keranjang setelah berhasil checkout

        $_SESSION['message'] = "Pesanan Anda berhasil diproses! Nomor Pesanan: " . $order_id;
        header("Location: ../terimakasih.php?order_id=" . $order_id);
        exit();

    } catch (mysqli_sql_exception $e) {
        $koneksi->rollback();
        $_SESSION['message'] = "Terjadi kesalahan database saat memproses pesanan: " . htmlspecialchars($e->getMessage());
        header("Location: ../checkout.php");
        exit();
    } catch (Exception $e) {
        $koneksi->rollback();
        $_SESSION['message'] = "Terjadi kesalahan saat memproses pesanan: " . htmlspecialchars($e->getMessage());
        header("Location: ../checkout.php");
        exit();
    } finally {
        if ($koneksi && !$koneksi->connect_error) {
            $koneksi->close();
        }
    }

} else {
    $_SESSION['message'] = "Akses tidak sah.";
    header("Location: ../checkout.php");
    exit();
}
?>