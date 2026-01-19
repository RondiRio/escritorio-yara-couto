<?php

/**
 * Funções Auxiliares Globais
 * Sistema de Gestão de Escritórios
 */

// ==================== URL E REDIRECIONAMENTO ====================

/**
 * Gera URL base
 */
function base_url($path = '')
{
    $baseUrl = getenv('APP_URL') ?: 'http://localhost';
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

/**
 * Gera URL para assets
 */
function asset($path)
{
    return base_url('public/' . ltrim($path, '/'));
}

/**
 * Redireciona para URL
 */
function redirect($path)
{
    $url = base_url($path);
    header("Location: {$url}");
    exit;
}

/**
 * Redireciona para trás
 */
function back()
{
    $referer = $_SERVER['HTTP_REFERER'] ?? base_url();
    header("Location: {$referer}");
    exit;
}

/**
 * Gera URL de rota nomeada
 */
function route($name, $params = [])
{
    // Implementação básica - pode ser expandida
    $url = base_url($name);
    
    foreach ($params as $key => $value) {
        $url = str_replace("{{$key}}", $value, $url);
    }
    
    return $url;
}

// ==================== SESSÃO E FLASH MESSAGES ====================

/**
 * Define mensagem flash
 */
function flash($type, $message)
{
    $_SESSION['flash'][$type] = $message;
}

/**
 * Obtém mensagem flash
 */
function get_flash($type)
{
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

/**
 * Verifica se existe flash
 */
function has_flash($type)
{
    return isset($_SESSION['flash'][$type]);
}

/**
 * Obtém valor antigo do formulário
 */
function old($key, $default = '')
{
    return $_SESSION['old'][$key] ?? $default;
}

/**
 * Define valores antigos
 */
function set_old($data)
{
    $_SESSION['old'] = $data;
}

/**
 * Limpa valores antigos
 */
function clear_old()
{
    unset($_SESSION['old']);
}

// ==================== AUTENTICAÇÃO ====================

/**
 * Verifica se está autenticado
 */
function is_auth()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Obtém ID do usuário autenticado
 */
function auth_id()
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Obtém dados do usuário autenticado
 */
function auth_user()
{
    if (is_auth()) {
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'role' => $_SESSION['user_role'] ?? 'admin'
        ];
    }
    return null;
}

/**
 * Define usuário autenticado
 */
function set_auth($user)
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'] ?? 'admin';
}

/**
 * Remove autenticação
 */
function logout()
{
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_role']);
    session_destroy();
}

// ==================== SANITIZAÇÃO E VALIDAÇÃO ====================

/**
 * Sanitiza string
 */
function sanitize($data)
{
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Valida email
 */
function is_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida URL
 */
function is_url($url)
{
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// ==================== FORMATAÇÃO ====================

/**
 * Formata data brasileira
 */
function format_date($date, $format = 'd/m/Y')
{
    if (empty($date)) return '';
    $timestamp = strtotime($date);
    return date($format, $timestamp);
}

/**
 * Formata data e hora
 */
function format_datetime($datetime, $format = 'd/m/Y H:i')
{
    if (empty($datetime)) return '';
    $timestamp = strtotime($datetime);
    return date($format, $timestamp);
}

/**
 * Formata telefone brasileiro
 */
function format_phone($phone)
{
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    if (strlen($phone) == 11) {
        return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7);
    } elseif (strlen($phone) == 10) {
        return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6);
    }
    
    return $phone;
}

/**
 * Formata CPF
 */
function format_cpf($cpf)
{
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    if (strlen($cpf) == 11) {
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }
    
    return $cpf;
}

/**
 * Trunca texto
 */
function truncate($text, $length = 100, $suffix = '...')
{
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

/**
 * Gera slug
 */
function slug($text)
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}

// ==================== REQUEST ====================

/**
 * Obtém método HTTP
 */
function request_method()
{
    return $_SERVER['REQUEST_METHOD'];
}

/**
 * Verifica se é POST
 */
function is_post()
{
    return request_method() === 'POST';
}

/**
 * Verifica se é GET
 */
function is_get()
{
    return request_method() === 'GET';
}

/**
 * Obtém valor de POST
 */
function post($key = null, $default = null)
{
    if ($key === null) {
        return $_POST;
    }
    return $_POST[$key] ?? $default;
}

/**
 * Obtém valor de GET
 */
function get($key = null, $default = null)
{
    if ($key === null) {
        return $_GET;
    }
    return $_GET[$key] ?? $default;
}

// ==================== DEBUG ====================

/**
 * Debug dump
 */
function dd($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

/**
 * Debug print
 */
function dump($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

// ==================== CONFIGURAÇÕES ====================

/**
 * Obtém configuração
 */
function config($key, $default = null)
{
    return getenv($key) ?: $default;
}

/**
 * Obtém nome do app
 */
function app_name()
{
    return config('APP_NAME', 'Sistema de Gestão de Escritórios');
}

// ==================== ARQUIVO ====================

/**
 * Upload de arquivo
 */
function upload_file($file, $destination, $allowedTypes = [])
{
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return false;
    }

    // Verifica tipo
    if (!empty($allowedTypes)) {
        $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileType, $allowedTypes)) {
            return false;
        }
    }

    // Gera nome único
    $fileName = uniqid() . '_' . basename($file['name']);
    $targetPath = $destination . '/' . $fileName;

    // Move arquivo
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $fileName;
    }

    return false;
}

// ==================== VIEWS ====================

/**
 * Inclui partial
 */
function partial($name, $data = [])
{
    extract($data);
    $file = __DIR__ . "/../views/partials/{$name}.php";
    
    if (file_exists($file)) {
        include $file;
    }
}

/**
 * Verifica se URL está ativa
 */
function is_active($path)
{
    $currentPath = $_SERVER['REQUEST_URI'];
    return strpos($currentPath, $path) !== false ? 'active' : '';
}

// ==================== CSRF ====================

/**
 * Gera token CSRF
 */
function csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Campo hidden com token CSRF
 */
function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Verifica token CSRF
 */
function verify_csrf($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ==================== EMAIL ====================

/**
 * Envia um email usando PHPMailer
 *
 * @param string $to Email do destinatário
 * @param string $subject Assunto
 * @param string $body Corpo do email (HTML)
 * @param string $toName Nome do destinatário (opcional)
 * @return bool
 */
function send_mail($to, $subject, $body, $toName = '')
{
    try {
        $mailer = Core\Mailer::getInstance();
        return $mailer->send($to, $subject, $body, $toName);
    } catch (Exception $e) {
        error_log("Erro ao enviar email: {$e->getMessage()}");
        return false;
    }
}

/**
 * Envia email usando template
 *
 * @param string $to Email do destinatário
 * @param string $subject Assunto
 * @param string $template Nome do template
 * @param array $data Dados para o template
 * @return bool
 */
function send_mail_template($to, $subject, $template, $data = [])
{
    try {
        $mailer = Core\Mailer::getInstance();
        return $mailer->sendTemplate($to, $subject, $template, $data);
    } catch (Exception $e) {
        error_log("Erro ao enviar email com template: {$e->getMessage()}");
        return false;
    }
}