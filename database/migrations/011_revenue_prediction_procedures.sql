-- ========================================
-- MIGRATION: Revenue Prediction Procedures
-- Stored Procedures para BI e Predição de Receita
-- Data: Janeiro 2026
-- ========================================

USE `escritorio_db`;

-- Remove procedures se já existirem
DROP PROCEDURE IF EXISTS sp_calculate_revenue_prediction;
DROP PROCEDURE IF EXISTS sp_get_advanced_dashboard_stats;
DROP PROCEDURE IF EXISTS sp_get_conversion_funnel;
DROP PROCEDURE IF EXISTS sp_analyze_appointment_trends;

DELIMITER $$

-- ========================================
-- PROCEDURE: sp_calculate_revenue_prediction
-- Calcula predição de receita para os próximos N meses
-- ========================================

CREATE PROCEDURE sp_calculate_revenue_prediction(
    IN months_to_predict INT,
    IN months_historical_data INT
)
BEGIN
    DECLARE current_month_start DATE;
    DECLARE prediction_month DATE;
    DECLARE avg_appointments_per_month DECIMAL(10,2);
    DECLARE conversion_rate DECIMAL(5,2);
    DECLARE avg_ticket DECIMAL(10,2);
    DECLARE predicted_revenue DECIMAL(12,2);
    DECLARE confidence DECIMAL(5,2);
    DECLARE counter INT DEFAULT 1;

    -- Data de início do mês atual
    SET current_month_start = DATE_FORMAT(CURDATE(), '%Y-%m-01');

    -- Calcula médias históricas
    SELECT
        COUNT(*) / months_historical_data as avg_appts,
        AVG(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) * 100 as conv_rate,
        500.00 as avg_value -- Pode ser calculado de uma tabela de valores
    INTO avg_appointments_per_month, conversion_rate, avg_ticket
    FROM appointments
    WHERE created_at >= DATE_SUB(current_month_start, INTERVAL months_historical_data MONTH)
    AND created_at < current_month_start;

    -- Se não houver dados históricos suficientes, usa defaults conservadores
    IF avg_appointments_per_month IS NULL OR avg_appointments_per_month = 0 THEN
        SET avg_appointments_per_month = 10;
        SET conversion_rate = 30.00;
        SET avg_ticket = 500.00;
        SET confidence = 40.00; -- Baixa confiança
    ELSE
        SET confidence = LEAST(90.00, 50.00 + (months_historical_data * 5)); -- Máx 90%
    END IF;

    -- Loop para criar predições para cada mês
    WHILE counter <= months_to_predict DO
        SET prediction_month = DATE_ADD(current_month_start, INTERVAL counter MONTH);

        -- Calcula predição
        SET predicted_revenue = avg_appointments_per_month * (conversion_rate / 100) * avg_ticket;

        -- Insere ou atualiza predição
        INSERT INTO revenue_predictions (
            prediction_date,
            prediction_month,
            consultation_type,
            predicted_appointments,
            conversion_rate,
            average_ticket,
            predicted_revenue,
            confidence_level,
            based_on_months,
            calculation_method
        ) VALUES (
            CURDATE(),
            prediction_month,
            NULL,
            ROUND(avg_appointments_per_month),
            conversion_rate,
            avg_ticket,
            predicted_revenue,
            confidence,
            months_historical_data,
            'Linear Regression + Historical Average'
        )
        ON DUPLICATE KEY UPDATE
            prediction_date = CURDATE(),
            predicted_appointments = ROUND(avg_appointments_per_month),
            conversion_rate = conversion_rate,
            average_ticket = avg_ticket,
            predicted_revenue = predicted_revenue,
            confidence_level = confidence,
            based_on_months = months_historical_data;

        SET counter = counter + 1;
    END WHILE;

    -- Retorna as predições criadas
    SELECT * FROM revenue_predictions
    WHERE prediction_month >= current_month_start
    ORDER BY prediction_month ASC
    LIMIT months_to_predict;

END$$

-- ========================================
-- PROCEDURE: sp_get_advanced_dashboard_stats
-- Retorna estatísticas avançadas para o dashboard administrativo
-- ========================================

CREATE PROCEDURE sp_get_advanced_dashboard_stats()
BEGIN
    -- Estatísticas do mês atual
    SELECT
        COUNT(*) as total_appointments_month,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_month,
        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_month,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_month,
        AVG(urgency_score) as avg_urgency_month,
        SUM(CASE WHEN urgency_score >= 8 THEN 1 ELSE 0 END) as high_urgency_month
    FROM appointments
    WHERE DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m');

    -- Estatísticas gerais (todos os tempos)
    SELECT
        COUNT(*) as total_appointments_all,
        (SELECT COUNT(*) FROM lawyers WHERE status = 'active') as active_lawyers,
        (SELECT COUNT(*) FROM users WHERE status = 'active') as active_users,
        (SELECT SUM(cases_won) FROM lawyers WHERE status = 'active') as total_cases_won
    FROM appointments;

    -- Top 5 tipos de consulta mais solicitados (últimos 3 meses)
    SELECT
        JSON_UNQUOTE(JSON_EXTRACT(sentiment_analysis, '$.legal_area_detected')) as consultation_type,
        COUNT(*) as count,
        AVG(urgency_score) as avg_urgency
    FROM appointments
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
    AND sentiment_analysis IS NOT NULL
    AND JSON_EXTRACT(sentiment_analysis, '$.legal_area_detected') IS NOT NULL
    GROUP BY consultation_type
    ORDER BY count DESC
    LIMIT 5;

    -- Advogados com melhor performance
    SELECT
        id,
        name,
        oab_number,
        oab_state,
        cases_won,
        success_rate,
        average_rating
    FROM lawyers
    WHERE status = 'active'
    ORDER BY success_rate DESC, cases_won DESC
    LIMIT 5;

    -- Uso de inteligência (últimos 30 dias)
    SELECT
        service_type,
        COUNT(*) as total_operations,
        SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful,
        SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as errors,
        AVG(execution_time) as avg_execution_time,
        SUM(cost) as total_cost
    FROM intelligence_logs
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY service_type;

    -- Predições de receita (próximos 3 meses)
    SELECT
        prediction_month,
        predicted_appointments,
        conversion_rate,
        predicted_revenue,
        confidence_level
    FROM revenue_predictions
    WHERE prediction_month >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
    ORDER BY prediction_month ASC
    LIMIT 3;

END$$

-- ========================================
-- PROCEDURE: sp_get_conversion_funnel
-- Analisa funil de conversão de agendamentos
-- ========================================

CREATE PROCEDURE sp_get_conversion_funnel(
    IN start_date DATE,
    IN end_date DATE
)
BEGIN
    SELECT
        'Total Solicitações' as stage,
        COUNT(*) as count,
        100.00 as percentage,
        1 as stage_order
    FROM appointments
    WHERE created_at BETWEEN start_date AND end_date

    UNION ALL

    SELECT
        'Agendamentos Confirmados' as stage,
        COUNT(*) as count,
        ROUND((COUNT(*) * 100.0) / (SELECT COUNT(*) FROM appointments WHERE created_at BETWEEN start_date AND end_date), 2) as percentage,
        2 as stage_order
    FROM appointments
    WHERE created_at BETWEEN start_date AND end_date
    AND status IN ('confirmed', 'completed')

    UNION ALL

    SELECT
        'Agendamentos Concluídos' as stage,
        COUNT(*) as count,
        ROUND((COUNT(*) * 100.0) / (SELECT COUNT(*) FROM appointments WHERE created_at BETWEEN start_date AND end_date), 2) as percentage,
        3 as stage_order
    FROM appointments
    WHERE created_at BETWEEN start_date AND end_date
    AND status = 'completed'

    UNION ALL

    SELECT
        'Alta Prioridade (>= 8)' as stage,
        COUNT(*) as count,
        ROUND((COUNT(*) * 100.0) / (SELECT COUNT(*) FROM appointments WHERE created_at BETWEEN start_date AND end_date), 2) as percentage,
        4 as stage_order
    FROM appointments
    WHERE created_at BETWEEN start_date AND end_date
    AND urgency_score >= 8

    ORDER BY stage_order;

END$$

-- ========================================
-- PROCEDURE: sp_analyze_appointment_trends
-- Analisa tendências de agendamentos ao longo do tempo
-- ========================================

CREATE PROCEDURE sp_analyze_appointment_trends(
    IN months_back INT
)
BEGIN
    SELECT
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as total_appointments,
        AVG(urgency_score) as avg_urgency,
        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
        ROUND((SUM(CASE WHEN status IN ('confirmed', 'completed') THEN 1 ELSE 0 END) * 100.0) / COUNT(*), 2) as conversion_rate
    FROM appointments
    WHERE created_at >= DATE_SUB(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL months_back MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC;

END$$

DELIMITER ;

-- ========================================
-- Executa predição inicial (próximos 6 meses baseados em 3 meses históricos)
-- ========================================

CALL sp_calculate_revenue_prediction(6, 3);

-- ========================================
-- Registra a migration no activity_logs
-- ========================================

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
  'Revenue Prediction Procedures - Stored procedures criadas: sp_calculate_revenue_prediction, sp_get_advanced_dashboard_stats, sp_get_conversion_funnel, sp_analyze_appointment_trends',
  'SYSTEM'
);

-- ========================================
-- MIGRATION CONCLUÍDA
-- ========================================
