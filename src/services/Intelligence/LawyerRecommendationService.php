<?php

namespace Services\Intelligence;

use Core\Database;
use Models\Lawyer;

/**
 * LawyerRecommendationService - Camada 2 do IDS
 *
 * Motor de recomendação de advogados baseado em:
 * - Especialização no tipo de consulta
 * - Taxa de sucesso histórica
 * - Número de casos vencidos
 * - Performance específica por tipo de caso
 *
 * Algoritmo: Calcula score ponderado considerando múltiplos fatores
 *
 * @package Services\Intelligence
 */
class LawyerRecommendationService
{
    private $db;
    private $lawyerModel;

    // Pesos do algoritmo de recomendação
    private $weights = [
        'specialty_match' => 0.40,    // 40% - Especialização específica
        'success_rate' => 0.30,        // 30% - Taxa de sucesso geral
        'cases_won' => 0.15,           // 15% - Experiência (casos vencidos)
        'recent_activity' => 0.10,     // 10% - Atividade recente
        'rating' => 0.05               //  5% - Avaliação de clientes
    ];

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->lawyerModel = new Lawyer();
    }

    /**
     * Recomenda o melhor advogado para um tipo de consulta
     *
     * @param string $consultationType Tipo de consulta (ex: "Direito Civil", "Trabalhista")
     * @param int|null $appointmentId ID do agendamento (para salvar recomendação)
     * @return array Ranking de advogados com scores
     */
    public function recommendLawyer($consultationType, $appointmentId = null)
    {
        $startTime = microtime(true);

        try {
            // Busca todos os advogados ativos
            $lawyers = $this->lawyerModel->where('status', '=', 'active');

            if (empty($lawyers)) {
                throw new \Exception('Nenhum advogado ativo encontrado');
            }

            // Calcula score para cada advogado
            $ranking = [];
            foreach ($lawyers as $lawyer) {
                $score = $this->calculateLawyerScore($lawyer, $consultationType);

                $ranking[] = [
                    'lawyer_id' => $lawyer['id'],
                    'name' => $lawyer['name'],
                    'oab' => $lawyer['oab_number'] . '/' . $lawyer['oab_state'],
                    'specialties' => $lawyer['specialties'],
                    'cases_won' => $lawyer['cases_won'],
                    'success_rate' => $lawyer['success_rate'],
                    'total_score' => $score['total'],
                    'score_breakdown' => $score['breakdown'],
                    'recommendation_reason' => $score['reason']
                ];
            }

            // Ordena por score (maior para menor)
            usort($ranking, function($a, $b) {
                return $b['total_score'] <=> $a['total_score'];
            });

            // Pega o melhor (top 1)
            $bestLawyer = $ranking[0];

            // Se foi passado appointment_id, salva a recomendação
            if ($appointmentId) {
                $this->saveRecommendation($appointmentId, $bestLawyer['lawyer_id'], $bestLawyer['total_score']);
            }

            // Registra log de inteligência
            $executionTime = microtime(true) - $startTime;
            $this->logIntelligence('lawyer_recommendation', 'appointment', $appointmentId ?? 0, [
                'consultation_type' => $consultationType
            ], [
                'top_recommendation' => $bestLawyer,
                'total_analyzed' => count($ranking)
            ], $executionTime, 'success');

            return [
                'success' => true,
                'data' => [
                    'recommended' => $bestLawyer,
                    'alternatives' => array_slice($ranking, 1, 3), // Top 2-4
                    'full_ranking' => $ranking
                ],
                'message' => 'Recomendação calculada com sucesso'
            ];

        } catch (\Exception $e) {
            $executionTime = microtime(true) - $startTime;

            $this->logIntelligence('lawyer_recommendation', 'appointment', $appointmentId ?? 0, [
                'consultation_type' => $consultationType
            ], null, $executionTime, 'error', $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erro ao calcular recomendação'
            ];
        }
    }

    /**
     * Calcula score de um advogado para um tipo de consulta específico
     *
     * @param array $lawyer Dados do advogado
     * @param string $consultationType Tipo de consulta
     * @return array Score total e breakdown
     */
    private function calculateLawyerScore($lawyer, $consultationType)
    {
        $breakdown = [];
        $reasons = [];

        // 1. SPECIALTY MATCH (40%) - Match da especialidade
        $specialtyScore = $this->calculateSpecialtyMatch($lawyer['specialties'], $consultationType);
        $breakdown['specialty_match'] = round($specialtyScore * 100, 2);

        if ($specialtyScore >= 0.8) {
            $reasons[] = "Especialista direto em {$consultationType}";
        } elseif ($specialtyScore >= 0.5) {
            $reasons[] = "Possui especialização relacionada";
        }

        // 2. SUCCESS RATE (30%) - Taxa de sucesso histórica
        $successRate = floatval($lawyer['success_rate']) / 100; // Converte % para 0-1
        $breakdown['success_rate'] = round($successRate * 100, 2);

        if ($successRate >= 0.8) {
            $reasons[] = "Excelente histórico de vitórias ({$lawyer['success_rate']}%)";
        }

        // 3. CASES WON (15%) - Experiência (número de casos vencidos)
        $casesWonScore = $this->calculateExperienceScore($lawyer['cases_won']);
        $breakdown['experience'] = round($casesWonScore * 100, 2);

        if ($lawyer['cases_won'] > 50) {
            $reasons[] = "Grande experiência ({$lawyer['cases_won']} casos vencidos)";
        }

        // 4. RECENT ACTIVITY (10%) - Performance em casos recentes do mesmo tipo
        $recentScore = $this->getRecentPerformanceScore($lawyer['id'], $consultationType);
        $breakdown['recent_activity'] = round($recentScore * 100, 2);

        // 5. RATING (5%) - Avaliação de clientes
        $ratingScore = floatval($lawyer['average_rating']) / 5; // Converte 0-5 para 0-1
        $breakdown['client_rating'] = round($ratingScore * 100, 2);

        if ($lawyer['average_rating'] >= 4.5) {
            $reasons[] = "Altamente avaliado pelos clientes ({$lawyer['average_rating']}/5)";
        }

        // Calcula score total ponderado
        $totalScore = (
            ($specialtyScore * $this->weights['specialty_match']) +
            ($successRate * $this->weights['success_rate']) +
            ($casesWonScore * $this->weights['cases_won']) +
            ($recentScore * $this->weights['recent_activity']) +
            ($ratingScore * $this->weights['rating'])
        );

        // Normaliza para 0-100
        $totalScore = $totalScore * 100;

        return [
            'total' => round($totalScore, 2),
            'breakdown' => $breakdown,
            'reason' => implode(' • ', $reasons) ?: 'Perfil adequado para o caso'
        ];
    }

    /**
     * Calcula match entre especialidades do advogado e tipo de consulta
     *
     * @param string|null $specialties Especialidades do advogado (string separada por vírgula)
     * @param string $consultationType Tipo de consulta buscado
     * @return float Score de 0 a 1
     */
    private function calculateSpecialtyMatch($specialties, $consultationType)
    {
        if (empty($specialties)) {
            return 0.3; // Score mínimo se não tiver especialidades cadastradas
        }

        // Normaliza strings para comparação
        $consultationType = $this->normalizeString($consultationType);
        $specialtiesArray = array_map('trim', explode(',', $specialties));

        // Verifica match exato
        foreach ($specialtiesArray as $specialty) {
            $normalizedSpecialty = $this->normalizeString($specialty);

            // Match exato
            if ($normalizedSpecialty === $consultationType) {
                return 1.0;
            }

            // Match parcial (contém)
            if (strpos($normalizedSpecialty, $consultationType) !== false ||
                strpos($consultationType, $normalizedSpecialty) !== false) {
                return 0.8;
            }
        }

        // Busca por palavras-chave em comum
        $consultationWords = explode(' ', $consultationType);
        $matchingWords = 0;

        foreach ($specialtiesArray as $specialty) {
            $specialtyWords = explode(' ', $this->normalizeString($specialty));
            foreach ($consultationWords as $word) {
                if (strlen($word) >= 4 && in_array($word, $specialtyWords)) {
                    $matchingWords++;
                }
            }
        }

        if ($matchingWords > 0) {
            return min(0.5 + ($matchingWords * 0.1), 0.7);
        }

        // Sem match relevante
        return 0.3;
    }

    /**
     * Calcula score baseado em experiência (número de casos)
     *
     * @param int $casesWon Número de casos vencidos
     * @return float Score de 0 a 1
     */
    private function calculateExperienceScore($casesWon)
    {
        // Curva logarítmica para não favorecer demais a quantidade
        if ($casesWon >= 100) return 1.0;
        if ($casesWon >= 50) return 0.9;
        if ($casesWon >= 30) return 0.8;
        if ($casesWon >= 20) return 0.7;
        if ($casesWon >= 10) return 0.6;
        if ($casesWon >= 5) return 0.5;
        if ($casesWon >= 1) return 0.4;

        return 0.2; // Iniciante
    }

    /**
     * Busca performance recente do advogado neste tipo de caso
     *
     * @param int $lawyerId ID do advogado
     * @param string $consultationType Tipo de consulta
     * @return float Score de 0 a 1
     */
    private function getRecentPerformanceScore($lawyerId, $consultationType)
    {
        // Busca na tabela de histórico de performance
        $sql = "SELECT success_rate, last_case_date
                FROM lawyer_performance_history
                WHERE lawyer_id = :lawyer_id
                AND consultation_type LIKE :type
                ORDER BY last_case_date DESC
                LIMIT 1";

        $result = $this->db->selectOne($sql, [
            'lawyer_id' => $lawyerId,
            'type' => '%' . $consultationType . '%'
        ]);

        if (!$result) {
            return 0.5; // Score neutro se não tiver histórico
        }

        // Verifica se tem casos recentes (últimos 6 meses)
        $sixMonthsAgo = date('Y-m-d', strtotime('-6 months'));
        $isRecent = $result['last_case_date'] >= $sixMonthsAgo;

        $baseScore = floatval($result['success_rate']) / 100;

        // Bonifica se for recente
        return $isRecent ? min($baseScore * 1.2, 1.0) : $baseScore * 0.8;
    }

    /**
     * Salva recomendação no banco de dados
     *
     * @param int $appointmentId ID do agendamento
     * @param int $lawyerId ID do advogado recomendado
     * @param float $score Score da recomendação
     * @return bool Sucesso
     */
    private function saveRecommendation($appointmentId, $lawyerId, $score)
    {
        $sql = "UPDATE appointments
                SET recommended_lawyer_id = :lawyer_id,
                    recommendation_score = :score
                WHERE id = :id";

        return $this->db->update($sql, [
            'lawyer_id' => $lawyerId,
            'score' => $score,
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
            'api_used' => 'Internal Algorithm'
        ]);
    }

    /**
     * Normaliza string para comparação (remove acentos, lowercase)
     */
    private function normalizeString($str)
    {
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/[áàãâä]/u', 'a', $str);
        $str = preg_replace('/[éèêë]/u', 'e', $str);
        $str = preg_replace('/[íìîï]/u', 'i', $str);
        $str = preg_replace('/[óòõôö]/u', 'o', $str);
        $str = preg_replace('/[úùûü]/u', 'u', $str);
        $str = preg_replace('/[ç]/u', 'c', $str);
        return $str;
    }

    /**
     * Atualiza estatísticas de performance após conclusão de um caso
     *
     * @param int $lawyerId ID do advogado
     * @param string $consultationType Tipo de consulta
     * @param bool $won Se o caso foi vencido
     * @param int|null $durationDays Duração em dias
     * @param float|null $revenue Receita gerada
     * @return bool Sucesso
     */
    public function updatePerformanceHistory($lawyerId, $consultationType, $won, $durationDays = null, $revenue = null)
    {
        // Busca registro existente
        $sql = "SELECT * FROM lawyer_performance_history
                WHERE lawyer_id = :lawyer_id AND consultation_type = :type";

        $existing = $this->db->selectOne($sql, [
            'lawyer_id' => $lawyerId,
            'type' => $consultationType
        ]);

        if ($existing) {
            // Atualiza registro existente
            $newCasesHandled = $existing['cases_handled'] + 1;
            $newCasesWon = $existing['cases_won'] + ($won ? 1 : 0);
            $newSuccessRate = ($newCasesWon / $newCasesHandled) * 100;

            // Calcula nova média de duração (se fornecido)
            $newAvgDuration = $existing['avg_duration_days'];
            if ($durationDays !== null) {
                $newAvgDuration = (($existing['avg_duration_days'] * $existing['cases_handled']) + $durationDays) / $newCasesHandled;
            }

            // Calcula nova média de receita (se fornecido)
            $newAvgRevenue = $existing['avg_revenue'];
            if ($revenue !== null) {
                $newAvgRevenue = (($existing['avg_revenue'] * $existing['cases_handled']) + $revenue) / $newCasesHandled;
            }

            $updateSql = "UPDATE lawyer_performance_history SET
                cases_handled = :cases_handled,
                cases_won = :cases_won,
                success_rate = :success_rate,
                avg_duration_days = :avg_duration,
                avg_revenue = :avg_revenue,
                last_case_date = CURDATE()
                WHERE id = :id";

            return $this->db->update($updateSql, [
                'cases_handled' => $newCasesHandled,
                'cases_won' => $newCasesWon,
                'success_rate' => $newSuccessRate,
                'avg_duration' => $newAvgDuration,
                'avg_revenue' => $newAvgRevenue,
                'id' => $existing['id']
            ]);
        } else {
            // Cria novo registro
            $insertSql = "INSERT INTO lawyer_performance_history (
                lawyer_id, consultation_type, cases_handled, cases_won,
                success_rate, avg_duration_days, avg_revenue, last_case_date
            ) VALUES (
                :lawyer_id, :type, 1, :won, :success_rate,
                :avg_duration, :avg_revenue, CURDATE()
            )";

            return $this->db->insert($insertSql, [
                'lawyer_id' => $lawyerId,
                'type' => $consultationType,
                'won' => $won ? 1 : 0,
                'success_rate' => $won ? 100.00 : 0.00,
                'avg_duration' => $durationDays,
                'avg_revenue' => $revenue
            ]);
        }
    }
}
