-- Migration: 005 - Create lawyers table
-- Descrição: Advogados do escritório
-- Referência: OAB - https://cna.oab.org.br/
-- Data: 2025-10-31

CREATE TABLE IF NOT EXISTS `lawyers` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `oab_number` VARCHAR(50) NOT NULL COMMENT 'Número da OAB',
  `oab_state` CHAR(2) NOT NULL COMMENT 'UF da seccional OAB',
  `photo` VARCHAR(255) DEFAULT NULL,
  `bio` TEXT NOT NULL COMMENT 'Mini biografia',
  `specialties` TEXT DEFAULT NULL COMMENT 'Especialidades (separadas por vírgula)',
  `email` VARCHAR(255) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `whatsapp` VARCHAR(20) DEFAULT NULL,
  `cases_won` INT(11) UNSIGNED DEFAULT 0 COMMENT 'Número de ações vencidas',
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `display_order` INT(11) DEFAULT 999 COMMENT 'Ordem de exibição',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oab` (`oab_number`, `oab_state`),
  KEY `status` (`status`),
  KEY `display_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;