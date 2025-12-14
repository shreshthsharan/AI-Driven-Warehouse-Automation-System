-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: warehouse_inv
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `warehouse_inv`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `warehouse_inv` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `warehouse_inv`;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory` (
  `sku` varchar(10) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `stock` int NOT NULL,
  `min_threshold` int NOT NULL,
  `last_update` datetime NOT NULL,
  PRIMARY KEY (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
INSERT INTO `inventory` VALUES ('40E7BA14','Product C',70,90,'2025-06-26 08:51:23'),('73F785E2','Product A',954,100,'2025-06-25 09:07:55'),('933497E2','Product B',1232,180,'2025-06-26 08:38:18'),('93FF89E2','Product D',1557,251,'2025-06-26 08:44:30');
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rfid_logs`
--

DROP TABLE IF EXISTS `rfid_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rfid_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `rfid` varchar(32) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `item` varchar(100) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `mfg` date DEFAULT NULL,
  `exp` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rfid_logs`
--

LOCK TABLES `rfid_logs` WRITE;
/*!40000 ALTER TABLE `rfid_logs` DISABLE KEYS */;
INSERT INTO `rfid_logs` VALUES (1,'2025-06-25','03:24:33','93FF89E2','Received','PRODUCT D','Free','2025-03-10','2026-03-10'),(2,'2025-06-25','03:25:16','40E7BA14','Dispatched','PRODUCT C','42','2025-01-15','2027-01-15'),(3,'2025-06-26','05:02:38','933497E2','Dispatched','PRODUCT B','1L','2024-12-01','2029-12-01'),(4,'2025-06-26','05:02:47','93FF89E2','Dispatched','PRODUCT D','Free','2025-03-10','2026-03-10'),(5,'2025-06-26','05:03:36','93FF89E2','Dispatched','PRODUCT D','Free','2025-03-10','2026-03-10'),(6,'2025-06-26','05:03:53','933497E2','Dispatched','PRODUCT B','1L','2024-12-01','2029-12-01'),(7,'2025-06-26','05:09:04','933497E2','Dispatched','PRODUCT B','1L','2024-12-01','2029-12-01'),(8,'2025-06-26','05:11:03','933497E2','Dispatched','PRODUCT B','1L','2024-12-01','2029-12-01'),(9,'2025-06-26','05:11:56','93FF89E2','Dispatched','PRODUCT D','Free','2025-03-10','2026-03-10'),(10,'2025-06-26','05:12:01','93FF89E2','Dispatched','PRODUCT D','Free','2025-03-10','2026-03-10'),(11,'2025-06-26','05:12:06','40E7BA14','Dispatched','PRODUCT C','42','2025-01-15','2027-01-15'),(12,'2025-06-26','05:12:12','933497E2','Dispatched','PRODUCT B','1L','2024-12-01','2029-12-01'),(13,'2025-06-26','05:12:58','40E7BA14','Dispatched','PRODUCT C','42','2025-01-15','2027-01-15'),(14,'2025-06-26','05:58:48','40E7BA14','Dispatched','PRODUCT C','42','2025-01-15','2027-01-15'),(15,'2025-06-26','05:58:54','93FF89E2','Dispatched','PRODUCT D','Free','2025-03-10','2026-03-10'),(16,'2025-06-26','06:33:56','93FF89E2','Dispatched','PRODUCT D','Free','2025-03-10','2026-03-10'),(17,'2025-06-26','07:31:05','40E7BA14','Dispatched','PRODUCT C','42','2025-01-15','2027-01-15'),(18,'2025-06-26','07:31:05','933497E2','Dispatched','PRODUCT B','1L','2024-12-01','2029-12-01'),(19,'2025-06-26','07:31:05','933497E2','Dispatched','PRODUCT B','1L','2024-12-01','2029-12-01'),(20,'2025-06-26','07:31:05','933497E2','Dispatched','PRODUCT B','1L','2024-12-01','2029-12-01'),(21,'2025-06-26','07:31:05','933497E2','Dispatched','PRODUCT B','1L','2024-12-01','2029-12-01'),(22,'2025-06-26','08:20:03','40E7BA14','Dispatched','PRODUCT C','42','2025-01-15','2027-01-15'),(23,'2025-06-26','08:26:52','40E7BA14','Dispatched','PRODUCT C','42','2025-01-15','2027-01-15'),(24,'2025-06-26','08:31:13','40E7BA14','Dispatched','PRODUCT C','42','2025-01-15','2027-01-15'),(25,'2025-06-26','08:37:59','933497E2','Dispatched','PRODUCT B','1L','2024-12-01','2029-12-01'),(26,'2025-06-26','08:38:18','933497E2','Dispatched','PRODUCT B','1L','2024-12-01','2029-12-01'),(27,'2025-06-26','08:44:30','93FF89E2','Dispatched','PRODUCT D','Free','2025-03-10','2026-03-10'),(28,'2025-06-26','08:44:36','40E7BA14','Dispatched','PRODUCT C','42','2025-01-15','2027-01-15'),(29,'2025-06-26','08:44:42','40E7BA14','Dispatched','PRODUCT C','42','2025-01-15','2027-01-15'),(30,'2025-06-26','08:51:23','40E7BA14','Dispatched','PRODUCT C','42','2025-01-15','2027-01-15');
/*!40000 ALTER TABLE `rfid_logs` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-13  0:55:09
