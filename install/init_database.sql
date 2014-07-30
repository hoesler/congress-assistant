-- MySQL dump 10.11
--
-- Host: localhost    Database: bzdinf1
-- ------------------------------------------------------
-- Server version	5.0.67

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) collate utf8_bin NOT NULL default '0',
  `ip_address` varchar(16) collate utf8_bin NOT NULL default '0',
  `user_agent` varchar(150) collate utf8_bin NOT NULL,
  `last_activity` int(10) unsigned NOT NULL default '0',
  `user_data` text collate utf8_bin NOT NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `contributions`
--

DROP TABLE IF EXISTS `contributions`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `contributions` (
  `id` int(11) NOT NULL auto_increment,
  `participantId` int(11) NOT NULL,
  `symposiumId` int(11) NOT NULL,
  `title` text collate utf8_unicode_ci,
  `authors` text collate utf8_unicode_ci,
  `institute` text collate utf8_unicode_ci,
  `summary` text collate utf8_unicode_ci,
  `contributionKey` varchar(20) collate utf8_unicode_ci NOT NULL,
  `startTime` datetime default NULL,
  `room` enum('N0','N1','N2','N3','N4','N5','N6','N7','N9') collate utf8_unicode_ci default NULL,
  `type` enum('PLENARY_TALK','INVITED_TALK','ORAL_PRESENTATION','REGULAR_POSTER','ESSENCE_POSTER') collate utf8_unicode_ci NOT NULL,
  `endTime` datetime default NULL,
  `pdfReceived` tinyint(1) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `contributionKey` (`contributionKey`),
  UNIQUE KEY `participantId` (`participantId`),
  KEY `symposiumId` (`symposiumId`),
  CONSTRAINT `contributions_ibfk_2` FOREIGN KEY (`symposiumId`) REFERENCES `symposia` (`id`),
  CONSTRAINT `contributions_ibfk_3` FOREIGN KEY (`participantId`) REFERENCES `participants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1091 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(80) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lhc_slides`
--

DROP TABLE IF EXISTS `lhc_slides`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lhc_slides` (
  `id` int(11) NOT NULL auto_increment,
  `content` text collate utf8_unicode_ci NOT NULL,
  `title` varchar(80) collate utf8_unicode_ci NOT NULL default '',
  `startTime` time NOT NULL,
  `endTime` time NOT NULL,
  `days` varchar(255) collate utf8_unicode_ci NOT NULL default '[]',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=63 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lhc_log`
--

DROP TABLE IF EXISTS `lhc_log`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `lhc_log` (
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `message` text collate utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL auto_increment,
  `ip_address` varchar(40) collate utf8_bin NOT NULL,
  `login` varchar(50) collate utf8_bin NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `mail`
--

DROP TABLE IF EXISTS `mail`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mail` (
  `id` int(11) NOT NULL auto_increment,
  `subject` varchar(255) collate utf8_unicode_ci NOT NULL,
  `body` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `mail_participant_map`
--

DROP TABLE IF EXISTS `mail_participant_map`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `mail_participant_map` (
  `id` int(11) NOT NULL auto_increment,
  `participantId` int(11) NOT NULL,
  `mailId` int(11) NOT NULL,
  `status` enum('QUEUED','IN_PROCESS','SENT','ERROR') NOT NULL default 'QUEUED',
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `participantId_2` (`participantId`,`mailId`),
  KEY `participantId` (`participantId`),
  KEY `mailId` (`mailId`),
  CONSTRAINT `mail_participant_map_ibfk_1` FOREIGN KEY (`participantId`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mail_participant_map_ibfk_2` FOREIGN KEY (`participantId`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mail_participant_map_ibfk_3` FOREIGN KEY (`mailId`) REFERENCES `mail` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1918 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `participants`
--

DROP TABLE IF EXISTS `participants`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `participants` (
  `id` int(11) NOT NULL auto_increment,
  `firstName` varchar(80) collate utf8_unicode_ci NOT NULL default '',
  `lastName` varchar(80) collate utf8_unicode_ci NOT NULL default '',
  `organization` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `uuid` char(32) collate utf8_unicode_ci NOT NULL,
  `type` enum('PLENARY','INVITED','ORAL','REGULAR_POSTER','ESSENCE_POSTER','COMMITTEE','UNKNOWN') collate utf8_unicode_ci NOT NULL default 'UNKNOWN',
  `isSilverback` tinyint(1) NOT NULL default '0',
  `level` enum('SENIOR','STUDENT','COMMITTEE','UNKNOWN') collate utf8_unicode_ci NOT NULL default 'UNKNOWN',
  `email` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `department` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `title` varchar(10) collate utf8_unicode_ci NOT NULL default '',
  `silverback` enum('SELECTED','INVITED','NO_ANSWER','ANSWERED') collate utf8_unicode_ci default NULL,
  `hasCancelled` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uuid_2` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=11010 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `poster_visitor_map`
--

DROP TABLE IF EXISTS `poster_visitor_map`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `poster_visitor_map` (
  `id` int(11) NOT NULL auto_increment,
  `presenterId` int(11) NOT NULL,
  `visitorId` int(11) NOT NULL,
  `accepted` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `presenterId` (`presenterId`),
  KEY `visitorId` (`visitorId`),
  CONSTRAINT `seeMeAtMyPosterActivity_ibfk_1` FOREIGN KEY (`presenterId`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seeMeAtMyPosterActivity_ibfk_2` FOREIGN KEY (`visitorId`) REFERENCES `participants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=887 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `silverback_answers`
--

DROP TABLE IF EXISTS `silverback_answers`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `silverback_answers` (
  `id` int(11) NOT NULL auto_increment,
  `day` enum('ASSIGN_ME','MONDAY','TUESDAY','WEDNESDAY','NOT_IN','ASSIGNED_MONDAY','ASSIGNED_TUESDAY') NOT NULL,
  `timeOfAnswer` datetime NOT NULL,
  `participantId` int(11) NOT NULL,
  `maxStudents` tinyint(4) NOT NULL default '5',
  `restaurant` varchar(80) default NULL,
  PRIMARY KEY  (`id`),
  KEY `participantId` (`participantId`),
  CONSTRAINT `silverback_answers_ibfk_1` FOREIGN KEY (`participantId`) REFERENCES `participants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `silverback_student_map`
--

DROP TABLE IF EXISTS `silverback_student_map`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `silverback_student_map` (
  `id` int(11) NOT NULL auto_increment,
  `silverbackId` int(11) NOT NULL,
  `studentId` int(11) NOT NULL,
  `timeOfInsert` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `silverbackId` (`silverbackId`,`studentId`),
  KEY `studentId` (`studentId`),
  CONSTRAINT `silverback_student_map_ibfk_1` FOREIGN KEY (`silverbackId`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `silverback_student_map_ibfk_2` FOREIGN KEY (`studentId`) REFERENCES `participants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `symposia`
--

DROP TABLE IF EXISTS `symposia`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `symposia` (
  `id` int(11) NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `id_2` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `timetable`
--

DROP TABLE IF EXISTS `timetable`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `timetable` (
  `id` int(11) NOT NULL auto_increment,
  `startTime` datetime NOT NULL,
  `duration` int(11) NOT NULL,
  `room` enum('LECTURE_HALL_N11','LECTURE_HALL_N12') collate utf8_unicode_ci default NULL,
  `type` enum('WELCOME','PLENARY_TALK','GENERAL_SESSION','SYMPOSIA_SESSION','POSTER_PRESENTATION') collate utf8_unicode_ci NOT NULL,
  `info` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user_autologin`
--

DROP TABLE IF EXISTS `user_autologin`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user_autologin` (
  `key_id` char(32) collate utf8_bin NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  `user_agent` varchar(150) collate utf8_bin NOT NULL,
  `last_ip` varchar(40) collate utf8_bin NOT NULL,
  `last_login` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`key_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `country` varchar(20) collate utf8_bin default NULL,
  `website` varchar(255) collate utf8_bin default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(50) collate utf8_bin NOT NULL,
  `password` varchar(255) collate utf8_bin NOT NULL,
  `email` varchar(100) collate utf8_bin NOT NULL,
  `activated` tinyint(1) NOT NULL default '1',
  `banned` tinyint(1) NOT NULL default '0',
  `ban_reason` varchar(255) collate utf8_bin default NULL,
  `new_password_key` varchar(50) collate utf8_bin default NULL,
  `new_password_requested` datetime default NULL,
  `new_email` varchar(100) collate utf8_bin default NULL,
  `new_email_key` varchar(50) collate utf8_bin default NULL,
  `last_ip` varchar(40) collate utf8_bin NOT NULL,
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-02-19 15:24:44
