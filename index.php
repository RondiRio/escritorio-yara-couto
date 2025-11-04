<?php

/**
 * Ponto de Entrada da Aplicação
 * Escritório Yara Couto Vitoria - Sistema de Gerenciamento
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

// Carrega Composer Autoload
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    die('Execute: composer install');
}

// ==================== CARREGA VARIÁVEIS DE AMBIENTE ====================

// Carrega .env
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// ==================== CONFIGURAÇÕES DA APLICAÇÃO ====================

// Define constantes
define('APP_NAME', getenv('APP_NAME') ?: 'Escritório Yara Couto Vitoria');
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