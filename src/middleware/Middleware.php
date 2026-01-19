<?php

namespace Middleware;

/**
 * Interface Middleware
 * Define o contrato para todos os middlewares
 */
interface Middleware
{
    /**
     * Manipula a requisição
     *
     * @return bool Retorna true se pode continuar, false se deve parar
     */
    public function handle();
}
