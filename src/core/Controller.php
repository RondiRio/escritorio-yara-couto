<?php

namespace Core;

/**
 * Classe Controller - Base para todos os controllers
 */
abstract class Controller
{
    /**
     * Carrega uma view
     */
    protected function view($viewPath, $data = [])
    {
        // Extrai variáveis do array $data
        extract($data);

        // Converte caminho da view (ex: "pages/home" para "src/views/pages/home.php")
        $viewFile = __DIR__ . '/../views/' . $viewPath . '.php';

        // Verifica se o arquivo existe
        if (!file_exists($viewFile)) {
            die("View não encontrada: {$viewPath}");
        }

        // Carrega a view
        require_once $viewFile;
    }

    /**
     * Carrega view com layout
     */
    protected function viewWithLayout($viewPath, $data = [], $layoutPath = 'layout/header')
    {
        // Extrai variáveis
        extract($data);

        // Inicia buffer de saída
        ob_start();

        // Carrega header/layout
        $this->view($layoutPath, $data);

        // Carrega conteúdo da página
        $this->view($viewPath, $data);

        // Carrega footer
        $this->view('layout/footer', $data);

        // Retorna conteúdo do buffer
        $content = ob_get_clean();
        echo $content;
    }

    /**
     * Redireciona para outra URL
     */
    protected function redirect($path)
    {
        $url = $this->url($path);
        header("Location: {$url}");
        exit;
    }

    /**
     * Gera URL completa
     */
    protected function url($path = '')
    {
        $baseUrl = getenv('APP_URL') ?: 'http://localhost';
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    /**
     * Retorna JSON como resposta
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Valida dados de entrada
     */
    protected function validate($data, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $ruleSet) {
            $rulesArray = explode('|', $ruleSet);

            foreach ($rulesArray as $rule) {
                // Required
                if ($rule === 'required' && empty($data[$field])) {
                    $errors[$field][] = "O campo {$field} é obrigatório.";
                }

                // Email
                if ($rule === 'email' && !empty($data[$field])) {
                    if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                        $errors[$field][] = "O campo {$field} deve ser um e-mail válido.";
                    }
                }

                // Min length
                if (strpos($rule, 'min:') === 0) {
                    $minLength = (int)substr($rule, 4);
                    if (!empty($data[$field]) && strlen($data[$field]) < $minLength) {
                        $errors[$field][] = "O campo {$field} deve ter no mínimo {$minLength} caracteres.";
                    }
                }

                // Max length
                if (strpos($rule, 'max:') === 0) {
                    $maxLength = (int)substr($rule, 4);
                    if (!empty($data[$field]) && strlen($data[$field]) > $maxLength) {
                        $errors[$field][] = "O campo {$field} deve ter no máximo {$maxLength} caracteres.";
                    }
                }

                // Numeric
                if ($rule === 'numeric' && !empty($data[$field])) {
                    if (!is_numeric($data[$field])) {
                        $errors[$field][] = "O campo {$field} deve ser numérico.";
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Define mensagem flash na sessão
     */
    protected function setFlash($type, $message)
    {
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Obtém e remove mensagem flash
     */
    protected function getFlash($type)
    {
        if (isset($_SESSION['flash'][$type])) {
            $message = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $message;
        }
        return null;
    }

    /**
     * Verifica se usuário está autenticado
     */
    protected function isAuthenticated()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Requer autenticação
     */
    protected function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            $this->setFlash('error', 'Você precisa estar autenticado para acessar esta página.');
            $this->redirect('admin/login');
        }
    }

    /**
     * Obtém dados do usuário autenticado
     */
    protected function getAuthUser()
    {
        if ($this->isAuthenticated()) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'] ?? '',
                'email' => $_SESSION['user_email'] ?? ''
            ];
        }
        return null;
    }

    /**
     * Sanitiza entrada
     */
    protected function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Obtém dados do POST
     */
    protected function post($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    /**
     * Obtém dados do GET
     */
    protected function get($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    /**
     * Verifica se é requisição POST
     */
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Verifica se é requisição GET
     */
    protected function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}