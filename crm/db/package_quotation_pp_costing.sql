-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2026 at 01:25 PM
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
-- Database: `tour_operator_new_2_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `package_quotation_pp_costing`
--

CREATE TABLE `package_quotation_pp_costing` (
  `id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `package_id` int(11) DEFAULT NULL,
  `pax_type` enum('adult','cweb','cwnb','infant') NOT NULL,
  `hotel_cost` decimal(10,2) DEFAULT 0.00,
  `transfer_cost` decimal(10,2) DEFAULT 0.00,
  `activity_cost` decimal(10,2) DEFAULT 0.00,
  `land_cost` decimal(10,2) DEFAULT 0.00,
  `service_charge` decimal(10,2) DEFAULT 0.00,
  `discount_in` tinyint(10) DEFAULT 0 COMMENT '1-percent,2-flat\r\n',
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `flight_cost` decimal(10,2) DEFAULT 0.00,
  `train_cost` decimal(10,2) DEFAULT 0.00,
  `cruise_cost` decimal(10,2) DEFAULT 0.00,
  `visa_cost` decimal(10,2) DEFAULT 0.00,
  `guide_cost` decimal(10,2) DEFAULT 0.00,
  `misc_cost` decimal(10,2) DEFAULT 0.00,
  `tax_apply_on` enum('basic','service','total') DEFAULT 'total',
  `tax_value` varchar(100) DEFAULT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `tcs_percent` decimal(5,2) DEFAULT 0.00,
  `tcs_amount` decimal(10,2) DEFAULT 0.00,
  `total_cost` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tcs` decimal(10,2) DEFAULT NULL,
  `tcsvalue` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `package_quotation_pp_costing`
--
ALTER TABLE `package_quotation_pp_costing`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `package_quotation_pp_costing`
--
ALTER TABLE `package_quotation_pp_costing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
