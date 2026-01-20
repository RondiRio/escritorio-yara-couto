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

spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . '/src/';

    // Carrega classes da pasta libs (ex: PHPMailer, DotEnv)
    if (strpos($class, 'PHPMailer\\PHPMailer\\') === 0) {
        $file = $base_dir . 'libs/PHPMailer/' . str_replace('PHPMailer\\PHPMailer\\', '', $class) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }

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

// ==================== CARREGA VARIÁVEIS DE AMBIENTE ====================

// Carrega DotEnv
require_once __DIR__ . '/src/libs/DotEnv.php';

// Carrega .env
if (file_exists(__DIR__ . '/.env')) {
    DotEnv::load(__DIR__);
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

// ==================== APLICA MIDDLEWARES GLOBAIS ====================

// Sanitização automática de todas as entradas (DEVE SER O PRIMEIRO)
$sanitizeMiddleware = new Middleware\SanitizeInputMiddleware();
$sanitizeMiddleware->handle();

// Headers de segurança (aplica em todas as requisições)
$securityHeaders = new Middleware\SecurityHeadersMiddleware();
$securityHeaders->handle();

// CSRF Protection (aplica automaticamente em POST/PUT/DELETE/PATCH)
$csrfMiddleware = new Middleware\CsrfMiddleware();
$csrfMiddleware->handle();

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