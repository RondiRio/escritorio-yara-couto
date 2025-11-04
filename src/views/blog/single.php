<?php
/**
 * View - Blog Single
 * Post individual do blog
 */
?>

<style>
    .post-single {
        padding: 80px 0;
        background: var(--color-white);
    }

    .post-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 60px;
        margin-top: 40px;
    }

    .post-main {
        background: var(--color-background);
        padding: 40px;
        border-radius: 10px;
    }

    .post-featured-image {
        width: 100%;
        height: 400px;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 30px;
        background: var(--color-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 100px;
        color: var(--color-white);
    }

    .post-featured-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .post-header-info {
        display: flex;
        gap: 25px;
        flex-wrap: wrap;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--color-border);
        font-size: 14px;
        color: var(--color-text-light);
    }

    .post-header-info span {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .post-title-main {
        font-size: 36px;
        color: var(--color-primary);
        margin-bottom: 20px;
        line-height: 1.3;
    }

    .post-content-main {
        font-size: 17px;
        line-height: 1.9;
        color: var(--color-text);
    }

    .post-content-main p {
        margin-bottom: 20px;
    }

    .post-content-main h2,
    .post-content-main h3,
    .post-content-main h4 {
        margin: 30px 0 15px;
        color: var(--color-primary);
    }

    .post-content-main ul,
    .post-content-main ol {
        margin: 20px 0;
        padding-left: 30px;
    }

    .post-content-main li {
        margin-bottom: 10px;
    }

    .post-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid var(--color-border);
    }

    .tag-link {
        background: var(--color-white);
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 14px;
        color: var(--color-text);
        border: 2px solid var(--color-secondary);
        transition: var(--transition);
    }

    .tag-link:hover {
        background: var(--color-secondary);
        color: var(--color-white);
    }

    .related-posts {
        margin-top: 60px;
    }

    .related-posts h3 {
        font-size: 24px;
        color: var(--color-primary);
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--color-secondary);
    }

    .related-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
    }

    .related-post-card {
        background: var(--color-white);
        border-radius: 8px;
        overflow: hidden;
        transition: var(--transition);
    }

    .related-post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .related-post-thumb {
        height: 150px;
        background: var(--color-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 50px;
        color: var(--color-white);
    }

    .related-post-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .related-post-info {
        padding: 20px;
    }

    .related-post-info h4 {
        font-size: 16px;
        color: var(--color-primary);
        margin-bottom: 10px;
    }

    .related-post-info h4:hover {
        color: var(--color-secondary);
    }

    .related-post-date {
        font-size: 13px;
        color: var(--color-text-light);
    }

    @media (max-width: 1024px) {
        .post-container {
            grid-template-columns: 1fr;
        }

        .post-title-main {
            font-size: 28px;
        }

        .post-featured-image {
            height: 300px;
        }
    }
</style>

<!-- Breadcrumb -->
<div style="background: var(--color-background); padding: 20px 0;">
    <div class="container">
        <div style="font-size: 14px; color: var(--color-text-light);">
            <a href="<?= base_url() ?>" style="color: var(--color-secondary);">Home</a> / 
            <a href="<?= base_url('blog') ?>" style="color: var(--color-secondary);">Blog</a> / 
            <span><?= $post['title'] ?></span>
        </div>
    </div>
</div>

<!-- Post Single -->
<section class="post-single">
    <div class="container">
        <div class="post-container">
            <!-- Main Content -->
            <article class="post-main">
                <!-- Featured Image -->
                <?php if (!empty($post['featured_image'])): ?>
                <div class="post-featured-image">
                    <img src="<?= base_url('storage/uploads/posts/' . $post['featured_image']) ?>" 
                         alt="<?= $post['title'] ?>">
                </div>
                <?php endif; ?>

                <!-- Header Info -->
                <div class="post-header-info">
                    <span>📅 <?= format_date($post['published_at']) ?></span>
                    <span>👤 <?= $post['author_name'] ?? 'Admin' ?></span>
                    <span>📂 <?= $post['category_name'] ?? 'Sem categoria' ?></span>
                    <span>👁 <?= $post['views'] ?? 0 ?> visualizações</span>
                </div>

                <!-- Title -->
                <h1 class="post-title-main"><?= $post['title'] ?></h1>

                <!-- Content -->
                <div class="post-content-main">
                    <?= $post['content'] ?>
                </div>

                <!-- Tags -->
                <?php if (!empty($tags)): ?>
                <div class="post-tags">
                    <strong style="color: var(--color-primary); margin-right: 10px;">Tags:</strong>
                    <?php foreach ($tags as $tag): ?>
                    <a href="<?= base_url('blog/tag/' . $tag['slug']) ?>" class="tag-link">
                        <?= $tag['name'] ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Related Posts -->
                <?php if (!empty($relatedPosts)): ?>
                <div class="related-posts">
                    <h3>Artigos Relacionados</h3>
                    <div class="related-grid">
                        <?php foreach ($relatedPosts as $related): ?>
                        <a href="<?= base_url('blog/' . $related['slug']) ?>" class="related-post-card">
                            <div class="related-post-thumb">
                                <?php if (!empty($related['featured_image'])): ?>
                                    <img src="<?= base_url('storage/uploads/posts/' . $related['featured_image']) ?>" 
                                         alt="<?= $related['title'] ?>">
                                <?php else: ?>
                                    📄
                                <?php endif; ?>
                            </div>
                            <div class="related-post-info">
                                <h4><?= truncate($related['title'], 60) ?></h4>
                                <div class="related-post-date">
                                    <?= format_date($related['published_at']) ?>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </article>

            <!-- Sidebar (reusa do blog/index.php) -->
            <aside class="sidebar">
                <div class="sidebar-widget">
                    <h3>Compartilhar</h3>
                    <div style="display: flex; gap: 10px;">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(base_url('blog/' . $post['slug'])) ?>" 
                           target="_blank"
                           style="padding: 10px 20px; background: #3b5998; color: white; border-radius: 5px; text-decoration: none;">
                            Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode(base_url('blog/' . $post['slug'])) ?>&text=<?= urlencode($post['title']) ?>" 
                           target="_blank"
                           style="padding: 10px 20px; background: #1da1f2; color: white; border-radius: 5px; text-decoration: none;">
                            Twitter
                        </a>
                        <a href="https://wa.me/?text=<?= urlencode($post['title'] . ' - ' . base_url('blog/' . $post['slug'])) ?>" 
                           target="_blank"
                           style="padding: 10px 20px; background: #25D366; color: white; border-radius: 5px; text-decoration: none;">
                            WhatsApp
                        </a>
                    </div>
                </div>

                <div class="sidebar-widget">
                    <h3>Sobre o Autor</h3>
                    <p style="color: var(--color-text-light); line-height: 1.8;">
                        <strong style="color: var(--color-primary);">
                            <?= $post['author_name'] ?? 'Admin' ?>
                        </strong><br>
                        Especialista em Direito Previdenciário
                    </p>
                </div>
            </aside>
        </div>
    </div>
</section>