-- Migration: 007 - Create settings table
-- Descrição: Configurações do sistema
-- Data: 2025-10-31

CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(255) NOT NULL,
  `value` TEXT DEFAULT NULL,
  `type` ENUM('string', 'text', 'boolean', 'integer', 'float', 'json', 'array') DEFAULT 'string',
  `group` VARCHAR(100) DEFAULT 'general' COMMENT 'Agrupamento de configurações',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`),
  KEY `group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configurações iniciais
INSERT INTO `settings` (`key`, `value`, `type`, `group`) VALUES
('site_name', 'Sistema de Gestão de Escritórios', 'string', 'general'),
('site_description', 'Sistema de Gestão para Escritórios de Advocacia e Contabilidade', 'string', 'general'),
('site_email', 'contato@seuescritorio.com.br', 'string', 'general'),
('site_phone', '', 'string', 'general'),
('site_whatsapp', '', 'string', 'general'),
('site_address', '', 'text', 'general'),
('oab_number', '', 'string', 'general'),
('oab_state', 'RJ', 'string', 'general'),
('meta_title', 'Sistema de Gestão de Escritórios - Advocacia e Contabilidade', 'string', 'seo'),
('meta_description', 'Escritório especializado em Direito Previdenciário. Aposentadorias, BPC/LOAS, Auxílio-Doença e mais.', 'string', 'seo'),
('meta_keywords', 'advocacia previdenciária, aposentadoria, inss, benefícios', 'string', 'seo'),
('facebook_url', '', 'string', 'social'),
('instagram_url', '', 'string', 'social'),
('linkedin_url', '', 'string', 'social'),
('youtube_url', '', 'string', 'social');