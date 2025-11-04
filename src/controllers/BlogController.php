<?php

namespace Controllers;

use Core\Controller;
use Models\Post;
use Models\Category;
use Models\Tag;

/**
 * BlogController - Gerencia o blog público
 */
class BlogController extends Controller
{
    private $postModel;
    private $categoryModel;
    private $tagModel;

    public function __construct()
    {
        $this->postModel = new Post();
        $this->categoryModel = new Category();
        $this->tagModel = new Tag();
    }

    /**
     * Lista todos os posts publicados
     */
    public function index()
    {
        // Obtém página atual
        $page = $this->get('page', 1);
        $perPage = 10;

        // Busca posts com paginação
        $postsData = $this->postModel->paginatePublished($page, $perPage);

        // Busca categorias
        $categories = $this->categoryModel->getAllWithPostCount();

        // Busca tags mais usadas
        $popularTags = $this->tagModel->getMostUsed(10);

        // Busca posts mais visualizados
        $mostViewed = $this->postModel->getMostViewed(5);

        // Define dados da página
        $pageTitle = 'Blog - Artigos sobre Direito Previdenciário';
        $pageDescription = 'Artigos, notícias e informações sobre direitos previdenciários';

        // Carrega view
        $this->view('blog/index', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'posts' => $postsData['data'],
            'pagination' => $postsData,
            'categories' => $categories,
            'popularTags' => $popularTags,
            'mostViewed' => $mostViewed
        ]);
    }

    /**
     * Exibe post individual
     */
    public function show($slug)
    {
        // Busca post por slug
        $post = $this->postModel->findBySlug($slug);

        // Verifica se existe
        if (!$post) {
            $this->redirect('blog');
        }

        // Incrementa visualizações
        $this->postModel->incrementViews($post['id']);

        // Busca tags do post
        $tags = $this->tagModel->getPostTags($post['id']);

        // Busca posts relacionados
        $relatedPosts = $this->postModel->getRelated(
            $post['id'], 
            $post['category_id'], 
            4
        );

        // Define dados da página
        $pageTitle = $post['title'];
        $pageDescription = $post['excerpt'] ?? truncate(strip_tags($post['content']), 160);

        // Carrega view
        $this->view('blog/single', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'post' => $post,
            'tags' => $tags,
            'relatedPosts' => $relatedPosts
        ]);
    }

    /**
     * Lista posts por categoria
     */
    public function category($slug)
    {
        // Busca categoria
        $category = $this->categoryModel->findBySlug($slug);

        if (!$category) {
            $this->redirect('blog');
        }

        // Obtém página atual
        $page = $this->get('page', 1);
        $perPage = 10;

        // Busca posts da categoria
        $posts = $this->postModel->getByCategory($category['id']);

        // Paginação manual
        $total = count($posts);
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $posts = array_slice($posts, $offset, $perPage);

        // Busca todas as categorias
        $categories = $this->categoryModel->getAllWithPostCount();

        // Define dados da página
        $pageTitle = 'Categoria: ' . $category['name'];
        $pageDescription = $category['description'] ?? '';

        // Carrega view
        $this->view('blog/category', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'category' => $category,
            'posts' => $posts,
            'categories' => $categories,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages
            ]
        ]);
    }

    /**
     * Lista posts por tag
     */
    public function tag($slug)
    {
        // Busca tag
        $tag = $this->tagModel->findBySlug($slug);

        if (!$tag) {
            $this->redirect('blog');
        }

        // Busca posts da tag
        $posts = $this->postModel->getByTag($tag['id']);

        // Busca todas as tags
        $allTags = $this->tagModel->getMostUsed(20);

        // Define dados da página
        $pageTitle = 'Tag: ' . $tag['name'];
        $pageDescription = 'Artigos relacionados a ' . $tag['name'];

        // Carrega view
        $this->view('blog/tag', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'tag' => $tag,
            'posts' => $posts,
            'allTags' => $allTags
        ]);
    }

    /**
     * Busca posts
     */
    public function search()
    {
        // Obtém termo de busca
        $searchTerm = $this->get('q') ?? $this->post('q');

        if (empty($searchTerm)) {
            $this->redirect('blog');
        }

        // Sanitiza termo
        $searchTerm = sanitize($searchTerm);

        // Busca posts
        $posts = $this->postModel->search($searchTerm);

        // Define dados da página
        $pageTitle = 'Busca: ' . $searchTerm;
        $pageDescription = 'Resultados da busca por: ' . $searchTerm;

        // Carrega view
        $this->view('blog/search', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'searchTerm' => $searchTerm,
            'posts' => $posts,
            'totalResults' => count($posts)
        ]);
    }

    /**
     * Feed RSS
     */
    public function feed()
    {
        // Busca últimos posts
        $posts = $this->postModel->getPublished(20);

        // Define header XML
        header('Content-Type: application/rss+xml; charset=UTF-8');

        // Gera XML
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<rss version="2.0">';
        echo '<channel>';
        echo '<title>' . app_name() . '</title>';
        echo '<link>' . base_url() . '</link>';
        echo '<description>Artigos sobre Direito Previdenciário</description>';

        foreach ($posts as $post) {
            echo '<item>';
            echo '<title>' . htmlspecialchars($post['title']) . '</title>';
            echo '<link>' . base_url('blog/' . $post['slug']) . '</link>';
            echo '<description>' . htmlspecialchars($post['excerpt'] ?? '') . '</description>';
            echo '<pubDate>' . date('r', strtotime($post['published_at'])) . '</pubDate>';
            echo '</item>';
        }

        echo '</channel>';
        echo '</rss>';
        exit;
    }
}