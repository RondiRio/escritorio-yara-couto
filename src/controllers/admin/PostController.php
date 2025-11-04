<?php

namespace Controllers\Admin;

use Core\Controller;
use Models\Post;
use Models\Category;
use Models\Tag;
use Models\ActivityLog;

/**
 * PostController (Admin) - Gerencia posts administrativamente
 */
class PostController extends Controller
{
    private $postModel;
    private $categoryModel;
    private $tagModel;
    private $activityLogModel;

    public function __construct()
    {
        $this->postModel = new Post();
        $this->categoryModel = new Category();
        $this->tagModel = new Tag();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * Lista todos os posts
     */
    public function index()
    {
        $this->requireAuth();

        // Busca todos os posts
        $posts = $this->postModel->getAllForAdmin();

        $pageTitle = 'Gerenciar Posts';

        $this->view('admin/posts/index', [
            'pageTitle' => $pageTitle,
            'posts' => $posts
        ]);
    }

    /**
     * Exibe formulário de criar post
     */
    public function create()
    {
        $this->requireAuth();

        // Busca categorias e tags
        $categories = $this->categoryModel->all('name ASC');
        $tags = $this->tagModel->all('name ASC');

        $pageTitle = 'Criar Novo Post';

        $this->view('admin/posts/create', [
            'pageTitle' => $pageTitle,
            'categories' => $categories,
            'tags' => $tags
        ]);
    }

    /**
     * Salva novo post
     */
    public function store()
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->redirect('admin/posts');
        }

        // Obtém dados
        $data = [
            'title' => $this->post('title'),
            'content' => $this->post('content'),
            'excerpt' => $this->post('excerpt'),
            'category_id' => $this->post('category_id'),
            'status' => $this->post('status', 'draft'),
            'author_id' => auth_id()
        ];

        // Valida
        $errors = $this->validate($data, [
            'title' => 'required|min:5',
            'content' => 'required|min:50',
            'category_id' => 'required|numeric'
        ]);

        if (!empty($errors)) {
            set_old($data);
            flash('error', 'Preencha todos os campos obrigatórios');
            $this->redirect('admin/posts/criar');
        }

        // Gera slug único
        $data['slug'] = $this->postModel->generateUniqueSlug($data['title']);

        // Define data de publicação se for publicado
        if ($data['status'] === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        // Upload de imagem destacada
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
            $imageName = upload_file(
                $_FILES['featured_image'],
                __DIR__ . '/../../../storage/uploads/posts',
                ['jpg', 'jpeg', 'png', 'gif', 'webp']
            );
            if ($imageName) {
                $data['featured_image'] = $imageName;
            }
        }

        // Sanitiza
        $data = $this->sanitize($data);

        // Cria post
        $postId = $this->postModel->create($data);

        if ($postId) {
            // Associa tags
            $tagIds = $this->post('tags', []);
            if (!empty($tagIds) && is_array($tagIds)) {
                $this->tagModel->attachToPost($postId, $tagIds);
            }

            // Log
            $this->activityLogModel->logCreate(
                'post',
                $postId,
                "Post criado: {$data['title']}"
            );

            flash('success', 'Post criado com sucesso!');
            $this->redirect('admin/posts');
        } else {
            flash('error', 'Erro ao criar post');
            $this->redirect('admin/posts/criar');
        }
    }

    /**
     * Exibe formulário de editar post
     */
    public function edit($id)
    {
        $this->requireAuth();

        $post = $this->postModel->find($id);

        if (!$post) {
            flash('error', 'Post não encontrado');
            $this->redirect('admin/posts');
        }

        // Busca categorias e tags
        $categories = $this->categoryModel->all('name ASC');
        $tags = $this->tagModel->all('name ASC');
        $postTags = $this->tagModel->getPostTags($id);
        $postTagIds = array_column($postTags, 'id');

        $pageTitle = 'Editar Post: ' . $post['title'];

        $this->view('admin/posts/edit', [
            'pageTitle' => $pageTitle,
            'post' => $post,
            'categories' => $categories,
            'tags' => $tags,
            'postTagIds' => $postTagIds
        ]);
    }

    /**
     * Atualiza post
     */
    public function update($id)
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->redirect('admin/posts');
        }

        $post = $this->postModel->find($id);

        if (!$post) {
            flash('error', 'Post não encontrado');
            $this->redirect('admin/posts');
        }

        // Obtém dados
        $data = [
            'title' => $this->post('title'),
            'content' => $this->post('content'),
            'excerpt' => $this->post('excerpt'),
            'category_id' => $this->post('category_id'),
            'status' => $this->post('status')
        ];

        // Valida
        $errors = $this->validate($data, [
            'title' => 'required|min:5',
            'content' => 'required|min:50',
            'category_id' => 'required|numeric'
        ]);

        if (!empty($errors)) {
            flash('error', 'Preencha todos os campos obrigatórios');
            $this->redirect("admin/posts/{$id}/editar");
        }

        // Atualiza slug se título mudou
        if ($data['title'] !== $post['title']) {
            $data['slug'] = $this->postModel->generateUniqueSlug($data['title'], $id);
        }

        // Define data de publicação se status mudou para publicado
        if ($data['status'] === 'published' && $post['status'] !== 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        // Upload de nova imagem
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
            $imageName = upload_file(
                $_FILES['featured_image'],
                __DIR__ . '/../../../storage/uploads/posts',
                ['jpg', 'jpeg', 'png', 'gif', 'webp']
            );
            if ($imageName) {
                $data['featured_image'] = $imageName;
                
                // Remove imagem antiga
                if (!empty($post['featured_image'])) {
                    $oldImage = __DIR__ . '/../../../storage/uploads/posts/' . $post['featured_image'];
                    if (file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                }
            }
        }

        // Sanitiza
        $data = $this->sanitize($data);

        // Atualiza
        $updated = $this->postModel->update($id, $data);

        if ($updated !== false) {
            // Atualiza tags
            $tagIds = $this->post('tags', []);
            $this->tagModel->attachToPost($id, $tagIds);

            // Log
            $this->activityLogModel->logUpdate(
                'post',
                $id,
                "Post atualizado: {$data['title']}"
            );

            flash('success', 'Post atualizado com sucesso!');
            $this->redirect('admin/posts');
        } else {
            flash('error', 'Erro ao atualizar post');
            $this->redirect("admin/posts/{$id}/editar");
        }
    }

    /**
     * Visualiza post
     */
    public function show($id)
    {
        $this->requireAuth();

        $post = $this->postModel->find($id);

        if (!$post) {
            flash('error', 'Post não encontrado');
            $this->redirect('admin/posts');
        }

        $pageTitle = 'Visualizar Post: ' . $post['title'];

        $this->view('admin/posts/show', [
            'pageTitle' => $pageTitle,
            'post' => $post
        ]);
    }

    /**
     * Deleta post
     */
    public function delete($id)
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->redirect('admin/posts');
        }

        $post = $this->postModel->find($id);

        if (!$post) {
            flash('error', 'Post não encontrado');
            $this->redirect('admin/posts');
        }

        // Remove imagem
        if (!empty($post['featured_image'])) {
            $imagePath = __DIR__ . '/../../../storage/uploads/posts/' . $post['featured_image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Remove tags associadas
        $this->tagModel->detachFromPost($id);

        // Deleta
        $deleted = $this->postModel->delete($id);

        if ($deleted) {
            // Log
            $this->activityLogModel->logDelete(
                'post',
                $id,
                "Post deletado: {$post['title']}"
            );

            flash('success', 'Post deletado com sucesso!');
        } else {
            flash('error', 'Erro ao deletar post');
        }

        $this->redirect('admin/posts');
    }

    /**
     * Altera status do post
     */
    public function changeStatus($id)
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->json(['error' => 'Método não permitido'], 405);
        }

        $status = $this->post('status');

        if (!in_array($status, ['draft', 'published'])) {
            $this->json(['error' => 'Status inválido'], 400);
        }

        $data = ['status' => $status];

        if ($status === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        $updated = $this->postModel->update($id, $data);

        if ($updated !== false) {
            $this->json([
                'success' => true,
                'message' => 'Status atualizado'
            ]);
        } else {
            $this->json(['error' => 'Erro ao atualizar status'], 500);
        }
    }

    /**
     * Upload de imagem (AJAX)
     */
    public function uploadImage()
    {
        $this->requireAuth();

        if (!isset($_FILES['image'])) {
            $this->json(['error' => 'Nenhuma imagem enviada'], 400);
        }

        $imageName = upload_file(
            $_FILES['image'],
            __DIR__ . '/../../../storage/uploads/posts',
            ['jpg', 'jpeg', 'png', 'gif', 'webp']
        );

        if ($imageName) {
            $this->json([
                'success' => true,
                'url' => base_url('storage/uploads/posts/' . $imageName)
            ]);
        } else {
            $this->json(['error' => 'Erro ao fazer upload'], 500);
        }
    }
}