<?php
/**
 * View - Admin Usuários Create
 * Formulário de criação de usuário
 */

$pageTitle = $pageTitle ?? 'Novo Usuário';
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

require_once __DIR__ . '/../layout/header.php';
?>

<style>
    .form-container {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        max-width: 800px;
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

    .row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    @media (max-width: 768px) {
        .row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="form-container">
    <div class="form-header">
        <h1 class="form-title">➕ Novo Usuário</h1>
        <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-secondary">← Voltar</a>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <?php foreach ($_SESSION['flash'] as $type => $message): ?>
            <div class="alert alert-<?= $type ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; background: <?= $type === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $type === 'success' ? '#155724' : '#721c24' ?>;">
                <?= htmlspecialchars($message) ?>
            </div>
            <?php unset($_SESSION['flash'][$type]); ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('admin/usuarios/novo') ?>">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="name">Nome Completo <span>*</span></label>
            <input
                type="text"
                class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                id="name"
                name="name"
                value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                required
                autofocus
            >
            <?php if (isset($errors['name'])): ?>
                <div class="invalid-feedback"><?= $errors['name'][0] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">E-mail <span>*</span></label>
            <input
                type="email"
                class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                id="email"
                name="email"
                value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                required
            >
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback"><?= $errors['email'][0] ?></div>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="form-group">
                <label for="role">Função <span>*</span></label>
                <select class="form-control" id="role" name="role" required>
                    <option value="">Selecione...</option>
                    <option value="author" <?= ($old['role'] ?? '') === 'author' ? 'selected' : '' ?>>Autor</option>
                    <option value="editor" <?= ($old['role'] ?? '') === 'editor' ? 'selected' : '' ?>>Editor</option>
                    <option value="admin" <?= ($old['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                </select>
                <div class="form-help">
                    <strong>Autor:</strong> Pode criar posts<br>
                    <strong>Editor:</strong> Pode criar e editar posts de outros<br>
                    <strong>Admin:</strong> Acesso total ao sistema
                </div>
            </div>

            <div class="form-group">
                <label for="status">Status <span>*</span></label>
                <select class="form-control" id="status" name="status" required>
                    <option value="active" <?= ($old['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Ativo</option>
                    <option value="inactive" <?= ($old['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label for="password">Senha <span>*</span></label>
                <input
                    type="password"
                    class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                    id="password"
                    name="password"
                    required
                >
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback"><?= $errors['password'][0] ?></div>
                <?php endif; ?>
                <div class="form-help">Mínimo 6 caracteres</div>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmar Senha <span>*</span></label>
                <input
                    type="password"
                    class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>"
                    id="password_confirm"
                    name="password_confirm"
                    required
                >
                <?php if (isset($errors['password_confirm'])): ?>
                    <div class="invalid-feedback"><?= $errors['password_confirm'][0] ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Salvar Usuário</button>
            <a href="<?= base_url('admin/usuarios') ?>" class="btn btn-secondary">❌ Cancelar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
