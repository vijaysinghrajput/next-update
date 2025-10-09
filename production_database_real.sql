-- MySQL dump 10.13  Distrib 9.4.0, for macos15.4 (arm64)
--
-- Host: localhost    Database: u715885454_next_update
-- ------------------------------------------------------
-- Server version	9.4.0

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
-- Table structure for table `ad_positions`
--

DROP TABLE IF EXISTS `ad_positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ad_positions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `position` enum('top_banner','bottom_banner','between_news','popup_modal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost_per_day` int NOT NULL DEFAULT '100',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `position` (`position`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_positions`
--

LOCK TABLES `ad_positions` WRITE;
/*!40000 ALTER TABLE `ad_positions` DISABLE KEYS */;
INSERT INTO `ad_positions` VALUES (1,'top_banner','Top Banner','Banner ad displayed at the top of the website',150,1,'2025-10-04 10:10:46','2025-10-04 10:10:46'),(2,'bottom_banner','Bottom Banner','Banner ad displayed at the bottom of the website',120,1,'2025-10-04 10:10:46','2025-10-04 10:10:46'),(3,'between_news','Between News','Ad displayed between news articles',200,1,'2025-10-04 10:10:46','2025-10-04 10:10:46'),(4,'popup_modal','Popup Modal','Popup ad that appears when app opens',300,1,'2025-10-04 10:10:46','2025-10-04 10:10:46');
/*!40000 ALTER TABLE `ad_positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ads`
--

DROP TABLE IF EXISTS `ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `position` enum('top_banner','bottom_banner','between_news','popup_modal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `heading` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `call_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_days` int NOT NULL DEFAULT '1',
  `cost_per_day` int NOT NULL DEFAULT '100',
  `total_cost` int NOT NULL DEFAULT '0',
  `status` enum('pending','approved','rejected','active','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `position` (`position`),
  KEY `status` (`status`),
  KEY `start_date` (`start_date`),
  KEY `end_date` (`end_date`),
  KEY `ads_admin_fk` (`approved_by`),
  CONSTRAINT `ads_admin_fk` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ads_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ads`
--

LOCK TABLES `ads` WRITE;
/*!40000 ALTER TABLE `ads` DISABLE KEYS */;
INSERT INTO `ads` VALUES (1,2,'between_news','34rdgdfgdf','ertertertetert','uploads/ads/68e0f99f469e7_1759574431.jpeg','+842-313-8649','+435-643-6654','https://tradygo.in',5,200,1000,'active','2025-10-04','2025-10-09','Test approval','2025-10-04 10:40:31','2025-10-05 11:20:08','2025-10-04 10:46:38',3),(2,2,'top_banner','संत कबीर फिल्म एंड टेलीविजन वेलफेयर – आपका फिल्मी भविष्य, हमारी जिम्मेदारी','dgsgfsfdsfdsfdsfdsfdsf','uploads/ads/68e0f9bb434a5_1759574459.png','+842-313-8649','+435-643-6654','',6,150,900,'active','2025-10-04','2025-10-10','rtrtr','2025-10-04 10:40:59','2025-10-05 11:20:08','2025-10-04 10:48:46',3),(3,2,'top_banner','Test Ad','This is a test advertisement',NULL,'','','',5,150,750,'rejected',NULL,NULL,'trrdt','2025-10-04 10:43:47','2025-10-04 10:48:41','2025-10-04 10:48:41',3),(4,2,'bottom_banner','संत कबीर फिल्म एंड टेलीविजन वेलफेयर – आपका फिल्मी भविष्य, हमारी जिम्मेदारी','vgdfsgvfdgvfvfvvvdfs','uploads/ads/68e253d793c8a_1759663063.jpeg','+842-313-8649','','https://tradygo.in',10,120,1200,'active','2025-10-05','2025-10-15','','2025-10-05 11:17:43','2025-10-05 11:20:08','2025-10-05 11:18:46',3),(5,2,'popup_modal','Hotel JS Inn: Where Comfort Meets Care in Kanpur','jtyjtyhjtyh','uploads/ads/68e253ff184c1_1759663103.jpeg','+842-313-8649','+435-643-6654','https://tradygo.in',10,300,3000,'active','2025-10-05','2025-10-15','','2025-10-05 11:18:23','2025-10-05 11:20:08','2025-10-05 11:18:49',3),(6,1,'top_banner','New Restaurant Opening','Best food in town, come visit us!','uploads/ads/68e0f9bb434a5_1759574459.png','+91-9876543210','+91-9876543210','https://restaurant.com',7,100,700,'active','2025-10-05','2025-10-12',NULL,'2025-10-05 11:25:59','2025-10-05 11:25:59',NULL,NULL),(7,1,'between_news','Tech Services','Professional IT services for your business','uploads/ads/68e0f99f469e7_1759574431.jpeg','+91-9876543211','+91-9876543211','https://techservices.com',5,50,250,'active','2025-10-05','2025-10-10',NULL,'2025-10-05 11:26:05','2025-10-05 11:26:05',NULL,NULL),(8,1,'popup_modal','Fitness Center','Join our gym for a healthy lifestyle','uploads/ads/68e253d793c8a_1759663063.jpeg','+91-9876543212','+91-9876543212','https://fitness.com',10,80,800,'active','2025-10-05','2025-10-15',NULL,'2025-10-05 11:26:10','2025-10-05 11:26:10',NULL,NULL),(9,1,'bottom_banner','Local Business Promotion','Support your local community businesses','uploads/ads/68e0f99f469e7_1759574431.jpeg','+91-9876543213','+91-9876543213','https://localbusiness.com',7,100,700,'active','2025-10-05','2025-10-12',NULL,'2025-10-05 12:06:50','2025-10-05 12:06:50',NULL,NULL),(10,1,'bottom_banner','Educational Services','Learn new skills with our courses','uploads/ads/68e253ff184c1_1759663103.jpeg','+91-9876543214','+91-9876543214','https://education.com',10,80,800,'active','2025-10-05','2025-10-15',NULL,'2025-10-05 12:06:57','2025-10-05 12:06:57',NULL,NULL);
/*!40000 ALTER TABLE `ads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Local News','local-news','Local community news and events',1,'2025-10-03 11:24:50'),(2,'Politics','politics','Political news and updates',1,'2025-10-03 11:24:50'),(3,'Sports','sports','Sports news and events',1,'2025-10-03 11:24:50'),(5,'Education','education','Educational news and updates',1,'2025-10-03 11:24:50'),(6,'Health','health','Health and medical news',1,'2025-10-03 11:24:50'),(7,'Entertainment','entertainment','Entertainment and cultural news',1,'2025-10-03 11:24:50'),(8,'Technology','technology','Technology news and updates',1,'2025-10-03 11:24:50');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `state` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cities`
--

LOCK TABLES `cities` WRITE;
/*!40000 ALTER TABLE `cities` DISABLE KEYS */;
INSERT INTO `cities` VALUES (1,'Bansgaon','Uttar Pradesh',1,'2025-10-03 11:24:50'),(2,'Gorakhpur','Uttar Pradesh',1,'2025-10-03 11:24:50'),(3,'Lucknow','Uttar Pradesh',1,'2025-10-03 11:24:50'),(4,'Varanasi','Uttar Pradesh',1,'2025-10-03 11:24:50'),(5,'Kanpur','Uttar Pradesh',1,'2025-10-03 11:24:50'),(6,'Agra','Uttar Pradesh',1,'2025-10-03 11:24:50'),(7,'Meerut','Uttar Pradesh',1,'2025-10-03 11:24:50'),(8,'Allahabad','Uttar Pradesh',1,'2025-10-03 11:24:50');
/*!40000 ALTER TABLE `cities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kyc_verifications`
--

DROP TABLE IF EXISTS `kyc_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kyc_verifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `document_type` enum('aadhar','pan','driving_license','passport','voter_id') NOT NULL,
  `document_number` varchar(100) NOT NULL,
  `document_image` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_notes` text,
  `verified_by` int DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `verified_by` (`verified_by`),
  CONSTRAINT `kyc_verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `kyc_verifications_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kyc_verifications`
--

LOCK TABLES `kyc_verifications` WRITE;
/*!40000 ALTER TABLE `kyc_verifications` DISABLE KEYS */;
INSERT INTO `kyc_verifications` VALUES (1,1,'aadhar','123456789012','test-image.jpg','approved',NULL,3,'2025-10-04 04:31:13','2025-10-04 09:47:10'),(2,2,'pan','4353453455534353','uploads/kyc/68e0ed2ce9e28_1759571244.png','rejected','Invalid document',3,'2025-10-04 04:31:17','2025-10-04 09:47:24'),(3,1,'pan','ABCDE1234F','test-pan.jpg','approved',NULL,3,'2025-10-04 04:26:45','2025-10-04 09:52:11'),(4,2,'driving_license','tutyutut','uploads/kyc/68e0efaddccb0_1759571885.png','approved',NULL,3,'2025-10-04 04:29:38','2025-10-04 09:58:05'),(5,2,'driving_license','tutyutut','uploads/kyc/68e0efb229ecd_1759571890.png','rejected','rhtyhrty',3,'2025-10-04 04:29:54','2025-10-04 09:58:10'),(6,2,'driving_license','tutyutut','uploads/kyc/68e0efb3a49c3_1759571891.png','rejected','jtyhty',3,'2025-10-04 04:29:45','2025-10-04 09:58:11'),(7,2,'passport','4353453455534353','uploads/kyc/68e28bfced10d_1759677436.png','pending',NULL,NULL,NULL,'2025-10-05 15:17:16');
/*!40000 ALTER TABLE `kyc_verifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news_articles`
--

DROP TABLE IF EXISTS `news_articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `news_articles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `excerpt` text,
  `featured_image` varchar(255) DEFAULT NULL,
  `external_link` varchar(500) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `city_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `author_id` int DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  `is_published` tinyint(1) DEFAULT '0',
  `is_bansgaonsandesh` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `views` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  KEY `city_id` (`city_id`),
  KEY `author_id` (`author_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `news_articles_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `news_articles_ibfk_2` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`),
  CONSTRAINT `news_articles_ibfk_3` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
  CONSTRAINT `news_articles_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news_articles`
--

LOCK TABLES `news_articles` WRITE;
/*!40000 ALTER TABLE `news_articles` DISABLE KEYS */;
INSERT INTO `news_articles` VALUES (1,'Test News Article','test-news-article','This is a test news article with local information about our community. It contains important updates and news that everyone should know about.','This is a test news article with local information about our community. It contains important updates and news that everyone should know about.',NULL,'',1,1,7,NULL,0,1,0,1,0,'2025-10-03 10:18:03','2025-10-03 15:48:03'),(2,'hhhhhh','hhhhhh','hgyg hvggvh hgygygy hbhggyg bhggf hgyff hvhyfgv hvfgg hgfg','hgyg hvggvh hgygygy hbhggyg bhggf hgyff hvhyfgv hvfgg hgfg',NULL,'',5,1,2,NULL,0,1,0,0,0,'2025-10-03 10:19:11','2025-10-06 05:39:58'),(3,'Test News with Image','test-news-with-image','This is a test news article with an image upload. It demonstrates the image upload functionality working correctly.','This is a test news article with an image upload. It demonstrates the image upload functionality working correctly.','uploads/news/68dff0ef151ad_1759506671.png','',1,1,7,NULL,1,1,0,1,2,'2025-10-03 10:21:11','2025-10-05 14:47:38'),(4,'1111','1111','dddd','dddd','uploads/news/68dff22d68692_1759506989.png','',5,1,2,NULL,1,1,0,0,0,'2025-10-03 10:26:29','2025-10-06 05:39:53'),(5,'jjjj','jjjj','jjbjj','jjbjj','uploads/news/68dff2acb90e0_1759507116.jpeg','',7,5,2,NULL,1,1,0,0,4,'2025-10-03 10:28:36','2025-10-06 05:39:56'),(6,'Test Edit Delete','test-edit-delete','This is a test article for edit and delete functionality.','This is a test article for edit and delete functionality.',NULL,'',1,1,7,NULL,0,1,0,1,0,'2025-10-03 10:41:39','2025-10-05 12:03:39'),(7,'dddd','dddd','ssssss','ssssss','uploads/news/68dff6c60b7ff_1759508166.png','',3,3,2,NULL,0,1,0,0,0,'2025-10-03 10:46:06','2025-10-06 05:39:50'),(8,'Test Dynamic Points','test-dynamic-points','This is a test to verify dynamic points update.','This is a test to verify dynamic points update.',NULL,'',1,1,7,NULL,0,1,0,1,0,'2025-10-03 10:48:41','2025-10-05 12:03:39');
/*!40000 ALTER TABLE `news_articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `used` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `point_transactions`
--

DROP TABLE IF EXISTS `point_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `point_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `points` int NOT NULL,
  `transaction_type` enum('earned','spent') NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `point_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `point_transactions`
--

LOCK TABLES `point_transactions` WRITE;
/*!40000 ALTER TABLE `point_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `point_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `points_packages`
--

DROP TABLE IF EXISTS `points_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `points_packages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `points` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `bonus_points` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `points_packages`
--

LOCK TABLES `points_packages` WRITE;
/*!40000 ALTER TABLE `points_packages` DISABLE KEYS */;
INSERT INTO `points_packages` VALUES (1,'Starter Pack',100,10.00,10,1,'2025-10-03 11:41:39'),(2,'Popular Pack',500,45.00,50,1,'2025-10-03 11:41:39'),(3,'Pro Pack',1000,85.00,150,1,'2025-10-03 11:41:39'),(4,'Premium Pack',2500,200.00,500,1,'2025-10-03 11:41:39');
/*!40000 ALTER TABLE `points_packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referrals`
--

DROP TABLE IF EXISTS `referrals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `referrals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `referrer_id` int NOT NULL,
  `referred_id` int NOT NULL,
  `points_earned` int DEFAULT '10',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `referrer_id` (`referrer_id`),
  KEY `referred_id` (`referred_id`),
  CONSTRAINT `referrals_ibfk_1` FOREIGN KEY (`referrer_id`) REFERENCES `users` (`id`),
  CONSTRAINT `referrals_ibfk_2` FOREIGN KEY (`referred_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referrals`
--

LOCK TABLES `referrals` WRITE;
/*!40000 ALTER TABLE `referrals` DISABLE KEYS */;
/*!40000 ALTER TABLE `referrals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_activity_log`
--

DROP TABLE IF EXISTS `user_activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_activity_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `activity_type` varchar(100) NOT NULL,
  `description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_activity_log`
--

LOCK TABLES `user_activity_log` WRITE;
/*!40000 ALTER TABLE `user_activity_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_ads`
--

DROP TABLE IF EXISTS `user_ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_ads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `points_cost` int NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `views` int DEFAULT '0',
  `clicks` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_ads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_ads`
--

LOCK TABLES `user_ads` WRITE;
/*!40000 ALTER TABLE `user_ads` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_ads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_notifications`
--

DROP TABLE IF EXISTS `user_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_notifications`
--

LOCK TABLES `user_notifications` WRITE;
/*!40000 ALTER TABLE `user_notifications` DISABLE KEYS */;
INSERT INTO `user_notifications` VALUES (1,2,'Payment Approved!','Your payment of ₹600.00 has been approved and 600 points have been added to your account.','success',0,'2025-10-04 09:14:20'),(2,2,'Payment Rejected','Your payment of ₹600.00 has been rejected. Reason: Test rejection - invalid payment screenshot','error',0,'2025-10-04 09:14:27'),(3,2,'Payment Rejected','Your payment of ₹600.00 has been rejected. Reason: dddde','error',0,'2025-10-04 09:14:28'),(4,2,'Payment Approved!','Your payment of ₹600000.00 has been approved and 600000 points have been added to your account.','success',0,'2025-10-04 09:14:34'),(5,2,'Payment Rejected','Your payment of ₹600000.00 has been rejected. Reason: ferer','error',0,'2025-10-04 09:14:46'),(6,2,'Payment Rejected','Your payment of ₹600000.00 has been rejected. Reason: rerr','error',0,'2025-10-04 09:14:49'),(7,2,'Payment Approved!','Your payment of ₹600000.00 has been approved and 600000 points have been added to your account.','success',0,'2025-10-04 09:14:51'),(8,2,'Payment Rejected','Your payment of ₹600000.00 has been rejected. Reason: rerre','error',0,'2025-10-04 09:14:53'),(9,2,'Payment Rejected','Your payment of ₹600000.00 has been rejected. Reason: erre','error',0,'2025-10-04 09:14:55'),(10,2,'Payment Rejected','Your payment of ₹600000.00 has been rejected. Reason: erre','error',0,'2025-10-04 09:16:32'),(11,2,'Payment Rejected','Your payment of ₹600000.00 has been rejected. Reason: okk','error',0,'2025-10-04 09:26:47'),(12,2,'Payment Rejected','Your payment of ₹600000.00 has been rejected. Reason: okk','error',0,'2025-10-04 09:26:51'),(13,2,'Payment Rejected','Your payment of ₹600000.00 has been rejected. Reason: okk','error',0,'2025-10-04 09:27:01'),(14,2,'Payment Rejected','Your payment of ₹600000.00 has been rejected. Reason: okk','error',0,'2025-10-04 09:27:34'),(15,2,'Payment Rejected','Your payment of ₹600000.00 has been rejected. Reason: yutjy','error',0,'2025-10-04 09:28:34'),(16,2,'Payment Rejected','Your payment of ₹600000.00 has been rejected. Reason: tutyu','error',0,'2025-10-04 09:28:37'),(17,2,'Payment Rejected','Your payment of ₹600000.00 has been rejected. Reason: tutyu','error',0,'2025-10-04 09:28:40'),(18,2,'Payment Rejected','Your payment of ₹600000.00 has been rejected. Reason: tutyu','error',0,'2025-10-04 09:29:15'),(19,2,'Payment Rejected','Your payment of ₹600000.00 has been rejected. Reason: tutyu','error',0,'2025-10-04 09:29:24'),(20,2,'Payment Rejected','Your payment of ₹600000.00 has been rejected. Reason: tutyu','error',0,'2025-10-04 09:30:39'),(21,1,'KYC Verification Submitted','Your KYC verification has been submitted and is under review.','info',0,'2025-10-04 09:47:10'),(22,2,'KYC Verification Submitted','Your KYC verification has been submitted and is under review.','info',0,'2025-10-04 09:47:24'),(23,1,'KYC Verification Submitted','Your KYC verification has been submitted and is under review. You spent 50 points.','info',0,'2025-10-04 09:52:11'),(24,1,'KYC Verification Approved!','Your KYC verification has been approved. Your account is now verified.','success',0,'2025-10-04 09:56:28'),(25,2,'KYC Verification Rejected','Your KYC verification has been rejected. Reason: Invalid document Your 50 points have been returned.','error',0,'2025-10-04 09:56:31'),(26,1,'KYC Verification Approved!','Your KYC verification has been approved. Your account is now verified.','success',0,'2025-10-04 09:56:45'),(27,2,'KYC Verification Submitted','Your KYC verification has been submitted and is under review. You spent 50 points.','info',0,'2025-10-04 09:58:05'),(28,2,'KYC Verification Submitted','Your KYC verification has been submitted and is under review. You spent 50 points.','info',0,'2025-10-04 09:58:10'),(29,2,'KYC Verification Submitted','Your KYC verification has been submitted and is under review. You spent 50 points.','info',0,'2025-10-04 09:58:11'),(30,1,'KYC Verification Approved!','Your KYC verification has been approved. Your account is now verified.','success',0,'2025-10-04 09:58:25'),(31,2,'KYC Verification Approved!','Your KYC verification has been approved. Your account is now verified.','success',0,'2025-10-04 09:59:38'),(32,2,'KYC Verification Rejected','Your KYC verification has been rejected. Reason: jtyhty Your 50 points have been returned.','error',0,'2025-10-04 09:59:45'),(33,2,'KYC Verification Rejected','Your KYC verification has been rejected. Reason: rhtyhrty Your 50 points have been returned.','error',0,'2025-10-04 09:59:54'),(34,1,'KYC Verification Approved!','Your KYC verification has been approved. Your account is now verified.','success',0,'2025-10-04 10:01:13'),(35,2,'KYC Verification Rejected','Your KYC verification has been rejected. Reason: Invalid document Your 50 points have been returned.','error',0,'2025-10-04 10:01:17'),(36,2,'KYC Verification Submitted','Your KYC verification has been submitted and is under review. You spent 50 points.','info',0,'2025-10-05 15:17:16');
/*!40000 ALTER TABLE `user_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_payments`
--

DROP TABLE IF EXISTS `user_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `points` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_screenshot` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `rejection_reason` text,
  `approved_by` int DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `approved_by` (`approved_by`),
  CONSTRAINT `user_payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_payments_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_payments`
--

LOCK TABLES `user_payments` WRITE;
/*!40000 ALTER TABLE `user_payments` DISABLE KEYS */;
INSERT INTO `user_payments` VALUES (1,2,600,600.00,'uploads/payments/68dffa63cbb61_1759509091.png','rejected','dddde',NULL,'2025-10-04 03:44:20','2025-10-04 03:44:28','2025-10-03 11:01:31','2025-10-04 09:14:28'),(2,2,600,600.00,'uploads/payments/68dffadccae04_1759509212.png','rejected','Test rejection - invalid payment screenshot',NULL,NULL,'2025-10-04 03:44:27','2025-10-03 11:03:32','2025-10-04 09:14:27'),(3,2,600000,600000.00,'uploads/payments/68e0e3fda4c83_1759568893.png','approved',NULL,NULL,'2025-10-04 03:44:34',NULL,'2025-10-04 03:38:13','2025-10-04 09:14:34'),(4,2,600000,600000.00,'uploads/payments/68e0e40716982_1759568903.png','rejected','ferer',NULL,NULL,'2025-10-04 03:44:46','2025-10-04 03:38:23','2025-10-04 09:14:46'),(5,2,600000,600000.00,'uploads/payments/68e0e4117ea2d_1759568913.png','rejected','rerr',NULL,NULL,'2025-10-04 03:44:49','2025-10-04 03:38:33','2025-10-04 09:14:49'),(6,2,600000,600000.00,'uploads/payments/68e0e41909607_1759568921.png','approved',NULL,NULL,'2025-10-04 03:44:51',NULL,'2025-10-04 03:38:41','2025-10-04 09:14:51'),(7,2,600000,600000.00,'uploads/payments/68e0e422666eb_1759568930.png','rejected','rerre',NULL,NULL,'2025-10-04 03:44:53','2025-10-04 03:38:50','2025-10-04 09:14:53'),(8,2,600000,600000.00,'uploads/payments/68e0e4d519200_1759569109.png','rejected','erre',NULL,NULL,'2025-10-04 03:46:32','2025-10-04 03:41:49','2025-10-04 09:16:32'),(9,2,600000,600000.00,'uploads/payments/68e0e600948d9_1759569408.png','rejected','okk',NULL,NULL,'2025-10-04 03:57:34','2025-10-04 03:46:48','2025-10-04 09:27:34'),(10,2,600000,600000.00,'uploads/payments/68e0e8ae32b63_1759570094.png','rejected','yutjy',NULL,NULL,'2025-10-04 03:58:34','2025-10-04 03:58:14','2025-10-04 09:28:34'),(11,2,600000,600000.00,'uploads/payments/68e0e8b1419b8_1759570097.png','rejected','tutyu',NULL,NULL,'2025-10-04 04:00:39','2025-10-04 03:58:17','2025-10-04 09:30:39');
/*!40000 ALTER TABLE `user_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_transactions`
--

DROP TABLE IF EXISTS `user_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `transaction_type` enum('earned','spent','purchased','bonus','refund') NOT NULL,
  `points` int NOT NULL,
  `description` varchar(255) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `status` enum('pending','completed','failed','cancelled') DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_transactions`
--

LOCK TABLES `user_transactions` WRITE;
/*!40000 ALTER TABLE `user_transactions` DISABLE KEYS */;
INSERT INTO `user_transactions` VALUES (1,2,'earned',10,'Welcome bonus','signup',2,'completed','2025-10-03 11:56:32'),(2,4,'earned',10,'Welcome bonus','signup',4,'completed','2025-10-03 15:29:11'),(3,5,'earned',10,'Welcome bonus','signup',5,'completed','2025-10-03 15:29:37'),(4,6,'earned',10,'Welcome bonus','signup',6,'completed','2025-10-03 15:30:17'),(5,7,'earned',10,'Welcome bonus','signup',7,'completed','2025-10-03 15:31:07'),(6,7,'earned',10,'Referral bonus','referral',3,'completed','2025-10-03 15:31:07'),(7,7,'earned',10,'News post reward','news',1,'completed','2025-10-03 15:48:03'),(8,2,'earned',10,'News post reward','news',2,'completed','2025-10-03 15:49:11'),(9,7,'earned',10,'News post reward','news',3,'completed','2025-10-03 15:51:11'),(10,2,'earned',10,'News post reward','news',4,'completed','2025-10-03 15:56:29'),(11,2,'earned',10,'News post reward','news',5,'completed','2025-10-03 15:58:36'),(12,7,'spent',10,'News article deletion - points refunded','news',3,'completed','2025-10-03 16:02:14'),(13,7,'spent',10,'News article deletion - points refunded','news',3,'completed','2025-10-03 16:02:31'),(14,7,'spent',10,'News article deletion - points refunded','news',3,'completed','2025-10-03 16:02:40'),(15,7,'spent',10,'News article deletion - points refunded','news',3,'completed','2025-10-03 16:02:55'),(16,2,'spent',10,'News article deletion - points refunded','news',4,'completed','2025-10-03 16:04:36'),(17,7,'spent',0,'News article deletion - no points to deduct','news',3,'completed','2025-10-03 16:04:39'),(18,7,'spent',0,'News article deletion - no points to deduct','news',3,'completed','2025-10-03 16:05:01'),(19,7,'spent',0,'News article deletion - no points to deduct','news',3,'completed','2025-10-03 16:05:08'),(20,7,'spent',0,'News article deletion - no points to deduct','news',3,'completed','2025-10-03 16:05:43'),(21,7,'spent',0,'News article deletion - no points to deduct','news',3,'completed','2025-10-03 16:06:43'),(22,7,'spent',0,'News article deletion - no points to deduct','news',3,'completed','2025-10-03 16:06:50'),(23,7,'spent',0,'News article deletion - no points to deduct','news',3,'completed','2025-10-03 16:07:04'),(24,7,'spent',0,'News article deletion - no points to deduct','news',3,'completed','2025-10-03 16:07:14'),(25,7,'spent',0,'News article deletion - no points to deduct','news',3,'completed','2025-10-03 16:07:39'),(26,7,'spent',0,'News article deletion - no points to deduct','news',3,'completed','2025-10-03 16:07:58'),(27,7,'spent',0,'News article deletion - no points to deduct','news',3,'completed','2025-10-03 16:08:23'),(28,7,'spent',0,'News article deletion - no points to deduct','news',3,'completed','2025-10-03 16:08:58'),(29,7,'spent',0,'News article deletion - no points to deduct','news',3,'completed','2025-10-03 16:09:11'),(30,7,'spent',0,'News article deletion - no points to deduct','news',3,'completed','2025-10-03 16:09:21'),(31,7,'spent',0,'News article deletion - no points to deduct','news',3,'completed','2025-10-03 16:09:26'),(32,7,'spent',0,'News article deletion - no points to deduct','news',3,'completed','2025-10-03 16:11:07'),(33,7,'earned',10,'News post reward','news',6,'completed','2025-10-03 16:11:39'),(34,7,'spent',10,'News article deletion - points refunded','news',6,'completed','2025-10-03 16:11:53'),(35,2,'spent',10,'News article deletion - points refunded','news',4,'completed','2025-10-03 16:15:35'),(36,2,'spent',10,'News article deletion - points refunded','news',2,'completed','2025-10-03 16:15:39'),(37,2,'earned',10,'News post reward','news',7,'completed','2025-10-03 16:16:06'),(38,7,'earned',10,'News post reward','news',8,'completed','2025-10-03 16:18:41'),(39,7,'spent',10,'News article deletion - points refunded','news',8,'completed','2025-10-03 16:19:01'),(42,2,'earned',600,'Points purchased via UPI payment','payment',1,'completed','2025-10-04 09:14:20'),(43,2,'earned',600000,'Points purchased via UPI payment','payment',3,'completed','2025-10-04 09:14:34'),(44,2,'earned',600000,'Points purchased via UPI payment','payment',6,'completed','2025-10-04 09:14:51'),(45,1,'spent',50,'KYC verification fee','kyc',3,'completed','2025-10-04 09:52:11'),(46,2,'earned',50,'KYC verification rejected - points returned','kyc_refund',2,'completed','2025-10-04 09:56:31'),(47,2,'spent',50,'KYC verification fee','kyc',4,'completed','2025-10-04 09:58:05'),(48,2,'spent',50,'KYC verification fee','kyc',5,'completed','2025-10-04 09:58:10'),(49,2,'spent',50,'KYC verification fee','kyc',6,'completed','2025-10-04 09:58:11'),(50,2,'earned',50,'KYC verification rejected - points returned','kyc_refund',6,'completed','2025-10-04 09:59:45'),(51,2,'earned',50,'KYC verification rejected - points returned','kyc_refund',5,'completed','2025-10-04 09:59:54'),(52,2,'earned',50,'KYC verification rejected - points returned','kyc_refund',2,'completed','2025-10-04 10:01:17'),(53,2,'spent',900,'Ad purchase - 900 points','ad',2,'completed','2025-10-04 10:48:46'),(54,2,'spent',1200,'Ad purchase - 1200 points','ad',4,'completed','2025-10-05 11:18:46'),(55,2,'spent',3000,'Ad purchase - 3000 points','ad',5,'completed','2025-10-05 11:18:49'),(56,2,'spent',10,'News article deletion - points refunded','news',7,'completed','2025-10-05 11:37:17'),(57,2,'spent',10,'News article deletion - points refunded','news',5,'completed','2025-10-05 11:37:20'),(58,2,'spent',50,'KYC verification fee','kyc',7,'completed','2025-10-05 15:17:16'),(59,2,'spent',10,'News article deletion - points refunded','news',7,'completed','2025-10-06 05:39:50'),(60,2,'spent',10,'News article deletion - points refunded','news',4,'completed','2025-10-06 05:39:53'),(61,2,'spent',10,'News article deletion - points refunded','news',5,'completed','2025-10-06 05:39:56'),(62,2,'spent',10,'News article deletion - points refunded','news',2,'completed','2025-10-06 05:39:58');
/*!40000 ALTER TABLE `user_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `referral_code` varchar(20) DEFAULT NULL,
  `referred_by` varchar(20) DEFAULT NULL,
  `points` int DEFAULT '0',
  `is_admin` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `email_verified` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_verified` tinyint(1) DEFAULT '0',
  `kyc_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `kyc_document` varchar(255) DEFAULT NULL,
  `verification_points_spent` int DEFAULT '0',
  `total_earned_points` int DEFAULT '0',
  `total_spent_points` int DEFAULT '0',
  `last_login` timestamp NULL DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `bio` text,
  `social_links` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `referral_code` (`referral_code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'bansgaonsandesh','admin@bansgaonsandesh.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Bansgaonsandesh Admin',NULL,NULL,NULL,NULL,0,1,1,1,'2025-10-03 11:24:50','2025-10-04 09:56:28',1,'approved',NULL,100,0,100,NULL,NULL,NULL,NULL),(2,'ertete','iamvijaysinghrajput@gmail.com','$2y$12$lb7q/BYs9kZmeqYrin5jsu0IjmhXEcFtCY3ehLkSqBO/pRVjBfZwG','Vijay Singh','08052553000','2','BE13ADE6','',1194410,0,1,0,'2025-10-03 06:26:32','2025-10-06 05:39:58',1,'pending',NULL,250,1200850,6440,NULL,NULL,NULL,NULL),(3,'admin','admin@gmail.com','$2y$12$ebuWE5osQsDN3yEk4vMP..KIgw1ilvhTNLpnvIIE8/YlszqqtgqnG','Admin User','1234567890','Admin City','ADMIN001',NULL,1000,1,1,1,'2025-10-03 12:07:01','2025-10-03 12:07:01',1,'approved',NULL,0,1000,0,NULL,NULL,NULL,NULL),(4,'testuser','test@example.com','$2y$12$b86awCKu4GbiPcyMxauk5.ZQjEoKpOFHSDxI5cxhoBe0oDf55gLvy','Test User','1234567890','1','1E40D4B0','',10,0,1,0,'2025-10-03 09:59:11','2025-10-03 15:29:11',0,'pending',NULL,0,10,0,NULL,NULL,NULL,NULL),(5,'testuser3','test3@example.com','$2y$12$gdutTswYKWsskImxTVNV5eepcP53bLTaYofNtL1np6ou4KdcOqYx6','Test User 3','1234567892','1','99A89771','ADMIN001',10,0,1,0,'2025-10-03 09:59:37','2025-10-03 15:29:37',0,'pending',NULL,0,10,0,NULL,NULL,NULL,NULL),(6,'testuser4','test4@example.com','$2y$12$iFVSmoTWduf1Fz2jcReLVO8tFV0LOkcTTHNyI2NtYTJ4MP/pjDeRa','Test User 4','1234567893','1','62C42B3D','ADMIN001',10,0,1,0,'2025-10-03 10:00:17','2025-10-03 15:30:17',0,'pending',NULL,0,10,0,NULL,NULL,NULL,NULL),(7,'testuser5','test5@example.com','$2y$12$L6BHQD4muY0zqsAMFHFWGeR/Am/xBRj9X2OsOV1NTa/RWallAexoC','Test User 5','1234567894','1','65FBAB26','ADMIN001',0,0,1,0,'2025-10-03 10:01:07','2025-10-03 16:19:01',0,'pending',NULL,0,60,60,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-06 11:29:34
