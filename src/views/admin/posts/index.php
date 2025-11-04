<?php
/**
 * View - Admin Posts Index
 * Lista de posts administrativo
 */

require_once __DIR__ . '/../layout/header.php';
?>

<style>
    .page-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--color-secondary);
        color: var(--color-white);
    }

    .btn-primary:hover {
        background: var(--color-primary);
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-info {
        background: #17a2b8;
        color: white;
    }

    .table-container {
        background: var(--color-white);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: var(--color-background);
    }

    th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: var(--color-primary);
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    td {
        padding: 15px;
        border-bottom: 1px solid var(--color-border);
    }

    tbody tr:hover {
        background: var(--color-background);
    }

    .post-title {
        font-weight: 600;
        color: var(--color-primary);
        display: block;
        margin-bottom: 5px;
    }

    .post-meta {
        font-size: 13px;
        color: var(--color-text-light);
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
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

    .actions {
        display: flex;
        gap: 8px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--color-text-light);
    }

    .empty-state-icon {
        font-size: 80px;
        margin-bottom: 20px;
        opacity: 0.3;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <h1>Gerenciar Posts</h1>
    <p>Crie e gerencie os artigos do blog</p>
</div>

<!-- Page Actions -->
<div class="page-actions">
    <div></div>
    <a href="<?= base_url('admin/posts/criar') ?>" class="btn btn-primary">
        ➕ Criar Novo Post
    </a>
</div>

<!-- Table -->
<div class="table-container">
    <?php if (!empty($posts) && count($posts) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Post</th>
                <th>Categoria</th>
                <th>Autor</th>
                <th>Status</th>
                <th>Data</th>
                <th>Visualizações</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
            <tr>
                <td>
                    <span class="post-title"><?= $post['title'] ?></span>
                    <span class="post-meta">
                        <?= truncate(strip_tags($post['content']), 80) ?>
                    </span>
                </td>
                <td><?= $post['category_name'] ?? 'Sem categoria' ?></td>
                <td><?= $post['author_name'] ?? 'Desconhecido' ?></td>
                <td>
                    <span class="status-badge status-<?= $post['status'] ?>">
                        <?= ucfirst($post['status']) ?>
                    </span>
                </td>
                <td><?= format_date($post['created_at']) ?></td>
                <td><?= $post['views'] ?? 0 ?></td>
                <td>
                    <div class="actions">
                        <a href="<?= base_url('admin/posts/' . $post['id'] . '/editar') ?>" 
                           class="btn btn-info btn-sm"
                           title="Editar">
                            ✏️
                        </a>
                        <form action="<?= base_url('admin/posts/' . $post['id'] . '/deletar') ?>" 
                              method="POST" 
                              style="display: inline;">
                            <?= csrf_field() ?>
                            <button type="submit" 
                                    class="btn btn-danger btn-sm"
                                    data-confirm="Tem certeza que deseja excluir este post?"
                                    title="Excluir">
                                🗑️
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon">📝</div>
        <h3>Nenhum post cadastrado</h3>
        <p>Comece criando seu primeiro post</p>
        <br>
        <a href="<?= base_url('admin/posts/criar') ?>" class="btn btn-primary">
            Criar Primeiro Post
        </a>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>