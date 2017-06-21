-- MySQL dump 10.15  Distrib 10.0.29-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: localhost
-- ------------------------------------------------------
-- Server version	10.0.29-MariaDB-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `acl_actions`
--

DROP TABLE IF EXISTS `acl_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_actions` (
  `action_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `controller_id` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `descript` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`action_id`),
  KEY `idx-controller_id` (`controller_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_actions`
--

LOCK TABLES `acl_actions` WRITE;
/*!40000 ALTER TABLE `acl_actions` DISABLE KEYS */;
INSERT INTO `acl_actions` VALUES (1,1,'*','全部功能','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,2,'*','全部功能','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `acl_actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_controllers`
--

DROP TABLE IF EXISTS `acl_controllers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_controllers` (
  `controller_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `descript` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`controller_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_controllers`
--

LOCK TABLES `acl_controllers` WRITE;
/*!40000 ALTER TABLE `acl_controllers` DISABLE KEYS */;
INSERT INTO `acl_controllers` VALUES (1,'Panel','面版','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'Message','訊息','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `acl_controllers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_grants`
--

DROP TABLE IF EXISTS `acl_grants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_grants` (
  `grant_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` char(15) NOT NULL,
  `controller_id` int(10) unsigned NOT NULL,
  `action_id` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`grant_id`),
  KEY `idx-role_id` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_grants`
--

LOCK TABLES `acl_grants` WRITE;
/*!40000 ALTER TABLE `acl_grants` DISABLE KEYS */;
INSERT INTO `acl_grants` VALUES (1,'member',1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'member',2,2,'0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `acl_grants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_roles`
--

DROP TABLE IF EXISTS `acl_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_roles` (
  `role_id` char(15) NOT NULL,
  `name` varchar(64) NOT NULL,
  `admin` char(1) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_roles`
--

LOCK TABLES `acl_roles` WRITE;
/*!40000 ALTER TABLE `acl_roles` DISABLE KEYS */;
INSERT INTO `acl_roles` VALUES ('member','會員','N','0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `acl_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `smt` double DEFAULT NULL,
  `message` varchar(512) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
INSERT INTO `message` VALUES (1,1497763320.3702,'張尚仁是帥哥','2017-06-18 05:22:00','0000-00-00 00:00:00'),(2,1497763362.4469,'張尚仁是帥哥','2017-06-18 05:22:42','0000-00-00 00:00:00'),(3,1497763369.0062,'張尚仁是帥哥','2017-06-18 05:22:49','0000-00-00 00:00:00'),(4,1497763371.6286,'張尚仁是帥哥','2017-06-18 05:22:51','0000-00-00 00:00:00'),(5,1497763382.9654,'張尚仁是帥哥','2017-06-18 05:23:02','0000-00-00 00:00:00'),(6,1497763608.7478,'張尚仁是帥哥','2017-06-18 05:26:48','0000-00-00 00:00:00'),(7,1497763735.3968,'張尚仁是帥哥','2017-06-18 05:28:55','0000-00-00 00:00:00'),(8,1497764067.4613,'張尚仁是帥哥','2017-06-18 05:34:27','0000-00-00 00:00:00'),(9,1497764091.2516,'張尚仁是帥哥','2017-06-18 05:34:51','0000-00-00 00:00:00'),(10,1497764094.2957,'張尚仁是帥哥','2017-06-18 05:34:54','0000-00-00 00:00:00'),(11,1497764096.5179,'張尚仁是帥哥','2017-06-18 05:34:56','0000-00-00 00:00:00'),(12,1497764151.7397,'張尚仁是帥哥','2017-06-18 05:35:51','2017-06-18 05:35:51'),(13,1497764166.3508,'張尚仁是帥哥','2017-06-18 05:36:06','2017-06-18 05:36:06'),(14,1497764173.1134,'張尚仁是帥哥','2017-06-18 05:36:13','2017-06-18 05:36:13'),(15,1497764264.4864,'翁慈韓是美女','2017-06-18 05:37:44','2017-06-18 05:37:44'),(16,1497764270.513,'翁慈韓是美女','2017-06-18 05:37:50','2017-06-18 05:37:50'),(17,1497764681.1321,'大家好','2017-06-18 05:44:41','2017-06-18 05:44:41'),(18,1497764721.6307,'123','2017-06-18 05:45:21','2017-06-18 05:45:21'),(19,1497764782.6257,'234','2017-06-18 05:46:22','2017-06-18 05:46:22'),(20,1497764831.6819,'123','2017-06-18 05:47:11','2017-06-18 05:47:11'),(21,1497764877.7242,'123','2017-06-18 05:47:57','2017-06-18 05:47:57'),(22,1497764885.5036,'123','2017-06-18 05:48:05','2017-06-18 05:48:05'),(23,1497765013.9797,'123','2017-06-18 05:50:13','2017-06-18 05:50:13'),(24,1497765123.1672,'123','2017-06-18 05:52:03','2017-06-18 05:52:03'),(25,1497765127.3313,'456','2017-06-18 05:52:07','2017-06-18 05:52:07'),(26,1497765269.6022,'1123','2017-06-18 05:54:29','2017-06-18 05:54:29'),(27,1497765282.6845,'張尚仁是師哥','2017-06-18 05:54:42','2017-06-18 05:54:42'),(28,1497765320.9333,'大家好我是小狗','2017-06-18 05:55:20','2017-06-18 05:55:20'),(29,1497765349.1484,'王八蛋','2017-06-18 05:55:49','2017-06-18 05:55:49'),(30,1497765370.3217,'我是天才','2017-06-18 05:56:10','2017-06-18 05:56:10'),(31,1497765378.5392,'我是天才','2017-06-18 05:56:18','2017-06-18 05:56:18'),(32,1497765387.6801,'我是天才','2017-06-18 05:56:27','2017-06-18 05:56:27'),(33,1497765421.3207,'123','2017-06-18 05:57:01','2017-06-18 05:57:01'),(34,1497765442.5153,'suck my dick','2017-06-18 05:57:22','2017-06-18 05:57:22'),(35,1497766309.2906,'1234','2017-06-18 06:11:49','2017-06-18 06:11:49'),(36,1497766330.166,'5 5 6 8 8','2017-06-18 06:12:10','2017-06-18 06:12:10'),(37,1497766891.0825,'翁慈韓是美女','2017-06-18 06:21:31','2017-06-18 06:21:31'),(38,1497766997.6454,'大家好','2017-06-18 06:23:17','2017-06-18 06:23:17'),(39,1497767008.4765,'怎麼這麼慢','2017-06-18 06:23:28','2017-06-18 06:23:28'),(40,1497767019.3885,'幹你娘','2017-06-18 06:23:39','2017-06-18 06:23:39'),(41,1497767051.0966,'hi','2017-06-18 06:24:11','2017-06-18 06:24:11'),(42,1497767083.2881,'你好','2017-06-18 06:24:43','2017-06-18 06:24:43'),(43,1497767104.5494,'你好世界','2017-06-18 06:25:04','2017-06-18 06:25:04'),(44,1497767146.256,'123','2017-06-18 06:25:46','2017-06-18 06:25:46'),(45,1497767153.0246,'1234','2017-06-18 06:25:53','2017-06-18 06:25:53'),(46,1497767169.9504,'為什麼要辦台灣之星','2017-06-18 06:26:09','2017-06-18 06:26:09'),(47,1497767180.7785,'收訊好嗎','2017-06-18 06:26:20','2017-06-18 06:26:20'),(48,1497767201.7561,'幹你老師','2017-06-18 06:26:41','2017-06-18 06:26:41'),(49,1497767202.038,'幹你老師','2017-06-18 06:26:42','2017-06-18 06:26:42'),(50,1497767232.439,'幹你好溼','2017-06-18 06:27:12','2017-06-18 06:27:12'),(51,1497767253.5796,'幹你老師','2017-06-18 06:27:33','2017-06-18 06:27:33'),(52,1497767295.8534,'hi','2017-06-18 06:28:15','2017-06-18 06:28:15'),(53,1497767316.8489,'為什麼變這麼慢','2017-06-18 06:28:36','2017-06-18 06:28:36'),(54,1497767358.1183,'123','2017-06-18 06:29:18','2017-06-18 06:29:18'),(55,1497767410.5698,'654','2017-06-18 06:30:10','2017-06-18 06:30:10'),(56,1497767428.1132,'張尚仁是帥哥','2017-06-18 06:30:28','2017-06-18 06:30:28'),(57,1497767433.1306,'張尚仁是帥哥','2017-06-18 06:30:33','2017-06-18 06:30:33'),(58,1497767437.9367,'張尚仁是帥哥','2017-06-18 06:30:37','2017-06-18 06:30:37'),(59,1497767441.0488,'張尚仁是帥哥','2017-06-18 06:30:41','2017-06-18 06:30:41'),(60,1497767444.0586,'張尚仁是帥哥','2017-06-18 06:30:44','2017-06-18 06:30:44'),(61,1497767446.7579,'張尚仁是帥哥','2017-06-18 06:30:46','2017-06-18 06:30:46'),(62,1497767448.8894,'張尚仁是帥哥','2017-06-18 06:30:48','2017-06-18 06:30:48'),(63,1497767451.3816,'張尚仁是帥哥','2017-06-18 06:30:51','2017-06-18 06:30:51'),(64,1497767466.5016,'老婆我肚子餓了','2017-06-18 06:31:06','2017-06-18 06:31:06'),(65,1497767477.9079,'對阿','2017-06-18 06:31:17','2017-06-18 06:31:17'),(66,1497767490.2827,'我不知道耶','2017-06-18 06:31:30','2017-06-18 06:31:30'),(67,1497767503.3149,'你公司附近吃嗎','2017-06-18 06:31:43','2017-06-18 06:31:43'),(68,1497767515.6906,'好阿','2017-06-18 06:31:55','2017-06-18 06:31:55'),(69,1497767529.1532,'我不寫了','2017-06-18 06:32:09','2017-06-18 06:32:09'),(70,1497767539.3414,'我已經完成了超強的功能','2017-06-18 06:32:19','2017-06-18 06:32:19'),(71,1497767549.6058,'帥阿','2017-06-18 06:32:29','2017-06-18 06:32:29'),(72,1497767560.0867,'你幾點要去','2017-06-18 06:32:40','2017-06-18 06:32:40'),(73,1497767574.8226,'不然 45 分','2017-06-18 06:32:54','2017-06-18 06:32:54'),(74,1497767586.5543,'okay','2017-06-18 06:33:06','2017-06-18 06:33:06'),(75,1497767594.8993,'ok','2017-06-18 06:33:14','2017-06-18 06:33:14'),(76,1497767612.3685,'我的意思是說好','2017-06-18 06:33:32','2017-06-18 06:33:32'),(77,1497767620.7517,'對','2017-06-18 06:33:40','2017-06-18 06:33:40'),(78,1497767628.9193,'好廢','2017-06-18 06:33:48','2017-06-18 06:33:48'),(79,1497767801.4278,'fuck','2017-06-18 06:36:41','2017-06-18 06:36:41'),(80,1497767822.5616,'your pussy is so wet','2017-06-18 06:37:02','2017-06-18 06:37:02'),(81,1497767837.3379,'suck my dick baby','2017-06-18 06:37:17','2017-06-18 06:37:17'),(82,1497767857.9086,'you mother fucker','2017-06-18 06:37:37','2017-06-18 06:37:37'),(83,1497767871.1964,'fuck you','2017-06-18 06:37:51','2017-06-18 06:37:51'),(84,1497767913.9536,'https:','2017-06-18 06:38:33','2017-06-18 06:38:33'),(85,1497768156.8619,'好聽','2017-06-18 06:42:36','2017-06-18 06:42:36'),(86,1497768463.2763,'    ラブ・ストーリーは突然に - 小田和正    伍佰 & China Blue - 釘子花 Ding Zi Hua（Official MV 官方完整版）    薛之謙【演員】官方完整版 MV','2017-06-18 06:47:43','2017-06-18 06:47:43'),(87,1497768480.2565,'    ラブ・ストーリーは突然に - 小田和正    伍佰 & China Blue - 釘子花 Ding Zi Hua（Official MV 官方完整版）    薛之謙【演員】官方完整版 MV','2017-06-18 06:48:00','2017-06-18 06:48:00'),(88,1497769408.9147,'幹你娘','2017-06-18 07:03:28','2017-06-18 07:03:28'),(89,1497769439.6453,'fuck u','2017-06-18 07:03:59','2017-06-18 07:03:59'),(90,1497769555.908,'電腦好慢 ','2017-06-18 07:05:55','2017-06-18 07:05:55'),(91,1497769626.3533,'快點送訊息阿','2017-06-18 07:07:06','2017-06-18 07:07:06'),(92,1497769653.7907,'你媽媽屁股大','2017-06-18 07:07:33','2017-06-18 07:07:33'),(93,1497786506.6305,'123','2017-06-18 11:48:26','2017-06-18 11:48:26'),(94,1497786515.071,'123','2017-06-18 11:48:35','2017-06-18 11:48:35'),(95,1497786531.5508,'123','2017-06-18 11:48:51','2017-06-18 11:48:51'),(96,1497786533.2618,'123','2017-06-18 11:48:53','2017-06-18 11:48:53'),(97,1497786630.3161,'張尚仁好帥','2017-06-18 11:50:30','2017-06-18 11:50:30');
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `play_queue`
--

DROP TABLE IF EXISTS `play_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `play_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `order_user_id` int(11) NOT NULL,
  `url` varchar(256) NOT NULL,
  `info_result_code` tinyint(3) DEFAULT NULL,
  `video_id` varchar(64) NOT NULL,
  `title` varchar(256) NOT NULL,
  `comment` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `sort_no` double unsigned NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `idx-user_id-sort_no-status` (`user_id`,`sort_no`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `play_queue`
--

LOCK TABLES `play_queue` WRITE;
/*!40000 ALTER TABLE `play_queue` DISABLE KEYS */;
INSERT INTO `play_queue` VALUES (1,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497704639.6188,'2017-06-17 13:04:01','2017-06-17 13:03:59'),(2,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497704726.4908,'2017-06-17 13:08:08','2017-06-17 13:05:26'),(3,2,4,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',0,1497704825.4432,'2017-06-17 13:07:05','2017-06-17 13:07:05'),(4,4,4,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497704837.1606,'2017-06-17 13:07:24','2017-06-17 13:07:17'),(5,3,3,'https://www.youtube.com/watch?v=ybfWYpYhTQQ',0,'ybfWYpYhTQQ','盧廣仲 Crowd Lu 【魚仔】 Official Music Video （花甲男孩轉大人主題曲）','',1,1497707135.5117,'2017-06-17 13:45:35','2017-06-17 13:45:35'),(6,3,3,'https://www.youtube.com/watch?v=XKuL5xaKZHM',0,'XKuL5xaKZHM','薛之謙【演員】官方完整版 MV','',1,1497707404.4682,'2017-06-17 13:52:21','2017-06-17 13:50:04'),(7,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,0,'2017-06-17 15:41:21','2017-06-17 15:40:38'),(8,3,3,'https://www.youtube.com/watch?v=ZFo8-JqzSCM',-1,'ZFo8-JqzSCM','Chuck Berry - Johnny B. Goode','',1,1497714135.651,'2017-06-17 15:43:15','2017-06-17 15:41:08'),(9,3,3,'https://www.youtube.com/watch?v=ZFo8-JqzSCM',-1,'ZFo8-JqzSCM','Chuck Berry - Johnny B. Goode','',1,0,'2017-06-17 15:44:22','2017-06-17 15:42:15'),(10,3,3,'https://www.youtube.com/watch?v=ZFo8-JqzSCM',-1,'ZFo8-JqzSCM','Chuck Berry - Johnny B. Goode','',1,1497714681.6102,'2017-06-17 15:51:24','2017-06-17 15:51:21'),(11,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497714695.596,'2017-06-17 16:22:04','2017-06-17 15:51:35'),(12,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497716850.4965,'2017-06-17 16:27:39','2017-06-17 16:27:30'),(13,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497716879.6478,'2017-06-17 16:30:50','2017-06-17 16:27:59'),(14,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497716967.2863,'2017-06-17 16:34:15','2017-06-17 16:29:27'),(15,3,3,'https://www.youtube.com/watch?v=ZFo8-JqzSCM',-1,'ZFo8-JqzSCM','Chuck Berry - Johnny B. Goode','',1,1497717071.0626,'2017-06-17 16:38:38','2017-06-17 16:31:11'),(16,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497717093.1574,'2017-06-17 16:41:46','2017-06-17 16:31:33'),(17,3,3,'https://www.youtube.com/watch?v=ZFo8-JqzSCM',-1,'ZFo8-JqzSCM','Chuck Berry - Johnny B. Goode','',1,1497717093.1573,'2017-06-17 16:38:59','2017-06-17 16:31:44'),(18,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497717071.0625,'2017-06-17 16:38:24','2017-06-17 16:34:27'),(19,3,3,'https://www.youtube.com/watch?v=ZFo8-JqzSCM',-1,'ZFo8-JqzSCM','Chuck Berry - Johnny B. Goode','',1,1497717549.3122,'2017-06-17 16:41:51','2017-06-17 16:39:09'),(20,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497717738.7864,'2017-06-17 16:48:07','2017-06-17 16:42:18'),(21,3,3,'https://www.youtube.com/watch?v=ZFo8-JqzSCM',-1,'ZFo8-JqzSCM','Chuck Berry - Johnny B. Goode','',1,1497717738.7863,'2017-06-17 16:44:37','2017-06-17 16:42:36'),(22,3,3,'https://www.youtube.com/watch?v=ZFo8-JqzSCM',-1,'ZFo8-JqzSCM','Chuck Berry - Johnny B. Goode','',1,1497717738.7863,'2017-06-17 16:47:22','2017-06-17 16:45:12'),(23,3,3,'https://www.youtube.com/watch?v=ZFo8-JqzSCM',-1,'ZFo8-JqzSCM','Chuck Berry - Johnny B. Goode','',1,1497718097.4638,'2017-06-17 16:52:33','2017-06-17 16:48:17'),(24,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497718097.4637,'2017-06-17 16:52:20','2017-06-17 16:48:58'),(25,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497718561.0293,'2017-06-17 16:56:05','2017-06-17 16:56:01'),(26,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497718562.8013,'2017-06-17 16:56:10','2017-06-17 16:56:02'),(27,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497718606.3473,'2017-06-17 19:09:02','2017-06-17 16:56:46'),(28,3,3,'https://www.youtube.com/watch?v=ZFo8-JqzSCM',-1,'ZFo8-JqzSCM','Chuck Berry - Johnny B. Goode','',1,1497718606.3532,'2017-06-17 17:00:20','2017-06-17 16:56:53'),(29,3,3,'https://www.youtube.com/watch?v=ZFo8-JqzSCM',-1,'ZFo8-JqzSCM','Chuck Berry - Johnny B. Goode','',1,1497718606.3476,'2017-06-17 19:11:36','2017-06-17 17:01:02'),(30,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497718606.3475,'2017-06-17 19:11:05','2017-06-17 17:01:20'),(31,3,3,'https://www.youtube.com/watch?v=ZFo8-JqzSCM',-1,'ZFo8-JqzSCM','Chuck Berry - Johnny B. Goode','',1,1497718606.3474,'2017-06-17 19:10:22','2017-06-17 17:01:51'),(32,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497718606.3529,'2017-06-17 17:02:20','2017-06-17 17:02:01'),(33,3,3,'https://www.youtube.com/watch?v=ZFo8-JqzSCM',-1,'ZFo8-JqzSCM','Chuck Berry - Johnny B. Goode','',1,1497718606.3472,'2017-06-17 17:10:34','2017-06-17 17:03:21'),(34,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497718606.3471,'2017-06-17 17:06:28','2017-06-17 17:03:34'),(35,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497760336.9724,'2017-06-18 04:32:17','2017-06-18 04:32:16'),(36,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497763474.8826,'2017-06-18 05:24:36','2017-06-18 05:24:34'),(37,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497767923.3563,'2017-06-18 06:38:43','2017-06-18 06:38:43'),(38,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497767956.1379,'2017-06-18 06:39:19','2017-06-18 06:39:16'),(39,3,3,'https://www.youtube.com/watch?v=XKuL5xaKZHM',0,'XKuL5xaKZHM','薛之謙【演員】官方完整版 MV','',1,1497767994.8765,'2017-06-18 07:03:47','2017-06-18 06:39:54'),(40,3,3,'https://www.youtube.com/watch?v=VwJruL9nBwQ',0,'VwJruL9nBwQ','ラブ・ストーリーは突然に - 小田和正','',1,1497767994.8762,'2017-06-18 06:48:26','2017-06-18 06:40:47'),(41,3,3,'https://www.youtube.com/watch?v=h1LcDAN7d3w',0,'h1LcDAN7d3w','伍佰&China Blue – 熱淚暗班車 Re Lei An Ban Che (Official Video 官方MV)','',1,1497767994.8763,'2017-06-18 06:43:26','2017-06-18 06:41:59'),(42,3,3,'https://www.youtube.com/watch?v=3MHVNxd130E',0,'3MHVNxd130E','伍佰 & China Blue - 釘子花 Ding Zi Hua（Official MV 官方完整版）','',1,1497767994.8763,'2017-06-18 06:54:33','2017-06-18 06:44:06'),(43,3,3,'https://www.youtube.com/watch?v=ybfWYpYhTQQ',0,'ybfWYpYhTQQ','盧廣仲 Crowd Lu 【魚仔】 Official Music Video （花甲男孩轉大人主題曲）','',1,1497768513.1888,'2017-06-18 07:06:55','2017-06-18 06:48:33'),(44,7,7,'https://www.youtube.com/watch?v=VwJruL9nBwQ',0,'VwJruL9nBwQ','ラブ・ストーリーは突然に - 小田和正','',1,1497768656.7098,'2017-06-18 06:58:02','2017-06-18 06:50:56'),(45,7,7,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497768777.5685,'2017-06-18 07:03:03','2017-06-18 06:52:57'),(46,7,7,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',0,1497768779.7638,'2017-06-18 06:57:19','2017-06-18 06:52:59'),(47,7,7,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',0,1497768781.6449,'2017-06-18 06:57:19','2017-06-18 06:53:01'),(48,7,7,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',0,1497768783.1379,'2017-06-18 06:57:19','2017-06-18 06:53:03'),(49,8,8,'https://www.youtube.com/watch?v=1Wp2gjbu4BM',0,'1Wp2gjbu4BM','孩子王Kid King   “Lucky&Kobe” 【Official Music Video】','',1,1497769880.7516,'2017-06-18 07:11:25','2017-06-18 07:11:20'),(50,3,3,'https://www.youtube.com/watch?v=F0zKhlnbooM',0,'F0zKhlnbooM','A/DA 阿達《上班不要跟我聊夢想》Official Music Video','',1,1497875243.5818,'2017-06-19 12:27:25','2017-06-19 12:27:23'),(51,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497886719.4153,'2017-06-19 15:38:41','2017-06-19 15:38:39'),(52,3,3,'https://www.youtube.com/watch?v=VwJruL9nBwQ',0,'VwJruL9nBwQ','ラブ・ストーリーは突然に - 小田和正','',1,1497886730.7392,'2017-06-19 15:42:46','2017-06-19 15:38:50'),(53,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497888288.0183,'2017-06-19 16:04:52','2017-06-19 16:04:48'),(54,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497888319.8607,'2017-06-19 16:07:26','2017-06-19 16:05:19'),(55,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497888387.1446,'2017-06-19 16:09:23','2017-06-19 16:06:27'),(56,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497888456.5879,'2017-06-19 16:09:53','2017-06-19 16:07:36'),(57,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497888474.8659,'2017-06-19 16:13:31','2017-06-19 16:07:54'),(58,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497888484.635,'2017-06-19 16:17:37','2017-06-19 16:08:04'),(59,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497888566.0484,'2017-06-19 16:21:20','2017-06-19 16:09:26'),(60,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497888568.4378,'2017-06-19 16:21:57','2017-06-19 16:09:28'),(61,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497888570.4469,'2017-06-19 16:22:25','2017-06-19 16:09:30'),(62,3,3,'https://www.youtube.com/watch?v=VwJruL9nBwQ',0,'VwJruL9nBwQ','ラブ・ストーリーは突然に - 小田和正','',1,1497888610.5472,'2017-06-19 16:26:04','2017-06-19 16:10:10'),(63,3,3,'https://www.youtube.com/watch?v=VwJruL9nBwQ',0,'VwJruL9nBwQ','ラブ・ストーリーは突然に - 小田和正','',1,1497889299.7429,'2017-06-19 16:27:07','2017-06-19 16:21:39'),(64,3,9,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497889609.7597,'2017-06-19 16:27:54','2017-06-19 16:26:49'),(65,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497889622.3072,'2017-06-20 11:09:08','2017-06-19 16:27:02'),(66,3,9,'https://www.youtube.com/watch?v=VwJruL9nBwQ',0,'VwJruL9nBwQ','ラブ・ストーリーは突然に - 小田和正','',1,1497889835.909,'2017-06-20 11:09:28','2017-06-19 16:30:35'),(67,3,3,'https://www.youtube.com/watch?v=VwJruL9nBwQ',0,'VwJruL9nBwQ','ラブ・ストーリーは突然に - 小田和正','',1,1497889895.3048,'2017-06-20 11:21:09','2017-06-19 16:31:35'),(68,3,9,'https://www.youtube.com/watch?v=VwJruL9nBwQ',0,'VwJruL9nBwQ','ラブ・ストーリーは突然に - 小田和正','',1,1497889926.4006,'2017-06-20 11:26:12','2017-06-19 16:32:06'),(69,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497957002.838,'2017-06-20 11:39:41','2017-06-20 11:10:02'),(70,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497889895.3047,'2017-06-20 11:14:28','2017-06-20 11:11:00'),(71,3,3,'https://www.youtube.com/watch?v=ybfWYpYhTQQ',0,'ybfWYpYhTQQ','盧廣仲 Crowd Lu 【魚仔】 Official Music Video （花甲男孩轉大人主題曲）','',1,1497957002.8379,'2017-06-20 11:31:09','2017-06-20 11:27:55'),(72,3,3,'https://www.youtube.com/watch?v=ybfWYpYhTQQ',0,'ybfWYpYhTQQ','盧廣仲 Crowd Lu 【魚仔】 Official Music Video （花甲男孩轉大人主題曲）','',1,1497958092.403,'2017-06-20 11:48:15','2017-06-20 11:28:12'),(73,3,3,'https://www.youtube.com/watch?v=h1LcDAN7d3w',0,'h1LcDAN7d3w','伍佰&China Blue – 熱淚暗班車 Re Lei An Ban Che (Official Video 官方MV)','',1,1497958116.3537,'2017-06-20 11:48:41','2017-06-20 11:28:36'),(74,3,3,'https://www.youtube.com/watch?v=qIF8xvSA0Gw',0,'qIF8xvSA0Gw','黃明志Namewee feat. 王力宏 Leehom Wang【漂向北方 Stranger In The North 】@CROSSOVER ASIA 2017亞洲通車專輯','',1,1497958146.6391,'2017-06-20 11:53:37','2017-06-20 11:29:06'),(75,3,3,'https://www.youtube.com/watch?v=-ycwrpsZp7U',0,'-ycwrpsZp7U','閃靈-殘枝（失竊千年）','',1,1497958170.3284,'2017-06-20 11:58:52','2017-06-20 11:29:30'),(76,3,3,'https://www.youtube.com/watch?v=rVEMTxg_LrU',-1,'rVEMTxg_LrU','Jonathan Lee李宗盛 [ 山丘 ] Official Music Video','',1,1497958185.8131,'2017-06-20 12:04:06','2017-06-20 11:29:45'),(77,3,3,'https://www.youtube.com/watch?v=wdypZWuoKvQ',-1,'wdypZWuoKvQ','李榮浩 - 李白 (官方版MV)','',1,1497958201.1974,'2017-06-20 12:10:58','2017-06-20 11:30:01'),(78,3,3,'https://www.youtube.com/watch?v=j3AW1uKj2aE',-1,'j3AW1uKj2aE','關喆 Grady - 想你的夜 (未眠版) Miss You Tonight (Official 高畫質 HD 官方完整版 MV)','',1,1497958214.7603,'2017-06-20 12:12:17','2017-06-20 11:30:14'),(79,3,3,'https://www.youtube.com/watch?v=Oc_VUUE9MHo',-1,'Oc_VUUE9MHo','MP魔幻力量 [ 我還是愛著你 I still love you ] Official Music Video - 三立華劇「幸福兌換券」片尾曲','',1,1497958230.0799,'2017-06-20 12:16:18','2017-06-20 11:30:30'),(80,3,3,'https://www.youtube.com/watch?v=vJO4Fg_iEss',0,'vJO4Fg_iEss','❄「求婚大作戰」主題曲:小さな恋のうた《小小戀歌》 －粉ミルク (Cover)中文字幕❄','',1,1497958253.4562,'2017-06-20 12:21:13','2017-06-20 11:30:53'),(81,3,3,'https://www.youtube.com/watch?v=R0zid8kt3yA',0,'R0zid8kt3yA','【你的名字MAD】前前前世《中日字幕》','',1,1497958273.757,'2017-06-20 12:26:05','2017-06-20 11:31:13'),(82,3,3,'https://www.youtube.com/watch?v=h2PJkbIBmVQ',-1,'h2PJkbIBmVQ','The Chainsmokers - Paris Lyrics','',1,1497957002.8379,'2017-06-20 11:35:54','2017-06-20 11:31:48'),(83,3,3,'https://www.youtube.com/watch?v=CMm0RYovM5U',0,'CMm0RYovM5U','T.Rex \'20th Century Boy\'','',1,1497958349.8635,'2017-06-20 12:30:53','2017-06-20 11:32:29'),(84,9,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',0,1497959083.1882,'2017-06-20 11:44:43','2017-06-20 11:44:43'),(85,3,3,'https://www.youtube.com/watch?v=9Udy1EEVLWg',0,'9Udy1EEVLWg','孩子王 Kid King - 異鄉男兒【Official Live Video】','',1,1497960353.2876,'2017-06-20 12:34:32','2017-06-20 12:05:53'),(86,3,3,'https://www.youtube.com/watch?v=S4JLJVVjevI',0,'S4JLJVVjevI','美秀集團 Amazing Show－捲菸【Official Music Video】','',1,1497960408.4196,'2017-06-20 12:38:48','2017-06-20 12:06:48'),(87,3,3,'https://www.youtube.com/watch?v=p_fEh6dBAG0',0,'p_fEh6dBAG0','美秀集團 Amazing Show－細粒的目睭【Official Lyrics Video】','',1,1497960436.3179,'2017-06-20 12:42:50','2017-06-20 12:07:16'),(88,3,3,'https://www.youtube.com/watch?v=-qWSk3JdUOI',0,'-qWSk3JdUOI','狗柏 － 魯之歌','',1,1497960460.8758,'2017-06-20 12:48:08','2017-06-20 12:07:40'),(89,3,3,'https://www.youtube.com/watch?v=cOiUY2vdfOs',0,'cOiUY2vdfOs','八十八顆芭樂籽 88balaz 追太陽的人【OFFICIAL MUSIC VIDEO】','',1,1497960813.4174,'2017-06-20 12:48:33','2017-06-20 12:13:33'),(90,3,3,'https://www.youtube.com/watch?v=NjTT5_RSkw4',0,'NjTT5_RSkw4','朴樹 - 平凡之路 [歌詞字幕][電影《後會無期》主題曲][完整高清音質] The Continent Theme Song - The Ordinary Road (Pu Shu)','',1,1497962160.8479,'2017-06-20 12:49:07','2017-06-20 12:36:00'),(91,3,3,'https://www.youtube.com/watch?v=XKuL5xaKZHM',0,'XKuL5xaKZHM','薛之謙【演員】官方完整版 MV','',1,1497962682.4795,'2017-06-20 12:49:34','2017-06-20 12:44:42'),(92,3,3,'https://www.youtube.com/watch?v=qJopuCSGE5M',0,'qJopuCSGE5M','薛之謙【剛剛好】官方完整版 MV','張尚仁點歌',1,1497962891.1172,'2017-06-20 12:52:07','2017-06-20 12:48:11'),(93,3,3,'https://www.youtube.com/watch?v=qJopuCSGE5M',0,'qJopuCSGE5M','薛之謙【剛剛好】官方完整版 MV','剛在講話剛剛好',1,1497963279.5285,'2017-06-20 12:54:41','2017-06-20 12:54:39'),(94,3,3,'https://www.youtube.com/watch?v=3MHVNxd130E',0,'3MHVNxd130E','伍佰 & China Blue - 釘子花 Ding Zi Hua（Official MV 官方完整版）','',1,1497963591.4511,'2017-06-20 12:59:53','2017-06-20 12:59:51'),(95,3,3,'https://www.youtube.com/watch?v=bu7nU9Mhpyo',0,'bu7nU9Mhpyo','周杰倫 Jay Chou (特別演出: 派偉俊)【告白氣球 Love Confession】Official MV','',1,1497963639.4005,'2017-06-20 13:10:18','2017-06-20 13:00:39'),(96,3,3,'https://www.youtube.com/watch?v=ybfWYpYhTQQ',0,'ybfWYpYhTQQ','盧廣仲 Crowd Lu 【魚仔】 Official Music Video （花甲男孩轉大人主題曲）','',1,1497963688.8777,'2017-06-20 13:17:57','2017-06-20 13:01:28'),(97,3,3,'https://www.youtube.com/watch?v=T4SimnaiktU',0,'T4SimnaiktU','G.E.M.【光年之外 LIGHT YEARS AWAY 】MV (電影《太空潛航者 Passengers》中文主題曲) [HD] 鄧紫棋','',1,1497963710.8997,'2017-06-20 13:22:21','2017-06-20 13:01:50'),(98,3,3,'https://www.youtube.com/watch?v=edTQsoNcADA&list=PLsyOSbh5bs15OXJIigNdRgK0za-JXwhz1&index=12',-1,'edTQsoNcADA','謝和弦 R-chord – 謝謝妳愛我 Thanks for your love (華納 Official 高畫質 HD 官方完整版 MV)','',1,1497963748.7123,'2017-06-20 13:23:08','2017-06-20 13:02:28'),(99,3,3,'https://www.youtube.com/watch?v=gd38-X3HpbM',-1,'gd38-X3HpbM','林俊傑 JJ Lin – 不為誰而作的歌 Twilight (華納 Official 高畫質 HD 官方完整版 MV)','',1,1497963773.2292,'2017-06-20 13:27:02','2017-06-20 13:02:53'),(100,3,3,'https://www.youtube.com/watch?v=Oi2261-l7nY',0,'Oi2261-l7nY','【六弄咖啡館At Cafe 6】Movie Theme Song-孫燕姿SunYanZi 《半句再見》電影版MV','',1,1497963639.4004,'2017-06-20 13:06:09','2017-06-20 13:03:40'),(101,3,3,'https://www.youtube.com/watch?v=BsvIwqyiaJw',0,'BsvIwqyiaJw','李毓芬Tia Lee《是我不夠好Not Good Enough》Official Music Video HD','',1,1497963842.0995,'2017-06-20 13:27:20','2017-06-20 13:04:02'),(102,3,3,'https://www.youtube.com/watch?v=c9PEYJdwdwI',0,'c9PEYJdwdwI','滅火器 Fire EX. - 長途夜車 Southbound Night Bus Lyric Video','',1,1497963868.5893,'2017-06-20 13:29:37','2017-06-20 13:04:28'),(103,3,3,'https://www.youtube.com/watch?v=Dnz-BTz9eDU',0,'Dnz-BTz9eDU','怕胖團 PA PUN BAND 《 魚 》Music Video','',1,1497963922.242,'2017-06-20 13:36:19','2017-06-20 13:05:22'),(104,3,3,'https://www.youtube.com/watch?v=TY1RRAQYsVk',0,'TY1RRAQYsVk','拍謝少年 - 深海的你','',1,1497963688.8776,'2017-06-20 13:13:57','2017-06-20 13:05:57'),(105,3,3,'https://www.youtube.com/watch?v=ZFo8-JqzSCM',-1,'ZFo8-JqzSCM','Chuck Berry - Johnny B. Goode','',1,1497963868.5892,'2017-06-20 13:27:47','2017-06-20 13:27:36'),(106,3,3,'https://www.youtube.com/watch?v=ZFo8-JqzSCM',-1,'ZFo8-JqzSCM','Chuck Berry - Johnny B. Goode','',1,1497963922.2419,'2017-06-20 13:29:57','2017-06-20 13:29:48'),(107,3,3,'https://www.youtube.com/watch?v=gd38-X3HpbM',-1,'gd38-X3HpbM','林俊傑 JJ Lin – 不為誰而作的歌 Twilight (華納 Official 高畫質 HD 官方完整版 MV)','',1,1497963922.2419,'2017-06-20 13:31:15','2017-06-20 13:30:57'),(108,3,3,'https://www.youtube.com/watch?v=edTQsoNcADA&list=PLsyOSbh5bs15OXJIigNdRgK0za-JXwhz1&index=12',-1,'edTQsoNcADA','謝和弦 R-chord – 謝謝妳愛我 Thanks for your love (華納 Official 高畫質 HD 官方完整版 MV)','',1,1497965774.6297,'2017-06-20 13:36:51','2017-06-20 13:36:14'),(109,3,3,'https://www.youtube.com/watch?v=TY1RRAQYsVk',0,'TY1RRAQYsVk','拍謝少年 - 深海的你','',1,1497966382.5419,'2017-06-20 13:46:25','2017-06-20 13:46:22'),(110,3,3,'https://www.youtube.com/watch?v=TY1RRAQYsVk',0,'TY1RRAQYsVk','拍謝少年 - 深海的你','',1,1497966872.681,'2017-06-20 13:54:42','2017-06-20 13:54:32'),(111,3,3,'https://www.youtube.com/watch?v=TY1RRAQYsVk',0,'TY1RRAQYsVk','拍謝少年 - 深海的你','',1,1497966900.8904,'2017-06-20 13:57:06','2017-06-20 13:55:00'),(112,3,3,'https://www.youtube.com/watch?v=TY1RRAQYsVk',0,'TY1RRAQYsVk','拍謝少年 - 深海的你','',1,1497967120.7039,'2017-06-20 14:03:30','2017-06-20 13:58:40'),(113,3,3,'https://www.youtube.com/watch?v=TY1RRAQYsVk',0,'TY1RRAQYsVk','拍謝少年 - 深海的你','',1,1497967458.7491,'2017-06-20 14:05:12','2017-06-20 14:04:18'),(114,3,3,'https://www.youtube.com/watch?v=TY1RRAQYsVk',0,'TY1RRAQYsVk','拍謝少年 - 深海的你','',1,1497967574.1191,'2017-06-20 14:06:17','2017-06-20 14:06:14'),(115,3,3,'https://www.youtube.com/watch?v=TY1RRAQYsVk',0,'TY1RRAQYsVk','拍謝少年 - 深海的你','',1,1497967606.1119,'2017-06-20 14:06:49','2017-06-20 14:06:46'),(116,3,3,'https://www.youtube.com/watch?v=TY1RRAQYsVk',0,'TY1RRAQYsVk','拍謝少年 - 深海的你','',1,1497967624.5237,'2017-06-20 14:07:08','2017-06-20 14:07:04'),(117,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497967649.1297,'2017-06-20 14:08:00','2017-06-20 14:07:29'),(118,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497967698.1061,'2017-06-20 14:08:21','2017-06-20 14:08:18'),(119,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497967855.0996,'2017-06-20 14:10:55','2017-06-20 14:10:55'),(120,3,3,'https://www.youtube.com/watch?v=1xlASR2bfGk',0,'1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)','',1,1497967928.9391,'2017-06-20 14:12:09','2017-06-20 14:12:08'),(121,3,3,'https://www.youtube.com/watch?v=TY1RRAQYsVk',0,'TY1RRAQYsVk','拍謝少年 - 深海的你','',1,1497967991.5917,'2017-06-20 14:13:15','2017-06-20 14:13:11'),(122,3,3,'https://www.youtube.com/watch?v=TY1RRAQYsVk',0,'TY1RRAQYsVk','拍謝少年 - 深海的你','',1,1497968137.5579,'2017-06-20 14:15:39','2017-06-20 14:15:37'),(123,3,3,'https://www.youtube.com/watch?v=TY1RRAQYsVk',0,'TY1RRAQYsVk','拍謝少年 - 深海的你','',1,1497968143.8931,'2017-06-20 14:15:48','2017-06-20 14:15:43'),(124,3,3,'https://www.youtube.com/watch?v=TY1RRAQYsVk',0,'TY1RRAQYsVk','拍謝少年 - 深海的你','',1,1497968353.7108,'2017-06-20 14:19:14','2017-06-20 14:19:13');
/*!40000 ALTER TABLE `play_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `random_account` varchar(32) NOT NULL,
  `name` varchar(45) NOT NULL DEFAULT 'Guest',
  `channel` varchar(200) DEFAULT NULL,
  `user_role` varchar(45) NOT NULL,
  `account` varchar(200) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'45f5ed82c4e56d8c99caf0f0db98307d','Guest','1','member',NULL,NULL,'2017-06-17 08:30:15','2017-06-17 08:11:57'),(2,'57a6fb92689df6a3d6bfa6d06a4b8f2d','Guest','2','member','',NULL,'2017-06-17 08:30:15','2017-06-17 08:18:28'),(3,'88130a7018a18e3243b4125d94b3a4c5','Guest','hikaruocean','member',NULL,NULL,'2017-06-17 10:28:12','2017-06-17 08:32:14'),(4,'01f8d4c8a92cfb80fa541552e5f92ca9','Guest','4','member',NULL,NULL,'2017-06-17 09:06:13','2017-06-17 09:06:12'),(5,'9fae78edcfd5d9eb95ce11710138ee40','Guest','5','member',NULL,NULL,'2017-06-18 03:28:36','2017-06-18 03:28:36'),(6,'bb1bc04e6c7c3121460376f9e09348c5','Guest','6','member',NULL,NULL,'2017-06-18 03:37:46','2017-06-18 03:37:46'),(7,'e57d84bef3a3a742ebae3a3be2419140','Guest','charlie','member',NULL,NULL,'2017-06-18 06:52:13','2017-06-18 06:50:39'),(8,'053d5641f806f83133f8d920814874b3','Guest','8','member',NULL,NULL,'2017-06-18 07:10:43','2017-06-18 07:10:43'),(9,'29dad43ec279b81a66d38e1271e6315f','Guest','9','member',NULL,NULL,'2017-06-19 16:22:57','2017-06-19 16:22:57');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `video_list`
--

DROP TABLE IF EXISTS `video_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `video_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(256) NOT NULL,
  `video_id` varchar(64) NOT NULL,
  `title` varchar(256) NOT NULL,
  `info_result_code` tinyint(4) NOT NULL,
  `order_count` int(10) unsigned NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni-video_id` (`video_id`),
  KEY `idx-order_count` (`order_count`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `video_list`
--

LOCK TABLES `video_list` WRITE;
/*!40000 ALTER TABLE `video_list` DISABLE KEYS */;
INSERT INTO `video_list` VALUES (1,'https://www.youtube.com/watch?v=1xlASR2bfGk','1xlASR2bfGk','薛之謙【醜八怪】官方完整版 MV (曲: 李榮浩)',0,46,'2017-06-20 14:12:08','2017-06-17 13:03:59'),(2,'https://www.youtube.com/watch?v=ybfWYpYhTQQ','ybfWYpYhTQQ','盧廣仲 Crowd Lu 【魚仔】 Official Music Video （花甲男孩轉大人主題曲）',0,5,'2017-06-20 13:01:28','2017-06-17 13:45:35'),(3,'https://www.youtube.com/watch?v=XKuL5xaKZHM','XKuL5xaKZHM','薛之謙【演員】官方完整版 MV',0,3,'2017-06-20 12:44:42','2017-06-17 13:50:04'),(4,'https://www.youtube.com/watch?v=ZFo8-JqzSCM','ZFo8-JqzSCM','Chuck Berry - Johnny B. Goode',-1,15,'2017-06-20 13:29:48','2017-06-17 15:41:08'),(5,'https://www.youtube.com/watch?v=VwJruL9nBwQ','VwJruL9nBwQ','ラブ・ストーリーは突然に - 小田和正',0,8,'2017-06-19 16:32:06','2017-06-18 06:40:47'),(6,'https://www.youtube.com/watch?v=h1LcDAN7d3w','h1LcDAN7d3w','伍佰&China Blue – 熱淚暗班車 Re Lei An Ban Che (Official Video 官方MV)',0,2,'2017-06-20 11:28:36','2017-06-18 06:41:59'),(7,'https://www.youtube.com/watch?v=3MHVNxd130E','3MHVNxd130E','伍佰 & China Blue - 釘子花 Ding Zi Hua（Official MV 官方完整版）',0,2,'2017-06-20 12:59:51','2017-06-18 06:44:06'),(8,'https://www.youtube.com/watch?v=1Wp2gjbu4BM','1Wp2gjbu4BM','孩子王Kid King   “Lucky&Kobe” 【Official Music Video】',0,1,'2017-06-18 07:11:20','2017-06-18 07:11:20'),(9,'https://www.youtube.com/watch?v=F0zKhlnbooM','F0zKhlnbooM','A/DA 阿達《上班不要跟我聊夢想》Official Music Video',0,1,'2017-06-19 12:27:23','2017-06-19 12:27:23'),(10,'https://www.youtube.com/watch?v=qIF8xvSA0Gw','qIF8xvSA0Gw','黃明志Namewee feat. 王力宏 Leehom Wang【漂向北方 Stranger In The North 】@CROSSOVER ASIA 2017亞洲通車專輯',0,1,'2017-06-20 11:29:06','2017-06-20 11:29:06'),(11,'https://www.youtube.com/watch?v=-ycwrpsZp7U','-ycwrpsZp7U','閃靈-殘枝（失竊千年）',0,1,'2017-06-20 11:29:30','2017-06-20 11:29:30'),(12,'https://www.youtube.com/watch?v=rVEMTxg_LrU','rVEMTxg_LrU','Jonathan Lee李宗盛 [ 山丘 ] Official Music Video',-1,1,'2017-06-20 11:29:45','2017-06-20 11:29:45'),(13,'https://www.youtube.com/watch?v=wdypZWuoKvQ','wdypZWuoKvQ','李榮浩 - 李白 (官方版MV)',-1,1,'2017-06-20 11:30:01','2017-06-20 11:30:01'),(14,'https://www.youtube.com/watch?v=j3AW1uKj2aE','j3AW1uKj2aE','關喆 Grady - 想你的夜 (未眠版) Miss You Tonight (Official 高畫質 HD 官方完整版 MV)',-1,1,'2017-06-20 11:30:14','2017-06-20 11:30:14'),(15,'https://www.youtube.com/watch?v=Oc_VUUE9MHo','Oc_VUUE9MHo','MP魔幻力量 [ 我還是愛著你 I still love you ] Official Music Video - 三立華劇「幸福兌換券」片尾曲',-1,1,'2017-06-20 11:30:30','2017-06-20 11:30:30'),(16,'https://www.youtube.com/watch?v=vJO4Fg_iEss','vJO4Fg_iEss','❄「求婚大作戰」主題曲:小さな恋のうた《小小戀歌》 －粉ミルク (Cover)中文字幕❄',0,1,'2017-06-20 11:30:53','2017-06-20 11:30:53'),(17,'https://www.youtube.com/watch?v=R0zid8kt3yA','R0zid8kt3yA','【你的名字MAD】前前前世《中日字幕》',0,1,'2017-06-20 11:31:13','2017-06-20 11:31:13'),(18,'https://www.youtube.com/watch?v=h2PJkbIBmVQ','h2PJkbIBmVQ','The Chainsmokers - Paris Lyrics',-1,1,'2017-06-20 11:31:48','2017-06-20 11:31:48'),(19,'https://www.youtube.com/watch?v=CMm0RYovM5U','CMm0RYovM5U','T.Rex \'20th Century Boy\'',0,1,'2017-06-20 11:32:29','2017-06-20 11:32:29'),(20,'https://www.youtube.com/watch?v=9Udy1EEVLWg','9Udy1EEVLWg','孩子王 Kid King - 異鄉男兒【Official Live Video】',0,1,'2017-06-20 12:05:53','2017-06-20 12:05:53'),(21,'https://www.youtube.com/watch?v=S4JLJVVjevI','S4JLJVVjevI','美秀集團 Amazing Show－捲菸【Official Music Video】',0,1,'2017-06-20 12:06:48','2017-06-20 12:06:48'),(22,'https://www.youtube.com/watch?v=p_fEh6dBAG0','p_fEh6dBAG0','美秀集團 Amazing Show－細粒的目睭【Official Lyrics Video】',0,1,'2017-06-20 12:07:16','2017-06-20 12:07:16'),(23,'https://www.youtube.com/watch?v=-qWSk3JdUOI','-qWSk3JdUOI','狗柏 － 魯之歌',0,1,'2017-06-20 12:07:40','2017-06-20 12:07:40'),(24,'https://www.youtube.com/watch?v=cOiUY2vdfOs','cOiUY2vdfOs','八十八顆芭樂籽 88balaz 追太陽的人【OFFICIAL MUSIC VIDEO】',0,1,'2017-06-20 12:13:33','2017-06-20 12:13:33'),(25,'https://www.youtube.com/watch?v=NjTT5_RSkw4','NjTT5_RSkw4','朴樹 - 平凡之路 [歌詞字幕][電影《後會無期》主題曲][完整高清音質] The Continent Theme Song - The Ordinary Road (Pu Shu)',0,1,'2017-06-20 12:36:00','2017-06-20 12:36:00'),(26,'https://www.youtube.com/watch?v=qJopuCSGE5M','qJopuCSGE5M','薛之謙【剛剛好】官方完整版 MV',0,2,'2017-06-20 12:54:39','2017-06-20 12:48:11'),(27,'https://www.youtube.com/watch?v=bu7nU9Mhpyo','bu7nU9Mhpyo','周杰倫 Jay Chou (特別演出: 派偉俊)【告白氣球 Love Confession】Official MV',0,1,'2017-06-20 13:00:39','2017-06-20 13:00:39'),(28,'https://www.youtube.com/watch?v=T4SimnaiktU','T4SimnaiktU','G.E.M.【光年之外 LIGHT YEARS AWAY 】MV (電影《太空潛航者 Passengers》中文主題曲) [HD] 鄧紫棋',0,1,'2017-06-20 13:01:50','2017-06-20 13:01:50'),(29,'https://www.youtube.com/watch?v=edTQsoNcADA&list=PLsyOSbh5bs15OXJIigNdRgK0za-JXwhz1&index=12','edTQsoNcADA','謝和弦 R-chord – 謝謝妳愛我 Thanks for your love (華納 Official 高畫質 HD 官方完整版 MV)',-1,2,'2017-06-20 13:36:14','2017-06-20 13:02:28'),(30,'https://www.youtube.com/watch?v=gd38-X3HpbM','gd38-X3HpbM','林俊傑 JJ Lin – 不為誰而作的歌 Twilight (華納 Official 高畫質 HD 官方完整版 MV)',-1,2,'2017-06-20 13:30:57','2017-06-20 13:02:53'),(31,'https://www.youtube.com/watch?v=Oi2261-l7nY','Oi2261-l7nY','【六弄咖啡館At Cafe 6】Movie Theme Song-孫燕姿SunYanZi 《半句再見》電影版MV',0,1,'2017-06-20 13:03:40','2017-06-20 13:03:40'),(32,'https://www.youtube.com/watch?v=BsvIwqyiaJw','BsvIwqyiaJw','李毓芬Tia Lee《是我不夠好Not Good Enough》Official Music Video HD',0,1,'2017-06-20 13:04:02','2017-06-20 13:04:02'),(33,'https://www.youtube.com/watch?v=c9PEYJdwdwI','c9PEYJdwdwI','滅火器 Fire EX. - 長途夜車 Southbound Night Bus Lyric Video',0,1,'2017-06-20 13:04:28','2017-06-20 13:04:28'),(34,'https://www.youtube.com/watch?v=Dnz-BTz9eDU','Dnz-BTz9eDU','怕胖團 PA PUN BAND 《 魚 》Music Video',0,1,'2017-06-20 13:05:22','2017-06-20 13:05:22'),(35,'https://www.youtube.com/watch?v=TY1RRAQYsVk','TY1RRAQYsVk','拍謝少年 - 深海的你',0,13,'2017-06-20 14:19:13','2017-06-20 13:05:57');
/*!40000 ALTER TABLE `video_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `video_list_user`
--

DROP TABLE IF EXISTS `video_list_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `video_list_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `video_id` varchar(64) NOT NULL,
  `order_count` int(10) unsigned NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni-user_id-video_id` (`user_id`,`video_id`),
  KEY `idx-order_count` (`order_count`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `video_list_user`
--

LOCK TABLES `video_list_user` WRITE;
/*!40000 ALTER TABLE `video_list_user` DISABLE KEYS */;
INSERT INTO `video_list_user` VALUES (1,3,'1xlASR2bfGk',39,'2017-06-20 14:12:08','2017-06-17 13:03:59'),(2,4,'1xlASR2bfGk',2,'2017-06-17 13:07:17','2017-06-17 13:07:05'),(3,3,'ybfWYpYhTQQ',5,'2017-06-20 13:01:28','2017-06-17 13:45:35'),(4,3,'XKuL5xaKZHM',3,'2017-06-20 12:44:42','2017-06-17 13:50:04'),(5,3,'ZFo8-JqzSCM',15,'2017-06-20 13:29:48','2017-06-17 15:41:08'),(6,3,'VwJruL9nBwQ',5,'2017-06-19 16:31:35','2017-06-18 06:40:47'),(7,3,'h1LcDAN7d3w',2,'2017-06-20 11:28:36','2017-06-18 06:41:59'),(8,3,'3MHVNxd130E',2,'2017-06-20 12:59:51','2017-06-18 06:44:06'),(9,7,'VwJruL9nBwQ',1,'2017-06-18 06:50:56','2017-06-18 06:50:56'),(10,7,'1xlASR2bfGk',4,'2017-06-18 06:53:03','2017-06-18 06:52:57'),(11,8,'1Wp2gjbu4BM',1,'2017-06-18 07:11:20','2017-06-18 07:11:20'),(12,3,'F0zKhlnbooM',1,'2017-06-19 12:27:23','2017-06-19 12:27:23'),(13,9,'1xlASR2bfGk',1,'2017-06-19 16:26:49','2017-06-19 16:26:49'),(14,9,'VwJruL9nBwQ',2,'2017-06-19 16:32:06','2017-06-19 16:30:35'),(15,3,'qIF8xvSA0Gw',1,'2017-06-20 11:29:06','2017-06-20 11:29:06'),(16,3,'-ycwrpsZp7U',1,'2017-06-20 11:29:30','2017-06-20 11:29:30'),(17,3,'rVEMTxg_LrU',1,'2017-06-20 11:29:45','2017-06-20 11:29:45'),(18,3,'wdypZWuoKvQ',1,'2017-06-20 11:30:01','2017-06-20 11:30:01'),(19,3,'j3AW1uKj2aE',1,'2017-06-20 11:30:14','2017-06-20 11:30:14'),(20,3,'Oc_VUUE9MHo',1,'2017-06-20 11:30:30','2017-06-20 11:30:30'),(21,3,'vJO4Fg_iEss',1,'2017-06-20 11:30:53','2017-06-20 11:30:53'),(22,3,'R0zid8kt3yA',1,'2017-06-20 11:31:13','2017-06-20 11:31:13'),(23,3,'h2PJkbIBmVQ',1,'2017-06-20 11:31:48','2017-06-20 11:31:48'),(24,3,'CMm0RYovM5U',1,'2017-06-20 11:32:29','2017-06-20 11:32:29'),(25,3,'9Udy1EEVLWg',1,'2017-06-20 12:05:53','2017-06-20 12:05:53'),(26,3,'S4JLJVVjevI',1,'2017-06-20 12:06:48','2017-06-20 12:06:48'),(27,3,'p_fEh6dBAG0',1,'2017-06-20 12:07:16','2017-06-20 12:07:16'),(28,3,'-qWSk3JdUOI',1,'2017-06-20 12:07:40','2017-06-20 12:07:40'),(29,3,'cOiUY2vdfOs',1,'2017-06-20 12:13:33','2017-06-20 12:13:33'),(30,3,'NjTT5_RSkw4',1,'2017-06-20 12:36:00','2017-06-20 12:36:00'),(31,3,'qJopuCSGE5M',2,'2017-06-20 12:54:39','2017-06-20 12:48:11'),(32,3,'bu7nU9Mhpyo',1,'2017-06-20 13:00:39','2017-06-20 13:00:39'),(33,3,'T4SimnaiktU',1,'2017-06-20 13:01:50','2017-06-20 13:01:50'),(34,3,'edTQsoNcADA',2,'2017-06-20 13:36:14','2017-06-20 13:02:28'),(35,3,'gd38-X3HpbM',2,'2017-06-20 13:30:57','2017-06-20 13:02:53'),(36,3,'Oi2261-l7nY',1,'2017-06-20 13:03:40','2017-06-20 13:03:40'),(37,3,'BsvIwqyiaJw',1,'2017-06-20 13:04:02','2017-06-20 13:04:02'),(38,3,'c9PEYJdwdwI',1,'2017-06-20 13:04:28','2017-06-20 13:04:28'),(39,3,'Dnz-BTz9eDU',1,'2017-06-20 13:05:22','2017-06-20 13:05:22'),(40,3,'TY1RRAQYsVk',13,'2017-06-20 14:19:13','2017-06-20 13:05:57');
/*!40000 ALTER TABLE `video_list_user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-06-20 23:30:11
