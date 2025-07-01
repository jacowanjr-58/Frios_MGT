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
-- Table structure for table `fgp_category_fgp_item`
--

/* CREATE TABLE `fgp_category_fgp_item` (
  `fgp_category_id` bigint(20) UNSIGNED NOT NULL,
  `fgp_item_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 */
--
-- Dumping data for table `fgp_category_fgp_item`
--

INSERT INTO `fgp_category_fgp_item` (`fgp_category_id`, `fgp_item_id`) VALUES
(2, 2),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 11),
(2, 12),
(2, 14),
(2, 15),
(2, 16),
(2, 19),
(2, 20),
(2, 21),
(2, 22),
(2, 24),
(2, 26),
(2, 27),
(2, 31),
(2, 33),
(3, 3),
(3, 4),
(3, 10),
(3, 13),
(3, 17),
(3, 18),
(3, 23),
(3, 25),
(3, 28),
(3, 29),
(3, 30),
(3, 32),
(3, 34),
(3, 35),
(3, 36),
(3, 37),
(3, 38),
(3, 39),
(5, 2),
(5, 3),
(5, 4),
(5, 8),
(5, 9),
(5, 10),
(5, 11),
(5, 12),
(5, 13),
(5, 14),
(5, 16),
(5, 18),
(5, 20),
(5, 27),
(5, 28),
(5, 30),
(5, 32),
(5, 33),
(5, 34),
(5, 35),
(5, 38),
(5, 39),
(6, 5),
(6, 6),
(6, 7),
(6, 15),
(6, 17),
(6, 19),
(6, 21),
(6, 22),
(6, 23),
(6, 24),
(6, 25),
(6, 36),
(6, 37),
(7, 6),
(7, 24),
(8, 4),
(8, 6),
(8, 7),
(8, 8),
(8, 9),
(8, 10),
(8, 11),
(8, 13),
(8, 14),
(8, 15),
(8, 17),
(8, 18),
(8, 19),
(8, 20),
(8, 21),
(8, 22),
(8, 23),
(8, 24),
(8, 25),
(8, 28),
(8, 33),
(8, 36),
(8, 37),
(8, 39),
(9, 6),
(9, 8),
(9, 9),
(9, 11),
(9, 13),
(9, 16),
(9, 18),
(9, 19),
(9, 20),
(9, 21),
(9, 22),
(9, 23),
(9, 24),
(9, 25),
(9, 27),
(9, 33),
(9, 36),
(9, 37),
(9, 38),
(9, 39),
(10, 6),
(10, 7),
(10, 19),
(10, 21),
(10, 22),
(10, 23),
(10, 24),
(10, 25),
(10, 36),
(10, 37),
(12, 2),
(12, 3),
(12, 4),
(12, 5),
(12, 7),
(12, 8),
(12, 9),
(12, 11),
(12, 12),
(12, 14),
(12, 15),
(12, 16),
(12, 17),
(12, 19),
(12, 20),
(12, 21),
(12, 22),
(12, 23),
(12, 24),
(12, 25),
(12, 27),
(12, 28),
(12, 30),
(12, 34),
(12, 35),
(12, 36),
(12, 37),
(12, 38),
(12, 39),
(13, 4),
(13, 5),
(13, 6),
(13, 8),
(13, 9),
(13, 10),
(13, 11),
(13, 13),
(13, 14),
(13, 15),
(13, 17),
(13, 19),
(13, 20),
(13, 21),
(13, 22),
(13, 23),
(13, 24),
(13, 25),
(13, 28),
(13, 33),
(13, 34),
(13, 36),
(13, 37),
(13, 39),
(14, 4),
(14, 5),
(14, 6),
(14, 7),
(14, 8),
(14, 9),
(14, 13),
(14, 14),
(14, 15),
(14, 17),
(14, 19),
(14, 20),
(14, 21),
(14, 22),
(14, 23),
(14, 24),
(14, 25),
(14, 33),
(14, 36),
(14, 37),
(14, 39),
(15, 6),
(15, 7),
(15, 15),
(15, 17),
(15, 19),
(15, 21),
(15, 22),
(15, 23),
(15, 24),
(15, 25),
(15, 36),
(15, 37),
(16, 31);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fgp_category_fgp_item`
--
/* ALTER TABLE `fgp_category_fgp_item`
  ADD PRIMARY KEY (`fgp_category_id`,`fgp_item_id`),
  ADD KEY `fgp_category_fgp_item_fgp_item_id_foreign` (`fgp_item_id`);

-- */
-- Constraints for dumped tables
--

--
-- Constraints for table `fgp_category_fgp_item`
--
/* ALTER TABLE `fgp_category_fgp_item`
  ADD CONSTRAINT `fgp_category_fgp_item_fgp_category_id_foreign` FOREIGN KEY (`fgp_category_id`) REFERENCES `fgp_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fgp_category_fgp_item_fgp_item_id_foreign` FOREIGN KEY (`fgp_item_id`) REFERENCES `fgp_items` (`id`) ON DELETE CASCADE;
COMMIT;
 */
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
