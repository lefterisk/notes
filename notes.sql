# ************************************************************
# Sequel Pro SQL dump
# Version 4529
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.10)
# Database: notes
# Generation Time: 2016-02-25 12:43:05 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table note
# ------------------------------------------------------------

DROP TABLE IF EXISTS `note`;

CREATE TABLE `note` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `note` WRITE;
/*!40000 ALTER TABLE `note` DISABLE KEYS */;

INSERT INTO `note` (`id`, `user`, `title`, `text`, `created`, `modified`)
VALUES
	(1,1,'asdasdasd','11111','2016-02-24 23:51:40','2016-02-25 10:19:51'),
	(2,1,'asdasdasd1','asdasdasd2','2016-02-25 00:01:26','2016-02-25 00:01:26'),
	(3,1,'asdasdasd3','asdasdasd4','2016-02-25 00:01:33','2016-02-25 00:01:33'),
	(4,1,'title 1','text 1','2016-02-25 12:07:01','2016-02-25 12:07:01'),
	(5,1,'title 2','text 2','2016-02-25 12:08:44','2016-02-25 12:08:44'),
	(6,1,'whatever','whatever','2016-02-25 12:09:29','2016-02-25 12:09:29'),
	(7,1,'asdasd 1','asd asd asd 2','2016-02-25 12:10:27','2016-02-25 12:42:03'),
	(8,1,'asdasd 2','asd asd asd ','2016-02-25 12:10:27','2016-02-25 12:10:27'),
	(9,1,'asdasd 3','asd asd asd ','2016-02-25 12:10:27','2016-02-25 12:10:27'),
	(10,1,'asdasd 4','asd asd asd ','2016-02-25 12:10:27','2016-02-25 12:10:27'),
	(11,1,'asdasd 5','asd asd asd ','2016-02-25 12:10:27','2016-02-25 12:10:27'),
	(12,1,'asdasd 6','asd asd asd ','2016-02-25 12:10:27','2016-02-25 12:10:27'),
	(13,1,'asdasd 7','asd asd asd ','2016-02-25 12:10:27','2016-02-25 12:10:27'),
	(14,1,'asdasd 8','asd asd asd ','2016-02-25 12:10:27','2016-02-25 12:10:27'),
	(15,1,'asdasd 9','asd asd asd ','2016-02-25 12:10:27','2016-02-25 12:10:27'),
	(16,1,'asdasd 10','asd asd asd ','2016-02-25 12:10:27','2016-02-25 12:10:27'),
	(17,1,'asdasd 11','asd asd asd ','2016-02-25 12:10:27','2016-02-25 12:10:27'),
	(18,1,'asdasd 12','asd asd asd ','2016-02-25 12:10:27','2016-02-25 12:10:27'),
	(19,1,'asdasd 13','asd asd asd ','2016-02-25 12:10:27','2016-02-25 12:10:27'),
	(20,1,'asdasd 14','asd asd asd ','2016-02-25 12:10:27','2016-02-25 12:10:27'),
	(21,1,'asdasd 15','asd asd asd ','2016-02-25 12:10:27','2016-02-25 12:10:27'),
	(22,1,'asdasd 16','asd asd asd ','2016-02-25 12:10:27','2016-02-25 12:10:27');

/*!40000 ALTER TABLE `note` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table session
# ------------------------------------------------------------

DROP TABLE IF EXISTS `session`;

CREATE TABLE `session` (
  `id` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;

INSERT INTO `session` (`id`, `name`, `modified`, `lifetime`, `data`)
VALUES
	('3b1itf08l8mrb2930ifvm8cu66','notes',1456403965,2500000,'__ZF|a:1:{s:20:\"_REQUEST_ACCESS_TIME\";d:1456403965.6322401;}user|C:23:\"Zend\\Stdlib\\ArrayObject\":405:{a:4:{s:7:\"storage\";a:1:{s:7:\"storage\";O:22:\"Application\\Model\\User\":6:{s:2:\"id\";i:1;s:5:\"image\";N;s:9:\"firstName\";s:8:\"Lefteris\";s:8:\"lastName\";s:8:\"Kokkonas\";s:5:\"email\";s:27:\"lefteris.kokkonas@gmail.com\";s:8:\"password\";N;}}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}'),
	('cb9dfgk1rsl60lenpdnjhdm6l2','notes',1456358542,2500000,'__ZF|a:1:{s:20:\"_REQUEST_ACCESS_TIME\";d:1456358542.427572;}user|C:23:\"Zend\\Stdlib\\ArrayObject\":405:{a:4:{s:7:\"storage\";a:1:{s:7:\"storage\";O:22:\"Application\\Model\\User\":6:{s:2:\"id\";i:1;s:5:\"image\";N;s:9:\"firstName\";s:8:\"Lefteris\";s:8:\"lastName\";s:8:\"Kokkonas\";s:5:\"email\";s:27:\"lefteris.kokkonas@gmail.com\";s:8:\"password\";N;}}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}'),
	('jte3kgsade0e49kcfk39okm5c4','notes',1456404151,2500000,'__ZF|a:1:{s:20:\"_REQUEST_ACCESS_TIME\";d:1456404151.4081111;}user|C:23:\"Zend\\Stdlib\\ArrayObject\":405:{a:4:{s:7:\"storage\";a:1:{s:7:\"storage\";O:22:\"Application\\Model\\User\":6:{s:2:\"id\";i:1;s:5:\"image\";N;s:9:\"firstName\";s:8:\"Lefteris\";s:8:\"lastName\";s:8:\"Kokkonas\";s:5:\"email\";s:27:\"lefteris.kokkonas@gmail.com\";s:8:\"password\";N;}}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}');

/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `image` int(11) DEFAULT NULL,
  `firstName` varchar(100) NOT NULL DEFAULT '',
  `lastName` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `login` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;

INSERT INTO `user` (`id`, `image`, `firstName`, `lastName`, `email`, `password`)
VALUES
	(1,NULL,'Lefteris','Kokkonas','lefteris.kokkonas@gmail.com','$2y$10$kznLGkn1/dZhA.ntK9FF0eUqrld3c3/1Mp02wn.6vOe/fXOxrtqjC');

/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
