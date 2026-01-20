<?php

namespace Controllers\Admin;

use Core\Controller;
use Models\Tag;
use Models\ActivityLog;

/**
 * TagController - Gerencia tags de posts (Admin)
 */
class TagController extends Controller
{
    protected $middlewares = ['AuthMiddleware', ['RoleMiddleware', 'admin,editor']];

    private $tagModel;
    private $activityLogModel;

    public function __construct()
    {
        $this->tagModel = new Tag();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * Lista tags
     */
    public function index()
    {
        // Busca todas as tags com contagem de posts
        $tags = $this->tagModel->getAllWithPostCount();

        // Estatísticas
        $stats = [
            'total' => count($tags),
            'used' => count(array_filter($tags, fn($t) => $t['post_count'] > 0)),
            'unused' => count(array_filter($tags, fn($t) => $t['post_count'] == 0)),
            'total_posts' => array_sum(array_column($tags, 'post_count'))
        ];

        // Tags mais usadas
        $mostUsed = $this->tagModel->getMostUsed(5);

        $this->view('admin/tags/index', [
            'pageTitle' => 'Gerenciar Tags',
            'tags' => $tags,
            'stats' => $stats,
            'mostUsed' => $mostUsed
        ]);
    }

    /**
     * Cria nova tag (AJAX ou POST)
     */
    public function store()
    {
        if (!$this->isPost()) {
            return $this->json(['error' => 'Método não permitido'], 405);
        }

        $name = trim($this->post('name'));

        if (empty($name)) {
            return $this->json(['error' => 'Nome da tag é obrigatório'], 400);
        }

        // Verifica se já existe
        $existing = $this->tagModel->whereOne('name', '=', $name);
        if ($existing) {
            return $this->json(['error' => 'Esta tag já existe'], 400);
        }

        // Gera slug único
        $slug = $this->tagModel->generateUniqueSlug($name);

        // Cria tag
        $tagId = $this->tagModel->create([
            'name' => $name,
            'slug' => $slug
        ]);

        if ($tagId) {
            // Log
            $this->activityLogModel->create([
                'user_id' => auth_id(),
                'action' => 'tag_created',
                'entity_type' => 'tag',
                'entity_id' => $tagId,
                'description' => "Tag criada: {$name}",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            // Busca tag criada
            $tag = $this->tagModel->find($tagId);

            return $this->json([
                'success' => true,
                'message' => 'Tag criada com sucesso!',
                'tag' => $tag
            ]);
        }

        return $this->json(['error' => 'Erro ao criar tag'], 500);
    }

    /**
     * Atualiza tag (AJAX)
     */
    public function update($id)
    {
        if (!$this->isPost()) {
            return $this->json(['error' => 'Método não permitido'], 405);
        }

        $tag = $this->tagModel->find($id);

        if (!$tag) {
            return $this->json(['error' => 'Tag não encontrada'], 404);
        }

        $name = trim($this->post('name'));

        if (empty($name)) {
            return $this->json(['error' => 'Nome da tag é obrigatório'], 400);
        }

        // Verifica se já existe outra tag com esse nome
        $existing = $this->tagModel->whereOne('name', '=', $name);
        if ($existing && $existing['id'] != $id) {
            return $this->json(['error' => 'Já existe outra tag com este nome'], 400);
        }

        // Se mudou o nome, gera novo slug
        $data = ['name' => $name];
        if ($name !== $tag['name']) {
            $data['slug'] = $this->tagModel->generateUniqueSlug($name, $id);
        }

        // Atualiza
        if ($this->tagModel->update($id, $data)) {
            // Log
            $this->activityLogModel->create([
                'user_id' => auth_id(),
                'action' => 'tag_updated',
                'entity_type' => 'tag',
                'entity_id' => $id,
                'description' => "Tag atualizada: {$name}",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            // Busca tag atualizada
            $tag = $this->tagModel->find($id);

            return $this->json([
                'success' => true,
                'message' => 'Tag atualizada com sucesso!',
                'tag' => $tag
            ]);
        }

        return $this->json(['error' => 'Erro ao atualizar tag'], 500);
    }

    /**
     * Deleta tag (AJAX ou POST)
     */
    public function delete($id)
    {
        if (!$this->isPost()) {
            return $this->json(['error' => 'Método não permitido'], 405);
        }

        $tag = $this->tagModel->find($id);

        if (!$tag) {
            return $this->json(['error' => 'Tag não encontrada'], 404);
        }

        // Remove associações com posts antes de deletar
        $this->tagModel->detachFromPost($id);

        // Deleta tag
        if ($this->tagModel->delete($id)) {
            // Log
            $this->activityLogModel->create([
                'user_id' => auth_id(),
                'action' => 'tag_deleted',
                'entity_type' => 'tag',
                'entity_id' => $id,
                'description' => "Tag deletada: {$tag['name']}",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            return $this->json([
                'success' => true,
                'message' => 'Tag deletada com sucesso!'
            ]);
        }

        return $this->json(['error' => 'Erro ao deletar tag'], 500);
    }

    /**
     * Busca tags (AJAX - para autocomplete)
     */
    public function search()
    {
        $query = $this->get('q', '');

        if (empty($query)) {
            return $this->json([]);
        }

        // Busca tags que começam com a query
        $sql = "SELECT * FROM tags WHERE name LIKE :query ORDER BY name ASC LIMIT 10";
        $tags = $this->tagModel->db->select($sql, ['query' => $query . '%']);

        return $this->json($tags);
    }

    /**
     * Limpa tags não usadas (AJAX)
     */
    public function cleanUnused()
    {
        if (!$this->isPost()) {
            return $this->json(['error' => 'Método não permitido'], 405);
        }

        // Busca tags não usadas
        $sql = "SELECT t.id, t.name
                FROM tags t
                LEFT JOIN post_tags pt ON t.id = pt.tag_id
                WHERE pt.tag_id IS NULL";

        $unusedTags = $this->tagModel->db->select($sql);

        if (empty($unusedTags)) {
            return $this->json([
                'success' => true,
                'message' => 'Não há tags não utilizadas',
                'count' => 0
            ]);
        }

        // Deleta tags não usadas
        $count = 0;
        foreach ($unusedTags as $tag) {
            if ($this->tagModel->delete($tag['id'])) {
                $count++;
            }
        }

        // Log
        $this->activityLogModel->create([
            'user_id' => auth_id(),
            'action' => 'tags_cleaned',
            'entity_type' => 'tag',
            'entity_id' => null,
            'description' => "Limpeza de tags: {$count} tag(s) não utilizada(s) removida(s)",
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        return $this->json([
            'success' => true,
            'message' => "{$count} tag(s) não utilizada(s) removida(s)",
            'count' => $count
        ]);
    }

    /**
     * Mescla tags duplicadas (AJAX)
     */
    public function merge()
    {
        if (!$this->isPost()) {
            return $this->json(['error' => 'Método não permitido'], 405);
        }

        $sourceId = $this->post('source_id');
        $targetId = $this->post('target_id');

        if (empty($sourceId) || empty($targetId)) {
            return $this->json(['error' => 'IDs das tags são obrigatórios'], 400);
        }

        if ($sourceId == $targetId) {
            return $this->json(['error' => 'Não é possível mesclar uma tag com ela mesma'], 400);
        }

        $sourceTag = $this->tagModel->find($sourceId);
        $targetTag = $this->tagModel->find($targetId);

        if (!$sourceTag || !$targetTag) {
            return $this->json(['error' => 'Uma ou ambas as tags não foram encontradas'], 404);
        }

        // Move todas as associações da tag source para target
        $sql = "UPDATE post_tags SET tag_id = :target_id WHERE tag_id = :source_id";
        $this->tagModel->db->update($sql, [
            'target_id' => $targetId,
            'source_id' => $sourceId
        ]);

        // Deleta tag source
        $this->tagModel->delete($sourceId);

        // Log
        $this->activityLogModel->create([
            'user_id' => auth_id(),
            'action' => 'tags_merged',
            'entity_type' => 'tag',
            'entity_id' => $targetId,
            'description' => "Tags mescladas: '{$sourceTag['name']}' → '{$targetTag['name']}'",
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        return $this->json([
            'success' => true,
            'message' => "Tags mescladas com sucesso: '{$sourceTag['name']}' → '{$targetTag['name']}'"
        ]);
    }
}
