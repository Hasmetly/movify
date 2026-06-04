-- ============================================
-- MOVIFY - Streaming Platform Database Schema
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+03:00";

CREATE DATABASE IF NOT EXISTS `movify` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `movify`;

-- =====================
-- USERS TABLE
-- =====================
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','user') NOT NULL DEFAULT 'user',
  `subscription_status` ENUM('active','inactive','expired') NOT NULL DEFAULT 'active',
  `notifications_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `reset_token` VARCHAR(255) DEFAULT NULL,
  `reset_token_expiry` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- PROFILES TABLE
-- =====================
CREATE TABLE `profiles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `profile_name` VARCHAR(50) NOT NULL,
  `avatar_image` VARCHAR(255) DEFAULT 'default-avatar.png',
  `is_child` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_profiles_user` (`user_id`),
  CONSTRAINT `fk_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- SUBSCRIPTIONS TABLE
-- =====================
CREATE TABLE `subscriptions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `plan_name` VARCHAR(100) NOT NULL DEFAULT 'Deneme Aboneliği',
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('active','cancelled','expired') NOT NULL DEFAULT 'active',
  `start_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_date` DATETIME DEFAULT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'Ücretsiz Deneme',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_subscriptions_user` (`user_id`),
  CONSTRAINT `fk_subscriptions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- CATEGORIES TABLE
-- =====================
CREATE TABLE `categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- CONTENT TABLE
-- =====================
CREATE TABLE `content` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `type` ENUM('movie','series') NOT NULL DEFAULT 'movie',
  `description` TEXT DEFAULT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `banner_image` VARCHAR(255) DEFAULT NULL,
  `release_year` INT(4) DEFAULT NULL,
  `age_rating` VARCHAR(10) DEFAULT NULL,
  `imdb_rating` DECIMAL(3,1) DEFAULT NULL,
  `rotten_tomatoes_rating` INT(3) DEFAULT NULL,
  `duration_minutes` INT(5) DEFAULT NULL,
  `views_count` INT(11) NOT NULL DEFAULT 0,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_coming_soon` TINYINT(1) NOT NULL DEFAULT 0,
  `is_exclusive` TINYINT(1) NOT NULL DEFAULT 0,
  `is_top10` TINYINT(1) NOT NULL DEFAULT 0,
  `coming_soon_date` DATE DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `ft_content_title` (`title`),
  FULLTEXT KEY `ft_content_search` (`title`, `description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- CONTENT_CATEGORIES (JUNCTION TABLE)
-- =====================
CREATE TABLE `content_categories` (
  `content_id` INT(11) NOT NULL,
  `category_id` INT(11) NOT NULL,
  PRIMARY KEY (`content_id`, `category_id`),
  KEY `fk_cc_category` (`category_id`),
  CONSTRAINT `fk_cc_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cc_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- CONTENT_LINKS TABLE
-- =====================
CREATE TABLE `content_links` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `content_id` INT(11) NOT NULL,
  `link_type` ENUM('trailer','movie_file','youtube_movie') NOT NULL DEFAULT 'trailer',
  `label` VARCHAR(100) DEFAULT NULL,
  `url` TEXT NOT NULL,
  `sort_order` INT(3) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_cl_content` (`content_id`),
  CONSTRAINT `fk_cl_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- CONTENT_CAST TABLE
-- =====================
CREATE TABLE `content_cast` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `content_id` INT(11) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `role` ENUM('actor','director') NOT NULL DEFAULT 'actor',
  `character_name` VARCHAR(150) DEFAULT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `sort_order` INT(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_cast_content` (`content_id`),
  CONSTRAINT `fk_cast_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- EPISODES TABLE
-- =====================
CREATE TABLE `episodes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `content_id` INT(11) NOT NULL,
  `season_number` INT(3) NOT NULL DEFAULT 1,
  `episode_number` INT(3) NOT NULL DEFAULT 1,
  `title` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `video_url` TEXT DEFAULT NULL,
  `thumbnail` VARCHAR(255) DEFAULT NULL,
  `duration` VARCHAR(20) DEFAULT NULL,
  `intro_start_seconds` INT(5) DEFAULT NULL,
  `intro_end_seconds` INT(5) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_episodes_content` (`content_id`),
  CONSTRAINT `fk_episodes_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- WATCHLIST TABLE
-- =====================
CREATE TABLE `watchlist` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `profile_id` INT(11) NOT NULL,
  `content_id` INT(11) NOT NULL,
  `added_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_watchlist` (`profile_id`, `content_id`),
  KEY `fk_wl_content` (`content_id`),
  CONSTRAINT `fk_wl_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wl_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- WATCH_HISTORY TABLE
-- =====================
CREATE TABLE `watch_history` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `profile_id` INT(11) NOT NULL,
  `content_id` INT(11) NOT NULL,
  `episode_id` INT(11) DEFAULT NULL,
  `watched_position_seconds` INT(11) NOT NULL DEFAULT 0,
  `total_duration_seconds` INT(11) NOT NULL DEFAULT 0,
  `is_finished` TINYINT(1) NOT NULL DEFAULT 0,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_watch` (`profile_id`, `content_id`, `episode_id`),
  KEY `fk_wh_content` (`content_id`),
  KEY `fk_wh_episode` (`episode_id`),
  CONSTRAINT `fk_wh_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wh_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wh_episode` FOREIGN KEY (`episode_id`) REFERENCES `episodes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- COMMENTS TABLE
-- =====================
CREATE TABLE `comments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `profile_id` INT(11) NOT NULL,
  `content_id` INT(11) NOT NULL,
  `comment_text` TEXT NOT NULL,
  `is_like` TINYINT(1) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_comments_content` (`content_id`),
  CONSTRAINT `fk_comments_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- LIKES TABLE (Beğen / Beğenme)
-- =====================
CREATE TABLE `likes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `profile_id` INT(11) NOT NULL,
  `content_id` INT(11) NOT NULL,
  `is_like` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`profile_id`, `content_id`),
  CONSTRAINT `fk_likes_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_likes_content` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================
-- NOTIFICATIONS TABLE
-- =====================
CREATE TABLE `notifications` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `expected_date` DATE DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DEFAULT DATA INSERTS
-- ============================================

-- Default Admin User (password: admin123)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `subscription_status`) VALUES
('Admin', 'admin@movify.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');

-- Default Profile for Admin
INSERT INTO `profiles` (`user_id`, `profile_name`, `avatar_image`) VALUES
(1, 'Admin', 'default-avatar.png');

-- Default Subscription for Admin
INSERT INTO `subscriptions` (`user_id`, `plan_name`, `price`, `status`, `payment_method`) VALUES
(1, 'Premium', 0.00, 'active', 'Sistem');

-- Default Categories
INSERT INTO `categories` (`name`, `slug`) VALUES
('Tüm Kategoriler', 'tum-kategoriler'),
('Aksiyon', 'aksiyon'),
('Anime', 'anime'),
('Astroloji', 'astroloji'),
('Bağımsız', 'bagimsiz'),
('Belgeseller', 'belgeseller'),
('Bilim Kurgu', 'bilim-kurgu'),
('Çocuk ve Aile', 'cocuk-ve-aile'),
('Drama', 'drama'),
('Fantastik', 'fantastik'),
('Gerilim', 'gerilim'),
('Kısa Filmler', 'kisa-filmler'),
('Klasikler', 'klasikler'),
('Hollywood', 'hollywood'),
('Korku', 'korku'),
('Komedi', 'komedi'),
('Ödüllü Yapımlar', 'odullu-yapimlar'),
('Romantizm', 'romantizm'),
('Spor', 'spor'),
('Stand-Up', 'stand-up'),
('Yerli Yapımlar', 'yerli-yapimlar'),
('Suç', 'suc');

COMMIT;
