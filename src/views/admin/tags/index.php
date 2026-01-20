<?php
/**
 * View - Admin Tags Index
 * Listagem e gerenciamento de tags de posts
 */

$pageTitle = $pageTitle ?? 'Gerenciar Tags';
$tags = $tags ?? [];
$stats = $stats ?? [];
$mostUsed = $mostUsed ?? [];

require_once __DIR__ . '/../layout/header.php';
?>

<style>
    .tags-header {
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

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
    }

    .tags-table {
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

    .tag-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        background: var(--color-primary);
        color: white;
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

    .btn-warning {
        background: #ffc107;
        color: #000;
    }

    .btn-warning:hover {
        background: #e0a800;
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

    .sidebar-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--color-primary);
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--color-border);
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid var(--color-border);
        border-radius: 6px;
        font-size: 14px;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(6, 37, 61, 0.1);
    }

    .most-used-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .most-used-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
    }

    .most-used-item:last-child {
        border-bottom: none;
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

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .content-grid {
            grid-template-columns: 1fr;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            min-width: 600px;
        }
    }
</style>

<div class="tags-header">
    <h1 class="page-title">🏷️ Gerenciar Tags</h1>
    <div style="display: flex; gap: 10px;">
        <button onclick="cleanUnused()" class="btn btn-warning">
            🧹 Limpar Não Usadas
        </button>
    </div>
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
        <div class="stat-label">Total de Tags</div>
    </div>

    <div class="stat-card">
        <div class="stat-value"><?= $stats['used'] ?? 0 ?></div>
        <div class="stat-label">Tags Utilizadas</div>
    </div>

    <div class="stat-card">
        <div class="stat-value"><?= $stats['unused'] ?? 0 ?></div>
        <div class="stat-label">Tags Não Usadas</div>
    </div>

    <div class="stat-card">
        <div class="stat-value"><?= $stats['total_posts'] ?? 0 ?></div>
        <div class="stat-label">Posts Associados</div>
    </div>
</div>

<!-- Grid Principal -->
<div class="content-grid">
    <!-- Tabela de Tags -->
    <div class="tags-table">
        <?php if (!empty($tags)): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Slug</th>
                            <th>Posts</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tags as $tag): ?>
                            <tr>
                                <td><strong>#<?= $tag['id'] ?></strong></td>
                                <td>
                                    <span class="tag-badge">
                                        <?= htmlspecialchars($tag['name']) ?>
                                    </span>
                                </td>
                                <td>
                                    <code style="background: #f8f9fa; padding: 3px 8px; border-radius: 4px; font-size: 12px;">
                                        <?= htmlspecialchars($tag['slug']) ?>
                                    </code>
                                </td>
                                <td>
                                    <span style="font-weight: 500;">
                                        <?= $tag['post_count'] ?> post(s)
                                    </span>
                                </td>
                                <td>
                                    <button onclick="editTag(<?= $tag['id'] ?>, '<?= addslashes($tag['name']) ?>')" class="btn btn-primary btn-sm">
                                        ✏️ Editar
                                    </button>
                                    <button onclick="deleteTag(<?= $tag['id'] ?>, '<?= addslashes($tag['name']) ?>')" class="btn btn-danger btn-sm">
                                        🗑️ Deletar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">🏷️</div>
                <h3>Nenhuma tag encontrada</h3>
                <p>As tags são criadas automaticamente quando você cria posts</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Criar Nova Tag -->
        <div class="sidebar-card">
            <h2 class="card-title">➕ Criar Nova Tag</h2>
            <form id="createTagForm">
                <div class="form-group">
                    <label for="tag_name">Nome da Tag</label>
                    <input
                        type="text"
                        class="form-control"
                        id="tag_name"
                        name="name"
                        placeholder="Ex: Direito Civil"
                        required
                    >
                </div>
                <button type="submit" class="btn btn-success" style="width: 100%;">
                    ✅ Criar Tag
                </button>
            </form>
        </div>

        <!-- Tags Mais Usadas -->
        <?php if (!empty($mostUsed)): ?>
        <div class="sidebar-card">
            <h2 class="card-title">🔥 Tags Mais Usadas</h2>
            <ul class="most-used-list">
                <?php foreach ($mostUsed as $tag): ?>
                    <li class="most-used-item">
                        <span class="tag-badge" style="font-size: 12px;">
                            <?= htmlspecialchars($tag['name']) ?>
                        </span>
                        <strong><?= $tag['post_count'] ?> posts</strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Criar nova tag
document.getElementById('createTagForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const name = document.getElementById('tag_name').value.trim();

    if (!name) {
        alert('Digite o nome da tag');
        return;
    }

    fetch('<?= base_url('admin/tags/criar') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'name=' + encodeURIComponent(name) + '&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.error || 'Erro ao criar tag');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao criar tag');
    });
});

// Editar tag
function editTag(id, currentName) {
    const newName = prompt('Digite o novo nome da tag:', currentName);

    if (!newName || newName.trim() === '') {
        return;
    }

    if (newName.trim() === currentName) {
        alert('O nome não foi alterado');
        return;
    }

    fetch('<?= base_url('admin/tags/') ?>' + id + '/editar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'name=' + encodeURIComponent(newName.trim()) + '&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.error || 'Erro ao editar tag');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao editar tag');
    });
}

// Deletar tag
function deleteTag(id, name) {
    if (!confirm(`Tem certeza que deseja deletar a tag "${name}"?\n\nAs associações com posts serão removidas.`)) {
        return;
    }

    fetch('<?= base_url('admin/tags/') ?>' + id + '/deletar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.error || 'Erro ao deletar tag');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao deletar tag');
    });
}

// Limpar tags não usadas
function cleanUnused() {
    if (!confirm('Tem certeza que deseja remover todas as tags não utilizadas?\n\nEsta ação não pode ser desfeita.')) {
        return;
    }

    fetch('<?= base_url('admin/tags/limpar-nao-usadas') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.error || 'Erro ao limpar tags');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao limpar tags');
    });
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
