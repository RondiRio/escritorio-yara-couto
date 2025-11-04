<?php

namespace Models;

use Core\Model;

/**
 * Model ActivityLog - Gerencia logs de atividades do sistema
 */
class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'entity_type',
        'entity_id',
        'ip_address',
        'user_agent'
    ];
    protected $timestamps = true;

    /**
     * Registra uma atividade
     */
    public function log($action, $description, $entityType = null, $entityId = null)
    {
        $data = [
            'user_id' => $_SESSION['user_id'] ?? null,
            'action' => $action,
            'description' => $description,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'ip_address' => $this->getIpAddress(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];

        return $this->create($data);
    }

    /**
     * Busca logs de um usuário
     */
    public function getByUser($userId, $limit = 50)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT {$limit}";
        
        return $this->db->select($sql, ['user_id' => $userId]);
    }

    /**
     * Busca logs de uma entidade
     */
    public function getByEntity($entityType, $entityId)
    {
        $sql = "SELECT al.*, u.name as user_name 
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.entity_type = :entity_type 
                AND al.entity_id = :entity_id
                ORDER BY al.created_at DESC";
        
        return $this->db->select($sql, [
            'entity_type' => $entityType,
            'entity_id' => $entityId
        ]);
    }

    /**
     * Busca logs recentes
     */
    public function getRecent($limit = 50)
    {
        $sql = "SELECT al.*, u.name as user_name, u.email as user_email
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                ORDER BY al.created_at DESC
                LIMIT {$limit}";
        
        return $this->db->select($sql);
    }

    /**
     * Busca logs por ação
     */
    public function getByAction($action, $limit = 50)
    {
        $sql = "SELECT al.*, u.name as user_name
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.action = :action
                ORDER BY al.created_at DESC
                LIMIT {$limit}";
        
        return $this->db->select($sql, ['action' => $action]);
    }

    /**
     * Busca logs de um período
     */
    public function getByDateRange($startDate, $endDate)
    {
        $sql = "SELECT al.*, u.name as user_name
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE DATE(al.created_at) BETWEEN :start_date AND :end_date
                ORDER BY al.created_at DESC";
        
        return $this->db->select($sql, [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }

    /**
     * Busca estatísticas de atividades
     */
    public function getStatistics($days = 30)
    {
        $sql = "SELECT 
                    action,
                    COUNT(*) as count
                FROM {$this->table}
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
                GROUP BY action
                ORDER BY count DESC";
        
        return $this->db->select($sql);
    }

    /**
     * Busca atividades por IP
     */
    public function getByIp($ipAddress, $limit = 50)
    {
        $sql = "SELECT al.*, u.name as user_name
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.ip_address = :ip_address
                ORDER BY al.created_at DESC
                LIMIT {$limit}";
        
        return $this->db->select($sql, ['ip_address' => $ipAddress]);
    }

    /**
     * Limpa logs antigos
     */
    public function cleanOldLogs($days = 90)
    {
        $sql = "DELETE FROM {$this->table} 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL {$days} DAY)";
        
        return $this->db->delete($sql);
    }

    /**
     * Obtém endereço IP do usuário
     */
    private function getIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? null;
        }
    }

    /**
     * Registra login
     */
    public function logLogin($userId, $success = true)
    {
        $action = $success ? 'login_success' : 'login_failed';
        $description = $success ? 'Usuário fez login no sistema' : 'Tentativa de login falhou';
        
        return $this->log($action, $description, 'user', $userId);
    }

    /**
     * Registra logout
     */
    public function logLogout($userId)
    {
        return $this->log('logout', 'Usuário fez logout do sistema', 'user', $userId);
    }

    /**
     * Registra criação
     */
    public function logCreate($entityType, $entityId, $description)
    {
        return $this->log('create', $description, $entityType, $entityId);
    }

    /**
     * Registra atualização
     */
    public function logUpdate($entityType, $entityId, $description)
    {
        return $this->log('update', $description, $entityType, $entityId);
    }

    /**
     * Registra exclusão
     */
    public function logDelete($entityType, $entityId, $description)
    {
        return $this->log('delete', $description, $entityType, $entityId);
    }

    /**
     * Conta ações por usuário
     */
    public function getUserActivityCount($userId, $days = 30)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table}
                WHERE user_id = :user_id
                AND created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)";
        
        $result = $this->db->selectOne($sql, ['user_id' => $userId]);
        return $result ? (int)$result['total'] : 0;
    }
}