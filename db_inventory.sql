-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2026 at 05:21 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `kode_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `satuan` varchar(30) DEFAULT 'pcs',
  `harga_beli` decimal(15,2) DEFAULT 0.00,
  `harga_jual` decimal(15,2) DEFAULT 0.00,
  `deskripsi` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `kode_barang`, `nama_barang`, `id_kategori`, `id_supplier`, `stok`, `satuan`, `harga_beli`, `harga_jual`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'BRG-001', 'Beras Premium 5kg', 1, 3, 50, 'karung', 65000.00, 75000.00, NULL, '2026-06-18 09:39:55', '2026-06-19 13:01:53'),
(2, 'BRG-002', 'Minyak Goreng Bimoli 2L', 1, 1, 160, 'pouch', 32000.00, 38000.00, NULL, '2026-06-18 09:39:55', '2026-06-22 07:42:18'),
(3, 'BRG-003', 'Mie Instan Indomie Goreng', 2, 1, 500, 'pcs', 2800.00, 3500.00, NULL, '2026-06-18 09:39:55', '2026-06-18 09:39:55'),
(4, 'BRG-004', 'Susu UHT Ultra Milk 1L', 2, 2, 120, 'kotak', 15000.00, 19000.00, NULL, '2026-06-18 09:39:55', '2026-06-22 07:42:50'),
(5, 'BRG-005', 'Sabun Mandi Lifebuoy 85g', 3, 2, 200, 'pcs', 4000.00, 5000.00, NULL, '2026-06-18 09:39:55', '2026-06-18 09:39:55'),
(8, 'BRG-006', 'Shampo', 3, 2, 20, 'pcs', 2000.00, 7000.00, NULL, '2026-06-22 07:31:05', '2026-06-22 07:32:42');

-- --------------------------------------------------------

--
-- Table structure for table `histori_barang`
--

CREATE TABLE `histori_barang` (
  `id` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `jenis` enum('masuk','keluar') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `histori_barang`
--

INSERT INTO `histori_barang` (`id`, `id_barang`, `id_user`, `jenis`, `jumlah`, `keterangan`, `tanggal`, `created_at`, `updated_at`) VALUES
(4, 1, 3, 'keluar', 5, 'Penjualan', '2026-06-19 00:00:00', '2026-06-19 12:54:58', '2026-06-19 12:54:58'),
(5, 1, 3, 'keluar', 5, 'Penjualan', '0000-00-00 00:00:00', '2026-06-19 13:01:53', '2026-06-19 13:01:53'),
(6, 2, 3, 'masuk', 100, 'Pembelian', '2026-06-18 00:00:00', '2026-06-19 13:06:30', '2026-06-22 07:42:18');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Sembako', 'Gula, minyak, garam, dan gula merah', '2026-06-18 09:37:13', '2026-06-18 20:24:55'),
(2, 'Makanan dan Minuman', 'Camilan, instan, minuman kemasan, dan produk susu', '2026-06-18 09:37:13', '2026-06-19 15:16:13'),
(3, 'Perawatan Tubuh', 'Sabun mandi, sampo, pasta gigi, dan kosmetik harian', '2026-06-18 09:37:13', '2026-06-18 09:37:13'),
(4, 'Kebutuhan Rumah Tangga', 'Detergen, pembersih lantai, tisu, dan obat nyamuk', '2026-06-18 09:37:13', '2026-06-18 09:37:13');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id` int(11) NOT NULL,
  `nama_supplier` varchar(150) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id`, `nama_supplier`, `alamat`, `telepon`, `email`, `created_at`, `updated_at`) VALUES
(1, 'PT. Indomarco Adi Prima', 'Jl. Jend. Sudirman No. 23, Jakarta', '021-8881234', 'info@indomarco.co.id', '2026-06-18 09:38:25', '2026-06-18 09:38:25'),
(2, 'PT. Sumber Alfaria Trijaya', 'Jl. Alfa Tower No. 7, Tangerang', '021-5559876', 'supplier@sat.co.id', '2026-06-18 09:38:25', '2026-06-18 09:38:25'),
(3, 'CV. Sembako Makmur Bersama', 'Jl. Pasar Grosir No. 12, Surabaya', '031-7774321', 'sembako.makmur@gmail.com', '2026-06-18 09:38:25', '2026-06-18 09:38:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `token`, `created_at`, `updated_at`) VALUES
(3, '', 'admin@inventory.com', '$2b$10$PRXHkS/oAzZrJibx93Dgb.24kdFOCzwfOXIcFJpfrqQbb/iK.kkuu', '5725a51a067834865824a39217f59188f72ecb4b209c9d42ce26006d6c9f3aac', '2026-06-19 09:29:30', '2026-06-22 07:22:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_barang` (`kode_barang`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_supplier` (`id_supplier`);

--
-- Indexes for table `histori_barang`
--
ALTER TABLE `histori_barang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_barang` (`id_barang`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `histori_barang`
--
ALTER TABLE `histori_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `barang_ibfk_2` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `histori_barang`
--
ALTER TABLE `histori_barang`
  ADD CONSTRAINT `histori_barang_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `histori_barang_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
