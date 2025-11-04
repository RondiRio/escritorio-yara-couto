<?php
/**
 * Layout - Header
 * Cabeçalho global do site
 */

// Busca configurações do site
use Models\Setting;
$settingModel = new Setting();
$siteInfo = $settingModel->getSiteInfo();
$siteName = $siteInfo['site_name'] ?? 'Escritório de Advocacia';
$siteDescription = $siteInfo['site_description'] ?? 'Advocacia com Excelência';

// Define título da página
$pageTitle = $pageTitle ?? $siteName;
$fullTitle = ($pageTitle !== $siteName) ? $pageTitle . ' - ' . $siteName : $siteName;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $pageDescription ?? $siteDescription ?>">
    <meta name="author" content="<?= $siteName ?>">
    
    <title><?= $fullTitle ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('images/favicon.ico') ?>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <style>
        :root {
            --color-primary: #06253D;
            --color-secondary: #CC8C5D;
            --color-background: #F5F6F7;
            --color-white: #FFFFFF;
            --color-text: #2C3E50;
            --color-text-light: #6C757D;
            --font-body: 'Inter', sans-serif;
            --font-heading: 'Playfair Display', serif;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            color: var(--color-text);
            background-color: var(--color-background);
            line-height: 1.6;
            font-size: 16px;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            color: var(--color-primary);
            line-height: 1.3;
            font-weight: 600;
        }

        a {
            color: var(--color-secondary);
            text-decoration: none;
            transition: var(--transition);
        }

        a:hover {
            color: var(--color-primary);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Principal */
        .header {
            background: var(--color-white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-top {
            background: var(--color-primary);
            color: var(--color-white);
            padding: 10px 0;
            font-size: 14px;
        }

        .header-top .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .header-top-links a {
            color: var(--color-white);
            margin-left: 20px;
            opacity: 0.9;
        }

        .header-top-links a:hover {
            opacity: 1;
            color: var(--color-secondary);
        }

        .header-main {
            padding: 20px 0;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            color: var(--color-primary);
            font-family: var(--font-heading);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .logo span {
            color: var(--color-secondary);
        }

        /* Navegação */
        .nav {
            display: flex;
            gap: 30px;
            list-style: none;
            align-items: center;
        }

        .nav a {
            color: var(--color-text);
            font-weight: 500;
            padding: 8px 0;
            position: relative;
        }

        .nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--color-secondary);
            transition: var(--transition);
        }

        .nav a:hover::after,
        .nav a.active::after {
            width: 100%;
        }

        .nav a:hover,
        .nav a.active {
            color: var(--color-secondary);
        }

        .btn-contact {
            background: var(--color-secondary);
            color: var(--color-white) !important;
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: 600;
            border: 2px solid var(--color-secondary);
        }

        .btn-contact:hover {
            background: transparent;
            color: var(--color-secondary) !important;
        }

        /* Menu Mobile */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--color-primary);
            cursor: pointer;
        }

        /* Flash Messages */
        .flash-messages {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }

        .alert {
            padding: 15px 20px;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            animation: slideIn 0.3s ease;
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

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-top {
                font-size: 12px;
            }

            .header-top .container {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .header-top-links {
                margin-left: 0;
            }

            .header-top-links a {
                margin: 0 10px;
            }

            .mobile-menu-btn {
                display: block;
            }

            .nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--color-white);
                flex-direction: column;
                padding: 20px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }

            .nav.active {
                display: flex;
            }

            .logo {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

    <!-- Flash Messages -->
    <?php if (has_flash('success') || has_flash('error') || has_flash('info')): ?>
    <div class="flash-messages">
        <?php if (has_flash('success')): ?>
            <div class="alert alert-success">
                <?= get_flash('success') ?>
            </div>
        <?php endif; ?>

        <?php if (has_flash('error')): ?>
            <div class="alert alert-error">
                <?= get_flash('error') ?>
            </div>
        <?php endif; ?>

        <?php if (has_flash('info')): ?>
            <div class="alert alert-info">
                <?= get_flash('info') ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Header -->
    <header class="header">
        <!-- Top Bar -->
        <div class="header-top">
            <div class="container">
                <div>
                    <?php if (!empty($siteInfo['site_email'])): ?>
                        <span>✉ <?= $siteInfo['site_email'] ?></span>
                    <?php endif; ?>
                    
                    <?php if (!empty($siteInfo['site_phone'])): ?>
                        <span style="margin-left: 20px;">☎ <?= format_phone($siteInfo['site_phone']) ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="header-top-links">
                    <a href="<?= base_url('blog') ?>">Blog</a>
                    <a href="<?= base_url('contato') ?>">Contato</a>
                    <?php if (is_auth()): ?>
                        <a href="<?= base_url('admin') ?>">Painel Admin</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Main Header -->
        <div class="header-main">
            <div class="container">
                <div class="header-content">
                    <a href="<?= base_url() ?>" class="logo">
                        <?php
                        // Pega apenas primeira palavra do nome
                        $logoText = explode(' ', $siteName);
                        echo $logoText[0];
                        if (isset($logoText[1])) {
                            echo ' <span>' . $logoText[1] . '</span>';
                        }
                        ?>
                    </a>

                    <button class="mobile-menu-btn" onclick="toggleMenu()">☰</button>

                    <nav>
                        <ul class="nav" id="mainNav">
                            <li><a href="<?= base_url() ?>" class="<?= is_active('/') ?>">Início</a></li>
                            <li><a href="<?= base_url('sobre') ?>" class="<?= is_active('sobre') ?>">Sobre</a></li>
                            <li><a href="<?= base_url('areas-de-atuacao') ?>" class="<?= is_active('areas') ?>">Áreas</a></li>
                            <li><a href="<?= base_url('equipe') ?>" class="<?= is_active('equipe') ?>">Equipe</a></li>
                            <li><a href="<?= base_url('blog') ?>" class="<?= is_active('blog') ?>">Blog</a></li>
                            <li><a href="<?= base_url('agendar') ?>" class="btn-contact">Agendar Consulta</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <script>
        function toggleMenu() {
            document.getElementById('mainNav').classList.toggle('active');
        }

        // Auto-hide flash messages
        setTimeout(() => {
            const flashMessages = document.querySelector('.flash-messages');
            if (flashMessages) {
                flashMessages.style.opacity = '0';
                setTimeout(() => flashMessages.remove(), 300);
            }
        }, 5000);
    </script>