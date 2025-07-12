-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 12, 2025 at 05:00 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `geeshop_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `geeshop_db`;

CREATE TABLE `detail_transaksi` (
  `id` int(11) NOT NULL,
  `transaksi_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `kuantitas` int(11) NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `stok` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `produk` (`id`, `nama`, `deskripsi`, `harga`, `gambar`, `stok`) VALUES
(1, 'Sepatu Casual Pria KDN 717', 'Sepatu casual pria dengan desain modern dan nyaman, cocok untuk kegiatan sehari-hari.', 1250000.00, 'Sepatu Casual Pria KDN 717.png', 50),
(2, 'Sepatu Lifestyle Pria Eagle Revor', 'Sepatu lifestyle ringan dengan sol yang empuk, ideal untuk gaya hidup aktif.', 1800000.00, 'Sepatu Lifestyle Pria Eagle Revor.jpeg', 45),
(3, 'Nike Fly.by Mid 2', 'Sepatu basket Nike Fly.by Mid 2, memberikan dukungan dan traksi optimal di lapangan.', 1100000.00, 'Nike Fly.by Mid 2.png', 30),
(4, 'Sepatu Futsal IBERO IV', 'Sepatu Futsal IBERO IV dengan sentuhan klasik dan bahan berkualitas tinggi.', 2200000.00, 'Sepatu Futsal IBERO IV.png', 25),
(5, 'Asics Unisex Royte Japan Lyte FF 3 Standard', 'Asics Unisex Royte Japan Lyte FF 3 Standard, ringan dan responsif untuk performa maksimal.', 1950000.00, 'Asics Unisex Royte Japan Lyte FF 3 Standard.png', 40),
(6, 'New Balance Fresh Foam X 1080 v14', 'Sepatu lari New Balance Fresh Foam X 1080 v14, dengan bantalan superior untuk kenyamanan jarak jauh.', 2500000.00, 'New Balance Fresh Foam X 1080 v14.png', 35),
(7, 'Nike Air Ships Michael Jordan', 'Replika sepatu ikonik Nike Air Ships yang dikenakan oleh Michael Jordan, koleksi wajib penggemar basket.', 2950000.00, 'Nike Air Ships Michael Jordan.jpg', 10),
(8, 'Nike LD-1000', 'Sepatu sneakers Nike LD-1000, desain retro yang cocok untuk gaya kasual sehari-hari.', 1950000.00, 'Nike LD-1000.png', 28),
(9, 'Dane and Dine Pria Model Crocs Kodok - LS 9945', 'Sandal Dane and Dine Pria Model Crocs Kodok - LS 9945, nyaman dan stylish untuk dipakai sehari-hari.', 550000.00, 'Dane and Dine Pria Model Crocs Kodok - LS 9945.webp', 60),
(10, 'Laviola 3001 TGK', 'Sandal Laviola 3001 TGK, desain elegan dan nyaman untuk berbagai kesempatan.', 800000.00, 'Laviola 3001 TGK.jpeg', 55),
(11, 'Air Jordan 4 Eminem x Carhartt', 'Sepatu Air Jordan 4 edisi spesial Carhartt, desain tangguh dan stylish.', 3200000.00, 'Air Jordan 4 Eminem x Carhartt.png', 20),
(12, 'Solid Gold OVO Nike Air Jordan 10', 'Sepatu Nike Air Jordan 10 edisi Solid Gold OVO, koleksi eksklusif dengan sentuhan mewah.', 3500000.00, 'Solid Gold OVO Nike Air Jordan 10.jpg', 15);

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `status` enum('pending','completed','cancelled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_id` (`transaksi_id`),
  ADD KEY `produk_id` (`produk_id`);

ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `detail_transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;