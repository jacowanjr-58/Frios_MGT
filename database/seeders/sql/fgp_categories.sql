-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 29, 2025 at 11:32 PM
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
-- Database: `u134716093_frios`
--

-- --------------------------------------------------------

--
-- Table structure for table `fgp_categories`
--

CREATE TABLE `fgp_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fgp_categories`
--

INSERT INTO `fgp_categories` (`id`, `name`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'Availability', NULL, '2025-06-28 23:37:11', '2025-06-28 23:37:11'),
(2, 'Signature', 1, '2025-06-28 23:37:11', '2025-06-28 23:37:11'),
(3, 'Seasonal', 1, '2025-06-28 23:37:11', '2025-06-28 23:37:11'),
(4, 'Flavor', NULL, '2025-06-28 23:37:11', '2025-06-28 23:37:11'),
(5, 'Creamy', 4, '2025-06-28 23:37:11', '2025-06-28 23:37:11'),
(6, 'Fruity', 4, '2025-06-28 23:37:11', '2025-06-28 23:37:11'),
(7, 'No Sugar Added', 4, '2025-06-28 23:37:11', '2025-06-28 23:37:11'),
(8, 'Gluten Free', 4, '2025-06-28 23:37:11', '2025-06-28 23:37:11'),
(9, 'Dye Free', 4, '2025-06-28 23:37:11', '2025-06-28 23:37:11'),
(10, 'Vegan', 4, '2025-06-28 23:37:11', '2025-06-28 23:37:11'),
(11, 'Allergen', NULL, '2025-06-28 23:37:11', '2025-06-28 23:37:11'),
(12, 'Nut Free', 11, '2025-06-28 23:37:11', '2025-06-28 23:37:11'),
(13, 'Wheat Free', 11, '2025-06-28 23:37:11', '2025-06-28 23:37:11'),
(14, 'Soy Free', 11, '2025-06-28 23:37:11', '2025-06-28 23:37:11'),
(15, 'Dairy Free', 11, '2025-06-28 23:37:11', '2025-06-28 23:37:11'),
(16, 'Protein Plus', 4, '2025-06-28 23:40:30', '2025-06-28 23:48:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fgp_categories`
--
ALTER TABLE `fgp_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fgp_categories_parent_id_foreign` (`parent_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fgp_categories`
--
ALTER TABLE `fgp_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fgp_categories`
--
ALTER TABLE `fgp_categories`
  ADD CONSTRAINT `fgp_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `fgp_categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
