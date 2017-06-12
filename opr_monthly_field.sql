-- MySQL dump 10.13  Distrib 5.6.26, for Linux (i686)
--
-- Host: localhost    Database: operation
-- ------------------------------------------------------
-- Server version	5.6.26

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
-- Table structure for table `opr_monthly_field`
--

DROP TABLE IF EXISTS `opr_monthly_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `opr_monthly_field` (
  `code` char(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `upd_type` char(1) NOT NULL DEFAULT 'M',
  `field_type` char(1) NOT NULL DEFAULT 'N',
  `status` char(1) DEFAULT 'Y',
  `function_name` varchar(200) DEFAULT NULL,
  `lcu` varchar(30) DEFAULT NULL,
  `luu` varchar(30) DEFAULT NULL,
  `lcd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `lud` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opr_monthly_field`
--

LOCK TABLES `opr_monthly_field` WRITE;
/*!40000 ALTER TABLE `opr_monthly_field` DISABLE KEYS */;
INSERT INTO `opr_monthly_field` VALUES ('10001','清洁收入','M','N','Y',NULL,'admin','admin','2017-05-25 08:48:01','2017-05-25 08:48:01'),('10002','灭虫收入','M','N','Y',NULL,'admin','admin','2017-05-25 08:48:01','2017-05-25 08:48:01'),('10003','杂项及其他销售收入','M','N','Y',NULL,'admin','admin','2017-05-25 08:48:01','2017-05-25 08:48:01'),('10004','飘盈香收入','M','N','Y',NULL,'admin','admin','2017-05-25 08:48:01','2017-05-25 08:48:01'),('10005','甲醛收入','M','N','Y',NULL,'admin','admin','2017-05-25 08:48:01','2017-05-25 08:48:01'),('10006','纸品销售收入','M','N','Y',NULL,'admin','admin','2017-05-25 08:48:01','2017-05-25 08:48:01'),('10007','服务费收入合共','Y','N','Y',NULL,'admin','admin','2017-05-31 01:59:48','2017-05-31 02:00:34'),('10008','服务专利费','Y','N','Y',NULL,'admin','admin','2017-05-31 01:59:48','2017-05-31 02:00:37'),('10009','纸品专利费','Y','N','Y',NULL,'admin','admin','2017-05-31 02:01:46','2017-05-31 02:01:46'),('10010','专利费合共','Y','N','Y',NULL,'admin','admin','2017-05-31 02:01:46','2017-05-31 02:01:46');
/*!40000 ALTER TABLE `opr_monthly_field` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-06-08 15:27:36
