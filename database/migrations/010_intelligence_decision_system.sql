-- ========================================
-- MIGRATION: Intelligence Decision System
-- Adiciona campos necessários para IDS
-- Data: Janeiro 2026
-- ========================================

USE `escritorio_db`;

-- ========================================
-- 1. ALTERAÇÕES NA TABELA appointments
-- Adiciona campos para Background Check, NLP e Priorização
-- ========================================

ALTER TABLE `appointments`
ADD COLUMN `cpf` VARCHAR(14) DEFAULT NULL COMMENT 'CPF do cliente para background check' AFTER `whatsapp`,
ADD COLUMN `background_check` JSON DEFAULT NULL COMMENT 'Resultado do background check (processos, situação cadastral)' AFTER `admin_notes`,
ADD COLUMN `sentiment_analysis` JSON DEFAULT NULL COMMENT 'Análise de sentimento da mensagem (OpenAI)' AFTER `background_check`,
ADD COLUMN `priority_level` TINYINT(2) DEFAULT 5 COMMENT 'Nível de prioridade (1-10)' AFTER `sentiment_analysis`,
ADD COLUMN `urgency_score` DECIMAL(3,2) DEFAULT 5.00 COMMENT 'Score de urgência calculado por IA' AFTER `priority_level`,
ADD COLUMN `recommended_lawyer_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'ID do advogado recomendado pelo sistema' AFTER `urgency_score`,
ADD COLUMN `recommendation_score` DECIMAL(5,2) DEFAULT NULL COMMENT 'Score da recomendação (0-100)' AFTER `recommended_lawyer_id`,
ADD INDEX `idx_priority` (`priority_level`),
ADD INDEX `idx_urgency` (`urgency_score`),
ADD INDEX `idx_recommended_lawyer` (`recommended_lawyer_id`),
ADD FOREIGN KEY (`recommended_lawyer_id`) REFERENCES `lawyers` (`id`) ON DELETE SET NULL;

-- ========================================
-- 2. ALTERAÇÕES NA TABELA lawyers
-- Adiciona campos para analytics e especialização detalhada
-- ========================================

ALTER TABLE `lawyers`
ADD COLUMN `cases_total` INT(11) UNSIGNED DEFAULT 0 COMMENT 'Total de casos atendidos' AFTER `cases_won`,
ADD COLUMN `success_rate` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Taxa de sucesso (%)' AFTER `cases_total`,
ADD COLUMN `specialties_json` JSON DEFAULT NULL COMMENT 'Especialidades estruturadas com detalhes' AFTER `specialties`,
ADD COLUMN `average_rating` DECIMAL(3,2) DEFAULT 0.00 COMMENT 'Avaliação média (0-5)' AFTER `success_rate`,
ADD COLUMN `total_ratings` INT(11) UNSIGNED DEFAULT 0 COMMENT 'Total de avaliações' AFTER `average_rating`,
ADD INDEX `idx_success_rate` (`success_rate`),
ADD INDEX `idx_rating` (`average_rating`);

-- ========================================
-- 3. NOVA TABELA: intelligence_logs
-- Registra todas as operações de inteligência para auditoria
-- ========================================

CREATE TABLE IF NOT EXISTS `intelligence_logs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_type` ENUM('background_check', 'lawyer_recommendation', 'sentiment_analysis', 'lgpd_compliance', 'revenue_prediction') NOT NULL COMMENT 'Tipo de serviço de inteligência',
  `entity_type` VARCHAR(50) NOT NULL COMMENT 'Tipo de entidade (appointment, lawyer, etc)',
  `entity_id` INT(11) UNSIGNED NOT NULL COMMENT 'ID da entidade',
  `input_data` JSON DEFAULT NULL COMMENT 'Dados de entrada',
  `output_data` JSON DEFAULT NULL COMMENT 'Dados de saída/resultado',
  `execution_time` DECIMAL(10,3) DEFAULT NULL COMMENT 'Tempo de execução em segundos',
  `status` ENUM('success', 'error', 'partial') DEFAULT 'success',
  `error_message` TEXT DEFAULT NULL,
  `api_used` VARCHAR(100) DEFAULT NULL COMMENT 'API externa utilizada (se houver)',
  `cost` DECIMAL(10,4) DEFAULT 0.0000 COMMENT 'Custo da operação (créditos de API)',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_service` (`service_type`),
  KEY `idx_entity` (`entity_type`, `entity_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 4. NOVA TABELA: lawyer_performance_history
-- Histórico de performance dos advogados por tipo de caso
-- ========================================

CREATE TABLE IF NOT EXISTS `lawyer_performance_history` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lawyer_id` INT(11) UNSIGNED NOT NULL,
  `consultation_type` VARCHAR(255) NOT NULL COMMENT 'Tipo de consulta/caso',
  `cases_handled` INT(11) UNSIGNED DEFAULT 0 COMMENT 'Casos tratados deste tipo',
  `cases_won` INT(11) UNSIGNED DEFAULT 0 COMMENT 'Casos vencidos deste tipo',
  `success_rate` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Taxa de sucesso específica (%)',
  `avg_duration_days` INT(11) DEFAULT NULL COMMENT 'Duração média em dias',
  `avg_revenue` DECIMAL(10,2) DEFAULT NULL COMMENT 'Receita média por caso',
  `last_case_date` DATE DEFAULT NULL COMMENT 'Data do último caso',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_lawyer_type` (`lawyer_id`, `consultation_type`),
  KEY `idx_consultation_type` (`consultation_type`),
  KEY `idx_success_rate` (`success_rate`),
  FOREIGN KEY (`lawyer_id`) REFERENCES `lawyers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 5. NOVA TABELA: lgpd_anonymization_log
-- Log de anonimizações realizadas para compliance LGPD
-- ========================================

CREATE TABLE IF NOT EXISTS `lgpd_anonymization_log` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `table_name` VARCHAR(100) NOT NULL COMMENT 'Tabela onde ocorreu anonimização',
  `record_id` INT(11) UNSIGNED NOT NULL COMMENT 'ID do registro anonimizado',
  `fields_anonymized` JSON NOT NULL COMMENT 'Campos que foram anonimizados',
  `reason` VARCHAR(255) DEFAULT 'Compliance LGPD - Retenção expirada' COMMENT 'Motivo da anonimização',
  `original_created_at` DATETIME DEFAULT NULL COMMENT 'Data de criação original do registro',
  `retention_months` INT(11) DEFAULT 24 COMMENT 'Meses de retenção aplicados',
  `anonymized_by` VARCHAR(100) DEFAULT 'System - LGPD Auto Purge' COMMENT 'Quem/o que realizou',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_table_record` (`table_name`, `record_id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 6. NOVA TABELA: revenue_predictions
-- Armazena predições de receita calculadas pelo sistema
-- ========================================

CREATE TABLE IF NOT EXISTS `revenue_predictions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `prediction_date` DATE NOT NULL COMMENT 'Data para qual a predição foi feita',
  `prediction_month` DATE NOT NULL COMMENT 'Mês sendo previsto (primeiro dia do mês)',
  `consultation_type` VARCHAR(255) DEFAULT NULL COMMENT 'Tipo específico ou NULL para geral',
  `predicted_appointments` INT(11) DEFAULT 0 COMMENT 'Agendamentos previstos',
  `conversion_rate` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Taxa de conversão esperada (%)',
  `average_ticket` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Ticket médio',
  `predicted_revenue` DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Receita prevista',
  `confidence_level` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Nível de confiança da predição (%)',
  `based_on_months` INT(11) DEFAULT 3 COMMENT 'Baseado em quantos meses históricos',
  `calculation_method` VARCHAR(100) DEFAULT 'Linear Regression' COMMENT 'Método de cálculo usado',
  `actual_revenue` DECIMAL(12,2) DEFAULT NULL COMMENT 'Receita real (preenchido depois)',
  `accuracy` DECIMAL(5,2) DEFAULT NULL COMMENT 'Acurácia da predição (%) quando comparado com real',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_prediction` (`prediction_month`, `consultation_type`),
  KEY `idx_prediction_month` (`prediction_month`),
  KEY `idx_confidence` (`confidence_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 7. DADOS INICIAIS
-- Popula specialties_json para advogados existentes
-- ========================================

UPDATE `lawyers`
SET `specialties_json` = JSON_ARRAY(
  JSON_OBJECT(
    'name', SUBSTRING_INDEX(specialties, ',', 1),
    'experience_years', 0,
    'cases_handled', 0
  )
)
WHERE `specialties` IS NOT NULL AND `specialties_json` IS NULL;

-- ========================================
-- MIGRATION CONCLUÍDA
-- ========================================

-- Registra a migration no activity_logs
INSERT INTO `activity_logs` (
  `user_id`,
  `action`,
  `entity_type`,
  `description`,
  `ip_address`
) VALUES (
  NULL,
  'database_migration',
  'system',
  'Intelligence Decision System (IDS) - Migration aplicada com sucesso. Adicionados campos para Background Check, NLP, Recomendação de Advogados, LGPD Compliance e Revenue Prediction.',
  'SYSTEM'
);
