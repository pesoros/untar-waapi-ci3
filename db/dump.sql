# ************************************************************
# Sequel Ace SQL dump
# Version 20059
#
# https://sequel-ace.com/
# https://github.com/Sequel-Ace/Sequel-Ace
#
# Host: 127.0.0.1 (MySQL 5.7.41)
# Database: ci_app
# Generation Time: 2023-11-21 10:48:11 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE='NO_AUTO_VALUE_ON_ZERO', SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table wa_kirim
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wa_kirim`;

CREATE TABLE `wa_kirim` (
  `recid` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `flag_id` varchar(32) DEFAULT NULL,
  `nim` varchar(9) DEFAULT NULL,
  `nama_template` varchar(50) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `isi_variabel` varchar(200) DEFAULT NULL,
  `kirim_at` datetime DEFAULT NULL,
  `no_sender` varchar(15) DEFAULT NULL,
  `message_id` varchar(50) DEFAULT NULL,
  `status_code` varchar(5) DEFAULT NULL,
  `error` varchar(50) DEFAULT NULL,
  `message` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`recid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `wa_kirim` WRITE;
/*!40000 ALTER TABLE `wa_kirim` DISABLE KEYS */;

INSERT INTO `wa_kirim` (`recid`, `flag_id`, `nim`, `nama_template`, `no_hp`, `isi_variabel`, `kirim_at`, `no_sender`, `message_id`, `status_code`, `error`, `message`)
VALUES
	(1,'studentholiday','123123','admisi_lulus','081288855773','1|2|3|4|5',NULL,'phone_a','wamid.HBgNNjI4MTI4ODg1NTc3MxUCABEYEjFBOUU2ODk0MDJE',NULL,NULL,NULL);

/*!40000 ALTER TABLE `wa_kirim` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wa_list_nomor
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wa_list_nomor`;

CREATE TABLE `wa_list_nomor` (
  `recid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nomor_hp` varchar(15) DEFAULT NULL,
  `nama_nomor` varchar(50) DEFAULT NULL,
  `token` longtext,
  PRIMARY KEY (`recid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `wa_list_nomor` WRITE;
/*!40000 ALTER TABLE `wa_list_nomor` DISABLE KEYS */;

INSERT INTO `wa_list_nomor` (`recid`, `nomor_hp`, `nama_nomor`, `token`)
VALUES
	(1,'6281285581611','phone_a','eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ3YVBob25lSWQiOiI0MDM1IiwicGhvbmVOdW1iZXIiOiI4MTI4NTU4MTYxMSIsInJhbmRvbSI6NTY3NCwiaWF0IjoxNzAwNTUwMzk1fQ.v2VzP1_8I_oDIp0XBTiDZaZLZT4-Fsh-8tE5XA1CWaQ');

/*!40000 ALTER TABLE `wa_list_nomor` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wa_list_nomor_auth
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wa_list_nomor_auth`;

CREATE TABLE `wa_list_nomor_auth` (
  `recid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nomor_recid` bigint(20) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`recid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `wa_list_nomor_auth` WRITE;
/*!40000 ALTER TABLE `wa_list_nomor_auth` DISABLE KEYS */;

INSERT INTO `wa_list_nomor_auth` (`recid`, `nomor_recid`, `username`, `password`)
VALUES
	(1,1,'untar-791d7b98','9wtSrUMvLExdsHhG');

/*!40000 ALTER TABLE `wa_list_nomor_auth` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wa_otp
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wa_otp`;

CREATE TABLE `wa_otp` (
  `recid` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `no_hp` varchar(15) DEFAULT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `jenis` char(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `expired_at` datetime DEFAULT NULL,
  `status_otp` char(1) DEFAULT NULL,
  PRIMARY KEY (`recid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `wa_otp` WRITE;
/*!40000 ALTER TABLE `wa_otp` DISABLE KEYS */;

INSERT INTO `wa_otp` (`recid`, `no_hp`, `otp`, `jenis`, `created_at`, `expired_at`, `status_otp`)
VALUES
	(1,'6281288855773','881026','0','2023-11-21 14:13:24','2023-11-21 14:15:24','0');

/*!40000 ALTER TABLE `wa_otp` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
