-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 29, 2025 at 11:29 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

/* SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00"; */


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u134716093_frios`
--

-- --------------------------------------------------------

--
-- Table structure for table `fgp_items`
--
/*
CREATE TABLE `fgp_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `case_cost` decimal(10,2) NOT NULL,
  `internal_inventory` int(11) NOT NULL,
  `split_factor` int(10) UNSIGNED NOT NULL DEFAULT 48,
  `dates_available` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dates_available`)),
  `image1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `orderable` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; */

--
-- Dumping data for table `fgp_items`
--

INSERT INTO `fgp_items` (`id`, `name`, `description`, `case_cost`, `internal_inventory`, `split_factor`, `dates_available`, `image1`, `image2`, `image3`, `orderable`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(2, 'Birthday Cake', 'Classic birthday cake ice cream in a pop form', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/ee5UXMqlrUuUiUhHhcBV6Lyx9fYsLX1yTcfm57UV.png', NULL, NULL, 1, 2, 2, '2025-06-29 00:19:33', '2025-06-29 21:43:58'),
(3, 'Strawberries & Cream', 'Fresh strawberries, and sweet cream.', 65.00, 10, 48, NULL, 'images/fgp_items/c5IZeXMIc6GinB0CWxgi2dhIxT0dVpAqr3O4Qzbe.png', NULL, NULL, 1, 2, 2, '2025-06-29 02:27:01', '2025-06-29 02:27:01'),
(4, 'Peaches n’ Cream', 'Delicious peaches with a nice creamy texture throughout. Summertime in every bite!', 65.00, 10, 48, NULL, 'images/fgp_items/x5noMKk6W6oUsg8gfpQXbazLd5rVrv9KZKAZg3qX.png', NULL, NULL, 1, 2, 2, '2025-06-29 02:29:54', '2025-06-29 02:29:54'),
(5, 'White Peach Lemonade', 'Delicious peaches with a tart lemon kick. Like summer on an Alabama porch.', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/aPSsmmOS0yChgCFi5v043meGWt8tvZAh2Mf6ccHg.png', NULL, NULL, 1, 2, 2, '2025-06-29 02:32:02', '2025-06-29 21:43:14'),
(6, 'Pineapple Mango', 'A fun, tropical mix of pineapple and mango with no added sugar! A healthy and delicious gourmet treat. Win, win!', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/PaEdQpgCJMvkHZQcMl5Ik0V9sO9SOzDomaFADpgj.png', NULL, NULL, 1, 2, 2, '2025-06-29 02:33:35', '2025-06-29 21:43:31'),
(7, 'Blue Raspberry', 'Sweet and tangy blue raspberry. Send your taste buds on a ride', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/dv2xKMF86h8E78F3WBb9Hw9I7irE4PKM1pGE8yAT.png', NULL, NULL, 1, 2, 2, '2025-06-29 02:35:30', '2025-06-29 21:43:53'),
(8, 'Blueberry Cheesecake', 'That decadent blueberry cheesecake experience whipped into an ice cream pop. All on a stick.', 70.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"11\"]', 'images/fgp_items/VLgNlqy2W7dmHlxsw0CiPGocSWEsUwoz4GSOrPrY.png', NULL, NULL, 1, 2, 2, '2025-06-29 02:38:16', '2025-06-29 21:49:35'),
(9, 'Caramel Sea Salt', 'Smooth. Velvety. Caramel. Salty.', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/TS5UJxWUqdb44AjrO0wJ7xe737YgrAKguhmhnxyI.png', NULL, NULL, 1, 2, 2, '2025-06-29 02:47:58', '2025-06-29 21:43:46'),
(10, 'Chocolate Dipped Cold Brew', 'Classic Cold Brew with a chocolatey twist', 65.00, 10, 48, NULL, 'images/fgp_items/8Fn40YMYuioXyHm35bJZKd6pYTtAbdizMpdHs0z0.png', NULL, NULL, 1, 2, 2, '2025-06-29 02:51:34', '2025-06-29 02:51:34'),
(11, 'Chocolate', 'Rich, decadent chocolate ice cream bar.', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/Yy2LyT5gIoShMJ31YFHCjTM8agmNioxj3ifJsCtJ.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:14:58', '2025-06-29 21:43:43'),
(12, 'Cookies and Cream', 'Classic cookies & cream ice cream in a pop form', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/AIJECzUQJPhqID5QqwYhcTsobsFJs7tia58jROPy.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:17:56', '2025-06-29 21:43:41'),
(13, 'Creamy Coconut', 'Rich and creamy, classic coconut flavor', 65.00, 10, 48, '[\"11\",\"9\",\"7\"]', 'images/fgp_items/pDIFrcDyOhNJOF1nnEGzd9Rj7SuywkWdc9VZHcQA.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:19:43', '2025-06-29 21:46:59'),
(14, 'Fruity Pebbles', 'A unique dessert with a favorite colorful cereal.', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/lfVGmraCOGcb28GvSgDB0oJhJe7EpBGilmk50DnK.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:24:25', '2025-06-29 21:43:36'),
(15, 'Fruit Punch', 'Look out. This fruit packs a punch!', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/zcg5OR44TNREZEDu56v5M9jBwEV7Nei76RmEUFR0.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:25:49', '2025-06-29 21:43:39'),
(16, 'Key Lime Pie', 'A decadent, frozen version of the classic key lime pie with a graham cracker crunch.', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/3IVxNoOeuoQKB2usYTWpbLfJQiZVAhifV6f1X66U.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:26:45', '2025-06-29 21:43:34'),
(17, 'Margarita (Lemon Lime)', 'Our (non-alcoholic) approach to this most favorite drink', 65.00, 10, 48, NULL, 'images/fgp_items/yWkxVDXALZFSp6qFQuqJafVGhJUgXIg3FD7HXovp.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:27:51', '2025-06-29 17:27:51'),
(18, 'Orange Cream', 'Fresh orange with creamy texture.', 65.00, 10, 48, NULL, 'images/fgp_items/eDVXNYCrFTytQjxMJbJY0WVQ36o8gmLl7afroCRy.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:29:43', '2025-06-29 17:29:43'),
(19, 'Pink Lemonade', 'A fun, tropical mix of pineapple and mango with no added sugar! A healthy and delicious gourmet treat. Win, win!', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/PGPtYSbnOJ9smlhRXgpLC2RIPtzTUXlkeICSBzSA.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:32:27', '2025-06-29 21:43:29'),
(20, 'Root Beer Float', 'Frozen version of the classic, creamy root beer float.', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/taLEMIVuyj4rX5IgVmOKXCcaPowAWAaBMGK6De4f.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:33:37', '2025-06-29 21:43:24'),
(21, 'Strawberry Mango', 'Refreshing mixture of delicious strawberries and mangoes', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/xHNhP3hS9ROEZKHVurkQqomajvsGZlyAFoGa68KT.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:36:02', '2025-06-29 21:43:19'),
(22, 'Strawberry', 'Classic summertime strawberry taste.', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/SWQdAT8fWPzCIFBmTAgcXC5HTwwSkECUU44IiWAL.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:37:06', '2025-06-29 21:43:21'),
(23, 'Watermelon', 'Classic watermelon. You’ll think you’re eating a real watermelon!', 65.00, 10, 48, NULL, 'images/fgp_items/ZCOcSUsAZO2TC1P0N37Goo2B7KeBnr7AQzh2J8Hq.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:38:06', '2025-06-29 17:38:06'),
(24, 'Blackberry Ginger Lemonade', 'Blackberry lemonade with a splash of ginger', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/AOJ8X7IrcwTyhXLDcz2qdjq2UPz7orxo8PVEO2BA.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:39:42', '2025-06-29 21:43:55'),
(25, 'Guavanade', 'Your newest favorite flavor', 65.00, 10, 48, NULL, 'images/fgp_items/udVHhztP6p0NiRQRY0iRIqG0v5MK0cefulzs4HHl.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:41:09', '2025-06-29 17:41:09'),
(26, 'Caramel Cheesecake', 'Decadent caramel with a graham cracker crust', 75.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"10\",\"12\"]', 'images/fgp_items/GjeDa9nP5LqZPQA6137wqD84xBeJqdctLnSXoq8y.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:52:58', '2025-06-29 21:49:33'),
(27, 'Banana Pudding', 'Classic banana pudding ice cream in a pop form', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/9FavuoRsI1q7sYDqUXgG55DzdHVrlyw2H4WXxT8H.png', NULL, NULL, 1, 2, 2, '2025-06-29 17:55:26', '2025-06-29 21:44:00'),
(28, 'Mint Chocolate Chip', 'Classic mint ice cream with chocolate chunks.', 65.00, 10, 48, '[\"6\",\"8\",\"10\",\"12\"]', 'images/fgp_items/1rBuyIcGYXbmkswMzK4yQEK4Qd0JhjcYQzcGb0Fs.jpg', NULL, NULL, 1, 2, 2, '2025-06-29 17:57:14', '2025-06-29 21:48:35'),
(29, 'Cookies & Milk', 'Cookies & Milk', 65.00, 10, 48, NULL, 'images/fgp_items/vNL07gSdnHaP4AdD8PgvfqSVwIO8b1344yoDdqPf.jpg', NULL, NULL, 1, 2, 2, '2025-06-29 17:58:43', '2025-06-29 17:58:43'),
(30, 'Cookie Dough', 'Chocolate chip cookie dough in a pop. Oh yes.', 65.00, 10, 48, '[\"7\",\"9\",\"11\"]', 'images/fgp_items/pzx5WZz9pW1eT6ctfVZcSLLHIE8TzT2jLZoed5M3.jpg', NULL, NULL, 1, 2, 2, '2025-06-29 17:59:55', '2025-06-29 21:46:16'),
(31, 'Strawberry Protein', 'Creamy Strawberry With 17g of Protein to Fuel Your Fun!', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/YTb4CB1e8hBr5CmcwDBxfcwi2ERTzzQWiDDM8u8t.png', NULL, NULL, 1, 2, 2, '2025-06-29 18:03:02', '2025-06-29 21:43:16'),
(32, 'Christmas Tree Cake', 'Christmas Tree Cake', 65.00, 10, 48, NULL, 'images/fgp_items/wgKw4ZGvXFZcEWYhafbEyrRXWAFm4mLyLKyyxurA.jpg', NULL, NULL, 1, 2, 2, '2025-06-29 18:04:02', '2025-06-29 18:04:02'),
(33, 'Pup Pop', 'Every good doggo deserves a heckin’ good PB pop. This one is tasty enough for your hooman, but made for a pup.', 65.00, 10, 48, '[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\",\"8\",\"9\",\"10\",\"11\",\"12\"]', 'images/fgp_items/RXKNOWEdCHu1wHuj6VFFMmxrIkHxhS4VmvNu4FxX.png', NULL, NULL, 1, 2, 2, '2025-06-29 18:06:31', '2025-06-29 21:43:26'),
(34, 'Peppermint White Chocolate', 'All I want for Christmas is a Peppermint White Chocolate pop!', 65.00, 10, 48, NULL, 'images/fgp_items/46Hs57lEdSoaPk9hcwKpCM4o0JsVaHXfSFAxwSyz.jpg', NULL, NULL, 1, 2, 2, '2025-06-29 18:08:07', '2025-06-29 18:08:07'),
(35, 'S’mores', 'The campfire favorite. Chocolate, marshmallow and graham cracker. Campfire not included', 65.00, 10, 48, NULL, 'images/fgp_items/R0UOjqrO8MpW1fOiC3n7D4Ox6wtvUQjf0jWrdx8h.jpg', NULL, NULL, 1, 2, 2, '2025-06-29 18:09:14', '2025-06-29 18:09:14'),
(36, 'Strawberry Mojito', 'Signature strawberry. Exciting lime juice and mint. Yummy', 65.00, 10, 48, NULL, 'images/fgp_items/T3xNSN8gPJlPzid8n4vXHZ56Wfuv3fKzogSoYEFP.jpg', NULL, NULL, 1, 2, 2, '2025-06-29 18:11:07', '2025-06-29 18:11:07'),
(37, 'Pickle', 'Pickle', 65.00, 10, 48, NULL, 'images/fgp_items/pDGhNx6gxQQWTT9IV4nUbEiEMWWoS3GQOnKLh9PU.jpg', NULL, NULL, 1, 2, 2, '2025-06-29 18:13:01', '2025-06-29 18:13:01'),
(38, 'Pumpkin Cheesecake', 'Deliciously creamy pumpkin cheesecake on a stick', 65.00, 10, 48, NULL, 'images/fgp_items/Sci9yNidIH3UuVVzD3qiRK9aqcAdsdFeYe6H3qqB.jpg', NULL, NULL, 1, 2, 2, '2025-06-29 18:14:15', '2025-06-29 18:14:15'),
(39, 'Cold Brew', 'Sweet cold brew coffee with a little pick-me-up kick', 65.00, 10, 48, '[\"7\",\"9\",\"11\"]', 'images/fgp_items/WEXPGUuWyTXUK99QDs81TRK0k9QkZSP86UpupNLW.jpg', NULL, NULL, 1, 2, 2, '2025-06-29 18:17:17', '2025-06-29 21:45:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fgp_items`
--
/* ALTER TABLE `fgp_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fgp_items_franchise_id_foreign` (`franchise_id`),
  ADD KEY `fgp_items_created_by_foreign` (`created_by`),
  ADD KEY `fgp_items_updated_by_foreign` (`updated_by`); */

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fgp_items`
--
/* ALTER TABLE `fgp_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40; */

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fgp_items`
--
/* ALTER TABLE `fgp_items`
  ADD CONSTRAINT `fgp_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fgp_items_franchise_id_foreign` FOREIGN KEY (`franchise_id`) REFERENCES `franchises` (`id`),
  ADD CONSTRAINT `fgp_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);
COMMIT; */

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
