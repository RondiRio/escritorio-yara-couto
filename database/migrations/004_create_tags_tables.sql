-- Migration: 004 - Create tags and post_tags tables
-- Descrição: Tags e relacionamento Many-to-Many com posts
-- Data: 2025-10-31

-- Tabela de tags
CREATE TABLE IF NOT EXISTS `tags` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de relacionamento
CREATE TABLE IF NOT EXISTS `post_tags` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` INT(11) UNSIGNED NOT NULL,
  `tag_id` INT(11) UNSIGNED NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_tag` (`post_id`, `tag_id`),
  KEY `post_id` (`post_id`),
  KEY `tag_id` (`tag_id`),
  FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tags padrão
INSERT INTO `tags` (`name`, `slug`) VALUES
('Aposentadoria', 'aposentadoria'),
('INSS', 'inss'),
('Benefícios', 'beneficios'),
('Revisão', 'revisao'),
('Perícia Médica', 'pericia-medica'),
('Auxílio-Doença', 'auxilio-doenca'),
('Pensão', 'pensao'),
('LOAS', 'loas'),
('Reforma da Previdência', 'reforma-previdencia'),
('Direitos', 'direitos');