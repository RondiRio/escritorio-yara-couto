<?php

namespace Models;

use Core\Model;

/**
 * Model Post - Gerencia posts do blog
 */
class Post extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'category_id',
        'author_id',
        'status',
        'published_at',
        'views'
    ];
    protected $timestamps = true;

    /**
     * Busca posts publicados
     */
    public function getPublished($limit = null)
    {
        $sql = "SELECT p.*, c.name as category_name, u.name as author_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users u ON p.author_id = u.id
                WHERE p.status = 'published' 
                AND (p.published_at IS NULL OR p.published_at <= NOW())
                ORDER BY p.published_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->db->select($sql);
    }

    /**
     * Busca post por slug
     */
    public function findBySlug($slug)
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, u.name as author_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users u ON p.author_id = u.id
                WHERE p.slug = :slug 
                AND p.status = 'published'
                LIMIT 1";
        
        return $this->db->selectOne($sql, ['slug' => $slug]);
    }

    /**
     * Busca posts por categoria
     */
    public function getByCategory($categoryId, $limit = null)
    {
        $sql = "SELECT p.*, c.name as category_name, u.name as author_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users u ON p.author_id = u.id
                WHERE p.category_id = :category_id 
                AND p.status = 'published'
                ORDER BY p.published_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->db->select($sql, ['category_id' => $categoryId]);
    }

    /**
     * Busca posts por tag
     */
    public function getByTag($tagId, $limit = null)
    {
        $sql = "SELECT p.*, c.name as category_name, u.name as author_name 
                FROM {$this->table} p
                INNER JOIN post_tags pt ON p.id = pt.post_id
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users u ON p.author_id = u.id
                WHERE pt.tag_id = :tag_id 
                AND p.status = 'published'
                ORDER BY p.published_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->db->select($sql, ['tag_id' => $tagId]);
    }

    /**
     * Busca posts relacionados
     */
    public function getRelated($postId, $categoryId, $limit = 4)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.category_id = :category_id 
                AND p.id != :post_id
                AND p.status = 'published'
                ORDER BY p.published_at DESC
                LIMIT {$limit}";
        
        return $this->db->select($sql, [
            'category_id' => $categoryId,
            'post_id' => $postId
        ]);
    }

    /**
     * Busca posts mais visualizados
     */
    public function getMostViewed($limit = 5)
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published'
                ORDER BY p.views DESC
                LIMIT {$limit}";
        
        return $this->db->select($sql);
    }

    /**
     * Incrementa visualizações
     */
    public function incrementViews($postId)
    {
        $sql = "UPDATE {$this->table} SET views = views + 1 WHERE id = :id";
        return $this->db->update($sql, ['id' => $postId]);
    }

    /**
     * Gera slug único
     */
    public function generateUniqueSlug($title, $excludeId = null)
    {
        $slug = $this->createSlug($title);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Cria slug a partir do título
     */
    private function createSlug($text)
    {
        // Remove acentos
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        
        // Converte para minúsculas
        $text = strtolower($text);
        
        // Remove caracteres especiais
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        
        // Substitui espaços por hífens
        $text = preg_replace('/[\s-]+/', '-', $text);
        
        // Remove hífens do início e fim
        $text = trim($text, '-');
        
        return $text;
    }

    /**
     * Verifica se slug existe
     */
    public function slugExists($slug, $excludeId = null)
    {
        return $this->exists('slug', $slug, $excludeId);
    }

    /**
     * Busca com paginação (override para incluir joins)
     */
    public function paginatePublished($page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT p.*, c.name as category_name, u.name as author_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users u ON p.author_id = u.id
                WHERE p.status = 'published'
                ORDER BY p.published_at DESC
                LIMIT {$perPage} OFFSET {$offset}";

        $total = $this->count("status = 'published'");

        return [
            'data' => $this->db->select($sql),
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Busca posts (admin - todos os status)
     */
    public function getAllForAdmin($orderBy = 'created_at DESC')
    {
        $sql = "SELECT p.*, c.name as category_name, u.name as author_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users u ON p.author_id = u.id
                ORDER BY {$orderBy}";
        
        return $this->db->select($sql);
    }

    /**
     * Busca por termo de pesquisa
     */
    public function search($term, $limit = null)
    {
        $sql = "SELECT p.*, c.name as category_name, u.name as author_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users u ON p.author_id = u.id
                WHERE (p.title LIKE :term OR p.content LIKE :term OR p.excerpt LIKE :term)
                AND p.status = 'published'
                ORDER BY p.published_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        return $this->db->select($sql, ['term' => "%{$term}%"]);
    }

    /**
     * Conta posts por status
     */
    public function countByStatus($status)
    {
        return $this->count("status = :status", ['status' => $status]);
    }
}