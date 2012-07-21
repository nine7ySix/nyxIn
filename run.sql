-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 16, 2012 at 02:24 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `nyxIn_classes`
--

CREATE TABLE `nyxIn_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `permissions` text NOT NULL,
  `deleted_status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `nyxIn_classes`
--

INSERT INTO `nyxIn_classes` VALUES(1, 'Administrator', '{"1":"1","2":"1","3":"1","4":"1","5":"1","6":"1","7":"1","8":"1","9":"1","10":"1","11":"1","12":"1"}', 0);
INSERT INTO `nyxIn_classes` VALUES(2, 'Banned', '{"1":"0","2":"0","3":"0","4":"0","5":"0","6":"0","7":"0","8":"0","9":"0","10":"0","11":"0","12":"0"}', 0);
INSERT INTO `nyxIn_classes` VALUES(3, 'Viewer', '{"1":"1","2":"0","3":"0","4":"0","5":"0","6":"0","7":"0","8":"0","9":"0","10":"0","11":"0","12":"0"}', 0);
INSERT INTO `nyxIn_classes` VALUES(4, 'Uploader', '{"1":"1","2":"1","3":"0","4":"0","5":"0","6":"0","7":"0","8":"0","9":"0","10":"0","11":"0","12":"0"}', 0);
INSERT INTO `nyxIn_classes` VALUES(5, 'Moderator', '{"1":"1","2":"0","3":"0","4":"0","5":"1","6":"1","7":"1","8":"1","9":"0","10":"0","11":"0","12":"0"}', 0);

-- --------------------------------------------------------

--
-- Table structure for table `nyxIn_galleries`
--

CREATE TABLE `nyxIn_galleries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `order_int` int(11) NOT NULL,
  `thumbnail` varchar(100) NOT NULL,
  `name` varchar(32) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1 NOT NULL,
  `locked_status` tinyint(1) NOT NULL,
  `password` varchar(32) NOT NULL,
  `deleted_status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `nyxIn_galleries`
--


-- --------------------------------------------------------

--
-- Table structure for table `nyxIn_images`
--

CREATE TABLE `nyxIn_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gallery_id` int(11) NOT NULL,
  `moderation_status` tinyint(1) NOT NULL,
  `order_int` int(11) NOT NULL,
  `upload_timestamp` int(11) NOT NULL,
  `views` int(100) NOT NULL,
  `filename` varchar(32) NOT NULL,
  `safename` varchar(100) NOT NULL,
  `fileextension` varchar(32) NOT NULL,
  `filesize` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `description` text NOT NULL,
  `deleted_status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `nyxIn_images`
--


-- --------------------------------------------------------

--
-- Table structure for table `nyxIn_permissions`
--

CREATE TABLE `nyxIn_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `shorthand` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shorthand_name` (`shorthand`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `nyxIn_permissions`
--

INSERT INTO `nyxIn_permissions` VALUES(1, 'View Gallery During Maintenance', 'view_gallery_during_maintenace');
INSERT INTO `nyxIn_permissions` VALUES(2, 'Upload Images', 'upload');
INSERT INTO `nyxIn_permissions` VALUES(3, 'Manage Galleries', 'galleries_management');
INSERT INTO `nyxIn_permissions` VALUES(4, 'Customize Galleries', 'gallery_customization');
INSERT INTO `nyxIn_permissions` VALUES(5, 'Organize Galleries', 'gallery_organization');
INSERT INTO `nyxIn_permissions` VALUES(6, 'Move Images', 'move_images');
INSERT INTO `nyxIn_permissions` VALUES(7, 'Delete Images', 'delete_images');
INSERT INTO `nyxIn_permissions` VALUES(8, 'Moderate Images', 'moderate_images');
INSERT INTO `nyxIn_permissions` VALUES(9, 'Manage Staff Classes', 'manage_staff_classes');
INSERT INTO `nyxIn_permissions` VALUES(10, 'Manage Staff', 'manage_staff');
INSERT INTO `nyxIn_permissions` VALUES(11, 'Preferences', 'preferences');
INSERT INTO `nyxIn_permissions` VALUES(12, 'Reset nyxIn', 'reset');

-- --------------------------------------------------------

--
-- Table structure for table `nyxIn_preferences`
--

CREATE TABLE `nyxIn_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `preference` varchar(32) NOT NULL,
  `shorthand` varchar(32) NOT NULL,
  `description` text NOT NULL,
  `accepted_values` text NOT NULL,
  `value` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `nyxIn_preferences`
--

INSERT INTO `nyxIn_preferences` VALUES(1, 'Timestamp', 'timestamp', 'Unix timestamp of nyxIn installation.', 'Unix Timestamp', '1342162626');
INSERT INTO `nyxIn_preferences` VALUES(2, 'Maintenance Mode', 'maintenance_mode', 'Activate or Deacivate Maintenence Mode. During maintenance mode, only logged-in staff can browse the gallery.', '0 or 1', '1');
INSERT INTO `nyxIn_preferences` VALUES(3, 'Number of Columns', 'cols', 'Number of columns of images and sub-galleries displayed.', 'Any integer', '5');
INSERT INTO `nyxIn_preferences` VALUES(4, 'Moderated Image Only', 'display_moderated_only', 'Only staff-moderated images are displayed in the gallery.', '0 or 1', '1');
INSERT INTO `nyxIn_preferences` VALUES(5, 'Thumbnail Length', 'thumbnail_length', 'The length in pixels of the square thumbnail image produced by nyxIn. Please refrain from changing this unless nyxIn has been image/gallery reset or if this is a fresh copy of nyxIn.', 'Any integer.', '450');

-- --------------------------------------------------------

--
-- Table structure for table `nyxIn_staff`
--

CREATE TABLE `nyxIn_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password_hash` varchar(40) NOT NULL,
  `deleted_status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `nyxIn_staff`
--

INSERT INTO `nyxIn_staff` VALUES(1, 1, 'Admin', '516b9783fca517eecbd1d064da2d165310b19759', 0);
