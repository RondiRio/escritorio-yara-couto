-- Migration: 006 - Create appointments table
-- Descrição: Agendamentos de consultas
-- Data: 2025-10-31

CREATE TABLE IF NOT EXISTS `appointments` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `whatsapp` VARCHAR(20) DEFAULT NULL,
  `preferred_date` DATE NOT NULL,
  `preferred_time` TIME NOT NULL,
  `consultation_type` VARCHAR(255) NOT NULL COMMENT 'Tipo de consulta',
  `message` TEXT DEFAULT NULL COMMENT 'Mensagem adicional',
  `status` ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
  `admin_notes` TEXT DEFAULT NULL COMMENT 'Notas administrativas',
  `confirmed_at` DATETIME DEFAULT NULL,
  `completed_at` DATETIME DEFAULT NULL,
  `cancelled_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `preferred_date` (`preferred_date`),
  KEY `email` (`email`),
  KEY `phone` (`phone`),
  KEY `idx_appointments_date_status` (`preferred_date`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;