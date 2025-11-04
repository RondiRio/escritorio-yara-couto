<?php

namespace Models;

use Core\Model;

/**
 * Model Category - Gerencia categorias de posts
 */
class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id'
    ];
    protected $timestamps = true;

    /**
     * Busca todas as categorias com contagem de posts
     */
    public function getAllWithPostCount()
    {
        $sql = "SELECT c.*, COUNT(p.id) as post_count 
                FROM {$this->table} c
                LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published'
                GROUP BY c.id
                ORDER BY c.name ASC";
        
        return $this->db->select($sql);
    }

    /**
     * Busca categoria por slug
     */
    public function findBySlug($slug)
    {
        return $this->whereOne('slug', '=', $slug);
    }

    /**
     * Gera slug único
     */
    public function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = $this->createSlug($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Cria slug a partir do nome
     */
    private function createSlug($text)
    {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
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
     * Busca categorias principais (sem parent)
     */
    public function getMainCategories()
    {
        $sql = "SELECT * FROM {$this->table} WHERE parent_id IS NULL ORDER BY name ASC";
        return $this->db->select($sql);
    }

    /**
     * Busca subcategorias de uma categoria
     */
    public function getSubcategories($parentId)
    {
        return $this->where('parent_id', '=', $parentId);
    }

    /**
     * Conta posts em uma categoria
     */
    public function getPostCount($categoryId)
    {
        $sql = "SELECT COUNT(*) as total FROM posts WHERE category_id = :id AND status = 'published'";
        $result = $this->db->selectOne($sql, ['id' => $categoryId]);
        return $result ? (int)$result['total'] : 0;
    }
}