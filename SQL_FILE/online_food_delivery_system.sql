-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2026 at 03:35 AM
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
-- Database: `online_food_delivery_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `middleInitial` varchar(1) DEFAULT NULL,
  `extension` varchar(10) DEFAULT NULL,
  `sex` enum('male','female') NOT NULL,
  `birthdate` date NOT NULL,
  `age` int(11) NOT NULL,
  `purok` varchar(50) NOT NULL,
  `barangay` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `zipCode` varchar(10) NOT NULL,
  `country` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `secure_question` varchar(100) DEFAULT NULL,
  `secure_answer` varchar(255) DEFAULT NULL,
  `secure_question2` varchar(100) DEFAULT NULL,
  `secure_answer2` varchar(255) DEFAULT NULL,
  `secure_question3` varchar(100) DEFAULT NULL,
  `secure_answer3` varchar(255) DEFAULT NULL,
  `role` enum('consumer','admin','superadmin') NOT NULL DEFAULT 'consumer',
  `is_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('pending','active','rejected') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstName`, `lastName`, `middleInitial`, `extension`, `sex`, `birthdate`, `age`, `purok`, `barangay`, `city`, `province`, `zipCode`, `country`, `username`, `email`, `password`, `secure_question`, `secure_answer`, `secure_question2`, `secure_answer2`, `secure_question3`, `secure_answer3`, `role`, `is_blocked`, `status`) VALUES
('0000-0001', 'Fanny', 'Lightborn', '', '', 'male', '2000-02-02', 26, 'Purok 5', 'Baranggay 6', 'Cabadbaran City', 'Agusan Del Norte', '8605', 'Philippines', 'fanny21', 'fanny@gmail.com', '$2y$10$kegL1i4v3DBT7GYkEqoAKufipuMoc3tDESp6tCAI/z2b3CMkhCWgC', '1. Who is your bestfriend in elementary? *', '$2y$10$oAxzxNREO5zqOOqdlv64MOrSILWtPDXTBAI9zKBaRexUP9/3mPBp2', '2. What is the name of your pet? *', '$2y$10$qe8S/KD9Iff/3q5Ea.J5duSHhZS6mCZG70/YbPoCqfuTwJA8VrM06', '3. Who is your favorite teacher in highschool? *', '$2y$10$9IwiBrtHZBdldBDT.gYVnuALtbRNyEYEsHKWXmerwlpDvaHb2/iTe', 'superadmin', 0, 'active'),
('0000-0002', 'Kagura', 'Nyks', '', '', 'male', '2000-02-02', 26, 'Purok 5', 'Baranggay 6', 'Cabadbaran City', 'Agusan Del Norte', '8605', 'Philippines', 'kagura21', 'kagura@gmail.com', '$2y$10$KEHj4Eb2RTS4qaTUbtnEsugoJOujqcvxiI8J/S0.T/PIDjYNLGEzq', '1. Who is your bestfriend in elementary? *', '$2y$10$MvcpF9RZfcRxnba0bjOVmeIA2FkmTIS/5t4Pn3IlWZJNJVCBmrdH6', '2. What is the name of your pet? *', '$2y$10$gtUup7ukshnxnhNUko11Veou3FaPJI8tdvwstmG4FG5pvayPyjJ0y', '3. Who is your favorite teacher in highschool? *', '$2y$10$FaOdDbvwhRZTbqC4.yXu8eFtYMkr2/kfg5wxL9Kr7kT.zZUxDc4pu', 'admin', 0, 'active'),
('2022-0909', 'Maria', 'Cruz', '', '', 'female', '2003-02-02', 23, 'Purok 5', 'Baranggay 6', 'City of Cabadbaran', 'Agusan Del Norte', '8605', 'Philippines', 'maria21', 'maria@gmail.com', '$2y$10$Q.vSW.uIu4JA4kU7mNauf.o0./fwkyHjBCtlSh.J9m7jt.bvb4LBy', 'Who is your bestfriend in elementary?', '$2y$10$/FNv6W81EvU1x0Tdg7fAPOyW9TEf2J/2Wtv0ZjzoaAg5uo1Y1zyCS', 'What elementary school did you attend?', '$2y$10$NyzO.19X5yz6kLsPS.anq.8hQRsDs8RrzLESMV7TSUs9WsVyGzBmu', 'What street did you grow up on?', '$2y$10$mQitZHHHrBleeAkTaGdODu27nriLKBd4jLnVRXUl4kQMD1QxRp4k2', 'consumer', 0, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
