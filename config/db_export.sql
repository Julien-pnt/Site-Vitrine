-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 06 mai 2025 à 16:10
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `elixir_du_temps`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `date_action` datetime NOT NULL DEFAULT current_timestamp(),
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `utilisateur_id`, `action`, `ip_address`, `date_action`, `details`) VALUES
(1, 1, 'accès_dashboard', '::1', '2025-04-05 18:29:43', NULL),
(2, 1, 'vider_cache', '::1', '2025-04-05 18:29:46', 'Dossiers: '),
(3, 1, 'accès_dashboard', '::1', '2025-04-05 18:42:30', NULL),
(4, 1, 'accès_dashboard', '::1', '2025-04-05 18:42:49', NULL),
(5, 1, 'accès_dashboard', '::1', '2025-04-05 18:43:01', NULL),
(6, 1, 'accès_dashboard', '::1', '2025-04-05 18:43:18', NULL),
(7, 1, 'accès_dashboard', '::1', '2025-04-05 20:06:05', NULL),
(8, 1, 'accès_dashboard', '::1', '2025-04-05 20:33:19', NULL),
(9, 1, 'vider_cache', '::1', '2025-04-05 20:33:24', 'Dossiers: '),
(10, 1, 'accès_dashboard', '::1', '2025-04-05 20:33:47', NULL),
(11, 1, 'accès_dashboard', '::1', '2025-04-05 20:35:52', NULL),
(12, 1, 'accès_dashboard', '::1', '2025-04-05 20:49:46', NULL),
(13, 1, 'accès_dashboard', '::1', '2025-04-05 20:49:47', NULL),
(14, 1, 'accès_dashboard', '::1', '2025-04-05 20:50:07', NULL),
(15, 1, 'accès_dashboard', '::1', '2025-04-05 20:50:11', NULL),
(16, 1, 'accès_dashboard', '::1', '2025-04-05 21:02:56', NULL),
(17, 1, 'accès_dashboard', '::1', '2025-04-05 21:02:58', NULL),
(18, 1, 'accès_dashboard', '::1', '2025-04-05 21:12:20', NULL),
(19, 1, 'vider_cache', '::1', '2025-04-05 21:12:21', 'Dossiers: '),
(20, 1, 'accès_dashboard', '::1', '2025-04-05 21:12:29', NULL),
(21, 1, 'accès_dashboard', '::1', '2025-04-05 21:57:34', NULL),
(22, 1, 'accès_dashboard', '::1', '2025-04-05 21:57:34', NULL),
(23, 1, 'accès_dashboard', '::1', '2025-04-05 21:57:35', NULL),
(24, 1, 'accès_dashboard', '::1', '2025-04-05 22:08:58', NULL),
(25, 1, 'accès_dashboard', '::1', '2025-04-05 22:15:42', NULL),
(26, 1, 'accès_dashboard', '::1', '2025-04-05 22:15:46', NULL),
(27, 1, 'accès_dashboard', '::1', '2025-04-05 22:16:24', NULL),
(28, 1, 'accès_dashboard', '::1', '2025-04-05 22:26:28', NULL),
(29, 1, 'accès_dashboard', '::1', '2025-04-05 22:26:52', NULL),
(30, 1, 'accès_dashboard', '::1', '2025-04-05 22:27:03', NULL),
(31, 1, 'accès_dashboard', '::1', '2025-04-05 22:27:19', NULL),
(32, 1, 'accès_dashboard', '::1', '2025-04-06 10:48:20', NULL),
(33, 1, 'accès_dashboard', '::1', '2025-04-06 11:06:30', NULL),
(34, 1, 'accès_dashboard', '::1', '2025-04-06 11:07:03', NULL),
(35, 1, 'accès_dashboard', '::1', '2025-04-06 11:16:25', NULL),
(36, 1, 'accès_dashboard', '::1', '2025-04-06 11:16:46', NULL),
(37, 1, 'accès_dashboard', '::1', '2025-04-06 11:22:25', NULL),
(38, 1, 'accès_dashboard', '::1', '2025-04-06 11:22:30', NULL),
(39, 1, 'accès_dashboard', '::1', '2025-04-06 11:22:53', NULL),
(40, 1, 'accès_dashboard', '::1', '2025-04-06 11:51:55', NULL),
(41, 1, 'accès_dashboard', '::1', '2025-04-06 11:57:12', NULL),
(42, 1, 'accès_dashboard', '::1', '2025-04-06 11:57:12', NULL),
(43, 1, 'accès_dashboard', '::1', '2025-04-06 11:57:13', NULL),
(44, 1, 'accès_dashboard', '::1', '2025-04-06 11:57:22', NULL),
(45, 1, 'accès_dashboard', '::1', '2025-04-06 11:57:38', NULL),
(46, 1, 'vider_cache', '::1', '2025-04-06 11:57:50', 'Dossiers: '),
(47, 1, 'accès_dashboard', '::1', '2025-04-06 11:57:53', NULL),
(48, 1, 'accès_dashboard', '::1', '2025-04-06 12:05:55', NULL),
(49, 1, 'accès_dashboard', '::1', '2025-04-06 12:06:40', NULL),
(50, 1, 'accès_dashboard', '::1', '2025-04-06 12:06:41', NULL),
(51, 1, 'accès_dashboard', '::1', '2025-04-06 12:06:51', NULL),
(52, 1, 'accès_dashboard', '::1', '2025-04-06 12:07:52', NULL),
(53, 1, 'accès_dashboard', '::1', '2025-04-06 12:07:56', NULL),
(54, 1, 'accès_dashboard', '::1', '2025-04-06 12:25:43', NULL),
(55, 1, 'accès_dashboard', '::1', '2025-04-06 12:32:04', NULL),
(56, 1, 'accès_dashboard', '::1', '2025-04-06 12:33:03', NULL),
(57, 1, 'accès_dashboard', '::1', '2025-04-06 12:33:05', NULL),
(58, 1, 'accès_dashboard', '::1', '2025-04-06 12:33:14', NULL),
(59, 1, 'accès_dashboard', '::1', '2025-04-06 12:33:19', NULL),
(60, 1, 'accès_dashboard', '::1', '2025-04-06 12:34:15', NULL),
(61, 1, 'accès_dashboard', '::1', '2025-04-06 12:34:21', NULL),
(62, 1, 'accès_dashboard', '::1', '2025-04-06 12:34:33', NULL),
(63, 1, 'accès_dashboard', '::1', '2025-04-06 12:34:39', NULL),
(64, 1, 'accès_dashboard', '::1', '2025-04-06 13:35:14', NULL),
(65, 1, 'accès_dashboard', '::1', '2025-04-06 13:35:15', NULL),
(66, 1, 'accès_dashboard', '::1', '2025-04-06 13:35:15', NULL),
(67, 1, 'accès_dashboard', '::1', '2025-04-06 13:36:21', NULL),
(68, 1, 'accès_dashboard', '::1', '2025-04-06 13:36:35', NULL),
(69, 1, 'accès_dashboard', '::1', '2025-04-06 13:38:11', NULL),
(70, 1, 'accès_dashboard', '::1', '2025-04-06 13:38:18', NULL),
(71, 1, 'accès_dashboard', '::1', '2025-04-06 13:41:51', NULL),
(72, 1, 'accès_dashboard', '::1', '2025-04-06 13:42:18', NULL),
(73, 1, 'accès_dashboard', '::1', '2025-04-06 13:42:31', NULL),
(74, 1, 'accès_dashboard', '::1', '2025-04-06 13:42:37', NULL),
(75, 1, 'accès_dashboard', '::1', '2025-04-06 13:42:43', NULL),
(76, 1, 'accès_dashboard', '::1', '2025-04-06 13:48:08', NULL),
(77, 1, 'accès_dashboard', '::1', '2025-04-06 13:48:10', NULL),
(78, 1, 'accès_dashboard', '::1', '2025-04-06 13:48:11', NULL),
(79, 1, 'accès_dashboard', '::1', '2025-04-06 14:00:59', NULL),
(80, 1, 'accès_dashboard', '::1', '2025-04-06 14:01:03', NULL),
(81, 1, 'accès_dashboard', '::1', '2025-04-06 14:11:54', NULL),
(82, 1, 'accès_dashboard', '::1', '2025-04-06 14:29:29', NULL),
(83, 1, 'accès_dashboard', '::1', '2025-04-06 15:45:43', NULL),
(84, 1, 'accès_dashboard', '::1', '2025-04-06 15:46:00', NULL),
(85, 1, 'accès_dashboard', '::1', '2025-04-06 15:55:31', NULL),
(86, 1, 'accès_dashboard', '::1', '2025-04-06 15:56:03', NULL),
(87, 1, 'accès_dashboard', '::1', '2025-04-06 15:56:04', NULL),
(88, 1, 'accès_dashboard', '::1', '2025-04-06 15:56:04', NULL),
(89, 1, 'accès_dashboard', '::1', '2025-04-06 15:56:05', NULL),
(90, 1, 'accès_dashboard', '::1', '2025-04-06 15:56:07', NULL),
(91, 1, 'accès_dashboard', '::1', '2025-04-06 15:56:08', NULL),
(92, 1, 'accès_dashboard', '::1', '2025-04-06 15:56:10', NULL),
(93, 1, 'accès_dashboard', '::1', '2025-04-06 15:56:43', NULL),
(94, 1, 'accès_dashboard', '::1', '2025-04-06 15:56:43', NULL),
(95, 1, 'accès_dashboard', '::1', '2025-04-06 15:56:43', NULL),
(96, 1, 'accès_dashboard', '::1', '2025-04-06 15:56:44', NULL),
(97, 1, 'accès_dashboard', '::1', '2025-04-06 15:56:44', NULL),
(98, 1, 'accès_dashboard', '::1', '2025-04-06 15:56:44', NULL),
(99, 1, 'accès_dashboard', '::1', '2025-04-06 15:56:44', NULL),
(100, 1, 'accès_dashboard', '::1', '2025-04-06 15:56:44', NULL),
(101, 1, 'accès_dashboard', '::1', '2025-04-06 15:57:10', NULL),
(102, 1, 'accès_dashboard', '::1', '2025-04-06 15:57:13', NULL),
(103, 1, 'accès_dashboard', '::1', '2025-04-06 15:57:14', NULL),
(104, 1, 'accès_dashboard', '::1', '2025-04-06 15:57:29', NULL),
(105, 1, 'accès_dashboard', '::1', '2025-04-06 15:57:29', NULL),
(106, 1, 'accès_dashboard', '::1', '2025-04-06 15:57:30', NULL),
(107, 1, 'accès_dashboard', '::1', '2025-04-06 15:57:31', NULL),
(108, 1, 'accès_dashboard', '::1', '2025-04-06 16:02:31', NULL),
(109, 1, 'accès_dashboard', '::1', '2025-04-06 16:02:32', NULL),
(110, 1, 'accès_dashboard', '::1', '2025-04-06 16:05:58', NULL),
(111, 1, 'accès_dashboard', '::1', '2025-04-06 16:07:20', NULL),
(112, 1, 'accès_dashboard', '::1', '2025-04-06 16:07:30', NULL),
(113, 1, 'accès_dashboard', '::1', '2025-04-06 18:04:50', NULL),
(114, 1, 'accès_dashboard', '::1', '2025-04-06 18:05:19', NULL),
(115, 1, 'accès_dashboard', '::1', '2025-04-06 18:10:06', NULL),
(116, 1, 'accès_dashboard', '::1', '2025-04-06 18:11:43', NULL),
(117, 1, 'accès_dashboard', '::1', '2025-04-06 18:11:57', NULL),
(118, 1, 'accès_dashboard', '::1', '2025-04-06 18:12:15', NULL),
(119, 1, 'accès_dashboard', '::1', '2025-04-06 18:12:23', NULL),
(120, 1, 'accès_dashboard', '::1', '2025-04-06 18:12:29', NULL),
(121, 1, 'accès_dashboard', '::1', '2025-04-06 18:16:13', NULL),
(122, 1, 'accès_dashboard', '::1', '2025-04-06 18:16:15', NULL),
(123, 1, 'accès_dashboard', '::1', '2025-04-06 18:16:31', NULL),
(124, 1, 'accès_dashboard', '::1', '2025-04-06 18:17:38', NULL),
(125, 1, 'accès_dashboard', '::1', '2025-04-06 18:21:34', NULL),
(126, 1, 'accès_dashboard', '::1', '2025-04-06 18:22:06', NULL),
(127, 1, 'accès_dashboard', '::1', '2025-04-06 18:22:17', NULL),
(128, 1, 'accès_dashboard', '::1', '2025-04-06 18:23:02', NULL),
(129, 1, 'accès_dashboard', '::1', '2025-04-06 18:23:57', NULL),
(130, 1, 'accès_dashboard', '::1', '2025-04-06 18:24:01', NULL),
(131, 1, 'accès_dashboard', '::1', '2025-04-06 18:28:04', NULL),
(132, 1, 'accès_dashboard', '::1', '2025-04-06 18:28:56', NULL),
(133, 1, 'accès_dashboard', '::1', '2025-04-12 18:52:11', NULL),
(134, 1, 'accès_dashboard', '::1', '2025-04-12 18:52:39', NULL),
(135, 1, 'accès_dashboard', '::1', '2025-04-12 19:07:37', NULL),
(136, 1, 'accès_dashboard', '::1', '2025-04-12 19:07:38', NULL),
(137, 1, 'accès_dashboard', '::1', '2025-04-13 08:22:38', NULL),
(138, 1, 'accès_dashboard', '::1', '2025-04-13 18:56:24', NULL),
(139, 1, 'accès_dashboard', '::1', '2025-04-13 19:00:48', NULL),
(140, 1, 'accès_dashboard', '::1', '2025-04-13 21:34:58', NULL),
(141, 1, 'accès_dashboard', '::1', '2025-04-13 21:34:58', NULL),
(142, 1, 'accès_dashboard', '::1', '2025-04-13 21:35:03', NULL),
(143, 1, 'accès_dashboard', '::1', '2025-04-19 11:03:14', NULL),
(144, 1, 'accès_dashboard', '::1', '2025-04-19 11:03:41', NULL),
(145, 1, 'accès_dashboard', '::1', '2025-04-19 11:09:41', NULL),
(146, 1, 'accès_dashboard', '::1', '2025-04-19 11:11:54', NULL),
(147, 1, 'accès_dashboard', '::1', '2025-04-19 11:11:55', NULL),
(148, 1, 'accès_dashboard', '::1', '2025-04-19 11:11:55', NULL),
(149, 1, 'accès_dashboard', '::1', '2025-04-19 12:19:03', NULL),
(150, 1, 'accès_gestion_avis', '::1', '2025-04-19 12:19:06', NULL),
(151, 1, 'accès_dashboard', '::1', '2025-04-19 12:19:19', NULL),
(152, 1, 'accès_dashboard', '::1', '2025-04-20 12:56:52', NULL),
(153, 1, 'accès_dashboard', '::1', '2025-04-20 12:56:57', NULL),
(154, 1, 'accès_dashboard', '::1', '2025-04-20 14:28:17', NULL),
(155, 1, 'accès_dashboard', '::1', '2025-04-20 15:36:52', NULL),
(156, 1, 'accès_dashboard', '::1', '2025-05-01 15:02:03', NULL),
(157, 1, 'accès_dashboard', '::1', '2025-05-01 15:02:09', NULL),
(158, 1, 'accès_dashboard', '::1', '2025-05-03 14:11:51', NULL),
(159, 1, 'accès_dashboard', '::1', '2025-05-03 14:11:52', NULL),
(160, 1, 'accès_dashboard', '::1', '2025-05-03 14:11:53', NULL),
(161, 1, 'accès_dashboard', '::1', '2025-05-03 14:11:53', NULL),
(162, 1, 'accès_dashboard', '::1', '2025-05-03 14:11:53', NULL),
(163, 1, 'accès_dashboard', '::1', '2025-05-03 14:11:53', NULL),
(164, 1, 'accès_dashboard', '::1', '2025-05-03 14:11:53', NULL),
(165, 1, 'accès_dashboard', '::1', '2025-05-03 14:11:54', NULL),
(166, 1, 'accès_dashboard', '::1', '2025-05-03 14:11:54', NULL),
(167, 1, 'accès_dashboard', '::1', '2025-05-03 14:11:54', NULL),
(168, 1, 'accès_dashboard', '::1', '2025-05-03 14:11:54', NULL),
(169, 1, 'accès_gestion_avis', '::1', '2025-05-03 14:11:57', NULL),
(170, 1, 'accès_dashboard', '::1', '2025-05-03 14:12:01', NULL),
(171, 1, 'accès_gestion_avis', '::1', '2025-05-03 14:12:04', NULL),
(172, 1, 'accès_gestion_avis', '::1', '2025-05-03 14:12:05', NULL),
(173, 1, 'accès_dashboard', '::1', '2025-05-03 14:12:07', NULL),
(174, 1, 'accès_dashboard', '::1', '2025-05-03 15:48:58', NULL),
(175, 1, 'accès_dashboard', '::1', '2025-05-04 15:51:00', NULL),
(176, 1, 'accès_dashboard', '::1', '2025-05-04 15:55:37', NULL),
(177, 1, 'accès_dashboard', '::1', '2025-05-04 16:43:14', NULL),
(178, 1, 'accès_dashboard', '::1', '2025-05-04 16:59:13', NULL),
(179, 1, 'accès_dashboard', '::1', '2025-05-04 18:37:33', NULL),
(180, 1, 'accès_dashboard', '::1', '2025-05-04 18:37:44', NULL),
(181, 1, 'accès_dashboard', '::1', '2025-05-04 18:37:47', NULL),
(182, 1, 'accès_dashboard', '::1', '2025-05-04 18:40:49', NULL),
(183, 1, 'accès_dashboard', '::1', '2025-05-04 18:41:26', NULL),
(184, 1, 'accès_dashboard', '::1', '2025-05-04 18:41:56', NULL),
(185, 1, 'accès_dashboard', '::1', '2025-05-04 18:42:03', NULL),
(186, 1, 'accès_dashboard', '::1', '2025-05-04 19:00:12', NULL),
(187, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:00:17', NULL),
(188, 1, 'accès_dashboard', '::1', '2025-05-04 19:00:18', NULL),
(189, 1, 'accès_dashboard', '::1', '2025-05-04 19:00:38', NULL),
(190, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:01:04', NULL),
(191, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:08:34', NULL),
(192, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:08:45', NULL),
(193, 1, 'accès_dashboard', '::1', '2025-05-04 19:08:49', NULL),
(194, 1, 'accès_dashboard', '::1', '2025-05-04 19:08:52', NULL),
(195, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:08:54', NULL),
(196, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:08:57', NULL),
(197, 1, 'accès_dashboard', '::1', '2025-05-04 19:09:20', NULL),
(198, 1, 'accès_dashboard', '::1', '2025-05-04 19:09:25', NULL),
(199, 1, 'accès_dashboard', '::1', '2025-05-04 19:15:12', NULL),
(200, 1, 'accès_dashboard', '::1', '2025-05-04 19:16:18', NULL),
(201, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:16:26', NULL),
(202, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:16:32', NULL),
(203, 1, 'accès_dashboard', '::1', '2025-05-04 19:16:37', NULL),
(204, 1, 'accès_dashboard', '::1', '2025-05-04 19:21:48', NULL),
(205, 1, 'accès_dashboard', '::1', '2025-05-04 19:21:52', NULL),
(206, 1, 'accès_dashboard', '::1', '2025-05-04 19:25:38', NULL),
(207, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:27:59', NULL),
(208, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:28:02', NULL),
(209, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:28:02', NULL),
(210, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:28:02', NULL),
(211, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:28:02', NULL),
(212, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:28:03', NULL),
(213, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:28:03', NULL),
(214, 1, 'accès_dashboard', '::1', '2025-05-04 19:28:05', NULL),
(215, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:28:13', NULL),
(216, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:28:18', NULL),
(217, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:28:47', NULL),
(218, 1, 'accès_dashboard', '::1', '2025-05-04 19:29:51', NULL),
(219, 1, 'accès_dashboard', '::1', '2025-05-04 19:29:58', NULL),
(220, 1, 'accès_dashboard', '::1', '2025-05-04 19:30:00', NULL),
(221, 1, 'accès_dashboard', '::1', '2025-05-04 19:30:03', NULL),
(222, 1, 'accès_dashboard', '::1', '2025-05-04 19:31:15', NULL),
(223, 1, 'accès_dashboard', '::1', '2025-05-04 19:31:17', NULL),
(224, 1, 'accès_dashboard', '::1', '2025-05-04 19:31:22', NULL),
(225, 1, 'accès_dashboard', '::1', '2025-05-04 19:37:59', NULL),
(226, 1, 'accès_dashboard', '::1', '2025-05-04 19:38:01', NULL),
(227, 1, 'accès_dashboard', '::1', '2025-05-04 19:38:07', NULL),
(228, 1, 'accès_dashboard', '::1', '2025-05-04 19:39:06', NULL),
(229, 1, 'accès_dashboard', '::1', '2025-05-04 19:39:10', NULL),
(230, 1, 'accès_dashboard', '::1', '2025-05-04 19:39:11', NULL),
(231, 1, 'accès_dashboard', '::1', '2025-05-04 19:39:13', NULL),
(232, 1, 'accès_dashboard', '::1', '2025-05-04 19:39:15', NULL),
(233, 1, 'accès_dashboard', '::1', '2025-05-04 19:39:17', NULL),
(234, 1, 'accès_dashboard', '::1', '2025-05-04 19:39:53', NULL),
(235, 1, 'accès_dashboard', '::1', '2025-05-04 19:39:56', NULL),
(236, 1, 'accès_dashboard', '::1', '2025-05-04 19:39:58', NULL),
(237, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:41:25', NULL),
(238, 1, 'accès_dashboard', '::1', '2025-05-04 19:41:34', NULL),
(239, 1, 'accès_dashboard', '::1', '2025-05-04 19:41:37', NULL),
(240, 1, 'accès_dashboard', '::1', '2025-05-04 19:44:23', NULL),
(241, 1, 'accès_dashboard', '::1', '2025-05-04 19:44:24', NULL),
(242, 1, 'accès_dashboard', '::1', '2025-05-04 19:44:25', NULL),
(243, 1, 'accès_dashboard', '::1', '2025-05-04 19:45:47', NULL),
(244, 1, 'accès_dashboard', '::1', '2025-05-04 19:45:47', NULL),
(245, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:45:53', NULL),
(246, 1, 'accès_dashboard', '::1', '2025-05-04 19:46:13', NULL),
(247, 1, 'accès_dashboard', '::1', '2025-05-04 19:48:36', NULL),
(248, 1, 'accès_gestion_avis', '::1', '2025-05-04 19:48:38', NULL),
(249, 1, 'accès_dashboard', '::1', '2025-05-04 19:48:43', NULL),
(250, 1, 'accès_dashboard', '::1', '2025-05-04 19:52:50', NULL),
(251, 1, 'accès_dashboard', '::1', '2025-05-04 19:54:21', NULL),
(252, 1, 'accès_dashboard', '::1', '2025-05-04 19:54:22', NULL),
(253, 1, 'accès_dashboard', '::1', '2025-05-04 19:54:30', NULL),
(254, 1, 'accès_dashboard', '::1', '2025-05-04 19:55:40', NULL),
(255, 1, 'accès_dashboard', '::1', '2025-05-04 19:55:41', NULL),
(256, 1, 'accès_gestion_avis', '::1', '2025-05-04 20:15:37', NULL),
(257, 1, 'accès_dashboard', '::1', '2025-05-04 21:14:54', NULL),
(258, 1, 'accès_dashboard', '::1', '2025-05-04 21:17:13', NULL),
(259, 1, 'accès_dashboard', '::1', '2025-05-04 21:17:23', NULL),
(260, 1, 'accès_dashboard', '::1', '2025-05-04 21:30:04', NULL),
(261, 1, 'accès_gestion_avis', '::1', '2025-05-04 21:51:27', NULL),
(262, 1, 'accès_dashboard', '::1', '2025-05-04 21:51:35', NULL),
(263, 1, 'accès_dashboard', '::1', '2025-05-04 22:15:17', NULL),
(264, 1, 'accès_dashboard', '::1', '2025-05-04 22:16:42', NULL),
(265, 1, 'accès_dashboard', '::1', '2025-05-04 22:16:48', NULL),
(266, 1, 'accès_dashboard', '::1', '2025-05-04 22:16:50', NULL),
(267, 1, 'accès_dashboard', '::1', '2025-05-04 22:16:58', NULL),
(268, 1, 'vider_cache', '::1', '2025-05-04 22:17:01', 'Dossiers: '),
(269, 1, 'accès_dashboard', '::1', '2025-05-04 22:17:03', NULL),
(270, 1, 'accès_gestion_avis', '::1', '2025-05-04 22:17:08', NULL),
(271, 1, 'accès_dashboard', '::1', '2025-05-04 22:17:16', NULL),
(272, 1, 'accès_dashboard', '::1', '2025-05-04 22:18:03', NULL),
(273, 1, 'accès_dashboard', '::1', '2025-05-04 22:18:04', NULL),
(274, 1, 'accès_dashboard', '::1', '2025-05-04 22:18:06', NULL),
(275, 1, 'accès_dashboard', '::1', '2025-05-04 22:18:10', NULL),
(276, 1, 'accès_gestion_avis', '::1', '2025-05-04 22:18:11', NULL),
(277, 1, 'accès_dashboard', '::1', '2025-05-04 22:18:13', NULL),
(278, 1, 'accès_dashboard', '::1', '2025-05-04 22:22:26', NULL),
(279, 1, 'accès_dashboard', '::1', '2025-05-04 22:38:01', NULL),
(280, 1, 'accès_dashboard', '::1', '2025-05-05 08:45:17', NULL),
(281, 1, 'accès_dashboard', '::1', '2025-05-06 12:28:02', NULL),
(282, 1, 'accès_dashboard', '::1', '2025-05-06 12:34:52', NULL),
(283, 1, 'accès_dashboard', '::1', '2025-05-06 12:35:06', NULL),
(284, 1, 'accès_dashboard', '::1', '2025-05-06 12:53:20', NULL),
(285, 1, 'accès_dashboard', '::1', '2025-05-06 13:03:24', NULL),
(286, 1, 'accès_gestion_avis', '::1', '2025-05-06 13:03:29', NULL),
(287, 1, 'accès_dashboard', '::1', '2025-05-06 13:04:57', NULL),
(288, 1, 'accès_dashboard', '::1', '2025-05-06 13:20:13', NULL),
(289, 1, 'accès_dashboard', '::1', '2025-05-06 14:45:08', NULL),
(290, 1, 'delete_product', '::1', '2025-05-06 14:47:10', 'Suppression du produit: test (ID: 756)'),
(291, 1, 'update_product', '::1', '2025-05-06 14:56:28', 'Mise à jour du produit: Test (ID: 757)'),
(292, 1, 'update_product', '::1', '2025-05-06 14:56:41', 'Mise à jour du produit: Test (ID: 757)'),
(293, 1, 'accès_dashboard', '::1', '2025-05-06 14:57:24', NULL),
(294, 1, 'deactivate_product', '::1', '2025-05-06 14:57:54', 'Désactivation du produit: Test (ID: 757)'),
(295, 1, 'activate_product', '::1', '2025-05-06 14:57:58', 'Activation du produit: Test (ID: 757)'),
(296, 1, 'update_product', '::1', '2025-05-06 14:58:49', 'Mise à jour du produit: Test (ID: 757)'),
(297, 1, 'delete_product', '::1', '2025-05-06 15:04:58', 'Suppression du produit: Test (ID: 757)'),
(298, 1, 'create_product', '::1', '2025-05-06 15:40:29', 'Création du produit: Test (ID: 758)'),
(299, 1, 'update_product', '::1', '2025-05-06 15:40:53', 'Mise à jour du produit: Test (ID: 758)'),
(300, 1, 'delete_product', '::1', '2025-05-06 15:41:22', 'Suppression du produit: Test (ID: 758)'),
(301, 1, 'accès_dashboard', '::1', '2025-05-06 15:44:05', NULL),
(302, 1, 'accès_dashboard', '::1', '2025-05-06 15:44:43', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `articles_commande`
--

CREATE TABLE `articles_commande` (
  `id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `nom_produit` varchar(255) NOT NULL,
  `reference_produit` varchar(50) DEFAULT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `prix_total` decimal(10,2) NOT NULL,
  `taux_taxe` decimal(5,2) DEFAULT 20.00,
  `montant_taxe` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `articles_commande`
--

INSERT INTO `articles_commande` (`id`, `commande_id`, `produit_id`, `nom_produit`, `reference_produit`, `quantite`, `prix_unitaire`, `prix_total`, `taux_taxe`, `montant_taxe`) VALUES
(5, 13, 101, 'Élégance Éternelle', 'ELX-CL-001', 1, 8950.00, 8950.00, 20.00, 1790.00),
(6, 14, 103, 'Raffinement Or', 'ELX-CL-003', 1, 12800.00, 12800.00, 20.00, 2560.00),
(7, 15, 104, 'Heritage Prestige', 'ELX-CL-004', 1, 7500.00, 7500.00, 20.00, 1500.00);

-- --------------------------------------------------------

--
-- Structure de la table `attributs`
--

CREATE TABLE `attributs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `type` enum('texte','nombre','booleen','liste') NOT NULL DEFAULT 'texte'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `auth_tokens`
--

CREATE TABLE `auth_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `auth_tokens`
--

INSERT INTO `auth_tokens` (`id`, `user_id`, `token`, `created_at`, `expires_at`) VALUES
(1, 2, '3f92f96bd9c72952958ad71dff639a0d03af2c32ef5d4965d72de898aa325a2b', '2025-05-06 10:32:56', '2025-06-05 12:32:56');

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `note` int(11) NOT NULL CHECK (`note` between 1 and 5),
  `commentaire` text DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `statut` enum('en_attente','approuve','rejete') DEFAULT 'en_attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `slug`, `description`, `image`, `parent_id`, `position`) VALUES
(1, 'Montres Homme', 'montres-homme', 'Notre collection exclusive de montres pour homme combine élégance intemporelle et précision technique exceptionnelle.', '/assets/img/categories/montres-homme.jpg', NULL, 10),
(2, 'Montres Sport', 'montres-sport', 'Des montres robustes conçues pour résister aux conditions les plus extrêmes tout en conservant un style incomparable.', '/assets/img/categories/montres-sport.jpg', NULL, 20),
(3, 'Montres Connectées', 'montres-connectees', 'L\'alliance parfaite entre tradition horlogère et technologie de pointe pour les amateurs d\'horlogerie contemporaine.', '/assets/img/categories/montres-connectees.jpg', NULL, 30),
(4, 'Haute Horlogerie', 'haute-horlogerie', 'Des pièces d\'exception qui incarnent le summum de l\'art horloger avec des complications et matériaux prestigieux.', '/assets/img/categories/haute-horlogerie.jpg', NULL, 40),
(5, 'Montres Femme', 'montres-femme', 'Une sélection raffinée de montres féminines alliant élégance, précision et matériaux précieux.', '/assets/img/categories/montres-femme.jpg', NULL, 50);

-- --------------------------------------------------------

--
-- Structure de la table `collections`
--

CREATE TABLE `collections` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `collections`
--

INSERT INTO `collections` (`id`, `nom`, `slug`, `description`, `image`, `date_debut`, `date_fin`, `active`) VALUES
(1, 'Collection Classic', 'collection-classic', 'L\'élégance intemporelle dans sa forme la plus pure. Notre collection Classic rend hommage aux traditions horlogères avec une touche contemporaine.', '/assets/img/collections/classic-collection.jpg', '2019-05-15', NULL, 1),
(2, 'Collection Sport', 'collection-sport', 'Des montres conçues pour l\'action et l\'aventure. La Collection Sport allie robustesse, précision et élégance dynamique.', '/assets/img/collections/sport-collection.jpg', '2020-03-10', NULL, 1),
(3, 'Collection Prestige', 'collection-prestige', 'L\'apogée de notre savoir-faire horloger. La Collection Prestige met en valeur des matériaux d\'exception et des finitions exquises.', '/assets/img/collections/prestige-collection.jpg', '2021-06-28', NULL, 1),
(4, 'Collection Limited Edition', 'collection-limited-edition', 'Des créations exclusives produites en série limitée et numérotée. Chaque montre de cette collection est un chef-d\'œuvre d\'horlogerie.', '/assets/img/collections/limited-edition-collection.jpg', '2022-11-30', NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `statut` enum('en_attente','payee','en_preparation','expediee','livree','annulee','remboursee') DEFAULT 'en_attente',
  `date_commande` datetime DEFAULT current_timestamp(),
  `date_modification` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `total_produits` decimal(10,2) NOT NULL,
  `frais_livraison` decimal(10,2) DEFAULT 0.00,
  `total_taxe` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `mode_paiement` varchar(50) DEFAULT NULL,
  `adresse_livraison` text NOT NULL,
  `adresse_facturation` text NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `reference`, `utilisateur_id`, `statut`, `date_commande`, `date_modification`, `total_produits`, `frais_livraison`, `total_taxe`, `total`, `mode_paiement`, `adresse_livraison`, `adresse_facturation`, `notes`) VALUES
(13, 'CMD-2025050001', 1, 'payee', '2025-05-04 21:22:06', NULL, 8950.00, 9.99, 1790.00, 10749.99, 'carte_credit', '12 Rue du Commerce, 75001 Paris', '12 Rue du Commerce, 75001 Paris', 'Commande prioritaire'),
(14, 'CMD-2025050002', 2, 'en_preparation', '2025-05-04 17:22:06', NULL, 12800.00, 0.00, 2560.00, 15360.00, 'paypal', '45 Avenue des Fleurs, 69002 Lyon', '45 Avenue des Fleurs, 69002 Lyon', NULL),
(15, 'CMD-2025050003', 3, 'expediee', '2025-05-03 22:22:06', NULL, 7500.00, 9.99, 1500.00, 9009.99, 'carte_credit', '8 Boulevard des Arbres, 33000 Bordeaux', '8 Boulevard des Arbres, 33000 Bordeaux', 'Client fidèle');

-- --------------------------------------------------------

--
-- Structure de la table `connexions_log`
--

CREATE TABLE `connexions_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `action` enum('login','logout','failed_attempt') DEFAULT 'login',
  `date_connexion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `favoris`
--

CREATE TABLE `favoris` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `date_ajout` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modification` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `publiee` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

CREATE TABLE `panier` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1,
  `date_ajout` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `description_courte` varchar(500) DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `prix_promo` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `images_supplementaires` text DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `stock_alerte` int(11) DEFAULT 5,
  `poids` decimal(8,3) DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  `collection_id` int(11) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modification` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `visible` tinyint(1) DEFAULT 1,
  `featured` tinyint(1) DEFAULT 0,
  `nouveaute` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `slug`, `reference`, `description`, `description_courte`, `prix`, `prix_promo`, `image`, `images_supplementaires`, `stock`, `stock_alerte`, `poids`, `categorie_id`, `collection_id`, `date_creation`, `date_modification`, `visible`, `featured`, `nouveaute`) VALUES
(101, 'Élégance Éternelle', 'elegance-eternelle', 'ELX-CL-001', 'L\'Élégance Éternelle incarne l\'apogée de l\'horlogerie classique avec son boîtier en or rose 18k et son cadran guilloché à la main. Son mouvement manufacture offre une réserve de marche exceptionnelle de 72 heures et une précision chronométrique certifiée. La finition méticuleuse de chaque composant, des ponts décorés à la main aux vis polies en bleu, témoigne du savoir-faire inégalé de nos artisans horlogers.', 'Montre automatique en or rose 18k avec cadran guilloché et mouvement manufacture', 8950.00, NULL, 'elegance-eternal.jpg', 'elegance-eternal-detail1.jpg,elegance-eternal-detail2.jpg', 50, 4, 0.125, 1, 1, '2025-04-05 18:28:46', '2025-05-06 15:23:45', 1, 1, 0),
(102, 'Tradition Classique', 'tradition-classique', 'ELX-CL-002', 'La Tradition Classique allie élégance et savoir-faire horloger avec son boîtier en acier inoxydable 316L aux finitions alternées polies et satinées. Son cadran argenté soleil et ses index appliqués créent un effet de profondeur saisissant. Le mouvement à remontage manuel est visible à travers le fond saphir et offre une autonomie de 56 heures. Un hommage aux grands classiques de l\'horlogerie avec une touche contemporaine.', 'Montre à remontage manuel en acier inoxydable avec finitions polies et satinées', 4750.00, NULL, 'tradition-classique.jpg', NULL, 14, 5, 0.110, 1, 1, '2025-04-05 18:28:46', '2025-05-06 15:23:45', 1, 0, 0),
(103, 'Raffinement Or', 'raffinement-or', 'ELX-CL-003', 'Chef-d\'œuvre de transparence, cette montre squelette en or jaune 18k dévoile les rouages de son mouvement manufacture apparent. Ses 274 composants sont assemblés à la main par nos maîtres horlogers. La cage squelettée permet d\'admirer les engrenages en action, tandis que l\'indicateur de réserve de marche de 72 heures rappelle sa complexité technique remarquable. Un véritable témoignage d\'excellence horlogère.', 'Montre squelette en or jaune 18k avec mouvement apparent et réserve de marche', 12800.00, NULL, 'raffinement-or.jpg', 'raffinement-or-detail1.jpg', 8, 3, 0.135, 4, 1, '2025-04-05 18:28:46', '2025-05-06 15:23:45', 1, 1, 0),
(104, 'Heritage Prestige', 'heritage-prestige', 'ELX-CL-004', 'L\'Heritage Prestige revisite les codes classiques de l\'horlogerie avec une sensibilité contemporaine. Son chronographe à roue à colonnes, considéré comme l\'une des complications les plus nobles, offre une précision irréprochable. La lunette en céramique high-tech contraste élégamment avec le cadran laqué blanc cassé et le bracelet en alligator cousu main. Un garde-temps qui transcende les époques.', 'Montre chronographe en acier avec lunette en céramique et bracelet en alligator', 7500.00, NULL, 'heritage-prestige.jpg', NULL, 18, 5, 0.142, 1, 1, '2025-04-05 18:28:46', '2025-05-06 15:23:45', 1, 0, 0),
(201, 'Élégance Rose', 'elegance-rose', 'ELX-CL-F01', 'Cette montre féminine allie délicatesse et précision avec son boîtier en or rose 18k serti de 60 diamants sur la lunette (0.75 carats au total). Son cadran en nacre rosée est sublimé par des index appliqués en or rose et son bracelet en cuir d\'alligator complète son esthétique raffinée. Le mouvement automatique suisse offre une fiabilité sans compromis et une réserve de marche de 50 heures.', 'Montre en or rose 18k sertie de 60 diamants et bracelet en cuir d\'alligator', 13500.00, NULL, 'elegance-rose.jpg', 'elegance-rose-detail1.jpg', 12, 3, 0.095, 5, 1, '2025-04-05 18:28:46', '2025-05-06 15:23:45', 1, 1, 0),
(202, 'Pureté Divine', 'purete-divine', 'ELX-CL-F02', 'Avec son design minimaliste et élégant, cette montre automatique pour femme présente un boîtier en acier poli de 29mm. Son cadran en nacre blanche est orné de 8 diamants en guise d\'index et ses aiguilles bleuies apportent une touche de sophistication. Le bracelet en alligator blanc complète parfaitement ce garde-temps d\'exception, aussi précis qu\'élégant.', 'Montre automatique avec boîtier en acier et nacre, sertie de 8 diamants', 3950.00, NULL, 'purete-divine.jpg', NULL, 20, 5, 0.082, 5, 1, '2025-04-05 18:28:46', '2025-05-06 15:23:45', 1, 0, 1),
(203, 'Clarté Étoilée', 'clarte-etoilee', 'ELX-CL-F03', 'La Clarté Étoilée capture l\'essence du firmament avec son cadran en aventurine serti de 87 diamants taille brillant. Son boîtier en or blanc 18k (36mm) abrite un mouvement automatique avec indication des phases de lune d\'une précision astronomique. Un chef-d\'œuvre de joaillerie horlogère qui ne nécessitera qu\'un ajustement de la lune tous les 122 ans.', 'Montre en or blanc 18k avec cadran serti de diamants et phases de lune', 16400.00, NULL, 'clarte-etoilee.jpg', 'clarte-etoilee-detail1.jpg,clarte-etoilee-detail2.jpg', 5, 2, 0.118, 5, 1, '2025-04-05 18:28:46', '2025-05-06 15:23:45', 1, 0, 0),
(204, 'Élégance Saphir', 'elegance-saphir', 'ELX-CL-F04', 'L\'Élégance Saphir se distingue par sa lunette sertie de 36 saphirs bleus taillés à la main, encerclant un cadran bleu profond aux reflets soleil. Son boîtier en acier de haute qualité et son bracelet intégré offrent une ergonomie parfaite. Le mouvement automatique visible par le fond transparent révèle une mécanique aussi belle que précise.', 'Montre en acier avec lunette sertie de saphirs et cadran bleu profond', 5650.00, NULL, 'elegance-saphir.jpg', NULL, 15, 4, 0.105, 5, 1, '2025-04-05 18:28:46', '2025-05-06 15:23:45', 1, 0, 1),
(301, 'Chronos Édition Limitée', 'chronos-edition-limitee', 'ELX-LE-001', 'Le Chronos Édition Limitée représente l\'excellence horlogère dans sa forme la plus pure. Produit en seulement 100 exemplaires numérotés, ce garde-temps exceptionnel allie tradition et innovation avec un boîtier en platine 950 et un cadran en émail grand feu réalisé à la main. Son mouvement manufacture à remontage manuel offre une réserve de marche de 80 heures et présente des finitions exceptionnelles visibles à travers le fond saphir. Chaque pièce est signée et numérotée par notre maître horloger.', 'Montre en platine 950 avec cadran en émail grand feu et mouvement manufacture exclusif. Édition limitée à 100 exemplaires numérotés.', 8950.00, NULL, 'Chronos-edition-limited.png', 'chronos-edition-limitee-detail1.jpg,chronos-edition-limitee-detail2.jpg', 35, 5, 0.145, 1, 4, '2025-04-12 18:52:37', '2025-05-06 15:23:45', 1, 1, 1),
(302, 'Prestige Unique', 'prestige-unique', 'ELX-LE-002', 'Le Prestige Unique incarne l\'exclusivité avec son boîtier en or blanc 18k et son cadran en météorite de Muonionalusta, d\'origine extraterrestre. Chaque pièce présente des motifs cristallins uniques, rendant chaque montre véritablement unique. Limitée à 50 exemplaires, cette création exceptionnelle abrite notre calibre squelette à complication phase de lune astronomique. Le bracelet en alligator bleu nuit est cousu main par nos artisans maroquiniers.', 'Montre en or blanc 18k avec cadran en météorite et phase de lune astronomique. Édition limitée à 50 exemplaires.', 24500.00, NULL, 'prestige-unique.jpg', 'prestige-unique-detail1.jpg', 20, 3, 0.155, 1, 4, '2025-04-12 18:52:37', '2025-05-06 15:23:45', 1, 1, 1),
(303, 'Héritage Exclusif', 'heritage-exclusif', 'ELX-LE-003', 'L\'Héritage Exclusif rend hommage aux techniques horlogères ancestrales avec un mouvement tourbillon entièrement fabriqué et décoré à la main. Son boîtier en or rose 18k abrite un cadran en aventurine qui capture la magie d\'un ciel étoilé. Limitée à 25 exemplaires, chaque pièce nécessite plus de 500 heures de travail minutieux. Le tourbillon volant visible à 6 heures témoigne de la maîtrise technique incomparable de nos horlogers.', 'Montre tourbillon en or rose 18k avec cadran en aventurine. Édition limitée à 25 exemplaires fabriqués entièrement à la main.', 23500.00, NULL, 'heritage-exclusif.jpg', 'heritage-exclusif-detail1.jpg,heritage-exclusif-detail2.jpg', 15, 3, 0.160, 1, 4, '2025-04-12 18:52:37', '2025-05-06 15:23:45', 1, 1, 1),
(304, 'Souveraineté Singulière', 'souverainete-singuliere', 'ELX-LE-004', 'La Souveraineté Singulière représente l\'apogée de l\'art horloger contemporain avec son boîtier en tantale, métal rare et difficile à travailler. Édition strictement limitée à 15 exemplaires, cette montre à grande complication intègre un quantième perpétuel, une équation du temps et une indication de réserve de marche de 7 jours. Le cadran en nacre noire est décoré de motifs guillochés réalisés à la main par notre maître guillocheur.', 'Montre grande complication en tantale avec quantième perpétuel et équation du temps. Édition ultra-limitée à 15 exemplaires.', 21000.00, NULL, 'souverainete-singuliere.jpg', 'souverainete-singuliere-detail1.jpg', 8, 2, 0.170, 1, 4, '2025-04-12 18:52:37', '2025-05-06 15:23:45', 1, 1, 1),
(305, 'Épopée Rare', 'epopee-rare', 'ELX-LE-005', 'L\'Épopée Rare associe innovation et tradition avec son boîtier en or palladié et titane grade 5. Ce chronographe rattrapante monopoussoir est doté d\'un mécanisme révolutionnaire breveté par notre manufacture. Produite en série limitée de 20 exemplaires, chaque pièce est accompagnée d\'un certificat d\'authenticité signé et d\'un livre retraçant l\'histoire de sa conception. Le cadran en carbone forgé offre une légèreté et une résistance exceptionnelles.', 'Chronographe rattrapante monopoussoir en or palladié et titane avec mécanisme breveté. Série limitée à 20 exemplaires.', 25000.00, NULL, 'epopee-rare.jpg', 'epopee-rare-detail1.jpg,epopee-rare-detail2.jpg', 12, 3, 0.138, 1, 4, '2025-04-12 18:52:37', '2025-05-06 15:23:45', 1, 1, 1),
(306, 'Conquest Chrono', 'conquest-chrono', 'SP-306', 'Le Conquest Chrono est le chronographe de référence pour les passionnés de précision. Avec son mouvement mécanique à remontage automatique et son boîtier en acier inoxydable, cette montre incarne l\'alliance parfaite entre robustesse et élégance sportive. Son cadran noir avec compteurs contrastés et sa lunette tachymétrique permettent une lecture immédiate dans toutes les conditions.', 'Chronographe haute performance avec boîtier en acier inoxydable, étanche jusqu\'à 300 mètres. Précision exceptionnelle pour les amateurs de sports extrêmes.', 14200.00, NULL, 'conquest-chrono.jpg', NULL, 15, 5, 98.500, 2, 3, '2025-05-03 15:29:00', NULL, 1, 1, 0),
(307, 'Lady Diver', 'lady-diver', 'SP-307', 'La Lady Diver réinvente la montre de plongée au féminin. Son boîtier en acier de 38mm accueille un cadran bleu profond avec des index luminescents et une lunette tournante unidirectionnelle. Étanche jusqu\'à 300 mètres, elle combine fonctionnalité et élégance avec son bracelet en acier à maillons satinés et polis.', 'Montre de plongée élégante pour femme avec étanchéité jusqu\'à 300 mètres. Son design raffiné en fait un accessoire polyvalent aussi bien sous l\'eau qu\'en soirée.', 7900.00, NULL, 'lady-diver.jpg', NULL, 12, 4, 85.200, 1, 3, '2025-05-03 15:29:00', NULL, 1, 0, 1),
(308, 'Sport Élégance', 'sport-elegance', 'SP-308', 'La Sport Élégance offre une silhouette dynamique qui s\'adresse aux femmes actives. Son boîtier en acier de 36mm aux finitions alternées satinées et polies abrite un mouvement automatique de haute précision. Le cadran argenté avec détails roses et le bracelet intégré en font un modèle à la fois sportif et chic.', 'Alliance parfaite entre sport et sophistication, cette montre automatique accompagne les femmes actives dans leur quotidien avec style et fiabilité.', 8400.00, NULL, 'sport-elegance.jpg', NULL, 8, 5, 78.500, 1, 3, '2025-05-03 15:29:00', NULL, 1, 0, 0),
(309, 'Aquatic Rose', 'aquatic-rose', 'SP-309', 'L\'Aquatic Rose séduit par son boîtier en acier rehaussé d\'une lunette en céramique rose. Étanche jusqu\'à 200 mètres, son cadran nacré est orné d\'index diamantés et protégé par un verre saphir. Cette montre de sport féminine marie performance et esthétique avec son mouvement automatique suisse de haute précision.', 'Montre aquatique féminine avec touches de rose et détails délicats. Parfaite combinaison de performance technique et d\'élégance raffinée pour toutes les activités.', 9200.00, 8280.00, 'aquatic-rose.jpg', NULL, 6, 5, 82.300, 1, 3, '2025-05-03 15:29:00', NULL, 1, 1, 0),
(310, 'Diamond Sport', 'diamond-sport', 'SP-310', 'La Diamond Sport transcende les codes du sport luxe avec son cadran serti de 12 diamants véritables pour marquer les heures. Son boîtier en acier et or rose de 40mm abrite un mouvement chronographe automatique de haute précision. La lunette en céramique noire et le bracelet en caoutchouc texturé soulignent son caractère sportif malgré sa préciosité.', 'Chronographe de luxe combinant diamants et matériaux techniques pour les femmes qui ne font aucun compromis entre performance et élégance.', 16500.00, NULL, 'diamond-sport.jpg', NULL, 4, 3, 92.750, 1, 3, '2025-05-03 15:29:00', NULL, 1, 1, 0),
(311, 'Chrono Lady', 'chrono-lady', 'SP-311', 'Le Chrono Lady offre toutes les fonctionnalités d\'un chronographe sportif dans un format adapté aux poignets féminins. Boîtier en acier de 38mm avec finition PVD or rose, cadran blanc nacré avec compteurs à 3, 6 et 9 heures. Mouvement chronographe quartz de haute précision, étanche à 100 mètres avec bracelet en acier bicolore.', 'Chronographe féminin combinant performance et style. Fonctionnalités avancées et lisibilité parfaite dans un design élégamment sportif.', 9600.00, NULL, 'chrono-lady.jpg', NULL, 10, 4, 84.200, 1, 3, '2025-05-03 15:29:00', NULL, 1, 0, 0),
(312, 'Athletic Grace', 'athletic-grace', 'SP-312', 'L\'Athletic Grace redéfinit l\'esthétique des montres de sport pour femme avec son profil affiné et ses lignes épurées. Boîtier en céramique légère de 36mm résistant aux rayures, cadran bleu marine avec détails argentés. Mouvement automatique avec réserve de marche de 48 heures, étanche à 150 mètres et bracelet interchangeable en caoutchouc et cuir de veau.', 'Montre sport féminine alliant légèreté, durabilité et confort. Son design épuré et ses matériaux innovants en font l\'accessoire idéal pour un style actif.', 7900.00, NULL, 'athletic-grace.jpg', NULL, 9, 4, 68.500, 1, 3, '2025-05-03 15:29:00', NULL, 1, 0, 0),
(401, 'Grâce Limitée', 'grace-limitee', 'ELX-LE-F01', 'La Grâce Limitée incarne l\'élégance féminine dans sa forme la plus pure avec son boîtier en or blanc 18k entièrement pavé de diamants taille brillant (2.8 carats). Son cadran en nacre blanche est orné de 12 rubis en guise d\'index horaires. Limitée à 30 exemplaires, cette création d\'exception abrite un mouvement automatique ultra-plat de haute précision. Le bracelet en satin est complété par une boucle déployante sertie de diamants.', 'Montre en or blanc 18k entièrement pavée de diamants avec cadran en nacre et index en rubis. Édition limitée à 30 exemplaires.', 20500.00, NULL, 'grace-limitee.jpg', 'grace-limitee-detail1.jpg', 18, 4, 0.110, 5, 4, '2025-04-12 18:52:37', '2025-05-06 15:23:45', 1, 1, 1),
(402, 'Splendeur Exclusif', 'splendeur-exclusif', 'ELX-LE-F02', 'La Splendeur Exclusif allie horlogerie et joaillerie dans un chef-d\'œuvre technique et esthétique. Son boîtier en or rose 18k adopte une forme florale sertie de saphirs roses taillés sur mesure. Le cadran en opale australienne présente des teintes irisées uniques pour chaque pièce de cette série limitée à 25 exemplaires. Le mouvement à remontage manuel est visible par le fond saphir et présente des ponts gravés à la main.', 'Montre joaillière en or rose 18k sertie de saphirs roses avec cadran en opale australienne. Série limitée à 25 exemplaires.', 22000.00, NULL, 'splendeur-exclusif.jpg', 'splendeur-exclusif-detail1.jpg,splendeur-exclusif-detail2.jpg', 15, 3, 0.115, 5, 4, '2025-04-12 18:52:37', '2025-05-06 15:23:45', 1, 1, 1),
(403, 'Éclat Unique', 'eclat-unique', 'ELX-LE-F03', 'L\'Éclat Unique se distingue par son cadran en malachite véritable dont les veines naturelles créent des motifs uniques sur chaque exemplaire. Son boîtier ovale en or jaune 18k est délicatement serti de tsavorites formant un dégradé subtil. Cette pièce d\'exception, limitée à 20 exemplaires, présente une petite seconde excentrée à 7 heures et une indication de phases de lune poétique à 2 heures, dans une composition asymétrique captivante.', 'Montre en or jaune 18k avec cadran en malachite véritable et sertissage de tsavorites. Édition limitée à 20 exemplaires.', 23500.00, NULL, 'eclat-unique.jpg', 'eclat-unique-detail1.jpg', 12, 3, 0.120, 5, 4, '2025-04-12 18:52:37', '2025-05-06 15:23:45', 1, 1, 1),
(404, 'Noblesse Singulière', 'noblesse-singuliere', 'ELX-LE-F04', 'La Noblesse Singulière allie innovation et raffinement avec son boîtier en céramique blanche high-tech sublimé par des inserts en or rose 18k. Son cadran laqué blanc neige présente une indication rétrograde des jours et une grande date à 12 heures. Limitée à 40 exemplaires, cette création unique abrite un mouvement automatique exclusif développé par notre manufacture. Le bracelet intégré en céramique et or rose offre un confort incomparable.', 'Montre en céramique blanche et or rose 18k avec fonctions rétrogrades et grande date. Édition limitée à 40 exemplaires.', 21000.00, NULL, 'noblesse-singuliere.jpg', 'noblesse-singuliere-detail1.jpg,noblesse-singuliere-detail2.jpg', 22, 4, 0.125, 5, 4, '2025-04-12 18:52:37', '2025-05-06 15:23:45', 1, 1, 1),
(405, 'Élégance Rare', 'elegance-rare', 'ELX-LE-F05', 'L\'Élégance Rare redéfinit les codes du luxe horloger féminin avec son boîtier tonneau en titane et sa lunette sertie de diamants baguette (1.8 carats). Son cadran en lapis-lazuli véritable est parsemé de particules de pyrite créant un effet ciel étoilé saisissant. Cette édition limitée à 15 exemplaires intègre un mouvement squelette exclusif visible à travers le cadran ajouré, alliant prouesse technique et beauté hypnotique.', 'Montre en titane et diamants avec cadran en lapis-lazuli et mouvement squelette visible. Série ultra-limitée à 15 exemplaires.', 25000.00, NULL, 'elegance-rare.jpg', 'elegance-rare-detail1.jpg', 8, 2, 0.105, 5, 4, '2025-04-12 18:52:37', '2025-05-06 15:23:45', 1, 1, 1),
(501, 'Excellence Royale', 'excellence-royale', 'ELX-PR-001', 'L\'Excellence Royale incarne le summum du raffinement horloger avec son boîtier en or blanc 18k finement ciselé à la main. Son cadran bleu profond aux reflets soleil est réalisé selon une technique ancestrale et nécessite plus de 20 étapes de fabrication. Le mouvement manufacture à remontage automatique offre une réserve de marche exceptionnelle de 72 heures avec un tourbillon volant visible à travers le cadran. Le bracelet en alligator bleu roi est cousu main par nos artisans maroquiniers et complété par une boucle déployante en or blanc.', 'Montre en or blanc 18k avec tourbillon volant et cadran bleu soleil réalisé selon une technique ancestrale. Chef-d\'œuvre de la haute horlogerie.', 32500.00, NULL, 'excellence-royale.jpg', 'excellence-royale-detail1.jpg,excellence-royale-detail2.jpg', 12, 3, 0.165, 4, 3, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(502, 'Majesté Éternelle', 'majeste-eternelle', 'ELX-PR-002', 'La Majesté Éternelle est une démonstration parfaite de la maîtrise horlogère contemporaine, associant tradition et innovation. Son boîtier en platine 950 entièrement poli à la main abrite un mouvement manufacture avec remontage manuel et quantième perpétuel. Cette complication prestigieuse indique le jour, la date, le mois et les années bissextiles, ne nécessitant aucun ajustement jusqu\'en 2100. Le cadran en émail grand feu noir est réalisé par notre maître émailleur, avec des index en appliques d\'or blanc. Le fond transparent révèle un mouvement finement décoré avec des ponts anglés et polis, ainsi que des vis bleues et des rubis flamboyants.', 'Montre en platine 950 avec quantième perpétuel et cadran en émail grand feu noir. Une référence en matière de haute horlogerie technique.', 45000.00, NULL, 'majeste-eternelle.jpg', 'majeste-eternelle-detail1.jpg', 8, 2, 0.175, 4, 3, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(503, 'Grandeur Suprême', 'grandeur-supreme', 'ELX-PR-003', 'La Grandeur Suprême est l\'expression ultime de notre savoir-faire horloger, combinant technicité et élégance. Son boîtier en or rose 18k de 42mm renferme un calibre manufacture chronographe à rattrapante avec roue à colonnes, l\'une des complications les plus nobles et complexes de l\'horlogerie. Le cadran est composé de jade impérial, pierre rare d\'un vert profond uniforme, qui est travaillée avec une extrême délicatesse et finesse. Les sous-cadrans contrastants et l\'échelle tachymétrique gravée sur la lunette confèrent à cette pièce d\'exception un caractère sportif et élégant à la fois. Le fond transparent permet d\'admirer le mouvement avec ses finitions côtes de Genève, anglage à la main et perlage.', 'Chronographe rattrapante en or rose 18k avec cadran en jade impérial. Alliance parfaite entre performance technique et rareté des matériaux.', 38500.00, NULL, 'grandeur-supreme.jpg', 'grandeur-supreme-detail1.jpg,grandeur-supreme-detail2.jpg', 10, 3, 0.158, 1, 3, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(504, 'Accomplissement Royal', 'accomplissement-royal', 'ELX-PR-004', 'L\'Accomplissement Royal est une œuvre horlogère qui défie les conventions avec son boîtier en tantalum, métal rare aux reflets bleu-gris et d\'une résistance exceptionnelle. À l\'intérieur, notre calibre manufacture à remontage manuel intègre une répétition minutes, complication rarissime qui sonne les heures, quarts d\'heures et minutes sur demande grâce à deux timbres cathédrale. Le cadran en onyx noir profond est incrusté d\'indices en diamants baguette totalisant 1.2 carats. Au verso, le fond saphir dévoile le mécanisme complexe de sonnerie avec ses marteaux et timbres, ainsi que les finitions exceptionnelles réalisées entièrement à la main.', 'Montre à répétition minutes en tantalum avec cadran en onyx et indices en diamants. Une merveille technique et acoustique.', 52000.00, NULL, 'accomplissement-royal.jpg', 'accomplissement-royal-detail1.jpg', 5, 2, 0.162, 4, 3, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(505, 'Perfection Ultime', 'perfection-ultime', 'ELX-PR-005', 'La Perfection Ultime représente l\'apogée de notre quête d\'excellence avec son boîtier en or gris 18k aux finitions alternées polies et brossées. Le cadran en météorite de Gibeon, vieille de plus de 4 milliards d\'années, présente des motifs cristallins naturels qui rendent chaque pièce véritablement unique. Cette météorite est soigneusement découpée puis polie pour révéler ses figures de Widmanstätten caractéristiques. Notre mouvement manufacture à double barillet offre une réserve de marche exceptionnelle de 8 jours, avec indicateur de réserve de marche intégré au cadran. Les index appliqués en or gris et les aiguilles dauphine polies à la main complètent cette création d\'exception.', 'Montre en or gris 18k avec cadran en météorite de Gibeon et réserve de marche de 8 jours. Un témoignage cosmique au poignet.', 41500.00, NULL, 'perfection-ultime.jpg', 'perfection-ultime-detail1.jpg,perfection-ultime-detail2.jpg', 7, 2, 0.155, 1, 3, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(601, 'Splendeur Céleste', 'splendeur-celeste', 'ELX-PR-F01', 'La Splendeur Céleste capture l\'essence même de la voûte étoilée dans une création horlogère d\'exception. Son boîtier en or blanc 18k de 36mm est entièrement serti de diamants taille brillant sur la lunette et les cornes (2.3 carats au total). Le cadran en aventurine véritable offre un spectacle fascinant, semblable à un ciel nocturne constellé d\'étoiles scintillantes. Les 12 index sont des saphirs bleus taillés spécialement pour cette pièce, tandis que les aiguilles en or blanc sont finement ajourées. Le mouvement automatique ultra-plat intègre une complication phase de lune astronomique, visible à travers une ouverture à 6 heures, où le disque de lune est réalisé en nacre blanche sur fond d\'aventurine.', 'Montre en or blanc 18k sertie de diamants avec cadran en aventurine et phase de lune astronomique. Un véritable ciel étoilé au poignet.', 36500.00, NULL, 'splendeur-celeste.jpg', 'splendeur-celeste-detail1.jpg,splendeur-celeste-detail2.jpg', 8, 2, 0.120, 5, 3, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(602, 'Délicatesse Majestueuse', 'delicatesse-majestueuse', 'ELX-PR-F02', 'La Délicatesse Majestueuse redéfinit l\'élégance féminine avec son boîtier tonneau en or rose 18k, dont la forme ergonomique épouse parfaitement le poignet. Le cadran en opale noble australienne présente des jeux de couleurs irisées uniques, oscillant entre bleus, verts et violets selon l\'angle de vue. Chaque opale est sélectionnée pour ses qualités exceptionnelles et polie avec un soin extrême par nos lapidaires. Le mouvement manufacture à remontage manuel est visible à travers le fond saphir, révélant des ponts en forme de fleurs délicatement gravés et incrustés de diamants. Le bracelet en cuir d\'alligator rose poudré est fabriqué sur mesure et assemblé à la main.', 'Montre tonneau en or rose 18k avec cadran en opale noble australienne aux reflets multicolores. Une création joaillière d\'exception.', 34000.00, NULL, 'delicatesse-majestueuse.jpg', 'delicatesse-majestueuse-detail1.jpg', 6, 2, 0.115, 5, 3, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(603, 'Élégance Souveraine', 'elegance-souveraine', 'ELX-PR-F03', 'L\'Élégance Souveraine incarne la quintessence du luxe discret avec son boîtier ovale en titane grade 5 et sa lunette sertie d\'une double rangée de diamants taille brillant (1.85 carats). Le cadran en nacre blanche est travaillé selon la technique du guillochage à la main, créant un motif ondulé d\'une finesse extrême. Les chiffres romains sont peints à la main avec une encre composite mêlant or 24k et résine spéciale, procurant une texture en relief unique. Le mouvement automatique ultra-plat, visible par le fond transparent, est décoré de Côtes de Genève et de ponts anglés à la main. Le bracelet intégré en titane et or rose 18k offre une flexibilité et un confort exceptionnels.', 'Montre ovale en titane et diamants avec cadran en nacre guillochée à la main et bracelet intégré. Alliance parfaite de légèreté et d\'élégance.', 31500.00, NULL, 'elegance-souveraine.jpg', 'elegance-souveraine-detail1.jpg,elegance-souveraine-detail2.jpg', 10, 3, 0.095, 5, 3, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(604, 'Éclat Impérial', 'eclat-imperial', 'ELX-PR-F04', 'L\'Éclat Impérial est un chef-d\'œuvre de joaillerie horlogère qui marie l\'art de la haute horlogerie et de la haute joaillerie. Son boîtier en or blanc 18k adopte une forme inspirée des arts déco et est entièrement pavé de diamants taille brillant et baguette (3.4 carats). Le cadran en onyx noir profond est orné d\'un motif floral serti de rubis, saphirs et émeraudes formant un dégradé de couleurs. Le mouvement manufacture à remontage manuel est squeletté et ajouré à la main, permettant d\'admirer ses rouages à travers le cadran partiellement transparent. Le bracelet en satin noir est complété par une boucle déployante en or blanc également sertie de diamants.', 'Montre joaillière en or blanc 18k entièrement pavée de diamants avec motif floral en pierres précieuses sur cadran en onyx. Une pièce d\'art au poignet.', 45000.00, NULL, 'eclat-imperial.jpg', 'eclat-imperial-detail1.jpg', 4, 1, 0.125, 5, 3, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(605, 'Grâce Absolue', 'grace-absolue', 'ELX-PR-F05', 'La Grâce Absolue est l\'incarnation de la délicatesse et de la précision technique dans une montre féminine. Son boîtier rectangulaire aux angles arrondis en or jaune 18k est inspiré des années 1920 et abrite notre calibre miniature le plus fin (seulement 2.1mm d\'épaisseur). Le cadran en laque urushi traditionnelle japonaise représente des fleurs de cerisier sur fond noir profond, réalisé par un maître laqueur selon une technique millénaire nécessitant plus de 40 couches de laque. Chaque fleur est rehaussée de poudre d\'or et de fragments de nacre selon la technique du maki-e. Les aiguilles dauphine en or jaune complètent harmonieusement ce garde-temps d\'exception.', 'Montre rectangulaire Art Déco en or jaune 18k avec cadran en laque urushi représentant des fleurs de cerisier. Un hommage à l\'artisanat traditionnel japonais.', 29800.00, NULL, 'grace-absolue.jpg', 'grace-absolue-detail1.jpg,grace-absolue-detail2.jpg', 7, 2, 0.085, 5, 3, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(701, 'Force Titan', 'force-titan', 'ELX-SP-001', 'La Force Titan incarne la robustesse et la fiabilité à l\'état pur. Son boîtier en titane grade 5 traité DLC noir de 45mm offre une résistance exceptionnelle aux chocs et aux rayures, tout en restant étonnamment léger au poignet. Le cadran structuré noir mat avec motif guilloché concentrique présente des index et aiguilles surdimensionnés revêtus de Super-LumiNova® pour une parfaite lisibilité dans toutes les conditions. Notre calibre automatique sport ELX-89 protégé par un système anti-magnétique offre une précision chronométrique même dans les environnements les plus extrêmes. Étanche jusqu\'à 300 mètres, cette montre intègre une valve à hélium automatique pour les plongées professionnelles. Le bracelet en caoutchouc technique intègre une extension rapide pour un ajustement parfait sur combinaison de plongée.', 'Montre de plongée professionnelle en titane grade 5 avec traitement DLC noir. Étanche à 300 mètres avec protection anti-magnétique et système de valve à hélium automatique.', 12500.00, NULL, 'force-titan.jpg', 'force-titan-detail1.jpg,force-titan-detail2.jpg', 15, 3, 0.120, 1, 2, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(702, 'Dynamique Élite', 'dynamique-elite', 'ELX-SP-002', 'La Dynamique Élite est l\'alliance parfaite entre performance technique et design contemporain. Son boîtier en céramique high-tech de 44mm offre une résistance aux rayures incomparable et une légèreté idéale pour les activités sportives intenses. Le cadran texturé bleu profond avec dégradé subtil est protégé par un verre saphir bombé traité antireflet sur les deux faces. Notre chronographe flyback manufacture permet des mesures successives instantanées grâce à une simple pression. L\'échelle tachymétrique sur la lunette permet de calculer vitesses moyennes et distances. Le fond transparent révèle notre calibre ELX-725 et sa masse oscillante en or rose 18k gravée. Le bracelet en caoutchouc intègre des inserts en titane pour une durabilité exceptionnelle tout en offrant un confort optimal.', 'Chronographe flyback en céramique high-tech avec échelle tachymétrique. Alliance de légèreté et résistance aux rayures avec mouvement manufacture sophistiqué visible par le fond transparent.', 14000.00, NULL, 'dynamique-elite.jpg', 'dynamique-elite-detail1.jpg,dynamique-elite-detail2.jpg', 18, 4, 0.115, 1, 2, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(703, 'Vitesse Suprême', 'vitesse-supreme', 'ELX-SP-003', 'La Vitesse Suprême est née de notre passion pour les sports mécaniques et s\'inspire directement du monde des compétitions automobiles. Son boîtier en titane et carbone forgé de 43mm présente un design aérodynamique avec poussoirs ergonomiques inspirés des pistons de moteur. Le cadran squelette multicouche révèle partiellement le mouvement chronographe et intègre des compteurs directement inspirés des tableaux de bord de voitures de course. La lunette en céramique comporte une échelle tachymétrique pour mesurer les vitesses jusqu\'à 400 km/h. Notre calibre chronographe automatique haute fréquence bat à 36,000 alternances par heure, permettant des mesures au 1/10e de seconde d\'une précision absolue. Le bracelet en cuir de veau perforé avec coutures contrastantes évoque les gants de pilote de course.', 'Chronographe haute fréquence en titane et carbone forgé inspiré des sports mécaniques. Mesure du temps au 1/10e de seconde avec design évoquant les voitures de course.', 13000.00, NULL, 'vitesse-supreme.jpg', 'vitesse-supreme-detail1.jpg', 12, 3, 0.118, 1, 2, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(704, 'Puissance Infinie', 'puissance-infinie', 'ELX-SP-004', 'La Puissance Infinie repousse les limites de l\'innovation horlogère sportive avec son boîtier en composite de carbone et titane de 45mm. Sa structure interne en nid d\'abeille lui confère une résistance exceptionnelle pour un poids plume de seulement 72 grammes. Le cadran openworked à géométrie complexe permet d\'admirer notre calibre manufacture ELX-P120 à remontage automatique doté d\'une réserve de marche de 120 heures - une prouesse technique pour une montre sportive. L\'indicateur de réserve de marche à 9 heures est complété par une petite seconde à 6 heures et une date à guichet à 3 heures. La couronne vissée à double joint garantit une étanchéité à 200 mètres. Le système breveté d\'absorption des chocs protège le mouvement dans les conditions les plus extrêmes. Le bracelet intégré en fibre technique et caoutchouc naturel offre flexibilité et durabilité.', 'Montre ultralégère en composite carbone-titane avec structure en nid d\'abeille et réserve de marche de 120 heures. Un concentré de technologie offrant résistance exceptionnelle et légèreté extrême.', 15500.00, NULL, 'puissance-infinie.jpg', 'puissance-infinie-detail1.jpg,puissance-infinie-detail2.jpg', 10, 2, 0.072, 1, 2, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(705, 'Sprint Ultime', 'sprint-ultime', 'ELX-SP-005', 'Le Sprint Ultime représente une fusion parfaite entre innovation technologique et design inspiré de l\'athlétisme de haut niveau. Son boîtier en alliage d\'aluminium aéronautique de 43mm traité par anodisation offre une palette de couleurs vives et une légèreté exceptionnelle. Le cadran multicouche avec compteur 60 minutes surdimensionné à 6 heures est optimisé pour les entraînements fractionnés. Notre calibre chronographe à rattrapante permet des chronométrages intermédiaires précis, idéal pour les coachs sportifs et les athlètes. Le système exclusif de bracelet interchangeable permet de passer d\'un bracelet technique à un bracelet en cuir en quelques secondes sans outil. La glace saphir avec traitement antireflet 7 couches garantit une lisibilité parfaite même en plein soleil.', 'Chronographe rattrapante en aluminium anodisé conçu pour les athlètes et entraîneurs. Système exclusif de bracelet interchangeable et cadran optimisé pour l\'entraînement fractionné.', 14800.00, NULL, 'sprint-ultime.jpg', 'sprint-ultime-detail1.jpg,sprint-ultime-detail2.jpg', 14, 3, 0.085, 1, 2, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(706, 'Rivalité Acérée', 'rivalite-aceree', 'ELX-SP-006', 'La Rivalité Acérée incarne l\'esprit de compétition dans sa forme la plus pure. Son boîtier en titane grade 5 et céramique noire de 46mm arbore une architecture complexe inspirée des moteurs de Formule 1. Le cadran multicouche comporte une échelle tachymétrique, un totalisateur 12 heures et un indicateur de réserve de marche de 65 heures. Notre calibre chronographe automatique intégré ELX-500 à roue à colonnes et embrayage vertical garantit une précision absolue et un fonctionnement parfait des poussoirs. La lunette bidirectionnelle en céramique permet le calcul de temps intermédiaires. Le fond saphir révèle la masse oscillante en forme de volant de course finement ajourée et le pont de chronographe en forme d\'aileron. Le bracelet en caoutchouc texturé évoque les pneus de course avec son motif directionnel.', 'Chronographe de compétition en titane et céramique avec calibre intégré à roue à colonnes. Design inspiré des Formule 1 avec finitions évoquant la mécanique de haute performance.', 16200.00, NULL, 'rivalite-aceree.jpg', 'rivalite-aceree-detail1.jpg,rivalite-aceree-detail2.jpg', 9, 2, 0.130, 1, 2, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(751, 'Élan Glamour', 'elan-glamour', 'ELX-SP-F01', 'L\'Élan Glamour redéfinit le concept de montre sport féminine avec son boîtier en céramique blanche high-tech de 38mm, alliant légèreté et résistance aux rayures. La lunette sertie de 60 diamants taille brillant (0.75 carat) encadre un cadran en nacre blanche aux reflets iridescents avec motif ondulé évoquant les vagues. Les index appliqués en or rose 18k complètent harmonieusement les aiguilles squelettées luminescentes. Notre calibre automatique ELX-222 visible par le fond saphir offre une réserve de marche de 48 heures. Étanche à 100 mètres, cette montre allie performance sportive et élégance raffinée. Le bracelet intégré en céramique blanche comporte un système de micro-ajustement pour un confort optimal en toutes circonstances.', 'Montre sport-chic en céramique blanche sertie de diamants avec cadran en nacre. Alliance d\'élégance féminine et de technologie sportive, étanche à 100 mètres.', 11500.00, NULL, 'elan-glamour.jpg', 'elan-glamour-detail1.jpg,elan-glamour-detail2.jpg', 12, 3, 0.085, 5, 2, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(752, 'Énergie Radieuse', 'energie-radieuse', 'ELX-SP-F02', 'L\'Énergie Radieuse capture l\'essence du dynamisme féminin dans une création à la fois sportive et raffinée. Son boîtier en titane grade 5 de 36mm traité par PVD or rose crée un contraste saisissant avec le cadran en aventurine bleue mouchetée d\'éclats métalliques, évoquant un ciel étoilé. Ce garde-temps intègre notre chronographe monopoussoir exclusif, permettant le contrôle des fonctions start/stop/reset par une seule couronne, sublimant ainsi la pureté des lignes. Étanche à 50 mètres, cette montre associe fonctionnalité sportive et élégance sophistiquée. Le bracelet en caoutchouc technique bleu nuit est orné de surpiqûres or rose et doté d\'un système de changement rapide pour s\'adapter à toutes les occasions.', 'Chronographe monopoussoir en titane or rose avec cadran en aventurine bleue. Design épuré alliant élégance et fonctionnalité pour une femme active et raffinée.', 12800.00, NULL, 'energie-radieuse.jpg', 'energie-radieuse-detail1.jpg', 15, 3, 0.078, 5, 2, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(753, 'Vibrance Pure', 'vibrance-pure', 'ELX-SP-F03', 'La Vibrance Pure incarne la fusion parfaite entre haute performance sportive et esthétique contemporaine. Son boîtier en composite de fibre de carbone de 37mm présente un motif marbré unique sur chaque pièce. Le cadran en saphir fumé laisse entrevoir le mouvement squelette manufacture, créant un effet de profondeur saisissant. Notre calibre automatique ELX-VS2 visible des deux côtés combine légèreté et robustesse grâce à ses ponts en titane anodisé bleu. La lunette en céramique blanche graduée sur 60 minutes permet le chronométrage d\'activités sportives. Étanche à 100 mètres, cette montre résiste aux conditions les plus exigeantes tout en conservant une allure sophistiquée. Le bracelet interchangeable en caoutchouc blanc texturé est complété par une boucle déployante en titane.', 'Montre squelette en fibre de carbone avec cadran en saphir fumé et mouvement visible. Une pièce technique ultramoderne combinant légèreté, transparence et résistance sportive.', 13000.00, NULL, 'vibrance-pure.jpg', 'vibrance-pure-detail1.jpg,vibrance-pure-detail2.jpg', 10, 2, 0.065, 5, 2, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(754, 'Aura Athlétique', 'aura-athletique', 'ELX-SP-F04', 'L\'Aura Athlétique est conçue pour la femme d\'action qui ne sacrifie pas l\'élégance à la performance. Son boîtier tonneau en titane et céramique noire de 35mm offre une robustesse exceptionnelle dans un format parfaitement adapté au poignet féminin. Le cadran guilloché soleil rouge grenat s\'anime de reflets changeants selon l\'incidence de la lumière, tandis que les index diamantés apportent une touche de brillance subtile. Notre calibre chronographe flyback automatique ultraplat permet de chronométrer des événements successifs avec une seule pression. Le système breveté de correction rapide de la date et du fuseau horaire facilite les voyages internationaux. Étanche à 200 mètres, cette montre accompagne sa propriétaire dans toutes ses aventures. Le bracelet intégré en titane et céramique noire est ajustable au demi-maillon pour un confort optimal.', 'Chronographe flyback en titane et céramique avec cadran guilloché grenat et index diamantés. Une montre sport-chic pour femmes voyageuses combinant technique horlogère et design audacieux.', 14500.00, NULL, 'aura-athletique.jpg', 'aura-athletique-detail1.jpg', 8, 2, 0.095, 5, 2, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1),
(755, 'Allure Détente', 'allure-detente', 'ELX-SP-F05', 'L\'Allure Détente réinvente le concept de la montre de sport féminine avec son boîtier en aluminium aéronautique anodisé turquoise de 39mm. Ultraléger (seulement 55 grammes avec le bracelet), ce garde-temps se fait oublier au poignet tout en affirmant un style audacieux. Le cadran en nacre noire avec motif vagues en trois dimensions capture et réfléchit la lumière de façon spectaculaire. Notre calibre automatique avec indicateur jour/nuit à 6 heures est visible par le fond transparent. L\'étanchéité à 100 mètres permet de pratiquer la natation ou le snorkeling en toute sérénité. Les index et aiguilles luminescents garantissent une parfaite lisibilité dans toutes les conditions. Le bracelet en caoutchouc technique turquoise avec système de fixation rapide permet de changer facilement de style.', 'Montre sport-loisir ultraléger en aluminium turquoise avec cadran en nacre noire texturée. Design contemporain et audacieux pour une femme active appréciant confort et style distinctif.', 13200.00, NULL, 'allure-detente.jpg', 'allure-detente-detail1.jpg,allure-detente-detail2.jpg', 14, 3, 0.055, 5, 2, '2025-04-19 11:11:50', '2025-05-06 15:23:45', 1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `produit_attributs`
--

CREATE TABLE `produit_attributs` (
  `produit_id` int(11) NOT NULL,
  `attribut_id` int(11) NOT NULL,
  `valeur` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produit_pages`
--

CREATE TABLE `produit_pages` (
  `id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `page_code` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produit_pages`
--

INSERT INTO `produit_pages` (`id`, `produit_id`, `page_code`) VALUES
(41, 101, 'accueil'),
(116, 101, 'catalogue'),
(1, 101, 'collection_classic'),
(170, 101, 'montres_homme'),
(107, 102, 'catalogue'),
(2, 102, 'collection_classic'),
(171, 102, 'montres_homme'),
(117, 103, 'catalogue'),
(3, 103, 'collection_classic'),
(108, 104, 'catalogue'),
(4, 104, 'collection_classic'),
(172, 104, 'montres_homme'),
(99, 201, 'accueil'),
(118, 201, 'catalogue'),
(5, 201, 'collection_classic'),
(201, 201, 'montres_femme'),
(109, 202, 'catalogue'),
(6, 202, 'collection_classic'),
(202, 202, 'montres_femme'),
(232, 202, 'nouveautes'),
(110, 203, 'catalogue'),
(7, 203, 'collection_classic'),
(203, 203, 'montres_femme'),
(111, 204, 'catalogue'),
(8, 204, 'collection_classic'),
(204, 204, 'montres_femme'),
(233, 204, 'nouveautes'),
(42, 301, 'accueil'),
(119, 301, 'catalogue'),
(31, 301, 'collection_limited'),
(173, 301, 'montres_homme'),
(235, 301, 'nouveautes'),
(120, 302, 'catalogue'),
(32, 302, 'collection_limited'),
(174, 302, 'montres_homme'),
(236, 302, 'nouveautes'),
(121, 303, 'catalogue'),
(33, 303, 'collection_limited'),
(175, 303, 'montres_homme'),
(237, 303, 'nouveautes'),
(122, 304, 'catalogue'),
(34, 304, 'collection_limited'),
(176, 304, 'montres_homme'),
(238, 304, 'nouveautes'),
(123, 305, 'catalogue'),
(35, 305, 'collection_limited'),
(177, 305, 'montres_homme'),
(239, 305, 'nouveautes'),
(124, 306, 'catalogue'),
(70, 306, 'collection_prestige'),
(112, 307, 'catalogue'),
(71, 307, 'collection_prestige'),
(178, 307, 'montres_homme'),
(234, 307, 'nouveautes'),
(113, 308, 'catalogue'),
(72, 308, 'collection_prestige'),
(179, 308, 'montres_homme'),
(101, 309, 'accueil'),
(125, 309, 'catalogue'),
(73, 309, 'collection_prestige'),
(180, 309, 'montres_homme'),
(126, 310, 'catalogue'),
(74, 310, 'collection_prestige'),
(181, 310, 'montres_homme'),
(114, 311, 'catalogue'),
(75, 311, 'collection_prestige'),
(182, 311, 'montres_homme'),
(115, 312, 'catalogue'),
(76, 312, 'collection_prestige'),
(183, 312, 'montres_homme'),
(46, 401, 'accueil'),
(127, 401, 'catalogue'),
(36, 401, 'collection_limited'),
(205, 401, 'montres_femme'),
(240, 401, 'nouveautes'),
(128, 402, 'catalogue'),
(37, 402, 'collection_limited'),
(206, 402, 'montres_femme'),
(241, 402, 'nouveautes'),
(129, 403, 'catalogue'),
(38, 403, 'collection_limited'),
(207, 403, 'montres_femme'),
(242, 403, 'nouveautes'),
(130, 404, 'catalogue'),
(39, 404, 'collection_limited'),
(208, 404, 'montres_femme'),
(243, 404, 'nouveautes'),
(131, 405, 'catalogue'),
(40, 405, 'collection_limited'),
(209, 405, 'montres_femme'),
(244, 405, 'nouveautes'),
(43, 501, 'accueil'),
(132, 501, 'catalogue'),
(9, 501, 'collection_classic'),
(21, 501, 'collection_prestige'),
(245, 501, 'nouveautes'),
(133, 502, 'catalogue'),
(22, 502, 'collection_prestige'),
(246, 502, 'nouveautes'),
(134, 503, 'catalogue'),
(23, 503, 'collection_prestige'),
(184, 503, 'montres_homme'),
(247, 503, 'nouveautes'),
(135, 504, 'catalogue'),
(24, 504, 'collection_prestige'),
(248, 504, 'nouveautes'),
(136, 505, 'catalogue'),
(25, 505, 'collection_prestige'),
(185, 505, 'montres_homme'),
(249, 505, 'nouveautes'),
(44, 601, 'accueil'),
(137, 601, 'catalogue'),
(26, 601, 'collection_prestige'),
(210, 601, 'montres_femme'),
(250, 601, 'nouveautes'),
(138, 602, 'catalogue'),
(27, 602, 'collection_prestige'),
(211, 602, 'montres_femme'),
(251, 602, 'nouveautes'),
(139, 603, 'catalogue'),
(28, 603, 'collection_prestige'),
(212, 603, 'montres_femme'),
(252, 603, 'nouveautes'),
(140, 604, 'catalogue'),
(29, 604, 'collection_prestige'),
(213, 604, 'montres_femme'),
(253, 604, 'nouveautes'),
(141, 605, 'catalogue'),
(30, 605, 'collection_prestige'),
(214, 605, 'montres_femme'),
(254, 605, 'nouveautes'),
(98, 701, 'accueil'),
(142, 701, 'catalogue'),
(10, 701, 'collection_sport'),
(186, 701, 'montres_homme'),
(255, 701, 'nouveautes'),
(143, 702, 'catalogue'),
(11, 702, 'collection_sport'),
(187, 702, 'montres_homme'),
(256, 702, 'nouveautes'),
(144, 703, 'catalogue'),
(12, 703, 'collection_sport'),
(188, 703, 'montres_homme'),
(257, 703, 'nouveautes'),
(45, 704, 'accueil'),
(145, 704, 'catalogue'),
(13, 704, 'collection_sport'),
(189, 704, 'montres_homme'),
(258, 704, 'nouveautes'),
(146, 705, 'catalogue'),
(14, 705, 'collection_sport'),
(190, 705, 'montres_homme'),
(259, 705, 'nouveautes'),
(147, 706, 'catalogue'),
(15, 706, 'collection_sport'),
(191, 706, 'montres_homme'),
(260, 706, 'nouveautes'),
(100, 751, 'accueil'),
(148, 751, 'catalogue'),
(16, 751, 'collection_sport'),
(215, 751, 'montres_femme'),
(261, 751, 'nouveautes'),
(149, 752, 'catalogue'),
(17, 752, 'collection_sport'),
(216, 752, 'montres_femme'),
(262, 752, 'nouveautes'),
(150, 753, 'catalogue'),
(18, 753, 'collection_sport'),
(217, 753, 'montres_femme'),
(263, 753, 'nouveautes'),
(151, 754, 'catalogue'),
(19, 754, 'collection_sport'),
(218, 754, 'montres_femme'),
(264, 754, 'nouveautes'),
(152, 755, 'catalogue'),
(20, 755, 'collection_sport'),
(219, 755, 'montres_femme'),
(265, 755, 'nouveautes');

-- --------------------------------------------------------

--
-- Structure de la table `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `min_purchase` decimal(10,2) DEFAULT 0.00,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `products` varchar(255) DEFAULT NULL,
  `collections` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `promotion_produits`
--

CREATE TABLE `promotion_produits` (
  `promotion_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `date_ajout` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `level` enum('debug','info','notice','warning','error','critical','alert','emergency') NOT NULL DEFAULT 'info',
  `user_id` int(11) DEFAULT NULL,
  `user_type` enum('admin','customer','system','guest') NOT NULL DEFAULT 'system',
  `category` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` varchar(50) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `before_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`before_state`)),
  `after_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`after_state`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `http_method` varchar(10) DEFAULT NULL,
  `request_url` varchar(255) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `execution_time` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `system_logs`
--

INSERT INTO `system_logs` (`id`, `level`, `user_id`, `user_type`, `category`, `action`, `entity_type`, `entity_id`, `details`, `before_state`, `after_state`, `ip_address`, `user_agent`, `created_at`, `context`, `http_method`, `request_url`, `session_id`, `execution_time`) VALUES
(1, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 08:48:35', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.0118899),
(2, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:03:03', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000782013),
(3, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:03:05', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000345945),
(4, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:03:05', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000324965),
(5, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:07:46', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000298977),
(6, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '1', 'Accès à l\'édition de l\'utilisateur #1', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:11:55', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000216007),
(7, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:12:05', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000484943),
(8, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:16:20', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000366926),
(9, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:16:21', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000254154),
(10, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:16:21', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000338078),
(11, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:16:55', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000324011),
(12, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:19:01', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000342131),
(13, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:19:33', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000187159),
(14, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:19:34', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000576973),
(15, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:19:34', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000430107),
(16, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:19:34', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000452042),
(17, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:19:34', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000273943),
(18, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:19:35', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000341177),
(19, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:19:35', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000883102),
(20, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:19:52', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000436068),
(21, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:19:52', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000268936),
(22, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:19:53', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00030303),
(23, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:21:55', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000332117),
(24, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:21:55', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000478983),
(25, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:22:01', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000336885),
(26, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:26:18', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000310898),
(27, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:27:32', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000272036),
(28, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:27:57', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000294209),
(29, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:29:22', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000334978),
(30, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:34:52', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000394106),
(31, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:34:53', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000296831),
(32, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:34:53', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000265837),
(33, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:34:53', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000182867),
(34, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:34:54', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000238895),
(35, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:37:46', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000359058),
(36, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:37:47', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000461817),
(37, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:37:47', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000429153),
(38, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:37:47', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000469923),
(39, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:37:47', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000378847),
(40, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:43', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000328064),
(41, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:43', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000283957),
(42, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:44', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.0003438),
(43, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:44', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000508785),
(44, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:44', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000931978),
(45, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:45', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000339985),
(46, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:45', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000344992),
(47, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:45', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000492096),
(48, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:45', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000291824),
(49, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:45', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000252962),
(50, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:46', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000467062),
(51, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:46', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000328064),
(52, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:46', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000545979),
(53, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:46', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00031805),
(54, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:47', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000330925),
(55, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:47', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00028491),
(56, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:47', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000513077),
(57, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:47', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000468016),
(58, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:47', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000730038),
(59, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:42:48', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000303984),
(60, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:15', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000614882),
(61, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:16', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000995159),
(62, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:16', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000350952),
(63, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:16', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000632048),
(64, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:16', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00036788),
(65, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:17', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000607014),
(66, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:17', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000226021),
(67, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:17', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000417948),
(68, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:17', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00037694),
(69, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:18', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000453949),
(70, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:18', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000252008),
(71, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:18', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000747919),
(72, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:18', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00022912),
(73, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:18', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000611067),
(74, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:19', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00027895),
(75, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:19', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000602007),
(76, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:19', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000202894),
(77, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:19', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000515938),
(78, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:19', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00229692),
(79, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:20', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000421047),
(80, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:20', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000208855),
(81, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:20', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000212908),
(82, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:20', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00031805),
(83, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:20', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000225067),
(84, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:20', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000239134),
(85, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:36', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000572205),
(86, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:36', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00090313),
(87, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:43:36', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00019598),
(88, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:47:54', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000346184),
(89, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:31', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000289917),
(90, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:32', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000354052),
(91, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:32', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000301123),
(92, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:33', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000271082),
(93, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:33', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000245094),
(94, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:33', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000277996),
(95, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:34', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000257969),
(96, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:34', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000385046),
(97, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:34', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000362158),
(98, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:35', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000333071),
(99, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:35', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000340939),
(100, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:35', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000381947),
(101, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:36', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00104499),
(102, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:36', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000370026),
(103, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:37', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00032115),
(104, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:37', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000346899),
(105, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:37', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000357151),
(106, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:37', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000330925),
(107, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:38', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000337839),
(108, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:38', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000275135),
(109, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:38', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000281096),
(110, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:39', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000272989),
(111, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:39', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00029707),
(112, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:39', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000241995),
(113, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:40', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000283957),
(114, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:40', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00027895),
(115, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:40', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000888824),
(116, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:41', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000264168),
(117, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:50:41', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000319004),
(118, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:51:50', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000299931),
(119, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:51:50', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000353098),
(120, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:51:51', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000325918),
(121, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:51:57', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000209093),
(122, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:06', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000390053),
(123, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:06', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000381947),
(124, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:07', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000427961),
(125, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:07', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000349998),
(126, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:08', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000273943),
(127, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:08', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000361919),
(128, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:08', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000349045),
(129, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:09', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000365019),
(130, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:09', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000540018),
(131, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:10', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000247002),
(132, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:10', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00208306),
(133, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:10', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000279903),
(134, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:11', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000833035),
(135, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:11', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000306129);
INSERT INTO `system_logs` (`id`, `level`, `user_id`, `user_type`, `category`, `action`, `entity_type`, `entity_id`, `details`, `before_state`, `after_state`, `ip_address`, `user_agent`, `created_at`, `context`, `http_method`, `request_url`, `session_id`, `execution_time`) VALUES
(136, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:12', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000293016),
(137, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:12', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000369072),
(138, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:12', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000604153),
(139, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:12', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000718832),
(140, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:13', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000521898),
(141, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:13', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000288963),
(142, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:14', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000322819),
(143, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:28', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000237226),
(144, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:28', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000360966),
(145, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:28', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000279903),
(146, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:29', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000519037),
(147, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:55:29', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000540972),
(148, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:56:10', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000569105),
(149, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:56:42', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000653982),
(150, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:56:46', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000329971),
(151, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:35', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000255823),
(152, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:36', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000353098),
(153, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:36', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000426054),
(154, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:36', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.0001688),
(155, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:36', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000319004),
(156, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:37', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000200987),
(157, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:37', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00025177),
(158, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:37', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000212908),
(159, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:37', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000394821),
(160, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:38', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000291109),
(161, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:39', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000361919),
(162, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:39', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00028801),
(163, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:40', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000227928),
(164, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:40', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000181913),
(165, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:40', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000222921),
(166, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:40', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.00022912),
(167, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:40', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000292063),
(168, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:40', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000288963),
(169, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:41', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000236988),
(170, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:41', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000326157),
(171, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:41', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000404119),
(172, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:41', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000195026),
(173, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:41', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000430107),
(174, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:41', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000202179),
(175, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 09:58:42', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000315905),
(176, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:00:51', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000406981),
(177, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:00:58', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000237942),
(178, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:00:58', NULL, 'GET', '/Site-Vitrine/public/admin/users/', 'dqhamj4epcki33n4bab186vkak', 0.000227928),
(179, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:00:59', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000268936),
(180, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:01:00', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000334978),
(181, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:02:58', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000286102),
(182, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:04:55', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000550985),
(183, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:04:55', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.00047493),
(184, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:04:56', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000968218),
(185, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:04:56', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000271082),
(186, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '1', 'Accès à l\'édition de l\'utilisateur #1', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:04:57', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000169992),
(187, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:05:01', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.00030303),
(188, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:05:46', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000253916),
(189, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:06:40', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000406981),
(190, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:06:51', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000363111),
(191, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:07:55', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000267029),
(192, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:33:20', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000288963),
(193, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:33:24', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000237942),
(194, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '1', 'Accès à l\'édition de l\'utilisateur #1', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:33:26', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000288963),
(195, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 10:33:36', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000276804),
(196, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:06:46', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000323772),
(197, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:06:52', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000277996),
(198, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '1', 'Accès à l\'édition de l\'utilisateur #1', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:06:54', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.00022006),
(199, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '1', 'Accès à l\'édition de l\'utilisateur #1', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:10:33', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.00019598),
(200, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:10:41', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000241041),
(201, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '1', 'Accès à l\'édition de l\'utilisateur #1', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:10:44', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000242949),
(202, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '1', 'Accès à l\'édition de l\'utilisateur #1', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:11:05', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000325918),
(203, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:11:22', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000292063),
(204, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:13:13', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000652075),
(205, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:13:16', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000421047),
(206, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:13:18', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000205994),
(207, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:20:15', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000252008),
(208, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:21:57', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000211),
(209, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:21:58', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000165939),
(210, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:21:58', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000159025),
(211, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:21:59', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000159979),
(212, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:22:01', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000467062),
(213, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:22:01', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000254869),
(214, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:22:01', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000263929),
(215, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:22:01', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000191927),
(216, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:22:01', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000274181),
(217, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:22:02', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000168085),
(218, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:23:14', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000219822),
(219, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:27:43', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000287056),
(220, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:27:55', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000194073),
(221, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '1', 'Accès à l\'édition de l\'utilisateur #1', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:29:10', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000261068),
(222, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:29:23', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000353098),
(223, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:31:23', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000340223),
(224, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:31:24', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000210047),
(225, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:13', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000225067),
(226, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:14', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000224829),
(227, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:15', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000232935),
(228, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:15', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000184059),
(229, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:15', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000245094),
(230, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:15', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000179052),
(231, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:15', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000194073),
(232, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:16', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000231028),
(233, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:16', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000264168),
(234, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:16', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000195026),
(235, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:16', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000228167),
(236, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:16', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000129938),
(237, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:16', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000151873),
(238, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:17', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000144005),
(239, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:17', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000211954),
(240, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:17', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000255823),
(241, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:17', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000172853),
(242, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:18', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000227213),
(243, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:18', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000196934),
(244, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:18', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000344038),
(245, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:18', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000207186),
(246, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:18', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000183821),
(247, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:18', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000324011),
(248, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:19', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000252008),
(249, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:33:19', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000202179),
(250, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:06', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000182867),
(251, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:07', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000263929),
(252, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:07', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000211954),
(253, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:07', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000282049),
(254, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:07', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000169992),
(255, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:07', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000324965),
(256, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:08', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000203133),
(257, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:08', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000241041),
(258, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:08', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.00018692),
(259, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:08', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000586987),
(260, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:08', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000145912),
(261, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:09', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000761986),
(262, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:09', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.00135612),
(263, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:09', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000383139);
INSERT INTO `system_logs` (`id`, `level`, `user_id`, `user_type`, `category`, `action`, `entity_type`, `entity_id`, `details`, `before_state`, `after_state`, `ip_address`, `user_agent`, `created_at`, `context`, `http_method`, `request_url`, `session_id`, `execution_time`) VALUES
(264, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:09', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000241995),
(265, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:09', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.00057292),
(266, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:09', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000142813),
(267, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:10', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000444889),
(268, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:10', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000142097),
(269, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:35:13', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000227928),
(270, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 11:42:34', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000290155),
(271, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 12:00:19', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000373125),
(272, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 12:00:22', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000180006),
(273, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 12:23:27', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000432014),
(274, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 12:23:29', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000169992),
(275, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 12:27:20', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000138998),
(276, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 12:28:37', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000174046),
(277, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 16:23:03', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'dqhamj4epcki33n4bab186vkak', 0.000389814),
(278, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 16:23:08', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000231028),
(279, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '1', 'Accès à l\'édition de l\'utilisateur #1', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 OPR/117.0.0.0', '2025-04-06 16:23:11', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=1', 'dqhamj4epcki33n4bab186vkak', 0.000221968),
(280, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 16:55:47', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000524998),
(281, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 16:59:53', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000424147),
(282, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:00:14', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000270128),
(283, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:00:24', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000382185),
(284, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:16:23', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000419855),
(285, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:50:28', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000520945),
(286, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:52:37', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000438929),
(287, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:52:42', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000258923),
(288, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:52:58', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000293016),
(289, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:53:13', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000720978),
(290, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:53:52', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000215054),
(291, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:53:56', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000365019),
(292, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:54:19', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000371933),
(293, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:54:32', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000352144),
(294, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:55:38', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.00028801),
(295, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:55:45', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000273943),
(296, 'info', 1, 'admin', 'admin', 'access_user_create', NULL, NULL, 'Accès au formulaire de création d\'utilisateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:55:57', NULL, 'GET', '/Site-Vitrine/public/admin/users/create.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000634193),
(297, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:55:59', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.00031805),
(298, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:58:57', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.00037694),
(299, 'info', 1, 'admin', 'admin', 'access_user_create', NULL, NULL, 'Accès au formulaire de création d\'utilisateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:58:59', NULL, 'GET', '/Site-Vitrine/public/admin/users/create.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000257969),
(300, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 17:59:09', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000299931),
(301, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:02:33', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000225782),
(302, 'info', 1, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:02:35', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', '7mtrdnnvvq02v3f8t79hume8m3', 0.000205994),
(303, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:02:40', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000217915),
(304, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:03:38', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.00017786),
(305, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:03:44', NULL, 'POST', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000181913),
(306, 'info', 1, 'admin', 'utilisateur', 'update', 'utilisateur', '2', 'Mise à jour de l\'utilisateur Utilisateur Test (ID: 2)', '{\"id\":2,\"nom\":\"Test\",\"prenom\":\"Utilisateur\",\"email\":\"test@exemple.com\",\"telephone\":\"0600000000\",\"adresse\":\"\",\"code_postal\":\"\",\"ville\":\"\",\"pays\":\"France\",\"role\":\"client\",\"actif\":1}', '{\"id\":2,\"nom\":\"Test\",\"prenom\":\"Utilisateur\",\"email\":\"test@exemple.com\",\"telephone\":\"0600000000\",\"role\":\"client\",\"statut\":1,\"date_creation\":\"2025-05-04 15:37:55\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:03:44', '{\"changes\":{\"statut\":{\"old\":null,\"new\":1},\"date_creation\":{\"old\":null,\"new\":\"2025-05-04 15:37:55\"}}}', 'POST', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000198841),
(307, 'info', 1, 'admin', 'user', 'update', 'user', '2', 'Utilisateur #2 modifié par l\'admin #1', '{\"id\":2,\"nom\":\"Test\",\"prenom\":\"Utilisateur\",\"email\":\"test@exemple.com\",\"telephone\":\"0600000000\",\"role\":\"client\",\"statut\":1,\"date_creation\":\"2025-05-04 15:37:55\"}', '{\"id\":2,\"nom\":\"Test\",\"prenom\":\"Utilisateur\",\"email\":\"test@exemple.com\",\"telephone\":\"0600000000\",\"adresse\":\"\",\"code_postal\":\"\",\"ville\":\"\",\"pays\":\"France\",\"role\":\"client\",\"actif\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:03:44', '{\"changes\":{\"adresse\":{\"old\":null,\"new\":\"\"},\"code_postal\":{\"old\":null,\"new\":\"\"},\"ville\":{\"old\":null,\"new\":\"\"},\"pays\":{\"old\":null,\"new\":\"France\"},\"actif\":{\"old\":null,\"new\":1}}}', 'POST', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000261068),
(308, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:03:44', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000390053),
(309, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:03:47', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000257015),
(310, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:04:18', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000272989),
(311, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '1', 'Accès à l\'édition de l\'utilisateur #1', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:04:24', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=1', '7mtrdnnvvq02v3f8t79hume8m3', 0.000255108),
(312, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:04:28', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000627995),
(313, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:05:23', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000361919),
(314, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:05:24', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.0003829),
(315, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:07:35', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000189066),
(316, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:07:37', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.00030899),
(317, 'info', 2, 'admin', 'admin', 'view_user', NULL, NULL, 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:07:40', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000206947),
(318, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:07:43', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000460863),
(319, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:07:44', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.00022316),
(320, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:09:00', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000139952),
(321, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:09:01', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000321865),
(322, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:09:03', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000360012),
(323, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:09:05', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.00018096),
(324, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:09:45', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000311852),
(325, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:07', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000286102),
(326, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:08', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.00029397),
(327, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:10', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000277042),
(328, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:11', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000162125),
(329, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:13', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000485897),
(330, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:15', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000286102),
(331, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:49', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000156879),
(332, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:49', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000227928),
(333, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:50', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000311136),
(334, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:50', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000522852),
(335, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:50', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000365019),
(336, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:50', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000136137),
(337, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:50', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.0001719),
(338, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:52', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000235796),
(339, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:52', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000164986),
(340, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:54', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000208139),
(341, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:55', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000303984),
(342, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '1', 'Accès à l\'édition de l\'utilisateur #1', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:10:58', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=1', '7mtrdnnvvq02v3f8t79hume8m3', 0.000233889),
(343, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:11:00', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000354052),
(344, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:11:06', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000248909),
(345, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:11:07', NULL, 'POST', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000222921),
(346, 'info', 1, 'admin', 'utilisateur', 'update', 'utilisateur', '2', 'Mise à jour de l\'utilisateur Utilisateur Test (ID: 2)', '{\"id\":2,\"nom\":\"Test\",\"prenom\":\"Utilisateur\",\"email\":\"test@exemple.com\",\"telephone\":\"0600000000\",\"adresse\":\"\",\"code_postal\":\"\",\"ville\":\"\",\"pays\":\"France\",\"role\":\"client\",\"actif\":0}', '{\"id\":2,\"nom\":\"Test\",\"prenom\":\"Utilisateur\",\"email\":\"test@exemple.com\",\"telephone\":\"0600000000\",\"role\":\"client\",\"statut\":0,\"date_creation\":\"2025-05-04 15:37:55\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:11:07', '{\"changes\":{\"statut\":{\"old\":null,\"new\":0},\"date_creation\":{\"old\":null,\"new\":\"2025-05-04 15:37:55\"}}}', 'POST', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000274897),
(347, 'info', 1, 'admin', 'user', 'update', 'user', '2', 'Utilisateur #2 modifié par l\'admin #1', '{\"id\":2,\"nom\":\"Test\",\"prenom\":\"Utilisateur\",\"email\":\"test@exemple.com\",\"telephone\":\"0600000000\",\"role\":\"client\",\"statut\":1,\"date_creation\":\"2025-05-04 15:37:55\"}', '{\"id\":2,\"nom\":\"Test\",\"prenom\":\"Utilisateur\",\"email\":\"test@exemple.com\",\"telephone\":\"0600000000\",\"adresse\":\"\",\"code_postal\":\"\",\"ville\":\"\",\"pays\":\"France\",\"role\":\"client\",\"actif\":0}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:11:07', '{\"changes\":{\"adresse\":{\"old\":null,\"new\":\"\"},\"code_postal\":{\"old\":null,\"new\":\"\"},\"ville\":{\"old\":null,\"new\":\"\"},\"pays\":{\"old\":null,\"new\":\"France\"},\"actif\":{\"old\":null,\"new\":0}}}', 'POST', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000203848),
(348, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:11:07', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000247002),
(349, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:11:09', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000244856),
(350, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:11:12', NULL, 'POST', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000267982),
(351, 'info', 1, 'admin', 'utilisateur', 'update', 'utilisateur', '2', 'Mise à jour de l\'utilisateur Utilisateur Test (ID: 2)', '{\"id\":2,\"nom\":\"Test\",\"prenom\":\"Utilisateur\",\"email\":\"test@exemple.com\",\"telephone\":\"0600000000\",\"adresse\":\"\",\"code_postal\":\"\",\"ville\":\"\",\"pays\":\"France\",\"role\":\"client\",\"actif\":1}', '{\"id\":2,\"nom\":\"Test\",\"prenom\":\"Utilisateur\",\"email\":\"test@exemple.com\",\"telephone\":\"0600000000\",\"role\":\"client\",\"statut\":1,\"date_creation\":\"2025-05-04 15:37:55\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:11:12', '{\"changes\":{\"statut\":{\"old\":null,\"new\":1},\"date_creation\":{\"old\":null,\"new\":\"2025-05-04 15:37:55\"}}}', 'POST', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000152111),
(352, 'info', 1, 'admin', 'user', 'update', 'user', '2', 'Utilisateur #2 modifié par l\'admin #1', '{\"id\":2,\"nom\":\"Test\",\"prenom\":\"Utilisateur\",\"email\":\"test@exemple.com\",\"telephone\":\"0600000000\",\"role\":\"client\",\"statut\":0,\"date_creation\":\"2025-05-04 15:37:55\"}', '{\"id\":2,\"nom\":\"Test\",\"prenom\":\"Utilisateur\",\"email\":\"test@exemple.com\",\"telephone\":\"0600000000\",\"adresse\":\"\",\"code_postal\":\"\",\"ville\":\"\",\"pays\":\"France\",\"role\":\"client\",\"actif\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:11:12', '{\"changes\":{\"adresse\":{\"old\":null,\"new\":\"\"},\"code_postal\":{\"old\":null,\"new\":\"\"},\"ville\":{\"old\":null,\"new\":\"\"},\"pays\":{\"old\":null,\"new\":\"France\"},\"actif\":{\"old\":null,\"new\":1}}}', 'POST', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000184059),
(353, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:11:12', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000218868),
(354, 'info', 1, 'admin', 'admin', 'access_user_create', NULL, NULL, 'Accès au formulaire de création d\'utilisateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:14:02', NULL, 'GET', '/Site-Vitrine/public/admin/users/create.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000536203),
(355, 'info', 1, 'admin', 'admin', 'access_user_create', NULL, NULL, 'Accès au formulaire de création d\'utilisateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:15:31', NULL, 'GET', '/Site-Vitrine/public/admin/users/create.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000317812),
(356, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:15:34', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000252008),
(357, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:49:49', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000430107),
(358, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:49:51', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000163794),
(359, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:51:22', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000212908),
(360, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '1', 'Accès à l\'édition de l\'utilisateur #1', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:51:27', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=1', '7mtrdnnvvq02v3f8t79hume8m3', 0.000197887),
(361, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '1', 'Accès à l\'édition de l\'utilisateur #1', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:51:30', NULL, 'POST', '/Site-Vitrine/public/admin/users/edit.php?id=1', '7mtrdnnvvq02v3f8t79hume8m3', 0.000167131),
(362, 'info', 1, 'admin', 'utilisateur', 'update', 'utilisateur', '1', 'Mise à jour de l\'utilisateur Admin Administrateur (ID: 1)', '{\"id\":1,\"nom\":\"Administrateur\",\"prenom\":\"Admin\",\"email\":\"admin@elixirdutemps.com\",\"telephone\":\"\",\"adresse\":\"\",\"code_postal\":\"\",\"ville\":\"\",\"pays\":\"France\",\"role\":\"admin\",\"actif\":1}', '{\"id\":1,\"nom\":\"Administrateur\",\"prenom\":\"Admin\",\"email\":\"admin@elixirdutemps.com\",\"telephone\":\"\",\"role\":\"admin\",\"statut\":1,\"date_creation\":\"2025-04-05 18:29:34\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:51:30', '{\"changes\":{\"statut\":{\"old\":null,\"new\":1},\"date_creation\":{\"old\":null,\"new\":\"2025-04-05 18:29:34\"}}}', 'POST', '/Site-Vitrine/public/admin/users/edit.php?id=1', '7mtrdnnvvq02v3f8t79hume8m3', 0.000167131),
(363, 'info', 1, 'admin', 'user', 'update', 'user', '1', 'Utilisateur #1 modifié par l\'admin #1', '{\"id\":1,\"nom\":\"Administrateur\",\"prenom\":\"Admin\",\"email\":\"admin@elixirdutemps.com\",\"telephone\":null,\"role\":\"admin\",\"statut\":1,\"date_creation\":\"2025-04-05 18:29:34\"}', '{\"id\":1,\"nom\":\"Administrateur\",\"prenom\":\"Admin\",\"email\":\"admin@elixirdutemps.com\",\"telephone\":\"\",\"adresse\":\"\",\"code_postal\":\"\",\"ville\":\"\",\"pays\":\"France\",\"role\":\"admin\",\"actif\":1}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:51:30', '{\"changes\":{\"telephone\":{\"old\":null,\"new\":\"\"},\"adresse\":{\"old\":null,\"new\":\"\"},\"code_postal\":{\"old\":null,\"new\":\"\"},\"ville\":{\"old\":null,\"new\":\"\"},\"pays\":{\"old\":null,\"new\":\"France\"},\"actif\":{\"old\":null,\"new\":1}}}', 'POST', '/Site-Vitrine/public/admin/users/edit.php?id=1', '7mtrdnnvvq02v3f8t79hume8m3', 0.000162125),
(364, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:51:30', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000380039),
(365, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:51:31', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000185966),
(366, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:51:40', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000311852),
(367, 'info', 1, 'admin', 'admin', 'view_user', 'user', '1', 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:51:41', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', '7mtrdnnvvq02v3f8t79hume8m3', 0.000221014),
(368, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '1', 'Accès à l\'édition de l\'utilisateur #1', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:51:46', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=1', '7mtrdnnvvq02v3f8t79hume8m3', 0.000226021),
(369, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:52:56', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.00030899),
(370, 'info', 1, 'admin', 'admin', 'access_user_create', NULL, NULL, 'Accès au formulaire de création d\'utilisateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:53:06', NULL, 'GET', '/Site-Vitrine/public/admin/users/create.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000452042),
(371, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:53:08', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000296831),
(372, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:53:30', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000223875),
(373, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:53:33', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000308037),
(374, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:53:34', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.00020504),
(375, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:55:06', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000218868),
(376, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:55:41', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000264883),
(377, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:55:42', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000203848),
(378, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:55:50', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000253916),
(379, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 18:56:31', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000165939);
INSERT INTO `system_logs` (`id`, `level`, `user_id`, `user_type`, `category`, `action`, `entity_type`, `entity_id`, `details`, `before_state`, `after_state`, `ip_address`, `user_agent`, `created_at`, `context`, `http_method`, `request_url`, `session_id`, `execution_time`) VALUES
(380, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:00:22', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000423908),
(381, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:00:23', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000324011),
(382, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:00:24', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000182152),
(383, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:00:24', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000189066),
(384, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:00:24', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000221968),
(385, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:00:25', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000235796),
(386, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:00:27', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000196934),
(387, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:00:34', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000369072),
(388, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:01:37', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000363111),
(389, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:01:38', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000380993),
(390, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:01:40', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000224113),
(391, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:01:47', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000150919),
(392, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:02:47', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000145912),
(393, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:02:52', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000322819),
(394, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:02:54', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000226021),
(395, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:26', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000165939),
(396, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:28', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.00019002),
(397, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:29', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000183821),
(398, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:29', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000389814),
(399, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:30', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000219107),
(400, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:30', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000354052),
(401, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:30', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000236034),
(402, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:30', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000227928),
(403, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:30', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000185013),
(404, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:30', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000445127),
(405, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:30', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000152111),
(406, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:31', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000187874),
(407, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:31', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.00019002),
(408, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:31', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000237942),
(409, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:31', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000164986),
(410, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:32', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000257969),
(411, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:33', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000256062),
(412, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:35', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000295877),
(413, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:04:36', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000192881),
(414, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:06:33', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.00604701),
(415, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:06:37', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000213861),
(416, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:06:38', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.00039196),
(417, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:06:38', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000424147),
(418, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:07:56', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.010781),
(419, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:08:37', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000135899),
(420, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:08:39', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000261068),
(421, 'info', 1, 'admin', 'admin', 'view_user', 'user', '1', 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:08:41', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', '7mtrdnnvvq02v3f8t79hume8m3', 0.000207186),
(422, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:08:45', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000241995),
(423, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:26:48', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000298023),
(424, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:26:51', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000165939),
(425, 'info', 2, 'admin', 'admin', 'view_user', 'user', '2', 'Consultation des détails de l\'utilisateur Utilisateur Test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:27:00', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000183105),
(426, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:27:07', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000310183),
(427, 'info', 1, 'admin', 'admin', 'view_user', 'user', '1', 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:27:10', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', '7mtrdnnvvq02v3f8t79hume8m3', 0.000160933),
(428, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:27:23', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000303984),
(429, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:28:00', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000559807),
(430, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:29:13', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000397921),
(431, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:30:02', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000458002),
(432, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 19:51:25', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000456095),
(433, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 20:35:16', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000370026),
(434, 'info', 3, 'admin', 'admin', 'view_user', 'user', '3', 'Consultation des détails de l\'utilisateur Jean Dupont', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 20:35:18', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=3', '7mtrdnnvvq02v3f8t79hume8m3', 0.000176907),
(435, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-04 20:35:27', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000310898),
(436, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-05 06:44:07', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.00248003),
(437, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-05 06:44:08', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000442028),
(438, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-05 06:45:37', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.000438929),
(439, 'info', 1, 'admin', 'admin', 'access_user_edit', 'user', '2', 'Accès à l\'édition de l\'utilisateur #2', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-05 06:45:40', NULL, 'GET', '/Site-Vitrine/public/admin/users/edit.php?id=2', '7mtrdnnvvq02v3f8t79hume8m3', 0.000213861),
(440, 'info', 1, 'admin', 'admin', 'view_user', 'user', '1', 'Consultation des détails de l\'utilisateur Admin Administrateur', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-05 06:45:48', NULL, 'GET', '/Site-Vitrine/public/admin/users/view.php?id=1', '7mtrdnnvvq02v3f8t79hume8m3', 0.000221014),
(441, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-06 10:31:57', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', '7mtrdnnvvq02v3f8t79hume8m3', 0.00836205),
(442, 'info', 1, 'admin', 'admin', 'access_users_list', NULL, NULL, 'Accès à la liste des utilisateurs', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/118.0.0.0', '2025-05-06 11:03:28', NULL, 'GET', '/Site-Vitrine/public/admin/users/index.php', 'odlvml6dc5set73iiotmjvm6se', 0.000265121);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `pays` varchar(100) DEFAULT 'France',
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_modification` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `derniere_connexion` datetime DEFAULT NULL,
  `role` enum('client','admin','manager') DEFAULT 'client',
  `actif` tinyint(1) DEFAULT 1,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `telephone`, `adresse`, `code_postal`, `ville`, `pays`, `date_creation`, `date_modification`, `derniere_connexion`, `role`, `actif`, `photo`) VALUES
(1, 'Administrateur', 'Admin', 'admin@elixirdutemps.com', '$2y$10$1nMPtXjFM5A06EQihI2VA..Uo1WGGaN.fLR/vUl.qAqPOCsBoOMiS', '', '', '', '', 'France', '2025-04-05 18:29:34', '2025-05-06 16:08:52', '2025-05-06 16:08:52', 'admin', 1, 'a.jpg'),
(2, 'Test', 'Utilisateur', 'test@exemple.com', '$2y$10$d31xeVfZY92mykiiPNvKV.AkJYF1SwFM4bQaoS/T2iBss8Mk5XNBq', '0600000000', '', '', '', 'France', '2025-05-04 15:37:55', '2025-05-06 12:33:14', '2025-05-06 12:33:14', 'client', 1, NULL),
(3, 'Dupont', 'Jean', 'jean.dupont@example.com', '$2y$10$8sN8Ozs.wYs7cH5QBFtRJuW9HGCbplJL21M5wFUsvIPRxBIXYMZ7C', NULL, NULL, NULL, NULL, 'France', '2025-05-04 22:21:57', NULL, NULL, 'client', 1, NULL),
(4, 'Martin', 'Marie', 'marie.martin@example.com', '$2y$10$8sN8Ozs.wYs7cH5QBFtRJuW9HGCbplJL21M5wFUsvIPRxBIXYMZ7C', NULL, NULL, NULL, NULL, 'France', '2025-05-04 22:21:57', NULL, NULL, 'client', 1, NULL),
(5, 'Bernard', 'Pierre', 'pierre.bernard@example.com', '$2y$10$8sN8Ozs.wYs7cH5QBFtRJuW9HGCbplJL21M5wFUsvIPRxBIXYMZ7C', NULL, NULL, NULL, NULL, 'France', '2025-05-04 22:21:57', NULL, NULL, 'client', 1, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_utilisateur` (`utilisateur_id`),
  ADD KEY `idx_date` (`date_action`);

--
-- Index pour la table `articles_commande`
--
ALTER TABLE `articles_commande`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produit_id` (`produit_id`),
  ADD KEY `idx_commande` (`commande_id`);

--
-- Index pour la table `attributs`
--
ALTER TABLE `attributs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expiration` (`expires_at`);

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `idx_produit` (`produit_id`),
  ADD KEY `idx_statut` (`statut`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `idx_slug` (`slug`);

--
-- Index pour la table `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `idx_reference` (`reference`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_date` (`date_commande`);

--
-- Index pour la table `connexions_log`
--
ALTER TABLE `connexions_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_date` (`date_connexion`);

--
-- Index pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favori` (`utilisateur_id`,`produit_id`),
  ADD KEY `produit_id` (`produit_id`),
  ADD KEY `idx_user_date` (`utilisateur_id`,`date_ajout`);

--
-- Index pour la table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`);

--
-- Index pour la table `panier`
--
ALTER TABLE `panier`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_panier_item` (`utilisateur_id`,`session_id`,`produit_id`),
  ADD KEY `produit_id` (`produit_id`),
  ADD KEY `idx_session` (`session_id`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expiration` (`expires_at`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `idx_reference` (`reference`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_prix` (`prix`),
  ADD KEY `idx_categorie` (`categorie_id`),
  ADD KEY `idx_collection` (`collection_id`),
  ADD KEY `idx_visible_featured` (`visible`,`featured`);

--
-- Index pour la table `produit_attributs`
--
ALTER TABLE `produit_attributs`
  ADD PRIMARY KEY (`produit_id`,`attribut_id`),
  ADD KEY `attribut_id` (`attribut_id`);

--
-- Index pour la table `produit_pages`
--
ALTER TABLE `produit_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_produit_page` (`produit_id`,`page_code`);

--
-- Index pour la table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Index pour la table `promotion_produits`
--
ALTER TABLE `promotion_produits`
  ADD PRIMARY KEY (`promotion_id`,`produit_id`),
  ADD KEY `fk_promotion_produit_produit` (`produit_id`);

--
-- Index pour la table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_level` (`level`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_user_type` (`user_type`),
  ADD KEY `idx_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_ip_address` (`ip_address`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=303;

--
-- AUTO_INCREMENT pour la table `articles_commande`
--
ALTER TABLE `articles_commande`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `attributs`
--
ALTER TABLE `attributs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `collections`
--
ALTER TABLE `collections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `connexions_log`
--
ALTER TABLE `connexions_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `favoris`
--
ALTER TABLE `favoris`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `panier`
--
ALTER TABLE `panier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=759;

--
-- AUTO_INCREMENT pour la table `produit_pages`
--
ALTER TABLE `produit_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=297;

--
-- AUTO_INCREMENT pour la table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=443;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `articles_commande`
--
ALTER TABLE `articles_commande`
  ADD CONSTRAINT `articles_commande_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `articles_commande_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE NO ACTION;

--
-- Contraintes pour la table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `auth_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `avis_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `connexions_log`
--
ALTER TABLE `connexions_log`
  ADD CONSTRAINT `connexions_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD CONSTRAINT `favoris_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favoris_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `panier`
--
ALTER TABLE `panier`
  ADD CONSTRAINT `panier_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `panier_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `produits_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `produits_ibfk_2` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `produit_attributs`
--
ALTER TABLE `produit_attributs`
  ADD CONSTRAINT `produit_attributs_ibfk_1` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `produit_attributs_ibfk_2` FOREIGN KEY (`attribut_id`) REFERENCES `attributs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `produit_pages`
--
ALTER TABLE `produit_pages`
  ADD CONSTRAINT `produit_pages_ibfk_1` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `promotion_produits`
--
ALTER TABLE `promotion_produits`
  ADD CONSTRAINT `fk_promotion_produit_produit` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_promotion_produit_promotion` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
