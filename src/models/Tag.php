<?php

namespace Models;

use Core\Model;

/**
 * Model Tag - Gerencia tags de posts
 */
class Tag extends Model
{
    protected $table = 'tags';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'slug'
    ];
    protected $timestamps = true;

    /**
     * Busca tags de um post
     */
    public function getPostTags($postId)
    {
        $sql = "SELECT t.* 
                FROM {$this->table} t
                INNER JOIN post_tags pt ON t.id = pt.tag_id
                WHERE pt.post_id = :post_id
                ORDER BY t.name ASC";
        
        return $this->db->select($sql, ['post_id' => $postId]);
    }

    /**
     * Associa tags a um post
     */
    public function attachToPost($postId, $tagIds)
    {
        // Remove tags antigas
        $this->detachFromPost($postId);

        // Adiciona novas tags
        if (!empty($tagIds) && is_array($tagIds)) {
            foreach ($tagIds as $tagId) {
                $sql = "INSERT INTO post_tags (post_id, tag_id, created_at) VALUES (:post_id, :tag_id, NOW())";
                $this->db->insert($sql, [
                    'post_id' => $postId,
                    'tag_id' => $tagId
                ]);
            }
        }

        return true;
    }

    /**
     * Remove tags de um post
     */
    public function detachFromPost($postId)
    {
        $sql = "DELETE FROM post_tags WHERE post_id = :post_id";
        return $this->db->delete($sql, ['post_id' => $postId]);
    }

    /**
     * Busca tag por slug
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
     * Busca tags com contagem de posts
     */
    public function getAllWithPostCount()
    {
        $sql = "SELECT t.*, COUNT(pt.post_id) as post_count 
                FROM {$this->table} t
                LEFT JOIN post_tags pt ON t.id = pt.tag_id
                LEFT JOIN posts p ON pt.post_id = p.id AND p.status = 'published'
                GROUP BY t.id
                ORDER BY t.name ASC";
        
        return $this->db->select($sql);
    }

    /**
     * Busca tags mais usadas
     */
    public function getMostUsed($limit = 10)
    {
        $sql = "SELECT t.*, COUNT(pt.post_id) as post_count 
                FROM {$this->table} t
                INNER JOIN post_tags pt ON t.id = pt.tag_id
                INNER JOIN posts p ON pt.post_id = p.id AND p.status = 'published'
                GROUP BY t.id
                ORDER BY post_count DESC
                LIMIT {$limit}";
        
        return $this->db->select($sql);
    }

    /**
     * Cria ou retorna tag existente
     */
    public function findOrCreate($name)
    {
        // Busca tag existente
        $tag = $this->whereOne('name', '=', $name);

        if ($tag) {
            return $tag['id'];
        }

        // Cria nova tag
        $slug = $this->generateUniqueSlug($name);
        return $this->create([
            'name' => $name,
            'slug' => $slug
        ]);
    }
}