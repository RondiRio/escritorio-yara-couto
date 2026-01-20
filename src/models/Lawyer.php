<?php

namespace Models;

use Core\Model;

/**
 * Model Lawyer - Gerencia advogados
 */
class Lawyer extends Model
{
    protected $table = 'lawyers';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'oab_number',
        'oab_state',
        'photo',
        'bio',
        'specialties',
        'specialties_json',
        'email',
        'phone',
        'whatsapp',
        'cases_won',
        'cases_total',
        'success_rate',
        'average_rating',
        'total_ratings',
        'status',
        'display_order'
    ];
    protected $timestamps = true;

    /**
     * Busca advogados ativos
     */
    public function getActive($orderBy = 'display_order ASC')
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY {$orderBy}";
        return $this->db->select($sql);
    }

    /**
     * Busca advogado por OAB
     */
    public function findByOAB($oabNumber, $oabState)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE oab_number = :oab_number 
                AND oab_state = :oab_state 
                LIMIT 1";
        
        return $this->db->selectOne($sql, [
            'oab_number' => $oabNumber,
            'oab_state' => $oabState
        ]);
    }

    /**
     * Verifica se OAB já existe
     */
    public function oabExists($oabNumber, $oabState, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE oab_number = :oab_number 
                AND oab_state = :oab_state";
        
        $params = [
            'oab_number' => $oabNumber,
            'oab_state' => $oabState
        ];

        if ($excludeId) {
            $sql .= " AND {$this->primaryKey} != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $result = $this->db->selectOne($sql, $params);
        return $result && $result['total'] > 0;
    }

    /**
     * Valida número de OAB
     * Referência: https://cna.oab.org.br/
     */
    public function validateOAB($oabNumber, $oabState)
    {
        // Remove caracteres não numéricos
        $oabNumber = preg_replace('/[^0-9]/', '', $oabNumber);

        // Verifica se tem apenas números
        if (!is_numeric($oabNumber)) {
            return false;
        }

        // Verifica tamanho (geralmente entre 5 e 7 dígitos)
        if (strlen($oabNumber) < 3 || strlen($oabNumber) > 7) {
            return false;
        }

        // Lista de UFs válidas
        $validStates = [
            'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA',
            'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN',
            'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
        ];

        if (!in_array(strtoupper($oabState), $validStates)) {
            return false;
        }

        return true;
    }

    /**
     * Formata número OAB
     */
    public function formatOAB($oabNumber, $oabState)
    {
        $oabNumber = preg_replace('/[^0-9]/', '', $oabNumber);
        return $oabNumber . '/' . strtoupper($oabState);
    }

    /**
     * Busca advogados por especialidade
     */
    public function getBySpecialty($specialty)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE specialties LIKE :specialty 
                AND status = 'active'
                ORDER BY display_order ASC";
        
        return $this->db->select($sql, ['specialty' => "%{$specialty}%"]);
    }

    /**
     * Atualiza ordem de exibição
     */
    public function updateDisplayOrder($lawyerId, $order)
    {
        return $this->update($lawyerId, ['display_order' => $order]);
    }

    /**
     * Conta casos ganhos total
     */
    public function getTotalCasesWon()
    {
        $sql = "SELECT SUM(cases_won) as total FROM {$this->table} WHERE status = 'active'";
        $result = $this->db->selectOne($sql);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Busca advogado com maior número de casos ganhos
     */
    public function getTopPerformer()
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'active' 
                ORDER BY cases_won DESC 
                LIMIT 1";
        
        return $this->db->selectOne($sql);
    }

    /**
     * Ativa advogado
     */
    public function activate($lawyerId)
    {
        return $this->update($lawyerId, ['status' => 'active']);
    }

    /**
     * Desativa advogado
     */
    public function deactivate($lawyerId)
    {
        return $this->update($lawyerId, ['status' => 'inactive']);
    }

    /**
     * Remove foto do advogado
     */
    public function removePhoto($lawyerId)
    {
        $lawyer = $this->find($lawyerId);
        
        if ($lawyer && !empty($lawyer['photo'])) {
            $photoPath = __DIR__ . '/../../public/images/advogados/' . $lawyer['photo'];
            
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }

        return $this->update($lawyerId, ['photo' => null]);
    }

    /**
     * Busca estatísticas dos advogados
     */
    public function getStatistics()
    {
        $sql = "SELECT 
                    COUNT(*) as total_lawyers,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_lawyers,
                    SUM(cases_won) as total_cases_won,
                    AVG(cases_won) as avg_cases_won
                FROM {$this->table}";
        
        return $this->db->selectOne($sql);
    }
}