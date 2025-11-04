<?php

/**
 * Rotas Públicas do Site
 * Escritório Yara Couto Vitoria
 */

// ==================== PÁGINA INICIAL ====================
$router->get('/', 'HomeController@index');
$router->get('/home', 'HomeController@index');

// ==================== SOBRE ====================
$router->get('/sobre', 'AboutController@index');
$router->get('/sobre-escritorio', 'AboutController@index');

// ==================== ÁREAS DE ATUAÇÃO ====================
$router->get('/areas-de-atuacao', 'AreasController@index');
$router->get('/areas', 'AreasController@index');

// Área específica por slug
$router->get('/areas/{slug}', 'AreasController@show');

// ==================== EQUIPE/ADVOGADOS ====================
$router->get('/equipe', 'TeamController@index');
$router->get('/advogados', 'TeamController@index');

// Advogado específico por ID
$router->get('/advogado/{id}', 'TeamController@show');

// ==================== BLOG ====================
// Lista de posts
$router->get('/blog', 'BlogController@index');
$router->get('/artigos', 'BlogController@index');

// Post individual
$router->get('/blog/{slug}', 'BlogController@show');
$router->get('/artigo/{slug}', 'BlogController@show');

// Posts por categoria
$router->get('/blog/categoria/{slug}', 'BlogController@category');
$router->get('/categoria/{slug}', 'BlogController@category');

// Posts por tag
$router->get('/blog/tag/{slug}', 'BlogController@tag');
$router->get('/tag/{slug}', 'BlogController@tag');

// Busca de posts
$router->get('/blog/buscar', 'BlogController@search');
$router->post('/blog/buscar', 'BlogController@search');

// ==================== CONTATO ====================
$router->get('/contato', 'ContactController@index');

// Envio do formulário de contato
$router->post('/contato/enviar', 'ContactController@send');

// ==================== AGENDAMENTO ====================
$router->get('/agendar', 'AppointmentController@index');
$router->get('/agendamento', 'AppointmentController@index');

// Criar agendamento
$router->post('/agendamento/criar', 'AppointmentController@create');

// Verificar disponibilidade
$router->post('/agendamento/verificar-disponibilidade', 'AppointmentController@checkAvailability');

// ==================== POLÍTICA DE PRIVACIDADE (LGPD) ====================
$router->get('/politica-de-privacidade', 'PageController@privacy');
$router->get('/privacidade', 'PageController@privacy');

// ==================== TERMOS DE USO ====================
$router->get('/termos-de-uso', 'PageController@terms');
$router->get('/termos', 'PageController@terms');

// ==================== INFORMAÇÕES LEGAIS ====================
// Aviso sobre Publicidade Profissional (OAB)
$router->get('/aviso-legal', 'PageController@legalNotice');

// ==================== RSS FEED (Opcional) ====================
$router->get('/feed', 'BlogController@feed');
$router->get('/rss', 'BlogController@feed');

// ==================== SITEMAP (SEO) ====================
$router->get('/sitemap.xml', 'SitemapController@index');

// ==================== PÁGINAS ESTÁTICAS ====================
$router->get('/faq', 'PageController@faq');
$router->get('/perguntas-frequentes', 'PageController@faq');

// ==================== API PÚBLICA (Opcional) ====================
// Busca rápida de artigos
$router->get('/api/posts/search', 'ApiController@searchPosts');

// Lista de categorias (para menus dinâmicos)
$router->get('/api/categories', 'ApiController@categories');

// ==================== REDIRECIONAMENTOS ====================
// Redireciona /index.php para /
$router->get('/index.php', function() {
    header('Location: /');
    exit;
});

// ==================== PÁGINA 404 ====================
$router->notFound(function() {
    http_response_code(404);
    require __DIR__ . '/../views/errors/404.php';
});