<?php
/**
 * View - Admin Agendamento Show
 * Visualização detalhada de um agendamento
 */

$pageTitle = $pageTitle ?? 'Detalhes do Agendamento';
$appointment = $appointment ?? [];

require_once __DIR__ . '/../layout/header.php';
?>

<style>
    .appointment-container {
        max-width: 1000px;
    }

    .appointment-header {
        background: white;
        padding: 25px 30px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-info h1 {
        font-size: 28px;
        font-weight: 700;
        color: var(--color-primary);
        margin: 0 0 10px 0;
    }

    .header-status {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
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

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    .card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .card-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--color-primary);
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--color-border);
    }

    .info-row {
        display: flex;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 500;
        color: var(--color-text);
        min-width: 150px;
    }

    .info-value {
        color: var(--color-text-light);
        flex: 1;
    }

    .actions-card {
        position: sticky;
        top: 20px;
    }

    .action-group {
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--color-border);
    }

    .action-group:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .action-group h3 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
        color: var(--color-text);
    }

    .btn {
        width: 100%;
        padding: 12px 20px;
        border-radius: 6px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: var(--transition);
        border: none;
        cursor: pointer;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #545b62;
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #218838;
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

    .btn-warning {
        background: #ffc107;
        color: #000;
    }

    .btn-warning:hover {
        background: #e0a800;
    }

    .message-box {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid var(--color-primary);
        margin-top: 10px;
    }

    .message-box h4 {
        margin: 0 0 10px 0;
        font-size: 14px;
        font-weight: 600;
        color: var(--color-primary);
    }

    .message-box p {
        margin: 0;
        font-size: 14px;
        color: var(--color-text);
        white-space: pre-wrap;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        align-items: center;
        justify-content: center;
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        background: white;
        padding: 30px;
        border-radius: 10px;
        max-width: 500px;
        width: 90%;
    }

    .modal-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        color: var(--color-primary);
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid var(--color-border);
        border-radius: 6px;
        font-size: 14px;
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .actions-card {
            position: static;
        }
    }
</style>

<div class="appointment-container">
    <?php if (isset($_SESSION['flash'])): ?>
        <?php foreach ($_SESSION['flash'] as $type => $message): ?>
            <div class="alert alert-<?= $type ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; background: <?= $type === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $type === 'success' ? '#155724' : '#721c24' ?>;">
                <?= htmlspecialchars($message) ?>
            </div>
            <?php unset($_SESSION['flash'][$type]); ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Cabeçalho -->
    <div class="appointment-header">
        <div class="header-info">
            <h1>📅 Agendamento #<?= $appointment['id'] ?></h1>
            <p style="margin: 0; color: #666;">
                Criado em <?= date('d/m/Y H:i', strtotime($appointment['created_at'])) ?>
            </p>
        </div>
        <div class="header-status">
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
            <a href="<?= base_url('admin/agendamentos') ?>" class="btn btn-secondary" style="width: auto; margin: 0;">
                ← Voltar
            </a>
        </div>
    </div>

    <!-- Grid de Conteúdo -->
    <div class="content-grid">
        <!-- Detalhes do Agendamento -->
        <div>
            <div class="card">
                <h2 class="card-title">👤 Informações do Cliente</h2>

                <div class="info-row">
                    <span class="info-label">Nome:</span>
                    <span class="info-value"><strong><?= htmlspecialchars($appointment['name']) ?></strong></span>
                </div>

                <div class="info-row">
                    <span class="info-label">E-mail:</span>
                    <span class="info-value">
                        <a href="mailto:<?= htmlspecialchars($appointment['email']) ?>">
                            <?= htmlspecialchars($appointment['email']) ?>
                        </a>
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Telefone:</span>
                    <span class="info-value">
                        <a href="tel:<?= htmlspecialchars($appointment['phone']) ?>">
                            <?= htmlspecialchars($appointment['phone']) ?>
                        </a>
                    </span>
                </div>

                <?php if (!empty($appointment['whatsapp'])): ?>
                <div class="info-row">
                    <span class="info-label">WhatsApp:</span>
                    <span class="info-value">
                        <a href="https://wa.me/<?= htmlspecialchars($appointment['whatsapp']) ?>" target="_blank">
                            <?= htmlspecialchars($appointment['whatsapp']) ?>
                        </a>
                    </span>
                </div>
                <?php endif; ?>
            </div>

            <div class="card" style="margin-top: 20px;">
                <h2 class="card-title">📋 Detalhes do Agendamento</h2>

                <div class="info-row">
                    <span class="info-label">Data Preferida:</span>
                    <span class="info-value">
                        <strong><?= date('d/m/Y', strtotime($appointment['preferred_date'])) ?></strong>
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Horário Preferido:</span>
                    <span class="info-value"><strong><?= htmlspecialchars($appointment['preferred_time']) ?></strong></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Tipo de Consulta:</span>
                    <span class="info-value"><?= htmlspecialchars($appointment['consultation_type']) ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="badge badge-<?= $appointment['status'] ?>">
                            <?= $statusLabels[$appointment['status']] ?? $appointment['status'] ?>
                        </span>
                    </span>
                </div>

                <?php if (!empty($appointment['confirmed_at'])): ?>
                <div class="info-row">
                    <span class="info-label">Confirmado em:</span>
                    <span class="info-value"><?= date('d/m/Y H:i', strtotime($appointment['confirmed_at'])) ?></span>
                </div>
                <?php endif; ?>

                <?php if (!empty($appointment['completed_at'])): ?>
                <div class="info-row">
                    <span class="info-label">Completado em:</span>
                    <span class="info-value"><?= date('d/m/Y H:i', strtotime($appointment['completed_at'])) ?></span>
                </div>
                <?php endif; ?>

                <?php if (!empty($appointment['cancelled_at'])): ?>
                <div class="info-row">
                    <span class="info-label">Cancelado em:</span>
                    <span class="info-value"><?= date('d/m/Y H:i', strtotime($appointment['cancelled_at'])) ?></span>
                </div>
                <?php endif; ?>

                <?php if (!empty($appointment['message'])): ?>
                <div class="message-box">
                    <h4>💬 Mensagem do Cliente:</h4>
                    <p><?= nl2br(htmlspecialchars($appointment['message'])) ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($appointment['admin_notes'])): ?>
                <div class="message-box">
                    <h4>📝 Notas do Administrador:</h4>
                    <p><?= nl2br(htmlspecialchars($appointment['admin_notes'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ações -->
        <div class="actions-card card">
            <h2 class="card-title">⚡ Ações Rápidas</h2>

            <?php if ($appointment['status'] === 'pending'): ?>
                <div class="action-group">
                    <h3>Confirmar Agendamento</h3>
                    <button onclick="openConfirmModal()" class="btn btn-success">
                        ✅ Confirmar
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($appointment['status'] === 'confirmed'): ?>
                <div class="action-group">
                    <h3>Marcar como Completado</h3>
                    <button onclick="openCompleteModal()" class="btn btn-info">
                        ✔️ Completar
                    </button>
                </div>
            <?php endif; ?>

            <?php if (in_array($appointment['status'], ['pending', 'confirmed'])): ?>
                <div class="action-group">
                    <h3>Cancelar Agendamento</h3>
                    <button onclick="openCancelModal()" class="btn btn-warning">
                        ❌ Cancelar
                    </button>
                </div>
            <?php endif; ?>

            <div class="action-group">
                <h3>Outras Ações</h3>
                <button onclick="openNotesModal()" class="btn btn-secondary">
                    📝 Adicionar Notas
                </button>
                <?php if ($appointment['status'] !== 'completed'): ?>
                <button onclick="confirmDelete()" class="btn btn-danger">
                    🗑️ Excluir
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Confirmar Agendamento -->
<div id="confirmModal" class="modal">
    <div class="modal-content">
        <h3 class="modal-title">✅ Confirmar Agendamento</h3>
        <form method="POST" action="<?= base_url('admin/agendamentos/' . $appointment['id'] . '/confirmar') ?>">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="admin_notes_confirm">Notas (opcional):</label>
                <textarea id="admin_notes_confirm" name="admin_notes" class="form-control" rows="3" placeholder="Adicione observações sobre a confirmação..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="submit" class="btn btn-success" style="flex: 1;">Confirmar</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('confirmModal')" style="flex: 1;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Completar Agendamento -->
<div id="completeModal" class="modal">
    <div class="modal-content">
        <h3 class="modal-title">✔️ Completar Agendamento</h3>
        <form method="POST" action="<?= base_url('admin/agendamentos/' . $appointment['id'] . '/completar') ?>">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="admin_notes_complete">Notas (opcional):</label>
                <textarea id="admin_notes_complete" name="admin_notes" class="form-control" rows="3" placeholder="Adicione observações sobre o atendimento..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="submit" class="btn btn-info" style="flex: 1;">Completar</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('completeModal')" style="flex: 1;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Cancelar Agendamento -->
<div id="cancelModal" class="modal">
    <div class="modal-content">
        <h3 class="modal-title">❌ Cancelar Agendamento</h3>
        <form method="POST" action="<?= base_url('admin/agendamentos/' . $appointment['id'] . '/cancelar') ?>">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="reason">Motivo do cancelamento:</label>
                <textarea id="reason" name="reason" class="form-control" rows="3" placeholder="Informe o motivo do cancelamento..." required></textarea>
            </div>
            <div class="modal-actions">
                <button type="submit" class="btn btn-warning" style="flex: 1;">Cancelar Agendamento</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('cancelModal')" style="flex: 1;">Voltar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Adicionar Notas -->
<div id="notesModal" class="modal">
    <div class="modal-content">
        <h3 class="modal-title">📝 Adicionar Notas</h3>
        <form id="notesForm">
            <div class="form-group">
                <label for="notes">Notas:</label>
                <textarea id="notes" name="notes" class="form-control" rows="4" placeholder="Digite suas notas aqui..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" onclick="saveNotes()" class="btn btn-success" style="flex: 1;">Salvar</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('notesModal')" style="flex: 1;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function openConfirmModal() {
    document.getElementById('confirmModal').classList.add('show');
}

function openCompleteModal() {
    document.getElementById('completeModal').classList.add('show');
}

function openCancelModal() {
    document.getElementById('cancelModal').classList.add('show');
}

function openNotesModal() {
    document.getElementById('notesModal').classList.add('show');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
}

function saveNotes() {
    const notes = document.getElementById('notes').value;

    if (!notes.trim()) {
        alert('Por favor, digite alguma nota');
        return;
    }

    fetch('<?= base_url('admin/agendamentos/' . $appointment['id'] . '/adicionar-notas') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'notes=' + encodeURIComponent(notes) + '&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Notas adicionadas com sucesso!');
            location.reload();
        } else {
            alert('Erro ao adicionar notas: ' + (data.error || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        alert('Erro ao adicionar notas');
        console.error('Erro:', error);
    });
}

function confirmDelete() {
    if (confirm('Tem certeza que deseja excluir este agendamento? Esta ação não pode ser desfeita.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('admin/agendamentos/' . $appointment['id'] . '/deletar') ?>';

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = '<?= $_SESSION['csrf_token'] ?? '' ?>';

        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Fecha modal ao clicar fora
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('show');
    }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
