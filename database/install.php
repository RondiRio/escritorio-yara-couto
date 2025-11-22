<?php
/*
/**
 * Script de Instalação do Banco de Dados
 * Escritório Yara Couto Vitoria
 * 
 * Execute este arquivo para criar o banco de dados e todas as tabelas
 * 
 * Uso: php database/install.php
 * Ou acesse via navegador: http://localhost/database/install.php
 */

// Define o caminho raiz
// define('ROOT_PATH', dirname(__DIR__));

// // Carrega variáveis de ambiente
// if (file_exists(ROOT_PATH . '/.env')) {
//     $lines = file(ROOT_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
//     foreach ($lines as $line) {
//         if (strpos(trim($line), '#') === 0) {
//             continue;
//         }
//         list($name, $value) = explode('=', $line, 2);
//         putenv(trim($name) . '=' . trim($value));
//         $_ENV[trim($name)] = trim($value);
//     }
// }

// // Configurações do banco
// $host = getenv('DB_HOST') ?: '127.0.0.1';
// $database = getenv('DB_DATABASE') ?: 'escritorio_yara';
// $username = getenv('DB_USERNAME') ?: 'root';
// $password = getenv('DB_PASSWORD') ?: '';

// echo "============================================\n";
// echo " INSTALAÇÃO DO BANCO DE DADOS\n";
// echo " Escritório Yara Couto Vitoria\n";
// echo "============================================\n\n";

// echo "Configurações:\n";
// echo "Host: {$host}\n";
// echo "Database: {$database}\n";
// echo "Username: {$username}\n";
// echo "Password: " . (empty($password) ? '(vazio)' : '****') . "\n\n";

// try {
//     // Conecta ao MySQL (sem selecionar database)
//     echo "[1/3] Conectando ao MySQL...\n";
//     $pdo = new PDO("mysql:host={$host};charset=utf8mb4", $username, $password);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     echo "✓ Conectado com sucesso!\n\n";

//     // Cria o banco de dados
//     echo "[2/3] Criando banco de dados '{$database}'...\n";
//     $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` 
//                 DEFAULT CHARACTER SET utf8mb4 
//                 COLLATE utf8mb4_unicode_ci");
//     $pdo->exec("USE `{$database}`");
//     echo "✓ Banco de dados criado/selecionado!\n\n";

//     // Executa as migrations
//     echo "[3/3] Executando migrations...\n";
    
//     $migrationsPath = __DIR__ . '/migrations';
//     $migrations = glob($migrationsPath . '/*.sql');
//     sort($migrations);

//     if (empty($migrations)) {
//         echo "⚠ Nenhuma migration encontrada em {$migrationsPath}\n";
//         echo "Executando schema completo...\n";
        
//         // Se não houver migrations, executa o schema completo
//         $schemaFile = __DIR__ . '/schema.sql';
//         if (file_exists($schemaFile)) {
//             $sql = file_get_contents($schemaFile);
//             $pdo->exec($sql);
//             echo "✓ Schema completo executado!\n\n";
//         } else {
//             throw new Exception("Arquivo schema.sql não encontrado!");
//         }
//     } else {
//         foreach ($migrations as $migration) {
//             $filename = basename($migration);
//             echo "  Executando: {$filename}...";
            
//             $sql = file_get_contents($migration);
            
//             // Remove comandos USE database do arquivo
//             $sql = preg_replace('/USE `[^`]+`;/', '', $sql);
            
//             // Divide por statements (separados por ;)
//             $statements = array_filter(
//                 array_map('trim', explode(';', $sql)),
//                 function($stmt) {
//                     return !empty($stmt) && 
//                            strpos($stmt, '--') !== 0 && 
//                            strpos($stmt, '/*') !== 0;
//                 }
//             );
            
//             foreach ($statements as $statement) {
//                 if (!empty(trim($statement))) {
//                     try {
//                         $pdo->exec($statement);
//                     } catch (PDOException $e) {
//                         // Ignora erros de "já existe" mas loga outros
//                         if (strpos($e->getMessage(), 'already exists') === false &&
//                             strpos($e->getMessage(), 'Duplicate') === false) {
//                             echo "\n⚠ Aviso: " . $e->getMessage() . "\n";
//                         }
//                     }
//                 }
//             }
            
//             echo " ✓\n";
//         }
//         echo "\n✓ Todas as migrations executadas!\n\n";
//     }

//     // Verifica as tabelas criadas
//     echo "============================================\n";
//     echo " VERIFICANDO INSTALAÇÃO\n";
//     echo "============================================\n\n";

//     $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
//     echo "Tabelas criadas (" . count($tables) . "):\n";
//     foreach ($tables as $table) {
//         echo "  ✓ {$table}\n";
//     }

//     echo "\n============================================\n";
//     echo " INSTALAÇÃO CONCLUÍDA COM SUCESSO!\n";
//     echo "============================================\n\n";

//     echo "Credenciais de acesso:\n";
//     echo "URL Admin: " . (getenv('APP_URL') ?: 'http://localhost') . "/admin\n";
//     echo "Email: admin@escritorioyara.com.br\n";
//     echo "Senha: admin123\n\n";
    
//     echo "⚠ IMPORTANTE: Altere a senha padrão após o primeiro login!\n\n";

//     echo "Próximos passos:\n";
//     echo "1. Acesse o painel administrativo\n";
//     echo "2. Altere a senha do administrador\n";
//     echo "3. Configure as informações do site em Configurações\n";
//     echo "4. Cadastre os advogados\n";
//     echo "5. Comece a publicar conteúdo!\n\n";

// } catch (PDOException $e) {
//     echo "\n❌ ERRO: " . $e->getMessage() . "\n\n";
//     echo "Verifique:\n";
//     echo "1. Se o MySQL está rodando\n";
//     echo "2. Se as credenciais no .env estão corretas\n";
//     echo "3. Se o usuário tem permissão para criar databases\n\n";
//     exit(1);
// } catch (Exception $e) {
//     echo "\n❌ ERRO: " . $e->getMessage() . "\n\n";
//     exit(1);
// }


// ==================== CARREGA VARIÁVEIS DE AMBIENTE (MANUAL) ====================

if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}