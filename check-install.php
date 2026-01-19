<?php
/**
 * Script de Verificação de Instalação
 * Execute este arquivo para verificar se tudo está configurado corretamente
 *
 * URL: http://localhost/escritorio-yara-couto/check-install.php
 */

// Configuração de exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função para exibir status
function showStatus($title, $success, $message = '', $details = '') {
    $icon = $success ? '✅' : '❌';
    $color = $success ? '#28a745' : '#dc3545';

    echo "<div style='margin: 10px 0; padding: 15px; border-left: 4px solid {$color}; background: #f8f9fa;'>";
    echo "<strong style='color: {$color};'>{$icon} {$title}</strong>";
    if ($message) {
        echo "<br><small style='color: #6c757d;'>{$message}</small>";
    }
    if ($details) {
        echo "<br><code style='display: block; margin-top: 5px; padding: 5px; background: #e9ecef;'>{$details}</code>";
    }
    echo "</div>";
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação de Instalação - Sistema de Gestão de Escritórios</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
        }
        h1 {
            color: #2d3748;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #718096;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }
        .section {
            margin: 30px 0;
        }
        .section-title {
            font-size: 20px;
            color: #2d3748;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .summary-item {
            padding: 20px;
            background: #f7fafc;
            border-radius: 8px;
            text-align: center;
        }
        .summary-value {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .summary-label {
            color: #718096;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 10px 10px 0;
            font-weight: 500;
        }
        .btn:hover {
            background: #5a67d8;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verificação de Instalação</h1>
        <p class="subtitle">Sistema de Gestão de Escritórios</p>

        <?php
        $totalChecks = 0;
        $passedChecks = 0;

        // ==================== 1. VERIFICAR PHP ====================
        echo '<div class="section">';
        echo '<h2 class="section-title">1. Verificação do PHP</h2>';

        // Versão PHP
        $phpVersion = phpversion();
        $phpOk = version_compare($phpVersion, '7.4.0', '>=');
        showStatus(
            'Versão do PHP',
            $phpOk,
            $phpOk ? "Versão {$phpVersion} - OK" : "Versão {$phpVersion} - Necessário PHP 7.4+",
            "php -v"
        );
        $totalChecks++; if ($phpOk) $passedChecks++;

        // Extensões necessárias
        $requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json'];
        foreach ($requiredExtensions as $ext) {
            $loaded = extension_loaded($ext);
            showStatus(
                "Extensão: {$ext}",
                $loaded,
                $loaded ? 'Carregada' : 'NÃO ENCONTRADA - Instale esta extensão'
            );
            $totalChecks++; if ($loaded) $passedChecks++;
        }

        echo '</div>';

        // ==================== 2. VERIFICAR ARQUIVOS ====================
        echo '<div class="section">';
        echo '<h2 class="section-title">2. Verificação de Arquivos e Diretórios</h2>';

        // .env existe
        $envExists = file_exists(__DIR__ . '/.env');
        showStatus(
            'Arquivo .env',
            $envExists,
            $envExists ? 'Arquivo encontrado' : 'Arquivo não encontrado - Copie .env.example para .env',
            $envExists ? '' : 'cp .env.example .env'
        );
        $totalChecks++; if ($envExists) $passedChecks++;

        // .htaccess existe
        $htaccessExists = file_exists(__DIR__ . '/.htaccess');
        showStatus(
            'Arquivo .htaccess',
            $htaccessExists,
            $htaccessExists ? 'Arquivo encontrado' : 'Arquivo não encontrado'
        );
        $totalChecks++; if ($htaccessExists) $passedChecks++;

        // Diretórios necessários
        $requiredDirs = [
            'public/uploads' => 'Uploads públicos',
            'public/uploads/posts' => 'Imagens de posts',
            'public/uploads/lawyers' => 'Fotos de advogados',
            'public/uploads/avatars' => 'Avatares de usuários',
            'storage/logs' => 'Logs do sistema',
            'storage/cache' => 'Cache do sistema'
        ];

        foreach ($requiredDirs as $dir => $description) {
            $fullPath = __DIR__ . '/' . $dir;
            $exists = is_dir($fullPath);
            $writable = $exists && is_writable($fullPath);

            showStatus(
                $description,
                $exists && $writable,
                $exists ? ($writable ? 'Diretório OK' : 'SEM PERMISSÃO DE ESCRITA') : 'Diretório não existe',
                $exists ? '' : "mkdir -p {$dir}"
            );
            $totalChecks++; if ($exists && $writable) $passedChecks++;
        }

        echo '</div>';

        // ==================== 3. VERIFICAR BANCO DE DADOS ====================
        echo '<div class="section">';
        echo '<h2 class="section-title">3. Verificação do Banco de Dados</h2>';

        if ($envExists) {
            // Carrega .env
            $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (strpos($line, '=') !== false) {
                    list($name, $value) = explode('=', $line, 2);
                    putenv(trim($name) . '=' . trim($value));
                }
            }

            $dbHost = getenv('DB_HOST') ?: '127.0.0.1';
            $dbName = getenv('DB_DATABASE') ?: 'escritorio_db';
            $dbUser = getenv('DB_USERNAME') ?: 'root';
            $dbPass = getenv('DB_PASSWORD') ?: '';

            try {
                // Tenta conectar
                $pdo = new PDO("mysql:host={$dbHost};charset=utf8mb4", $dbUser, $dbPass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                showStatus(
                    'Conexão MySQL',
                    true,
                    "Conectado em {$dbHost}"
                );
                $totalChecks++; $passedChecks++;

                // Verifica se banco existe
                $stmt = $pdo->query("SHOW DATABASES LIKE '{$dbName}'");
                $dbExists = $stmt->rowCount() > 0;

                showStatus(
                    'Banco de Dados',
                    $dbExists,
                    $dbExists ? "Banco '{$dbName}' encontrado" : "Banco '{$dbName}' NÃO EXISTE",
                    $dbExists ? '' : "CREATE DATABASE {$dbName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
                );
                $totalChecks++; if ($dbExists) $passedChecks++;

                if ($dbExists) {
                    // Conecta ao banco específico
                    $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Verifica tabelas
                    $requiredTables = ['users', 'posts', 'categories', 'lawyers', 'appointments', 'settings', 'activity_logs', 'password_resets'];
                    $stmt = $pdo->query("SHOW TABLES");
                    $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    $missingTables = array_diff($requiredTables, $existingTables);
                    $tablesOk = empty($missingTables);

                    showStatus(
                        'Tabelas do Banco',
                        $tablesOk,
                        $tablesOk
                            ? count($existingTables) . ' tabelas encontradas'
                            : 'Faltam tabelas: ' . implode(', ', $missingTables),
                        $tablesOk ? '' : 'Importe database/schema.sql no phpMyAdmin'
                    );
                    $totalChecks++; if ($tablesOk) $passedChecks++;

                    // Verifica usuário admin
                    if (in_array('users', $existingTables)) {
                        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
                        $adminCount = $stmt->fetchColumn();
                        $adminExists = $adminCount > 0;

                        showStatus(
                            'Usuário Administrador',
                            $adminExists,
                            $adminExists ? "{$adminCount} admin(s) encontrado(s)" : 'Nenhum admin - Importe o schema.sql'
                        );
                        $totalChecks++; if ($adminExists) $passedChecks++;
                    }
                }

            } catch (PDOException $e) {
                showStatus(
                    'Conexão MySQL',
                    false,
                    'Erro ao conectar: ' . $e->getMessage(),
                    "Verifique se MySQL está rodando no XAMPP"
                );
                $totalChecks++;
            }
        } else {
            showStatus(
                'Configuração do Banco',
                false,
                'Arquivo .env não encontrado - não é possível verificar banco de dados'
            );
        }

        echo '</div>';

        // ==================== 4. VERIFICAR CONFIGURAÇÕES ====================
        echo '<div class="section">';
        echo '<h2 class="section-title">4. Verificação de Configurações</h2>';

        if ($envExists) {
            $appUrl = getenv('APP_URL') ?: 'http://localhost';
            $appName = getenv('APP_NAME') ?: 'Sistema de Gestão de Escritórios';
            $appDebug = getenv('APP_DEBUG') === 'true';

            showStatus(
                'APP_URL',
                true,
                "Configurado: {$appUrl}"
            );

            showStatus(
                'APP_NAME',
                true,
                "Configurado: {$appName}"
            );

            showStatus(
                'APP_DEBUG',
                true,
                $appDebug ? 'Modo DEBUG ativado (desenvolvimento)' : 'Modo DEBUG desativado (produção)',
                'Mantenha false em produção'
            );
        }

        echo '</div>';

        // ==================== RESUMO ====================
        $percentage = ($totalChecks > 0) ? round(($passedChecks / $totalChecks) * 100) : 0;
        $allPassed = $passedChecks === $totalChecks;

        echo '<div class="section">';
        echo '<h2 class="section-title">📊 Resumo da Verificação</h2>';
        echo '<div class="summary">';
        echo "<div class='summary-item'>";
        echo "<div class='summary-value' style='color: " . ($allPassed ? '#28a745' : '#dc3545') . ";'>{$percentage}%</div>";
        echo "<div class='summary-label'>Aprovação</div>";
        echo "</div>";
        echo "<div class='summary-item'>";
        echo "<div class='summary-value' style='color: #28a745;'>{$passedChecks}</div>";
        echo "<div class='summary-label'>Passou</div>";
        echo "</div>";
        echo "<div class='summary-item'>";
        echo "<div class='summary-value' style='color: #dc3545;'>" . ($totalChecks - $passedChecks) . "</div>";
        echo "<div class='summary-label'>Falhou</div>";
        echo "</div>";
        echo "<div class='summary-item'>";
        echo "<div class='summary-value' style='color: #667eea;'>{$totalChecks}</div>";
        echo "<div class='summary-label'>Total de Testes</div>";
        echo "</div>";
        echo '</div>';

        if ($allPassed) {
            echo '<div style="margin: 20px 0; padding: 20px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; color: #155724;">';
            echo '<strong>🎉 Tudo certo! O sistema está pronto para uso.</strong><br>';
            echo 'Todos os requisitos foram atendidos. Você pode começar a usar o sistema agora!';
            echo '</div>';

            echo '<div style="margin: 20px 0;">';
            echo '<a href="/" class="btn btn-success">🏠 Ir para o Site</a>';
            echo '<a href="/admin/login" class="btn">🔐 Acessar Painel Admin</a>';
            echo '</div>';
        } else {
            echo '<div style="margin: 20px 0; padding: 20px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; color: #856404;">';
            echo '<strong>⚠️ Alguns problemas foram encontrados</strong><br>';
            echo 'Corrija os itens marcados com ❌ acima e execute este script novamente.';
            echo '</div>';

            echo '<div style="margin: 20px 0;">';
            echo '<a href="?" class="btn">🔄 Verificar Novamente</a>';
            echo '<a href="INSTALACAO.md" class="btn">📚 Ver Guia de Instalação</a>';
            echo '</div>';
        }

        echo '</div>';
        ?>

        <div class="section">
            <h2 class="section-title">📝 Informações Úteis</h2>
            <div style="background: #f7fafc; padding: 15px; border-radius: 8px;">
                <p><strong>Credenciais Padrão:</strong></p>
                <ul style="margin-left: 20px; color: #4a5568;">
                    <li>Email: <code>admin@escritorio.com.br</code></li>
                    <li>Senha: <code>admin123</code></li>
                </ul>
                <p style="margin-top: 15px; color: #e53e3e;"><strong>⚠️ IMPORTANTE:</strong> Altere a senha imediatamente após o primeiro login!</p>
            </div>
        </div>

        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; text-align: center; color: #718096; font-size: 14px;">
            <p>Sistema de Gestão de Escritórios - <?php echo date('Y'); ?></p>
            <p>Para suporte, consulte o arquivo <a href="INSTALACAO.md" style="color: #667eea;">INSTALACAO.md</a></p>
        </div>
    </div>
</body>
</html>
