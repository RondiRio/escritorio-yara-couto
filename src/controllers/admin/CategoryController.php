<?php

namespace Controllers\Admin;

use Core\Controller;
use Models\Category;
use Models\ActivityLog;

/**
 * CategoryController - Gerencia categorias de posts (Admin)
 */
class CategoryController extends Controller
{
    protected $middlewares = ['AuthMiddleware', ['RoleMiddleware', 'admin,editor']];

    private $categoryModel;
    private $activityLogModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * Lista categorias
     */
    public function index()
    {
        // Busca todas as categorias com contagem de posts
        $categories = $this->categoryModel->getAllWithPostCount();

        // Estatísticas
        $stats = [
            'total' => count($categories),
            'main_categories' => count(array_filter($categories, fn($c) => $c['parent_id'] === null)),
            'subcategories' => count(array_filter($categories, fn($c) => $c['parent_id'] !== null)),
            'total_posts' => array_sum(array_column($categories, 'post_count'))
        ];

        $this->view('admin/categories/index', [
            'pageTitle' => 'Gerenciar Categorias',
            'categories' => $categories,
            'stats' => $stats
        ]);
    }

    /**
     * Exibe formulário de criar categoria
     */
    public function create()
    {
        // Busca categorias principais para dropdown
        $parentCategories = $this->categoryModel->getMainCategories();

        $this->view('admin/categories/create', [
            'pageTitle' => 'Nova Categoria',
            'parentCategories' => $parentCategories
        ]);
    }

    /**
     * Cria nova categoria
     */
    public function store()
    {
        if (!$this->isPost()) {
            redirect('/admin/categorias');
        }

        // Obtém dados
        $data = [
            'name' => $this->post('name'),
            'description' => $this->post('description'),
            'parent_id' => $this->post('parent_id') ?: null
        ];

        // Validações
        $errors = $this->validate($data, [
            'name' => 'required|min:3|max:100'
        ]);

        if (!empty($errors)) {
            flash('error', 'Corrija os erros abaixo');
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            redirect('/admin/categorias/criar');
        }

        // Gera slug único
        $data['slug'] = $this->categoryModel->generateUniqueSlug($data['name']);

        // Cria categoria
        $categoryId = $this->categoryModel->create($data);

        if ($categoryId) {
            // Log
            $this->activityLogModel->create([
                'user_id' => auth_id(),
                'action' => 'category_created',
                'entity_type' => 'category',
                'entity_id' => $categoryId,
                'description' => "Categoria criada: {$data['name']}",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            flash('success', 'Categoria criada com sucesso!');
            redirect('/admin/categorias');
        } else {
            flash('error', 'Erro ao criar categoria');
            redirect('/admin/categorias/criar');
        }
    }

    /**
     * Exibe formulário de editar categoria
     */
    public function edit($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            flash('error', 'Categoria não encontrada');
            redirect('/admin/categorias');
        }

        // Busca categorias principais (exceto a atual e suas filhas)
        $parentCategories = $this->categoryModel->getMainCategories();

        $this->view('admin/categories/edit', [
            'pageTitle' => 'Editar Categoria',
            'category' => $category,
            'parentCategories' => $parentCategories
        ]);
    }

    /**
     * Atualiza categoria
     */
    public function update($id)
    {
        if (!$this->isPost()) {
            redirect('/admin/categorias');
        }

        $category = $this->categoryModel->find($id);

        if (!$category) {
            flash('error', 'Categoria não encontrada');
            redirect('/admin/categorias');
        }

        // Obtém dados
        $data = [
            'name' => $this->post('name'),
            'description' => $this->post('description'),
            'parent_id' => $this->post('parent_id') ?: null
        ];

        // Validações
        $errors = $this->validate($data, [
            'name' => 'required|min:3|max:100'
        ]);

        // Valida parent_id (não pode ser filho dela mesma)
        if ($data['parent_id'] == $id) {
            $errors['parent_id'][] = 'Uma categoria não pode ser filha dela mesma';
        }

        if (!empty($errors)) {
            flash('error', 'Corrija os erros abaixo');
            $_SESSION['errors'] = $errors;
            redirect('/admin/categorias/' . $id . '/editar');
        }

        // Se mudou o nome, gera novo slug
        if ($data['name'] !== $category['name']) {
            $data['slug'] = $this->categoryModel->generateUniqueSlug($data['name'], $id);
        }

        // Atualiza
        if ($this->categoryModel->update($id, $data)) {
            // Log
            $this->activityLogModel->create([
                'user_id' => auth_id(),
                'action' => 'category_updated',
                'entity_type' => 'category',
                'entity_id' => $id,
                'description' => "Categoria atualizada: {$data['name']}",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            flash('success', 'Categoria atualizada com sucesso!');
            redirect('/admin/categorias');
        } else {
            flash('error', 'Erro ao atualizar categoria');
            redirect('/admin/categorias/' . $id . '/editar');
        }
    }

    /**
     * Deleta categoria
     */
    public function delete($id)
    {
        if (!$this->isPost()) {
            redirect('/admin/categorias');
        }

        $category = $this->categoryModel->find($id);

        if (!$category) {
            flash('error', 'Categoria não encontrada');
            redirect('/admin/categorias');
        }

        // Verifica se tem posts
        $postCount = $this->categoryModel->getPostCount($id);
        if ($postCount > 0) {
            flash('error', "Não é possível deletar. Esta categoria possui {$postCount} post(s) associado(s)");
            redirect('/admin/categorias');
        }

        // Verifica se tem subcategorias
        $subcategories = $this->categoryModel->getSubcategories($id);
        if (!empty($subcategories)) {
            flash('error', 'Não é possível deletar. Esta categoria possui subcategorias');
            redirect('/admin/categorias');
        }

        // Deleta
        if ($this->categoryModel->delete($id)) {
            // Log
            $this->activityLogModel->create([
                'user_id' => auth_id(),
                'action' => 'category_deleted',
                'entity_type' => 'category',
                'entity_id' => $id,
                'description' => "Categoria deletada: {$category['name']}",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            flash('success', 'Categoria deletada com sucesso!');
        } else {
            flash('error', 'Erro ao deletar categoria');
        }

        redirect('/admin/categorias');
    }

    /**
     * Reordena categorias (AJAX)
     */
    public function reorder()
    {
        if (!$this->isPost()) {
            return $this->json(['error' => 'Método não permitido'], 405);
        }

        $order = $this->post('order'); // Array de IDs na ordem

        if (empty($order) || !is_array($order)) {
            return $this->json(['error' => 'Ordem inválida'], 400);
        }

        // Atualiza ordem (se tiver campo 'order' na tabela)
        foreach ($order as $index => $categoryId) {
            $this->categoryModel->update($categoryId, ['order' => $index]);
        }

        return $this->json([
            'success' => true,
            'message' => 'Ordem atualizada com sucesso'
        ]);
    }
}
