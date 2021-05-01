-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2021 at 03:45 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 7.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simulasi_crypto`
--

-- --------------------------------------------------------

--
-- Table structure for table `logsimulasi`
--

CREATE TABLE `logsimulasi` (
  `id` bigint(20) NOT NULL,
  `datebeli` datetime NOT NULL,
  `symbol` char(8) NOT NULL,
  `crypto_amount` decimal(20,12) NOT NULL,
  `nominal_amount` bigint(11) NOT NULL,
  `isactive` tinyint(1) NOT NULL DEFAULT 1,
  `isjual` tinyint(1) NOT NULL DEFAULT 0,
  `return` bigint(20) NOT NULL DEFAULT 0,
  `datejual` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `logsimulasi`
--

INSERT INTO `logsimulasi` (`id`, `datebeli`, `symbol`, `crypto_amount`, `nominal_amount`, `isactive`, `isjual`, `return`, `datejual`) VALUES
(1, '2021-05-01 19:03:47', 'BTC', '0.178986079656', 150000000, 1, 0, 0, NULL),
(2, '2021-05-01 19:04:04', 'ETH', '8.443529841292', 350000000, 1, 0, 0, NULL),
(3, '2021-05-01 19:04:24', 'XRP', '5462.331760181800', 125000000, 1, 0, 0, NULL),
(4, '2021-05-01 19:04:40', 'BNB', '13.661203678820', 125000000, 1, 0, 0, NULL),
(5, '2021-05-01 19:05:00', 'DASH', '29.073861822918', 135000000, 0, 1, -313736, '2021-05-01 19:17:02'),
(6, '2021-05-01 19:05:15', 'DOGE', '33647.375504711000', 175000000, 1, 0, 0, NULL),
(7, '2021-05-01 19:05:50', 'BCH', '3.768155659085', 55000000, 1, 0, 0, NULL),
(8, '2021-05-01 19:06:13', 'QTUM', '345.653977325100', 75000000, 0, 1, -63946, '2021-05-01 19:16:52'),
(9, '2021-05-01 19:17:26', 'COAL', '341614.906832300000', 55000000, 1, 0, 0, NULL),
(10, '2021-05-01 19:17:44', 'XRP', '3256.480395988000', 75000000, 1, 0, 0, NULL),
(11, '2021-05-01 19:18:19', 'NXT', '65627.563576702000', 80000000, 1, 0, 0, NULL),
(12, '2021-05-01 19:18:42', 'LINK', '114.544913060410', 65000000, 1, 0, 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `logsimulasi`
--
ALTER TABLE `logsimulasi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `logsimulasi`
--
ALTER TABLE `logsimulasi`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
