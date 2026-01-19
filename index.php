<?php

/**
 * Ponto de Entrada da Aplicação
 * Sistema de Gestão de Escritórios - Advocacia e Contabilidade
 */

// ==================== CONFIGURAÇÃO INICIAL ====================

// Exibe erros em modo desenvolvimento
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define timezone
date_default_timezone_set('America/Sao_Paulo');

// Inicia sessão
session_start();

// ==================== AUTOLOAD ====================

// ==================== AUTOLOAD (MANUAL) ====================

spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . '/src/';
    
    // Converte o namespace (ex: Core\Controller) para um caminho de arquivo (ex: core/Controller.php)
    $parts = explode('\\', $class);

    // Converte o primeiro segmento do namespace (que é o diretório) para minúsculas
    // ex: "Controllers" vira "controllers", "Models" vira "models"
    if (!empty($parts)) {
        $parts[0] = strtolower($parts[0]);
    }

    $file = $base_dir . implode('/', $parts) . '.php';

    // // Se o arquivo existir, inclua-o
    // if (file_exists($file)) {
    //     require $file;
    // }
    // ==================== AUTOLOAD (MANUAL) ====================

spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . '/src/';
    
    // Converte o namespace (ex: Core\Controller) para um caminho de arquivo
    $parts = explode('\\', $class);

    // Converte o primeiro segmento (ex: "Core", "Models", "Controllers") para minúsculas
    if (!empty($parts)) {
        $parts[0] = strtolower($parts[0]);
    }

    $file = $base_dir . implode('/', $parts) . '.php';

    // Se o arquivo existir, inclua-o
    if (file_exists($file)) {
        require $file;
    }
});
});

// ==================== CARREGA VARIÁVEIS DE AMBIENTE ====================

// // Carrega .env
// if (file_exists(__DIR__ . '/.env')) {
//     $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
//     $dotenv->load();
// }
// Carrega .env (Manualmente)
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) { // Ignora comentários
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

// ==================== CONFIGURAÇÕES DA APLICAÇÃO ====================

// Define constantes
define('APP_NAME', getenv('APP_NAME') ?: 'Sistema de Gestão de Escritórios');
define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('APP_DEBUG', getenv('APP_DEBUG') === 'true');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost');

// Configuração de erros baseada no ambiente
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/storage/logs/php-errors.log');
}

// ==================== CARREGA HELPERS ====================

require_once __DIR__ . '/src/helpers/functions.php';

// ==================== CARREGA CONFIGURAÇÕES ====================

require_once __DIR__ . '/src/config/database.php';
require_once __DIR__ . '/src/config/app.php';

// ==================== INICIALIZA ROTEADOR ====================

use Core\Router;

$router = new Router();

// ==================== CARREGA ROTAS ====================

// Rotas públicas
require_once __DIR__ . '/src/routes/web.php';

// Rotas administrativas
require_once __DIR__ . '/src/routes/admin.php';

// ==================== EXECUTA O ROTEAMENTO ====================

$router->dispatch();