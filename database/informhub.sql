-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Jun 2025 pada 15.41
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `informhub`
--
CREATE DATABASE IF NOT EXISTS InformHub;
USE InformHub;
-- --------------------------------------------------------

--
-- Struktur dari tabel `code_examples`
--

CREATE TABLE `code_examples` (
  `id` int(11) NOT NULL,
  `tutorial_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `code` text NOT NULL,
  `language` varchar(50) NOT NULL DEFAULT 'html'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `code_examples`
--

INSERT INTO `code_examples` (`id`, `tutorial_id`, `title`, `code`, `language`) VALUES
(1, 1, 'Basic HTML Document', '<!DOCTYPE html>\n<html>\n<head>\n  <title>My First Web Page</title>\n</head>\n<body>\n  <h1>Welcome to My Web Page</h1>\n  <p>This is a paragraph.</p>\n</body>\n</html>', 'html'),
(2, 2, 'Basic CSS Styling', 'body {\n  font-family: Arial, sans-serif;\n  margin: 0;\n  padding: 20px;\n  background-color: #f0f0f0;\n}\n\nh1 {\n  color: #0066cc;\n  text-align: center;\n}\n\np {\n  line-height: 1.6;\n  margin-bottom: 15px;\n}', 'css'),
(7, 6, 'Simpel cpp', '#include <iostream>\r\n\r\nint main() {\r\n  std::cout << \"Hello, World!\" << std::endl;\r\n  return 0;\r\n}', 'html'),
(8, 7, 'PHP Hello World!', '<!DOCTYPE html>\r\n<html>\r\n<body>\r\n\r\n<h1>My first PHP page</h1>\r\n\r\n<?php\r\necho \"Hello World!\";\r\n?>\r\n\r\n</body>\r\n</html>', 'html');

-- --------------------------------------------------------

--
-- Struktur dari tabel `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `parent_comment_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `upvotes` int(11) DEFAULT 0,
  `downvotes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `comments`
--

INSERT INTO `comments` (`comment_id`, `post_id`, `user_id`, `parent_comment_id`, `content`, `upvotes`, `downvotes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, 'Great guide! Very helpful for beginners.', 0, 0, '2025-05-21 16:46:24', '2025-05-21 16:46:24'),
(2, 1, 1, NULL, 'Thanks for sharing!', 0, 0, '2025-05-21 16:46:24', '2025-05-21 16:46:24'),
(3, 5, 1, NULL, 'Excellent tutorial!', 0, 0, '2025-05-21 16:46:24', '2025-05-21 16:46:24'),
(4, 9, 3, NULL, 'oke udah dapat', 0, 0, '2025-06-12 17:44:34', '2025-06-12 17:44:34'),
(5, 9, 4, NULL, 'ohhh gitu ya bang', 0, 0, '2025-06-12 18:15:18', '2025-06-12 18:15:18'),
(6, 9, 7, NULL, 'hoho gitu ya', 0, 0, '2025-06-12 19:10:45', '2025-06-12 19:10:45'),
(13, 8, 7, NULL, 'satu dua', 0, 0, '2025-06-12 19:27:19', '2025-06-12 19:27:19'),
(16, 8, 7, NULL, 'mY istri', 0, 0, '2025-06-12 19:39:30', '2025-06-12 19:39:30'),
(24, 25, 9, NULL, 'dihh', 0, 0, '2025-06-23 12:09:21', '2025-06-23 12:09:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `comments_tutorial`
--

CREATE TABLE `comments_tutorial` (
  `id` int(11) NOT NULL,
  `tutorial_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `comments_tutorial`
--

INSERT INTO `comments_tutorial` (`id`, `tutorial_id`, `user_id`, `content`, `is_pinned`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'wow keren', 0, '2025-06-22 20:57:58', '2025-06-22 20:57:58'),
(4, 1, 1, 'nah lo', 0, '2025-06-22 22:54:49', '2025-06-22 22:54:49'),
(6, 6, 9, 'wah keren kodenya', 0, '2025-06-23 11:46:54', '2025-06-23 11:46:54'),
(7, 7, 9, 'pertama', 0, '2025-06-23 11:47:13', '2025-06-23 11:47:13'),
(8, 1, 9, '!!!', 0, '2025-06-23 11:47:30', '2025-06-23 11:47:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `media_url` varchar(255) DEFAULT NULL,
  `media_type` enum('image','video','none') DEFAULT 'none',
  `is_featured` tinyint(1) DEFAULT 0,
  `is_tutorial` tinyint(1) DEFAULT 0,
  `is_pinned` tinyint(1) DEFAULT 0,
  `upvotes` int(11) DEFAULT 0,
  `downvotes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `posts`
--

INSERT INTO `posts` (`post_id`, `user_id`, `topic_id`, `title`, `content`, `media_url`, `media_type`, `is_featured`, `is_tutorial`, `is_pinned`, `upvotes`, `downvotes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'HTML5 Semantic Elements Guide', 'Here\'s a comprehensive guide to semantic HTML5 elements and when to use them...', NULL, 'none', 0, 0, 0, 0, 0, '2025-05-21 16:31:24', '2025-05-21 16:31:24'),
(2, 1, 2, 'CSS Grid vs Flexbox', 'When should you use CSS Grid versus Flexbox? Let\'s discuss the pros and cons of each...', NULL, 'none', 0, 0, 0, 1, 0, '2025-05-21 16:31:24', '2025-06-06 14:31:51'),
(3, 1, 3, 'Understanding JavaScript Promises', 'Promises are a powerful way to handle asynchronous operations in JavaScript...', NULL, 'none', 0, 0, 0, 0, 1, '2025-05-21 16:31:24', '2025-06-06 14:31:57'),
(4, 1, 1, 'HTML5 Semantic Elements Guide', 'Learn about the new semantic elements introduced in HTML5...', NULL, 'none', 1, 0, 0, 0, 2, '2025-05-21 16:45:28', '2025-06-12 21:01:55'),
(5, 1, 2, 'CSS Grid Layout Tutorial', 'Master CSS Grid with this comprehensive guide...', NULL, 'none', 1, 1, 0, 0, 1, '2025-05-21 16:45:28', '2025-06-06 14:31:30'),
(6, 1, 3, 'JavaScript ES6+ Features', 'Explore the latest features of JavaScript...', NULL, 'none', 1, 1, 0, 2, 1, '2025-05-21 16:45:28', '2025-06-23 12:07:20'),
(8, 3, 5, 'woilah cik', 'mas ini jam berapa ya ?', '684b0d4cb70ea_e8ac6deb-d41e-40d8-b041-13595f40beb1.jpg', 'image', 0, 0, 0, 1, 0, '2025-06-12 17:24:28', '2025-06-12 21:01:07'),
(9, 3, 4, 'Saya memiliki masalah di bagian php lag tidak ter load', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla auctor luctus diam sit amet pulvinar. Duis vel laoreet mauris. Morbi et ante et magna vulputate lobortis sed et nisi. Vestibulum efficitur enim id ipsum pulvinar, sit amet auctor nunc condimentum. Morbi in orci nisl. Quisque non velit id urna sollicitudin scelerisque eget non sapien. In a nibh blandit, vehicula quam ut, ultricies tellus. Fusce eu sapien tempor, blandit quam eu, fermentum nulla. Phasellus et sapien risus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae;\r\n\r\nInteger in ultricies orci. Nullam eu suscipit magna. Mauris et tempus nunc. Vivamus facilisis vestibulum feugiat. Sed euismod, nunc vel maximus hendrerit, lacus felis porttitor leo, et convallis libero magna ac ex. Maecenas ac congue est. Morbi malesuada vel ex non sagittis. Aliquam consequat nisi a sapien convallis, ut efficitur justo venenatis. Maecenas luctus tincidunt tempus. Quisque commodo tristique erat, ut semper lectus porta id. Curabitur facilisis pretium enim, eu dignissim arcu elementum in. Nulla nec nisl at nisl suscipit vulputate. Curabitur vitae ligula nec dolor euismod dictum. Phasellus a arcu auctor, rhoncus mauris nec, commodo eros.\r\n\r\nInteger viverra, justo eget finibus facilisis, dolor metus aliquet tellus, ut aliquet odio ante non nibh. Suspendisse facilisis felis vitae sodales dapibus. Phasellus vulputate in erat id mattis. Quisque dignissim, est id aliquam mattis, ligula sapien dignissim turpis, vel volutpat metus risus sit amet lorem. In eget dolor semper, ullamcorper nulla eu, feugiat lorem. Nulla facilisi. Ut suscipit placerat sem nec malesuada. Praesent sit amet erat enim. Maecenas vitae viverra risus, vulputate ornare diam. Mauris bibendum lectus et velit vehicula pharetra.\r\n\r\nInterdum et malesuada fames ac ante ipsum primis in faucibus. Aenean semper id lectus quis fermentum. Etiam convallis sollicitudin tellus vitae laoreet. Nam pretium, sem vitae venenatis molestie, diam tortor congue nibh, ut convallis felis elit at dui. Fusce tincidunt nisi dictum tellus gravida, id aliquet est interdum. Fusce ac dictum odio, quis laoreet nisl. Nam vehicula eu nibh sed egestas. Mauris non rutrum enim, sed lobortis velit. Phasellus fringilla, eros non fringilla pharetra, ligula tellus suscipit tortor, quis pretium sapien odio sit amet est. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Maecenas viverra, magna id elementum tincidunt, nisl nunc dignissim dolor, quis ultricies ipsum eros hendrerit quam. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.\r\n\r\nIn sollicitudin lorem et laoreet consectetur. Donec facilisis fermentum interdum. Integer et semper eros. Praesent feugiat pharetra enim eu viverra. Sed mattis gravida placerat. Duis consequat lectus vel dapibus ultrices. Phasellus dui justo, rhoncus at feugiat ut, sodales in ex. Nam pharetra augue porttitor ipsum vehicula, at malesuada tortor vestibulum. Phasellus vitae ligula condimentum, viverra justo eget, vestibulum nibh. Donec pellentesque ut urna a dictum. Proin eu mi non lectus lacinia fringilla ac ac arcu. Duis id nibh quis urna iaculis ultrices.', NULL, 'none', 0, 0, 0, 1, 0, '2025-06-12 17:35:03', '2025-06-12 21:01:43'),
(10, 5, 3, 'Aduh Kaki Saya Sakit', 'aduh aduh aduhhhhhh', '684b31d6e0e3e_hu tao genshin impact.jpg', 'image', 0, 0, 0, 1, 0, '2025-06-12 20:00:22', '2025-06-12 21:01:39'),
(11, 7, 4, 'Sorry ketiduran', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla auctor luctus diam sit amet pulvinar. Duis vel laoreet mauris. Morbi et ante et magna vulputate lobortis sed et nisi. Vestibulum efficitur enim id ipsum pulvinar, sit amet auctor nunc condimentum. Morbi in orci nisl. Quisque non velit id urna sollicitudin scelerisque eget non sapien. In a nibh blandit, vehicula quam ut, ultricies tellus. Fusce eu sapien tempor, blandit quam eu, fermentum nulla. Phasellus et sapien risus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae;', '684b330aceedd_06fbb5a8-0238-4a00-8c2a-723fb1f86892.jpg', 'image', 0, 0, 0, 1, 0, '2025-06-12 20:05:30', '2025-06-12 21:01:37'),
(12, 6, 5, 'Tidor 12 jam masih ngantuk', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla auctor luctus diam sit amet pulvinar. Duis vel laoreet mauris. Morbi et ante et magna vulputate lobortis sed et nisi. Vestibulum efficitur enim id ipsum pulvinar, sit amet auctor nunc condimentum. Morbi in orci nisl. Quisque non velit id urna sollicitudin scelerisque eget non sapien. In a nibh blandit, vehicula quam ut, ultricies tellus. Fusce eu sapien tempor, blandit quam eu, fermentum nulla. Phasellus et sapien risus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae;\r\n\r\nInteger in ultricies orci. Nullam eu suscipit magna. Mauris et tempus nunc. Vivamus facilisis vestibulum feugiat. Sed euismod, nunc vel maximus hendrerit, lacus felis porttitor leo, et convallis libero magna ac ex. Maecenas ac congue est. Morbi malesuada vel ex non sagittis. Aliquam consequat nisi a sapien convallis, ut efficitur justo venenatis. Maecenas luctus tincidunt tempus. Quisque commodo tristique erat, ut semper lectus porta id. Curabitur facilisis pretium enim, eu dignissim arcu elementum in. Nulla nec nisl at nisl suscipit vulputate. Curabitur vitae ligula nec dolor euismod dictum. Phasellus a arcu auctor, rhoncus mauris nec, commodo eros.\r\n\r\nInteger viverra, justo eget finibus facilisis, dolor metus aliquet tellus, ut aliquet odio ante non nibh. Suspendisse facilisis felis vitae sodales dapibus. Phasellus vulputate in erat id mattis. Quisque dignissim, est id aliquam mattis, ligula sapien dignissim turpis, vel volutpat metus risus sit amet lorem. In eget dolor semper, ullamcorper nulla eu, feugiat lorem. Nulla facilisi. Ut suscipit placerat sem nec malesuada. Praesent sit amet erat enim. Maecenas vitae viverra risus, vulputate ornare diam. Mauris bibendum lectus et velit vehicula pharetra.', '684b339edc3af_a92811f3-cb3d-4d9b-bf99-92f3bcd9adca.jpg', 'image', 0, 0, 0, 2, 0, '2025-06-12 20:07:58', '2025-06-22 20:11:20'),
(25, 3, 3, 'kamu tau gak apa yang lebih manis dari gula ?', 'kamu 💖💗💓💞💘', '6859248eebe44_胡桃 (1).jpg', 'image', 0, 0, 0, 0, 0, '2025-06-23 09:55:26', '2025-06-23 12:09:04'),
(27, 1, 1, 'Asyaapppp', 'yayyy selesai', NULL, 'none', 0, 0, 0, 0, 0, '2025-06-23 13:14:04', '2025-06-23 13:17:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `topic_spaces`
--

CREATE TABLE `topic_spaces` (
  `topic_id` int(11) NOT NULL,
  `topic_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `topic_spaces`
--

INSERT INTO `topic_spaces` (`topic_id`, `topic_name`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'HTML', 'Learn and discuss HTML basics and advanced techniques', 1, '2025-05-21 16:31:24', '2025-05-21 16:31:24'),
(2, 'CSS', 'Everything about styling your web pages with CSS', 1, '2025-05-21 16:31:24', '2025-05-21 16:31:24'),
(3, 'JavaScript', 'JavaScript programming language discussions', 1, '2025-05-21 16:31:24', '2025-05-21 16:31:24'),
(4, 'PHP', 'Server-side scripting with PHP', 1, '2025-05-21 16:31:24', '2025-05-21 16:31:24'),
(5, 'MySQL', 'Database design and queries using MySQL', 1, '2025-05-21 16:31:24', '2025-05-21 16:31:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tutorial_content`
--

CREATE TABLE `tutorial_content` (
  `tutorial_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tutorial_content`
--

INSERT INTO `tutorial_content` (`tutorial_id`, `category`, `title`, `description`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'html', 'HTML Fundamentals', 'Learn the basics of HTML, the building blocks of web pages', '2025-05-21 16:14:29', '2025-06-23 01:53:42', 1),
(2, 'css', 'CSS Styling', 'Learn how to style your HTML elements with CSS', '2025-05-21 16:14:29', '2025-06-23 01:53:37', 1),
(6, 'C++', 'CPP Dasar', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nisl elit, vehicula in dolor a, venenatis porttitor neque. Etiam sit amet mi felis. Nulla malesuada auctor sodales.', '2025-06-23 05:17:47', '2025-06-23 05:21:15', 1),
(7, 'PHP', 'PHP Dasar', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nisl elit, vehicula in dolor a, venenatis porttitor neque. Etiam sit amet mi felis. Nulla malesuada auctor sodales.', '2025-06-23 05:24:50', '2025-06-23 05:24:50', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tutorial_sections`
--

CREATE TABLE `tutorial_sections` (
  `id` int(11) NOT NULL,
  `tutorial_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `section_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tutorial_sections`
--

INSERT INTO `tutorial_sections` (`id`, `tutorial_id`, `title`, `content`, `section_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Introduction to HTML', 'HTML (HyperText Markup Language) is the standard markup language for creating web pages. It describes the structure of a web page and consists of a series of elements that tell the browser how to display the content.', 1, '2025-05-21 16:14:29', '2025-06-22 18:31:28'),
(2, 1, 'HTML Elements', 'HTML elements are represented by tags. Tags are enclosed in angle brackets, and come in pairs with opening and closing tags.', 2, '2025-05-21 16:14:29', '2025-06-22 18:31:44'),
(3, 1, 'HTML Attributes', 'HTML attributes provide additional information about an element. They are always specified in the start tag and usually come in name/value pairs.', 3, '2025-05-21 16:14:29', '2025-06-22 18:32:01'),
(4, 2, 'Introduction to CSS', 'CSS (Cascading Style Sheets) is used to style and layout web pages. It describes how HTML elements should be displayed.', 1, '2025-05-21 16:14:29', '2025-06-22 18:32:42'),
(5, 2, 'CSS Selectors', 'CSS selectors are used to \"find\" (or select) the HTML elements you want to style. They can be element selectors, class selectors, or ID selectors.', 2, '2025-05-21 16:14:29', '2025-06-22 18:32:57'),
(6, 2, 'CSS Box Model', 'The CSS box model is essentially a box that wraps around every HTML element. It consists of margins, borders, padding, and the actual content.', 3, '2025-05-21 16:14:29', '2025-06-22 18:33:09'),
(11, 6, 'Pendahuluan', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nisl elit, vehicula in dolor a, venenatis porttitor neque. Etiam sit amet mi felis. Nulla malesuada auctor sodales. Pellentesque dolor ligula, aliquet in aliquam ullamcorper, aliquam vel lorem. Curabitur metus dolor, semper at pellentesque vitae, luctus et enim. Sed sed lorem eget purus pharetra auctor sit amet id augue. Vestibulum lectus felis, bibendum eget massa eu, facilisis tincidunt urna. Donec ornare, nibh condimentum dapibus viverra, magna velit placerat metus, eget rutrum sapien elit ac est. Duis molestie metus bibendum ultricies posuere. Donec vel felis lectus. Sed pulvinar molestie pellentesque. Maecenas eget urna vel leo posuere egestas quis ut arcu. Morbi eget purus ante. Maecenas eu lacinia dolor, vitae pulvinar sem.', 1, '2025-06-23 05:21:15', '2025-06-23 05:21:15'),
(12, 6, 'Penggunaan', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nisl elit, vehicula in dolor a, venenatis porttitor neque. Etiam sit amet mi felis. Nulla malesuada auctor sodales. Pellentesque dolor ligula, aliquet in aliquam ullamcorper, aliquam vel lorem. Curabitur metus dolor, semper at pellentesque vitae, luctus et enim. Sed sed lorem eget purus pharetra auctor sit amet id augue. Vestibulum lectus felis, bibendum eget massa eu, facilisis tincidunt urna. Donec ornare, nibh condimentum dapibus viverra, magna velit placerat metus, eget rutrum sapien elit ac est. Duis molestie metus bibendum ultricies posuere. Donec vel felis lectus. Sed pulvinar molestie pellentesque. Maecenas eget urna vel leo posuere egestas quis ut arcu. Morbi eget purus ante. Maecenas eu lacinia dolor, vitae pulvinar sem.', 2, '2025-06-23 05:21:15', '2025-06-23 05:21:15'),
(13, 7, 'pendahuluan', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nisl elit, vehicula in dolor a, venenatis porttitor neque. Etiam sit amet mi felis. Nulla malesuada auctor sodales. Pellentesque dolor ligula, aliquet in aliquam ullamcorper, aliquam vel lorem. Curabitur metus dolor, semper at pellentesque vitae, luctus et enim. Sed sed lorem eget purus pharetra auctor sit amet id augue. Vestibulum lectus felis, bibendum eget massa eu, facilisis tincidunt urna. Donec ornare, nibh condimentum dapibus viverra, magna velit placerat metus, eget rutrum sapien elit ac est. Duis molestie metus bibendum ultricies posuere. Donec vel felis lectus. Sed pulvinar molestie pellentesque. Maecenas eget urna vel leo posuere egestas quis ut arcu. Morbi eget purus ante. Maecenas eu lacinia dolor, vitae pulvinar sem.', 1, '2025-06-23 05:24:50', '2025-06-23 05:24:50'),
(14, 7, 'Penggunaan', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nisl elit, vehicula in dolor a, venenatis porttitor neque. Etiam sit amet mi felis. Nulla malesuada auctor sodales. Pellentesque dolor ligula, aliquet in aliquam ullamcorper, aliquam vel lorem. Curabitur metus dolor, semper at pellentesque vitae, luctus et enim. Sed sed lorem eget purus pharetra auctor sit amet id augue. Vestibulum lectus felis, bibendum eget massa eu, facilisis tincidunt urna. Donec ornare, nibh condimentum dapibus viverra, magna velit placerat metus, eget rutrum sapien elit ac est. Duis molestie metus bibendum ultricies posuere. Donec vel felis lectus. Sed pulvinar molestie pellentesque. Maecenas eget urna vel leo posuere egestas quis ut arcu. Morbi eget purus ante. Maecenas eu lacinia dolor, vitae pulvinar sem.\r\n\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nisl elit, vehicula in dolor a, venenatis porttitor neque. Etiam sit amet mi felis. Nulla malesuada auctor sodales. Pellentesque dolor ligula, aliquet in aliquam ullamcorper, aliquam vel lorem. Curabitur metus dolor, semper at pellentesque vitae, luctus et enim. Sed sed lorem eget purus pharetra auctor sit amet id augue. Vestibulum lectus felis, bibendum eget massa eu, facilisis tincidunt urna. Donec ornare, nibh condimentum dapibus viverra, magna velit placerat metus, eget rutrum sapien elit ac est. Duis molestie metus bibendum ultricies posuere. Donec vel felis lectus. Sed pulvinar molestie pellentesque. Maecenas eget urna vel leo posuere egestas quis ut arcu. Morbi eget purus ante. Maecenas eu lacinia dolor, vitae pulvinar sem.\r\n\r\n', 2, '2025-06-23 05:24:50', '2025-06-23 05:24:50'),
(15, 7, 'Implementasi', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nisl elit, vehicula in dolor a, venenatis porttitor neque. Etiam sit amet mi felis. Nulla malesuada auctor sodales. Pellentesque dolor ligula, aliquet in aliquam ullamcorper, aliquam vel lorem. Curabitur metus dolor, semper at pellentesque vitae, luctus et enim. Sed sed lorem eget purus pharetra auctor sit amet id augue. Vestibulum lectus felis, bibendum eget massa eu, facilisis tincidunt urna. Donec ornare, nibh condimentum dapibus viverra, magna velit placerat metus, eget rutrum sapien elit ac est. Duis molestie metus bibendum ultricies posuere. Donec vel felis lectus. Sed pulvinar molestie pellentesque. Maecenas eget urna vel leo posuere egestas quis ut arcu. Morbi eget purus ante. Maecenas eu lacinia dolor, vitae pulvinar sem.', 3, '2025-06-23 05:24:50', '2025-06-23 05:24:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT 'default.jpg',
  `bio` text DEFAULT NULL,
  `role` varchar(10) DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `profile_image`, `bio`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$ahqB75k/89OboVt8FA0OM.mj2uLKOuqyGsqt.GppvZJYxppanypi2', 'default.jpg', NULL, 'admin', '2025-05-21 16:31:24', '2025-06-23 00:51:49'),
(3, 'Rakasya', 'yoga78074@gmail.com', '$2y$10$Wc3m2HMZAEEGygaKUJ6rc.xFkfVIY2OVVzUq1Pwf8x59ttiJEb4pC', '683fe2b7d577a_64d91e3c-8ce6-453e-9536-483df8a360b2.jpg', 'punya saya', 'mod', '2025-06-04 05:31:34', '2025-06-23 11:26:14'),
(4, 'Yoga', 'yogaputrapratama840@gmail.com', '$2y$10$iFY8onmh0x/zg5MNDDz7GeoTqT088KM/bG4Fs1xIYBbjb.T9SW9A2', 'default.jpg', NULL, '0', '2025-06-12 18:14:48', '2025-06-12 18:14:48'),
(5, 'Ali', 'murahbangettt003@gmail.com', '$2y$10$.MenW.jajDt..toefgmegeD/YfpdQY.NpZTtzinAtsXlzQdHlAQvC', 'default.jpg', NULL, '0', '2025-06-12 18:39:28', '2025-06-12 18:39:28'),
(6, 'Rafi', 'lirikmusikvideo79@gmail.com', '$2y$10$e/ZqKfmpu7qS/AvpbyIwQ.4fS6FepT1sGl90XIfbAlDP.FvXT71Oe', '684b3d1f78700_28a04c86-9cc5-4fb7-bce2-85892450f4be.jpg', 'muwehehe. . .', 'mod', '2025-06-12 18:43:21', '2025-06-23 10:55:59'),
(7, 'Aditt', 'adit@gmail.com', '$2y$10$/Sb.Qd84mMF/WvsGapbJ9ubtdRYmQW/tWFlnZ2uiaGd.C3.0QL7/G', 'default.jpg', NULL, 'member', '2025-06-12 18:44:29', '2025-06-23 11:03:04'),
(9, 'Ridho', 'hiudarat62@gmail.com', '$2y$10$qf2/oWsyq9v3LWVWFb5tQuG7O3CV6qFJ8qsuXtSmcpHZFbFTcMNJO', '68593f91dc155_cukurukuk.jpg', '', 'member', '2025-06-23 11:44:47', '2025-06-23 12:00:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_topic_follows`
--

CREATE TABLE `user_topic_follows` (
  `follow_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `followed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user_topic_follows`
--

INSERT INTO `user_topic_follows` (`follow_id`, `user_id`, `topic_id`, `followed_at`) VALUES
(1, 3, 1, '2025-06-12 18:06:55'),
(3, 1, 1, '2025-06-22 16:46:59'),
(4, 1, 2, '2025-06-23 07:07:06'),
(9, 9, 1, '2025-06-23 11:51:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `votes`
--

CREATE TABLE `votes` (
  `vote_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `content_type` enum('post','comment') NOT NULL,
  `content_id` int(11) NOT NULL,
  `vote_type` enum('upvote','downvote') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `votes`
--

INSERT INTO `votes` (`vote_id`, `user_id`, `content_type`, `content_id`, `vote_type`, `created_at`) VALUES
(8, 3, 'post', 4, 'downvote', '2025-06-06 14:31:21'),
(9, 3, 'post', 5, 'downvote', '2025-06-06 14:31:26'),
(10, 3, 'post', 6, 'upvote', '2025-06-06 14:31:31'),
(11, 3, 'post', 1, 'upvote', '2025-06-06 14:31:35'),
(14, 3, 'post', 2, 'upvote', '2025-06-06 14:31:49'),
(16, 3, 'post', 3, 'downvote', '2025-06-06 14:31:54'),
(19, 3, 'post', 7, 'upvote', '2025-06-06 14:32:04'),
(26, 6, 'post', 10, 'upvote', '2025-06-12 20:58:03'),
(36, 6, 'post', 8, 'upvote', '2025-06-12 21:01:06'),
(43, 6, 'post', 5, 'upvote', '2025-06-12 21:01:18'),
(45, 6, 'post', 4, 'downvote', '2025-06-12 21:01:19'),
(47, 6, 'post', 6, 'upvote', '2025-06-12 21:01:20'),
(49, 6, 'post', 2, 'upvote', '2025-06-12 21:01:22'),
(50, 6, 'post', 12, 'upvote', '2025-06-12 21:01:31'),
(53, 6, 'post', 11, 'upvote', '2025-06-12 21:01:36'),
(54, 6, 'post', 9, 'upvote', '2025-06-12 21:01:41'),
(58, 6, 'post', 7, 'upvote', '2025-06-12 21:01:48'),
(59, 1, 'post', 12, 'upvote', '2025-06-22 20:11:19'),
(60, 9, 'post', 6, 'downvote', '2025-06-23 12:07:17'),
(79, 9, 'post', 25, 'upvote', '2025-06-23 12:09:05');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `code_examples`
--
ALTER TABLE `code_examples`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tutorial_id` (`tutorial_id`);

--
-- Indeks untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_comment_id` (`parent_comment_id`);

--
-- Indeks untuk tabel `comments_tutorial`
--
ALTER TABLE `comments_tutorial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tutorial_id` (`tutorial_id`),
  ADD KEY `comments_ibfk_2` (`user_id`);

--
-- Indeks untuk tabel `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Indeks untuk tabel `topic_spaces`
--
ALTER TABLE `topic_spaces`
  ADD PRIMARY KEY (`topic_id`),
  ADD UNIQUE KEY `topic_name` (`topic_name`),
  ADD KEY `created_by` (`created_by`);

--
-- Indeks untuk tabel `tutorial_content`
--
ALTER TABLE `tutorial_content`
  ADD PRIMARY KEY (`tutorial_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_id_2` (`user_id`);

--
-- Indeks untuk tabel `tutorial_sections`
--
ALTER TABLE `tutorial_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tutorial_id` (`tutorial_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `user_topic_follows`
--
ALTER TABLE `user_topic_follows`
  ADD PRIMARY KEY (`follow_id`),
  ADD UNIQUE KEY `user_topic_unique` (`user_id`,`topic_id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Indeks untuk tabel `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`vote_id`),
  ADD UNIQUE KEY `user_content_unique` (`user_id`,`content_type`,`content_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `code_examples`
--
ALTER TABLE `code_examples`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `comments_tutorial`
--
ALTER TABLE `comments_tutorial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `topic_spaces`
--
ALTER TABLE `topic_spaces`
  MODIFY `topic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `tutorial_content`
--
ALTER TABLE `tutorial_content`
  MODIFY `tutorial_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `tutorial_sections`
--
ALTER TABLE `tutorial_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `user_topic_follows`
--
ALTER TABLE `user_topic_follows`
  MODIFY `follow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `votes`
--
ALTER TABLE `votes`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `code_examples`
--
ALTER TABLE `code_examples`
  ADD CONSTRAINT `code_examples_ibfk_1` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorial_content` (`tutorial_id`);

--
-- Ketidakleluasaan untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_comment_id`) REFERENCES `comments` (`comment_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `comments_tutorial`
--
ALTER TABLE `comments_tutorial`
  ADD CONSTRAINT `comments_tutorial_ibfk_1` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorial_content` (`tutorial_id`),
  ADD CONSTRAINT `comments_tutorial_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ketidakleluasaan untuk tabel `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`topic_id`) REFERENCES `topic_spaces` (`topic_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `topic_spaces`
--
ALTER TABLE `topic_spaces`
  ADD CONSTRAINT `topic_spaces_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `tutorial_content`
--
ALTER TABLE `tutorial_content`
  ADD CONSTRAINT `tutorial_content_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ketidakleluasaan untuk tabel `tutorial_sections`
--
ALTER TABLE `tutorial_sections`
  ADD CONSTRAINT `tutorial_sections_ibfk_1` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorial_content` (`tutorial_id`);

--
-- Ketidakleluasaan untuk tabel `user_topic_follows`
--
ALTER TABLE `user_topic_follows`
  ADD CONSTRAINT `user_topic_follows_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_topic_follows_ibfk_2` FOREIGN KEY (`topic_id`) REFERENCES `topic_spaces` (`topic_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
