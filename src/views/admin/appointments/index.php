<?php
/**
 * View - Admin Agendamentos Index
 * Listagem de agendamentos com filtros e estatísticas
 */

$pageTitle = $pageTitle ?? 'Gerenciar Agendamentos';
$appointments = $appointments ?? [];
$stats = $stats ?? [];
$currentStatus = $currentStatus ?? null;

require_once __DIR__ . '/../layout/header.php';
?>

<style>
    .appointments-header {
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
        cursor: pointer;
        transition: var(--transition);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .stat-card.pending {
        border-left-color: #ffc107;
    }

    .stat-card.confirmed {
        border-left-color: #28a745;
    }

    .stat-card.completed {
        border-left-color: #17a2b8;
    }

    .stat-card.cancelled {
        border-left-color: #dc3545;
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

    .filter-bar {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-btn {
        padding: 8px 16px;
        border: 1px solid var(--color-border);
        background: white;
        border-radius: 6px;
        cursor: pointer;
        transition: var(--transition);
        font-size: 14px;
        text-decoration: none;
        color: var(--color-text);
    }

    .filter-btn:hover {
        background: #f8f9fa;
    }

    .filter-btn.active {
        background: var(--color-primary);
        color: white;
        border-color: var(--color-primary);
    }

    .appointments-table {
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
    }

    .badge-pending {
        background: #fff3cd;
        color: #856404;
    }

    .badge-confirmed {
        background: #d4edda;
        color: #155724;
    }

    .badge-completed {
        background: #d1ecf1;
        color: #0c5460;
    }

    .badge-cancelled {
        background: #f8d7da;
        color: #721c24;
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

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            min-width: 800px;
        }
    }
</style>

<div class="appointments-header">
    <h1 class="page-title">📅 Gerenciar Agendamentos</h1>
    <a href="<?= base_url('admin/agendamentos/exportar') ?>" class="btn btn-success">
        📥 Exportar CSV
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
    <div class="stat-card" onclick="window.location.href='<?= base_url('admin/agendamentos') ?>'">
        <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
        <div class="stat-label">Total de Agendamentos</div>
    </div>

    <div class="stat-card pending" onclick="window.location.href='<?= base_url('admin/agendamentos?status=pending') ?>'">
        <div class="stat-value"><?= $stats['pending'] ?? 0 ?></div>
        <div class="stat-label">Pendentes</div>
    </div>

    <div class="stat-card confirmed" onclick="window.location.href='<?= base_url('admin/agendamentos?status=confirmed') ?>'">
        <div class="stat-value"><?= $stats['confirmed'] ?? 0 ?></div>
        <div class="stat-label">Confirmados</div>
    </div>

    <div class="stat-card completed" onclick="window.location.href='<?= base_url('admin/agendamentos?status=completed') ?>'">
        <div class="stat-value"><?= $stats['completed'] ?? 0 ?></div>
        <div class="stat-label">Completados</div>
    </div>

    <div class="stat-card cancelled" onclick="window.location.href='<?= base_url('admin/agendamentos?status=cancelled') ?>'">
        <div class="stat-value"><?= $stats['cancelled'] ?? 0 ?></div>
        <div class="stat-label">Cancelados</div>
    </div>
</div>

<!-- Filtros -->
<div class="filter-bar">
    <strong>Filtrar por Status:</strong>
    <a href="<?= base_url('admin/agendamentos') ?>" class="filter-btn <?= !$currentStatus ? 'active' : '' ?>">
        Todos
    </a>
    <a href="<?= base_url('admin/agendamentos?status=pending') ?>" class="filter-btn <?= $currentStatus === 'pending' ? 'active' : '' ?>">
        Pendentes
    </a>
    <a href="<?= base_url('admin/agendamentos?status=confirmed') ?>" class="filter-btn <?= $currentStatus === 'confirmed' ? 'active' : '' ?>">
        Confirmados
    </a>
    <a href="<?= base_url('admin/agendamentos?status=completed') ?>" class="filter-btn <?= $currentStatus === 'completed' ? 'active' : '' ?>">
        Completados
    </a>
    <a href="<?= base_url('admin/agendamentos?status=cancelled') ?>" class="filter-btn <?= $currentStatus === 'cancelled' ? 'active' : '' ?>">
        Cancelados
    </a>
</div>

<!-- Tabela de Agendamentos -->
<div class="appointments-table">
    <?php if (!empty($appointments)): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Contato</th>
                        <th>Data/Hora</th>
                        <th>Tipo de Consulta</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><strong>#<?= $appointment['id'] ?></strong></td>
                            <td>
                                <div style="font-weight: 500;"><?= htmlspecialchars($appointment['name']) ?></div>
                            </td>
                            <td>
                                <div style="font-size: 13px;">
                                    📧 <?= htmlspecialchars($appointment['email']) ?><br>
                                    📱 <?= htmlspecialchars($appointment['phone']) ?>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 500;">
                                    <?= date('d/m/Y', strtotime($appointment['preferred_date'])) ?>
                                </div>
                                <div style="font-size: 13px; color: #666;">
                                    <?= htmlspecialchars($appointment['preferred_time']) ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($appointment['consultation_type']) ?></td>
                            <td>
                                <span class="badge badge-<?= $appointment['status'] ?>">
                                    <?php
                                    $statusLabels = [
                                        'pending' => 'Pendente',
                                        'confirmed' => 'Confirmado',
                                        'completed' => 'Completado',
                                        'cancelled' => 'Cancelado'
                                    ];
                                    echo $statusLabels[$appointment['status']] ?? $appointment['status'];
                                    ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($appointment['created_at'])) ?></td>
                            <td>
                                <a href="<?= base_url('admin/agendamentos/' . $appointment['id']) ?>" class="btn btn-primary btn-sm">
                                    👁️ Ver
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">📭</div>
            <h3>Nenhum agendamento encontrado</h3>
            <p>
                <?php if ($currentStatus): ?>
                    Não há agendamentos com o status "<?= $statusLabels[$currentStatus] ?? $currentStatus ?>"
                <?php else: ?>
                    Ainda não há agendamentos cadastrados no sistema
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
