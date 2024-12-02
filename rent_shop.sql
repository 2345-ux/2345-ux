-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 02, 2024 at 11:57 AM
-- Server version: 5.7.31
-- PHP Version: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rent_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `t_marche`
--

DROP TABLE IF EXISTS `t_marche`;
CREATE TABLE IF NOT EXISTS `t_marche` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `nom_marche` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `commune` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `t_marche`
--

INSERT INTO `t_marche` (`id`, `code`, `nom_marche`, `region`, `commune`) VALUES
(1, 'MKT20241123123327831', 'Grand Marché', 'Niamey', '3'),
(2, 'MKT20241123123354400', 'Bonkaney', 'Niamey', '2');

-- --------------------------------------------------------

--
-- Table structure for table `t_payments`
--

DROP TABLE IF EXISTS `t_payments`;
CREATE TABLE IF NOT EXISTS `t_payments` (
  `id_payment` int(11) NOT NULL AUTO_INCREMENT,
  `rental_code` varchar(50) DEFAULT NULL,
  `shop_name` varchar(255) DEFAULT NULL,
  `tenant_name` varchar(255) DEFAULT NULL,
  `montant_verse` decimal(10,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `nom_marche` varchar(255) DEFAULT NULL,
  `situation_name` varchar(255) DEFAULT NULL,
  `id_situation` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_payment`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `t_payments`
--

INSERT INTO `t_payments` (`id_payment`, `rental_code`, `shop_name`, `tenant_name`, `montant_verse`, `payment_date`, `nom_marche`, `situation_name`, `id_situation`) VALUES
(1, 'PAY20241123124151210', 'chacha', 'HAMS', '1000.00', '2024-11-23', '1', 'SIT-20241123123833849', NULL),
(2, 'PAY20241123124151132', 'chacha', 'HAMS', '1000.00', '2024-11-23', '1', 'SIT-20241123123833849', NULL),
(3, 'PAY20241123124151850', 'chacha', 'HAMS', '1000.00', '2024-11-23', '1', 'SIT-20241123123833849', NULL),
(4, 'PAY20241123124848864', 'Safi mira', 'Habibi', '10000.00', '2024-11-12', '1', 'SIT-20241123123833849', NULL),
(5, 'PAY20241123124848237', 'Safi mira', 'Habibi', '10000.00', '2024-11-12', '1', 'SIT-20241123123833849', NULL),
(6, 'PAY20241123125241682', 'soso', 'mony', '20000.00', '2024-11-23', '1', 'SIT-20241123124004016', NULL),
(7, 'PAY20241123125340009', 'Haidara ets', 'Hassia', '10000.00', '2024-11-23', '2', 'SIT-20241123123833849', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `t_shop`
--

DROP TABLE IF EXISTS `t_shop`;
CREATE TABLE IF NOT EXISTS `t_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_code` varchar(50) NOT NULL,
  `shop_number` varchar(20) NOT NULL,
  `shop_name` varchar(100) NOT NULL,
  `tenant_name` varchar(100) NOT NULL,
  `tenant_phone` varchar(15) DEFAULT NULL,
  `shop_location` varchar(150) DEFAULT NULL,
  `shop_rent` decimal(10,2) DEFAULT NULL,
  `id_marche` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_marche` (`id_marche`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `t_shop`
--

INSERT INTO `t_shop` (`id`, `shop_code`, `shop_number`, `shop_name`, `tenant_name`, `tenant_phone`, `shop_location`, `shop_rent`, `id_marche`, `created_at`, `updated_at`) VALUES
(1, 'SHOP-20241130194754595', 'B658', 'Hamadou Alfousseini', 'Alfousseini', '+22798565678', '13°31\'23.2\"N 2°05\'24.0\"E', '1000.00', 2, '2024-11-30 19:47:54', '2024-11-30 19:47:54'),
(2, 'SHOP-20241202110217711', 'b555', 'boutique2', 'locataire', '+22798565678', '13°31\'23.2\"N 2°05\'24.0\"E', '100000.00', 1, '2024-12-02 11:02:17', '2024-12-02 11:02:17'),
(3, 'SHOP-20241202115558549', '3220', 'Boutique 4', 'Locataire 3', '+22798565678', '13°30\'49.3\"N 2°06\'35.3\"E', '10000.00', 2, '2024-12-02 11:55:58', '2024-12-02 11:55:58');

-- --------------------------------------------------------

--
-- Table structure for table `t_situations`
--

DROP TABLE IF EXISTS `t_situations`;
CREATE TABLE IF NOT EXISTS `t_situations` (
  `id_situation` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `situation_nom` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id_situation`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `t_situations`
--

INSERT INTO `t_situations` (`id_situation`, `code`, `situation_nom`, `description`) VALUES
(1, 'SIT-20241123123833849', 'Payé', 'Paiements complet'),
(2, 'SIT-20241123123924012', 'non-Payé', 'Paiements non effectuer'),
(3, 'SIT-20241123124004016', 'paiements partiel', 'Paiements en tranche');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
