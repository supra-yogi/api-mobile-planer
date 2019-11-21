-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 09, 2018 at 08:13 PM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mplanner`
--

-- --------------------------------------------------------

--
-- Table structure for table `batasans`
--

CREATE TABLE `batasans` (
  `id` int(5) NOT NULL,
  `userId` int(5) DEFAULT NULL,
  `waktuCepatFrom` int(3) DEFAULT NULL,
  `waktuCepatTo` int(3) DEFAULT NULL,
  `waktuLamaFrom` int(3) DEFAULT NULL,
  `waktuLamaTo` int(3) DEFAULT NULL,
  `biayaRendahFrom` decimal(10,2) DEFAULT NULL,
  `biayaRendahTo` decimal(10,2) DEFAULT NULL,
  `biayaSedangFrom` decimal(10,2) DEFAULT NULL,
  `biayaSedangTo` decimal(10,2) DEFAULT NULL,
  `biayaTinggiFrom` decimal(10,2) DEFAULT NULL,
  `biayaTinggiTo` decimal(10,2) DEFAULT NULL,
  `kebutuhanRendahFrom` int(2) DEFAULT NULL,
  `kebutuhanRendahTo` int(2) DEFAULT NULL,
  `kebutuhanTinggiFrom` int(2) DEFAULT NULL,
  `kebutuhanTinggiTo` int(2) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `batasans`
--

INSERT INTO `batasans` (`id`, `userId`, `waktuCepatFrom`, `waktuCepatTo`, `waktuLamaFrom`, `waktuLamaTo`, `biayaRendahFrom`, `biayaRendahTo`, `biayaSedangFrom`, `biayaSedangTo`, `biayaTinggiFrom`, `biayaTinggiTo`, `kebutuhanRendahFrom`, `kebutuhanRendahTo`, `kebutuhanTinggiFrom`, `kebutuhanTinggiTo`, `created_date`, `updated_date`) VALUES
(7, 21, 4, 24, 12, 36, '1000000.00', '4000000.00', '2000000.00', '8000000.00', '6000000.00', '10000000.00', 1, 7, 4, 10, '2018-05-23 06:31:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `detail_plannings`
--

CREATE TABLE `detail_plannings` (
  `planningId` int(5) DEFAULT NULL,
  `bulan` int(3) DEFAULT NULL,
  `tabunganAwal` decimal(10,2) DEFAULT NULL,
  `setoranBulanan` decimal(10,2) DEFAULT NULL,
  `bunga` decimal(10,2) DEFAULT NULL,
  `pajak` decimal(10,2) DEFAULT NULL,
  `tabunganAkhir` decimal(11,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `plannings`
--

CREATE TABLE `plannings` (
  `id` int(5) NOT NULL,
  `userId` int(5) DEFAULT NULL,
  `goalName` varchar(50) DEFAULT NULL,
  `jangkaWaktu` int(3) DEFAULT NULL,
  `currentCost` decimal(11,2) DEFAULT NULL,
  `futureCost` decimal(11,2) DEFAULT NULL,
  `alreadyInvest` decimal(11,2) DEFAULT NULL,
  `lumpsum` decimal(11,2) DEFAULT NULL,
  `monthlyInvest` decimal(11,2) DEFAULT NULL,
  `biayaAdmin` decimal(7,2) DEFAULT NULL,
  `totalBiayaAdmin` decimal(10,2) DEFAULT NULL,
  `pajakBunga` decimal(7,2) DEFAULT NULL,
  `totalPajakBunga` decimal(10,2) DEFAULT NULL,
  `totalBunga` decimal(10,2) DEFAULT NULL,
  `requiredRate` int(2) DEFAULT NULL,
  `inflationRate` decimal(4,2) DEFAULT NULL,
  `interestRate` decimal(4,2) DEFAULT NULL,
  `priority` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(5) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `suspend` tinyint(1) DEFAULT '0',
  `user_level` varchar(25) DEFAULT 'member',
  `created_date` datetime DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `suspend`, `user_level`, `created_date`, `updated_date`) VALUES
(21, 'Yogi', 'supra.2014tin041@civitas.ukrida.ac.id', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 0, 'member', '2018-05-23 06:31:42', '2018-05-23 11:03:01'),
(22, 'Supra Yogi Hermawan', 'hermawanyogi42@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 0, 'admin', '2018-05-28 00:00:00', '2018-05-29 05:45:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `batasans`
--
ALTER TABLE `batasans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_userId_batasans` (`userId`);

--
-- Indexes for table `detail_plannings`
--
ALTER TABLE `detail_plannings`
  ADD KEY `fk_detail_planningId` (`planningId`);

--
-- Indexes for table `plannings`
--
ALTER TABLE `plannings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_userId_plannigs` (`userId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `batasans`
--
ALTER TABLE `batasans`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `plannings`
--
ALTER TABLE `plannings`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `batasans`
--
ALTER TABLE `batasans`
  ADD CONSTRAINT `fk_userId_batasans` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `detail_plannings`
--
ALTER TABLE `detail_plannings`
  ADD CONSTRAINT `fk_detail_planningId` FOREIGN KEY (`planningId`) REFERENCES `plannings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `plannings`
--
ALTER TABLE `plannings`
  ADD CONSTRAINT `fk_userId_plannigs` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
