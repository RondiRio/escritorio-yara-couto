<?php

namespace Models;

use Core\Model;

/**
 * Model Appointment - Gerencia agendamentos
 */
class Appointment extends Model
{
    protected $table = 'appointments';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'whatsapp',
        'preferred_date',
        'preferred_time',
        'consultation_type',
        'message',
        'status',
        'admin_notes',
        'confirmed_at',
        'completed_at',
        'cancelled_at'
    ];
    protected $timestamps = true;

    /**
     * Busca agendamentos por status
     */
    public function getByStatus($status, $orderBy = 'created_at DESC')
    {
        return $this->where('status', '=', $status);
    }

    /**
     * Busca agendamentos pendentes
     */
    public function getPending($limit = null)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'pending' 
                ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->db->select($sql);
    }

    /**
     * Busca agendamentos de hoje
     */
    public function getToday()
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE DATE(preferred_date) = CURDATE() 
                AND status IN ('pending', 'confirmed')
                ORDER BY preferred_time ASC";
        
        return $this->db->select($sql);
    }

    /**
     * Busca agendamentos futuros
     */
    public function getUpcoming($limit = 10)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE preferred_date >= CURDATE() 
                AND status IN ('pending', 'confirmed')
                ORDER BY preferred_date ASC, preferred_time ASC
                LIMIT {$limit}";
        
        return $this->db->select($sql);
    }

    /**
     * Busca agendamentos de um período
     */
    public function getByDateRange($startDate, $endDate)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE preferred_date BETWEEN :start_date AND :end_date
                ORDER BY preferred_date ASC, preferred_time ASC";
        
        return $this->db->select($sql, [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }

    /**
     * Confirma agendamento
     */
    public function confirm($appointmentId, $adminNotes = null)
    {
        $data = [
            'status' => 'confirmed',
            'confirmed_at' => date('Y-m-d H:i:s')
        ];

        if ($adminNotes) {
            $data['admin_notes'] = $adminNotes;
        }

        return $this->update($appointmentId, $data);
    }

    /**
     * Completa agendamento
     */
    public function complete($appointmentId, $adminNotes = null)
    {
        $data = [
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s')
        ];

        if ($adminNotes) {
            $data['admin_notes'] = $adminNotes;
        }

        return $this->update($appointmentId, $data);
    }

    /**
     * Cancela agendamento
     */
    public function cancel($appointmentId, $reason = null)
    {
        $data = [
            'status' => 'cancelled',
            'cancelled_at' => date('Y-m-d H:i:s')
        ];

        if ($reason) {
            $data['admin_notes'] = $reason;
        }

        return $this->update($appointmentId, $data);
    }

    /**
     * Adiciona notas administrativas
     */
    public function addNotes($appointmentId, $notes)
    {
        return $this->update($appointmentId, ['admin_notes' => $notes]);
    }

    /**
     * Conta agendamentos por status
     */
    public function countByStatus($status)
    {
        return $this->count("status = :status", ['status' => $status]);
    }

    /**
     * Busca estatísticas de agendamentos
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                FROM {$this->table}";
        
        $params = [];

        if ($startDate && $endDate) {
            $sql .= " WHERE created_at BETWEEN :start_date AND :end_date";
            $params = [
                'start_date' => $startDate . ' 00:00:00',
                'end_date' => $endDate . ' 23:59:59'
            ];
        }

        return $this->db->selectOne($sql, $params);
    }

    /**
     * Busca agendamentos recentes
     */
    public function getRecent($limit = 10)
    {
        return $this->latest($limit);
    }

    /**
     * Verifica disponibilidade de horário
     */
    public function isTimeAvailable($date, $time, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE preferred_date = :date 
                AND preferred_time = :time
                AND status IN ('pending', 'confirmed')";
        
        $params = [
            'date' => $date,
            'time' => $time
        ];

        if ($excludeId) {
            $sql .= " AND {$this->primaryKey} != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $result = $this->db->selectOne($sql, $params);
        return $result && $result['total'] == 0;
    }

    /**
     * Busca horários disponíveis de um dia
     */
    public function getAvailableTimesForDate($date)
    {
        // Horários de trabalho (08:00 às 18:00, intervalos de 1 hora)
        $workHours = [
            '08:00', '09:00', '10:00', '11:00',
            '14:00', '15:00', '16:00', '17:00', '18:00'
        ];

        // Busca horários ocupados
        $sql = "SELECT preferred_time FROM {$this->table} 
                WHERE preferred_date = :date 
                AND status IN ('pending', 'confirmed')";
        
        $busyTimes = $this->db->select($sql, ['date' => $date]);
        $busyTimesArray = array_column($busyTimes, 'preferred_time');

        // Retorna apenas horários livres
        return array_values(array_diff($workHours, $busyTimesArray));
    }

    /**
     * Conta agendamentos de hoje
     */
    public function getTodayCount()
    {
        return $this->count("DATE(preferred_date) = CURDATE() AND status IN ('pending', 'confirmed')");
    }

    /**
     * Busca por email ou telefone
     */
    public function findByContact($email = null, $phone = null)
    {
        if ($email) {
            return $this->where('email', '=', $email);
        }

        if ($phone) {
            return $this->where('phone', '=', $phone);
        }

        return [];
    }
}