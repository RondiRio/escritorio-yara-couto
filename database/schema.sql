-- ========================================
-- SCHEMA DO BANCO DE DADOS
-- Sistema de Gestão de Escritórios
-- Advocacia e Contabilidade
-- ========================================

-- Configurações iniciais
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "-03:00";
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Cria banco de dados (se não existir)
CREATE DATABASE IF NOT EXISTS `escritorio_db`
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `escritorio_db`;

-- ========================================
-- TABELA: users
-- Usuários administradores do sistema
-- ========================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'editor', 'author') DEFAULT 'admin',
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `last_login` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `status` (`status`),
  KEY `role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABELA: categories
-- Categorias de posts do blog
-- ========================================
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

-- ========================================
-- TABELA: posts
-- Posts/Artigos do blog
-- ========================================
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
  FULLTEXT KEY `search` (`title`, `content`, `excerpt`),
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABELA: tags
-- Tags para posts
-- ========================================
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

-- ========================================
-- TABELA: post_tags
-- Relacionamento Many-to-Many entre posts e tags
-- ========================================
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

-- ========================================
-- TABELA: lawyers
-- Advogados do escritório
-- Referência: OAB - https://cna.oab.org.br/
-- ========================================
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

-- ========================================
-- TABELA: appointments
-- Agendamentos de consultas
-- ========================================
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
  KEY `phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABELA: settings
-- Configurações do sistema
-- ========================================
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

-- ========================================
-- TABELA: activity_logs
-- Logs de atividades do sistema (auditoria)
-- ========================================
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
  KEY `created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- INSERÇÃO DE DADOS INICIAIS
-- ========================================

-- Usuário administrador padrão
-- Email: admin@seuescritorio.com.br
-- Senha: admin123 (DEVE SER ALTERADA após primeiro login!)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`) VALUES
('Administrador', 'admin@seuescritorio.com.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');

-- Categorias padrão para o blog
INSERT INTO `categories` (`name`, `slug`, `description`) VALUES
('Direito Previdenciário', 'direito-previdenciario', 'Artigos sobre aposentadorias, benefícios e direitos previdenciários'),
('Aposentadorias', 'aposentadorias', 'Informações sobre diferentes tipos de aposentadoria'),
('INSS', 'inss', 'Notícias e orientações sobre o INSS'),
('BPC/LOAS', 'bpc-loas', 'Benefício de Prestação Continuada'),
('Direito do Trabalho', 'direito-trabalho', 'Artigos sobre direitos trabalhistas'),
('Legislação', 'legislacao', 'Mudanças e atualizações na legislação'),
('Notícias', 'noticias', 'Notícias gerais sobre advocacia');

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

-- Configurações iniciais do site
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

-- ========================================
-- VIEWS (CONSULTAS ÚTEIS)
-- ========================================

-- View: Posts publicados com informações completas
CREATE OR REPLACE VIEW `v_posts_published` AS
SELECT 
    p.id,
    p.title,
    p.slug,
    p.excerpt,
    p.featured_image,
    p.views,
    p.published_at,
    p.created_at,
    c.name AS category_name,
    c.slug AS category_slug,
    u.name AS author_name
FROM posts p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN users u ON p.author_id = u.id
WHERE p.status = 'published'
AND (p.published_at IS NULL OR p.published_at <= NOW())
ORDER BY p.published_at DESC;

-- View: Estatísticas de agendamentos
CREATE OR REPLACE VIEW `v_appointments_stats` AS
SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled,
    SUM(CASE WHEN DATE(preferred_date) = CURDATE() THEN 1 ELSE 0 END) AS today
FROM appointments;

-- View: Advogados ativos com estatísticas
CREATE OR REPLACE VIEW `v_lawyers_active` AS
SELECT 
    id,
    name,
    CONCAT(oab_number, '/', oab_state) AS oab_full,
    photo,
    specialties,
    cases_won,
    display_order
FROM lawyers
WHERE status = 'active'
ORDER BY display_order ASC;

-- ========================================
-- ÍNDICES ADICIONAIS PARA PERFORMANCE
-- ========================================

-- Índice composto para busca de posts
CREATE INDEX idx_posts_status_published ON posts(status, published_at DESC);

-- Índice para agendamentos por data e status
CREATE INDEX idx_appointments_date_status ON appointments(preferred_date, status);

-- Índice para logs por data
CREATE INDEX idx_activity_logs_date ON activity_logs(created_at DESC);

-- ========================================
-- TRIGGERS
-- ========================================

-- Trigger: Atualiza contador de posts após inserção
DELIMITER $$
CREATE TRIGGER after_post_insert 
AFTER INSERT ON posts
FOR EACH ROW
BEGIN
    -- Log automático pode ser adicionado aqui
    NULL;
END$$
DELIMITER ;

-- ========================================
-- PROCEDURES ÚTEIS
-- ========================================

-- Procedure: Limpar logs antigos (mais de 90 dias)
DELIMITER $$
CREATE PROCEDURE sp_clean_old_logs(IN days INT)
BEGIN
    DELETE FROM activity_logs 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL days DAY);
END$$
DELIMITER ;

-- Procedure: Estatísticas gerais do sistema
DELIMITER $$
CREATE PROCEDURE sp_get_dashboard_stats()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM posts WHERE status = 'published') AS total_posts,
        (SELECT COUNT(*) FROM lawyers WHERE status = 'active') AS total_lawyers,
        (SELECT COUNT(*) FROM appointments WHERE status = 'pending') AS pending_appointments,
        (SELECT COUNT(*) FROM users WHERE status = 'active') AS total_users,
        (SELECT SUM(cases_won) FROM lawyers WHERE status = 'active') AS total_cases_won;
END$$
DELIMITER ;

-- ========================================
-- COMENTÁRIOS FINAIS
-- ========================================

-- Este schema foi criado seguindo:
-- 1. Lei 8.906/94 (Estatuto da OAB) - https://www.planalto.gov.br/ccivil_03/leis/l8906.htm
-- 2. Provimento 205/2021 (Publicidade OAB) - https://www.oab.org.br/leisnormas/legislacao/provimentos/205-2021
-- 3. Lei 13.709/18 (LGPD) - https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709.htm
-- 4. Padrões de validação OAB - https://cna.oab.org.br/

-- Versão: 1.0
-- Data: 2025-10-31
-- Charset: UTF-8 (utf8mb4)
-- Engine: InnoDB
-- Collation: utf8mb4_unicode_ci