-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2025 at 04:31 PM
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
-- Database: `ussd_ordering_system_ok`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `product_name`, `quantity`, `price`, `total_amount`, `created_at`) VALUES
(8, 1, 1, 'LENOVO-V-XX', 1, 50000.00, 50000.00, '2025-05-14 12:41:41'),
(9, 1, 1, 'LENOVO-V-XX', 1, 50000.00, 50000.00, '2025-05-14 12:54:48'),
(10, 4, 3, 'SAMSUNG-S10', 1, 35000.00, 35000.00, '2025-05-14 13:17:50'),
(11, 4, 1, 'LENOVO-V-XX', 1, 50000.00, 50000.00, '2025-05-14 13:22:40'),
(12, 7, 1, 'LENOVO-V-XX', 1, 50000.00, 50000.00, '2025-05-14 13:55:50'),
(13, 7, 6, 'PUMA-RS', 1, 10000.00, 10000.00, '2025-05-14 14:01:47');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Electronics devices', 'Electronic gadgets and devices', '2025-05-13 23:14:50'),
(2, 'Shoes', 'Footwear collection', '2025-05-13 23:14:50'),
(3, 'Clothes', 'Clothing and apparel', '2025-05-13 23:14:50');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','delivered') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `product_name`, `quantity`, `price`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'LENOVO-V-XX', 1, 500000.00, 500000.00, 'confirmed', '2025-05-13 23:17:32', '2025-05-13 23:17:32'),
(2, 1, 1, 'LENOVO-V-XX', 1, 500000.00, 500000.00, 'confirmed', '2025-05-13 23:25:47', '2025-05-13 23:25:47'),
(3, 1, 1, 'LENOVO-V-XX', 1, 500000.00, 500000.00, 'confirmed', '2025-05-13 23:31:51', '2025-05-13 23:31:51'),
(43, 1, 1, 'LENOVO-V-XX', 1, 50000.00, 50000.00, 'confirmed', '2025-05-14 12:41:51', '2025-05-14 12:41:51'),
(49, 7, 1, 'LENOVO-V-XX', 1, 50000.00, 50000.00, 'confirmed', '2025-05-14 13:56:10', '2025-05-14 13:56:10'),
(50, 7, 1, 'LENOVO-V-XX', 1, 50000.00, 50000.00, 'confirmed', '2025-05-14 14:02:02', '2025-05-14 14:02:02'),
(51, 7, 6, 'PUMA-RS', 1, 10000.00, 10000.00, 'confirmed', '2025-05-14 14:02:02', '2025-05-14 14:02:02');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `stock_quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 'LENOVO-V-XX', 'Lenovo Laptop V Series', 50000.00, 10, '2025-05-13 23:14:50', '2025-05-14 09:45:50'),
(2, 1, 'DELL-PQ-88', 'Dell Desktop PC', 45000.00, 15, '2025-05-13 23:14:50', '2025-05-14 09:46:03'),
(3, 1, 'SAMSUNG-S10', 'Samsung Galaxy S10', 35000.00, 20, '2025-05-13 23:14:50', '2025-05-14 09:46:15'),
(4, 2, 'NIKE-AIR', 'Nike Air Max', 15000.00, 30, '2025-05-13 23:14:50', '2025-05-14 09:47:20'),
(5, 2, 'ADIDAS-SUPER', 'Adidas Superstar', 12000.00, 25, '2025-05-13 23:14:50', '2025-05-14 09:47:08'),
(6, 2, 'PUMA-RS', 'Puma RS-X', 10000.00, 20, '2025-05-13 23:14:50', '2025-05-14 09:46:55'),
(7, 3, 'T-SHIRT-BASIC', 'Basic T-Shirt', 25000.00, 50, '2025-05-13 23:14:50', '2025-05-13 23:14:50'),
(8, 3, 'JEANS-CLASSIC', 'Classic Jeans', 7500.00, 40, '2025-05-13 23:14:50', '2025-05-14 09:46:42'),
(9, 3, 'HOODIE-SWEAT', 'Sweat Hoodie', 8500.00, 35, '2025-05-13 23:14:50', '2025-05-14 09:46:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_names` varchar(100) NOT NULL,
  `pin` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `balance` decimal(10,2) DEFAULT 10000000.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `phone_number`, `email`, `full_names`, `pin`, `address`, `balance`, `created_at`, `updated_at`) VALUES
(1, '+250790000000', 'francois@gmail.com', 'MG Francois', '$2y$10$LIEiwaJtlfrUqKV3Fa66uOLrs1hW21qyovZEhDTn/8zSSUZfle8cC', 'Ruhango', 91899999.99, '2025-05-13 23:15:10', '2025-05-14 12:55:24'),
(4, '+250730000000', 'fred@gmail.com', 'Frederic', '$2y$10$a5o0zJNjld5trPM8j.w7i.BiIF5/SIl12Zqm2doOKeG/SdJjn6Qji', 'KIGALI', 9880000.00, '2025-05-14 11:44:00', '2025-05-14 13:22:55'),
(7, '+250730000001', 'fred1@gmail.com', 'Fred', '$2y$10$aRpWFarNTEYJcLP29BowgOmzyRwDF.JM.VWWoAOe2r5tM061OMiSa', 'Rulindo', 9890000.00, '2025-05-14 13:51:18', '2025-05-14 14:02:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_cart_user` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_orders_user` (`user_id`),
  ADD KEY `idx_orders_status` (`status`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_category` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone_number` (`phone_number`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_user_phone` (`phone_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
