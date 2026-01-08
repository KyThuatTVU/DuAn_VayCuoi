-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: cua_hang_vay_cuoi_db
-- ------------------------------------------------------
-- Server version	8.0.41

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
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `role` enum('super_admin','admin','moderator') DEFAULT 'admin',
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'admin_manager',NULL,'super_secret_password','Quản Trị Viên','admin','active',NULL,'2025-10-22 02:42:11'),(2,'nguyenhuynhkithuat84tv','nguyenhuynhkithuat84tv@gmail.com','$2y$10$yE2r.RooAkr48YeLckoYLeW.hVSXumpUSCAUydFNDsC9y00YVO3T6','Thuật Thuật','super_admin','active','2025-11-21 08:19:03','2025-11-21 08:10:57'),(4,'admin','admin@vaycuoi.com','$2y$10$PUQ9a04/ieid8r2yh/okY.Fs6Y.46WMaOrUYcjn/TPYYtAjpQ6586','Quản Trị Viên','admin','active',NULL,'2025-11-26 01:00:51');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_notifications`
--

DROP TABLE IF EXISTS `admin_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Loại thông báo: new_order, new_user, new_contact, new_booking, account_locked, new_payment',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tiêu đề thông báo',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nội dung thông báo',
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Đường dẫn liên kết',
  `is_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: chưa đọc, 1: đã đọc',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_is_read` (`is_read`),
  KEY `idx_type` (`type`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_notifications`
--

LOCK TABLES `admin_notifications` WRITE;
/*!40000 ALTER TABLE `admin_notifications` DISABLE KEYS */;
INSERT INTO `admin_notifications` VALUES (1,'new_contact','Liên hệ mới từ Lê Thị Hoa','Chủ đề: Đặt lịch thử váy','admin-contacts.php',0,'2025-12-10 02:37:04'),(2,'new_contact','Liên hệ mới từ Nguyễn Huỳnh Kỹ Thuật Thuật','Chủ đề: Khiếu nại dịch vụ','admin-contacts.php',0,'2025-12-10 02:54:40'),(3,'new_comment','Bình luận mới về sản phẩm','Test User đã bình luận về \"Test Product\": \"Test comment content\"','product-detail.php?id=1',0,'2025-12-10 09:14:16'),(4,'new_comment','Bình luận mới về \"Khuyễn mãi cuối năm\" (bài viết)','Thiên Vũ Đỗ: \"đẹp\"','blog-detail.php?id=2#comments',0,'2025-12-10 09:20:04'),(5,'new_user','Khách hàng mới đăng ký','Khách hàng Huỳnh Quốc Nhân (nhanhuynhtv345@gmail.com) vừa đăng ký tài khoản','admin-user-detail.php?id=8',0,'2025-12-14 07:37:45'),(6,'new_comment','Bình luận mới về \"Váy tay phồng cổ vuông thiết kế dáng ngắn trẻ trung\" (sản phẩm)','Thiên Vũ Đỗ: \"ok\"','product-detail.php?id=14#comments',1,'2025-12-24 00:23:29'),(7,'new_order','Đơn hàng mới #DH20251224013053370','Khách hàng Thiên Vũ Đỗ vừa đặt đơn hàng mới với tổng giá trị 1,155,000đ','admin-order-detail.php?id=28',1,'2025-12-24 00:30:53');
/*!40000 ALTER TABLE `admin_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banner_promotions`
--

DROP TABLE IF EXISTS `banner_promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banner_promotions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `admin_id` int DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text,
  `promo_code` varchar(80) DEFAULT NULL,
  `discount_value` varchar(50) DEFAULT NULL,
  `background_color` varchar(20) DEFAULT 'pink',
  `is_active` tinyint(1) DEFAULT '1',
  `display_order` int DEFAULT '0',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `promo_code` (`promo_code`),
  CONSTRAINT `banner_promotions_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Banner khuyến mãi hiển thị trên trang chủ và các trang khác';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banner_promotions`
--

LOCK TABLES `banner_promotions` WRITE;
/*!40000 ALTER TABLE `banner_promotions` DISABLE KEYS */;
INSERT INTO `banner_promotions` VALUES (20,NULL,'Mùa hè mát mẻ','Khuyến mãi đặc biệt','Giảm giá tưng bừng cả nhà ơi','MUAGE2025','10.00%','pink',1,1,'2025-12-13 07:25:00','2025-12-14 07:25:00','2025-12-13 07:41:48',NULL),(21,NULL,'Miễn Phí Vận Chuyển','Cho đơn hàng trên 3 triệu','Miễn phí vận chuyển toàn quốc cho đơn hàng từ 3 triệu đồng.',NULL,'FREE SHIP','pink',1,2,NULL,NULL,'2025-12-13 07:41:48',NULL),(22,NULL,'Giảm Thêm Cho Khách Hàng Mới','Đặc quyền khách hàng mới','Giảm thêm 5% cho khách hàng đăng ký tài khoản mới.','NEWCUSTOMER','5%','pink',1,1,'2025-12-14 08:41:48','2026-01-12 08:41:48','2025-12-13 07:41:48','2025-12-22 08:19:40');
/*!40000 ALTER TABLE `banner_promotions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `binh_luan_bai_viet`
--

DROP TABLE IF EXISTS `binh_luan_bai_viet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `binh_luan_bai_viet` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` bigint DEFAULT NULL,
  `admin_id` int DEFAULT NULL COMMENT 'ID admin nếu là admin trả lời',
  `is_admin_reply` tinyint(1) DEFAULT '0' COMMENT '1 = bình luận của admin',
  `bai_viet_id` bigint NOT NULL,
  `noi_dung` text NOT NULL,
  `parent_id` bigint DEFAULT NULL COMMENT 'ID bình luận cha (cho reply)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `reply_to_id` int DEFAULT NULL COMMENT 'ID bình luận đang được trả lời',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `idx_bai_viet_id` (`bai_viet_id`),
  KEY `idx_nguoi_dung_id` (`nguoi_dung_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `binh_luan_bai_viet_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  CONSTRAINT `binh_luan_bai_viet_ibfk_2` FOREIGN KEY (`bai_viet_id`) REFERENCES `tin_tuc_cuoi_hoi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `binh_luan_bai_viet_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `binh_luan_bai_viet` (`id`) ON DELETE CASCADE,
  CONSTRAINT `binh_luan_bai_viet_ibfk_4` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Bình luận bài viết tin tức';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `binh_luan_bai_viet`
--

LOCK TABLES `binh_luan_bai_viet` WRITE;
/*!40000 ALTER TABLE `binh_luan_bai_viet` DISABLE KEYS */;
INSERT INTO `binh_luan_bai_viet` VALUES (3,2,NULL,0,1,'Xu hướng váy cưới năm nay thật sự đẹp và hiện đại.',NULL,'2025-12-03 01:25:55',NULL,NULL),(4,6,NULL,0,2,'Đẹp',NULL,'2025-12-03 01:26:21',NULL,NULL),(5,6,NULL,0,2,'ok',NULL,'2025-12-03 01:26:38',NULL,NULL),(6,7,NULL,0,1,'có ưu đãi gì không ạ',NULL,'2025-12-03 08:16:11',NULL,NULL),(7,4,NULL,0,1,'hiện đang giảm 30% vào ngày 25/12 bạn nhé',6,'2025-12-03 08:22:33',NULL,NULL),(8,4,NULL,0,1,'ok',3,'2025-12-03 08:27:44',NULL,NULL),(9,NULL,2,1,1,'ok',6,'2025-12-03 08:33:50',NULL,NULL),(10,7,NULL,0,1,'vâng ạ',9,'2025-12-03 09:03:35',NULL,NULL),(11,7,NULL,0,1,'hi',9,'2025-12-03 09:04:10',NULL,NULL),(12,7,NULL,0,1,'cho tôi liên hệ với shop',6,'2025-12-03 09:05:53',NULL,NULL),(13,7,NULL,0,1,'hi',NULL,'2025-12-03 09:10:47',NULL,NULL),(14,NULL,2,1,1,'chào Vinh bạn cần hỗ trợ gì',13,'2025-12-03 09:11:08',NULL,NULL),(15,7,NULL,0,6,'đẹp quá ạ',NULL,'2025-12-04 07:24:33',NULL,NULL),(16,NULL,2,1,6,'cảm ơn bạn',15,'2025-12-04 08:58:06',NULL,NULL),(17,6,NULL,0,6,'sao tôi mua bị rách đái quần vậy',15,'2025-12-10 03:30:34',NULL,15),(18,6,NULL,0,6,'xấu quắt',NULL,'2025-12-10 03:30:49',NULL,NULL),(19,7,NULL,0,6,'vậy hả',15,'2025-12-10 03:32:28',NULL,17),(20,6,NULL,0,6,'ờ',15,'2025-12-10 03:33:26',NULL,19),(21,7,NULL,0,6,'khùng',15,'2025-12-10 07:44:05',NULL,20),(22,7,NULL,0,6,'khùng',15,'2025-12-10 07:44:34',NULL,20),(23,7,NULL,0,6,'khùng',15,'2025-12-10 07:47:36',NULL,20),(24,6,NULL,0,6,'muốn gì kiếm chuyện hả gì',15,'2025-12-10 07:55:47',NULL,23),(25,7,NULL,0,6,'ừ rồi sao',15,'2025-12-10 08:03:40',NULL,24),(26,7,NULL,0,6,'muốn gì gặp nhau nói chuyện',15,'2025-12-10 08:13:40',NULL,25),(27,7,NULL,0,6,'thích thì chơi',15,'2025-12-10 08:14:47',NULL,24),(28,7,NULL,0,6,'muốn thì chơi sợ ai',15,'2025-12-10 08:23:12',NULL,24),(29,7,NULL,0,6,'mày muốn gì',15,'2025-12-10 08:45:58',NULL,24),(30,6,NULL,0,2,'đẹp',NULL,'2025-12-10 09:20:04',NULL,NULL);
/*!40000 ALTER TABLE `binh_luan_bai_viet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `binh_luan_san_pham`
--

DROP TABLE IF EXISTS `binh_luan_san_pham`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `binh_luan_san_pham` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` bigint DEFAULT NULL,
  `admin_id` int DEFAULT NULL COMMENT 'ID admin nếu là admin trả lời',
  `is_admin_reply` tinyint(1) DEFAULT '0' COMMENT '1 = bình luận của admin',
  `vay_id` bigint NOT NULL,
  `noi_dung` text NOT NULL,
  `parent_id` bigint DEFAULT NULL COMMENT 'ID bình luận cha (cho reply)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `reply_to_id` int DEFAULT NULL COMMENT 'ID bình luận đang được trả lời',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `idx_vay_id` (`vay_id`),
  KEY `idx_nguoi_dung_id` (`nguoi_dung_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `binh_luan_san_pham_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  CONSTRAINT `binh_luan_san_pham_ibfk_2` FOREIGN KEY (`vay_id`) REFERENCES `vay_cuoi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `binh_luan_san_pham_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `binh_luan_san_pham` (`id`) ON DELETE CASCADE,
  CONSTRAINT `binh_luan_san_pham_ibfk_4` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Bình luận sản phẩm váy cưới';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `binh_luan_san_pham`
--

LOCK TABLES `binh_luan_san_pham` WRITE;
/*!40000 ALTER TABLE `binh_luan_san_pham` DISABLE KEYS */;
INSERT INTO `binh_luan_san_pham` VALUES (7,2,NULL,0,1,'Chất liệu váy có tốt không ạ? Mình đang cân nhắc thuê.',NULL,'2025-12-03 01:25:55',NULL,NULL),(8,6,NULL,0,4,'Hơi đẹp',NULL,'2025-12-03 01:31:55',NULL,NULL),(11,2,NULL,0,1,'Comment test của User 1 - 2025-12-10 09:22:38',NULL,'2025-12-10 08:22:38',NULL,NULL),(12,3,NULL,0,1,'Reply của User 2 vào comment User 1 - 2025-12-10 09:22:38',11,'2025-12-10 08:22:38',NULL,11),(13,2,NULL,0,1,'Comment test của User 1 - 2025-12-10 09:32:13',NULL,'2025-12-10 08:32:13',NULL,NULL),(14,3,NULL,0,1,'Reply của User 2 vào comment User 1 - 2025-12-10 09:32:13',13,'2025-12-10 08:32:13',NULL,13),(15,7,NULL,0,1,'Test reply - 09:37:23 10/12/2025',13,'2025-12-10 08:37:23',NULL,13),(16,6,NULL,0,8,'đẹp',NULL,'2025-12-10 08:58:30',NULL,NULL),(17,6,NULL,0,1,'Test comment d? ki?m tra admin notification',NULL,'2025-12-10 09:13:41',NULL,NULL),(18,6,NULL,0,14,'ok',NULL,'2025-12-24 00:23:29',NULL,NULL),(19,NULL,2,1,14,'chào bạn',18,'2025-12-24 00:24:06',NULL,NULL);
/*!40000 ALTER TABLE `binh_luan_san_pham` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cai_dat`
--

DROP TABLE IF EXISTS `cai_dat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cai_dat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL COMMENT 'Khóa cài đặt',
  `setting_value` text COMMENT 'Giá trị cài đặt',
  `setting_group` varchar(50) DEFAULT 'general' COMMENT 'Nhóm cài đặt',
  `setting_label` varchar(255) DEFAULT NULL COMMENT 'Nhãn hiển thị',
  `setting_type` enum('text','textarea','email','phone','url','number') DEFAULT 'text' COMMENT 'Loại input',
  `sort_order` int DEFAULT '0' COMMENT 'Thứ tự hiển thị',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `idx_group` (`setting_group`),
  KEY `idx_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Cài đặt hệ thống';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cai_dat`
--

LOCK TABLES `cai_dat` WRITE;
/*!40000 ALTER TABLE `cai_dat` DISABLE KEYS */;
INSERT INTO `cai_dat` VALUES (1,'contact_address','Nguyễn Thiện Thành, Phường Hòa Thuận, Tỉnh Vĩnh Long','contact','Địa chỉ','textarea',1,'2025-12-04 04:49:07','2025-12-04 05:53:17'),(2,'contact_phone','0388853044','contact','Số điện thoại','phone',2,'2025-12-04 04:49:07','2025-12-13 06:22:40'),(3,'contact_email','nguyenhuynhkithuat84tv@gmail.com','contact','Email','email',3,'2025-12-04 04:49:07','2025-12-04 05:53:17'),(4,'contact_hotline','078.797.2075','contact','Hotline','phone',4,'2025-12-04 04:49:07','2025-12-04 04:49:07'),(5,'working_days','Thứ 2 - Chủ Nhật','working','Ngày làm việc','text',1,'2025-12-04 04:49:07','2025-12-04 04:49:07'),(6,'working_hours','8:00 - 20:00','working','Giờ làm việc','text',2,'2025-12-04 04:49:07','2025-12-04 04:49:07'),(7,'social_facebook','https://www.facebook.com/huynh.thuat.902/','social','Facebook','url',1,'2025-12-04 04:49:07','2025-12-04 05:54:28'),(8,'social_instagram','','social','Instagram','url',2,'2025-12-04 04:49:07','2025-12-04 05:54:28'),(9,'social_youtube','','social','YouTube','url',3,'2025-12-04 04:49:07','2025-12-04 05:54:28'),(10,'social_zalo','https://zalo.me/0388853044','social','Zalo','url',4,'2025-12-04 04:49:07','2025-12-04 05:54:28'),(11,'bank_name','Vietcombank','bank','Tên ngân hàng','text',1,'2025-12-04 04:49:07','2025-12-04 04:49:07'),(12,'bank_account','1234567890123','bank','Số tài khoản','text',2,'2025-12-04 04:49:07','2025-12-04 04:49:07'),(13,'bank_holder','NGUYEN VAN A','bank','Chủ tài khoản','text',3,'2025-12-04 04:49:07','2025-12-04 04:49:07'),(14,'bank_branch','TP. Hồ Chí Minh','bank','Chi nhánh','text',4,'2025-12-04 04:49:07','2025-12-04 04:49:07'),(15,'site_name','Váy Cưới Thiên Thần','general','Tên website','text',1,'2025-12-04 04:49:07','2025-12-04 04:49:07'),(16,'site_description','Địa chỉ uy tín cho thuê váy cưới cao cấp tại TP.HCM với hơn 10 năm kinh nghiệm.','general','Mô tả website','textarea',2,'2025-12-04 04:49:07','2025-12-04 04:49:07'),(17,'site_copyright','© 2024 Váy Cưới Thiên Thần. All rights reserved.','general','Bản quyền','text',3,'2025-12-04 04:49:07','2025-12-04 04:49:07');
/*!40000 ALTER TABLE `cai_dat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cam_xuc_bai_viet`
--

DROP TABLE IF EXISTS `cam_xuc_bai_viet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cam_xuc_bai_viet` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` bigint NOT NULL,
  `bai_viet_id` bigint NOT NULL,
  `loai_cam_xuc` enum('like','love','wow','haha','sad','angry') NOT NULL DEFAULT 'like',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_post_reaction` (`nguoi_dung_id`,`bai_viet_id`),
  KEY `idx_bai_viet_id` (`bai_viet_id`),
  KEY `idx_loai_cam_xuc` (`loai_cam_xuc`),
  CONSTRAINT `cam_xuc_bai_viet_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cam_xuc_bai_viet_ibfk_2` FOREIGN KEY (`bai_viet_id`) REFERENCES `tin_tuc_cuoi_hoi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Cảm xúc cho bài viết tin tức';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cam_xuc_bai_viet`
--

LOCK TABLES `cam_xuc_bai_viet` WRITE;
/*!40000 ALTER TABLE `cam_xuc_bai_viet` DISABLE KEYS */;
INSERT INTO `cam_xuc_bai_viet` VALUES (3,2,1,'like','2025-12-03 01:25:55',NULL),(5,6,2,'like','2025-12-03 01:35:48',NULL),(6,6,1,'love','2025-12-03 01:46:01',NULL),(7,7,6,'wow','2025-12-04 07:24:24',NULL),(8,6,6,'love','2025-12-10 00:33:00',NULL);
/*!40000 ALTER TABLE `cam_xuc_bai_viet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cam_xuc_san_pham`
--

DROP TABLE IF EXISTS `cam_xuc_san_pham`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cam_xuc_san_pham` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` bigint NOT NULL,
  `vay_id` bigint NOT NULL,
  `loai_cam_xuc` enum('like','love','wow','haha','sad','angry') NOT NULL DEFAULT 'like',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product_reaction` (`nguoi_dung_id`,`vay_id`),
  KEY `idx_vay_id` (`vay_id`),
  KEY `idx_loai_cam_xuc` (`loai_cam_xuc`),
  CONSTRAINT `cam_xuc_san_pham_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cam_xuc_san_pham_ibfk_2` FOREIGN KEY (`vay_id`) REFERENCES `vay_cuoi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Cảm xúc cho sản phẩm váy cưới';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cam_xuc_san_pham`
--

LOCK TABLES `cam_xuc_san_pham` WRITE;
/*!40000 ALTER TABLE `cam_xuc_san_pham` DISABLE KEYS */;
INSERT INTO `cam_xuc_san_pham` VALUES (5,2,1,'like','2025-12-03 01:25:55',NULL),(6,2,3,'like','2025-12-03 01:25:55',NULL),(7,6,8,'love','2025-12-10 08:58:26',NULL),(8,6,14,'love','2025-12-24 00:23:25',NULL);
/*!40000 ALTER TABLE `cam_xuc_san_pham` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cau_hinh_thanh_toan`
--

DROP TABLE IF EXISTS `cau_hinh_thanh_toan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cau_hinh_thanh_toan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ten_ngan_hang` varchar(100) NOT NULL DEFAULT 'Vietcombank',
  `ma_ngan_hang` varchar(20) NOT NULL DEFAULT 'VCB',
  `so_tai_khoan` varchar(50) NOT NULL,
  `ten_tai_khoan` varchar(255) NOT NULL,
  `template` varchar(20) DEFAULT 'compact' COMMENT 'Template QR: compact, print, qr_only',
  `active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cau_hinh_thanh_toan`
--

LOCK TABLES `cau_hinh_thanh_toan` WRITE;
/*!40000 ALTER TABLE `cau_hinh_thanh_toan` DISABLE KEYS */;
INSERT INTO `cau_hinh_thanh_toan` VALUES (1,'Vietcombank','VCB','1052053578','NGUYEN HUYNH KY THUAT','compact',1,'2025-11-20 07:45:00',NULL),(2,'Vietcombank','VCB','1052053578','NGUYEN HUYNH KY THUAT','compact',1,'2025-11-20 07:45:11',NULL);
/*!40000 ALTER TABLE `cau_hinh_thanh_toan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chi_tiet_hoa_don`
--

DROP TABLE IF EXISTS `chi_tiet_hoa_don`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chi_tiet_hoa_don` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `hoa_don_id` bigint NOT NULL,
  `vay_id` bigint DEFAULT NULL,
  `description` text,
  `amount` decimal(12,2) NOT NULL,
  `quantity` int DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `hoa_don_id` (`hoa_don_id`),
  KEY `vay_id` (`vay_id`),
  CONSTRAINT `chi_tiet_hoa_don_ibfk_1` FOREIGN KEY (`hoa_don_id`) REFERENCES `hoa_don` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chi_tiet_hoa_don_ibfk_2` FOREIGN KEY (`vay_id`) REFERENCES `vay_cuoi` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chi_tiet_hoa_don`
--

LOCK TABLES `chi_tiet_hoa_don` WRITE;
/*!40000 ALTER TABLE `chi_tiet_hoa_don` DISABLE KEYS */;
INSERT INTO `chi_tiet_hoa_don` VALUES (1,1,1,'Thuê Váy Công Chúa Bồng Bềnh',5000000.00,1),(2,13,1,'Váy Công Chúa Bồng Bềnh - Thuê 1 ngày (20/11/2025 - 21/11/2025)',5000000.00,1),(3,14,3,'Váy Chữ A Tối Giản - Thuê 1 ngày (20/11/2025 - 21/11/2025)',3000000.00,1),(4,15,1,'Váy Công Chúa Bồng Bềnh - Thuê 1 ngày (20/11/2025 - 21/11/2025)',5000000.00,1),(5,16,4,'Váy Cưới Kiêu Sa - Thuê 1 ngày (27/11/2025 - 28/11/2025)',10000000.00,1),(6,17,1,'Váy Công Chúa Bồng Bềnh - Thuê 1 ngày (27/11/2025 - 28/11/2025)',5000000.00,1),(7,18,4,'Váy Cưới Kiêu Sa - Thuê 1 ngày (03/12/2025 - 04/12/2025)',10000000.00,1),(8,19,4,'Váy Cưới Kiêu Sa - Thuê 1 ngày (03/12/2025 - 04/12/2025)',10000000.00,1),(9,20,4,'Váy Cưới Kiêu Sa - Thuê 1 ngày (03/12/2025 - 04/12/2025)',10000000.00,1),(10,21,4,'Váy Cưới Kiêu Sa - Thuê 9 ngày (03/12/2025 - 12/12/2025)',90000000.00,1),(11,22,4,'Váy Cưới Kiêu Sa - Thuê 1 ngày (03/12/2025 - 04/12/2025)',10000000.00,1),(12,23,4,'Váy Cưới Kiêu Sa - Thuê 1 ngày (03/12/2025 - 04/12/2025)',10000000.00,1),(13,24,4,'Váy Cưới Kiêu Sa - Thuê 1 ngày (03/12/2025 - 04/12/2025)',10000000.00,1),(14,25,4,'Váy Cưới Kiêu Sa - Thuê 1 ngày (03/12/2025 - 04/12/2025)',10000000.00,1),(15,26,4,'Váy Cưới Kiêu Sa - Thuê 1 ngày (03/12/2025 - 04/12/2025)',10000000.00,1),(16,27,4,'Váy Cưới Kiêu Sa - Thuê 1 ngày (03/12/2025 - 04/12/2025)',10000000.00,1),(17,28,14,'Váy tay phồng cổ vuông thiết kế dáng ngắn trẻ trung - Thuê 1 ngày (24/12/2025 - 25/12/2025)',1100000.00,1);
/*!40000 ALTER TABLE `chi_tiet_hoa_don` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dat_lich_thu_vay`
--

DROP TABLE IF EXISTS `dat_lich_thu_vay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dat_lich_thu_vay` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `vay_id` bigint DEFAULT NULL,
  `scheduled_date` date NOT NULL,
  `scheduled_time` time DEFAULT NULL,
  `number_of_persons` int DEFAULT '1',
  `status` enum('pending','confirmed','attended','cancelled') DEFAULT 'pending',
  `note` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `vay_id` (`vay_id`),
  KEY `idx_date_status` (`scheduled_date`,`status`),
  CONSTRAINT `dat_lich_thu_vay_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dat_lich_thu_vay_ibfk_2` FOREIGN KEY (`vay_id`) REFERENCES `vay_cuoi` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dat_lich_thu_vay`
--

LOCK TABLES `dat_lich_thu_vay` WRITE;
/*!40000 ALTER TABLE `dat_lich_thu_vay` DISABLE KEYS */;
INSERT INTO `dat_lich_thu_vay` VALUES (1,2,'Trần Văn Bình','0912345678','binh.tran@example.com',2,'2023-12-25','14:30:00',1,'cancelled',NULL,'2025-10-22 02:42:11');
/*!40000 ALTER TABLE `dat_lich_thu_vay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `don_hang`
--

DROP TABLE IF EXISTS `don_hang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `don_hang` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `ma_don_hang` varchar(50) DEFAULT NULL COMMENT 'Mã đơn hàng duy nhất',
  `nguoi_dung_id` bigint DEFAULT NULL,
  `ho_ten` varchar(255) DEFAULT '' COMMENT 'Họ tên người nhận',
  `so_dien_thoai` varchar(30) DEFAULT '' COMMENT 'Số điện thoại người nhận',
  `dia_chi` text COMMENT 'Địa chỉ nhận váy',
  `tinh_thanh` varchar(100) DEFAULT NULL COMMENT 'Mã tỉnh/thành phố',
  `quan_huyen` varchar(100) DEFAULT NULL COMMENT 'Mã quận/huyện',
  `phuong_xa` varchar(100) DEFAULT NULL COMMENT 'Mã phường/xã',
  `dia_chi_cu_the` varchar(500) DEFAULT NULL COMMENT 'Địa chỉ cụ thể (số nhà, đường...)',
  `ghi_chu` text COMMENT 'Ghi chú đơn hàng',
  `tong_tien` decimal(14,2) NOT NULL,
  `trang_thai` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `phuong_thuc_thanh_toan` varchar(50) DEFAULT 'qr_code' COMMENT 'Phương thức thanh toán',
  `trang_thai_thanh_toan` enum('pending','paid','failed','expired') DEFAULT 'pending' COMMENT 'Trạng thái thanh toán',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_don_hang` (`ma_don_hang`),
  KEY `nguoi_dung_id` (`nguoi_dung_id`),
  KEY `idx_ma_don_hang` (`ma_don_hang`),
  KEY `idx_trang_thai` (`trang_thai`),
  KEY `idx_trang_thai_thanh_toan` (`trang_thai_thanh_toan`),
  KEY `idx_don_hang_tinh` (`tinh_thanh`),
  CONSTRAINT `don_hang_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `don_hang`
--

LOCK TABLES `don_hang` WRITE;
/*!40000 ALTER TABLE `don_hang` DISABLE KEYS */;
INSERT INTO `don_hang` VALUES (1,'DH202510220001',NULL,'','',NULL,NULL,NULL,NULL,NULL,NULL,5000000.00,'completed','qr_code','pending','2025-10-22 02:42:11','2025-11-20 08:07:04'),(13,'DH20251120091357539',4,'Thuật Thuật','0388853044','fkgf',NULL,NULL,NULL,NULL,'',5250000.00,'pending','qr_code','pending','2025-11-20 08:13:57',NULL),(14,'DH20251120093034343',4,'Thuật Thuật','0388853044','Trà Vinh',NULL,NULL,NULL,NULL,'',3150000.00,'pending','qr_code','pending','2025-11-20 08:30:34',NULL),(15,'DH20251120103917910',4,'Thuật Thuật','0388853049','ewewq',NULL,NULL,NULL,NULL,'ưqeqwe',5250000.00,'pending','qr_code','pending','2025-11-20 09:39:17',NULL),(16,'DH20251127090316730',6,'Thiên Vũ Đỗ','0388853044','Trà Vinh',NULL,NULL,NULL,NULL,'',10500000.00,'pending','vnpay','pending','2025-11-27 08:03:16',NULL),(17,'DH20251127091150595',6,'Thiên Vũ Đỗ','0388853044','trà vinh',NULL,NULL,NULL,NULL,'',5250000.00,'pending','vnpay','pending','2025-11-27 08:11:50',NULL),(18,'DH20251203014755622',6,'Thiên Vũ Đỗ','0388853044','Trà Vinh',NULL,NULL,NULL,NULL,'',10500000.00,'pending','momo','pending','2025-12-03 00:47:55',NULL),(19,'DH20251203015034841',6,'Thiên Vũ Đỗ','0388853044','Trà Vinh',NULL,NULL,NULL,NULL,'',10500000.00,'pending','momo','pending','2025-12-03 00:50:34',NULL),(20,'DH20251203015557732',6,'Thiên Vũ Đỗ','0388853044','Trà Cú',NULL,NULL,NULL,NULL,'',10500000.00,'pending','momo','pending','2025-12-03 00:55:57',NULL),(21,'DH20251203020151635',6,'Thiên Vũ Đỗ','0388853044','Trà cú',NULL,NULL,NULL,NULL,'',94500000.00,'pending','momo','pending','2025-12-03 01:01:51',NULL),(22,'DH20251203020354717',6,'Thiên Vũ Đỗ','0388853044','Trà Cú',NULL,NULL,NULL,NULL,'',10500000.00,'pending','momo','pending','2025-12-03 01:03:54',NULL),(23,'DH20251203021221820',6,'Thiên Vũ Đỗ','0388853044','Trà CÚ',NULL,NULL,NULL,NULL,'',10500000.00,'pending','momo','failed','2025-12-03 01:12:21','2025-12-03 01:15:03'),(24,'DH20251203033623732',7,'Phạm Quang Vinh','0388853048','Phường Long Đức Tỉnh Vĩnh Long',NULL,NULL,NULL,NULL,'',10500000.00,'pending','momo','failed','2025-12-03 02:36:23','2025-12-03 02:38:36'),(25,'DH20251203033908585',7,'Phạm Quang Vinh','0388853048','Phường Long Đức Tỉnh Vĩnh Long',NULL,NULL,NULL,NULL,'',10500000.00,'pending','momo','pending','2025-12-03 02:39:08',NULL),(26,'DH20251203034420115',7,'Phạm Quang Vinh','0388853048','Phường Long Đức Tỉnh Vĩnh Long',NULL,NULL,NULL,NULL,'',10500000.00,'completed','momo','paid','2025-12-03 02:44:20','2025-12-03 02:48:13'),(27,'DH20251203085051495',7,'Phạm Quang Vinh','0388853048','nhà trọ nhựt thành, Xã Đại Phúc, Huyện Càng Long, Trà Vinh','84','844','29599','nhà trọ nhựt thành','giao lúc 10h nha',10500000.00,'pending','momo','failed','2025-12-03 07:50:51','2025-12-03 07:51:05'),(28,'DH20251224013053370',6,'Thiên Vũ Đỗ','0377753044','số 1, Xã Má Lé, Huyện Đồng Văn, Tỉnh Hà Giang','02','26','718','số 1','',1155000.00,'completed','momo','paid','2025-12-24 00:30:53','2025-12-24 00:35:50');
/*!40000 ALTER TABLE `don_hang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `du_lieu_tim_kiem`
--

DROP TABLE IF EXISTS `du_lieu_tim_kiem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `du_lieu_tim_kiem` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `keyword` varchar(255) NOT NULL,
  `results_count` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_keyword` (`keyword`),
  CONSTRAINT `du_lieu_tim_kiem_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `du_lieu_tim_kiem`
--

LOCK TABLES `du_lieu_tim_kiem` WRITE;
/*!40000 ALTER TABLE `du_lieu_tim_kiem` DISABLE KEYS */;
/*!40000 ALTER TABLE `du_lieu_tim_kiem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gio_hang`
--

DROP TABLE IF EXISTS `gio_hang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gio_hang` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` bigint NOT NULL,
  `vay_id` bigint NOT NULL,
  `so_luong` int DEFAULT '1',
  `ngay_bat_dau_thue` date NOT NULL,
  `ngay_tra_vay` date NOT NULL,
  `so_ngay_thue` int DEFAULT '1',
  `ghi_chu` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_dress` (`nguoi_dung_id`,`vay_id`),
  KEY `vay_id` (`vay_id`),
  CONSTRAINT `gio_hang_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gio_hang_ibfk_2` FOREIGN KEY (`vay_id`) REFERENCES `vay_cuoi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gio_hang`
--

LOCK TABLES `gio_hang` WRITE;
/*!40000 ALTER TABLE `gio_hang` DISABLE KEYS */;
INSERT INTO `gio_hang` VALUES (17,7,4,1,'2025-12-20','2025-12-21',1,'','2025-12-09 01:27:52','2025-12-19 04:42:22'),(19,8,13,1,'2025-12-20','2025-12-21',1,'Size: M. ','2025-12-14 07:38:36','2025-12-19 04:42:22');
/*!40000 ALTER TABLE `gio_hang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hinh_anh_vay_cuoi`
--

DROP TABLE IF EXISTS `hinh_anh_vay_cuoi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hinh_anh_vay_cuoi` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `vay_id` bigint NOT NULL,
  `url` varchar(1024) NOT NULL COMMENT 'URL hoặc đường dẫn tới file ảnh',
  `alt_text` varchar(255) DEFAULT NULL COMMENT 'Văn bản thay thế cho ảnh (tốt cho SEO)',
  `is_primary` tinyint(1) DEFAULT '0' COMMENT '1 = là ảnh đại diện, 0 = là ảnh phụ',
  `sort_order` int DEFAULT '0' COMMENT 'Thứ tự hiển thị ảnh',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vay_id` (`vay_id`),
  CONSTRAINT `hinh_anh_vay_cuoi_ibfk_1` FOREIGN KEY (`vay_id`) REFERENCES `vay_cuoi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hinh_anh_vay_cuoi`
--

LOCK TABLES `hinh_anh_vay_cuoi` WRITE;
/*!40000 ALTER TABLE `hinh_anh_vay_cuoi` DISABLE KEYS */;
INSERT INTO `hinh_anh_vay_cuoi` VALUES (4,2,'/images/dresses/vc002-front.jpg','Váy đuôi cá quyến rũ dáng trước',1,0,'2025-10-22 02:42:11'),(5,2,'/images/dresses/vc002-side.jpg','Váy đuôi cá quyến rũ nhìn từ bên cạnh',0,1,'2025-10-22 02:42:11'),(6,1,'uploads/dresses/1764123846_0_xu-huong-vay-chup-anh-cuoi-dep-tuy-hoa-phu-yen-bong-benh-he-thu-2.jpg',NULL,0,3,'2025-11-26 02:24:06'),(10,5,'uploads/dresses/1765328800_0_vay-cuoi-cong-chua-xoe-bong-3.webp',NULL,0,1,'2025-12-10 01:06:40');
/*!40000 ALTER TABLE `hinh_anh_vay_cuoi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hoa_don`
--

DROP TABLE IF EXISTS `hoa_don`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hoa_don` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `don_hang_id` bigint NOT NULL,
  `nguoi_dung_id` bigint DEFAULT NULL,
  `ma_hoa_don` varchar(100) DEFAULT NULL,
  `tong_thanh_toan` decimal(14,2) NOT NULL,
  `status` enum('unpaid','paid','partially_paid','cancelled') DEFAULT 'unpaid',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `don_hang_id` (`don_hang_id`),
  UNIQUE KEY `ma_hoa_don` (`ma_hoa_don`),
  KEY `nguoi_dung_id` (`nguoi_dung_id`),
  CONSTRAINT `hoa_don_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hoa_don_ibfk_2` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hoa_don`
--

LOCK TABLES `hoa_don` WRITE;
/*!40000 ALTER TABLE `hoa_don` DISABLE KEYS */;
INSERT INTO `hoa_don` VALUES (1,1,NULL,'HD20230001',5000000.00,'paid','2025-10-22 02:42:11'),(13,13,4,'HD20251120091357454',5250000.00,'unpaid','2025-11-20 08:13:57'),(14,14,4,'HD20251120093034444',3150000.00,'unpaid','2025-11-20 08:30:34'),(15,15,4,'HD20251120103917317',5250000.00,'unpaid','2025-11-20 09:39:17'),(16,16,6,'HD20251127090316700',10500000.00,'unpaid','2025-11-27 08:03:16'),(17,17,6,'HD20251127091150719',5250000.00,'unpaid','2025-11-27 08:11:50'),(18,18,6,'HD20251203014755722',10500000.00,'unpaid','2025-12-03 00:47:55'),(19,19,6,'HD20251203015034988',10500000.00,'unpaid','2025-12-03 00:50:34'),(20,20,6,'HD20251203015557357',10500000.00,'unpaid','2025-12-03 00:55:57'),(21,21,6,'HD20251203020151137',94500000.00,'unpaid','2025-12-03 01:01:51'),(22,22,6,'HD20251203020354447',10500000.00,'unpaid','2025-12-03 01:03:54'),(23,23,6,'HD20251203021221270',10500000.00,'unpaid','2025-12-03 01:12:21'),(24,24,7,'HD20251203033623859',10500000.00,'unpaid','2025-12-03 02:36:23'),(25,25,7,'HD20251203033908595',10500000.00,'unpaid','2025-12-03 02:39:08'),(26,26,7,'HD20251203034420292',10500000.00,'unpaid','2025-12-03 02:44:20'),(27,27,7,'HD20251203085051841',10500000.00,'unpaid','2025-12-03 07:50:51'),(28,28,6,'HD20251224013053499',1155000.00,'unpaid','2025-12-24 00:30:53');
/*!40000 ALTER TABLE `hoa_don` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `khuyen_mai`
--

DROP TABLE IF EXISTS `khuyen_mai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `khuyen_mai` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `code` varchar(80) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `type` enum('percent','fixed') DEFAULT 'percent',
  `value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(12,2) DEFAULT '0.00',
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `usage_limit` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `khuyen_mai`
--

LOCK TABLES `khuyen_mai` WRITE;
/*!40000 ALTER TABLE `khuyen_mai` DISABLE KEYS */;
INSERT INTO `khuyen_mai` VALUES (1,'MUAGE2025','Mùa hè mát mẻ','Giảm giá tưng bừng cả nhà ơi','percent',10.00,10000000.00,'2025-12-13 07:25:00','2025-12-14 07:25:00',5,'2025-12-13 07:26:13',NULL),(2,'MUAGE2026','Giảm giá sốc','Ưu đãi nè','percent',5.00,5000000.00,'2025-12-19 04:31:00','2025-12-21 04:31:00',3,'2025-12-19 04:32:00',NULL),(3,'GIANGSINH2025','Giáng sinh an lành tại nhà Thiên Thần','Giảm sốc duy nhất ngày 25/12/2025','percent',7.00,5000000.00,'2025-12-22 08:21:00','2025-12-25 06:21:00',4,'2025-12-22 08:25:44',NULL);
/*!40000 ALTER TABLE `khuyen_mai` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `khuyen_mai_vay`
--

DROP TABLE IF EXISTS `khuyen_mai_vay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `khuyen_mai_vay` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `khuyen_mai_id` bigint NOT NULL,
  `vay_id` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `khuyen_mai_id` (`khuyen_mai_id`,`vay_id`),
  KEY `vay_id` (`vay_id`),
  CONSTRAINT `khuyen_mai_vay_ibfk_1` FOREIGN KEY (`khuyen_mai_id`) REFERENCES `khuyen_mai` (`id`) ON DELETE CASCADE,
  CONSTRAINT `khuyen_mai_vay_ibfk_2` FOREIGN KEY (`vay_id`) REFERENCES `vay_cuoi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `khuyen_mai_vay`
--

LOCK TABLES `khuyen_mai_vay` WRITE;
/*!40000 ALTER TABLE `khuyen_mai_vay` DISABLE KEYS */;
/*!40000 ALTER TABLE `khuyen_mai_vay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lich_su_chatbot`
--

DROP TABLE IF EXISTS `lich_su_chatbot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lich_su_chatbot` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `message_from` enum('user','bot') DEFAULT NULL,
  `message` text,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_session` (`session_id`),
  CONSTRAINT `lich_su_chatbot_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lich_su_chatbot`
--

LOCK TABLES `lich_su_chatbot` WRITE;
/*!40000 ALTER TABLE `lich_su_chatbot` DISABLE KEYS */;
INSERT INTO `lich_su_chatbot` VALUES (1,7,'user_7','user','Bảng giá dịch vụ',NULL,'2025-12-09 02:52:09'),(2,7,'user_7','bot','Chào Phạm Quang Vinh 👋, em là Trà My tư vấn viên của Váy Cưới Thiên Thần 💕. Dưới đây là bảng giá dịch vụ của chúng em:\n- Cho thuê váy cưới: 2.000.000đ - 15.000.000đ/bộ\n- May đo váy cưới: 5.000.000đ - 50.000.000đ\n- Cho thuê vest chú rể: 500.000đ - 3.000.000đ/bộ\n- Trang điểm cô dâu: 1.500.000đ - 5.000.000đ\n- Chụp ảnh cưới trọn gói: 8.000.000đ - 30.000.000đ\n- Phụ kiện cưới: giá cả tùy thuộc vào sản phẩm cụ thể 🎀. Anh muốn biết thêm thông tin gì không? 👰','{\"model\": \"llama-3.3-70b-versatile\"}','2025-12-09 02:52:09'),(3,7,'user_7','user','mình có váy cưới gì',NULL,'2025-12-09 02:52:37'),(4,7,'user_7','bot','Phạm Quang Vinh ơi 💕, Váy Cưới Thiên Thần của em có rất nhiều mẫu váy cưới đẹp và đa dạng 👗. Em có váy cưới đuôi cá, váy cưới công chúa, váy cưới chữ A, váy cưới tối giản, váy cưới ren vintage và áo dài cưới truyền thống Việt Nam 🌹. Anh có thể xem thêm tại trang products.php của chúng em để biết thêm thông tin chi tiết nhé 👉. Anh thích phong cách váy cưới nào? 👰','{\"model\": \"llama-3.3-70b-versatile\"}','2025-12-09 02:52:37'),(5,NULL,'guest_ruvt4g8h81it0re4i54ov4mvbh_1765328070','user','Tư vấn chọn váy',NULL,'2025-12-10 00:54:35'),(6,NULL,'guest_ruvt4g8h81it0re4i54ov4mvbh_1765328070','bot','Chị ơi 💕, em có thể giúp chị chọn váy cưới đẹp không ạ? 🤔 Em cần biết thêm một số thông tin về chị như chiều cao, cân nặng, số đo 3 vòng và phong cách yêu thích của chị là gì nhé 💖. Để em có thể tư vấn cho chị những mẫu váy phù hợp nhất 👰.','{\"model\": \"llama-3.3-70b-versatile\"}','2025-12-10 00:54:35'),(7,NULL,'guest_ruvt4g8h81it0re4i54ov4mvbh_1765328070','user','Bảng giá dịch vụ',NULL,'2025-12-10 02:38:54'),(8,NULL,'guest_ruvt4g8h81it0re4i54ov4mvbh_1765328070','bot','Chị ơi 💕, em gửi chị bảng giá dịch vụ của Váy Cưới Thiên Thần - Garden Home nhé 📝:\n- Cho thuê váy cưới: 2.000.000đ - 15.000.000đ/bộ\n- May đo váy cưới: 5.000.000đ - 50.000.000đ\n- Cho thuê vest chú rể: 500.000đ - 3.000.000đ/bộ\n- Trang điểm cô dâu: 1.500.000đ - 5.000.000đ\n- Chụp ảnh cưới trọn gói: 8.000.000đ - 30.000.000đ\n- Phụ kiện cưới: giá cả tùy thuộc vào sản phẩm cụ thể 💖. Chị có thể liên hệ hotline 078.797.2075 hoặc truy cập website vaycuoithienthan.com để biết thêm thông tin chi tiết nhé 📞.','{\"model\": \"llama-3.3-70b-versatile\"}','2025-12-10 02:38:54'),(9,6,'user_6','user','Tư vấn chọn váy',NULL,'2025-12-19 06:34:16'),(10,6,'user_6','bot','Chào Thiên Vũ Đỗ 💕, em là Trà My, tư vấn viên của Váy Cưới Thiên Thần 👰. Để chọn được váy cưới đẹp và phù hợp, em cần biết thêm một số thông tin về chị nhé. Chị có thể cho em biết chiều cao, cân nặng, số đo 3 vòng và phong cách yêu thích của chị không ạ? 💬','{\"model\": \"llama-3.3-70b-versatile\"}','2025-12-19 06:34:16'),(11,6,'user_6','user','Đặt lịch hẹn',NULL,'2025-12-19 06:34:23'),(12,6,'user_6','bot','Thiên Vũ Đỗ ạ, chị muốn đặt lịch hẹn để được tư vấn trực tiếp về váy cưới phải không? 😊 Chị có thể truy cập vào link này: booking.php để đặt lịch hẹn hoặc gọi hotline 0388853044 của chúng em nhé. Em sẽ hỗ trợ chị chọn được váy cưới đẹp và phù hợp nhất 💕!','{\"model\": \"llama-3.3-70b-versatile\"}','2025-12-19 06:34:23'),(13,NULL,'guest_jsn1s3qv857o81b89acauepgh5_1766211181','user','Bảng giá dịch vụ',NULL,'2025-12-20 06:13:05'),(14,NULL,'guest_jsn1s3qv857o81b89acauepgh5_1766211181','bot','Chị ơi, em gửi bảng giá dịch vụ của Váy Cưới Thiên Thần cho chị nhé 💕\nDịch vụ cho thuê váy cưới giá từ 2.000.000đ - 15.000.000đ/bộ, may đo váy cưới từ 5.000.000đ - 50.000.000đ, cho thuê vest chú rể từ 500.000đ - 3.000.000đ/bộ, trang điểm cô dâu từ 1.500.000đ - 5.000.000đ, chụp ảnh cưới trọn gói từ 8.000.000đ - 30.000.000đ. Chị muốn biết thêm thông tin chi tiết thì có thể gọi hotline 0388853044 hoặc vào trang web vaycuoithienthan.com nhé 👰✨','{\"model\": \"llama-3.3-70b-versatile\"}','2025-12-20 06:13:05'),(15,6,'user_6_1766394554','user','hello',NULL,'2025-12-22 09:09:29'),(16,6,'user_6_1766394554','bot','Xin chào Thiên Vũ Đỗ ạ! 😊 Em là Trà My, tư vấn viên của Váy Cưới Thiên Thần. Chị cần hỗ trợ gì về váy cưới không ạ? 💕','{\"model\": \"llama-3.3-70b-versatile\"}','2025-12-22 09:09:29'),(17,6,'user_6_1766394581','user','Bảng giá dịch vụ',NULL,'2025-12-22 09:09:44'),(18,6,'user_6_1766394581','bot','Chào Thiên Vũ Đỗ 😊, em gửi bảng giá dịch vụ của Váy Cưới Thiên Thần cho chị nhé: \n1. Cho thuê váy cưới: 2.000.000đ - 15.000.000đ/bộ\n2. May đo váy cưới: 5.000.000đ - 50.000.000đ\n3. Cho thuê vest chú rể: 500.000đ - 3.000.000đ/bộ\n4. Trang điểm cô dâu: 1.500.000đ - 5.000.000đ\n5. Chụp ảnh cưới trọn gói: 8.000.000đ - 30.000.000đ\n6. Phụ kiện cưới: giá tùy thuộc vào loại phụ kiện 💕. Chị muốn biết thêm thông tin nào không ạ? 👰','{\"model\": \"llama-3.3-70b-versatile\"}','2025-12-22 09:09:44'),(19,NULL,'guest_l02l1tld5mk6kq894qag0m1hqo_1766535520','user','hi',NULL,'2025-12-24 00:18:43'),(20,NULL,'guest_l02l1tld5mk6kq894qag0m1hqo_1766535520','bot','Chị ơi, em Trà My đây 👋💕! Chị cần tìm váy cưới xinh xắn không ạ? Hãy cho em biết chị đang tìm kiếm phong cách nào nhé, để em có thể tư vấn tốt nhất cho chị 💕!','{\"model\": \"llama-3.3-70b-versatile\"}','2025-12-24 00:18:43'),(21,NULL,'guest_l02l1tld5mk6kq894qag0m1hqo_1766535520','user','hi',NULL,'2025-12-24 00:21:27'),(22,NULL,'guest_l02l1tld5mk6kq894qag0m1hqo_1766535520','bot','Chào chị ơi 💕! Em Trà My tư vấn viên của Váy Cưới Thiên Thần đây 👰. Chị cần tìm váy cưới hay dịch vụ gì không ạ? Hãy cho em biết để em có thể giúp chị nhé 💖!','{\"model\": \"llama-3.3-70b-versatile\"}','2025-12-24 00:21:27'),(23,NULL,'guest_ocr5skcpje0v41u1kgttv5fpa9_1767140369','user','Bảng giá dịch vụ',NULL,'2025-12-31 00:19:34'),(24,NULL,'guest_ocr5skcpje0v41u1kgttv5fpa9_1767140369','bot','Chị ơi, em gửi chị bảng giá dịch vụ của Váy Cưới Thiên Thần nhé 💕\nCho thuê váy cưới: 2.000.000đ - 15.000.000đ/bộ\nMay đo váy cưới: 5.000.000đ - 50.000.000đ\nCho thuê vest chú rể: 500.000đ - 3.000.000đ/bộ\nTrang điểm cô dâu: 1.500.000đ - 5.000.000đ\nChụp ảnh cưới trọn gói: 8.000.000đ - 30.000.000đ\nPhụ kiện cưới: giá cả tùy theo sản phẩm cụ thể 👰\nChị muốn biết thêm thông tin gì không ạ? ✨','{\"model\": \"llama-3.3-70b-versatile\"}','2025-12-31 00:19:34');
/*!40000 ALTER TABLE `lich_su_chatbot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lien_he`
--

DROP TABLE IF EXISTS `lien_he`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lien_he` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `image_path` varchar(300) DEFAULT NULL,
  `status` enum('new','replied','closed') DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `email_is_valid` tinyint(1) DEFAULT '1' COMMENT 'Email có đúng format không',
  `email_is_real` tinyint(1) DEFAULT '1' COMMENT 'Email có thật không (kiểm tra DNS, MX record)',
  `email_validation_reason` varchar(255) DEFAULT NULL COMMENT 'Lý do kết quả xác thực',
  `email_validation_details` text COMMENT 'Chi tiết xác thực (JSON)',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `lien_he_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lien_he`
--

LOCK TABLES `lien_he` WRITE;
/*!40000 ALTER TABLE `lien_he` DISABLE KEYS */;
INSERT INTO `lien_he` VALUES (1,NULL,'Lê Thị Cúc','cuc.le@example.com','0987654321','Hỏi về giá thuê váy','Xin chào, tôi muốn hỏi giá thuê mẫu váy VC003. Cảm ơn.',NULL,'new','2025-10-22 02:42:11',1,1,'Email hợp lệ và có thể nhận được','{\"domain_exists\":true,\"mx_records\":true,\"mx_count\":1,\"is_temp_email\":false,\"is_trusted_provider\":false,\"suspicious_username\":false}'),(2,NULL,'vãng lai','vanglai@gmail.com','','Khác','qua coi chơi',NULL,'replied','2025-12-10 00:47:09',1,1,'Email từ nhà cung cấp uy tín','{\"domain_exists\":true,\"mx_records\":true,\"mx_count\":5,\"is_temp_email\":false,\"is_trusted_provider\":true,\"suspicious_username\":false}'),(3,NULL,'Lê Thị Hoa','admin@lagvintage.com','0388843044','Đặt lịch thử váy','váy bị rách','uploads/contacts/contact_1765333871_6938db6f0ff6d.jpg','new','2025-12-10 02:31:11',1,0,'Tên miền không tồn tại','{\"domain_exists\":false}'),(4,NULL,'Lê Thị Hoa','admin@lagvintage.com','0388843044','Đặt lịch thử váy','váy bị rách','uploads/contacts/contact_1765334224_6938dcd066c05.jpg','replied','2025-12-10 02:37:04',1,0,'Tên miền không tồn tại','{\"domain_exists\":false}'),(5,NULL,'Nguyễn Huỳnh Kỹ Thuật Thuật','nguyenhuynhkithuat84tv@gmail.com','0388853044','Khiếu nại dịch vụ','nhân viên tệ',NULL,'replied','2025-12-10 02:54:40',1,1,'Email từ nhà cung cấp uy tín (Tên người dùng đáng ngờ)','{\"domain_exists\":true,\"mx_records\":true,\"mx_count\":5,\"is_temp_email\":false,\"is_trusted_provider\":true,\"suspicious_username\":true}');
/*!40000 ALTER TABLE `lien_he` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nguoi_dung`
--

DROP TABLE IF EXISTS `nguoi_dung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nguoi_dung` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `ho_ten` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `so_dien_thoai` varchar(30) DEFAULT NULL,
  `dia_chi` text,
  `tinh_thanh` varchar(100) DEFAULT NULL COMMENT 'Mã tỉnh/thành phố',
  `quan_huyen` varchar(100) DEFAULT NULL COMMENT 'Mã quận/huyện',
  `phuong_xa` varchar(100) DEFAULT NULL COMMENT 'Mã phường/xã',
  `dia_chi_cu_the` varchar(500) DEFAULT NULL COMMENT 'Địa chỉ cụ thể (số nhà, đường...)',
  `avt` varchar(1000) DEFAULT NULL,
  `status` enum('active','locked','disabled') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_nguoi_dung_tinh` (`tinh_thanh`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nguoi_dung`
--

LOCK TABLES `nguoi_dung` WRITE;
/*!40000 ALTER TABLE `nguoi_dung` DISABLE KEYS */;
INSERT INTO `nguoi_dung` VALUES (2,'Trần Văn Bình','binh.tran@example.com','hashed_password_456','0912345678','456 Đường XYZ, Quận 3, TP.HCM',NULL,NULL,NULL,NULL,NULL,'active','2025-10-22 02:42:11'),(3,'Hứa Thị Thảo Vy','HuaThaoVy123@gmail.com','$2y$10$HbYd9RU5DreGZhnTiOP2..lce9ZmNU7psOXshDv2yx5L5eEzper9m','0388853044','Trà Vinh',NULL,NULL,NULL,NULL,'uploads/avatars/691d3167471ab_1763520871.jpg','active','2025-11-19 01:09:00'),(4,'Thuật Thuật','nguyenhuynhkithuat84tv@gmail.com','$2y$10$QSJqNyKcrFRfepyZlb3MX.T5c7zcnv9sS80WZ7oQ9bVlnTZ8duQL.',NULL,NULL,NULL,NULL,NULL,NULL,'https://lh3.googleusercontent.com/a/ACg8ocIo90HHFVO_TpBzGlbr-kcFij7f4VyqjWwWZYUSGqJIjRzSHp85=s96-c','active','2025-11-19 03:19:21'),(5,'Trường Nguyễn','nhattruong.261097@gmail.com','$2y$10$cS/.IuwH4wcpVK3bqvRemOyGfSLWPqxUGqwe8yffJaKCoTqcqU1Ua',NULL,NULL,NULL,NULL,NULL,NULL,'https://lh3.googleusercontent.com/a/ACg8ocK2WwYr6Ro0RBWQ8YoFRyBgU4F-PRX9Mq7ki4D7oDufxQUkKtH2=s96-c','active','2025-11-19 03:21:08'),(6,'Thiên Vũ Đỗ','dothienvu84tv@gmail.com','$2y$10$XmwsR5Yl9H/B58wbAtEHTO332qum7gmhv5jGBVhIhDT83JjVDfwjG',NULL,NULL,NULL,NULL,NULL,NULL,'https://lh3.googleusercontent.com/a/ACg8ocJtGsRrsTVGZKZl9h9Ug3fZgeVQ9O8hVafBB1jaWnRpKAW6rA=s96-c','active','2025-11-26 00:36:02'),(7,'Phạm Quang Vinh','vphamquang539@gmail.com','$2y$10$69iInYnd24CRNv/UrlCNQuthVE60/nIMV5OBwjdIykTUiEZEzvL2e','0388853048','Phường Long Đức Tỉnh Vĩnh Long',NULL,NULL,NULL,NULL,'uploads/avatars/692fa0ce8bb69_1764729038.jpg','active','2025-12-03 02:31:06'),(8,'Huỳnh Quốc Nhân','nhanhuynhtv345@gmail.com','$2y$10$r9p3OJtTtGCtguLCel5U8OUI8WNmsEjpKfXYQteYnmq4Qu0G/LbvO','0377753044','Trà Vinh',NULL,NULL,NULL,NULL,NULL,'locked','2025-12-14 07:37:45');
/*!40000 ALTER TABLE `nguoi_dung` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otp_verification`
--

DROP TABLE IF EXISTS `otp_verification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otp_verification` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `ho_ten` varchar(255) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `so_dien_thoai` varchar(30) DEFAULT NULL,
  `dia_chi` text,
  `avt` varchar(500) DEFAULT NULL,
  `expires_at` datetime NOT NULL COMMENT 'Thời gian hết hạn OTP (5 phút)',
  `is_verified` tinyint(1) DEFAULT '0' COMMENT '0 = chưa xác nhận, 1 = đã xác nhận',
  `attempts` int DEFAULT '0' COMMENT 'Số lần nhập sai',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_otp` (`otp_code`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Lưu mã OTP xác nhận đăng ký';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otp_verification`
--

LOCK TABLES `otp_verification` WRITE;
/*!40000 ALTER TABLE `otp_verification` DISABLE KEYS */;
/*!40000 ALTER TABLE `otp_verification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset`
--

DROP TABLE IF EXISTS `password_reset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `is_used` tinyint(1) DEFAULT '0',
  `attempts` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset`
--

LOCK TABLES `password_reset` WRITE;
/*!40000 ALTER TABLE `password_reset` DISABLE KEYS */;
INSERT INTO `password_reset` VALUES (1,'vphamquang539@gmail.com','982892','2025-12-09 08:26:14',0,0,'2025-12-09 01:16:14');
/*!40000 ALTER TABLE `password_reset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quang_cao`
--

DROP TABLE IF EXISTS `quang_cao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quang_cao` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `admin_id` int DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `image_url` varchar(1024) DEFAULT NULL,
  `link_url` varchar(1024) DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `idx_active_time` (`active`,`start_at`,`end_at`),
  CONSTRAINT `quang_cao_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quang_cao`
--

LOCK TABLES `quang_cao` WRITE;
/*!40000 ALTER TABLE `quang_cao` DISABLE KEYS */;
/*!40000 ALTER TABLE `quang_cao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `thanh_toan`
--

DROP TABLE IF EXISTS `thanh_toan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `thanh_toan` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `hoa_don_id` bigint DEFAULT NULL,
  `don_hang_id` bigint DEFAULT NULL,
  `payment_gateway` varchar(100) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `amount` decimal(14,2) NOT NULL,
  `status` enum('initiated','success','failed','refunded') DEFAULT 'initiated',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `hoa_don_id` (`hoa_don_id`),
  KEY `don_hang_id` (`don_hang_id`),
  KEY `idx_tx` (`transaction_id`),
  CONSTRAINT `thanh_toan_ibfk_1` FOREIGN KEY (`hoa_don_id`) REFERENCES `hoa_don` (`id`) ON DELETE SET NULL,
  CONSTRAINT `thanh_toan_ibfk_2` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thanh_toan`
--

LOCK TABLES `thanh_toan` WRITE;
/*!40000 ALTER TABLE `thanh_toan` DISABLE KEYS */;
INSERT INTO `thanh_toan` VALUES (1,1,1,'VNPAY','VNP123456789XYZ',5000000.00,'success','2025-10-22 02:42:11','2025-10-22 02:42:11'),(2,13,13,'qr_code','TT202511200913579894',5250000.00,'initiated',NULL,'2025-11-20 08:13:57'),(3,14,14,'qr_code','TT202511200930345914',3150000.00,'initiated',NULL,'2025-11-20 08:30:34'),(4,15,15,'qr_code','TT202511201039172868',5250000.00,'initiated',NULL,'2025-11-20 09:39:17'),(5,16,16,'qr_code','TT202511270903162966',10500000.00,'initiated',NULL,'2025-11-27 08:03:16'),(6,17,17,'qr_code','TT202511270911506412',5250000.00,'initiated',NULL,'2025-11-27 08:11:50'),(7,18,18,'qr_code','TT202512030147552896',10500000.00,'initiated',NULL,'2025-12-03 00:47:55'),(8,19,19,'qr_code','TT202512030150344120',10500000.00,'initiated',NULL,'2025-12-03 00:50:34'),(9,20,20,'qr_code','TT202512030155579467',10500000.00,'initiated',NULL,'2025-12-03 00:55:57'),(10,20,20,'momo','MOMO_20_1764723357',10500000.00,'initiated',NULL,'2025-12-03 00:55:57'),(11,21,21,'qr_code','TT202512030201513704',94500000.00,'initiated',NULL,'2025-12-03 01:01:51'),(12,22,22,'qr_code','TT202512030203547711',10500000.00,'initiated',NULL,'2025-12-03 01:03:54'),(13,22,22,'momo','MOMO_22_1764723835',10500000.00,'initiated',NULL,'2025-12-03 01:03:55'),(14,23,23,'qr_code','TT202512030212211381',10500000.00,'initiated',NULL,'2025-12-03 01:12:21'),(15,23,23,'momo','MOMO_23_1764724341',10500000.00,'failed',NULL,'2025-12-03 01:12:22'),(16,24,24,'qr_code','TT202512030336231874',10500000.00,'initiated',NULL,'2025-12-03 02:36:23'),(17,24,24,'momo','MOMO_24_1764729383',10500000.00,'failed',NULL,'2025-12-03 02:36:24'),(18,25,25,'qr_code','TT202512030339087938',10500000.00,'initiated',NULL,'2025-12-03 02:39:08'),(19,25,25,'momo','MOMO_25_1764729548',10500000.00,'initiated',NULL,'2025-12-03 02:39:08'),(20,26,26,'qr_code','TT202512030344203607',10500000.00,'initiated',NULL,'2025-12-03 02:44:20'),(21,26,26,'momo','MOMO_26_1764729860',10500000.00,'success','2025-12-03 02:46:54','2025-12-03 02:44:20'),(22,27,27,'qr_code','TT202512030850519217',10500000.00,'initiated',NULL,'2025-12-03 07:50:51'),(23,27,27,'momo','MOMO_27_1764748251',10500000.00,'failed',NULL,'2025-12-03 07:50:54'),(24,28,28,'qr_code','TT202512240130539294',1155000.00,'initiated',NULL,'2025-12-24 00:30:53'),(25,28,28,'momo','MOMO_28_1766536253',1155000.00,'initiated',NULL,'2025-12-24 00:30:53');
/*!40000 ALTER TABLE `thanh_toan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `thanh_toan_qr`
--

DROP TABLE IF EXISTS `thanh_toan_qr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `thanh_toan_qr` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `don_hang_id` bigint NOT NULL,
  `ma_giao_dich` varchar(100) NOT NULL COMMENT 'Mã giao dịch duy nhất',
  `so_tien` decimal(14,2) NOT NULL COMMENT 'Số tiền cần thanh toán',
  `noi_dung_chuyen_khoan` varchar(255) NOT NULL COMMENT 'Nội dung chuyển khoản',
  `qr_code_url` text COMMENT 'URL của QR code',
  `qr_data` text COMMENT 'Dữ liệu QR code',
  `trang_thai` enum('pending','paid','expired','cancelled') DEFAULT 'pending',
  `thoi_gian_het_han` datetime NOT NULL COMMENT 'Thời gian hết hạn (10 phút)',
  `thoi_gian_thanh_toan` datetime DEFAULT NULL COMMENT 'Thời gian thanh toán thực tế',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_giao_dich` (`ma_giao_dich`),
  KEY `don_hang_id` (`don_hang_id`),
  KEY `idx_ma_giao_dich` (`ma_giao_dich`),
  KEY `idx_trang_thai` (`trang_thai`),
  KEY `idx_het_han` (`thoi_gian_het_han`),
  CONSTRAINT `thanh_toan_qr_ibfk_1` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Thanh toán qua QR code';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thanh_toan_qr`
--

LOCK TABLES `thanh_toan_qr` WRITE;
/*!40000 ALTER TABLE `thanh_toan_qr` DISABLE KEYS */;
/*!40000 ALTER TABLE `thanh_toan_qr` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `thong_bao`
--

DROP TABLE IF EXISTS `thong_bao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `thong_bao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nguoi_dung_id` bigint NOT NULL COMMENT 'ID người nhận thông báo',
  `loai` enum('admin_reply','order_update','new_blog','promotion','system','comment_reply') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tieu_de` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tiêu đề thông báo',
  `noi_dung` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nội dung thông báo',
  `link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Link đến trang liên quan',
  `da_doc` tinyint(1) DEFAULT '0' COMMENT '0 = chưa đọc, 1 = đã đọc',
  `reference_id` int DEFAULT NULL COMMENT 'ID tham chiếu (comment_id, order_id, blog_id...)',
  `reference_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Loại tham chiếu (comment, order, blog...)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `read_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian đọc',
  PRIMARY KEY (`id`),
  KEY `idx_nguoi_dung` (`nguoi_dung_id`),
  KEY `idx_da_doc` (`da_doc`),
  KEY `idx_loai` (`loai`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `thong_bao_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thong_bao`
--

LOCK TABLES `thong_bao` WRITE;
/*!40000 ALTER TABLE `thong_bao` DISABLE KEYS */;
INSERT INTO `thong_bao` VALUES (1,2,'new_blog','Bài viết mới: Trang trí cổng đám cưới','Chúng tôi vừa đăng bài viết mới \"Trang trí cổng đám cưới\". Xem ngay!','blog-detail.php?slug=trang-tr-c-ng-m-c-i-1764753958',0,6,'blog','2025-12-03 09:25:58',NULL),(2,3,'new_blog','Bài viết mới: Trang trí cổng đám cưới','Chúng tôi vừa đăng bài viết mới \"Trang trí cổng đám cưới\". Xem ngay!','blog-detail.php?slug=trang-tr-c-ng-m-c-i-1764753958',0,6,'blog','2025-12-03 09:25:58',NULL),(3,4,'new_blog','Bài viết mới: Trang trí cổng đám cưới','Chúng tôi vừa đăng bài viết mới \"Trang trí cổng đám cưới\". Xem ngay!','blog-detail.php?slug=trang-tr-c-ng-m-c-i-1764753958',0,6,'blog','2025-12-03 09:25:58',NULL),(4,5,'new_blog','Bài viết mới: Trang trí cổng đám cưới','Chúng tôi vừa đăng bài viết mới \"Trang trí cổng đám cưới\". Xem ngay!','blog-detail.php?slug=trang-tr-c-ng-m-c-i-1764753958',0,6,'blog','2025-12-03 09:25:58',NULL),(5,6,'new_blog','Bài viết mới: Trang trí cổng đám cưới','Chúng tôi vừa đăng bài viết mới \"Trang trí cổng đám cưới\". Xem ngay!','blog-detail.php?slug=trang-tr-c-ng-m-c-i-1764753958',1,6,'blog','2025-12-03 09:25:58','2025-12-10 08:55:23'),(6,7,'new_blog','Bài viết mới: Trang trí cổng đám cưới','Chúng tôi vừa đăng bài viết mới \"Trang trí cổng đám cưới\". Xem ngay!','blog-detail.php?slug=trang-tr-c-ng-m-c-i-1764753958',1,6,'blog','2025-12-03 09:25:58','2025-12-04 07:24:18'),(7,7,'admin_reply','Admin đã trả lời bình luận của bạn','Admin đã trả lời bình luận của bạn trong bài viết \"Trang trí cổng đám cưới\"','blog-detail.php?id=6#comments',1,6,'comment_blog','2025-12-04 08:58:06','2025-12-04 08:58:20'),(9,2,'system','Test notification','Đây là test thông báo','index.php',0,NULL,NULL,'2025-12-10 07:54:42',NULL),(10,7,'system','Test thông báo','Đây là thông báo test lúc 09:04:17 10/12/2025','index.php',1,NULL,NULL,'2025-12-10 08:04:17','2025-12-10 08:14:24'),(11,7,'system','Test thông báo','Đây là thông báo test lúc 09:06:29 10/12/2025','index.php',0,NULL,NULL,'2025-12-10 08:06:29',NULL),(12,7,'system','Test thông báo','Đây là thông báo test lúc 09:06:30 10/12/2025','index.php',0,NULL,NULL,'2025-12-10 08:06:30',NULL),(13,7,'system','Test thông báo','Đây là thông báo test lúc 09:06:43 10/12/2025','index.php',0,NULL,NULL,'2025-12-10 08:06:43',NULL),(14,7,'system','Test thông báo','Đây là thông báo test lúc 09:06:47 10/12/2025','index.php',1,NULL,NULL,'2025-12-10 08:06:47','2025-12-10 08:13:17'),(18,2,'comment_reply','Hứa Thị Thảo Vy đã trả lời bình luận của bạn','\"Reply của User 2 vào comment User 1 - 2025-12-10 0...\" - trong sản phẩm \"Váy Công Chúa Bồng Bềnh\"','product-detail.php?id=1#comments',0,1,'comment_product','2025-12-10 08:22:38',NULL),(19,2,'comment_reply','Hứa Thị Thảo Vy đã trả lời bình luận của bạn','\"Đây là nội dung trả lời test\" - trong sản phẩm \"Váy Công Chúa Bồng Bềnh\"','product-detail.php?id=1#comments',0,1,'comment_product','2025-12-10 08:22:40',NULL),(20,2,'comment_reply','Hứa Thị Thảo Vy đã trả lời bình luận của bạn','\"Đây là nội dung trả lời test\" - trong sản phẩm \"Váy Công Chúa Bồng Bềnh\"','product-detail.php?id=1#comments',0,1,'comment_product','2025-12-10 08:32:12',NULL),(21,2,'comment_reply','Hứa Thị Thảo Vy đã trả lời bình luận của bạn','\"Reply của User 2 vào comment User 1 - 2025-12-10 0...\" - trong sản phẩm \"Váy Công Chúa Bồng Bềnh\"','product-detail.php?id=1#comments',0,1,'comment_product','2025-12-10 08:32:13',NULL),(22,2,'comment_reply','Hứa Thị Thảo Vy đã trả lời bình luận của bạn','\"Đây là nội dung trả lời test\" - trong sản phẩm \"Váy Công Chúa Bồng Bềnh\"','product-detail.php?id=1#comments',0,1,'comment_product','2025-12-10 08:32:18',NULL),(23,2,'comment_reply','Phạm Quang Vinh đã trả lời bình luận của bạn','\"Test reply - 09:37:23 10/12/2025\" - trong sản phẩm \"Váy Công Chúa Bồng Bềnh\"','product-detail.php?id=1#comments',0,1,'comment_product','2025-12-10 08:37:23',NULL),(24,7,'comment_reply','Thiên Vũ Đỗ đã trả lời bình luận của bạn','\"Test reply từ User 6 - 09:41:13 10/12/2025\" - trong bài viết \"Trang trí cổng đám cưới\"','blog-detail.php?id=6#comments',1,6,'comment_blog','2025-12-10 08:41:13','2025-12-10 08:45:40'),(25,6,'comment_reply','Phạm Quang Vinh đã trả lời bình luận của bạn','\"mày muốn gì\" - trong bài viết \"Trang trí cổng đám cưới\"','blog-detail.php?id=6#comments',1,6,'comment_blog','2025-12-10 08:45:58','2025-12-10 08:55:30'),(26,7,'comment_reply','Thiên Vũ Đỗ đã trả lời bình luận của bạn','\"Test reply content\" - trong bài viết \"Test Blog Title\"','blog-detail.php?id=6#comment-999',0,6,'comment_blog','2025-12-10 08:54:46',NULL),(27,6,'admin_reply','Admin đã trả lời bình luận của bạn','Admin đã trả lời bình luận của bạn trong sản phẩm \"Váy tay phồng cổ vuông thiết kế dáng ngắn trẻ trung\"','product-detail.php?id=14#comments',1,14,'comment_product','2025-12-24 00:24:06','2025-12-24 00:24:18'),(28,6,'order_update','Cập nhật đơn hàng #DH20251224013053370','Đơn hàng #DH20251224013053370 của bạn completed','order-detail.php?id=28',1,28,'order','2025-12-24 00:35:50','2025-12-28 06:52:59');
/*!40000 ALTER TABLE `thong_bao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `thong_bao_blog_sent`
--

DROP TABLE IF EXISTS `thong_bao_blog_sent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `thong_bao_blog_sent` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bai_viet_id` int NOT NULL,
  `nguoi_dung_id` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_blog_user` (`bai_viet_id`,`nguoi_dung_id`),
  KEY `nguoi_dung_id` (`nguoi_dung_id`),
  CONSTRAINT `thong_bao_blog_sent_ibfk_1` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thong_bao_blog_sent`
--

LOCK TABLES `thong_bao_blog_sent` WRITE;
/*!40000 ALTER TABLE `thong_bao_blog_sent` DISABLE KEYS */;
/*!40000 ALTER TABLE `thong_bao_blog_sent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `thong_ke_hang_ngay`
--

DROP TABLE IF EXISTS `thong_ke_hang_ngay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `thong_ke_hang_ngay` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `stat_date` date NOT NULL,
  `total_orders` int DEFAULT '0',
  `total_revenue` decimal(14,2) DEFAULT '0.00',
  `total_visitors` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stat_date` (`stat_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thong_ke_hang_ngay`
--

LOCK TABLES `thong_ke_hang_ngay` WRITE;
/*!40000 ALTER TABLE `thong_ke_hang_ngay` DISABLE KEYS */;
/*!40000 ALTER TABLE `thong_ke_hang_ngay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tin_tuc_cuoi_hoi`
--

DROP TABLE IF EXISTS `tin_tuc_cuoi_hoi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tin_tuc_cuoi_hoi` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `admin_id` int DEFAULT NULL,
  `title` varchar(300) NOT NULL,
  `slug` varchar(300) NOT NULL,
  `summary` text,
  `content` longtext,
  `promotion_code` varchar(80) DEFAULT NULL,
  `cover_image` varchar(1024) DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `admin_id` (`admin_id`),
  KEY `idx_status_pub` (`status`,`published_at`),
  KEY `fk_promotion_code` (`promotion_code`),
  CONSTRAINT `fk_promotion_code` FOREIGN KEY (`promotion_code`) REFERENCES `khuyen_mai` (`code`) ON DELETE SET NULL,
  CONSTRAINT `tin_tuc_cuoi_hoi_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tin_tuc_cuoi_hoi`
--

LOCK TABLES `tin_tuc_cuoi_hoi` WRITE;
/*!40000 ALTER TABLE `tin_tuc_cuoi_hoi` DISABLE KEYS */;
INSERT INTO `tin_tuc_cuoi_hoi` VALUES (1,1,'Xu Hướng Váy Cưới 2024','xu-huong-vay-cuoi-2024','Khám phá những xu hướng váy cưới hot nhất năm 2024.','Nội dung chi tiết về các xu hướng...',NULL,'uploads/blogs/1764125406_xu-huong-vay-chup-anh-cuoi-dep-tuy-hoa-phu-yen-bong-benh-he-thu-2.jpg','2023-11-20 10:00:00','published','2025-10-22 02:42:11'),(2,2,'Khuyễn mãi cuối năm','khuy-n-m-i-cu-i-n-m','Khuyễn mãi 50%','Khuyến mãi combo ',NULL,'uploads/blogs/1764181475_noel.jpg','2025-11-26 19:24:35','published','2025-11-26 18:24:36'),(3,2,'Trang trí cổng đám cưới','trang-tr-c-ng-m-c-i','Trang trí cổng đám cưới giảm ngay 30%','hãy nhấc máy lên và gọi cho chúng em để nhận ưu đãi tốt ạ. ',NULL,'uploads/blogs/1764753787_tong-hop-chi-phi-dam-cuoi-cho-nha-gai-gom-nhung-gi-2.jpg','2025-12-03 10:23:07','published','2025-12-03 09:23:07'),(6,2,'Trang trí cổng đám cưới','trang-tr-c-ng-m-c-i-1764753958','Trang trí cổng đám cưới giảm ngay 30%','hãy nhấc máy lên và gọi cho chúng em để nhận ưu đãi tốt ạ. ',NULL,'uploads/blogs/1764753958_tong-hop-chi-phi-dam-cuoi-cho-nha-gai-gom-nhung-gi-2.jpg','2025-12-03 10:25:58','published','2025-12-03 09:25:58');
/*!40000 ALTER TABLE `tin_tuc_cuoi_hoi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_coupon_usage`
--

DROP TABLE IF EXISTS `user_coupon_usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_coupon_usage` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` bigint NOT NULL,
  `coupon_code` varchar(80) NOT NULL,
  `order_id` bigint DEFAULT NULL,
  `discount_amount` decimal(12,2) NOT NULL,
  `used_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_coupon_order` (`user_id`,`coupon_code`,`order_id`),
  KEY `coupon_code` (`coupon_code`),
  KEY `order_id` (`order_id`),
  KEY `idx_user_coupon` (`user_id`,`coupon_code`),
  KEY `idx_used_at` (`used_at`),
  CONSTRAINT `user_coupon_usage_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_coupon_usage_ibfk_2` FOREIGN KEY (`coupon_code`) REFERENCES `khuyen_mai` (`code`) ON DELETE CASCADE,
  CONSTRAINT `user_coupon_usage_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `don_hang` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Theo dõi việc sử dụng coupon của từng user';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_coupon_usage`
--

LOCK TABLES `user_coupon_usage` WRITE;
/*!40000 ALTER TABLE `user_coupon_usage` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_coupon_usage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vay_cuoi`
--

DROP TABLE IF EXISTS `vay_cuoi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vay_cuoi` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `ma_vay` varchar(50) NOT NULL,
  `ten_vay` varchar(255) NOT NULL,
  `mo_ta` text,
  `phong_cach` enum('công chúa','đuôi cá','chữ a','hiện đại','vintage','minimalist') DEFAULT NULL COMMENT 'Phong cách váy cưới',
  `mau_sac` varchar(50) DEFAULT 'Trắng' COMMENT 'Màu sắc váy',
  `gia_thue` decimal(12,2) NOT NULL,
  `so_luong_ton` int DEFAULT '0',
  `size` text COMMENT 'JSON sizes',
  `hinh_anh_chinh` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_vay` (`ma_vay`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vay_cuoi`
--

LOCK TABLES `vay_cuoi` WRITE;
/*!40000 ALTER TABLE `vay_cuoi` DISABLE KEYS */;
INSERT INTO `vay_cuoi` VALUES (1,'VC001','Váy Công Chúa Bồng Bềnh','Váy cưới lộng lẫy với thiết kế công chúa, đính đá Swarovski.','công chúa','Trắng',5000000.00,5,'[{\"name\":\"S\",\"active\":true},{\"name\":\"M\",\"active\":true},{\"name\":\"L\",\"active\":true},{\"name\":\"XL\",\"active\":true}]',NULL,'2025-10-22 02:42:11'),(2,'VC002','Váy Đuôi Cá Quyến Rũ','Thiết kế đuôi cá tôn dáng, chất liệu ren cao cấp.','đuôi cá','Trắng',4500000.00,3,'[{\"name\":\"S\",\"active\":true},{\"name\":\"M\",\"active\":true},{\"name\":\"L\",\"active\":true},{\"name\":\"XL\",\"active\":true}]','uploads/dresses/1764126075_main_xu-huong-vay-chup-anh-cuoi-dep-tuy-hoa-phu-yen-bong-benh-he-thu-2.jpg','2025-10-22 02:42:11'),(3,'VC003','Váy Chữ A Tối Giản','Váy cưới phong cách minimalist, thanh lịch và sang trọng.','chữ a','Trắng',3000000.00,10,'[{\"name\":\"S\",\"active\":true},{\"name\":\"M\",\"active\":true},{\"name\":\"L\",\"active\":true},{\"name\":\"XL\",\"active\":true}]',NULL,'2025-10-22 02:42:11'),(4,'VC004','Váy Cưới Kiêu Sa','Váy Cưới sang trong, tone xanh toát lên vẻ đẹp cao quý',NULL,'Trắng',10000000.00,100,'[{\"name\":\"S\",\"active\":true},{\"name\":\"M\",\"active\":true},{\"name\":\"L\",\"active\":true},{\"name\":\"XL\",\"active\":true}]','uploads/dresses/1764181398_main_VayCuoiq.jpg','2025-11-26 18:23:18'),(5,'VC005','Váy cưới công chúa xoè bồng','Màu sắc: Trắng\r\nChất liệu: Voan Nhật dập ly + Lót',NULL,'Trắng',3500000.00,12,'[{\"name\":\"S\",\"active\":true},{\"name\":\"M\",\"active\":true},{\"name\":\"L\",\"active\":true},{\"name\":\"XL\",\"active\":true}]','uploads/dresses/1765328693_main_vay-cuoi-cong-chua-xoe-bong-3.webp','2025-12-10 01:04:53'),(6,'VC006','Váy cưới đẹp kiểu công chúa thiết kế đơn giản','Màu sắc: Trắng\r\nChất liệu: Voan Hàn phối ren hoạ tiết cao cấp',NULL,'Trắng',2900000.00,8,'[{\"name\":\"S\",\"active\":true},{\"name\":\"M\",\"active\":true},{\"name\":\"L\",\"active\":true},{\"name\":\"XL\",\"active\":true}]','uploads/dresses/1765329249_main_vay-cuoi-dep-kieu-cong-chua.webp','2025-12-10 01:14:09'),(7,'VC007','Váy cưới đi bàn màu đỏ kiểu dáng phối ren','Màu sắc: Trắng, Đỏ đô\r\nChất liệu: Phi lụa Hàn cao cấp + Ren',NULL,'Trắng',4200000.00,5,'[{\"name\":\"S\",\"active\":true},{\"name\":\"M\",\"active\":true},{\"name\":\"L\",\"active\":true},{\"name\":\"XL\",\"active\":true}]','uploads/dresses/1765329319_main_vay-cuoi-di-ban-mau-do.webp','2025-12-10 01:15:19'),(8,'VC008','Váy cưới xẻ tà trễ vai thiết kế đuôi cá ôm body tôn dáng','Màu sắc: Trắng\r\nChất liệu: Phi Hàn cao cấp + Voan',NULL,'Trắng',3950000.00,30,'[{\"name\":\"S\",\"active\":true},{\"name\":\"M\",\"active\":true},{\"name\":\"L\",\"active\":true},{\"name\":\"XL\",\"active\":true}]','uploads/dresses/1765329394_main_vay-cuoi-xe-ta-tre-vai.webp','2025-12-10 01:16:34'),(9,'VC009','Áo cưới tay dài đẹp thiết kế cổ vuông đơn giản','Màu sắc: Trắng\r\nChất liệu: Phi Hàn cao cấp',NULL,'Trắng',1985000.00,13,'[{\"name\":\"S\",\"active\":true},{\"name\":\"M\",\"active\":true},{\"name\":\"L\",\"active\":true},{\"name\":\"XL\",\"active\":true}]','uploads/dresses/1765329450_main_ao-cuoi-tay-dai-dep-2.webp','2025-12-10 01:17:30'),(10,'VC010','Váy cưới cúp ngực xoè thiết kế đơn giản','Màu sắc: Trắng\r\nChất liệu: Kim sa + Voan Hàn cao cấp',NULL,'Trắng',2350000.00,23,'[{\"name\":\"S\",\"active\":true},{\"name\":\"M\",\"active\":true},{\"name\":\"L\",\"active\":true},{\"name\":\"XL\",\"active\":true}]','uploads/dresses/1765329553_main_vay-cuoi-cup-nguc-xoe-deba5a06-5d4e-4c88-8c5c-fbc27584911f.webp','2025-12-10 01:19:13'),(11,'VC011','Váy cưới công chúa đi bàn trễ vai','Màu sắc: Trắng\r\nChất liệu: Gấm hoa văn cao cấp',NULL,'Trắng',1250000.00,9,'[{\"name\":\"S\",\"active\":true},{\"name\":\"M\",\"active\":true},{\"name\":\"L\",\"active\":true},{\"name\":\"XL\",\"active\":true}]','uploads/dresses/1765329660_main_va-y-cu-o-i-co-ng-chu-a-di-ba-n-2.jpg','2025-12-10 01:21:00'),(12,'VC012','Váy cưới ngắn đi bàn đính hoa eo','Màu sắc: Trắng\r\nChất liệu: Phi Hàn cao cấp',NULL,'Trắng',1750000.00,14,'[{\"name\":\"S\",\"active\":true},{\"name\":\"M\",\"active\":true},{\"name\":\"L\",\"active\":true},{\"name\":\"XL\",\"active\":true}]','uploads/dresses/1765329743_main_vay-trang-tay-phong-ngan.webp','2025-12-10 01:22:23'),(13,'VC013','Váy cưới ngắn xoè chất liệu ren tuyệt đẹp','Size: XS S M L\r\nChất liệu: Ren Hàn cao cấp + Lót',NULL,'Trắng',990000.00,16,'[{\"name\":\"M\",\"active\":true},{\"name\":\"S\",\"active\":true}]','uploads/dresses/1765329825_main_vay-cuoi-ngan-xoe4.webp','2025-12-10 01:23:45'),(14,'VC014','Váy tay phồng cổ vuông thiết kế dáng ngắn trẻ trung','Màu sắc: Trắng\r\nChất liệu: Ren Hàn cao cấp + Lót',NULL,'Trắng',1100000.00,15,'[{\"name\":\"S\",\"active\":true},{\"name\":\"M\",\"active\":true},{\"name\":\"XL\",\"active\":true}]','uploads/dresses/1765329919_main_vay-tay-phong-co-vuong.webp','2025-12-10 01:25:19');
/*!40000 ALTER TABLE `vay_cuoi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vay_cuoi_size`
--

DROP TABLE IF EXISTS `vay_cuoi_size`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vay_cuoi_size` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `vay_id` bigint NOT NULL,
  `size` enum('XS','S','M','L','XL','XXL','XXXL') NOT NULL,
  `so_luong` int DEFAULT '1' COMMENT 'Số lượng váy theo size này',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_vay_size` (`vay_id`,`size`),
  CONSTRAINT `vay_cuoi_size_ibfk_1` FOREIGN KEY (`vay_id`) REFERENCES `vay_cuoi` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Bảng quản lý size của từng váy cưới';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vay_cuoi_size`
--

LOCK TABLES `vay_cuoi_size` WRITE;
/*!40000 ALTER TABLE `vay_cuoi_size` DISABLE KEYS */;
INSERT INTO `vay_cuoi_size` VALUES (1,1,'S',1,'2025-12-03 07:13:09'),(2,1,'M',2,'2025-12-03 07:13:09'),(3,1,'L',2,'2025-12-03 07:13:09'),(4,2,'S',1,'2025-12-03 07:13:09'),(5,2,'M',1,'2025-12-03 07:13:09'),(6,2,'L',1,'2025-12-03 07:13:09'),(7,3,'XS',2,'2025-12-03 07:13:09'),(8,3,'S',3,'2025-12-03 07:13:09'),(9,3,'M',3,'2025-12-03 07:13:09'),(10,3,'L',2,'2025-12-03 07:13:09');
/*!40000 ALTER TABLE `vay_cuoi_size` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-08 13:57:20
