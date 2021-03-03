-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 03, 2021 at 07:17 AM
-- Server version: 10.3.27-MariaDB-0+deb10u1
-- PHP Version: 8.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `forum`
--

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `car_id` bigint(20) UNSIGNED NOT NULL,
  `num_plate` varchar(7) NOT NULL CHECK (`num_plate` regexp '[0-9]{4}[A-Z]{3}'),
  `reg_date` date NOT NULL,
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `model` varchar(24) NOT NULL CHECK (`model` regexp '^[a-z0-9 ]+$'),
  `color` varchar(7) NOT NULL CHECK (`color` regexp '^#[A-Z0-9]{6}$'),
  `kms` int(11) NOT NULL DEFAULT 0 CHECK (`kms` >= 0 and `kms` <= 999999),
  `itv` tinyint(1) NOT NULL,
  `wheel_power` enum('front','rear','all') NOT NULL,
  `price_eur_cent` int(11) NOT NULL CHECK (`price_eur_cent` >= 10000 and `price_eur_cent` <= 500000000),
  `created` date NOT NULL DEFAULT curdate(),
  `description` varchar(255) NOT NULL,
  `lat` double DEFAULT NULL,
  `lon` double DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`car_id`, `num_plate`, `reg_date`, `brand_id`, `model`, `color`, `kms`, `itv`, `wheel_power`, `price_eur_cent`, `created`, `description`, `lat`, `lon`, `views`) VALUES
(1, '3332AAA', '2021-02-01', 1, 'C3', '#00aaff', 60000, 0, 'front', 150000, '2021-02-03', 'No scratches', 40.7419760679919, -1.5378945317183565, 1),
(2, '3322ABB', '2020-01-01', 1, 'C4', '#44aaff', 70000, 0, 'rear', 150000, '2021-02-03', 'New', 42.76359929110072, -5.96031979567308, 4),
(3, '1122FBB', '2019-01-01', 1, 'C6', '#44aa00', 54000, 1, 'all', 250000, '2021-02-03', 'Ok', 39.58660330200065, -3.5780239493092743, 0),
(6, '1124FBN', '2019-01-01', 1, 'Max', '#44bb00', 84000, 1, 'all', 650000, '2021-02-03', 'Ok', 41.02907050111989, -3.5125754928044746, 0),
(7, '6432FBN', '2015-01-01', 3, 'Pro', '#44bb00', 14000, 1, 'all', 1650000, '2021-02-03', 'Ok', 42.028911186286166, -3.7097174364875234, 0),
(8, '8633GGR', '2015-01-01', 3, 'Serie 1', '#333333', 17004, 0, 'all', 100000, '2021-02-03', 'Ok', 38.04367941237211, -3.044450475008646, 0),
(10, '9203ARH', '2010-01-01', 2, 'Clio', '#665566', 197000, 1, 'front', 450000, '2021-02-03', 'Ok', 38.32570954150865, -3.6301007137625483, 1),
(11, '7100EEA', '2009-01-01', 2, 'Captur', '#665566', 228000, 1, 'front', 350000, '2021-02-03', 'Ok', 39.55136796032951, -5.7448579033965785, 0),
(12, '0540OOA', '2012-05-01', 2, 'Captur II', '#665566', 128000, 1, 'front', 390000, '2021-02-03', 'Ok', 40.3006067556968, -5.654392357658029, 66),
(16, '1234BBB', '2021-02-02', 3, 'Serie 1', '#000000', 30, 0, 'front', 100000, '2021-02-03', 'test', 38.50521809828767, -4.902732281919319, 13),
(20, '1234ABC', '2021-03-10', 14, 'Rio', '#d3c05f', 150123, 1, 'front', 100000, '2021-03-01', 'Some wear and tear on the chassis', NULL, NULL, 16);

-- --------------------------------------------------------

--
-- Table structure for table `car_brands`
--

CREATE TABLE `car_brands` (
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(24) NOT NULL CHECK (`name` regexp '^[a-z ]+$'),
  `img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `car_brands`
--

INSERT INTO `car_brands` (`brand_id`, `name`, `img`) VALUES
(1, 'Citroen', '/data/brand_imgs/citroen.png'),
(2, 'Renault', '/data/brand_imgs/renault.png'),
(3, 'BMW', '/data/brand_imgs/bmw.png'),
(4, 'Audi', '/data/brand_imgs/audi.png'),
(5, 'Fiat', '/data/brand_imgs/fiat.png'),
(6, 'Ford', '/data/brand_imgs/ford.png'),
(7, 'Honda', '/data/brand_imgs/honda.png'),
(8, 'Hyundai', '/data/brand_imgs/hyundai.png'),
(9, 'Opel', '/data/brand_imgs/opel.png'),
(10, 'Mazda', '/data/brand_imgs/mazda.png'),
(11, 'Mercedes Benz', '/data/brand_imgs/mercedes.png'),
(12, 'Mitsubishi', '/data/brand_imgs/mitsubishi.png'),
(13, 'Nissan', '/data/brand_imgs/nissan.png'),
(14, 'KIA', '/data/brand_imgs/kia.png');

-- --------------------------------------------------------

--
-- Table structure for table `car_images`
--

CREATE TABLE `car_images` (
  `img_id` bigint(20) UNSIGNED NOT NULL,
  `car_id` bigint(20) UNSIGNED DEFAULT NULL,
  `path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `car_images`
--

INSERT INTO `car_images` (`img_id`, `car_id`, `path`) VALUES
(1, 12, '/data/cars/captur2.jpg'),
(2, 20, '/data/cars/kiario.jpg'),
(3, 16, '/data/cars/bmwserie1.jpeg'),
(4, 12, '/data/cars/captur2-2.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`car_id`),
  ADD UNIQUE KEY `num_plate` (`num_plate`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Indexes for table `car_brands`
--
ALTER TABLE `car_brands`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `car_images`
--
ALTER TABLE `car_images`
  ADD PRIMARY KEY (`img_id`),
  ADD KEY `car_id` (`car_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `car_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `car_brands`
--
ALTER TABLE `car_brands`
  MODIFY `brand_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `car_images`
--
ALTER TABLE `car_images`
  MODIFY `img_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cars`
--
ALTER TABLE `cars`
  ADD CONSTRAINT `cars_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `car_brands` (`brand_id`);

--
-- Constraints for table `car_images`
--
ALTER TABLE `car_images`
  ADD CONSTRAINT `car_images_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`car_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
