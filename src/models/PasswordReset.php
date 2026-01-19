<?php

namespace Models;

use Core\Database;

/**
 * Model PasswordReset
 * Gerencia tokens de recuperação de senha
 */
class PasswordReset
{
    private $db;
    private $table = 'password_resets';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Cria um novo token de recuperação
     *
     * @param string $email
     * @return string Token gerado
     */
    public function createToken($email)
    {
        // Gera token aleatório
        $token = bin2hex(random_bytes(32));

        // Define expiração (1 hora)
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Remove tokens anteriores não usados deste email
        $this->deleteUnusedTokensByEmail($email);

        // Insere novo token
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (email, token, expires_at)
            VALUES (:email, :token, :expires_at)
        ");

        $stmt->execute([
            'email' => $email,
            'token' => hash('sha256', $token), // Armazena hash do token
            'expires_at' => $expiresAt
        ]);

        // Retorna token em texto plano (será enviado por email)
        return $token;
    }

    /**
     * Valida um token de recuperação
     *
     * @param string $token
     * @return array|false Dados do token se válido, false se inválido
     */
    public function validateToken($token)
    {
        $hashedToken = hash('sha256', $token);

        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE token = :token
            AND expires_at > NOW()
            AND used_at IS NULL
            LIMIT 1
        ");

        $stmt->execute(['token' => $hashedToken]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ?: false;
    }

    /**
     * Marca um token como usado
     *
     * @param string $token
     * @return bool
     */
    public function markAsUsed($token)
    {
        $hashedToken = hash('sha256', $token);

        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET used_at = NOW()
            WHERE token = :token
        ");

        return $stmt->execute(['token' => $hashedToken]);
    }

    /**
     * Remove tokens não usados de um email
     *
     * @param string $email
     * @return bool
     */
    public function deleteUnusedTokensByEmail($email)
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table}
            WHERE email = :email
            AND used_at IS NULL
        ");

        return $stmt->execute(['email' => $email]);
    }

    /**
     * Limpa tokens expirados e usados antigos
     *
     * @return bool
     */
    public function cleanExpiredTokens()
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table}
            WHERE expires_at < NOW()
            OR (used_at IS NOT NULL AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY))
        ");

        return $stmt->execute();
    }

    /**
     * Verifica se existe um token válido para o email
     *
     * @param string $email
     * @return bool
     */
    public function hasValidToken($email)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM {$this->table}
            WHERE email = :email
            AND expires_at > NOW()
            AND used_at IS NULL
        ");

        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result['count'] > 0;
    }

    /**
     * Obtém tempo restante até poder solicitar novo token
     *
     * @param string $email
     * @return int Segundos restantes, 0 se pode solicitar
     */
    public function getTimeUntilNextRequest($email)
    {
        $stmt = $this->db->prepare("
            SELECT TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(created_at, INTERVAL 5 MINUTE)) as seconds
            FROM {$this->table}
            WHERE email = :email
            AND used_at IS NULL
            ORDER BY created_at DESC
            LIMIT 1
        ");

        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result && $result['seconds'] > 0) {
            return $result['seconds'];
        }

        return 0;
    }
}
