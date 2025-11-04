<?php

namespace Models;

use Core\Model;

/**
 * Model User - Gerencia usuários administradores
 */
class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'last_login'
    ];
    protected $timestamps = true;

    /**
     * Cria novo usuário com senha hash
     */
    public function createUser($data)
    {
        // Hash da senha
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        // Define role padrão
        if (!isset($data['role'])) {
            $data['role'] = 'admin';
        }

        // Define status padrão
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }

        return $this->create($data);
    }

    /**
     * Busca usuário por email
     */
    public function findByEmail($email)
    {
        return $this->whereOne('email', '=', $email);
    }

    /**
     * Verifica credenciais de login
     */
    public function verifyCredentials($email, $password)
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            return false;
        }

        // Verifica se usuário está ativo
        if ($user['status'] !== 'active') {
            return false;
        }

        // Verifica senha
        if (password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    /**
     * Atualiza último login
     */
    public function updateLastLogin($userId)
    {
        $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE {$this->primaryKey} = :id";
        return $this->db->update($sql, ['id' => $userId]);
    }

    /**
     * Altera senha do usuário
     */
    public function changePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $sql = "UPDATE {$this->table} SET password = :password, updated_at = NOW() WHERE {$this->primaryKey} = :id";
        
        return $this->db->update($sql, [
            'password' => $hashedPassword,
            'id' => $userId
        ]);
    }

    /**
     * Verifica se email já existe
     */
    public function emailExists($email, $excludeId = null)
    {
        return $this->exists('email', $email, $excludeId);
    }

    /**
     * Busca usuários ativos
     */
    public function getActiveUsers()
    {
        return $this->where('status', '=', 'active');
    }

    /**
     * Desativa usuário (soft delete)
     */
    public function deactivate($userId)
    {
        return $this->update($userId, ['status' => 'inactive']);
    }

    /**
     * Ativa usuário
     */
    public function activate($userId)
    {
        return $this->update($userId, ['status' => 'active']);
    }

    /**
     * Conta usuários por role
     */
    public function countByRole($role)
    {
        return $this->count("role = :role AND status = 'active'", ['role' => $role]);
    }
}