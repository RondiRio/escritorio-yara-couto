<?php

namespace Middleware;

/**
 * Middleware de proteção CSRF
 * Verifica automaticamente o token CSRF em requisições POST
 */
class CsrfMiddleware implements Middleware
{
    /**
     * Rotas que devem ser excluídas da verificação CSRF
     * (útil para APIs)
     */
    private $except = [
        '/api/*'
    ];

    /**
     * Manipula a requisição verificando o token CSRF
     *
     * @return bool
     */
    public function handle()
    {
        // Ignora verificação se não for POST, PUT, DELETE ou PATCH
        if (!$this->shouldVerify()) {
            return true;
        }

        // Verifica se a rota está na lista de exceções
        if ($this->inExceptArray()) {
            return true;
        }

        // Obtém o token da requisição
        $token = $this->getTokenFromRequest();

        // Verifica o token
        if (!verify_csrf($token)) {
            $this->csrfTokenMismatch();
            return false;
        }

        return true;
    }

    /**
     * Verifica se deve validar CSRF para este método HTTP
     *
     * @return bool
     */
    private function shouldVerify()
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        return in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH']);
    }

    /**
     * Verifica se a URL atual está na lista de exceções
     *
     * @return bool
     */
    private function inExceptArray()
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';

        foreach ($this->except as $pattern) {
            // Converte padrão wildcard para regex
            $pattern = str_replace('*', '.*', $pattern);
            if (preg_match('#^' . $pattern . '$#', $uri)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtém o token CSRF da requisição
     *
     * @return string|null
     */
    private function getTokenFromRequest()
    {
        // Tenta pegar do POST
        if (isset($_POST['csrf_token'])) {
            return $_POST['csrf_token'];
        }

        // Tenta pegar do header X-CSRF-TOKEN
        $headers = getallheaders();
        if (isset($headers['X-CSRF-TOKEN'])) {
            return $headers['X-CSRF-TOKEN'];
        }

        return null;
    }

    /**
     * Ação quando o token CSRF não é válido
     */
    private function csrfTokenMismatch()
    {
        // Log do erro
        error_log('CSRF Token Mismatch - IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

        // Se for requisição AJAX, retorna JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            http_response_code(419);
            echo json_encode([
                'error' => true,
                'message' => 'Token CSRF inválido ou expirado. Por favor, recarregue a página.'
            ]);
            exit;
        }

        // Para requisições normais, mostra página de erro
        http_response_code(419);
        echo '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro 419 - Token CSRF Inválido</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            color: #667eea;
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
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">419</div>
        <h1 class="error-title">Token CSRF Inválido</h1>
        <p class="error-message">
            Sua sessão expirou ou o token de segurança é inválido.
            Por favor, volte e recarregue a página antes de tentar novamente.
        </p>
        <a href="javascript:history.back()" class="btn">Voltar</a>
    </div>
</body>
</html>';
        exit;
    }
}
