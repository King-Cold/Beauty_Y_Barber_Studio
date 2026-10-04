-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 03, 2026 at 08:05 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `barberia_bbs`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellidos` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `apellidos`, `telefono`, `email`, `email_verified_at`, `password`, `fecha_nacimiento`, `role_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Prueba', '1111111111', 'admin@bbs.com', NULL, '$2y$12$Kvzv/HnzuQB23MbvlPj9oO.0Vx1LN.vBqhU9j9Kv.mEPbgSq.PtcK', NULL, 1, NULL, NULL, NULL),
(2, 'Recep', 'Prueba', '2222222222', 'recepcion@bbs.com', NULL, '$2y$12$q21EjwuDOvlJqoySJGnbveheFUlI940hEj/x1cpaZaibNCDo2sWki', NULL, 2, NULL, NULL, NULL),
(3, 'Barbero', 'Prueba', '3333333333', 'barbero@bbs.com', NULL, '$2y$12$Qqskqa29aYmYNP0blbFUPefr7Kgpbig.wvrB5KNO/n/eCa2plTEZC', NULL, 3, NULL, NULL, NULL),
(4, 'Cliente', 'Prueba', '4444444444', 'cliente@bbs.com', NULL, '$2y$12$hUs.jVG6fHptvECdfbHGyeC8lWzToDRF399rC7vSCbghaJW41ylou', NULL, 4, NULL, NULL, NULL),
(13, 'Admin', 'Prueba', '1111111112', 'aadmin@bbs.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, 1, NULL, '2026-10-03 19:39:36', '2026-10-03 19:39:36'),
(14, 'Recep', 'Prueba', '2222222223', 'recepciaon@bbs.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, 2, NULL, '2026-10-03 19:39:36', '2026-10-03 19:39:36'),
(15, 'Barbero', 'Prueba', '3333333332', 'barabero@bbs.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, 3, NULL, '2026-10-03 19:39:36', '2026-10-03 19:39:36'),
(16, 'Cliente', 'Prueba', '4444444442', 'clienate@bbs.com', NULL, '$2y$10$abcdefghijklmnopqrstuv', NULL, 4, NULL, '2026-10-03 19:39:36', '2026-10-03 19:39:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_telefono_unique` (`telefono`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
