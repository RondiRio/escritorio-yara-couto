<?php
/**
 * View - Admin Categorias Index
 * Listagem e gerenciamento de categorias de posts
 */

$pageTitle = $pageTitle ?? 'Gerenciar Categorias';
$categories = $categories ?? [];
$stats = $stats ?? [];

require_once __DIR__ . '/../layout/header.php';
?>

<style>
    .categories-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--color-primary);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-left: 4px solid var(--color-primary);
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 5px;
    }

    .stat-label {
        color: var(--color-text-light);
        font-size: 14px;
    }

    .categories-table {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background: #f8f9fa;
        border-bottom: 2px solid var(--color-border);
    }

    .table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: var(--color-text);
        font-size: 14px;
    }

    .table td {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
    }

    .table tbody tr {
        transition: var(--transition);
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        background: #e7f3ff;
        color: #06253D;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: var(--transition);
        border: none;
        cursor: pointer;
        font-size: 13px;
    }

    .btn-primary {
        background: var(--color-primary);
        color: white;
    }

    .btn-primary:hover {
        background: #04192b;
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #218838;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--color-text-light);
    }

    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 20px;
    }

    .category-hierarchy {
        padding-left: 20px;
        color: #666;
        font-size: 13px;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            min-width: 700px;
        }
    }
</style>

<div class="categories-header">
    <h1 class="page-title">📁 Gerenciar Categorias</h1>
    <a href="<?= base_url('admin/categorias/criar') ?>" class="btn btn-success">
        ➕ Nova Categoria
    </a>
</div>

<?php if (isset($_SESSION['flash'])): ?>
    <?php foreach ($_SESSION['flash'] as $type => $message): ?>
        <div class="alert alert-<?= $type ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; background: <?= $type === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $type === 'success' ? '#155724' : '#721c24' ?>;">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php unset($_SESSION['flash'][$type]); ?>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Estatísticas -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
        <div class="stat-label">Total de Categorias</div>
    </div>

    <div class="stat-card">
        <div class="stat-value"><?= $stats['main_categories'] ?? 0 ?></div>
        <div class="stat-label">Categorias Principais</div>
    </div>

    <div class="stat-card">
        <div class="stat-value"><?= $stats['subcategories'] ?? 0 ?></div>
        <div class="stat-label">Subcategorias</div>
    </div>

    <div class="stat-card">
        <div class="stat-value"><?= $stats['total_posts'] ?? 0 ?></div>
        <div class="stat-label">Posts Associados</div>
    </div>
</div>

<!-- Tabela de Categorias -->
<div class="categories-table">
    <?php if (!empty($categories)): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th>Posts</th>
                        <th>Criada em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Separa categorias principais e subcategorias
                    $mainCategories = array_filter($categories, fn($c) => $c['parent_id'] === null);
                    $subcategories = array_filter($categories, fn($c) => $c['parent_id'] !== null);

                    // Agrupa subcategorias por parent_id
                    $subcategoriesByParent = [];
                    foreach ($subcategories as $sub) {
                        $subcategoriesByParent[$sub['parent_id']][] = $sub;
                    }

                    // Exibe categorias principais e suas subcategorias
                    foreach ($mainCategories as $category):
                    ?>
                        <tr>
                            <td><strong>#<?= $category['id'] ?></strong></td>
                            <td>
                                <div style="font-weight: 500;">
                                    <?= htmlspecialchars($category['name']) ?>
                                </div>
                                <?php if (!empty($category['description'])): ?>
                                    <div style="font-size: 12px; color: #666; margin-top: 3px;">
                                        <?= htmlspecialchars(substr($category['description'], 0, 60)) ?>...
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <code style="background: #f8f9fa; padding: 3px 8px; border-radius: 4px; font-size: 12px;">
                                    <?= htmlspecialchars($category['slug']) ?>
                                </code>
                            </td>
                            <td>
                                <span class="badge">
                                    <?= $category['post_count'] ?> post(s)
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($category['created_at'])) ?></td>
                            <td>
                                <a href="<?= base_url('admin/categorias/' . $category['id'] . '/editar') ?>" class="btn btn-primary btn-sm">
                                    ✏️ Editar
                                </a>
                                <button onclick="confirmDelete(<?= $category['id'] ?>, '<?= addslashes($category['name']) ?>')" class="btn btn-danger btn-sm">
                                    🗑️ Deletar
                                </button>
                            </td>
                        </tr>

                        <?php
                        // Exibe subcategorias desta categoria
                        if (isset($subcategoriesByParent[$category['id']])):
                            foreach ($subcategoriesByParent[$category['id']] as $sub):
                        ?>
                            <tr style="background: #f8f9fa;">
                                <td><strong>#<?= $sub['id'] ?></strong></td>
                                <td>
                                    <div class="category-hierarchy">
                                        └─ <?= htmlspecialchars($sub['name']) ?>
                                    </div>
                                </td>
                                <td>
                                    <code style="background: #e9ecef; padding: 3px 8px; border-radius: 4px; font-size: 12px;">
                                        <?= htmlspecialchars($sub['slug']) ?>
                                    </code>
                                </td>
                                <td>
                                    <span class="badge">
                                        <?= $sub['post_count'] ?> post(s)
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($sub['created_at'])) ?></td>
                                <td>
                                    <a href="<?= base_url('admin/categorias/' . $sub['id'] . '/editar') ?>" class="btn btn-primary btn-sm">
                                        ✏️ Editar
                                    </a>
                                    <button onclick="confirmDelete(<?= $sub['id'] ?>, '<?= addslashes($sub['name']) ?>')" class="btn btn-danger btn-sm">
                                        🗑️ Deletar
                                    </button>
                                </td>
                            </tr>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">📁</div>
            <h3>Nenhuma categoria encontrada</h3>
            <p>Comece criando sua primeira categoria para organizar os posts</p>
            <a href="<?= base_url('admin/categorias/criar') ?>" class="btn btn-success" style="margin-top: 20px;">
                ➕ Criar Primeira Categoria
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
function confirmDelete(id, name) {
    if (confirm(`Tem certeza que deseja deletar a categoria "${name}"?\n\nAtenção: Só é possível deletar categorias sem posts associados.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('admin/categorias/') ?>' + id + '/deletar';

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = '<?= $_SESSION['csrf_token'] ?? '' ?>';

        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
