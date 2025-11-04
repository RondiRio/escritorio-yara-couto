-- Migration: 008 - Create activity_logs table
-- Descrição: Logs de atividades do sistema (auditoria)
-- Data: 2025-10-31

CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL COMMENT 'Tipo de ação (create, update, delete, login, etc)',
  `description` TEXT NOT NULL COMMENT 'Descrição da ação',
  `entity_type` VARCHAR(100) DEFAULT NULL COMMENT 'Tipo de entidade afetada',
  `entity_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'ID da entidade afetada',
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `entity` (`entity_type`, `entity_id`),
  KEY `idx_activity_logs_date` (`created_at` DESC),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;