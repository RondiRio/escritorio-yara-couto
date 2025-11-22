<?php

namespace Core;

/**
 * Classe Router - Gerencia rotas da aplicação
 */
class Router
{
    private $routes = [];
    private $notFoundCallback;

    /**
     * Adiciona rota GET
     */
    public function get($path, $callback)
    {
        $this->addRoute('GET', $path, $callback);
    }

    /**
     * Adiciona rota POST
     */
    public function post($path, $callback)
    {
        $this->addRoute('POST', $path, $callback);
    }

    /**
     * Adiciona rota
     */
    private function addRoute($method, $path, $callback)
    {
        // Normaliza path
        $path = '/' . trim($path, '/');
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }

    /**
     * Define callback para rota não encontrada (404)
     */
    public function notFound($callback)
    {
        $this->notFoundCallback = $callback;
    }

    /**
     * Executa o roteamento
     */
    public function dispatch()
    {
        // Obtém método e URI
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getUri();

        // Busca rota correspondente
        foreach ($this->routes as $route) {
            // Converte parâmetros da rota em regex
            $pattern = $this->convertToRegex($route['path']);

            // Verifica se URI corresponde ao padrão
            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                // Remove primeira correspondência (URI completo)
                array_shift($matches);

                // Executa callback
                return $this->executeCallback($route['callback'], $matches);
            }
        }

        // Rota não encontrada
        $this->handleNotFound();
    }

    /**
     * Obtém URI limpo
     */
    private function getUri()
    {
        // Defina o caminho da sua subpasta
        // Deve ser o mesmo do RewriteBase no .htaccess, mas sem aspas
        $basePath = '/Softwares-Clientes/escritorio-yara-couto';

        // Obtém URI da requisição
        $uri = $_SERVER['REQUEST_URI'];

        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        // Remove o basePath do início da URI
        // (Isso transforma "/Softwares Clientes/escritorio-yara-couto/blog" em "/blog")
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        // Garante que a URI comece com uma barra (ou seja apenas "/")
        $uri = '/' . trim($uri, '/');

        return $uri;
    }

    /**
     * Converte path em regex para matching
     */
    private function convertToRegex($path)
    {
        // Converte {id} em (\d+) para capturar números
        // Converte {slug} em ([a-z0-9\-]+) para capturar slugs
        $pattern = preg_replace('/\{id\}/', '(\d+)', $path);
        $pattern = preg_replace('/\{([a-z]+)\}/', '([a-z0-9\-]+)', $pattern);
        
        return '#^' . $pattern . '$#';
    }

    /**
     * Executa callback da rota
     */
    private function executeCallback($callback, $params = [])
    {
        // Se callback é string: "ControllerName@methodName"
        if (is_string($callback)) {
            $parts = explode('@', $callback);
            
            if (count($parts) === 2) {
                $controllerName = $parts[0];
                $methodName = $parts[1];

                // Tenta carregar do namespace Controllers ou Admin
                $controllerClass = "\\Controllers\\{$controllerName}";
                
                if (!class_exists($controllerClass)) {
                    $controllerClass = "\\Controllers\\Admin\\{$controllerName}";
                }

                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    
                    if (method_exists($controller, $methodName)) {
                        return call_user_func_array([$controller, $methodName], $params);
                    }
                }
            }
        }

        // Se callback é função anônima
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        }

        // Callback inválido
        die("Callback inválido para rota");
    }

    /**
     * Trata rota não encontrada (404)
     */
    private function handleNotFound()
    {
        http_response_code(404);

        if ($this->notFoundCallback && is_callable($this->notFoundCallback)) {
            call_user_func($this->notFoundCallback);
        } else {
            // Página 404 padrão
            echo "<!DOCTYPE html>
            <html lang='pt-BR'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>404 - Página não encontrada</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                        background: linear-gradient(135deg, #3d1f1a 0%, #5c2e28 100%);
                        color: white;
                    }
                    .container {
                        text-align: center;
                    }
                    h1 {
                        font-size: 6rem;
                        margin: 0;
                        color: #c9a870;
                    }
                    p {
                        font-size: 1.5rem;
                    }
                    a {
                        color: #c9a870;
                        text-decoration: none;
                        font-weight: bold;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h1>404</h1>
                    <p>Página não encontrada</p>
                    <a href='/'>Voltar para página inicial</a>
                </div>
            </body>
            </html>";
        }
        exit;
    }

    /**
     * Carrega rotas de arquivo
     */
    public function loadRoutes($file)
    {
        if (file_exists($file)) {
            $router = $this;
            require $file;
        }
    }
}