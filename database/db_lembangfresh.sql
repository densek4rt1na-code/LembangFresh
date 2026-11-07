-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: db_lembangfresh
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `demands`
--

DROP TABLE IF EXISTS `demands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `demands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `buyer_id` bigint(20) unsigned NOT NULL,
  `commodity` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `grade` enum('A','B','C') NOT NULL DEFAULT 'B',
  `max_price_per_kg` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `status` enum('terbuka','terpenuhi','dibatalkan') NOT NULL DEFAULT 'terbuka',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `demands_buyer_id_foreign` (`buyer_id`),
  CONSTRAINT `demands_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `demands`
--

LOCK TABLES `demands` WRITE;
/*!40000 ALTER TABLE `demands` DISABLE KEYS */;
INSERT INTO `demands` VALUES (1,4,'Wortel Brastagi Lembang',300,'B',5000,'Kebutuhan mingguan salad box fresh untuk katering sehat Lembang.',NULL,'terbuka','2026-11-07 08:47:14','2026-11-07 08:47:14'),(2,4,'Tomat Beef & Cherry Lembang',200,'A',9000,'Untuk sajian fresh salad buah dan sayur di restoran.',NULL,'terbuka','2026-11-07 08:47:14','2026-11-07 08:47:14'),(3,4,'Kentang Granola Lembang',400,'A',13000,'Untuk menu mashed potato dan french fries organik.',NULL,'terbuka','2026-11-07 08:47:14','2026-11-07 08:47:14');
/*!40000 ALTER TABLE `demands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
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
-- Table structure for table `harvests`
--

DROP TABLE IF EXISTS `harvests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `harvests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `farmer_id` bigint(20) unsigned NOT NULL,
  `commodity` varchar(255) NOT NULL,
  `variety` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_kg` int(11) NOT NULL,
  `grade` enum('A','B','C') NOT NULL DEFAULT 'B',
  `marketing_channel` varchar(255) NOT NULL,
  `harvest_date` date NOT NULL,
  `ready_date` date NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('tersedia','sebagian_terjual','habis') NOT NULL DEFAULT 'tersedia',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `harvests_farmer_id_foreign` (`farmer_id`),
  CONSTRAINT `harvests_farmer_id_foreign` FOREIGN KEY (`farmer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `harvests`
--

LOCK TABLES `harvests` WRITE;
/*!40000 ALTER TABLE `harvests` DISABLE KEYS */;
INSERT INTO `harvests` VALUES (1,2,'Wortel Brastagi Lembang','Brastagi Jumbo',500,4500,'B','Pasar Alternatif (UMKM, Rumah Makan, Katering)','2026-11-05','2026-11-08','harvests/wortel.png','Wortel segar hasil kebun Cikidang Lembang. Bentuk tidak 100% seragam namun rasa sangat manis, renyah, dan cocok untuk jus maupun konsumsi UMKM katering.','tersedia','2026-11-07 08:47:14','2026-11-07 08:47:14'),(2,2,'Tomat Beef & Cherry Lembang','Beef Super',350,8000,'A','Pasar Normal (Supermarket, Hotel, Restoran)','2026-11-06','2026-11-09','harvests/tomat.png','Tomat kualitas Grade A dengan tingkat kematangan seragam 85%, kulit mulus tanpa cacat fisik, siap untuk suplai hotel, restoran, dan supermarket.','tersedia','2026-11-07 08:47:14','2026-11-07 08:47:14'),(3,3,'Kentang Granola Lembang','Granola Cibodas',1200,12000,'A','Pasar Normal (Supermarket, Hotel, Restoran)','2026-11-04','2026-11-10','harvests/kentang.png','Kentang dataran tinggi Cibodas Lembang ukuran seragam (Grade A), kadar air rendah, sangat cocok untuk perkedel, olahan resto, dan pasar modern.','tersedia','2026-11-07 08:47:14','2026-11-07 08:47:14'),(4,2,'Brokoli Hijau Super','Green Magic',250,15000,'A','Pasar Normal (Supermarket, Hotel, Restoran)','2026-11-07','2026-11-08','harvests/brokoli.png','Brokoli hijau segar petik pagi dari kebun lereng Tangkuban Perahu. Kuntum rapat padat, bebas ulat, kemasan peti kayu berpendingin.','tersedia','2026-11-07 08:47:14','2026-11-07 08:47:14'),(5,3,'Selada Keriting & Romaine','Grand Rapids',400,5000,'C','Jalur Pengolahan (Industri Olahan Makanan)','2026-11-06','2026-11-07','harvests/selada.png','Selada daun segar dengan sedikit sobekan visual (Grade C). Sangat higienis dan aman pangan, diarahkan untuk pabrik olahan jus sayur atau pakan olahan bernutrisi.','tersedia','2026-11-07 08:47:14','2026-11-07 08:47:14'),(6,3,'Stroberi Lembang Segar','Sweet Charlie',180,35000,'B','Pasar Alternatif (UMKM, Rumah Makan, Katering)','2026-11-07','2026-11-08','harvests/stroberi.png','Stroberi petik langsung, ukuran campur manis segar, cocok untuk UMKM pembuat selai, smoothie, atau bakery.','tersedia','2026-11-07 08:47:14','2026-11-07 08:47:14');
/*!40000 ALTER TABLE `harvests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logistics_shipments`
--

DROP TABLE IF EXISTS `logistics_shipments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logistics_shipments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tracking_code` varchar(255) NOT NULL,
  `mitra_id` bigint(20) unsigned DEFAULT NULL,
  `sender_name` varchar(255) NOT NULL,
  `receiver_name` varchar(255) NOT NULL,
  `commodity_info` varchar(255) NOT NULL,
  `weight_kg` int(11) NOT NULL DEFAULT 100,
  `distance_km` double NOT NULL DEFAULT 1,
  `shipping_method` varchar(255) NOT NULL DEFAULT 'mitra_pengangkut',
  `shipping_cost` int(11) NOT NULL DEFAULT 55000,
  `status` enum('menunggu','diproses','diambil','dikirim','sampai_tujuan','selesai') NOT NULL DEFAULT 'menunggu',
  `document_path` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `logistics_shipments_tracking_code_unique` (`tracking_code`),
  KEY `logistics_shipments_mitra_id_foreign` (`mitra_id`),
  CONSTRAINT `logistics_shipments_mitra_id_foreign` FOREIGN KEY (`mitra_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logistics_shipments`
--

LOCK TABLES `logistics_shipments` WRITE;
/*!40000 ALTER TABLE `logistics_shipments` DISABLE KEYS */;
INSERT INTO `logistics_shipments` VALUES (1,'LF-LOG-20261101-WTL1',5,'Kebun Pak Agus (Cikidang RT 02)','UMKM Salad Fresh (Jl. Panorama)','Wortel Grade B (300 kg)',300,3.5,'mitra_pengangkut',70000,'selesai','logistics/surat_jalan_001.png','Surat jalan resmi telah ditandatangani oleh penerima di lokasi toko.','2026-11-07 08:47:14','2026-11-07 08:47:14'),(2,'LF-LOG-20261102-TMT2',5,'Kebun Pak Agus (Cikidang RT 02)','Hotel Lembang Asri Resort','Tomat Cherry Grade A (150 kg)',150,4.8,'mitra_pengangkut',75000,'dikirim','logistics/surat_jalan_002.png','Armada pickup dalam perjalanan dari Cikidang menuju hotel.','2026-11-07 08:47:14','2026-11-07 08:47:14'),(3,'LF-LOG-20261103-KTG3',NULL,'Kebun Bu Siti (Cibodas RT 03)','Kios Sayur Berkah (Pasar Panorama)','Kentang Granola Grade A (600 kg)',600,2.1,'antar_petani',20000,'diambil','logistics/surat_jalan_003.png','Pengantaran langsung oleh pihak petani menggunakan tossa roda tiga.','2026-11-07 08:47:14','2026-11-07 08:47:14'),(4,'LF-LOG-20261104-SLD4',5,'Kebun Bu Siti (Cibodas RT 03)','Pabrik Olahan Sayur Lembang Jaya','Selada Grade C (200 kg)',200,5.5,'mitra_pengangkut',80000,'diproses','logistics/surat_jalan_004.png','Muatan dijadwalkan pickup sore hari pukul 16.00 WIB.','2026-11-07 08:47:14','2026-11-07 08:47:14'),(5,'LF-LOG-20261105-BKL5',NULL,'Kelompok Tani Cikahuripan Lembang','Resto Alam Sunda Lembang','Brokoli Hijau Grade A (100 kg)',100,1.2,'ambil_sendiri',0,'selesai','logistics/surat_jalan_005.png','Pembeli mengambil sendiri langsung ke kebun menggunakan pick-up pribadi.','2026-11-07 08:47:14','2026-11-07 08:47:14');
/*!40000 ALTER TABLE `logistics_shipments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_11_07_000001_create_harvests_table',1),(5,'2026_11_07_000002_create_logistics_shipments_table',1),(6,'2026_11_07_000003_create_demands_and_orders_table',1),(7,'2026_11_07_153849_create_orders_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) NOT NULL,
  `buyer_id` bigint(20) unsigned NOT NULL,
  `harvest_id` bigint(20) unsigned NOT NULL,
  `mitra_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` int(11) NOT NULL,
  `delivery_method` varchar(255) NOT NULL,
  `shipping_cost` int(11) NOT NULL DEFAULT 0,
  `total_price` int(11) NOT NULL,
  `status` enum('dibuat','diproses','diambil','dikirim','selesai','ditolak') NOT NULL DEFAULT 'dibuat',
  `proof_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_buyer_id_foreign` (`buyer_id`),
  KEY `orders_harvest_id_foreign` (`harvest_id`),
  KEY `orders_mitra_id_foreign` (`mitra_id`),
  CONSTRAINT `orders_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_harvest_id_foreign` FOREIGN KEY (`harvest_id`) REFERENCES `harvests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_mitra_id_foreign` FOREIGN KEY (`mitra_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petani','pembeli','mitra') NOT NULL DEFAULT 'petani',
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `lng` double DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `delivery_available` tinyint(1) NOT NULL DEFAULT 0,
  `delivery_radius` double NOT NULL DEFAULT 5,
  `delivery_fee` int(11) NOT NULL DEFAULT 25000,
  `vehicle_desc` varchar(255) DEFAULT NULL,
  `vehicle_capacity` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin LembangFresh','admin@lembangfresh.test',NULL,'$2y$12$75fimhT423MhQdvIotEhvuoRYLZnCqMOBHKbfI0VATY8DWgmTq1ri','admin','081122334455','Jl. Raya Lembang No. 100, Kec. Lembang',-6.812,107.618,1,0,0,0,NULL,0,1,NULL,'2026-11-07 08:47:12','2026-11-07 08:47:12'),(2,'Pak Agus Susanto (Petani Hortikultura)','agus@lembangfresh.test',NULL,'$2y$12$5aHwpzucKIydhwKSFNZZUemJn62.Jn9sVr27a00kpDD2mMdZc1WXi','petani','081234567891','Desa Cikidang RT 02/05, Kec. Lembang',-6.795,107.635,1,1,5,25000,'Tossa Roda Tiga',200,1,NULL,'2026-11-07 08:47:12','2026-11-07 08:47:12'),(3,'Ibu Siti Aminah (Petani Sayur Segar)','siti@lembangfresh.test',NULL,'$2y$12$TJ1Z0K8I2ndd2PwQfJoT6u1VZq7vKc8inBO/nO.gFokZ.SCqjlgv.','petani','081234567892','Desa Cibodas RT 03/02, Kec. Lembang',-6.83,107.61,1,0,3,20000,NULL,0,1,NULL,'2026-11-07 08:47:12','2026-11-07 08:47:12'),(4,'UMKM Salad Fresh Lembang','salad@lembangfresh.test',NULL,'$2y$12$ZNsiPr7D8X3ONkCNaWItIuR8qA/sJkijmVt2f.76U1f8AQI4po4Eq','pembeli','081334455667','Jl. Panorama No. 45, Kec. Lembang',-6.811,107.617,1,0,0,0,NULL,0,1,NULL,'2026-11-07 08:47:12','2026-11-07 08:47:12'),(5,'Pak Budi Santoso (Mitra Logistik)','budi@lembangfresh.test',NULL,'$2y$12$bAAeY7eFtyuWhn5SRINF1.ckWSHGE/wyNmqZicorPc4XjCCCwOi.m','mitra','081445566778','Jl. Tangkuban Perahu No. 12, Kec. Lembang',-6.805,107.62,1,0,15,50000,'Pickup Mitsubishi L300 (D 1234 XY)',800,1,NULL,'2026-11-07 08:47:12','2026-11-07 08:47:12'),(6,'Cakrabirawa Damanik S.Psi (Petani)','dummy1_kamidin.widiastuti@lembangfresh.test',NULL,'$2y$12$vReFDSinM8OaaePMT9LlfeQ90LDCUGe1DR0aOSplvoBy9bDDfY0QO','petani','0632 9908 2645','Desa Cikahuripan, Kec. Lembang, Kab. Bandung Barat',-6.8209,107.5881,1,1,10,20000,NULL,0,1,NULL,'2026-11-07 08:47:12','2026-11-07 08:47:12'),(7,'Olivia Puji Zulaika (Pembeli)','dummy2_mpratiwi@lembangfresh.test',NULL,'$2y$12$7xx57elv285QEVILDGQjdu4D9c/8gmq.G975aXOqq6FtBI6YcjCvi','pembeli','(+62) 390 6061 0240','Desa Kayuambon, Kec. Lembang, Kab. Bandung Barat',-6.7829,107.63,0,0,4,25000,NULL,0,1,NULL,'2026-11-07 08:47:12','2026-11-07 08:47:12'),(8,'Aurora Wastuti (Mitra)','dummy3_latika91@lembangfresh.test',NULL,'$2y$12$jSVVWTTGbynaAGxhYGpQD.7iu2cSXuHl.wm7ue/JCqVJHD6Cp0NGa','mitra','(+62) 733 7671 227','Desa Jayagiri, Kec. Lembang, Kab. Bandung Barat',-6.8243,107.597,1,0,4,30000,'Pickup Gran Max D 5120 ABC',623,1,NULL,'2026-11-07 08:47:13','2026-11-07 08:47:13'),(9,'Wirda Elvina Susanti (Petani)','dummy4_unjani23@lembangfresh.test',NULL,'$2y$12$fv8GZdgrkZmzD7JKye6oQeIoe98ZyRwg.e2ujgJWAdNJQAD0j0.f6','petani','0479 9005 475','Desa Gudangkahuripan, Kec. Lembang, Kab. Bandung Barat',-6.8205,107.6063,1,1,7.5,30000,NULL,0,1,NULL,'2026-11-07 08:47:13','2026-11-07 08:47:13'),(10,'Mustofa Winarno (Pembeli)','dummy5_qtarihoran@lembangfresh.test',NULL,'$2y$12$K0QsWPuUn0wKkxYg369/iecp/oBS0ZPUAN6lJXKg2g4KdEjhK50Xa','pembeli','021 2646 746','Desa Wangunsari, Kec. Lembang, Kab. Bandung Barat',-6.834,107.6064,1,0,4,25000,NULL,0,1,NULL,'2026-11-07 08:47:14','2026-11-07 08:47:14'),(11,'dense','densek4rt1na@gmail.com',NULL,'$2y$12$YuWMw2.5QqDDh6dZYM6pneliPe4cZftyOwH7XpVXUxhRSN5TUO/1W','petani','1234456789','Jl maribaya',-6.795,107.635,1,1,5,25000,NULL,0,1,NULL,'2026-11-07 08:50:18','2026-11-07 08:50:18');
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

-- Dump completed on 2026-11-07 23:03:45
