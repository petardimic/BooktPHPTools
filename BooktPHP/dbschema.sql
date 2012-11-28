-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 26, 2012 at 01:10 PM
-- Server version: 5.1.63
-- PHP Version: 5.3.2-1ubuntu4.18

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `vrdemo`
--

-- --------------------------------------------------------

--
-- Table structure for table `Availability`
--

DROP TABLE IF EXISTS `Availability`;
CREATE TABLE IF NOT EXISTS `Availability` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SolutionID` int(11) NOT NULL,
  `PropertyID` int(11) NOT NULL,
  `AltID` varchar(128) DEFAULT NULL,
  `CheckIn` date NOT NULL,
  `CheckOut` date NOT NULL,
  `LastMod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `SOLID_BPID` (`SolutionID`,`PropertyID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Photos`
--

DROP TABLE IF EXISTS `Photos`;
CREATE TABLE IF NOT EXISTS `Photos` (
  `PhotoID` int(11) NOT NULL AUTO_INCREMENT,
  `SolutionID` int(11) NOT NULL,
  `BooktPropertyID` int(11) NOT NULL,
  `OriginalURL` varchar(256) DEFAULT NULL,
  `MediumURL` varchar(256) DEFAULT NULL,
  `ThumbnailURL` varchar(256) DEFAULT NULL,
  `Caption` varchar(256) DEFAULT NULL,
  `Order` int(11) DEFAULT NULL,
  `LastUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PhotoID`),
  KEY `BPID_ORD` (`BooktPropertyID`,`Order`),
  KEY `SOLID_BPID` (`SolutionID`,`BooktPropertyID`),
  KEY `BPID` (`BooktPropertyID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Properties`
--

DROP TABLE IF EXISTS `Properties`;
CREATE TABLE IF NOT EXISTS `Properties` (
  `PropertyID` int(11) NOT NULL AUTO_INCREMENT,
  `SolutionID` int(11) NOT NULL,
  `BooktPropertyID` int(11) NOT NULL,
  `AltID` varchar(20) DEFAULT NULL,
  `Headline` varchar(256) DEFAULT NULL,
  `Category` varchar(64) DEFAULT NULL,
  `Summary` varchar(1024) DEFAULT NULL,
  `Description` varchar(4096) DEFAULT NULL,
  `Beds` float DEFAULT NULL,
  `Baths` float DEFAULT NULL,
  `Sleeps` int(11) DEFAULT NULL,
  `Address1` varchar(128) DEFAULT NULL,
  `Address2` varchar(64) DEFAULT NULL,
  `City` varchar(64) DEFAULT NULL,
  `State` varchar(64) DEFAULT NULL,
  `PostalCode` varchar(24) DEFAULT NULL,
  `Metro` varchar(128) DEFAULT NULL,
  `Country` varchar(128) DEFAULT NULL,
  `Region` varchar(100) DEFAULT NULL,
  `Neighborhood` varchar(128) DEFAULT NULL,
  `Latitude` float DEFAULT NULL,
  `Longitude` float DEFAULT NULL,
  `AvgReview` double DEFAULT NULL,
  `NumReviews` int(11) DEFAULT NULL,
  `MetaDescrip` varchar(1024) DEFAULT NULL,
  `MetaKeywords` varchar(1024) DEFAULT NULL,
  `PageTitle` varchar(256) DEFAULT NULL,
  `LastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PropertyID`),
  KEY `BOOKT_PROP_ID_SOL_ID` (`BooktPropertyID`,`SolutionID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Rates`
--

DROP TABLE IF EXISTS `Rates`;
CREATE TABLE IF NOT EXISTS `Rates` (
  `RateID` int(11) NOT NULL AUTO_INCREMENT,
  `SolutionID` int(11) NOT NULL,
  `PropertyID` int(11) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `Daily` float DEFAULT NULL,
  `Weekly` float DEFAULT NULL,
  `Monthly` float DEFAULT NULL,
  `LastMod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RateID`),
  KEY `SOLID_BPID` (`SolutionID`,`PropertyID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Solutions`
--

DROP TABLE IF EXISTS `Solutions`;
CREATE TABLE IF NOT EXISTS `Solutions` (
  `SolutionID` int(11) NOT NULL,
  `SolutionName` varchar(256) NOT NULL,
  `APIKey` varchar(256) NOT NULL,
  `LastAvailSyncStart` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `LastAvailSyncEnd` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `LastRateSyncStart` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `LastRateSyncEnd` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `LastFullSyncStart` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `LastFullSyncEnd` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Running` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`SolutionID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
