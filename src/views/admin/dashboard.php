<?php
/**
 * View - Admin Dashboard
 * Painel administrativo principal
 */

// Inclui o layout admin
require_once __DIR__ . '/layout/header.php';
?>

<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: var(--color-white);
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .stat-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .stat-icon.primary {
        background: rgba(6, 37, 61, 0.1);
        color: var(--color-primary);
    }

    .stat-icon.secondary {
        background: rgba(204, 140, 93, 0.1);
        color: var(--color-secondary);
    }

    .stat-icon.success {
        background: rgba(39, 174, 96, 0.1);
        color: #27ae60;
    }

    .stat-icon.warning {
        background: rgba(243, 156, 18, 0.1);
        color: #f39c12;
    }

    .stat-number {
        font-size: 32px;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 5px;
    }

    .stat-label {
        color: var(--color-text-light);
        font-size: 14px;
    }

    .content-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-bottom: 40px;
    }

    .card {
        background: var(--color-white);
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 25px;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--color-background);
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--color-primary);
    }

    .view-all {
        color: var(--color-secondary);
        font-size: 14px;
        text-decoration: none;
        font-weight: 600;
    }

    .view-all:hover {
        text-decoration: underline;
    }

    .appointment-item {
        display: flex;
        gap: 15px;
        padding: 15px;
        border-radius: 8px;
        background: var(--color-background);
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .appointment-item:hover {
        background: #e8ecef;
        transform: translateX(5px);
    }

    .appointment-icon {
        width: 50px;
        height: 50px;
        background: var(--color-secondary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        flex-shrink: 0;
    }

    .appointment-info {
        flex: 1;
    }

    .appointment-name {
        font-weight: 600;
        color: var(--color-primary);
        margin-bottom: 5px;
    }

    .appointment-details {
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

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-confirmed {
        background: #d1ecf1;
        color: #0c5460;
    }

    .activity-item {
        display: flex;
        gap: 15px;
        padding: 15px;
        border-left: 3px solid var(--color-secondary);
        background: var(--color-background);
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .activity-time {
        font-size: 12px;
        color: var(--color-text-light);
        white-space: nowrap;
    }

    .activity-description {
        font-size: 14px;
        color: var(--color-text);
        line-height: 1.6;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--color-text-light);
    }

    .empty-state-icon {
        font-size: 60px;
        margin-bottom: 15px;
        opacity: 0.3;
    }

    @media (max-width: 1024px) {
        .content-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <h1>Dashboard</h1>
    <p>Bem-vindo ao painel administrativo</p>
</div>

<!-- Statistics Cards -->
<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-number"><?= $stats['posts']['published'] ?? 0 ?></div>
                <div class="stat-label">Posts Publicados</div>
            </div>
            <div class="stat-icon primary">📝</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-number"><?= $stats['lawyers']['active'] ?? 0 ?></div>
                <div class="stat-label">Advogados Ativos</div>
            </div>
            <div class="stat-icon secondary">👨‍⚖️</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-number"><?= $stats['appointments']['pending'] ?? 0 ?></div>
                <div class="stat-label">Agendamentos Pendentes</div>
            </div>
            <div class="stat-icon warning">📅</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <div>
                <div class="stat-number"><?= $stats['lawyers']['cases_won'] ?? 0 ?></div>
                <div class="stat-label">Casos Ganhos</div>
            </div>
            <div class="stat-icon success">🏆</div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="content-row">
    <!-- Recent Appointments -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Agendamentos Recentes</h2>
            <a href="<?= base_url('admin/agendamentos') ?>" class="view-all">Ver todos →</a>
        </div>

        <?php if (!empty($recentAppointments)): ?>
            <?php foreach ($recentAppointments as $appointment): ?>
            <div class="appointment-item">
                <div class="appointment-icon">👤</div>
                <div class="appointment-info">
                    <div class="appointment-name"><?= $appointment['name'] ?></div>
                    <div class="appointment-details">
                        📅 <?= format_date($appointment['preferred_date']) ?> às <?= substr($appointment['preferred_time'], 0, 5) ?>
                        <br>
                        📧 <?= $appointment['email'] ?> | ☎ <?= format_phone($appointment['phone']) ?>
                        <br>
                        <span class="status-badge status-<?= $appointment['status'] ?>">
                            <?= ucfirst($appointment['status']) ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📅</div>
                <p>Nenhum agendamento recente</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Atividades Recentes</h2>
            <a href="<?= base_url('admin/logs') ?>" class="view-all">Ver todas →</a>
        </div>

        <?php if (!empty($recentActivities)): ?>
            <?php foreach (array_slice($recentActivities, 0, 5) as $activity): ?>
            <div class="activity-item">
                <div class="activity-time">
                    <?= format_datetime($activity['created_at'], 'd/m H:i') ?>
                </div>
                <div class="activity-description">
                    <strong><?= $activity['user_name'] ?? 'Sistema' ?>:</strong>
                    <?= $activity['description'] ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📊</div>
                <p>Nenhuma atividade recente</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Today's Appointments -->
<?php if (!empty($todayAppointments)): ?>
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Agendamentos de Hoje (<?= count($todayAppointments) ?>)</h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <?php foreach ($todayAppointments as $appointment): ?>
        <div class="appointment-item">
            <div class="appointment-icon">⏰</div>
            <div class="appointment-info">
                <div class="appointment-name"><?= $appointment['name'] ?></div>
                <div class="appointment-details">
                    🕐 <?= substr($appointment['preferred_time'], 0, 5) ?><br>
                    📋 <?= $appointment['consultation_type'] ?><br>
                    <span class="status-badge status-<?= $appointment['status'] ?>">
                        <?= ucfirst($appointment['status']) ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/layout/footer.php'; ?>