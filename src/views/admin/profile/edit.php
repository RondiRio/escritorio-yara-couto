<?php
/**
 * View - Editar Perfil
 * Formulário de edição de dados do perfil
 */

$pageTitle = $pageTitle ?? 'Editar Perfil';
$user = $user ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

require_once __DIR__ . '/../layout/header.php';
?>

<style>
    .form-container {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        max-width: 700px;
    }

    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--color-border);
    }

    .form-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--color-primary);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--color-text);
    }

    .form-group label span {
        color: #dc3545;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid var(--color-border);
        border-radius: 6px;
        font-size: 15px;
        transition: var(--transition);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(6, 37, 61, 0.1);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
    }

    .form-help {
        font-size: 13px;
        color: var(--color-text-light);
        margin-top: 5px;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--color-border);
    }

    .btn {
        padding: 12px 24px;
        border-radius: 6px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        border: none;
        cursor: pointer;
        font-size: 15px;
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

    .info-box {
        background: #e7f3ff;
        border-left: 4px solid var(--color-primary);
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .info-box p {
        margin: 0;
        color: var(--color-text);
        font-size: 14px;
    }
</style>

<div class="form-container">
    <div class="form-header">
        <h1 class="form-title">✏️ Editar Perfil</h1>
        <a href="<?= base_url('admin/perfil') ?>" class="btn btn-secondary">← Voltar</a>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <?php foreach ($_SESSION['flash'] as $type => $message): ?>
            <div class="alert alert-<?= $type ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; background: <?= $type === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $type === 'success' ? '#155724' : '#721c24' ?>;">
                <?= htmlspecialchars($message) ?>
            </div>
            <?php unset($_SESSION['flash'][$type]); ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="info-box">
        <p>💡 <strong>Dica:</strong> Altere apenas as informações que deseja atualizar. Sua senha permanecerá a mesma.</p>
    </div>

    <form method="POST" action="<?= base_url('admin/perfil/atualizar') ?>">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="name">Nome Completo <span>*</span></label>
            <input
                type="text"
                class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                id="name"
                name="name"
                value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                required
                autofocus
            >
            <?php if (isset($errors['name'])): ?>
                <div class="invalid-feedback"><?= $errors['name'][0] ?></div>
            <?php endif; ?>
            <div class="form-help">Mínimo 3 caracteres</div>
        </div>

        <div class="form-group">
            <label for="email">E-mail <span>*</span></label>
            <input
                type="email"
                class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                id="email"
                name="email"
                value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                required
            >
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback"><?= $errors['email'][0] ?></div>
            <?php endif; ?>
            <div class="form-help">Este e-mail será usado para login no sistema</div>
        </div>

        <div class="form-group">
            <label>Função</label>
            <input
                type="text"
                class="form-control"
                value="<?php
                    $roles = ['admin' => 'Administrador', 'editor' => 'Editor', 'author' => 'Autor'];
                    echo $roles[$user['role']] ?? 'Autor';
                ?>"
                readonly
                disabled
            >
            <div class="form-help">Apenas administradores podem alterar funções de usuários</div>
        </div>

        <div class="form-group">
            <label>Status da Conta</label>
            <input
                type="text"
                class="form-control"
                value="<?= ($user['status'] ?? 'active') === 'active' ? 'Ativo' : 'Inativo' ?>"
                readonly
                disabled
            >
            <div class="form-help">Entre em contato com um administrador para alterar o status</div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Salvar Alterações</button>
            <a href="<?= base_url('admin/perfil') ?>" class="btn btn-secondary">❌ Cancelar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
