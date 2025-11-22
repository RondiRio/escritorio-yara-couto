<?php
/**
 * View - Admin Posts Show
 * Visualização detalhada de um post
 */

require_once __DIR__ . '/../layout/header.php';
?>

<style>
    .post-view-container {
        background: var(--color-white);
        border-radius: 10px;
        padding: 40px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        max-width: 900px;
    }

    .post-header-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        background: var(--color-background);
        border-radius: 8px;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .status-badge {
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
    }

    .status-published {
        background: #d4edda;
        color: #155724;
    }

    .status-draft {
        background: #fff3cd;
        color: #856404;
    }

    .post-meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .meta-item {
        background: var(--color-background);
        padding: 20px;
        border-radius: 8px;
        text-align: center;
    }

    .meta-label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--color-text-light);
        margin-bottom: 8px;
    }

    .meta-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--color-secondary);
    }

    .post-title-display {
        font-size: 36px;
        color: var(--color-primary);
        margin-bottom: 20px;
        line-height: 1.3;
    }

    .post-excerpt-display {
        font-size: 18px;
        color: var(--color-text-light);
        padding: 20px;
        background: rgba(204, 140, 93, 0.1);
        border-left: 4px solid var(--color-secondary);
        border-radius: 5px;
        margin-bottom: 30px;
        font-style: italic;
    }

    .post-featured-image {
        margin-bottom: 30px;
        text-align: center;
    }

    .post-featured-image img {
        max-width: 100%;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .post-content-display {
        font-size: 17px;
        line-height: 1.9;
        color: var(--color-text);
        margin-bottom: 30px;
    }

    .post-content-display p {
        margin-bottom: 20px;
    }

    .post-content-display h2,
    .post-content-display h3 {
        margin: 30px 0 15px;
        color: var(--color-primary);
    }

    .post-info-section {
        background: var(--color-background);
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .info-section-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--color-primary);
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--color-secondary);
    }

    .info-row {
        display: flex;
        padding: 12px 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: var(--color-text);
        min-width: 150px;
    }

    .info-value {
        color: var(--color-text-light);
        flex: 1;
    }

    .tags-display {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }

    .tag-badge {
        background: var(--color-secondary);
        color: var(--color-white);
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid var(--color-background);
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        transition: var(--transition);
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: var(--color-secondary);
        color: var(--color-white);
    }

    .btn-primary:hover {
        background: var(--color-primary);
    }

    .btn-secondary {
        background: var(--color-background);
        color: var(--color-text);
    }

    .btn-secondary:hover {
        background: #e0e0e0;
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #218838;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    @media (max-width: 768px) {
        .post-view-container {
            padding: 25px 20px;
        }

        .post-title-display {
            font-size: 28px;
        }

        .post-meta-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <h1>Visualizar Post</h1>
    <p>Detalhes completos do post</p>
</div>

<!-- Post View Container -->
<div class="post-view-container">
    <!-- Header Meta -->
    <div class="post-header-meta">
        <div>
            <span class="status-badge status-<?= $post['status'] ?>">
                <?= $post['status'] === 'published' ? '✓ Publicado' : '📝 Rascunho' ?>
            </span>
        </div>
        <div style="color: var(--color-text-light); font-size: 14px;">
            Post #<?= $post['id'] ?> • Slug: <strong><?= $post['slug'] ?></strong>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="post-meta-grid">
        <div class="meta-item">
            <div class="meta-label">Visualizações</div>
            <div class="meta-value">👁 <?= $post['views'] ?? 0 ?></div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Categoria</div>
            <div class="meta-value" style="font-size: 16px;">
                <?= $post['category_name'] ?? 'Sem categoria' ?>
            </div>
        </div>
        <div class="meta-item">
            <div class="meta-label">Autor</div>
            <div class="meta-value" style="font-size: 16px;">
                <?= $post['author_name'] ?? 'Desconhecido' ?>
            </div>
        </div>
    </div>

    <!-- Título -->
    <h1 class="post-title-display"><?= $post['title'] ?></h1>

    <!-- Excerpt -->
    <?php if (!empty($post['excerpt'])): ?>
    <div class="post-excerpt-display">
        <?= $post['excerpt'] ?>
    </div>
    <?php endif; ?>

    <!-- Imagem Destacada -->
    <?php if (!empty($post['featured_image'])): ?>
    <div class="post-featured-image">
        <img src="<?= base_url('storage/uploads/posts/' . $post['featured_image']) ?>" 
             alt="<?= $post['title'] ?>">
    </div>
    <?php endif; ?>

    <!-- Conteúdo -->
    <div class="post-content-display">
        <?= $post['content'] ?>
    </div>

    <!-- Informações Detalhadas -->
    <div class="post-info-section">
        <div class="info-section-title">📋 Informações do Post</div>
        
        <div class="info-row">
            <div class="info-label">ID:</div>
            <div class="info-value">#<?= $post['id'] ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Slug:</div>
            <div class="info-value"><?= $post['slug'] ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                <span class="status-badge status-<?= $post['status'] ?>">
                    <?= ucfirst($post['status']) ?>
                </span>
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Categoria:</div>
            <div class="info-value"><?= $post['category_name'] ?? 'Sem categoria' ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Autor:</div>
            <div class="info-value"><?= $post['author_name'] ?? 'Desconhecido' ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Visualizações:</div>
            <div class="info-value"><?= $post['views'] ?? 0 ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Criado em:</div>
            <div class="info-value"><?= format_datetime($post['created_at']) ?></div>
        </div>

        <div class="info-row">
            <div class="info-label">Atualizado em:</div>
            <div class="info-value"><?= format_datetime($post['updated_at']) ?></div>
        </div>

        <?php if ($post['status'] === 'published' && !empty($post['published_at'])): ?>
        <div class="info-row">
            <div class="info-label">Publicado em:</div>
            <div class="info-value"><?= format_datetime($post['published_at']) ?></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Tags -->
    <?php if (!empty($post['tags'])): ?>
    <div class="post-info-section">
        <div class="info-section-title">🏷️ Tags</div>
        <div class="tags-display">
            <?php 
            $tags = is_string($post['tags']) ? json_decode($post['tags'], true) : $post['tags'];
            if (is_array($tags)) {
                foreach ($tags as $tag) {
                    echo '<span class="tag-badge">' . $tag . '</span>';
                }
            }
            ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="<?= base_url('admin/posts/' . $post['id'] . '/editar') ?>" class="btn btn-primary">
            ✏️ Editar Post
        </a>

        <?php if ($post['status'] === 'draft'): ?>
        <form action="<?= base_url('admin/posts/' . $post['id'] . '/status') ?>" 
              method="POST" 
              style="display: inline;">
            <?= csrf_field() ?>
            <input type="hidden" name="status" value="published">
            <button type="submit" class="btn btn-success">
                ✓ Publicar Agora
            </button>
        </form>
        <?php else: ?>
        <a href="<?= base_url('blog/' . $post['slug']) ?>" 
           target="_blank" 
           class="btn btn-success">
            👁️ Ver no Site
        </a>
        <?php endif; ?>

        <a href="<?= base_url('admin/posts') ?>" class="btn btn-secondary">
            ← Voltar para Lista
        </a>

        <form action="<?= base_url('admin/posts/' . $post['id'] . '/deletar') ?>" 
              method="POST" 
              style="display: inline; margin-left: auto;"
              onsubmit="return confirm('Tem certeza que deseja excluir este post? Esta ação não pode ser desfeita.')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-danger">
                🗑️ Excluir Post
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>