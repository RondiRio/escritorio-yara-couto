<?php

namespace Middleware;

/**
 * Middleware de Headers de Segurança
 * Adiciona headers HTTP para proteger contra vulnerabilidades comuns
 */
class SecurityHeadersMiddleware implements Middleware
{
    /**
     * Manipula a requisição adicionando headers de segurança
     *
     * @return bool
     */
    public function handle()
    {
        // Previne clickjacking
        header('X-Frame-Options: SAMEORIGIN');

        // Previne MIME-sniffing
        header('X-Content-Type-Options: nosniff');

        // Proteção XSS do navegador
        header('X-XSS-Protection: 1; mode=block');

        // Força HTTPS (descomente em produção com SSL)
        // header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');

        // Content Security Policy (CSP)
        // Ajuste conforme necessário para seu aplicativo
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",
            "img-src 'self' data: https:",
            "connect-src 'self'",
            "frame-ancestors 'self'"
        ]);
        header("Content-Security-Policy: {$csp}");

        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // Permissions Policy (Feature Policy)
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

        return true;
    }
}
