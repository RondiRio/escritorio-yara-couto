<?php

namespace Services\Intelligence;

use Core\Database;

/**
 * NLPSentimentService - Camada 3 do IDS
 *
 * Análise de sentimento e urgência usando OpenAI GPT
 * - Analisa mensagens de agendamentos e contatos
 * - Detecta urgência, emoção e sentimento
 * - Calcula score de prioridade
 * - Identifica palavras-chave críticas
 *
 * API: OpenAI GPT-4 ou GPT-3.5-turbo
 *
 * @package Services\Intelligence
 */
class NLPSentimentService
{
    private $db;
    private $apiKey;
    private $model = 'gpt-3.5-turbo'; // Mais barato, ideal para análise de texto
    private $maxTokens = 500;

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->apiKey = getenv('OPENAI_API_KEY');
    }

    /**
     * Analisa mensagem de um agendamento e atualiza urgência
     *
     * @param int $appointmentId ID do agendamento
     * @param string $message Mensagem a ser analisada
     * @param string|null $contactType Tipo de contato (opcional)
     * @return array Resultado da análise
     */
    public function analyzeAppointmentMessage($appointmentId, $message, $contactType = null)
    {
        $startTime = microtime(true);

        try {
            // Valida entrada
            if (empty($message)) {
                throw new \Exception('Mensagem vazia não pode ser analisada');
            }

            // Se API não configurada, usa análise por keywords (fallback)
            if (empty($this->apiKey)) {
                $analysis = $this->fallbackKeywordAnalysis($message, $contactType);
            } else {
                // Análise via OpenAI API
                $analysis = $this->analyzeWithOpenAI($message, $contactType);
            }

            // Salva análise no banco
            $this->saveAnalysisToAppointment($appointmentId, $analysis);

            // Log de sucesso
            $executionTime = microtime(true) - $startTime;
            $this->logIntelligence('sentiment_analysis', 'appointment', $appointmentId, [
                'message_length' => strlen($message),
                'contact_type' => $contactType
            ], $analysis, $executionTime, 'success');

            return [
                'success' => true,
                'data' => $analysis,
                'message' => 'Análise de sentimento concluída'
            ];

        } catch (\Exception $e) {
            $executionTime = microtime(true) - $startTime;

            $this->logIntelligence('sentiment_analysis', 'appointment', $appointmentId, [
                'message_length' => strlen($message ?? ''),
                'contact_type' => $contactType
            ], null, $executionTime, 'error', $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erro ao analisar sentimento'
            ];
        }
    }

    /**
     * Análise via OpenAI API
     *
     * @param string $message Texto a analisar
     * @param string|null $type Tipo de consulta
     * @return array Análise estruturada
     */
    private function analyzeWithOpenAI($message, $type = null)
    {
        $prompt = $this->buildPrompt($message, $type);

        $data = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Você é um assistente especializado em análise de sentimento e urgência para um escritório de advocacia. Analise mensagens de clientes e retorne JSON estruturado.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => $this->maxTokens,
            'temperature' => 0.3, // Baixa para respostas consistentes
            'response_format' => ['type' => 'json_object']
        ];

        // Chamada à API
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("OpenAI API retornou código {$httpCode}");
        }

        $result = json_decode($response, true);

        if (!isset($result['choices'][0]['message']['content'])) {
            throw new \Exception('Resposta inválida da OpenAI API');
        }

        // Parse do JSON retornado
        $analysis = json_decode($result['choices'][0]['message']['content'], true);

        // Custo estimado (GPT-3.5-turbo: $0.0015/1K input tokens, $0.002/1K output tokens)
        $tokensUsed = $result['usage']['total_tokens'] ?? 0;
        $estimatedCost = ($tokensUsed / 1000) * 0.002;

        $analysis['tokens_used'] = $tokensUsed;
        $analysis['cost'] = round($estimatedCost, 4);
        $analysis['api_used'] = 'OpenAI GPT-3.5-turbo';

        return $analysis;
    }

    /**
     * Constrói prompt para análise
     */
    private function buildPrompt($message, $type = null)
    {
        $typeContext = $type ? "Tipo de consulta: {$type}\n" : "";

        return "Analise a seguinte mensagem de um potencial cliente de escritório de advocacia:\n\n" .
               "{$typeContext}" .
               "Mensagem: \"{$message}\"\n\n" .
               "Retorne um JSON com:\n" .
               "{\n" .
               "  \"sentiment\": \"positive|neutral|negative|urgent\",\n" .
               "  \"urgency_score\": 1-10 (1=baixa urgência, 10=urgência extrema),\n" .
               "  \"priority_level\": 1-10,\n" .
               "  \"emotion\": \"calm|worried|anxious|desperate|angry|hopeful\",\n" .
               "  \"keywords\": [\"palavras-chave identificadas\"],\n" .
               "  \"legal_area_detected\": \"área do direito identificada ou null\",\n" .
               "  \"requires_immediate_attention\": true|false,\n" .
               "  \"reasoning\": \"breve explicação da análise\"\n" .
               "}\n\n" .
               "Critérios de urgência alta (8-10):\n" .
               "- Prazos judiciais iminentes\n" .
               "- Palavras como 'urgente', 'hoje', 'amanhã', 'prazo'\n" .
               "- Situações de risco (prisão, despejo, bloqueio)\n" .
               "- Emoção de desespero ou pânico\n\n" .
               "Retorne APENAS o JSON, sem texto adicional.";
    }

    /**
     * Análise por palavras-chave (fallback quando API não disponível)
     */
    private function fallbackKeywordAnalysis($message, $type = null)
    {
        $message = mb_strtolower($message, 'UTF-8');

        $urgencyScore = 5; // Neutro
        $priorityLevel = 5;
        $sentiment = 'neutral';
        $emotion = 'calm';
        $keywords = [];
        $requiresImmediate = false;

        // Palavras de URGÊNCIA ALTA (score +3)
        $urgentWords = ['urgente', 'urgência', 'emergência', 'imediato', 'hoje', 'agora',
                        'prazo', 'vence', 'amanhã', 'rápido', 'socorro'];
        foreach ($urgentWords as $word) {
            if (strpos($message, $word) !== false) {
                $urgencyScore += 3;
                $keywords[] = $word;
                $requiresImmediate = true;
            }
        }

        // Palavras de SITUAÇÃO CRÍTICA (score +4)
        $criticalWords = ['prisão', 'preso', 'detido', 'despejo', 'despejar', 'bloqueio',
                          'bloqueado', 'executado', 'execução', 'penhora'];
        foreach ($criticalWords as $word) {
            if (strpos($message, $word) !== false) {
                $urgencyScore += 4;
                $keywords[] = $word;
                $requiresImmediate = true;
                $emotion = 'desperate';
            }
        }

        // Palavras de SENTIMENTO NEGATIVO (score +2)
        $negativeWords = ['problema', 'dificuldade', 'preocupado', 'nervoso', 'medo',
                          'injustiça', 'prejudicado', 'lesado'];
        foreach ($negativeWords as $word) {
            if (strpos($message, $word) !== false) {
                $urgencyScore += 2;
                $sentiment = 'negative';
                $emotion = 'worried';
            }
        }

        // Palavras de ESPERANÇA (score neutro, mas identifica sentimento)
        $hopefulWords = ['espero', 'confio', 'acredito', 'ajuda', 'auxílio', 'orientação'];
        foreach ($hopefulWords as $word) {
            if (strpos($message, $word) !== false) {
                $emotion = 'hopeful';
                $sentiment = 'positive';
            }
        }

        // Detecta área do direito por keywords
        $legalAreas = [
            'civil' => ['divórcio', 'separação', 'inventário', 'herança', 'contrato', 'indenização'],
            'trabalhista' => ['trabalhista', 'demissão', 'fgts', 'rescisão', 'horas extras', 'férias'],
            'criminal' => ['criminal', 'processo', 'delegacia', 'polícia', 'prisão', 'audiência'],
            'família' => ['pensão', 'guarda', 'filho', 'filha', 'alimentos', 'visitação'],
            'consumidor' => ['consumidor', 'compra', 'produto', 'serviço', 'banco', 'dívida']
        ];

        $detectedArea = null;
        foreach ($legalAreas as $area => $words) {
            foreach ($words as $word) {
                if (strpos($message, $word) !== false) {
                    $detectedArea = "Direito " . ucfirst($area);
                    break 2;
                }
            }
        }

        // Normaliza scores (máximo 10)
        $urgencyScore = min($urgencyScore, 10);
        $priorityLevel = min($urgencyScore, 10);

        // Define sentimento final
        if ($urgencyScore >= 8) {
            $sentiment = 'urgent';
        }

        return [
            'sentiment' => $sentiment,
            'urgency_score' => $urgencyScore,
            'priority_level' => $priorityLevel,
            'emotion' => $emotion,
            'keywords' => array_unique($keywords),
            'legal_area_detected' => $detectedArea,
            'requires_immediate_attention' => $requiresImmediate,
            'reasoning' => 'Análise por palavras-chave (modo offline)',
            'api_used' => 'Keyword Analysis (Fallback)',
            'tokens_used' => 0,
            'cost' => 0
        ];
    }

    /**
     * Salva análise no banco de dados
     */
    private function saveAnalysisToAppointment($appointmentId, $analysis)
    {
        $sql = "UPDATE appointments SET
                sentiment_analysis = :analysis,
                priority_level = :priority,
                urgency_score = :urgency
                WHERE id = :id";

        return $this->db->update($sql, [
            'analysis' => json_encode($analysis),
            'priority' => $analysis['priority_level'],
            'urgency' => $analysis['urgency_score'],
            'id' => $appointmentId
        ]);
    }

    /**
     * Registra operação no intelligence_logs
     */
    private function logIntelligence($serviceType, $entityType, $entityId, $inputData, $outputData, $executionTime, $status = 'success', $errorMessage = null)
    {
        $sql = "INSERT INTO intelligence_logs (
            service_type, entity_type, entity_id, input_data, output_data,
            execution_time, status, error_message, api_used, cost
        ) VALUES (
            :service_type, :entity_type, :entity_id, :input_data, :output_data,
            :execution_time, :status, :error_message, :api_used, :cost
        )";

        $cost = $outputData['cost'] ?? 0;
        $apiUsed = $outputData['api_used'] ?? 'Unknown';

        return $this->db->insert($sql, [
            'service_type' => $serviceType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'input_data' => json_encode($inputData),
            'output_data' => $outputData ? json_encode($outputData) : null,
            'execution_time' => round($executionTime, 3),
            'status' => $status,
            'error_message' => $errorMessage,
            'api_used' => $apiUsed,
            'cost' => $cost
        ]);
    }

    /**
     * Analisa múltiplas mensagens em lote
     *
     * @param array $appointments Array de agendamentos com 'id' e 'message'
     * @return array Resultados processados
     */
    public function analyzeBatch(array $appointments)
    {
        $results = [];
        $totalCost = 0;

        foreach ($appointments as $appointment) {
            $result = $this->analyzeAppointmentMessage(
                $appointment['id'],
                $appointment['message'],
                $appointment['type'] ?? null
            );

            if ($result['success']) {
                $totalCost += $result['data']['cost'] ?? 0;
            }

            $results[] = $result;
        }

        return [
            'total_processed' => count($appointments),
            'total_cost' => round($totalCost, 4),
            'results' => $results
        ];
    }

    /**
     * Obtém estatísticas de uso da API
     */
    public function getUsageStats($startDate = null, $endDate = null)
    {
        $whereClause = "WHERE service_type = 'sentiment_analysis'";
        $params = [];

        if ($startDate) {
            $whereClause .= " AND DATE(created_at) >= :start_date";
            $params['start_date'] = $startDate;
        }

        if ($endDate) {
            $whereClause .= " AND DATE(created_at) <= :end_date";
            $params['end_date'] = $endDate;
        }

        $sql = "SELECT
                COUNT(*) as total_analyses,
                SUM(cost) as total_cost,
                AVG(execution_time) as avg_execution_time,
                SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful,
                SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as failed
                FROM intelligence_logs
                {$whereClause}";

        return $this->db->selectOne($sql, $params);
    }
}
