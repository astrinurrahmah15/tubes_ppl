-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2025 at 05:36 PM
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
-- Database: `gawe_yuk`
--

-- --------------------------------------------------------

--
-- Table structure for table `lowongan`
--

CREATE TABLE `lowongan` (
  `id` int(11) NOT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `gaji` varchar(100) NOT NULL,
  `jenis_pekerjaan` varchar(100) NOT NULL,
  `tunjangan` text NOT NULL,
  `umur` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `instagram` varchar(100) DEFAULT NULL,
  `facebook` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lowongan`
--

INSERT INTO `lowongan` (`id`, `nama_perusahaan`, `alamat`, `gaji`, `jenis_pekerjaan`, `tunjangan`, `umur`, `email`, `whatsapp`, `instagram`, `facebook`, `website`, `deskripsi`, `created_at`) VALUES
(1, 'gfhfjfi', 'dgdsg4', '2313123', 'dghdgdg', '34131', '123', 'raptor@kal-official.fun', 'e41413133', 'adadad33r3', '1321sfsfsf', '312315345', 'memasak', '2025-01-07 02:52:36'),
(2, 'bisa bisa', 'jl kebanggaan 1', '40000000', 'programming', 'tidak ada', '20-30', 'contoh@gmail.com', '888888888', 'bisa_bisa', 'bisa_bisa', 'bisa.com', 'melakukan pemograman website', '2025-01-07 06:43:06');

-- --------------------------------------------------------

--
-- Table structure for table `masukan`
--

CREATE TABLE `masukan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nomor_hp` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `jenis_kelamin` varchar(10) NOT NULL,
  `masukan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `masukan`
--

INSERT INTO `masukan` (`id`, `nama`, `nomor_hp`, `email`, `jenis_kelamin`, `masukan`, `created_at`) VALUES
(6, 'Andre', '978234862', 'contoh@gmail.com', 'Laki-laki', 'kurang lengkap fiturnya', '2025-01-07 06:43:48'),
(7, 'egg', '1234', 'fickiantoegar@gmail.com', 'Laki-laki', 'sayaaaaaaaaaaaaa', '2025-04-27 12:25:50');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `gender` varchar(50) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `education` varchar(100) DEFAULT NULL,
  `major` varchar(100) DEFAULT NULL,
  `work_experience` text DEFAULT NULL,
  `certifications` text DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `github` varchar(255) DEFAULT NULL,
  `portfolio` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `created_at`, `gender`, `birthdate`, `status`, `skills`, `phone`, `address`, `education`, `major`, `work_experience`, `certifications`, `linkedin`, `github`, `portfolio`, `profile_photo`) VALUES
(18, 'andre', 'andre@gmail.com', '123456', '2025-01-07 02:44:44', 'Laki-laki', '2025-01-01', 'Belum Menikah', 'memasak', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'Andre Arifin', 'contoh@gmail.com', '123456', '2025-01-07 06:40:28', 'Laki-laki', '2004-01-16', 'Belum Menikah', 'programing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'eggar', 'egg@bla.com', '123', '2025-04-27 10:50:15', 'Laki-laki', '2025-04-27', 'Pencari Kerja', 'mancing', '', '', '', '', '', '', '', '', '', 'uploads/profile_photos/21_1745755942.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `lamaran`
--

CREATE TABLE `lamaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `lowongan_id` int(11) NOT NULL,
  `status` enum('pending','diterima','ditolak') NOT NULL DEFAULT 'pending',
  `cv` varchar(255) NOT NULL,
  `surat_lamaran` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `lowongan_id` (`lowongan_id`),
  CONSTRAINT `lamaran_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lamaran_ibfk_2` FOREIGN KEY (`lowongan_id`) REFERENCES `lowongan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lowongan`
--
ALTER TABLE `