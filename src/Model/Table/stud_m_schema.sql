# ************************************************************
# Sequel Pro SQL dump
# Version 4499
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: mysql.origamistructures.com (MySQL 5.6.25-log)
# Database: stud_m
# Generation Time: 2016-01-21 15:19:45 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table addresses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `addresses`;

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `member_id` int(11) NOT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `address3` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `primary` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;

INSERT INTO `addresses` (`id`, `created`, `modified`, `user_id`, `member_id`, `address1`, `address2`, `address3`, `city`, `state`, `zip`, `country`, `label`, `primary`)
VALUES
	(1,'2016-01-14 23:02:13','2016-01-14 23:02:13',NULL,11,'123 Main St.','','','Los Angeles','CA','99567','USA','main',NULL),
	(2,'2016-01-14 23:12:33','2016-01-14 23:12:33',NULL,12,'123 Main St.','','','Los Angeles','CA','99567','USA','main',NULL),
	(3,'2016-01-20 22:32:01','2016-01-20 22:46:30',NULL,13,'123 Main St.','Second Floor Suite 58','','Palo Alto','CA','94567','USA','main',NULL),
	(4,'2016-01-20 23:08:56','2016-01-20 23:08:56',NULL,5,'1 Hogarth Blvd.','','','Pine Grove','CA','95665','USA','main',NULL);

/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table artworks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `artworks`;

CREATE TABLE `artworks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `edition_count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table cake_d_c_users_phinxlog
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cake_d_c_users_phinxlog`;

CREATE TABLE `cake_d_c_users_phinxlog` (
  `version` bigint(20) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `cake_d_c_users_phinxlog` WRITE;
/*!40000 ALTER TABLE `cake_d_c_users_phinxlog` DISABLE KEYS */;

INSERT INTO `cake_d_c_users_phinxlog` (`version`, `start_time`, `end_time`)
VALUES
	(20150513201111,'2016-01-06 19:32:01','2016-01-06 19:32:02');

/*!40000 ALTER TABLE `cake_d_c_users_phinxlog` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table contacts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `contacts`;

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `member_id` int(11) NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `data` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;

INSERT INTO `contacts` (`id`, `created`, `modified`, `user_id`, `member_id`, `label`, `data`)
VALUES
	(1,'2016-01-14 23:12:33','2016-01-14 23:12:33',NULL,12,'email','office@onceler.com'),
	(2,'2016-01-14 23:12:33','2016-01-14 23:12:33',NULL,12,'phone','212.884.8246'),
	(3,'2016-01-20 22:32:01','2016-01-20 22:32:01',NULL,13,'email','foo@men.com'),
	(4,'2016-01-20 22:32:01','2016-01-20 22:32:01',NULL,13,'phone','999-867-4784'),
	(5,'2016-01-20 23:08:56','2016-01-20 23:38:22',NULL,5,'email','boss@davisfound.com'),
	(6,'2016-01-20 23:08:56','2016-01-20 23:08:56',NULL,5,'phone','');

/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table designs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `designs`;

CREATE TABLE `designs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text COMMENT 'The text/caption/writing for this Content/Image',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table dispositions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dispositions`;

CREATE TABLE `dispositions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `piece_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table editions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `editions`;

CREATE TABLE `editions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `type` varchar(127) DEFAULT 'Unique',
  `quantity` int(11) NOT NULL DEFAULT '1',
  `artwork_id` int(11) DEFAULT NULL,
  `series_id` int(11) DEFAULT NULL,
  `assigned_piece_count` int(11) DEFAULT '0',
  `format_count` int(11) DEFAULT '0',
  `fluid_piece_count` int(11) DEFAULT '0' COMMENT 'pieces with no dispositions can easily move to other formats',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table formats
# ------------------------------------------------------------

DROP TABLE IF EXISTS `formats`;

CREATE TABLE `formats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `range_flag` tinyint(4) DEFAULT NULL COMMENT 'boolean to indicate the use of range values',
  `range_start` int(11) DEFAULT NULL,
  `range_end` int(11) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `edition_id` int(11) DEFAULT NULL,
  `subscription_id` int(11) DEFAULT NULL,
  `assigned_piece_count` int(11) DEFAULT '0',
  `fluid_piece_count` int(11) DEFAULT '0' COMMENT 'pieces with no dispositions can easily move to other formats',
  `collected_piece_count` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;

INSERT INTO `groups` (`id`, `created`, `modified`, `user_id`, `member_id`, `active`)
VALUES
	(1,NULL,NULL,'008ab31c-124d-4e15-a4e1-45fccd7becac',5,1),
	(2,'2016-01-14 19:27:31','2016-01-14 19:27:31',NULL,9,1),
	(3,'2016-01-14 19:30:44','2016-01-14 19:30:44','008ab31c-124d-4e15-a4e1-45fccd7becac',10,1),
	(4,'2016-01-14 23:02:13','2016-01-14 23:02:13','008ab31c-124d-4e15-a4e1-45fccd7becac',11,1),
	(5,'2016-01-14 23:12:33','2016-01-14 23:12:33','008ab31c-124d-4e15-a4e1-45fccd7becac',12,1),
	(6,'2016-01-20 22:32:02','2016-01-20 22:32:02','008ab31c-124d-4e15-a4e1-45fccd7becac',13,1);

/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table groups_members
# ------------------------------------------------------------

DROP TABLE IF EXISTS `groups_members`;

CREATE TABLE `groups_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `group_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `division` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table images
# ------------------------------------------------------------

DROP TABLE IF EXISTS `images`;

CREATE TABLE `images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `image_file` varchar(255) DEFAULT '' COMMENT 'The image file name',
  `image_dir` varchar(255) DEFAULT NULL,
  `mimetype` varchar(40) DEFAULT NULL COMMENT 'From EXIF data',
  `filesize` bigint(20) DEFAULT NULL COMMENT 'From EXIF data',
  `width` mediumint(9) DEFAULT NULL COMMENT 'From EXIF data',
  `height` mediumint(9) DEFAULT NULL COMMENT 'From EXIF data',
  `title` varchar(255) DEFAULT '' COMMENT 'HTML image title attribute',
  `date` bigint(20) DEFAULT NULL COMMENT 'From EXIF data',
  `alt` varchar(255) DEFAULT NULL COMMENT 'HTML image alt attribute',
  `upload` int(16) DEFAULT NULL COMMENT 'Upload batch number',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='collection of images and supporting text';

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;

INSERT INTO `images` (`id`, `modified`, `created`, `user_id`, `image_file`, `image_dir`, `mimetype`, `filesize`, `width`, `height`, `title`, `date`, `alt`, `upload`)
VALUES
	(1,NULL,NULL,NULL,'DSC04016.JPG',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL),
	(2,NULL,NULL,NULL,'DSC04022.JPG',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL),
	(3,NULL,NULL,NULL,'DSC04039.JPG',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL),
	(4,NULL,NULL,NULL,'DSC04517.JPG',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL),
	(5,NULL,NULL,NULL,'IMG_0041_2.jpg',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL),
	(6,NULL,NULL,NULL,'IMG_0089_2.jpg',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL),
	(12,'2016-01-07 05:47:48','2016-01-07 05:47:48',NULL,'',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL),
	(13,'2016-01-07 05:48:06','2016-01-07 05:48:06',NULL,'',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL),
	(14,'2016-01-07 05:48:43','2016-01-07 05:48:43',NULL,'',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL),
	(15,'2016-01-07 06:13:30','2016-01-07 06:13:30',NULL,'',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL),
	(16,'2016-01-07 15:01:52','2016-01-07 15:01:52',NULL,'',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL),
	(17,'2016-01-07 15:03:20','2016-01-07 15:03:20',NULL,'',NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL);

/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table locations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `locations`;

CREATE TABLE `locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `member_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table members
# ------------------------------------------------------------

DROP TABLE IF EXISTS `members`;

CREATE TABLE `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;

INSERT INTO `members` (`id`, `created`, `modified`, `user_id`, `image_id`, `first_name`, `last_name`, `type`, `active`)
VALUES
	(1,'2015-12-04 06:33:58','2015-12-04 06:33:58','1',NULL,NULL,NULL,NULL,1),
	(2,'2016-01-14 00:22:51','2016-01-14 00:22:51','008ab31c-124d-4e15-a4e1-45fccd7becac',NULL,'Bill','Blevins','Person',1),
	(3,'2016-01-14 03:21:15','2016-01-14 03:21:15','008ab31c-124d-4e15-a4e1-45fccd7becac',NULL,'Tommy','Tune','Person',1),
	(4,'2016-01-14 03:34:45','2016-01-14 03:34:45','008ab31c-124d-4e15-a4e1-45fccd7becac',NULL,'Davy','Jones','Person',1),
	(5,'2016-01-14 03:40:27','2016-01-20 23:38:22','008ab31c-124d-4e15-a4e1-45fccd7becac',NULL,'Davis Foundation','Davis Foundation','Institution',1),
	(6,'2016-01-14 19:16:10','2016-01-14 19:16:10','008ab31c-124d-4e15-a4e1-45fccd7becac',NULL,'Findley Group','','Institution',1),
	(7,'2016-01-14 19:20:15','2016-01-14 19:20:15','008ab31c-124d-4e15-a4e1-45fccd7becac',NULL,'Thompkins Group','','Institution',1),
	(8,'2016-01-14 19:21:46','2016-01-14 19:21:46','008ab31c-124d-4e15-a4e1-45fccd7becac',NULL,'Donneley','','Institution',1),
	(9,'2016-01-14 19:27:31','2016-01-14 19:27:31','008ab31c-124d-4e15-a4e1-45fccd7becac',NULL,'Blumenfeld and Associates','','Institution',1),
	(10,'2016-01-14 19:30:44','2016-01-14 19:30:44','008ab31c-124d-4e15-a4e1-45fccd7becac',NULL,'Curly Media','','Institution',1),
	(11,'2016-01-14 23:02:13','2016-01-14 23:02:13','008ab31c-124d-4e15-a4e1-45fccd7becac',NULL,'Floyd Society','','Institution',1),
	(12,'2016-01-14 23:12:33','2016-01-14 23:12:33','008ab31c-124d-4e15-a4e1-45fccd7becac',NULL,'Onceler Manufacturing','','Institution',1),
	(13,'2016-01-20 22:32:01','2016-01-20 22:46:29','008ab31c-124d-4e15-a4e1-45fccd7becac',NULL,'Foo Men','Foo Men','Institution',1);

/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table menus
# ------------------------------------------------------------

DROP TABLE IF EXISTS `menus`;

CREATE TABLE `menus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table pieces
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pieces`;

CREATE TABLE `pieces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT '1',
  `made` tinyint(1) DEFAULT '0',
  `edition_id` int(11) DEFAULT NULL,
  `format_id` int(11) DEFAULT NULL,
  `disposition_count` int(11) DEFAULT '0',
  `collected` int(1) DEFAULT '0' COMMENT 'counter cache boolean',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table series
# ------------------------------------------------------------

DROP TABLE IF EXISTS `series`;

CREATE TABLE `series` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `series` WRITE;
/*!40000 ALTER TABLE `series` DISABLE KEYS */;

INSERT INTO `series` (`id`, `created`, `modified`, `user_id`, `title`, `description`)
VALUES
	(1,NULL,NULL,'1','One Poem Book',NULL),
	(2,NULL,NULL,'1','Conversation',NULL),
	(3,NULL,NULL,'1','Platonics',NULL),
	(4,NULL,NULL,'2','Aristitolian',NULL),
	(5,'2015-12-24 03:13:29','2015-12-24 03:13:29',NULL,NULL,NULL);

/*!40000 ALTER TABLE `series` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table social_accounts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `social_accounts`;

CREATE TABLE `social_accounts` (
  `id` char(36) NOT NULL,
  `user_id` varchar(36) NOT NULL,
  `provider` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `reference` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `description` text,
  `link` varchar(255) NOT NULL,
  `token` varchar(500) NOT NULL,
  `token_secret` varchar(500) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `data` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table subscriptions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `subscriptions`;

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `user_id` char(36) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `range_flag` tinyint(4) DEFAULT NULL,
  `range_start` int(11) DEFAULT NULL,
  `range_end` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` char(36) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `token_expires` datetime DEFAULT NULL,
  `api_token` varchar(255) DEFAULT NULL,
  `activation_date` datetime DEFAULT NULL,
  `tos_date` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `is_superuser` tinyint(1) NOT NULL DEFAULT '0',
  `role` varchar(255) DEFAULT 'user',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `token`, `token_expires`, `api_token`, `activation_date`, `tos_date`, `active`, `is_superuser`, `role`, `created`, `modified`)
VALUES
	('008ab31c-124d-4e15-a4e1-45fccd7becac','jason','jason@curlymedia.com','$2y$10$jekmBBtzjM7zzs6TBH6dnup.uNi1sU2JLtlyvbkacIxe6jm/xwuUS','Jason','Tempestini',NULL,NULL,NULL,'2016-01-06 21:07:14','2016-01-06 21:06:27',1,1,'user','2016-01-06 21:06:27','2016-01-06 21:07:14'),
	('1','',NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,'user','2016-01-07 16:13:14','2016-01-07 18:08:42'),
	('f22f9b46-345f-4c6f-9637-060ceacb21b2','don','ddrake@dreamingmind.com','$2y$10$1/eIptEk18zwp.QGIWPVr.VaqM66Bfhk7H7Vf3z6CN.IR3r9uMSLS','Don','Drake',NULL,NULL,NULL,'2016-01-07 22:29:42','2016-01-08 21:17:35',1,0,'user','2016-01-06 21:17:35','2016-01-09 06:34:02');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
