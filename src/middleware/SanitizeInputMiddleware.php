<?php

namespace Middleware;

/**
 * Middleware de sanitização automática de entradas
 * Sanitiza todos os dados de $_GET, $_POST e $_REQUEST
 */
class SanitizeInputMiddleware implements Middleware
{
    /**
     * Campos que NÃO devem ser sanitizados
     * (ex: senhas, que serão hasheadas)
     */
    private $except = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password'
    ];

    /**
     * Manipula a requisição sanitizando as entradas
     *
     * @return bool
     */
    public function handle()
    {
        $_GET = $this->sanitizeArray($_GET);
        $_POST = $this->sanitizeArray($_POST);
        $_REQUEST = $this->sanitizeArray($_REQUEST);

        return true;
    }

    /**
     * Sanitiza recursivamente um array
     *
     * @param array $data
     * @return array
     */
    private function sanitizeArray($data)
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            // Pula campos na lista de exceções
            if (in_array($key, $this->except)) {
                $sanitized[$key] = $value;
                continue;
            }

            // Sanitiza recursivamente se for array
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeArray($value);
            } else {
                $sanitized[$key] = $this->sanitizeValue($value);
            }
        }

        return $sanitized;
    }

    /**
     * Sanitiza um valor individual
     *
     * @param mixed $value
     * @return string
     */
    private function sanitizeValue($value)
    {
        // Se não for string, retorna como está
        if (!is_string($value)) {
            return $value;
        }

        // Remove espaços extras
        $value = trim($value);

        // Remove tags HTML perigosas mantendo apenas as seguras
        // Permite: <b>, <i>, <u>, <strong>, <em>, <br>, <p>
        $value = strip_tags($value, '<b><i><u><strong><em><br><p>');

        // Converte caracteres especiais HTML
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);

        return $value;
    }
}
