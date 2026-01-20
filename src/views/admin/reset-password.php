<?php
/**
 * View - Redefinir Senha
 * Formulário para definir nova senha usando token de recuperação
 */

$pageTitle = $pageTitle ?? 'Redefinir Senha';
$token = $token ?? '';
$email = $email ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-logo {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .auth-title {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .auth-subtitle {
            color: #718096;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2d3748;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-help {
            font-size: 13px;
            color: #718096;
            margin-top: 8px;
            line-height: 1.5;
        }

        .password-strength {
            height: 5px;
            background: #e2e8f0;
            border-radius: 3px;
            margin-top: 10px;
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

        .btn {
            width: 100%;
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .security-tips {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .security-tips h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: 600;
            color: #856404;
        }

        .security-tips ul {
            margin: 0;
            padding-left: 20px;
            color: #856404;
        }

        .security-tips li {
            margin-bottom: 6px;
            font-size: 12px;
            line-height: 1.4;
        }

        .user-info {
            background: #e6fffa;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            color: #234e52;
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 30px 20px;
            }

            .auth-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <div class="auth-logo">🔑</div>
            <h1 class="auth-title">Redefinir Senha</h1>
            <p class="auth-subtitle">
                Crie uma nova senha forte e segura<br>
                para proteger sua conta.
            </p>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                <div class="alert alert-<?= $type ?>">
                    <?= $message ?>
                </div>
                <?php unset($_SESSION['flash'][$type]); ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($email)): ?>
        <div class="user-info">
            📧 Redefinindo senha para: <strong><?= htmlspecialchars($email) ?></strong>
        </div>
        <?php endif; ?>

        <div class="security-tips">
            <h3>🛡️ Sua senha deve ter:</h3>
            <ul>
                <li>No mínimo 6 caracteres (ideal: 8 ou mais)</li>
                <li>Letras maiúsculas e minúsculas</li>
                <li>Números e símbolos especiais</li>
                <li>Não use informações pessoais óbvias</li>
            </ul>
        </div>

        <form method="POST" action="<?= base_url('admin/redefinir-senha') ?>" id="resetForm">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="form-group">
                <label for="password">Nova Senha</label>
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    required
                    minlength="6"
                    autofocus
                >
                <div class="password-strength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                </div>
                <div class="password-strength-text" id="strengthText"></div>
                <div class="form-help">Mínimo 6 caracteres</div>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmar Nova Senha</label>
                <input
                    type="password"
                    class="form-control"
                    id="password_confirm"
                    name="password_confirm"
                    required
                    minlength="6"
                >
                <div class="form-help">Digite a senha novamente</div>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">
                🔐 Redefinir Senha
            </button>
        </form>
    </div>

    <script>
    // Validação de senha em tempo real
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirm');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const form = document.getElementById('resetForm');
    const submitBtn = document.getElementById('submitBtn');

    // Verificador de força da senha
    passwordInput.addEventListener('input', function() {
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

    // Verificação visual em tempo real se as senhas coincidem
    confirmInput.addEventListener('input', function() {
        if (this.value && this.value !== passwordInput.value) {
            this.style.borderColor = '#dc3545';
        } else if (this.value && this.value === passwordInput.value) {
            this.style.borderColor = '#28a745';
        } else {
            this.style.borderColor = '#e2e8f0';
        }
    });

    // Validação ao submeter
    form.addEventListener('submit', function(e) {
        const password = passwordInput.value;
        const confirmPassword = confirmInput.value;

        // Verifica tamanho mínimo
        if (password.length < 6) {
            e.preventDefault();
            alert('A senha deve ter no mínimo 6 caracteres');
            passwordInput.focus();
            return;
        }

        // Verifica se senhas coincidem
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('As senhas não coincidem. Por favor, verifique.');
            confirmInput.focus();
            return;
        }

        // Desabilita botão para evitar duplo envio
        submitBtn.disabled = true;
        submitBtn.textContent = 'Redefinindo...';
    });
    </script>
</body>
</html>
