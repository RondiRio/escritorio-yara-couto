<?php
/**
 * View - Admin Lawyers Index
 * Lista de advogados
 */

require_once __DIR__ . '/../layout/header.php';
?>

<style>
    .page-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
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

    .btn-info {
        background: #17a2b8;
        color: white;
    }

    .btn-info:hover {
        background: #138496;
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

    .btn-warning {
        background: #ffc107;
        color: #212529;
    }

    .lawyers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
    }

    .lawyer-card {
        background: var(--color-white);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: var(--transition);
    }

    .lawyer-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .lawyer-header {
        height: 200px;
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .lawyer-photo {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 5px solid var(--color-white);
        overflow: hidden;
        background: var(--color-white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 60px;
        color: var(--color-secondary);
    }

    .lawyer-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .status-badge-overlay {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-active {
        background: #28a745;
        color: white;
    }

    .status-inactive {
        background: #6c757d;
        color: white;
    }

    .lawyer-body {
        padding: 25px;
    }

    .lawyer-name {
        font-size: 20px;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 5px;
        text-align: center;
    }

    .lawyer-oab {
        text-align: center;
        color: var(--color-secondary);
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 14px;
    }

    .lawyer-specialties {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        justify-content: center;
        margin-bottom: 15px;
        min-height: 30px;
    }

    .specialty-tag {
        background: var(--color-background);
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 11px;
        color: var(--color-text);
    }

    .lawyer-stats {
        display: flex;
        justify-content: space-around;
        padding: 15px 0;
        margin: 15px 0;
        border-top: 1px solid #e0e0e0;
        border-bottom: 1px solid #e0e0e0;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 20px;
        font-weight: 700;
        color: var(--color-secondary);
    }

    .stat-label {
        font-size: 11px;
        color: var(--color-text-light);
        text-transform: uppercase;
    }

    .lawyer-actions {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: var(--color-text-light);
        background: var(--color-white);
        border-radius: 10px;
    }

    .empty-state-icon {
        font-size: 100px;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    @media (max-width: 768px) {
        .lawyers-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <h1>Gerenciar Advogados</h1>
    <p>Cadastre e gerencie os advogados do escritório</p>
</div>

<!-- Page Actions -->
<div class="page-actions">
    <div style="color: var(--color-text-light);">
        Total: <strong><?= count($lawyers) ?></strong> advogado(s)
    </div>
    <a href="<?= base_url('admin/advogados/criar') ?>" class="btn btn-primary">
        ➕ Cadastrar Advogado
    </a>
</div>

<!-- Lawyers Grid -->
<?php if (!empty($lawyers) && count($lawyers) > 0): ?>
<div class="lawyers-grid">
    <?php foreach ($lawyers as $lawyer): ?>
    <div class="lawyer-card">
        <!-- Header com Foto -->
        <div class="lawyer-header">
            <div class="lawyer-photo">
                <?php if (!empty($lawyer['photo'])): ?>
                    <img src="<?= asset('images/advogados/' . $lawyer['photo']) ?>" 
                         alt="<?= $lawyer['name'] ?>">
                <?php else: ?>
                    👤
                <?php endif; ?>
            </div>
            <span class="status-badge-overlay status-<?= $lawyer['status'] ?>">
                <?= $lawyer['status'] === 'active' ? '✓ Ativo' : '⏸ Inativo' ?>
            </span>
        </div>

        <!-- Body -->
        <div class="lawyer-body">
            <h3 class="lawyer-name"><?= $lawyer['name'] ?></h3>
            <div class="lawyer-oab">OAB <?= $lawyer['oab_number'] ?>/<?= $lawyer['oab_state'] ?></div>

            <!-- Especialidades -->
            <div class="lawyer-specialties">
                <?php 
                if (!empty($lawyer['specialties'])) {
                    $specialties = explode(',', $lawyer['specialties']);
                    foreach (array_slice($specialties, 0, 3) as $specialty) {
                        echo '<span class="specialty-tag">' . trim($specialty) . '</span>';
                    }
                }
                ?>
            </div>

            <!-- Stats -->
            <div class="lawyer-stats">
                <div class="stat-item">
                    <div class="stat-number"><?= $lawyer['cases_won'] ?? 0 ?></div>
                    <div class="stat-label">Casos</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?= $lawyer['display_order'] ?></div>
                    <div class="stat-label">Ordem</div>
                </div>
            </div>

            <!-- Actions -->
            <div class="lawyer-actions">
                <a href="<?= base_url('admin/advogados/' . $lawyer['id']) ?>" 
                   class="btn btn-info btn-sm"
                   title="Visualizar">
                    👁️
                </a>
                <a href="<?= base_url('admin/advogados/' . $lawyer['id'] . '/editar') ?>" 
                   class="btn btn-primary btn-sm"
                   title="Editar">
                    ✏️
                </a>
                <form action="<?= base_url('admin/advogados/' . $lawyer['id'] . '/deletar') ?>" 
                      method="POST" 
                      style="display: inline;"
                      onsubmit="return confirm('Tem certeza que deseja excluir este advogado?')">
                    <?= csrf_field() ?>
                    <button type="submit" 
                            class="btn btn-danger btn-sm"
                            title="Excluir">
                        🗑️
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<!-- Empty State -->
<div class="empty-state">
    <div class="empty-state-icon">👨‍⚖️</div>
    <h3>Nenhum advogado cadastrado</h3>
    <p>Comece cadastrando o primeiro advogado do escritório</p>
    <br>
    <a href="<?= base_url('admin/advogados/criar') ?>" class="btn btn-primary">
        Cadastrar Primeiro Advogado
    </a>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>