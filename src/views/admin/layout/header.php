<?php
/**
 * Admin Layout - Header
 * Cabeçalho do painel administrativo
 */

// Verifica autenticação
if (!is_auth()) {
    redirect('admin/login');
}

$currentUser = auth_user();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin' ?> - Painel Administrativo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --color-primary: #06253D;
            --color-secondary: #CC8C5D;
            --color-background: #F5F6F7;
            --color-white: #FFFFFF;
            --color-text: #2C3E50;
            --color-text-light: #6C757D;
            --color-border: #dee2e6;
            --sidebar-width: 260px;
            --font-body: 'Inter', sans-serif;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            background: var(--color-background);
            color: var(--color-text);
        }

        /* Layout */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--color-primary);
            color: var(--color-white);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: var(--transition);
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo {
            font-size: 22px;
            font-weight: 700;
            color: var(--color-secondary);
            text-decoration: none;
            display: block;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-section {
            margin-bottom: 30px;
        }

        .menu-label {
            padding: 10px 20px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.5);
            font-weight: 600;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: var(--transition);
            border-left: 3px solid transparent;
        }

        .menu-item:hover,
        .menu-item.active {
            background: rgba(255,255,255,0.1);
            color: var(--color-white);
            border-left-color: var(--color-secondary);
        }

        .menu-icon {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: var(--transition);
        }

        /* Top Bar */
        .topbar {
            background: var(--color-white);
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--color-primary);
        }

        .topbar-search {
            display: flex;
            align-items: center;
            background: var(--color-background);
            padding: 8px 15px;
            border-radius: 8px;
            gap: 10px;
        }

        .topbar-search input {
            border: none;
            background: none;
            outline: none;
            width: 300px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            position: relative;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--color-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-white);
            font-weight: 600;
        }

        .user-info {
            font-size: 14px;
        }

        .user-name {
            font-weight: 600;
            color: var(--color-primary);
        }

        .user-role {
            font-size: 12px;
            color: var(--color-text-light);
        }

        /* Content Area */
        .content-area {
            padding: 30px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;
            color: var(--color-primary);
            margin-bottom: 5px;
        }

        .page-header p {
            color: var(--color-text-light);
        }

        /* Flash Messages */
        .flash-messages {
            margin-bottom: 20px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        .alert-info {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            color: #0c5460;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
            }

            .sidebar.active {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }

            .topbar-search {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .user-info {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="<?= base_url('admin') ?>" class="sidebar-logo">
                    Admin Panel
                </a>
            </div>

            <nav class="sidebar-menu">
                <div class="menu-section">
                    <div class="menu-label">Principal</div>
                    <a href="<?= base_url('admin/dashboard') ?>" class="menu-item <?= is_active('admin/dashboard') ?>">
                        <span class="menu-icon">📊</span>
                        <span>Dashboard</span>
                    </a>
                </div>

                <div class="menu-section">
                    <div class="menu-label">Conteúdo</div>
                    <a href="<?= base_url('admin/posts') ?>" class="menu-item <?= is_active('admin/posts') ?>">
                        <span class="menu-icon">📝</span>
                        <span>Posts</span>
                    </a>
                    <a href="<?= base_url('admin/categorias') ?>" class="menu-item <?= is_active('admin/categorias') ?>">
                        <span class="menu-icon">📂</span>
                        <span>Categorias</span>
                    </a>
                    <a href="<?= base_url('admin/tags') ?>" class="menu-item <?= is_active('admin/tags') ?>">
                        <span class="menu-icon">🏷️</span>
                        <span>Tags</span>
                    </a>
                </div>

                <div class="menu-section">
                    <div class="menu-label">Equipe</div>
                    <a href="<?= base_url('admin/advogados') ?>" class="menu-item <?= is_active('admin/advogados') ?>">
                        <span class="menu-icon">👨‍⚖️</span>
                        <span>Advogados</span>
                    </a>
                </div>

                <div class="menu-section">
                    <div class="menu-label">Agendamentos</div>
                    <a href="<?= base_url('admin/agendamentos') ?>" class="menu-item <?= is_active('admin/agendamentos') ?>">
                        <span class="menu-icon">📅</span>
                        <span>Agendamentos</span>
                    </a>
                </div>

                <div class="menu-section">
                    <div class="menu-label">Sistema</div>
                    <a href="<?= base_url('admin/usuarios') ?>" class="menu-item <?= is_active('admin/usuarios') ?>">
                        <span class="menu-icon">👥</span>
                        <span>Usuários</span>
                    </a>
                    <a href="<?= base_url('admin/configuracoes') ?>" class="menu-item <?= is_active('admin/configuracoes') ?>">
                        <span class="menu-icon">⚙️</span>
                        <span>Configurações</span>
                    </a>
                    <a href="<?= base_url('admin/logs') ?>" class="menu-item <?= is_active('admin/logs') ?>">
                        <span class="menu-icon">📋</span>
                        <span>Logs</span>
                    </a>
                </div>

                <div class="menu-section">
                    <a href="<?= base_url() ?>" class="menu-item" target="_blank">
                        <span class="menu-icon">🌐</span>
                        <span>Ver Site</span>
                    </a>
                    <a href="<?= base_url('admin/logout') ?>" class="menu-item">
                        <span class="menu-icon">🚪</span>
                        <span>Sair</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <div class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
                    <div class="topbar-search">
                        <span>🔍</span>
                        <input type="text" placeholder="Buscar...">
                    </div>
                </div>

                <div class="topbar-right">
                    <div class="user-menu">
                        <div class="user-avatar">
                            <?= strtoupper(substr($currentUser['name'], 0, 2)) ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?= $currentUser['name'] ?></div>
                            <div class="user-role"><?= ucfirst($currentUser['role'] ?? 'admin') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Flash Messages -->
                <?php if (has_flash('success') || has_flash('error') || has_flash('info')): ?>
                <div class="flash-messages">
                    <?php if (has_flash('success')): ?>
                        <div class="alert alert-success">
                            <span>✓</span>
                            <span><?= get_flash('success') ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (has_flash('error')): ?>
                        <div class="alert alert-error">
                            <span>✗</span>
                            <span><?= get_flash('error') ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (has_flash('info')): ?>
                        <div class="alert alert-info">
                            <span>ℹ</span>
                            <span><?= get_flash('info') ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Page Content -->