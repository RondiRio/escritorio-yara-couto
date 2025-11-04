-- Migration: 002 - Create categories table
-- Descrição: Categorias para posts do blog
-- Data: 2025-10-31

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `parent_id` INT(11) UNSIGNED DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `parent_id` (`parent_id`),
  FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categorias padrão
INSERT INTO `categories` (`name`, `slug`, `description`) VALUES
('Direito Previdenciário', 'direito-previdenciario', 'Artigos sobre aposentadorias, benefícios e direitos previdenciários'),
('Aposentadorias', 'aposentadorias', 'Informações sobre diferentes tipos de aposentadoria'),
('INSS', 'inss', 'Notícias e orientações sobre o INSS'),
('BPC/LOAS', 'bpc-loas', 'Benefício de Prestação Continuada'),
('Direito do Trabalho', 'direito-trabalho', 'Artigos sobre direitos trabalhistas'),
('Legislação', 'legislacao', 'Mudanças e atualizações na legislação'),
('Notícias', 'noticias', 'Notícias gerais sobre advocacia');