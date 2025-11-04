<?php
/**
 * View - Admin Login
 * Página de login administrativo
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel Administrativo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --color-primary: #06253D;
            --color-secondary: #CC8C5D;
            --color-background: #F5F6F7;
            --color-white: #FFFFFF;
            --color-text: #2C3E50;
            --font-body: 'Inter', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            background: linear-gradient(135deg, var(--color-primary) 0%, #0a3a5f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: var(--color-white);
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, var(--color-primary) 0%, #0a3a5f 100%);
            color: var(--color-white);
            padding: 40px 30px;
            text-align: center;
        }

        .login-icon {
            width: 80px;
            height: 80px;
            background: var(--color-secondary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 20px;
        }

        .login-header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .login-body {
            padding: 40px 30px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background: #fee;
            border-left: 4px solid #e74c3c;
            color: #c0392b;
        }

        .alert-success {
            background: #efe;
            border-left: 4px solid #27ae60;
            color: #229954;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--color-text);
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
            font-size: 18px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            font-family: var(--font-body);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--color-secondary);
            box-shadow: 0 0 0 3px rgba(204, 140, 93, 0.1);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .forgot-link {
            color: var(--color-secondary);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--color-secondary);
            color: var(--color-white);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: var(--color-primary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            padding: 20px 30px;
            background: var(--color-background);
            text-align: center;
            font-size: 13px;
            color: #7f8c8d;
        }

        .back-to-site {
            color: var(--color-secondary);
            text-decoration: none;
            font-weight: 600;
        }

        .back-to-site:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-header {
                padding: 30px 20px;
            }

            .login-body {
                padding: 30px 20px;
            }

            .login-header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <div class="login-icon">🔐</div>
            <h1>Painel Administrativo</h1>
            <p>Faça login para acessar o sistema</p>
        </div>

        <!-- Body -->
        <div class="login-body">
            <!-- Flash Messages -->
            <?php if (has_flash('error')): ?>
                <div class="alert alert-error">
                    <?= get_flash('error') ?>
                </div>
            <?php endif; ?>

            <?php if (has_flash('success')): ?>
                <div class="alert alert-success">
                    <?= get_flash('success') ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="<?= base_url('admin/login') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <div class="input-wrapper">
                        <span class="input-icon">✉</span>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control" 
                               placeholder="seu@email.com"
                               required 
                               autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control" 
                               placeholder="••••••••"
                               required>
                    </div>
                </div>

                <div class="remember-forgot">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember" style="margin: 0; font-weight: 400;">Lembrar-me</label>
                    </div>
                    <a href="<?= base_url('admin/recuperar-senha') ?>" class="forgot-link">
                        Esqueceu a senha?
                    </a>
                </div>

                <button type="submit" class="btn-login">
                    Entrar no Sistema
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            <a href="<?= base_url() ?>" class="back-to-site">← Voltar para o site</a>
        </div>
    </div>
</body>
</html>