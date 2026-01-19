-- =====================================================
-- Migration: 009 - Criar tabela password_resets
-- Descrição: Tabela para tokens de recuperação de senha
-- Data: 2026-01-19
-- =====================================================

CREATE TABLE IF NOT EXISTS `password_resets` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP NULL,
    `used_at` TIMESTAMP NULL,
    INDEX `idx_email` (`email`),
    INDEX `idx_token` (`token`),
    INDEX `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comentários nas colunas
ALTER TABLE `password_resets`
    MODIFY `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'ID único do token',
    MODIFY `email` VARCHAR(255) NOT NULL COMMENT 'Email do usuário que solicitou',
    MODIFY `token` VARCHAR(255) NOT NULL COMMENT 'Token de recuperação (hash)',
    MODIFY `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de criação do token',
    MODIFY `expires_at` TIMESTAMP NULL COMMENT 'Data de expiração (1 hora após criação)',
    MODIFY `used_at` TIMESTAMP NULL COMMENT 'Data em que o token foi usado (null = não usado)';

-- =====================================================
-- Procedure para limpar tokens expirados
-- =====================================================

DELIMITER $$

CREATE PROCEDURE IF NOT EXISTS sp_clean_expired_password_resets()
BEGIN
    DELETE FROM password_resets
    WHERE expires_at < NOW()
    OR used_at IS NOT NULL AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY);
END$$

DELIMITER ;
