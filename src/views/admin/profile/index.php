<?php
/**
 * View - Meu Perfil
 * Página principal do perfil do usuário logado
 */

$pageTitle = $pageTitle ?? 'Meu Perfil';
$user = $user ?? [];
$recentActivities = $recentActivities ?? [];

require_once __DIR__ . '/../layout/header.php';
?>

<style>
    .profile-container {
        max-width: 1200px;
    }

    .profile-header {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 30px;
    }

    .avatar-section {
        position: relative;
    }

    .avatar-image {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--color-primary);
    }

    .avatar-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: var(--color-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        font-weight: bold;
        color: white;
        border: 4px solid var(--color-primary);
    }

    .avatar-upload-btn {
        position: absolute;
        bottom: 0;
        right: 0;
        background: var(--color-primary);
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        transition: var(--transition);
    }

    .avatar-upload-btn:hover {
        background: #04192b;
        transform: scale(1.1);
    }

    .profile-info {
        flex: 1;
    }

    .profile-name {
        font-size: 28px;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 5px;
    }

    .profile-email {
        font-size: 16px;
        color: var(--color-text-light);
        margin-bottom: 10px;
    }

    .profile-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        margin-right: 10px;
    }

    .badge-admin {
        background: #dc3545;
        color: white;
    }

    .badge-editor {
        background: #ffc107;
        color: #000;
    }

    .badge-author {
        background: #17a2b8;
        color: white;
    }

    .badge-active {
        background: #28a745;
        color: white;
    }

    .badge-inactive {
        background: #6c757d;
        color: white;
    }

    .profile-actions {
        display: flex;
        gap: 15px;
        margin-top: 15px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        border: none;
        cursor: pointer;
        font-size: 14px;
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

    .profile-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }

    .profile-card {
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
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 500;
        color: var(--color-text);
    }

    .info-value {
        color: var(--color-text-light);
    }

    .activity-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .activity-item {
        padding: 15px;
        border-left: 3px solid var(--color-primary);
        background: #f8f9fa;
        margin-bottom: 10px;
        border-radius: 0 6px 6px 0;
    }

    .activity-action {
        font-weight: 600;
        color: var(--color-primary);
        margin-bottom: 5px;
    }

    .activity-description {
        font-size: 14px;
        color: var(--color-text);
        margin-bottom: 5px;
    }

    .activity-time {
        font-size: 12px;
        color: var(--color-text-light);
    }

    .no-activities {
        text-align: center;
        padding: 40px;
        color: var(--color-text-light);
    }

    @media (max-width: 768px) {
        .profile-header {
            flex-direction: column;
            text-align: center;
        }

        .profile-grid {
            grid-template-columns: 1fr;
        }

        .profile-actions {
            flex-direction: column;
        }
    }

    #avatarInput {
        display: none;
    }
</style>

<div class="profile-container">
    <?php if (isset($_SESSION['flash'])): ?>
        <?php foreach ($_SESSION['flash'] as $type => $message): ?>
            <div class="alert alert-<?= $type ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; background: <?= $type === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $type === 'success' ? '#155724' : '#721c24' ?>;">
                <?= htmlspecialchars($message) ?>
            </div>
            <?php unset($_SESSION['flash'][$type]); ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Cabeçalho do Perfil -->
    <div class="profile-header">
        <div class="avatar-section">
            <?php if (!empty($user['avatar'])): ?>
                <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="avatar-image" id="avatarPreview">
            <?php else: ?>
                <div class="avatar-placeholder" id="avatarPreview">
                    <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                </div>
            <?php endif; ?>
            <button type="button" class="avatar-upload-btn" onclick="document.getElementById('avatarInput').click()" title="Alterar foto">
                📸
            </button>
            <input type="file" id="avatarInput" accept="image/jpeg,image/png,image/jpg" onchange="uploadAvatar(this)">
        </div>

        <div class="profile-info">
            <h1 class="profile-name"><?= htmlspecialchars($user['name'] ?? '') ?></h1>
            <p class="profile-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>

            <div>
                <span class="profile-badge badge-<?= $user['role'] ?? 'author' ?>">
                    <?php
                    $roles = ['admin' => 'Administrador', 'editor' => 'Editor', 'author' => 'Autor'];
                    echo $roles[$user['role']] ?? 'Autor';
                    ?>
                </span>
                <span class="profile-badge badge-<?= $user['status'] ?? 'active' ?>">
                    <?= ($user['status'] ?? 'active') === 'active' ? 'Ativo' : 'Inativo' ?>
                </span>
            </div>

            <div class="profile-actions">
                <a href="<?= base_url('admin/perfil/editar') ?>" class="btn btn-primary">✏️ Editar Perfil</a>
                <a href="<?= base_url('admin/perfil/alterar-senha') ?>" class="btn btn-secondary">🔒 Alterar Senha</a>
            </div>
        </div>
    </div>

    <!-- Grid de Informações -->
    <div class="profile-grid">
        <!-- Informações da Conta -->
        <div class="profile-card">
            <h2 class="card-title">📋 Informações da Conta</h2>

            <div class="info-row">
                <span class="info-label">ID do Usuário:</span>
                <span class="info-value">#<?= $user['id'] ?? '' ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">Criado em:</span>
                <span class="info-value"><?= date('d/m/Y H:i', strtotime($user['created_at'] ?? 'now')) ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">Última atualização:</span>
                <span class="info-value"><?= date('d/m/Y H:i', strtotime($user['updated_at'] ?? 'now')) ?></span>
            </div>

            <?php if (!empty($user['last_login_at'])): ?>
            <div class="info-row">
                <span class="info-label">Último acesso:</span>
                <span class="info-value"><?= date('d/m/Y H:i', strtotime($user['last_login_at'])) ?></span>
            </div>
            <?php endif; ?>

            <?php if (!empty($user['last_login_ip'])): ?>
            <div class="info-row">
                <span class="info-label">IP do último acesso:</span>
                <span class="info-value"><?= htmlspecialchars($user['last_login_ip']) ?></span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Atividades Recentes -->
        <div class="profile-card">
            <h2 class="card-title">🕒 Atividades Recentes</h2>

            <?php if (!empty($recentActivities)): ?>
                <ul class="activity-list">
                    <?php foreach (array_slice($recentActivities, 0, 5) as $activity): ?>
                        <li class="activity-item">
                            <div class="activity-action">
                                <?php
                                $actions = [
                                    'login' => '🔐 Login realizado',
                                    'logout' => '🚪 Logout',
                                    'profile_updated' => '✏️ Perfil atualizado',
                                    'password_changed' => '🔑 Senha alterada',
                                    'avatar_updated' => '📸 Avatar atualizado',
                                    'post_created' => '📝 Post criado',
                                    'post_updated' => '📝 Post atualizado',
                                    'post_deleted' => '🗑️ Post deletado'
                                ];
                                echo $actions[$activity['action']] ?? htmlspecialchars($activity['action']);
                                ?>
                            </div>
                            <?php if (!empty($activity['description'])): ?>
                                <div class="activity-description"><?= htmlspecialchars($activity['description']) ?></div>
                            <?php endif; ?>
                            <div class="activity-time"><?= date('d/m/Y H:i:s', strtotime($activity['created_at'])) ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="no-activities">
                    <p>Nenhuma atividade recente registrada</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function uploadAvatar(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Validações
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!allowedTypes.includes(file.type)) {
            alert('Tipo de arquivo não permitido. Use JPG ou PNG');
            return;
        }

        const maxSize = 2 * 1024 * 1024; // 2MB
        if (file.size > maxSize) {
            alert('Arquivo muito grande. Máximo: 2MB');
            return;
        }

        // Preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'avatar-image';
                img.id = 'avatarPreview';
                preview.parentNode.replaceChild(img, preview);
            }
        };
        reader.readAsDataURL(file);

        // Upload
        const formData = new FormData();
        formData.append('avatar', file);
        formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?? '' ?>');

        fetch('<?= base_url('admin/perfil/upload-avatar') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.error || 'Erro ao fazer upload');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao fazer upload do avatar');
        });
    }
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
