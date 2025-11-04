<?php
/**
 * View - Blog Index
 * Lista de posts do blog
 */
?>

<style>
    .blog-page {
        padding: 80px 0;
        background: var(--color-white);
    }

    .blog-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 60px;
        margin-top: 60px;
    }

    .posts-list {
        display: flex;
        flex-direction: column;
        gap: 40px;
    }

    .post-card-full {
        background: var(--color-background);
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        gap: 30px;
        transition: var(--transition);
    }

    .post-card-full:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .post-thumbnail {
        width: 300px;
        height: 250px;
        background: var(--color-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 80px;
        color: var(--color-white);
        flex-shrink: 0;
    }

    .post-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .post-content-full {
        padding: 30px 30px 30px 0;
        flex: 1;
    }

    .post-meta {
        display: flex;
        gap: 20px;
        font-size: 14px;
        color: var(--color-text-light);
        margin-bottom: 15px;
    }

    .post-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .post-title {
        font-size: 28px;
        margin-bottom: 15px;
        color: var(--color-primary);
    }

    .post-title:hover {
        color: var(--color-secondary);
    }

    .post-excerpt {
        color: var(--color-text-light);
        line-height: 1.8;
        margin-bottom: 20px;
    }

    .read-more {
        color: var(--color-secondary);
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .read-more:hover {
        gap: 10px;
    }

    /* Sidebar */
    .sidebar {
        position: sticky;
        top: 100px;
    }

    .sidebar-widget {
        background: var(--color-background);
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
    }

    .sidebar-widget h3 {
        font-size: 20px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--color-secondary);
    }

    .category-list,
    .tag-list {
        list-style: none;
    }

    .category-list li {
        margin-bottom: 12px;
    }

    .category-list a {
        color: var(--color-text);
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        transition: var(--transition);
    }

    .category-list a:hover {
        padding-left: 10px;
        color: var(--color-secondary);
    }

    .category-count {
        background: var(--color-secondary);
        color: var(--color-white);
        padding: 2px 10px;
        border-radius: 15px;
        font-size: 12px;
    }

    .tag-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .tag-item {
        background: var(--color-white);
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 14px;
        color: var(--color-text);
        border: 2px solid var(--color-secondary);
        transition: var(--transition);
    }

    .tag-item:hover {
        background: var(--color-secondary);
        color: var(--color-white);
    }

    .popular-post {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }

    .popular-post-thumb {
        width: 80px;
        height: 80px;
        background: var(--color-secondary);
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        color: var(--color-white);
        flex-shrink: 0;
    }

    .popular-post-info h4 {
        font-size: 16px;
        margin-bottom: 5px;
    }

    .popular-post-info h4:hover {
        color: var(--color-secondary);
    }

    .popular-post-date {
        font-size: 13px;
        color: var(--color-text-light);
    }

    /* Paginação */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 60px;
        flex-wrap: wrap;
    }

    .page-link {
        padding: 10px 20px;
        background: var(--color-background);
        border-radius: 5px;
        color: var(--color-text);
        font-weight: 600;
        transition: var(--transition);
    }

    .page-link:hover,
    .page-link.active {
        background: var(--color-secondary);
        color: var(--color-white);
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: var(--color-text-light);
    }

    .empty-state-icon {
        font-size: 100px;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    @media (max-width: 1024px) {
        .blog-container {
            grid-template-columns: 1fr;
        }

        .sidebar {
            position: static;
        }

        .post-card-full {
            flex-direction: column;
        }

        .post-thumbnail {
            width: 100%;
            height: 250px;
        }

        .post-content-full {
            padding: 30px;
        }
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Blog</h1>
        <p>Artigos e notícias sobre direito</p>
    </div>
</div>

<!-- Blog Page -->
<section class="blog-page">
    <div class="container">
        <div class="blog-container">
            <!-- Posts List -->
            <div class="posts-list">
                <?php if (!empty($posts) && count($posts) > 0): ?>
                    <?php foreach ($posts as $post): ?>
                    <article class="post-card-full">
                        <a href="<?= base_url('blog/' . $post['slug']) ?>" class="post-thumbnail">
                            <?php if (!empty($post['featured_image'])): ?>
                                <img src="<?= base_url('storage/uploads/posts/' . $post['featured_image']) ?>" 
                                     alt="<?= $post['title'] ?>">
                            <?php else: ?>
                                📰
                            <?php endif; ?>
                        </a>

                        <div class="post-content-full">
                            <div class="post-meta">
                                <span>📅 <?= format_date($post['published_at']) ?></span>
                                <span>📂 <?= $post['category_name'] ?? 'Sem categoria' ?></span>
                                <span>👁 <?= $post['views'] ?? 0 ?> visualizações</span>
                            </div>

                            <a href="<?= base_url('blog/' . $post['slug']) ?>">
                                <h2 class="post-title"><?= $post['title'] ?></h2>
                            </a>

                            <p class="post-excerpt">
                                <?= truncate($post['excerpt'] ?? strip_tags($post['content']), 200) ?>
                            </p>

                            <a href="<?= base_url('blog/' . $post['slug']) ?>" class="read-more">
                                Ler mais →
                            </a>
                        </div>
                    </article>
                    <?php endforeach; ?>

                    <!-- Paginação -->
                    <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="pagination">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <a href="?page=<?= $pagination['current_page'] - 1 ?>" class="page-link">← Anterior</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <a href="?page=<?= $i ?>" 
                               class="page-link <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <a href="?page=<?= $pagination['current_page'] + 1 ?>" class="page-link">Próxima →</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Empty State -->
                    <div class="empty-state">
                        <div class="empty-state-icon">📰</div>
                        <h3>Nenhum post publicado ainda</h3>
                        <p>Em breve publicaremos conteúdo relevante.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="sidebar">
                <!-- Categorias -->
                <?php if (!empty($categories) && count($categories) > 0): ?>
                <div class="sidebar-widget">
                    <h3>Categorias</h3>
                    <ul class="category-list">
                        <?php foreach ($categories as $category): ?>
                        <li>
                            <a href="<?= base_url('blog/categoria/' . $category['slug']) ?>">
                                <span><?= $category['name'] ?></span>
                                <span class="category-count"><?= $category['post_count'] ?? 0 ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Posts Populares -->
                <?php if (!empty($mostViewed) && count($mostViewed) > 0): ?>
                <div class="sidebar-widget">
                    <h3>Posts Populares</h3>
                    <?php foreach ($mostViewed as $popular): ?>
                    <div class="popular-post">
                        <a href="<?= base_url('blog/' . $popular['slug']) ?>" class="popular-post-thumb">
                            <?php if (!empty($popular['featured_image'])): ?>
                                <img src="<?= base_url('storage/uploads/posts/' . $popular['featured_image']) ?>" 
                                     alt="<?= $popular['title'] ?>"
                                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 5px;">
                            <?php else: ?>
                                📄
                            <?php endif; ?>
                        </a>
                        <div class="popular-post-info">
                            <a href="<?= base_url('blog/' . $popular['slug']) ?>">
                                <h4><?= truncate($popular['title'], 60) ?></h4>
                            </a>
                            <div class="popular-post-date">
                                <?= format_date($popular['published_at']) ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Tags -->
                <?php if (!empty($popularTags) && count($popularTags) > 0): ?>
                <div class="sidebar-widget">
                    <h3>Tags</h3>
                    <div class="tag-list">
                        <?php foreach ($popularTags as $tag): ?>
                        <a href="<?= base_url('blog/tag/' . $tag['slug']) ?>" class="tag-item">
                            <?= $tag['name'] ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>