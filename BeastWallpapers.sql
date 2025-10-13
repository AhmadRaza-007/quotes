-- MySQL dump 10.13  Distrib 8.0.43, for Linux (x86_64)
--
-- Host: localhost    Database: BeastWallpapers
-- ------------------------------------------------------
-- Server version	8.0.43-0ubuntu0.22.04.1

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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int NOT NULL DEFAULT '0',
  `position` int NOT NULL DEFAULT '0',
  `level` int NOT NULL DEFAULT '0',
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favourites`
--

DROP TABLE IF EXISTS `favourites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favourites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favourites`
--

LOCK TABLES `favourites` WRITE;
/*!40000 ALTER TABLE `favourites` DISABLE KEYS */;
/*!40000 ALTER TABLE `favourites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `follows`
--

DROP TABLE IF EXISTS `follows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `follows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `follower_id` bigint unsigned NOT NULL,
  `followee_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `follows_follower_id_followee_id_unique` (`follower_id`,`followee_id`),
  KEY `follows_followee_id_index` (`followee_id`),
  CONSTRAINT `follows_followee_id_foreign` FOREIGN KEY (`followee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `follows_follower_id_foreign` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `follows`
--

LOCK TABLES `follows` WRITE;
/*!40000 ALTER TABLE `follows` DISABLE KEYS */;
/*!40000 ALTER TABLE `follows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `likes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `wallpaper_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
INSERT INTO `likes` VALUES (1,3,2,'2025-09-18 13:59:51','2025-09-18 13:59:51'),(4,2,2,'2025-09-18 14:39:17','2025-09-18 14:39:17'),(5,2,1,'2025-09-21 11:41:12','2025-09-21 11:41:12');
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2024_02_22_090159_create_likes_table',1),(6,'2024_02_22_090238_create_favourites_table',1),(7,'2024_02_27_103253_create_wallpaper_categories_table',1),(8,'2024_02_29_114406_create_wallpaper_favourites_table',1),(9,'2024_02_29_114419_create_wallpaper_likes_table',1),(10,'2024_07_05_101345_create_wallpaper_comments_table',1),(11,'2024_07_11_073938_create_themes_table',1),(12,'2025_08_29_000001_add_media_fields_to_themes_table',1),(13,'2025_08_29_000002_create_wallpapers_table',1),(14,'2025_08_29_165216_categories',1),(15,'2025_09_01_000001_create_profile_posts_table',1),(16,'2025_09_01_000002_create_post_likes_table',1),(17,'2025_09_01_000003_create_post_comments_table',1),(18,'2025_09_01_000004_create_follows_table',1),(19,'2025_09_09_000003_add_owner_and_admin_to_wallpapers',1),(20,'2025_09_10_000001_add_hierarchy_to_categories',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (3,'App\\Models\\User',2,'test@test.com','5ae0575a1a3bffe15d88db3791bcaa78898bf14ca8d11ce547fee01b7013be41','[\"*\"]','2025-09-25 08:13:27','2025-09-18 14:39:01','2025-09-25 08:13:27'),(4,'App\\Models\\User',2,'test@test.com','49e46c429766ed4d237ad6340855b134b8c5ff4df83f9c1e8ecd89f319604a9b','[\"*\"]','2025-10-02 07:44:29','2025-09-21 11:56:20','2025-10-02 07:44:29'),(5,'App\\Models\\User',1,'admin@admin.com','35d6f7e5914d77672eac28a511d7bdd9f74aaff6244e9cb86959b2731d3bcbed','[\"*\"]',NULL,'2025-10-09 14:14:13','2025-10-09 14:14:13'),(6,'App\\Models\\User',1,'admin@admin.com','250ba3c9ef07987fd2fa44fdddc26f303e26d3450663618b6524aab7176be415','[\"*\"]','2025-10-10 15:24:15','2025-10-09 14:14:27','2025-10-10 15:24:15');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_comments`
--

DROP TABLE IF EXISTS `post_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `profile_post_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_comments_user_id_foreign` (`user_id`),
  KEY `post_comments_profile_post_id_created_at_index` (`profile_post_id`,`created_at`),
  CONSTRAINT `post_comments_profile_post_id_foreign` FOREIGN KEY (`profile_post_id`) REFERENCES `profile_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_comments`
--

LOCK TABLES `post_comments` WRITE;
/*!40000 ALTER TABLE `post_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_likes`
--

DROP TABLE IF EXISTS `post_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_likes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `profile_post_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_likes_user_id_profile_post_id_unique` (`user_id`,`profile_post_id`),
  KEY `post_likes_profile_post_id_index` (`profile_post_id`),
  CONSTRAINT `post_likes_profile_post_id_foreign` FOREIGN KEY (`profile_post_id`) REFERENCES `profile_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_likes`
--

LOCK TABLES `post_likes` WRITE;
/*!40000 ALTER TABLE `post_likes` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile_posts`
--

DROP TABLE IF EXISTS `profile_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile_posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `owner_user_id` bigint unsigned NOT NULL,
  `wallpaper_id` bigint unsigned NOT NULL,
  `caption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `likes_count` bigint unsigned NOT NULL DEFAULT '0',
  `comments_count` bigint unsigned NOT NULL DEFAULT '0',
  `shares_count` bigint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `profile_posts_owner_user_id_wallpaper_id_unique` (`owner_user_id`,`wallpaper_id`),
  KEY `profile_posts_owner_user_id_created_at_index` (`owner_user_id`,`created_at`),
  KEY `profile_posts_wallpaper_id_index` (`wallpaper_id`),
  CONSTRAINT `profile_posts_owner_user_id_foreign` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `profile_posts_wallpaper_id_foreign` FOREIGN KEY (`wallpaper_id`) REFERENCES `wallpapers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile_posts`
--

LOCK TABLES `profile_posts` WRITE;
/*!40000 ALTER TABLE `profile_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `profile_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reset_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin@admin.com',NULL,'1',NULL,'$2y$10$kUtvwtrjS01UuU73R5GAaeMazQ5KjUh2FzGo0D3bDCyF9UCrZ3kL2',NULL,NULL,'2025-09-18 13:29:40','2025-09-26 14:43:51');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wallpaper_categories`
--

DROP TABLE IF EXISTS `wallpaper_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallpaper_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `owner_user_id` bigint DEFAULT NULL,
  `depth` int NOT NULL DEFAULT '0',
  `order` int NOT NULL DEFAULT '0',
  `category_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wallpaper_categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `wallpaper_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `wallpaper_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wallpaper_categories`
--

LOCK TABLES `wallpaper_categories` WRITE;
/*!40000 ALTER TABLE `wallpaper_categories` DISABLE KEYS */;
INSERT INTO `wallpaper_categories` VALUES (11,NULL,NULL,0,1,'Wallpapers',1,'2025-09-30 11:05:29','2025-09-30 11:05:29'),(12,NULL,NULL,0,2,'Live Wallpapers',1,'2025-09-30 11:05:29','2025-09-30 11:05:29'),(20,12,1,1,0,'Cars',1,'2025-10-03 14:33:03','2025-10-03 14:33:03'),(21,12,1,1,0,'Shooting Wallpaper',1,'2025-10-04 06:50:55','2025-10-04 06:50:55'),(22,12,1,1,0,'Love',1,'2025-10-04 14:01:51','2025-10-04 14:01:51'),(23,12,1,1,0,'Don\'t Touch My Phone',1,'2025-10-04 15:04:14','2025-10-04 15:04:14');
/*!40000 ALTER TABLE `wallpaper_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wallpaper_comments`
--

DROP TABLE IF EXISTS `wallpaper_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallpaper_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `wallpaper_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wallpaper_comments`
--

LOCK TABLES `wallpaper_comments` WRITE;
/*!40000 ALTER TABLE `wallpaper_comments` DISABLE KEYS */;
INSERT INTO `wallpaper_comments` VALUES (6,2,2,'Hi','2025-09-18 14:38:21','2025-09-18 14:38:21'),(7,1,2,'hello','2025-09-18 14:39:10','2025-09-18 14:39:10');
/*!40000 ALTER TABLE `wallpaper_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wallpaper_favourites`
--

DROP TABLE IF EXISTS `wallpaper_favourites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallpaper_favourites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `wallpaper_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wallpaper_favourites`
--

LOCK TABLES `wallpaper_favourites` WRITE;
/*!40000 ALTER TABLE `wallpaper_favourites` DISABLE KEYS */;
/*!40000 ALTER TABLE `wallpaper_favourites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wallpapers`
--

DROP TABLE IF EXISTS `wallpapers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallpapers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'image',
  `thumbnail_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `owner_user_id` bigint unsigned DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `wallpapers_owner_user_id_foreign` (`owner_user_id`),
  CONSTRAINT `wallpapers_owner_user_id_foreign` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wallpapers`
--

LOCK TABLES `wallpapers` WRITE;
/*!40000 ALTER TABLE `wallpapers` DISABLE KEYS */;
INSERT INTO `wallpapers` VALUES (5,6,'Ahmad Raza','wallpapers/1758898579_Fxq9bi.jpg','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1758898579_Fxq9bi.jpg','image',NULL,'image/jpeg',212693,'2025-09-26 14:56:22','2025-09-26 14:56:22',NULL,1),(6,7,'wewdsaefd','wallpapers/1758904451_pKt2uP.webp','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1758904451_pKt2uP.webp','image',NULL,'image/webp',974494,'2025-09-26 16:34:24','2025-09-26 16:34:24',1,1),(7,6,'srdfghj','wallpapers/1758904492_0RaBoa.webp','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1758904492_0RaBoa.webp','image',NULL,'image/webp',264246,'2025-09-26 16:34:54','2025-09-26 16:34:54',1,1),(30,21,'0829 2','wallpapers/1759560905_NaKSeh.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759560905_NaKSeh.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759560906_0nmotM.jpg','video/mp4',1480377,'2025-10-04 06:55:07','2025-10-04 06:55:07',1,1),(31,21,'0829 4','wallpapers/1759560907_FsLLF6.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759560907_FsLLF6.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759560908_tDmAnh.jpg','video/mp4',1249759,'2025-10-04 06:55:08','2025-10-04 06:55:08',1,1),(32,21,'0829 5','wallpapers/1759560908_t2vaJy.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759560908_t2vaJy.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759560908_NI9CGf.jpg','video/mp4',467705,'2025-10-04 06:55:09','2025-10-04 06:55:09',1,1),(33,21,'0829(1) 2','wallpapers/1759560909_UMSV3B.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759560909_UMSV3B.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759560909_YJVSwA.jpg','video/mp4',884701,'2025-10-04 06:55:10','2025-10-04 06:55:10',1,1),(34,21,'0829(2) 2','wallpapers/1759560910_svSEmI.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759560910_svSEmI.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759560911_Gk4LGz.jpg','video/mp4',1075942,'2025-10-04 06:55:11','2025-10-04 06:55:11',1,1),(35,21,'0829(2) 3','wallpapers/1759560939_7rTkKP.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759560939_7rTkKP.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759560941_1fqc90.jpg','video/mp4',1185873,'2025-10-04 06:55:41','2025-10-04 06:55:41',1,1),(36,21,'0829(4) 2','wallpapers/1759560941_TsVSyO.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759560941_TsVSyO.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759560941_pmYZKn.jpg','video/mp4',715232,'2025-10-04 06:55:41','2025-10-04 06:55:41',1,1),(37,21,'0829(9) 2','wallpapers/1759560941_nFx2dv.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759560941_nFx2dv.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759560942_hMwe0f.jpg','video/mp4',849595,'2025-10-04 06:55:43','2025-10-04 06:55:43',1,1),(38,21,'0829(9)','wallpapers/1759560943_9KcWEZ.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759560943_9KcWEZ.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759560944_NJtuWO.jpg','video/mp4',1117180,'2025-10-04 06:55:44','2025-10-04 06:55:44',1,1),(39,21,'0829(20) 2','wallpapers/1759560944_pOOoKI.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759560944_pOOoKI.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759560944_f4BOdS.jpg','video/mp4',910727,'2025-10-04 06:55:45','2025-10-04 06:55:45',1,1),(40,21,'0829(20)','wallpapers/1759560961_QVXJCy.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759560961_QVXJCy.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759560962_pp9iZP.jpg','video/mp4',535451,'2025-10-04 06:56:02','2025-10-04 06:56:02',1,1),(41,21,'0829(22)','wallpapers/1759560962_FYJs3T.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759560962_FYJs3T.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759560962_JIXbPu.jpg','video/mp4',834929,'2025-10-04 06:56:03','2025-10-04 06:56:03',1,1),(42,21,'0829(28)','wallpapers/1759560963_ustPPH.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759560963_ustPPH.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759560963_Cu1nNN.jpg','video/mp4',438080,'2025-10-04 06:56:03','2025-10-04 06:56:03',1,1),(43,20,'0829(26) 2','wallpapers/1759561164_EV8Sb0.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759561164_EV8Sb0.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759561165_vcXnyj.jpg','video/mp4',274981,'2025-10-04 06:59:26','2025-10-04 06:59:26',1,1),(44,20,'0829(29) 2','wallpapers/1759561166_pfwH8O.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759561166_pfwH8O.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759561166_8e1qt7.jpg','video/mp4',123332,'2025-10-04 06:59:26','2025-10-04 06:59:26',1,1),(45,20,'0829(30)','wallpapers/1759561166_QDoCj6.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759561166_QDoCj6.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759561166_AbCAq5.jpg','video/mp4',535851,'2025-10-04 06:59:27','2025-10-04 06:59:27',1,1),(46,20,'0829(34)','wallpapers/1759561167_JFVbXP.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759561167_JFVbXP.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759561167_71WMZz.jpg','video/mp4',669968,'2025-10-04 06:59:27','2025-10-04 06:59:27',1,1),(47,20,'0829(37)','wallpapers/1759561167_bTGPri.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759561167_bTGPri.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759561167_NRTGQg.jpg','video/mp4',531256,'2025-10-04 06:59:28','2025-10-04 06:59:28',1,1),(48,20,'0829(39)','wallpapers/1759561185_tJFuhE.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759561185_tJFuhE.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759561186_cSl5Lp.jpg','video/mp4',982539,'2025-10-04 06:59:46','2025-10-04 06:59:46',1,1),(49,20,'0829(41)','wallpapers/1759561186_iUtKL1.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759561186_iUtKL1.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759561186_DPUCW7.jpg','video/mp4',745601,'2025-10-04 06:59:47','2025-10-04 06:59:47',1,1),(50,20,'0829(43)','wallpapers/1759561187_2bpKcD.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759561187_2bpKcD.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759561187_rn3OZa.jpg','video/mp4',459740,'2025-10-04 06:59:47','2025-10-04 06:59:47',1,1),(51,20,'0829(44)','wallpapers/1759561187_UsFW92.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759561187_UsFW92.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759561187_wc8nS3.jpg','video/mp4',102488,'2025-10-04 06:59:48','2025-10-04 06:59:48',1,1),(52,20,'0829(53)','wallpapers/1759561188_ovyQ7T.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759561188_ovyQ7T.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759561188_mZTdJQ.jpg','video/mp4',309998,'2025-10-04 06:59:48','2025-10-04 06:59:48',1,1),(53,20,'0829(54)','wallpapers/1759561188_cx9POS.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759561188_cx9POS.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759561188_6Dmkh1.jpg','video/mp4',473146,'2025-10-04 06:59:49','2025-10-04 06:59:49',1,1),(54,20,'0829(55)','wallpapers/1759561189_VYDywp.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759561189_VYDywp.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759561189_H8LByU.jpg','video/mp4',327164,'2025-10-04 06:59:50','2025-10-04 06:59:50',1,1),(55,22,'0829','wallpapers/1759586534_lVUKM6.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759586534_lVUKM6.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759586536_ZvM8ge.jpg','video/mp4',1422343,'2025-10-04 14:02:17','2025-10-04 14:02:17',1,1),(56,22,'0829(3)','wallpapers/1759587586_Lxx8Xg.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759587586_Lxx8Xg.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759587587_q677Qz.jpg','video/mp4',1166553,'2025-10-04 14:19:48','2025-10-04 14:19:48',1,1),(57,22,'0829(4)','wallpapers/1759588300_RRRFwh.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759588300_RRRFwh.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759588301_L2QyQH.jpg','video/mp4',1170162,'2025-10-04 14:31:42','2025-10-04 14:31:42',1,1),(58,22,'0829(6)','wallpapers/1759589655_PpG6O2.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759589655_PpG6O2.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759589656_Hw94SN.jpg','video/mp4',534965,'2025-10-04 14:54:16','2025-10-04 14:54:16',1,1),(59,22,'0829(8)','wallpapers/1759589911_CQUUri.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759589911_CQUUri.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759589912_H59Bzs.jpg','video/mp4',616652,'2025-10-04 14:58:32','2025-10-04 14:58:32',1,1),(60,23,'0829(9)','wallpapers/1759590272_oqZMxj.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759590272_oqZMxj.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759590273_A3lMk9.jpg','video/mp4',1287345,'2025-10-04 15:04:34','2025-10-04 15:04:34',1,1),(61,21,'0829(10)','wallpapers/1759590486_nTEI72.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759590486_nTEI72.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759590487_65cDrN.jpg','video/mp4',1379664,'2025-10-04 15:08:07','2025-10-04 15:08:07',1,1),(62,22,'0829(13)','wallpapers/1759591111_othkRZ.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759591111_othkRZ.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759591112_9FA1mv.jpg','video/mp4',1236301,'2025-10-04 15:18:32','2025-10-04 15:18:32',1,1),(63,22,'0829(14)','wallpapers/1759591441_ZVrUg2.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759591441_ZVrUg2.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759591442_b0oMZy.jpg','video/mp4',1332943,'2025-10-04 15:24:02','2025-10-04 15:24:02',1,1),(64,22,'0829(15)','wallpapers/1759591742_kx9jUl.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759591742_kx9jUl.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759591743_UkrtxV.jpg','video/mp4',1202811,'2025-10-04 15:29:04','2025-10-04 15:29:04',1,1),(65,22,'0829(17)','wallpapers/1759592144_sQeLiH.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759592144_sQeLiH.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759592145_aZwTuB.jpg','video/mp4',1334250,'2025-10-04 15:35:46','2025-10-04 15:35:46',1,1),(66,23,'0829(18)','wallpapers/1759592506_EqXTI0.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759592506_EqXTI0.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759592506_LLWRRS.jpg','video/mp4',439188,'2025-10-04 15:41:47','2025-10-04 15:41:47',1,1),(67,23,'0829(19)','wallpapers/1759592700_KprDW1.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759592700_KprDW1.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759592701_QVA6Y0.jpg','video/mp4',1108733,'2025-10-04 15:45:02','2025-10-04 15:45:02',1,1),(68,22,'0829(22)','wallpapers/1759773891_Su6FAj.mp4','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/1759773891_Su6FAj.mp4','video','https://s3.us-east-005.backblazeb2.com/beast-wallpaper/wallpapers/thumbnails/thumb_1759773892_39nxgE.jpg','video/mp4',1239882,'2025-10-06 18:04:53','2025-10-06 18:04:53',1,1);
/*!40000 ALTER TABLE `wallpapers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-10 15:40:34
