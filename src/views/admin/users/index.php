<?php
/**
 * View - Admin Usuários Index
 * Listagem de usuários do sistema
 */

// Dados da view
$pageTitle = $pageTitle ?? 'Usuários';
$users = $users ?? [];
$stats = $stats ?? [];
$search = $search ?? '';
$role_filter = $role_filter ?? '';
$status_filter = $status_filter ?? '';
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
$total = $total ?? 0;

// Layout admin
require_once __DIR__ . '/../layout/header.php';
?>

<style>
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

    .filters-bar {
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: end;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        font-size: 14px;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--color-border);
        border-radius: 6px;
        font-size: 14px;
    }

    .users-table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        background: var(--color-primary);
        color: white;
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
    }

    td {
        padding: 15px;
        border-bottom: 1px solid var(--color-border);
    }

    tr:hover {
        background: #f8f9fa;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-admin { background: #667eea; color: white; }
    .badge-editor { background: #48bb78; color: white; }
    .badge-author { background: #ed8936; color: white; }
    .badge-active { background: #48bb78; color: white; }
    .badge-inactive { background: #fc8181; color: white; }

    .btn-group {
        display: flex;
        gap: 8px;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: var(--transition);
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--color-primary);
        color: white;
    }

    .btn-primary:hover {
        background: #04192b;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #545b62;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
        padding: 20px;
        background: white;
        border-radius: 10px;
    }

    .pagination a,
    .pagination span {
        padding: 8px 16px;
        border: 1px solid var(--color-border);
        border-radius: 6px;
        text-decoration: none;
        color: var(--color-text);
    }

    .pagination a:hover {
        background: var(--color-primary);
        color: white;
    }

    .pagination .active {
        background: var(--color-primary);
        color: white;
        border-color: var(--color-primary);
    }
</style>

<!-- Estatísticas -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
        <div class="stat-label">Total de Usuários</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $stats['admins'] ?? 0 ?></div>
        <div class="stat-label">Administradores</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $stats['editors'] ?? 0 ?></div>
        <div class="stat-label">Editores</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $stats['active'] ?? 0 ?></div>
        <div class="stat-label">Ativos</div>
    </div>
</div>

<!-- Filtros -->
<div class="filters-bar">
    <div class="filter-group">
        <label>Buscar</label>
        <input type="text" id="search" placeholder="Nome ou e-mail..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="filter-group">
        <label>Função</label>
        <select id="role">
            <option value="">Todas</option>
            <option value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="editor" <?= $role_filter === 'editor' ? 'selected' : '' ?>>Editor</option>
            <option value="author" <?= $role_filter === 'author' ? 'selected' : '' ?>>Autor</option>
        </select>
    </div>
    <div class="filter-group">
        <label>Status</label>
        <select id="status">
            <option value="">Todos</option>
            <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Ativo</option>
            <option value="inactive" <?= $status_filter === 'inactive' ? 'selected' : '' ?>>Inativo</option>
        </select>
    </div>
    <div class="filter-group">
        <label>&nbsp;</label>
        <button class="btn-sm btn-primary" onclick="applyFilters()">🔍 Filtrar</button>
    </div>
    <div class="filter-group">
        <label>&nbsp;</label>
        <a href="<?= base_url('admin/usuarios/novo') ?>" class="btn-sm btn-primary">➕ Novo Usuário</a>
    </div>
</div>

<!-- Mensagens Flash -->
<?php if (isset($_SESSION['flash'])): ?>
    <?php foreach ($_SESSION['flash'] as $type => $message): ?>
        <div class="alert alert-<?= $type ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; background: <?= $type === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $type === 'success' ? '#155724' : '#721c24' ?>;">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php unset($_SESSION['flash'][$type]); ?>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Tabela -->
<div class="users-table">
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Função</th>
                <th>Status</th>
                <th>Último Login</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--color-text-light);">
                        Nenhum usuário encontrado
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($user['name']) ?></strong></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="badge badge-<?= $user['role'] ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-<?= $user['status'] ?>">
                                <?= $user['status'] === 'active' ? 'Ativo' : 'Inativo' ?>
                            </span>
                        </td>
                        <td><?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Nunca' ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="<?= base_url("admin/usuarios/{$user['id']}/editar") ?>" class="btn-sm btn-secondary">✏️ Editar</a>

                                <?php if ($user['id'] != auth_id()): ?>
                                    <form method="POST" action="<?= base_url("admin/usuarios/{$user['id']}/deletar") ?>" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja deletar este usuário?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-sm btn-danger">🗑️ Deletar</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Paginação -->
<?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role_filter) ?>&status=<?= urlencode($status_filter) ?>">← Anterior</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i == $page): ?>
                <span class="active"><?= $i ?></span>
            <?php else: ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role_filter) ?>&status=<?= urlencode($status_filter) ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role_filter) ?>&status=<?= urlencode($status_filter) ?>">Próxima →</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<script>
function applyFilters() {
    const search = document.getElementById('search').value;
    const role = document.getElementById('role').value;
    const status = document.getElementById('status').value;

    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (role) params.append('role', role);
    if (status) params.append('status', status);

    window.location.href = '<?= base_url('admin/usuarios') ?>?' + params.toString();
}

// Enter para buscar
document.getElementById('search').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
