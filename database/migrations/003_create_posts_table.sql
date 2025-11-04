-- Migration: 003 - Create posts table
-- Descrição: Posts/Artigos do blog
-- Data: 2025-10-31

CREATE TABLE IF NOT EXISTS `posts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `content` LONGTEXT NOT NULL,
  `excerpt` TEXT DEFAULT NULL,
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `category_id` INT(11) UNSIGNED DEFAULT NULL,
  `author_id` INT(11) UNSIGNED NOT NULL,
  `status` ENUM('draft', 'published') DEFAULT 'draft',
  `views` INT(11) UNSIGNED DEFAULT 0,
  `published_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  KEY `author_id` (`author_id`),
  KEY `status` (`status`),
  KEY `published_at` (`published_at`),
  KEY `idx_posts_status_published` (`status`, `published_at` DESC),
  FULLTEXT KEY `search` (`title`, `content`, `excerpt`),
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;