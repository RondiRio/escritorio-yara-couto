<?php

namespace Services\Intelligence;

use Core\Database;

/**
 * LGPDComplianceService - Camada 4 do IDS
 *
 * Gerencia compliance com LGPD (Lei Geral de Proteção de Dados)
 * - Anonimização automática após período de retenção
 * - Data masking de campos sensíveis
 * - Log de todas as operações
 * - Direitos do titular: esquecimento, portabilidade, acesso
 *
 * Período padrão de retenção: 24 meses após última interação
 *
 * @package Services\Intelligence
 */
class LGPDComplianceService
{
    private $db;
    private $retentionMonths = 24; // Prazo padrão de retenção

    // Campos sensíveis por tabela
    private $sensitiveFields = [
        'appointments' => ['name', 'email', 'phone', 'whatsapp', 'cpf', 'message', 'admin_notes'],
        'users' => ['email', 'last_login'],
        'lawyers' => ['email', 'phone', 'whatsapp']
    ];

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();

        // Permite configurar período de retenção via .env
        $customRetention = getenv('LGPD_RETENTION_MONTHS');
        if ($customRetention && is_numeric($customRetention)) {
            $this->retentionMonths = (int)$customRetention;
        }
    }

    /**
     * Executa purga automática de dados expirados
     * Deve ser executado via CRON diariamente
     *
     * @return array Resultado da operação
     */
    public function autoAnonymize()
    {
        $startTime = microtime(true);

        try {
            $totalAnonymized = 0;

            // 1. Anonimiza agendamentos antigos (após período de retenção)
            $appointmentsAnonymized = $this->anonymizeOldAppointments();
            $totalAnonymized += $appointmentsAnonymized;

            $executionTime = microtime(true) - $startTime;

            // Log de sucesso
            $this->logIntelligence('lgpd_compliance', 'system', 0, [
                'operation' => 'auto_anonymize',
                'retention_months' => $this->retentionMonths
            ], [
                'appointments_anonymized' => $appointmentsAnonymized,
                'total_records' => $totalAnonymized
            ], $executionTime, 'success');

            return [
                'success' => true,
                'data' => [
                    'appointments_anonymized' => $appointmentsAnonymized,
                    'total_records' => $totalAnonymized,
                    'execution_time' => round($executionTime, 2)
                ],
                'message' => "Anonimização concluída: {$totalAnonymized} registros processados"
            ];

        } catch (\Exception $e) {
            $executionTime = microtime(true) - $startTime;

            $this->logIntelligence('lgpd_compliance', 'system', 0, [
                'operation' => 'auto_anonymize'
            ], null, $executionTime, 'error', $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erro ao executar anonimização automática'
            ];
        }
    }

    /**
     * Anonimiza agendamentos que excederam período de retenção
     */
    private function anonymizeOldAppointments()
    {
        // Calcula data limite (ex: 24 meses atrás)
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$this->retentionMonths} months"));

        // Busca registros elegíveis para anonimização
        $sql = "SELECT id, name, email, phone, cpf, created_at
                FROM appointments
                WHERE created_at < :cutoff_date
                AND (name IS NOT NULL OR email IS NOT NULL OR phone IS NOT NULL OR cpf IS NOT NULL)
                AND name NOT LIKE '[ANONIMIZADO%'";

        $records = $this->db->select($sql, ['cutoff_date' => $cutoffDate]);

        if (empty($records)) {
            return 0;
        }

        $anonymizedCount = 0;

        foreach ($records as $record) {
            // Campos que serão anonimizados
            $fieldsAnonymized = [];

            if (!empty($record['name'])) $fieldsAnonymized[] = 'name';
            if (!empty($record['email'])) $fieldsAnonymized[] = 'email';
            if (!empty($record['phone'])) $fieldsAnonymized[] = 'phone';
            if (!empty($record['cpf'])) $fieldsAnonymized[] = 'cpf';

            // Anonimiza o registro
            $updateSql = "UPDATE appointments SET
                name = :name,
                email = :email,
                phone = :phone,
                whatsapp = :whatsapp,
                cpf = :cpf,
                message = :message,
                admin_notes = :admin_notes,
                background_check = NULL,
                sentiment_analysis = NULL
                WHERE id = :id";

            $anonymousId = 'ANON-' . $record['id'];

            $success = $this->db->update($updateSql, [
                'name' => "[ANONIMIZADO] Cliente {$anonymousId}",
                'email' => "anonimizado_{$record['id']}@lgpd.local",
                'phone' => null,
                'whatsapp' => null,
                'cpf' => null,
                'message' => '[MENSAGEM REMOVIDA POR LGPD]',
                'admin_notes' => '[NOTAS REMOVIDAS POR LGPD]',
                'id' => $record['id']
            ]);

            if ($success) {
                // Registra no log de anonimização
                $this->logAnonymization(
                    'appointments',
                    $record['id'],
                    $fieldsAnonymized,
                    'Compliance LGPD - Retenção expirada',
                    $record['created_at'],
                    $this->retentionMonths
                );

                $anonymizedCount++;
            }
        }

        return $anonymizedCount;
    }

    /**
     * Direito ao Esquecimento - Anonimiza dados de um cliente específico
     *
     * @param string $email Email do cliente
     * @param string $reason Motivo da solicitação
     * @return array Resultado
     */
    public function rightToBeForgotten($email, $reason = 'Solicitação do titular')
    {
        $startTime = microtime(true);

        try {
            $anonymizedCount = 0;

            // 1. Anonimiza agendamentos deste email
            $sql = "SELECT id, name, created_at FROM appointments WHERE email = :email";
            $appointments = $this->db->select($sql, ['email' => $email]);

            foreach ($appointments as $appointment) {
                $updateSql = "UPDATE appointments SET
                    name = :name,
                    email = :email,
                    phone = NULL,
                    whatsapp = NULL,
                    cpf = NULL,
                    message = '[REMOVIDO - DIREITO AO ESQUECIMENTO]',
                    admin_notes = '[REMOVIDO - DIREITO AO ESQUECIMENTO]',
                    background_check = NULL,
                    sentiment_analysis = NULL
                    WHERE id = :id";

                $anonymousId = 'FORGOTTEN-' . $appointment['id'];

                $this->db->update($updateSql, [
                    'name' => "[ESQUECIDO] {$anonymousId}",
                    'email' => "forgotten_{$appointment['id']}@lgpd.local",
                    'id' => $appointment['id']
                ]);

                $this->logAnonymization(
                    'appointments',
                    $appointment['id'],
                    ['name', 'email', 'phone', 'cpf', 'message', 'admin_notes'],
                    $reason,
                    $appointment['created_at'],
                    null
                );

                $anonymizedCount++;
            }

            $executionTime = microtime(true) - $startTime;

            $this->logIntelligence('lgpd_compliance', 'client', 0, [
                'operation' => 'right_to_be_forgotten',
                'email' => $email
            ], [
                'records_anonymized' => $anonymizedCount
            ], $executionTime, 'success');

            return [
                'success' => true,
                'data' => [
                    'records_anonymized' => $anonymizedCount
                ],
                'message' => 'Direito ao esquecimento processado com sucesso'
            ];

        } catch (\Exception $e) {
            $executionTime = microtime(true) - $startTime;

            $this->logIntelligence('lgpd_compliance', 'client', 0, [
                'operation' => 'right_to_be_forgotten',
                'email' => $email
            ], null, $executionTime, 'error', $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erro ao processar direito ao esquecimento'
            ];
        }
    }

    /**
     * Direito à Portabilidade - Exporta todos os dados de um cliente
     *
     * @param string $email Email do cliente
     * @return array Dados estruturados para exportação
     */
    public function rightToDataPortability($email)
    {
        try {
            $data = [];

            // Busca todos os agendamentos
            $sql = "SELECT * FROM appointments WHERE email = :email ORDER BY created_at DESC";
            $data['appointments'] = $this->db->select($sql, ['email' => $email]);

            // Remove campos internos sensíveis
            foreach ($data['appointments'] as &$appointment) {
                unset($appointment['admin_notes']);
                unset($appointment['recommended_lawyer_id']);
                unset($appointment['recommendation_score']);
            }

            // Log da operação
            $this->logIntelligence('lgpd_compliance', 'client', 0, [
                'operation' => 'data_portability',
                'email' => $email
            ], [
                'records_exported' => count($data['appointments'])
            ], 0, 'success');

            return [
                'success' => true,
                'data' => $data,
                'message' => 'Dados exportados com sucesso',
                'exported_at' => date('Y-m-d H:i:s')
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erro ao exportar dados'
            ];
        }
    }

    /**
     * Direito de Acesso - Retorna relatório de dados armazenados
     *
     * @param string $email Email do cliente
     * @return array Relatório
     */
    public function rightToAccess($email)
    {
        try {
            // Conta registros por tabela
            $summary = [];

            $sql = "SELECT COUNT(*) as total FROM appointments WHERE email = :email";
            $result = $this->db->selectOne($sql, ['email' => $email]);
            $summary['appointments'] = $result['total'] ?? 0;

            // Busca quando foi o último acesso/interação
            $sql = "SELECT MAX(created_at) as last_interaction FROM appointments WHERE email = :email";
            $result = $this->db->selectOne($sql, ['email' => $email]);
            $lastInteraction = $result['last_interaction'] ?? null;

            // Calcula quando os dados serão anonimizados
            $anonymizationDate = null;
            if ($lastInteraction) {
                $anonymizationDate = date('Y-m-d', strtotime($lastInteraction . " +{$this->retentionMonths} months"));
            }

            return [
                'success' => true,
                'data' => [
                    'email' => $email,
                    'data_summary' => $summary,
                    'last_interaction' => $lastInteraction,
                    'retention_period_months' => $this->retentionMonths,
                    'scheduled_anonymization' => $anonymizationDate,
                    'rights' => [
                        'access' => 'Você tem direito de acessar seus dados',
                        'rectification' => 'Você pode solicitar correção de dados incorretos',
                        'portability' => 'Você pode solicitar exportação de seus dados',
                        'erasure' => 'Você pode solicitar exclusão de seus dados (direito ao esquecimento)'
                    ]
                ],
                'message' => 'Relatório de acesso gerado'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erro ao gerar relatório de acesso'
            ];
        }
    }

    /**
     * Mascara campos sensíveis para exibição
     *
     * @param string $value Valor a ser mascarado
     * @param string $type Tipo (email, phone, cpf, name)
     * @return string Valor mascarado
     */
    public function maskSensitiveData($value, $type = 'generic')
    {
        if (empty($value)) {
            return $value;
        }

        switch ($type) {
            case 'email':
                // exemplo@email.com -> e****o@email.com
                $parts = explode('@', $value);
                if (count($parts) === 2) {
                    $name = $parts[0];
                    $masked = substr($name, 0, 1) . str_repeat('*', strlen($name) - 2) . substr($name, -1);
                    return $masked . '@' . $parts[1];
                }
                break;

            case 'phone':
                // (11) 98765-4321 -> (11) 98***-4321
                return preg_replace('/(\d{2})\s*(\d{4,5})-?(\d{4})/', '($1) $2***-****', $value);

            case 'cpf':
                // 123.456.789-00 -> ***.456.789-**
                return preg_replace('/(\d{3})(\.\d{3}\.\d{3}-)(\d{2})/', '***$2**', $value);

            case 'name':
                // João Silva Santos -> João S. S.
                $parts = explode(' ', $value);
                if (count($parts) > 1) {
                    $masked = $parts[0];
                    for ($i = 1; $i < count($parts); $i++) {
                        $masked .= ' ' . substr($parts[$i], 0, 1) . '.';
                    }
                    return $masked;
                }
                break;

            default:
                // Genérico: mostra apenas primeiros e últimos caracteres
                $len = strlen($value);
                if ($len <= 4) {
                    return str_repeat('*', $len);
                }
                return substr($value, 0, 2) . str_repeat('*', $len - 4) . substr($value, -2);
        }

        return $value;
    }

    /**
     * Verifica se um registro está dentro do período de retenção
     */
    public function isWithinRetentionPeriod($createdAt)
    {
        $cutoffDate = strtotime("-{$this->retentionMonths} months");
        $recordDate = strtotime($createdAt);
        return $recordDate >= $cutoffDate;
    }

    /**
     * Registra anonimização no log específico
     */
    private function logAnonymization($tableName, $recordId, $fieldsAnonymized, $reason, $originalCreatedAt, $retentionMonths)
    {
        $sql = "INSERT INTO lgpd_anonymization_log (
            table_name, record_id, fields_anonymized, reason,
            original_created_at, retention_months, anonymized_by
        ) VALUES (
            :table_name, :record_id, :fields, :reason,
            :original_created_at, :retention_months, :anonymized_by
        )";

        return $this->db->insert($sql, [
            'table_name' => $tableName,
            'record_id' => $recordId,
            'fields' => json_encode($fieldsAnonymized),
            'reason' => $reason,
            'original_created_at' => $originalCreatedAt,
            'retention_months' => $retentionMonths,
            'anonymized_by' => 'System - LGPD Auto Purge'
        ]);
    }

    /**
     * Registra operação no intelligence_logs
     */
    private function logIntelligence($serviceType, $entityType, $entityId, $inputData, $outputData, $executionTime, $status = 'success', $errorMessage = null)
    {
        $sql = "INSERT INTO intelligence_logs (
            service_type, entity_type, entity_id, input_data, output_data,
            execution_time, status, error_message, api_used
        ) VALUES (
            :service_type, :entity_type, :entity_id, :input_data, :output_data,
            :execution_time, :status, :error_message, :api_used
        )";

        return $this->db->insert($sql, [
            'service_type' => $serviceType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'input_data' => json_encode($inputData),
            'output_data' => $outputData ? json_encode($outputData) : null,
            'execution_time' => round($executionTime, 3),
            'status' => $status,
            'error_message' => $errorMessage,
            'api_used' => 'Internal LGPD Engine'
        ]);
    }

    /**
     * Obtém estatísticas de compliance
     */
    public function getComplianceStats()
    {
        // Total de registros anonimizados
        $sql = "SELECT COUNT(*) as total FROM lgpd_anonymization_log";
        $totalAnonymized = $this->db->selectOne($sql)['total'] ?? 0;

        // Registros por motivo
        $sql = "SELECT reason, COUNT(*) as count
                FROM lgpd_anonymization_log
                GROUP BY reason
                ORDER BY count DESC";
        $byReason = $this->db->select($sql);

        // Últimas anonimizações
        $sql = "SELECT * FROM lgpd_anonymization_log
                ORDER BY created_at DESC LIMIT 10";
        $recent = $this->db->select($sql);

        return [
            'total_anonymized' => $totalAnonymized,
            'by_reason' => $byReason,
            'recent_anonymizations' => $recent
        ];
    }
}
