<?php

namespace Services\Intelligence;

use Core\Database;
use Models\Appointment;

/**
 * BackgroundCheckService - Camada 1 do IDS
 *
 * Realiza verificações de background em clientes através de integração
 * com APIs externas (Jusbrasil, Serpro, etc.)
 *
 * Funcionalidades:
 * - Busca processos judiciais por CPF
 * - Consulta situação cadastral na Receita Federal
 * - Verifica restrições e pendências
 * - Salva resumo estruturado no banco de dados
 *
 * @package Services\Intelligence
 */
class BackgroundCheckService
{
    private $db;
    private $appointmentModel;
    private $config;

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->appointmentModel = new Appointment();

        // Configurações das APIs (podem vir do .env ou settings)
        $this->config = [
            'jusbrasil_api_key' => getenv('JUSBRASIL_API_KEY') ?: '',
            'serpro_api_key' => getenv('SERPRO_API_KEY') ?: '',
            'serpro_api_url' => 'https://gateway.apiserpro.serpro.gov.br/consulta-cpf-df/v1',
            'timeout' => 30, // segundos
            'cache_ttl' => 86400 // 24 horas em segundos
        ];
    }

    /**
     * Executa background check completo para um agendamento
     *
     * @param int $appointmentId ID do agendamento
     * @param string $cpf CPF do cliente (formato: 123.456.789-00 ou 12345678900)
     * @return array Resultado do background check
     */
    public function executeForAppointment($appointmentId, $cpf)
    {
        $startTime = microtime(true);

        try {
            // Valida e limpa CPF
            $cpf = $this->sanitizeCPF($cpf);

            if (!$this->isValidCPF($cpf)) {
                throw new \Exception('CPF inválido');
            }

            // Atualiza o CPF no registro do agendamento
            $this->appointmentModel->update($appointmentId, ['cpf' => $cpf]);

            // Executa as consultas em paralelo (simulação - na prática usar curl_multi)
            $results = [
                'cpf' => $this->formatCPF($cpf),
                'checked_at' => date('Y-m-d H:i:s'),
                'situacao_cadastral' => $this->checkCadastralStatus($cpf),
                'processos_judiciais' => $this->checkLegalProcesses($cpf),
                'restricoes' => $this->checkRestrictions($cpf),
                'score_risco' => 0,
                'recomendacao' => ''
            ];

            // Calcula score de risco (0-100, onde 0 = sem risco, 100 = alto risco)
            $results['score_risco'] = $this->calculateRiskScore($results);
            $results['recomendacao'] = $this->generateRecommendation($results['score_risco']);

            // Salva resultado estruturado no banco
            $this->saveBackgroundCheck($appointmentId, $results);

            // Registra log de inteligência
            $executionTime = microtime(true) - $startTime;
            $this->logIntelligence('background_check', 'appointment', $appointmentId, [
                'cpf' => $this->maskCPF($cpf)
            ], $results, $executionTime, 'success');

            return [
                'success' => true,
                'data' => $results,
                'message' => 'Background check realizado com sucesso'
            ];

        } catch (\Exception $e) {
            $executionTime = microtime(true) - $startTime;

            // Registra erro
            $this->logIntelligence('background_check', 'appointment', $appointmentId, [
                'cpf' => isset($cpf) ? $this->maskCPF($cpf) : 'N/A'
            ], null, $executionTime, 'error', $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erro ao realizar background check'
            ];
        }
    }

    /**
     * Consulta situação cadastral na Receita Federal via API Serpro
     *
     * @param string $cpf CPF limpo (apenas números)
     * @return array Dados cadastrais
     */
    private function checkCadastralStatus($cpf)
    {
        // Se não tiver API key, retorna simulação
        if (empty($this->config['serpro_api_key'])) {
            return $this->simulateCadastralStatus();
        }

        try {
            $url = $this->config['serpro_api_url'] . '/cpf/' . $cpf;

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $this->config['timeout'],
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $this->config['serpro_api_key'],
                    'Content-Type: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $data = json_decode($response, true);

                return [
                    'situacao' => $data['situacao']['codigo'] ?? 'Desconhecida',
                    'nome' => $data['nome'] ?? '',
                    'data_nascimento' => $data['nascimento'] ?? '',
                    'fonte' => 'API Serpro - Receita Federal',
                    'consultado_em' => date('Y-m-d H:i:s')
                ];
            }

            return $this->simulateCadastralStatus();

        } catch (\Exception $e) {
            // Em caso de erro, retorna simulação
            return $this->simulateCadastralStatus();
        }
    }

    /**
     * Busca processos judiciais via API Jusbrasil
     *
     * @param string $cpf CPF limpo
     * @return array Lista de processos encontrados
     */
    private function checkLegalProcesses($cpf)
    {
        // Se não tiver API key, retorna simulação
        if (empty($this->config['jusbrasil_api_key'])) {
            return $this->simulateLegalProcesses();
        }

        try {
            // Endpoint fictício - ajustar conforme documentação real da API
            $url = 'https://api.jusbrasil.com.br/v1/search/processes';

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url . '?cpf=' . $cpf,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $this->config['timeout'],
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $this->config['jusbrasil_api_key'],
                    'Content-Type: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $data = json_decode($response, true);

                $processes = [];
                foreach ($data['results'] ?? [] as $proc) {
                    $processes[] = [
                        'numero' => $proc['numero'] ?? '',
                        'tipo' => $proc['tipo'] ?? '',
                        'status' => $proc['status'] ?? '',
                        'tribunal' => $proc['tribunal'] ?? '',
                        'data_distribuicao' => $proc['data'] ?? ''
                    ];
                }

                return [
                    'total' => count($processes),
                    'processos' => array_slice($processes, 0, 10), // Máximo 10 para resumo
                    'fonte' => 'API Jusbrasil',
                    'consultado_em' => date('Y-m-d H:i:s')
                ];
            }

            return $this->simulateLegalProcesses();

        } catch (\Exception $e) {
            return $this->simulateLegalProcesses();
        }
    }

    /**
     * Verifica restrições (Serasa, SPC, etc.)
     * Nota: Requer integração com APIs específicas
     *
     * @param string $cpf CPF limpo
     * @return array Dados de restrições
     */
    private function checkRestrictions($cpf)
    {
        // Simulação - na prática, integrar com APIs de bureaus de crédito
        return [
            'possui_restricoes' => false,
            'total_restricoes' => 0,
            'valor_total' => 0.00,
            'detalhes' => [],
            'fonte' => 'Simulado - Integração futura',
            'consultado_em' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Calcula score de risco baseado nos dados coletados
     *
     * @param array $results Resultados das consultas
     * @return int Score de 0 a 100
     */
    private function calculateRiskScore($results)
    {
        $score = 0;

        // Situação cadastral irregular
        if (isset($results['situacao_cadastral']['situacao']) &&
            !in_array($results['situacao_cadastral']['situacao'], ['Regular', '0'])) {
            $score += 30;
        }

        // Processos judiciais
        $totalProcessos = $results['processos_judiciais']['total'] ?? 0;
        if ($totalProcessos > 0) {
            $score += min($totalProcessos * 10, 40); // Máximo 40 pontos
        }

        // Restrições financeiras
        if ($results['restricoes']['possui_restricoes']) {
            $score += 30;
        }

        return min($score, 100); // Garante máximo de 100
    }

    /**
     * Gera recomendação baseada no score de risco
     *
     * @param int $score Score de risco (0-100)
     * @return string Recomendação
     */
    private function generateRecommendation($score)
    {
        if ($score >= 70) {
            return '🔴 ALTO RISCO - Avaliar cuidadosamente antes de aceitar o caso. Considerar garantias adicionais.';
        } elseif ($score >= 40) {
            return '🟡 RISCO MODERADO - Proceder com cautela. Verificar capacidade de pagamento.';
        } else {
            return '🟢 BAIXO RISCO - Cliente com bom perfil. Prosseguir normalmente.';
        }
    }

    /**
     * Salva resultado do background check no banco
     *
     * @param int $appointmentId ID do agendamento
     * @param array $results Resultados
     * @return bool Sucesso
     */
    private function saveBackgroundCheck($appointmentId, $results)
    {
        // Converte para JSON
        $jsonData = json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // Atualiza o campo background_check
        $sql = "UPDATE appointments SET background_check = :data WHERE id = :id";
        return $this->db->update($sql, [
            'data' => $jsonData,
            'id' => $appointmentId
        ]);
    }

    /**
     * Registra operação no intelligence_logs
     *
     * @param string $serviceType Tipo de serviço
     * @param string $entityType Tipo de entidade
     * @param int $entityId ID da entidade
     * @param array $inputData Dados de entrada
     * @param array|null $outputData Dados de saída
     * @param float $executionTime Tempo de execução
     * @param string $status Status (success, error, partial)
     * @param string|null $errorMessage Mensagem de erro
     * @return bool Sucesso
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

        $apiUsed = [];
        if (!empty($this->config['serpro_api_key'])) $apiUsed[] = 'Serpro';
        if (!empty($this->config['jusbrasil_api_key'])) $apiUsed[] = 'Jusbrasil';

        return $this->db->insert($sql, [
            'service_type' => $serviceType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'input_data' => json_encode($inputData),
            'output_data' => $outputData ? json_encode($outputData) : null,
            'execution_time' => round($executionTime, 3),
            'status' => $status,
            'error_message' => $errorMessage,
            'api_used' => implode(', ', $apiUsed) ?: 'Simulação'
        ]);
    }

    // ============================================
    // MÉTODOS AUXILIARES
    // ============================================

    /**
     * Remove formatação do CPF
     */
    private function sanitizeCPF($cpf)
    {
        return preg_replace('/[^0-9]/', '', $cpf);
    }

    /**
     * Valida CPF
     */
    private function isValidCPF($cpf)
    {
        if (strlen($cpf) != 11) return false;
        if (preg_match('/(\d)\1{10}/', $cpf)) return false;

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) return false;
        }
        return true;
    }

    /**
     * Formata CPF (123.456.789-00)
     */
    private function formatCPF($cpf)
    {
        return substr($cpf, 0, 3) . '.' .
               substr($cpf, 3, 3) . '.' .
               substr($cpf, 6, 3) . '-' .
               substr($cpf, 9, 2);
    }

    /**
     * Mascara CPF para logs (***.456.789-**)
     */
    private function maskCPF($cpf)
    {
        $cpf = $this->formatCPF($cpf);
        return '***.' . substr($cpf, 4, 7) . '-**';
    }

    /**
     * Simulação de dados cadastrais (para testes sem API)
     */
    private function simulateCadastralStatus()
    {
        return [
            'situacao' => 'Regular',
            'nome' => '[Consulta via API Real]',
            'data_nascimento' => '',
            'fonte' => 'Simulação - Configure SERPRO_API_KEY no .env',
            'consultado_em' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Simulação de processos judiciais (para testes sem API)
     */
    private function simulateLegalProcesses()
    {
        return [
            'total' => 0,
            'processos' => [],
            'fonte' => 'Simulação - Configure JUSBRASIL_API_KEY no .env',
            'consultado_em' => date('Y-m-d H:i:s')
        ];
    }
}
