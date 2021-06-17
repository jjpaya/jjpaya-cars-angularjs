-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 17, 2021 at 04:34 PM
-- Server version: 10.5.9-MariaDB-1:10.5.9+maria~buster
-- PHP Version: 8.0.5

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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`jjpaya`@`%` PROCEDURE `create_recover_token` (IN `_email` VARCHAR(256), IN `_token` VARCHAR(32))  BEGIN
					DECLARE err_user_not_found CONDITION FOR SQLSTATE '45000';
					DECLARE selected_uid BIGINT DEFAULT 0;
					DECLARE selected_uname VARCHAR(30) DEFAULT '';
					DECLARE selected_email VARCHAR(256) DEFAULT '';

					SELECT u.uid, u.username, l.email INTO selected_uid, selected_uname, selected_email
					FROM users AS u
					INNER JOIN links_local AS l ON l.uid = u.uid
					WHERE _email = l.email;
					
					IF selected_uid = 0 THEN
						SIGNAL err_user_not_found;
					END IF;
					
					INSERT INTO pass_reset_tokens (target_uid, confirm_change_token)
					VALUES (selected_uid, _token)
					ON DUPLICATE KEY UPDATE confirm_change_token = VALUE(confirm_change_token), expires = VALUE(expires);
					
					SELECT selected_uid AS uid, selected_uname AS name, selected_email AS email;
				END$$

CREATE DEFINER=`jjpaya`@`%` PROCEDURE `create_verification_token` (IN `_uid` BIGINT, IN `_token` VARCHAR(32))  BEGIN
					DECLARE err_user_not_found CONDITION FOR SQLSTATE '45000';
					DECLARE selected_uid BIGINT DEFAULT 0;
					DECLARE selected_uname VARCHAR(30) DEFAULT '';
					DECLARE selected_email VARCHAR(256) DEFAULT '';

					SELECT u.uid, u.username, l.email INTO selected_uid, selected_uname, selected_email
					FROM users AS u
					INNER JOIN links_local AS l ON l.uid = u.uid
					WHERE u.uid = _uid;
					
					IF selected_uid = 0 THEN
						SIGNAL err_user_not_found;
					END IF;
					
					INSERT INTO mail_verify_tokens (target_uid, confirm_change_token)
					VALUES (selected_uid, _token)
					ON DUPLICATE KEY UPDATE confirm_change_token = VALUE(confirm_change_token), expires = VALUE(expires);
					
					SELECT selected_uid AS uid, selected_uname AS name, selected_email AS email;
				END$$

CREATE DEFINER=`jjpaya`@`%` PROCEDURE `get_github_account_info` (IN `_username` VARCHAR(30))  SELECT u.uid, u.username, u.is_admin, u.img, true AS email_verified, l.email
				FROM users AS u
				INNER JOIN links_github AS l ON l.uid = u.uid
				WHERE _username = u.username$$

CREATE DEFINER=`jjpaya`@`%` PROCEDURE `get_google_account_info` (IN `_username` VARCHAR(30))  SELECT u.uid, u.username, u.is_admin, u.img, true AS email_verified, l.email
				FROM users AS u
				INNER JOIN links_google AS l ON l.uid = u.uid
				WHERE _username = u.username$$

CREATE DEFINER=`jjpaya`@`%` PROCEDURE `get_local_account_hashed_pw` (IN `_username` VARCHAR(30))  SELECT u.uid, u.username, u.is_admin, u.img, l.password
				FROM user AS u
				INNER JOIN links_local AS l ON l.uid = u.uid
				WHERE _username IN (u.username, l.email)$$

CREATE DEFINER=`jjpaya`@`%` PROCEDURE `get_local_account_info` (IN `_username` VARCHAR(30))  SELECT u.uid, u.username, u.is_admin, u.img, l.password, l.email_verified, l.email
				FROM users AS u
				INNER JOIN links_local AS l ON l.uid = u.uid
				WHERE _username IN (u.username, l.email)$$

CREATE DEFINER=`jjpaya`@`%` PROCEDURE `get_or_create_github_account` (IN `_external_uid` VARCHAR(64), IN `_username` VARCHAR(30), IN `_img` VARCHAR(255), IN `_email` VARCHAR(256))  BEGIN
					DECLARE inserted_uid BIGINT DEFAULT 0;
					
					DECLARE EXIT HANDLER FOR SQLEXCEPTION
					BEGIN
						ROLLBACK;
						RESIGNAL;
					END;
					
					SELECT u.uid INTO inserted_uid
						FROM users AS u
						INNER JOIN links_github AS l ON u.uid = l.uid
						WHERE l.external_uid = _external_uid;
						
					IF inserted_uid = 0 THEN
						START TRANSACTION;
					
						INSERT INTO users (username, img)
							VALUES (_username, _img);
	
						SELECT uid INTO inserted_uid
							FROM users
							WHERE username = _username;
						
						INSERT INTO links_github (uid, external_uid, email)
							VALUES (inserted_uid, _external_uid, _email);
						
						COMMIT;
					END IF;
					
					SELECT *
						FROM users
						WHERE uid = inserted_uid;
				END$$

CREATE DEFINER=`jjpaya`@`%` PROCEDURE `get_or_create_google_account` (IN `_external_uid` VARCHAR(64), IN `_username` VARCHAR(30), IN `_img` VARCHAR(255), IN `_email` VARCHAR(256))  BEGIN
					DECLARE inserted_uid BIGINT DEFAULT 0;
					
					DECLARE EXIT HANDLER FOR SQLEXCEPTION
					BEGIN
						ROLLBACK;
						RESIGNAL;
					END;
					
					SELECT u.uid INTO inserted_uid
						FROM users AS u
						INNER JOIN links_google AS l ON u.uid = l.uid
						WHERE l.external_uid = _external_uid;
						
					IF inserted_uid = 0 THEN
						START TRANSACTION;
					
						INSERT INTO users (username, img)
							VALUES (_username, _img);
	
						SELECT uid INTO inserted_uid
							FROM users
							WHERE username = _username;
						
						INSERT INTO links_google (uid, external_uid, email)
							VALUES (inserted_uid, _external_uid, _email);
						
						COMMIT;
					END IF;
					
					SELECT *
						FROM users
						WHERE uid = inserted_uid;
				END$$

CREATE DEFINER=`jjpaya`@`%` PROCEDURE `login_local_account` (IN `username` VARCHAR(30), IN `hashed_pass` VARCHAR(60))  SELECT u.uid
				FROM user AS u
				INNER JOIN links_local AS l ON l.uid = u.uid
				WHERE username IN (u.username, l.email) AND l.password = hashed_pass$$

CREATE DEFINER=`jjpaya`@`%` PROCEDURE `register_local_account` (IN `_username` VARCHAR(30), IN `_email` VARCHAR(256), IN `_hashed_pass` VARCHAR(60))  BEGIN
					DECLARE inserted_uid BIGINT DEFAULT 0;
					
					DECLARE EXIT HANDLER FOR SQLEXCEPTION
					BEGIN
						ROLLBACK;
						RESIGNAL;
					END;
					
					START TRANSACTION;
					
					INSERT INTO users (username)
						VALUES (_username);

					SELECT uid INTO inserted_uid
						FROM users
						WHERE username = _username;
					
					INSERT INTO links_local (uid, email, password)
						VALUES (inserted_uid, _email, _hashed_pass);
					
					COMMIT;
					
					SELECT inserted_uid;
				END$$

CREATE DEFINER=`jjpaya`@`%` PROCEDURE `use_recover_token` (IN `_target_uid` BIGINT, IN `_rtoken` VARCHAR(32), IN `_hashed_pass` VARCHAR(60))  BEGIN
					DECLARE err_invalid_token CONDITION FOR SQLSTATE '45000';
					DECLARE token_valid BOOLEAN DEFAULT false;
					
					DECLARE EXIT HANDLER FOR SQLEXCEPTION
					BEGIN
						ROLLBACK;
						RESIGNAL;
					END;
					
					DELETE FROM pass_reset_tokens
						WHERE NOW() > expires;
					
					START TRANSACTION;
					
					SELECT true INTO token_valid
						FROM pass_reset_tokens
						WHERE target_uid = _target_uid AND confirm_change_token = _rtoken;
					
					IF NOT token_valid THEN
						SIGNAL err_invalid_token;
					END IF;
					
					DELETE FROM pass_reset_tokens
						WHERE target_uid = _target_uid AND confirm_change_token = _rtoken;
					
					UPDATE links_local
						SET password = _hashed_pass
						WHERE uid = _target_uid;
					
					COMMIT;
				END$$

CREATE DEFINER=`jjpaya`@`%` PROCEDURE `use_verify_token` (IN `_target_uid` BIGINT, IN `_vtoken` VARCHAR(32))  BEGIN
					DECLARE err_invalid_token CONDITION FOR SQLSTATE '45000';
					DECLARE token_valid BOOLEAN DEFAULT false;
					
					DECLARE EXIT HANDLER FOR SQLEXCEPTION
					BEGIN
						ROLLBACK;
						RESIGNAL;
					END;
					
					DELETE FROM mail_verify_tokens
						WHERE NOW() > expires;
					
					START TRANSACTION;
					
					SELECT true INTO token_valid
						FROM mail_verify_tokens
						WHERE target_uid = _target_uid AND confirm_change_token = _vtoken;
					
					IF NOT token_valid THEN
						SIGNAL err_invalid_token;
					END IF;
					
					DELETE FROM mail_verify_tokens
						WHERE target_uid = _target_uid AND confirm_change_token = _vtoken;
					
					UPDATE links_local
						SET email_verified = true
						WHERE uid = _target_uid;
					
					COMMIT;
				END$$

DELIMITER ;

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
(1, '3332AAA', '2021-02-01', 1, 'C3', '#00aaff', 60000, 0, 'front', 150000, '2021-02-03', 'No scratches', 40.7419760679919, -1.5378945317183565, 2),
(2, '3322ABB', '2020-01-01', 1, 'C4', '#44aaff', 70000, 0, 'rear', 150000, '2021-02-03', 'New', 42.76359929110072, -5.96031979567308, 8),
(3, '1122FBB', '2019-01-01', 1, 'C6', '#44aa00', 54000, 1, 'all', 250000, '2021-02-03', 'Ok', 39.58660330200065, -3.5780239493092743, 0),
(6, '1124FBN', '2019-01-01', 1, 'Max', '#44bb00', 84000, 1, 'all', 650000, '2021-02-03', 'Ok', 41.02907050111989, -3.5125754928044746, 0),
(7, '6432FBN', '2015-01-01', 3, 'Pro', '#44bb00', 14000, 1, 'all', 1650000, '2021-02-03', 'Ok', 42.028911186286166, -3.7097174364875234, 0),
(8, '8633GGR', '2015-01-01', 3, 'Serie 1', '#333333', 17004, 0, 'all', 100000, '2021-02-03', 'Ok', 38.04367941237211, -3.044450475008646, 1),
(10, '9203ARH', '2010-01-01', 2, 'Clio', '#665566', 197000, 1, 'front', 450000, '2021-02-03', 'Ok', 38.32570954150865, -3.6301007137625483, 1),
(12, '0540OOA', '2012-05-01', 2, 'Captur II', '#665566', 128000, 1, 'front', 390000, '2021-02-03', 'Ok', 40.3006067556968, -5.654392357658029, 91),
(16, '1234BBB', '2021-02-02', 3, 'Serie 1', '#000000', 30, 0, 'front', 100000, '2021-02-03', 'test', 38.50521809828767, -4.902732281919319, 22),
(20, '1234ABC', '2021-03-10', 14, 'Rio', '#d3c05f', 150123, 1, 'front', 100000, '2021-03-01', 'Some wear and tear on the chassis', NULL, NULL, 68),
(26, '7100EEA', '2009-01-01', 2, 'Captur', '#665566', 228000, 0, 'front', 350000, '2021-06-09', 'Ok', NULL, NULL, 11);

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

-- --------------------------------------------------------

--
-- Table structure for table `deleted_users`
--

CREATE TABLE `deleted_users` (
  `uid` bigint(20) NOT NULL,
  `created` datetime NOT NULL,
  `last_login` datetime NOT NULL,
  `deleted` datetime NOT NULL DEFAULT current_timestamp(),
  `username` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `uid` bigint(20) NOT NULL,
  `created` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`invoice_id`, `uid`, `created`) VALUES
(1, 99256657401348097, '2021-06-16'),
(2, 99256657401348097, '2021-06-16'),
(3, 99256657401348097, '2021-06-16'),
(4, 99256657401348097, '2021-06-16');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_lines`
--

CREATE TABLE `invoice_lines` (
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `car_id` bigint(20) UNSIGNED NOT NULL,
  `line_id` bigint(20) UNSIGNED NOT NULL,
  `price_eur_cent` int(11) DEFAULT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `invoice_lines`
--

INSERT INTO `invoice_lines` (`invoice_id`, `car_id`, `line_id`, `price_eur_cent`, `qty`) VALUES
(1, 16, 3, 100000, 1),
(1, 20, 2, 100000, 2),
(1, 26, 1, 350000, 1),
(2, 12, 7, 390000, 1),
(2, 16, 6, 100000, 1),
(2, 20, 5, 100000, 2),
(2, 26, 4, 350000, 1),
(3, 12, 11, 390000, 1),
(3, 16, 10, 100000, 1),
(3, 20, 9, 100000, 2),
(3, 26, 8, 350000, 1),
(4, 10, 14, 450000, 1),
(4, 12, 13, 390000, 1),
(4, 16, 12, 100000, 3);

-- --------------------------------------------------------

--
-- Table structure for table `links_github`
--

CREATE TABLE `links_github` (
  `uid` bigint(20) NOT NULL,
  `external_uid` varchar(64) NOT NULL,
  `email` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `links_github`
--

-- --------------------------------------------------------

--
-- Table structure for table `links_google`
--

CREATE TABLE `links_google` (
  `uid` bigint(20) NOT NULL,
  `external_uid` varchar(64) NOT NULL,
  `email` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `links_google`
--

-- --------------------------------------------------------

--
-- Table structure for table `links_local`
--

CREATE TABLE `links_local` (
  `uid` bigint(20) NOT NULL,
  `email` varchar(256) NOT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `password` varchar(60) NOT NULL
) ;

--
-- Dumping data for table `links_local`
--

INSERT INTO `links_local` (`uid`, `email`, `email_verified`, `password`) VALUES
(99256657401348097, 'admin@admin.com', 1, '$2y$12$nVZc.HcFbwwM5hGoH6fMg.wnAVmQIB1KiKyVEqkC/TxhUCoS03VOm');

-- --------------------------------------------------------

--
-- Table structure for table `mail_verify_tokens`
--

CREATE TABLE `mail_verify_tokens` (
  `target_uid` bigint(20) NOT NULL,
  `confirm_change_token` varchar(32) NOT NULL,
  `expires` datetime NOT NULL DEFAULT (current_timestamp() + interval 6 hour)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pass_reset_tokens`
--

CREATE TABLE `pass_reset_tokens` (
  `target_uid` bigint(20) NOT NULL,
  `confirm_change_token` varchar(32) NOT NULL,
  `expires` datetime NOT NULL DEFAULT (current_timestamp() + interval 6 hour)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `starred_cars`
--

CREATE TABLE `starred_cars` (
  `car_id` bigint(20) UNSIGNED NOT NULL,
  `uid` bigint(20) NOT NULL,
  `starred_on` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `starred_cars`
--

INSERT INTO `starred_cars` (`car_id`, `uid`, `starred_on`) VALUES
(2, 99256657401348097, '2021-06-12 14:42:44'),
(10, 99256657401348097, '2021-06-16 20:53:18'),
(12, 99256657401348097, '2021-06-13 04:10:19'),
(16, 99256657401348097, '2021-06-16 20:53:13'),
(20, 99256657401348097, '2021-06-16 22:00:13'),
(26, 99256657401348097, '2021-06-14 20:12:44');

-- --------------------------------------------------------


--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` bigint(20) NOT NULL DEFAULT (cast(uuid_short() as signed)),
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime NOT NULL DEFAULT current_timestamp(),
  `username` varchar(30) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `img` varchar(255) DEFAULT NULL
) ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `created`, `last_login`, `username`, `is_admin`, `img`) VALUES
(99256657401348097, '2021-05-17 16:34:55', '2021-05-17 16:34:55', 'admin', 1, '/data/avatars/admin.png');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `record_deleted_user_ad` AFTER DELETE ON `users` FOR EACH ROW INSERT INTO deleted_users (uid, created, last_login, username)
				VALUES (OLD.uid, OLD.created, OLD.last_login, OLD.username)
$$
DELIMITER ;

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
-- Indexes for table `deleted_users`
--
ALTER TABLE `deleted_users`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `invoice_lines`
--
ALTER TABLE `invoice_lines`
  ADD PRIMARY KEY (`invoice_id`,`car_id`),
  ADD UNIQUE KEY `line_id` (`line_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `links_github`
--
ALTER TABLE `links_github`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `external_uid` (`external_uid`);

--
-- Indexes for table `links_google`
--
ALTER TABLE `links_google`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `external_uid` (`external_uid`);

--
-- Indexes for table `links_local`
--
ALTER TABLE `links_local`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `mail_verify_tokens`
--
ALTER TABLE `mail_verify_tokens`
  ADD PRIMARY KEY (`target_uid`);

--
-- Indexes for table `pass_reset_tokens`
--
ALTER TABLE `pass_reset_tokens`
  ADD PRIMARY KEY (`target_uid`);

--
-- Indexes for table `starred_cars`
--
ALTER TABLE `starred_cars`
  ADD PRIMARY KEY (`car_id`,`uid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `car_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

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
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `invoice_lines`
--
ALTER TABLE `invoice_lines`
  MODIFY `line_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;


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

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE NO ACTION;

--
-- Constraints for table `invoice_lines`
--
ALTER TABLE `invoice_lines`
  ADD CONSTRAINT `invoice_lines_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`invoice_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_lines_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `cars` (`car_id`) ON DELETE NO ACTION;

--
-- Constraints for table `links_github`
--
ALTER TABLE `links_github`
  ADD CONSTRAINT `links_github_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE;

--
-- Constraints for table `links_google`
--
ALTER TABLE `links_google`
  ADD CONSTRAINT `links_google_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE;

--
-- Constraints for table `links_local`
--
ALTER TABLE `links_local`
  ADD CONSTRAINT `links_local_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE;

--
-- Constraints for table `mail_verify_tokens`
--
ALTER TABLE `mail_verify_tokens`
  ADD CONSTRAINT `mail_verify_tokens_ibfk_1` FOREIGN KEY (`target_uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE;

--
-- Constraints for table `pass_reset_tokens`
--
ALTER TABLE `pass_reset_tokens`
  ADD CONSTRAINT `pass_reset_tokens_ibfk_1` FOREIGN KEY (`target_uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE;

--
-- Constraints for table `starred_cars`
--
ALTER TABLE `starred_cars`
  ADD CONSTRAINT `starred_cars_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`car_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `starred_cars_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
