-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 23, 2012 at 12:34 AM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sis`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE IF NOT EXISTS `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `crn` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `number` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `title` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `m` tinyint(1) NOT NULL DEFAULT '0',
  `t` tinyint(1) NOT NULL DEFAULT '0',
  `w` tinyint(1) NOT NULL DEFAULT '0',
  `r` tinyint(1) NOT NULL DEFAULT '0',
  `f` tinyint(1) NOT NULL DEFAULT '0',
  `s` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2329 ;

-- --------------------------------------------------------

--
-- Table structure for table `courses_`
--

CREATE TABLE IF NOT EXISTS `courses_` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `crn` int(11) DEFAULT NULL,
  `subject` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `course` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `section` int(11) DEFAULT NULL,
  `title` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `begin_time_1` time DEFAULT NULL,
  `end_time_1` time DEFAULT NULL,
  `building_1` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `room_1` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `m_1` tinyint(1) DEFAULT NULL,
  `t_1` tinyint(1) DEFAULT NULL,
  `w_1` tinyint(1) DEFAULT NULL,
  `r_1` tinyint(1) DEFAULT NULL,
  `f_1` tinyint(1) DEFAULT NULL,
  `sat_1` tinyint(1) DEFAULT NULL,
  `sun_1` tinyint(1) DEFAULT NULL,
  `instructor_1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `begin_time_2` time DEFAULT NULL,
  `end_time_2` time DEFAULT NULL,
  `building_2` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `room_2` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `m_2` tinyint(1) DEFAULT NULL,
  `t_2` tinyint(1) DEFAULT NULL,
  `w_2` tinyint(1) DEFAULT NULL,
  `r_2` tinyint(1) DEFAULT NULL,
  `f_2` tinyint(1) DEFAULT NULL,
  `sat_2` tinyint(1) DEFAULT NULL,
  `sun_2` tinyint(1) DEFAULT NULL,
  `instructor_2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2356 ;

-- --------------------------------------------------------

--
-- Table structure for table `course_slots`
--

CREATE TABLE IF NOT EXISTS `course_slots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `building` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `room` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3929 ;

-- --------------------------------------------------------

--
-- Table structure for table `instructors`
--

CREATE TABLE IF NOT EXISTS `instructors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=656 ;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE IF NOT EXISTS `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=62 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
