<?php

namespace Middleware;

/**
 * Middleware de verificação de roles/permissões
 * Verifica se o usuário tem a permissão necessária
 */
class RoleMiddleware implements Middleware
{
    private $requiredRole;
    private $roleHierarchy = [
        'admin' => 3,
        'editor' => 2,
        'author' => 1
    ];

    /**
     * Construtor
     *
     * @param string $requiredRole Role necessária (admin, editor, author)
     */
    public function __construct($requiredRole = 'author')
    {
        $this->requiredRole = $requiredRole;
    }

    /**
     * Manipula a requisição verificando as permissões
     *
     * @return bool
     */
    public function handle()
    {
        // Primeiro verifica se está autenticado
        if (!is_auth()) {
            redirect('/admin/login');
            return false;
        }

        $user = auth_user();
        $userRole = $user['role'] ?? 'author';

        // Verifica se tem permissão
        if (!$this->hasPermission($userRole)) {
            $this->forbidden();
            return false;
        }

        return true;
    }

    /**
     * Verifica se o usuário tem permissão
     *
     * @param string $userRole
     * @return bool
     */
    private function hasPermission($userRole)
    {
        $userLevel = $this->roleHierarchy[$userRole] ?? 0;
        $requiredLevel = $this->roleHierarchy[$this->requiredRole] ?? 999;

        return $userLevel >= $requiredLevel;
    }

    /**
     * Ação quando o usuário não tem permissão
     */
    private function forbidden()
    {
        // Log da tentativa de acesso não autorizado
        if (class_exists('Models\ActivityLog')) {
            $log = new \Models\ActivityLog();
            $log->create([
                'user_id' => auth_id(),
                'action' => 'forbidden_access',
                'entity_type' => 'system',
                'entity_id' => null,
                'description' => 'Tentativa de acesso não autorizado a ' . ($_SERVER['REQUEST_URI'] ?? 'unknown'),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        }

        // Se for requisição AJAX
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode([
                'error' => true,
                'message' => 'Você não tem permissão para acessar este recurso.'
            ]);
            exit;
        }

        // Para requisições normais, mostra página de erro
        http_response_code(403);
        echo '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro 403 - Acesso Negado</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .error-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
        }
        .error-code {
            font-size: 80px;
            font-weight: 700;
            color: #f5576c;
            margin: 0;
            line-height: 1;
        }
        .error-title {
            font-size: 24px;
            font-weight: 600;
            color: #2d3748;
            margin: 20px 0 10px;
        }
        .error-message {
            font-size: 16px;
            color: #718096;
            line-height: 1.6;
            margin: 0 0 30px;
        }
        .btn {
            display: inline-block;
            background: #f5576c;
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s;
            margin: 0 5px;
        }
        .btn:hover {
            background: #e04454;
        }
        .btn-secondary {
            background: #718096;
        }
        .btn-secondary:hover {
            background: #5a6a7a;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">403</div>
        <h1 class="error-title">Acesso Negado</h1>
        <p class="error-message">
            Você não tem permissão para acessar este recurso.
            Entre em contato com o administrador do sistema se acredita que isso é um erro.
        </p>
        <a href="/admin" class="btn btn-secondary">Ir para Dashboard</a>
        <a href="javascript:history.back()" class="btn">Voltar</a>
    </div>
</body>
</html>';
        exit;
    }
}
