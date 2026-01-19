<?php

namespace Middleware;

/**
 * Middleware de autenticação
 * Verifica se o usuário está autenticado
 */
class AuthMiddleware implements Middleware
{
    /**
     * Manipula a requisição verificando a autenticação
     *
     * @return bool
     */
    public function handle()
    {
        if (!is_auth()) {
            // Se for requisição AJAX
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode([
                    'error' => true,
                    'message' => 'Não autenticado. Faça login para continuar.'
                ]);
                exit;
            }

            // Salva URL de destino para redirecionar após login
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'] ?? '/admin';

            // Redireciona para login
            redirect('/admin/login');
            return false;
        }

        return true;
    }
}
