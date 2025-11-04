<?php

namespace Core;

use Core\Database;

/**
 * Classe Model - Base para todos os models
 * Implementa operações CRUD básicas
 */
abstract class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $timestamps = true;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Busca todos os registros
     */
    public function all($orderBy = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        return $this->db->select($sql);
    }

    /**
     * Busca registro por ID
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        return $this->db->selectOne($sql, ['id' => $id]);
    }

    /**
     * Busca registros com condições
     */
    public function where($column, $operator, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} :value";
        return $this->db->select($sql, ['value' => $value]);
    }

    /**
     * Busca um único registro com condição
     */
    public function whereOne($column, $operator, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} :value LIMIT 1";
        return $this->db->selectOne($sql, ['value' => $value]);
    }

    /**
     * Cria novo registro
     */
    public function create($data)
    {
        // Filtra apenas campos permitidos
        $data = $this->filterFillable($data);

        // Adiciona timestamps se habilitado
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        // Monta SQL dinamicamente
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        return $this->db->insert($sql, $data);
    }

    /**
     * Atualiza registro
     */
    public function update($id, $data)
    {
        // Filtra apenas campos permitidos
        $data = $this->filterFillable($data);

        // Atualiza timestamp se habilitado
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        // Monta SQL dinamicamente
        $setClause = [];
        foreach (array_keys($data) as $column) {
            $setClause[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $setClause);

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id";
        $data['id'] = $id;

        return $this->db->update($sql, $data);
    }

    /**
     * Deleta registro
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->delete($sql, ['id' => $id]);
    }

    /**
     * Conta registros
     */
    public function count($where = null, $params = [])
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        
        if ($where) {
            $sql .= " WHERE {$where}";
        }

        $result = $this->db->selectOne($sql, $params);
        return $result ? (int)$result['total'] : 0;
    }

    /**
     * Busca com paginação
     */
    public function paginate($page = 1, $perPage = 10, $orderBy = null)
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";

        return [
            'data' => $this->db->select($sql),
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $this->count(),
            'total_pages' => ceil($this->count() / $perPage)
        ];
    }

    /**
     * Executa query personalizada
     */
    public function query($sql, $params = [])
    {
        return $this->db->select($sql, $params);
    }

    /**
     * Filtra apenas campos permitidos (fillable)
     */
    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Verifica se registro existe
     */
    public function exists($column, $value, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$column} = :value";
        $params = ['value' => $value];

        if ($excludeId) {
            $sql .= " AND {$this->primaryKey} != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $result = $this->db->selectOne($sql, $params);
        return $result && $result['total'] > 0;
    }

    /**
     * Busca últimos registros
     */
    public function latest($limit = 10)
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$this->primaryKey} DESC LIMIT {$limit}";
        return $this->db->select($sql);
    }
}