-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2025 at 06:07 AM
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
-- Database: `olaro`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `title`, `author`, `description`, `image`, `link`, `created_at`) VALUES
(1, 'yyyyyyyyyjh', 'yyyyyyyyy', 'hjjjjjjjjjjjn\r\nkjkkkkkkkkkkkkk\r\njkkkkkkkkkkkkkkk\r\njjjjj', 'download (1).jfif', 'https://www.linkedin.com/checkpoint/challengesV2/inapp/expired/AQES9qsZ5P7Y3wAAAZaulcV6vSxLwoFaaXtwLLZ63GhmgTgCqo7FJOi4v__kIC_USm396N5bLBkV41_AwoYRKxnsPcH6SVpS8Q?isNativeApp=false&flavour=CONSUMER_LOGIN', '2025-05-08 07:12:46'),
(2, 'yyyyyyyyyjh', 'yyyyyyyyy', 'hjjjjjjjjjjjn\r\nkjkkkkkkkkkkkkk\r\njkkkkkkkkkkkkkkk\r\njjjjj', 'download (1).jfif', 'https://www.linkedin.com/checkpoint/challengesV2/inapp/expired/AQES9qsZ5P7Y3wAAAZaulcV6vSxLwoFaaXtwLLZ63GhmgTgCqo7FJOi4v__kIC_USm396N5bLBkV41_AwoYRKxnsPcH6SVpS8Q?isNativeApp=false&flavour=CONSUMER_LOGIN', '2025-05-08 07:13:16'),
(3, 'yyyyyyyyyjh', 'yyyyyyyyy', 'hjjjjjjjjjjjn\r\nkjkkkkkkkkkkkkk\r\njkkkkkkkkkkkkkkk\r\njjjjj', 'download (1).jfif', 'https://www.linkedin.com/checkpoint/challengesV2/inapp/expired/AQES9qsZ5P7Y3wAAAZaulcV6vSxLwoFaaXtwLLZ63GhmgTgCqo7FJOi4v__kIC_USm396N5bLBkV41_AwoYRKxnsPcH6SVpS8Q?isNativeApp=false&flavour=CONSUMER_LOGIN', '2025-05-08 07:13:29'),
(4, 'yyyyyyyyyjh', 'yyyyyyyyy', 'hjjjjjjjjjjjn\r\nkjkkkkkkkkkkkkk\r\njkkkkkkkkkkkkkkk\r\njjjjj', 'download (1).jfif', 'https://www.linkedin.com/checkpoint/challengesV2/inapp/expired/AQES9qsZ5P7Y3wAAAZaulcV6vSxLwoFaaXtwLLZ63GhmgTgCqo7FJOi4v__kIC_USm396N5bLBkV41_AwoYRKxnsPcH6SVpS8Q?isNativeApp=false&flavour=CONSUMER_LOGIN', '2025-05-08 07:13:37'),
(5, 'yyyyyyyyyjh', 'yyyyyyyyy', 'hjjjjjjjjjjjn\r\nkjkkkkkkkkkkkkk\r\njkkkkkkkkkkkkkkk\r\njjjjj', 'download (1).jfif', 'https://www.linkedin.com/checkpoint/challengesV2/inapp/expired/AQES9qsZ5P7Y3wAAAZaulcV6vSxLwoFaaXtwLLZ63GhmgTgCqo7FJOi4v__kIC_USm396N5bLBkV41_AwoYRKxnsPcH6SVpS8Q?isNativeApp=false&flavour=CONSUMER_LOGIN', '2025-05-08 07:13:42'),
(7, 'kkkkkkkkkkkkkkk', 'olaro', 'oooooooooooooooo\r\nkkkkkkkkkkkkkkkk', 'p.jpg', 'https://jiji.ug/mobile-phones?query=iphone+8s', '2025-05-10 22:43:20'),
(8, 'yyyyyyyyyyyy', 'olaro', 'yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy', 'p.jpg', 'https://jiji.ug/mobile-phones?query=iphone+8s', '2025-05-10 23:27:41'),
(9, 'uyyyyyyyyyyyyyyyyy', 'olaro', 'nnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn', 'p.jpg', 'https://jiji.ug/mobile-phones?query=iphone+8s', '2025-05-10 23:29:32');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_images`
--

CREATE TABLE `gallery_images` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery_images`
--

INSERT INTO `gallery_images` (`id`, `image_path`, `uploaded_at`, `created_at`) VALUES
(26, 'p.jpg', '2025-05-11 03:57:00', '2025-05-11 03:57:00'),
(28, 'p.jpg', '2025-05-11 03:57:17', '2025-05-11 03:57:17');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `image_path`, `created_at`) VALUES
(1, 'download.jfif', '2025-05-08 07:07:01'),
(2, 'download.jfif', '2025-05-08 07:07:11'),
(3, 'download.jfif', '2025-05-08 07:07:17'),
(4, 'download.jfif', '2025-05-08 07:07:27'),
(5, 'download.jfif', '2025-05-08 07:07:40'),
(6, 'download (1).jfif', '2025-05-08 07:08:26');

-- --------------------------------------------------------

--
-- Table structure for table `team_members`
--

CREATE TABLE `team_members` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(150) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_members`
--

INSERT INTO `team_members` (`id`, `name`, `role`, `image_path`, `created_at`) VALUES
(1, 'olaropeter', 'presentetr', 'download.jfif', '2025-05-08 06:37:47'),
(2, 'olaropeter', 'presentetr', 'download.jfif', '2025-05-08 06:42:56'),
(3, 'olaropeter', 'presentetr', 'download.jfif', '2025-05-08 06:43:00'),
(4, 'olaropeter', 'presentetr', 'download.jfif', '2025-05-08 06:43:02'),
(5, 'olaropeter', 'presentetr', 'download (1).jfif', '2025-05-08 08:50:08'),
(6, 'olaro', 'presentetr', 'download.jfif', '2025-05-08 08:50:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(3, 'olaro1', '$2y$10$sllLgF0wOECqybkSgoDBrulOnL25GpqGjlCl7JY0nP7x96lYoAwve', 'user', '2025-05-11 02:55:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `gallery_images`
--
ALTER TABLE `gallery_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
