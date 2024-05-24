-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 24, 2024 at 12:43 PM
-- Server version: 8.0.31
-- PHP Version: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `opencart3`
--

-- --------------------------------------------------------

--
-- Table structure for table `oc_api_categorytypes`
--

DROP TABLE IF EXISTS `oc_api_categorytypes`;
CREATE TABLE IF NOT EXISTS `oc_api_categorytypes` (
  `id` int NOT NULL,
  `vehiclecategorytype` varchar(100) DEFAULT NULL,
  `displayorder` varchar(5) DEFAULT NULL,
  `api_name` varchar(100) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oc_api_driverages`
--

DROP TABLE IF EXISTS `oc_api_driverages`;
CREATE TABLE IF NOT EXISTS `oc_api_driverages` (
  `id` int NOT NULL,
  `driverage` int DEFAULT NULL,
  `isdefault` tinyint(1) DEFAULT NULL,
  `api_name` varchar(100) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oc_api_locations`
--

DROP TABLE IF EXISTS `oc_api_locations`;
CREATE TABLE IF NOT EXISTS `oc_api_locations` (
  `id` int NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `isdefault` int DEFAULT '0',
  `ispickupavailable` int DEFAULT '0',
  `isdropoffavailable` int DEFAULT '0',
  `isflightinrequired` int DEFAULT '0',
  `minimumbookingday` int DEFAULT '0',
  `noticerequired_numberofdays` int DEFAULT '0',
  `quoteisvalid_numberofdays` int DEFAULT '0',
  `officeopeningtime` time DEFAULT NULL,
  `officeclosingtime` time DEFAULT NULL,
  `afterhourbookingaccepted` int DEFAULT '0',
  `afterhourfeeid` int DEFAULT '0',
  `unattendeddropoffaccepted` int DEFAULT '0',
  `unattendeddropofffeeid` int DEFAULT '0',
  `minimumage` int DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `api_name` varchar(100) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oc_api_officetimes`
--

DROP TABLE IF EXISTS `oc_api_officetimes`;
CREATE TABLE IF NOT EXISTS `oc_api_officetimes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `locationid` int DEFAULT NULL,
  `dayofweek` int DEFAULT NULL,
  `openingtime` time DEFAULT NULL,
  `closingtime` time DEFAULT NULL,
  `startpickup` time DEFAULT NULL,
  `endpickup` time DEFAULT NULL,
  `startdropoff` time DEFAULT NULL,
  `enddropoff` time DEFAULT NULL,
  `startdate` varchar(100) DEFAULT NULL,
  `enddate` varchar(100) DEFAULT NULL,
  `api_name` varchar(100) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
