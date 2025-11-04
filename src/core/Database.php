<?php

namespace Core;

use PDO;
use PDOException;

/**
 * Classe Database - Gerencia conexão com banco de dados
 * Padrão Singleton para garantir única instância
 */
class Database
{
    private static $instance = null;
    private $connection;
    
    private $host;
    private $database;
    private $username;
    private $password;
    private $charset = 'utf8mb4';

    /**
     * Construtor privado (Singleton)
     */
    private function __construct()
    {
        $this->host = getenv('DB_HOST') ?: '127.0.0.1';
        $this->database = getenv('DB_DATABASE') ?: 'escritorio_yara';
        $this->username = getenv('DB_USERNAME') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';

        $this->connect();
    }

    /**
     * Obtém instância única da classe (Singleton)
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Estabelece conexão com banco de dados
     */
    private function connect()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ];

            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $e) {
            $this->logError($e->getMessage());
            die("Erro de conexão com o banco de dados. Verifique as configurações.");
        }
    }

    /**
     * Retorna a conexão PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Executa query SELECT
     */
    public function select($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->logError($e->getMessage());
            return false;
        }
    }

    /**
     * Executa query SELECT retornando única linha
     */
    public function selectOne($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            $this->logError($e->getMessage());
            return false;
        }
    }

    /**
     * Executa query INSERT
     */
    public function insert($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $this->connection->lastInsertId();
        } catch (PDOException $e) {
            $this->logError($e->getMessage());
            return false;
        }
    }

    /**
     * Executa query UPDATE
     */
    public function update($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->logError($e->getMessage());
            return false;
        }
    }

    /**
     * Executa query DELETE
     */
    public function delete($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->logError($e->getMessage());
            return false;
        }
    }

    /**
     * Inicia transação
     */
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Confirma transação
     */
    public function commit()
    {
        return $this->connection->commit();
    }

    /**
     * Reverte transação
     */
    public function rollback()
    {
        return $this->connection->rollback();
    }

    /**
     * Registra erros no log
     */
    private function logError($message)
    {
        $logFile = __DIR__ . '/../../storage/logs/database.log';
        $logMessage = date('Y-m-d H:i:s') . " - " . $message . PHP_EOL;
        
        // Cria diretório se não existir
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    /**
     * Previne clonagem (Singleton)
     */
    private function __clone() {}

    /**
     * Previne deserialização (Singleton)
     */
    public function __wakeup()
    {
        throw new \Exception("Não é possível deserializar singleton");
    }
}