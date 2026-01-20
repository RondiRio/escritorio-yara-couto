<?php

/**
 * Carregador simples de variáveis de ambiente
 * Substitui o vlucas/phpdotenv para não depender do Composer
 */
class DotEnv
{
    /**
     * Carrega variáveis de ambiente de um arquivo .env
     *
     * @param string $path Caminho para o diretório contendo o .env
     * @param string $file Nome do arquivo (padrão: .env)
     * @return void
     */
    public static function load($path, $file = '.env')
    {
        $filePath = rtrim($path, '/') . '/' . $file;

        if (!file_exists($filePath)) {
            throw new Exception("Arquivo .env não encontrado em: {$filePath}");
        }

        if (!is_readable($filePath)) {
            throw new Exception("Arquivo .env não pode ser lido: {$filePath}");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Ignora comentários
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Ignora linhas vazias
            if (trim($line) === '') {
                continue;
            }

            // Parse da linha (KEY=VALUE)
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);

                $name = trim($name);
                $value = trim($value);

                // Remove aspas do valor se existirem
                if (preg_match('/^(["\'])(.*)\\1$/', $value, $matches)) {
                    $value = $matches[2];
                }

                // Define no $_ENV e getenv()
                if (!array_key_exists($name, $_ENV)) {
                    $_ENV[$name] = $value;
                    putenv("{$name}={$value}");
                }
            }
        }
    }

    /**
     * Obtém uma variável de ambiente
     *
     * @param string $key Nome da variável
     * @param mixed $default Valor padrão se não encontrado
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            $value = isset($_ENV[$key]) ? $_ENV[$key] : $default;
        }

        return $value;
    }

    /**
     * Define uma variável de ambiente
     *
     * @param string $key Nome da variável
     * @param string $value Valor
     * @return void
     */
    public static function set($key, $value)
    {
        $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }

    /**
     * Verifica se uma variável existe
     *
     * @param string $key Nome da variável
     * @return bool
     */
    public static function has($key)
    {
        return getenv($key) !== false || isset($_ENV[$key]);
    }
}
