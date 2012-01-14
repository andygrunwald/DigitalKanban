-- MySQL dump 10.13  Distrib 5.1.49, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: digital_kanban
-- ------------------------------------------------------
-- Server version	5.1.49-3

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
-- Table structure for table `board`
--

DROP TABLE IF EXISTS `board`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `board` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` longtext NOT NULL,
  `created` datetime NOT NULL,
  `edited` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `board`
--

LOCK TABLES `board` WRITE;
/*!40000 ALTER TABLE `board` DISABLE KEYS */;
INSERT INTO `board` VALUES (1,'Team E-Commerce','Kanban-board of e-commerce-team. Displays issues from topics online-shopping, electronic trading/commerce and development of websites like online shops. Systems which are used are for example Magento, Oxid or Terrashop.','2012-01-13 10:18:14','2012-01-13 10:18:14'),(2,'Team Human Resources','Kanban-board of staff department. Working and managing at issues for human resources. For example different topics of employees, payment of them, hiring new people, organize travel for team events and ask for working feedback.','2012-01-13 10:18:14','2012-01-13 10:18:14'),(3,'Team Backend-Development','Kanban-board of backend-development-team. Tasks for connection different websites with third party systems like ERP or CRM.','2012-01-13 10:18:14','2012-01-13 10:18:14');
/*!40000 ALTER TABLE `board` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `board_column`
--

DROP TABLE IF EXISTS `board_column`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `board_column` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `board_id` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `max_issues` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `edited` datetime NOT NULL,
  `sorting` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D14DC3D9E7EC5785` (`board_id`),
  CONSTRAINT `FK_D14DC3D9E7EC5785` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `board_column`
--

LOCK TABLES `board_column` WRITE;
/*!40000 ALTER TABLE `board_column` DISABLE KEYS */;
INSERT INTO `board_column` VALUES (1,1,'Backlog',0,'2012-01-13 10:18:14','2012-01-13 10:18:14',10),(2,1,'ToDo',10,'2012-01-13 10:18:14','2012-01-13 10:18:14',20),(3,1,'Analysis',4,'2012-01-13 10:18:14','2012-01-13 10:18:14',30),(4,1,'Development',2,'2012-01-13 10:18:14','2012-01-13 10:18:14',40),(5,1,'Approval',0,'2012-01-13 10:18:14','2012-01-13 10:18:14',50),(6,1,'Deploy',3,'2012-01-13 10:18:14','2012-01-13 10:18:14',60),(7,1,'Done',0,'2012-01-13 10:18:14','2012-01-13 10:18:14',70),(8,2,'Job requests',15,'2012-01-13 10:18:14','2012-01-13 10:18:14',10),(9,2,'Job meeting',2,'2012-01-13 10:18:14','2012-01-13 10:18:14',20),(10,2,'Done',0,'2012-01-13 10:18:14','2012-01-13 10:18:14',30);
/*!40000 ALTER TABLE `board_column` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `issue`
--

DROP TABLE IF EXISTS `issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `issue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_user_id` int(11) DEFAULT NULL,
  `last_edited_user_id` int(11) DEFAULT NULL,
  `boardcolumn_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  `edited` datetime NOT NULL,
  `sorting` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_12AD233EE104C1D3` (`created_user_id`),
  KEY `IDX_12AD233E2CDA43A` (`last_edited_user_id`),
  KEY `IDX_12AD233EB9C80C` (`boardcolumn_id`),
  CONSTRAINT `FK_12AD233EB9C80C` FOREIGN KEY (`boardcolumn_id`) REFERENCES `board_column` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_12AD233E2CDA43A` FOREIGN KEY (`last_edited_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_12AD233EE104C1D3` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `issue`
--

LOCK TABLES `issue` WRITE;
/*!40000 ALTER TABLE `issue` DISABLE KEYS */;
INSERT INTO `issue` VALUES (1,1,1,1,'Evaluate Paypal module for Magento','2012-01-13 10:18:14','2012-01-13 10:18:14',30),(2,2,2,1,'Evaluate Paypal module for Oxid','2012-01-13 10:18:14','2012-01-13 10:18:14',20),(3,1,1,1,'Insert new design collection to Karl Lagerfelds onlineshop','2012-01-13 10:18:14','2012-01-13 10:18:14',10),(4,3,3,3,'Hash user passwords in database for more security','2012-01-13 10:18:14','2012-01-13 10:18:14',10),(5,2,1,1,'Write a new magento module to take A/B usability tests','2012-01-13 10:18:14','2012-01-13 10:18:14',40),(6,1,2,4,'\"Sign up\" function for newsletter at Levis online shop','2012-01-13 10:18:14','2012-01-13 10:18:14',10),(7,1,1,8,'Dennis Putta requested via Linked.in','2012-01-13 10:18:14','2012-01-13 10:18:14',10),(8,1,1,10,'Hire a new java developer','2012-01-13 10:18:14','2012-01-13 10:18:14',10);
/*!40000 ALTER TABLE `issue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` longtext NOT NULL,
  `created` datetime NOT NULL,
  `edited` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'ROLE_ADMIN','Administration. This group is focused on system management.','2012-01-13 10:18:14','2012-01-13 10:18:14'),(2,'ROLE_USER','Normal usergroup. For example employees.','2012-01-13 10:18:14','2012-01-13 10:18:14');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  `edited` datetime NOT NULL,
  `disabled` tinyint(1) NOT NULL,
  `salt` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'john','john@example.com','zymPo7bAqnceLYREPgTUTqmNIJR7+f4RFF4co0RT2YtB0/xf/aGmuSL9/HtCL5Ih/TM03AOLik39nb2DKycEMQ==','John','Doe','2012-01-13 10:18:14','2012-01-13 10:18:14',0,'a4ad2922a225b6c62acf017e6158be3d'),(2,'max','max@mustermann.de','GakKZx2DTgk1pto+UYMUqAEBF7FY+8ANCryxQFu0cfN1QyhUQEE0ETFYZQTacd14jt4HBmOXfZeCvdWDnjL8NQ==','Max','Mustermann','2012-01-13 10:18:14','2012-01-13 10:18:14',0,'3d9e1a5099ef3a8ba3416dd70ebfa3c4'),(3,'dieter','dieter@google.de','nUS3LE5Wc6viU757Ar5Qo5WK2IAs3NyZUPPbV0cierxujU4S+ds52aJyBrk7NhAvbJF616D+pgrsnxD6bzGqvA==','Dieter','Müller','2012-01-13 10:18:14','2012-01-13 10:18:14',1,'d96edaba7a5b363c7f9915bac2a0ded1'),(4,'markus','markus@yahoo.de','zpoKXub9etH9z5x1vx5UWpxbQHBSDZhuYBm+d/+D8niNMbtEklHdl1IJCtsr6XKnrWcSYhpS+SVfucVVYJUZIA==','Markus','Ele','2012-01-13 10:18:14','2012-01-13 10:18:14',0,'6c4133e29aae4a3eae1805f3cd52b3b2'),(5,'daniel','daniel@web.de','qy8Gv+IAaytHrN6hB1dCgpbQLxdvhKgwAOwRvSID5oLoCJO6UKUY+64DtN6f3Fp7TCWITBPtlMhsXswbTJqRSA==','Daniel','Schmi','2012-01-13 10:18:14','2012-01-13 10:18:14',0,'93f0d05cafb9c10a705068c8de9f1a04');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_has_board`
--

DROP TABLE IF EXISTS `user_has_board`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_has_board` (
  `user_id` int(11) NOT NULL,
  `board_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`board_id`),
  KEY `IDX_A33FB61DA76ED395` (`user_id`),
  KEY `IDX_A33FB61DE7EC5785` (`board_id`),
  CONSTRAINT `FK_A33FB61DE7EC5785` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`),
  CONSTRAINT `FK_A33FB61DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_has_board`
--

LOCK TABLES `user_has_board` WRITE;
/*!40000 ALTER TABLE `user_has_board` DISABLE KEYS */;
INSERT INTO `user_has_board` VALUES (1,1),(1,3),(2,2),(2,3),(3,2),(3,3),(4,1),(4,2),(4,3),(5,1);
/*!40000 ALTER TABLE `user_has_board` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_has_role`
--

DROP TABLE IF EXISTS `user_has_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_has_role` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `IDX_EAB8B535A76ED395` (`user_id`),
  KEY `IDX_EAB8B535D60322AC` (`role_id`),
  CONSTRAINT `FK_EAB8B535D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`),
  CONSTRAINT `FK_EAB8B535A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_has_role`
--

LOCK TABLES `user_has_role` WRITE;
/*!40000 ALTER TABLE `user_has_role` DISABLE KEYS */;
INSERT INTO `user_has_role` VALUES (1,1),(2,2),(3,2),(4,1),(5,2);
/*!40000 ALTER TABLE `user_has_role` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-01-13 10:54:25
