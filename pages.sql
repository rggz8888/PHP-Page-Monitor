-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Jeu 28 Novembre 2013 à 23:16
-- Version du serveur: 5.5.31
-- Version de PHP: 5.4.4-14+deb7u5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `monitor`
--

-- --------------------------------------------------------

--
-- Structure de la table `pages`
--
CREATE TABLE IF NOT EXISTS `pages` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_url` tinytext NOT NULL,
  `p_date` datetime NOT NULL,
  `p_sms` int(1) NOT NULL,
  `p_mail` int(1) NOT NULL,
  `p_often` int(1) NOT NULL,
  `p_regularity` int(11) NOT NULL,
  `p_lasthash` varchar(32) NOT NULL,
  `p_completed` int(1) NOT NULL,
  `p_notification_tpl` tinytext NOT NULL,
  `p_lastcheck` datetime NOT NULL,
  PRIMARY KEY (`p_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
