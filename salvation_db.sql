-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2023 at 11:40 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `salvation_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_table`
--

CREATE TABLE `admin_table` (
  `admin_id` int(11) NOT NULL,
  `admin_email_address` varchar(30) NOT NULL,
  `admin_password` varchar(100) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `company_address` mediumtext NOT NULL,
  `company_contact_no` int(10) NOT NULL,
  `company_logo` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `admin_table`
--

INSERT INTO `admin_table` (`admin_id`, `admin_email_address`, `admin_password`, `admin_name`, `company_name`, `company_address`, `company_contact_no`, `company_logo`) VALUES
(1, 'Admin@gmail.com', 'admin1234', 'Senol Wijetunge', 'Salvation Jewellers', 'Colombo, Sri Lanka', 112568727, '../../img/logo.png');

-- --------------------------------------------------------

--
-- Table structure for table `customer_table`
--

CREATE TABLE `customer_table` (
  `customer_id` int(11) NOT NULL,
  `customer_email_address` varchar(200) NOT NULL,
  `customer_password` varchar(100) NOT NULL,
  `customer_first_name` varchar(100) NOT NULL,
  `customer_last_name` varchar(100) NOT NULL,
  `customer_date_of_birth` varchar(100) NOT NULL,
  `customer_phone_no` varchar(100) NOT NULL,
  `customer_address` varchar(100) NOT NULL,
  `customer_added_on` datetime NOT NULL,
  `customer_verification_code` varchar(100) NOT NULL,
  `email_verify` enum('No','Yes') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emergency_table`
--

CREATE TABLE `emergency_table` (
  `emergency_id` int(11) NOT NULL,
  `emergency_email_address` text NOT NULL,
  `emergency_first_name` text NOT NULL,
  `emergency_last_name` text NOT NULL,
  `emergency_contact` text NOT NULL,
  `emergency_address` text NOT NULL,
  `customer_id` int(11) NOT NULL,
  `emergency_added_on` datetime NOT NULL,
  `emergency_verification_code` varchar(100) NOT NULL,
  `emergency_email_verify` enum('No','Yes') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_table`
--

CREATE TABLE `feedback_table` (
  `feedback_id` int(11) NOT NULL,
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `feedback` varchar(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_table`
--

CREATE TABLE `order_table` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_added_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_table`
--

CREATE TABLE `product_table` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(30) NOT NULL,
  `product_type` varchar(30) NOT NULL,
  `product_category` varchar(30) NOT NULL,
  `product_price` int(30) NOT NULL,
  `product_image` varchar(100) NOT NULL,
  `product_added_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `product_table`
--

INSERT INTO `product_table` (`product_id`, `product_name`, `product_type`, `product_category`, `product_price`, `product_image`, `product_added_on`) VALUES
(1, 'ghs', 'sfbg', 'db', 4500, '2063601126.png', '2023-03-19 12:08:50'),
(2, 'Ring', 'Ring', 'Men', 4500, 'Ring.png', '2023-05-10 12:57:35');

-- --------------------------------------------------------

--
-- Table structure for table `profile_table`
--

CREATE TABLE `profile_table` (
  `victim_id` int(11) NOT NULL,
  `victim_image` text NOT NULL,
  `victim_first_name` text NOT NULL,
  `victim_last_name` text NOT NULL,
  `victim_date_of_birth` text NOT NULL,
  `victim_gender` text NOT NULL,
  `victim_height` text NOT NULL,
  `customer_id` int(11) NOT NULL,
  `victim_status` enum('Active','Inactive') NOT NULL,
  `victim_added_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_table`
--
ALTER TABLE `admin_table`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `customer_table`
--
ALTER TABLE `customer_table`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `emergency_table`
--
ALTER TABLE `emergency_table`
  ADD PRIMARY KEY (`emergency_id`),
  ADD KEY `pet_table_fk` (`customer_id`);

--
-- Indexes for table `feedback_table`
--
ALTER TABLE `feedback_table`
  ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `order_table`
--
ALTER TABLE `order_table`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `product_table`
--
ALTER TABLE `product_table`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `profile_table`
--
ALTER TABLE `profile_table`
  ADD PRIMARY KEY (`victim_id`),
  ADD KEY `pet_table_fk` (`customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_table`
--
ALTER TABLE `admin_table`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer_table`
--
ALTER TABLE `customer_table`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emergency_table`
--
ALTER TABLE `emergency_table`
  MODIFY `emergency_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_table`
--
ALTER TABLE `feedback_table`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_table`
--
ALTER TABLE `order_table`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_table`
--
ALTER TABLE `product_table`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `profile_table`
--
ALTER TABLE `profile_table`
  MODIFY `victim_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `emergency_table`
--
ALTER TABLE `emergency_table`
  ADD CONSTRAINT `pet_table_fk` FOREIGN KEY (`customer_id`) REFERENCES `customer_table` (`customer_id`);

--
-- Constraints for table `order_table`
--
ALTER TABLE `order_table`
  ADD CONSTRAINT `order_table_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product_table` (`product_id`),
  ADD CONSTRAINT `order_table_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer_table` (`customer_id`);

--
-- Constraints for table `profile_table`
--
ALTER TABLE `profile_table`
  ADD CONSTRAINT `profile_table_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer_table` (`customer_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
