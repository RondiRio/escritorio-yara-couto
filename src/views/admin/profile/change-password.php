<?php
/**
 * View - Alterar Senha
 * Formulário de alteração de senha do usuário
 */

$pageTitle = $pageTitle ?? 'Alterar Senha';
$user = $user ?? [];

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

    .security-tips {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 20px;
        margin-bottom: 25px;
        border-radius: 4px;
    }

    .security-tips h3 {
        margin: 0 0 15px 0;
        font-size: 16px;
        font-weight: 600;
        color: #856404;
    }

    .security-tips ul {
        margin: 0;
        padding-left: 20px;
        color: #856404;
    }

    .security-tips li {
        margin-bottom: 8px;
        font-size: 14px;
    }

    .password-strength {
        height: 5px;
        background: #e9ecef;
        border-radius: 3px;
        margin-top: 8px;
        overflow: hidden;
    }

    .password-strength-bar {
        height: 100%;
        width: 0%;
        transition: all 0.3s ease;
        border-radius: 3px;
    }

    .strength-weak {
        width: 33%;
        background: #dc3545;
    }

    .strength-medium {
        width: 66%;
        background: #ffc107;
    }

    .strength-strong {
        width: 100%;
        background: #28a745;
    }

    .password-strength-text {
        font-size: 12px;
        margin-top: 5px;
        font-weight: 500;
    }
</style>

<div class="form-container">
    <div class="form-header">
        <h1 class="form-title">🔒 Alterar Senha</h1>
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

    <div class="security-tips">
        <h3>🛡️ Dicas de Segurança</h3>
        <ul>
            <li>Use uma senha forte com pelo menos 6 caracteres</li>
            <li>Combine letras maiúsculas, minúsculas, números e símbolos</li>
            <li>Não use informações pessoais óbvias (nome, data de nascimento, etc)</li>
            <li>Não reutilize senhas de outros sites</li>
            <li>Considere usar um gerenciador de senhas</li>
        </ul>
    </div>

    <form method="POST" action="<?= base_url('admin/perfil/alterar-senha') ?>" id="changePasswordForm">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="current_password">Senha Atual <span>*</span></label>
            <input
                type="password"
                class="form-control"
                id="current_password"
                name="current_password"
                required
                autofocus
            >
            <div class="form-help">Digite sua senha atual para confirmar a alteração</div>
        </div>

        <div class="form-group">
            <label for="new_password">Nova Senha <span>*</span></label>
            <input
                type="password"
                class="form-control"
                id="new_password"
                name="new_password"
                required
                minlength="6"
            >
            <div class="password-strength">
                <div class="password-strength-bar" id="strengthBar"></div>
            </div>
            <div class="password-strength-text" id="strengthText"></div>
            <div class="form-help">Mínimo 6 caracteres</div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirmar Nova Senha <span>*</span></label>
            <input
                type="password"
                class="form-control"
                id="confirm_password"
                name="confirm_password"
                required
                minlength="6"
            >
            <div class="form-help">Digite a nova senha novamente</div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">🔑 Alterar Senha</button>
            <a href="<?= base_url('admin/perfil') ?>" class="btn btn-secondary">❌ Cancelar</a>
        </div>
    </form>
</div>

<script>
// Validação de senha em tempo real
const newPasswordInput = document.getElementById('new_password');
const confirmPasswordInput = document.getElementById('confirm_password');
const strengthBar = document.getElementById('strengthBar');
const strengthText = document.getElementById('strengthText');
const form = document.getElementById('changePasswordForm');

// Verificador de força da senha
newPasswordInput.addEventListener('input', function() {
    const password = this.value;
    const strength = calculatePasswordStrength(password);

    // Atualiza barra visual
    strengthBar.className = 'password-strength-bar';
    strengthText.className = 'password-strength-text';

    if (password.length === 0) {
        strengthBar.className = 'password-strength-bar';
        strengthText.textContent = '';
    } else if (strength < 3) {
        strengthBar.classList.add('strength-weak');
        strengthText.textContent = 'Senha fraca';
        strengthText.style.color = '#dc3545';
    } else if (strength < 5) {
        strengthBar.classList.add('strength-medium');
        strengthText.textContent = 'Senha média';
        strengthText.style.color = '#ffc107';
    } else {
        strengthBar.classList.add('strength-strong');
        strengthText.textContent = 'Senha forte';
        strengthText.style.color = '#28a745';
    }
});

function calculatePasswordStrength(password) {
    let strength = 0;

    if (password.length >= 6) strength++;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z\d]/.test(password)) strength++;

    return strength;
}

// Validação ao submeter
form.addEventListener('submit', function(e) {
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = newPasswordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    // Verifica se preencheu todos
    if (!currentPassword || !newPassword || !confirmPassword) {
        e.preventDefault();
        alert('Por favor, preencha todos os campos');
        return;
    }

    // Verifica tamanho mínimo
    if (newPassword.length < 6) {
        e.preventDefault();
        alert('A nova senha deve ter no mínimo 6 caracteres');
        return;
    }

    // Verifica se senhas coincidem
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('As senhas não coincidem');
        confirmPasswordInput.focus();
        return;
    }

    // Verifica se nova senha é diferente da atual
    if (currentPassword === newPassword) {
        e.preventDefault();
        alert('A nova senha deve ser diferente da senha atual');
        newPasswordInput.focus();
        return;
    }
});

// Verificação visual em tempo real se as senhas coincidem
confirmPasswordInput.addEventListener('input', function() {
    if (this.value && this.value !== newPasswordInput.value) {
        this.style.borderColor = '#dc3545';
    } else {
        this.style.borderColor = '#28a745';
    }
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
