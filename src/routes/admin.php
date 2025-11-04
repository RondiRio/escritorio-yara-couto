<?php

/**
 * Rotas Administrativas
 * Escritório Yara Couto Vitoria
 * 
 * IMPORTANTE: Todas as rotas administrativas devem ter proteção de autenticação
 */

// ==================== AUTENTICAÇÃO ====================

// Login
$router->get('/admin/login', 'AuthController@showLoginForm');
$router->post('/admin/login', 'AuthController@login');

// Logout
$router->get('/admin/logout', 'AuthController@logout');
$router->post('/admin/logout', 'AuthController@logout');

// Recuperação de senha
$router->get('/admin/recuperar-senha', 'AuthController@showForgotPasswordForm');
$router->post('/admin/recuperar-senha', 'AuthController@forgotPassword');

// Redefinir senha
$router->get('/admin/redefinir-senha/{token}', 'AuthController@showResetPasswordForm');
$router->post('/admin/redefinir-senha', 'AuthController@resetPassword');

// ==================== DASHBOARD ====================
$router->get('/admin', 'DashboardController@index');
$router->get('/admin/dashboard', 'DashboardController@index');

// Estatísticas rápidas (AJAX)
$router->get('/admin/dashboard/stats', 'DashboardController@getStats');

// ==================== POSTS/ARTIGOS ====================

// Listar posts
$router->get('/admin/posts', 'PostController@index');
$router->get('/admin/artigos', 'PostController@index');

// Criar post
$router->get('/admin/posts/criar', 'PostController@create');
$router->post('/admin/posts/criar', 'PostController@store');

// Editar post
$router->get('/admin/posts/{id}/editar', 'PostController@edit');
$router->post('/admin/posts/{id}/editar', 'PostController@update');

// Visualizar post
$router->get('/admin/posts/{id}', 'PostController@show');

// Deletar post
$router->post('/admin/posts/{id}/deletar', 'PostController@delete');

// Alterar status (publicar/rascunho)
$router->post('/admin/posts/{id}/status', 'PostController@changeStatus');

// Upload de imagem (AJAX)
$router->post('/admin/posts/upload-imagem', 'PostController@uploadImage');

// ==================== CATEGORIAS ====================

// Listar categorias
$router->get('/admin/categorias', 'CategoryController@index');

// Criar categoria
$router->get('/admin/categorias/criar', 'CategoryController@create');
$router->post('/admin/categorias/criar', 'CategoryController@store');

// Editar categoria
$router->get('/admin/categorias/{id}/editar', 'CategoryController@edit');
$router->post('/admin/categorias/{id}/editar', 'CategoryController@update');

// Deletar categoria
$router->post('/admin/categorias/{id}/deletar', 'CategoryController@delete');

// ==================== TAGS ====================

// Listar tags
$router->get('/admin/tags', 'TagController@index');

// Criar tag
$router->post('/admin/tags/criar', 'TagController@store');

// Editar tag
$router->post('/admin/tags/{id}/editar', 'TagController@update');

// Deletar tag
$router->post('/admin/tags/{id}/deletar', 'TagController@delete');

// Buscar tags (AJAX - para autocomplete)
$router->get('/admin/tags/buscar', 'TagController@search');

// ==================== ADVOGADOS ====================

// Listar advogados
$router->get('/admin/advogados', 'LawyerController@index');

// Criar advogado
$router->get('/admin/advogados/criar', 'LawyerController@create');
$router->post('/admin/advogados/criar', 'LawyerController@store');

// Editar advogado
$router->get('/admin/advogados/{id}/editar', 'LawyerController@edit');
$router->post('/admin/advogados/{id}/editar', 'LawyerController@update');

// Visualizar advogado
$router->get('/admin/advogados/{id}', 'LawyerController@show');

// Deletar advogado
$router->post('/admin/advogados/{id}/deletar', 'LawyerController@delete');

// Upload de foto
$router->post('/admin/advogados/upload-foto', 'LawyerController@uploadPhoto');

// Remover foto
$router->post('/admin/advogados/{id}/remover-foto', 'LawyerController@removePhoto');

// Alterar status (ativar/desativar)
$router->post('/admin/advogados/{id}/status', 'LawyerController@changeStatus');

// Alterar ordem de exibição
$router->post('/admin/advogados/ordenar', 'LawyerController@reorder');

// Validar OAB (AJAX)
$router->post('/admin/advogados/validar-oab', 'LawyerController@validateOAB');

// ==================== AGENDAMENTOS ====================

// Listar agendamentos
$router->get('/admin/agendamentos', 'AppointmentAdminController@index');

// Visualizar agendamento
$router->get('/admin/agendamentos/{id}', 'AppointmentAdminController@show');

// Confirmar agendamento
$router->post('/admin/agendamentos/{id}/confirmar', 'AppointmentAdminController@confirm');

// Completar agendamento
$router->post('/admin/agendamentos/{id}/completar', 'AppointmentAdminController@complete');

// Cancelar agendamento
$router->post('/admin/agendamentos/{id}/cancelar', 'AppointmentAdminController@cancel');

// Adicionar notas
$router->post('/admin/agendamentos/{id}/notas', 'AppointmentAdminController@addNotes');

// Deletar agendamento
$router->post('/admin/agendamentos/{id}/deletar', 'AppointmentAdminController@delete');

// Filtrar por status
$router->get('/admin/agendamentos/status/{status}', 'AppointmentAdminController@filterByStatus');

// Filtrar por data
$router->get('/admin/agendamentos/data/{date}', 'AppointmentAdminController@filterByDate');

// Exportar agendamentos (CSV/Excel)
$router->get('/admin/agendamentos/exportar', 'AppointmentAdminController@export');

// ==================== USUÁRIOS ====================

// Listar usuários
$router->get('/admin/usuarios', 'UserController@index');

// Criar usuário
$router->get('/admin/usuarios/criar', 'UserController@create');
$router->post('/admin/usuarios/criar', 'UserController@store');

// Editar usuário
$router->get('/admin/usuarios/{id}/editar', 'UserController@edit');
$router->post('/admin/usuarios/{id}/editar', 'UserController@update');

// Visualizar usuário
$router->get('/admin/usuarios/{id}', 'UserController@show');

// Deletar usuário
$router->post('/admin/usuarios/{id}/deletar', 'UserController@delete');

// Alterar status
$router->post('/admin/usuarios/{id}/status', 'UserController@changeStatus');

// Alterar senha
$router->get('/admin/usuarios/{id}/alterar-senha', 'UserController@changePasswordForm');
$router->post('/admin/usuarios/{id}/alterar-senha', 'UserController@changePassword');

// ==================== PERFIL DO USUÁRIO ====================

// Ver perfil
$router->get('/admin/perfil', 'ProfileController@index');

// Editar perfil
$router->get('/admin/perfil/editar', 'ProfileController@edit');
$router->post('/admin/perfil/editar', 'ProfileController@update');

// Alterar senha própria
$router->get('/admin/perfil/senha', 'ProfileController@changePasswordForm');
$router->post('/admin/perfil/senha', 'ProfileController@changePassword');

// Upload de avatar
$router->post('/admin/perfil/avatar', 'ProfileController@uploadAvatar');

// ==================== CONFIGURAÇÕES ====================

// Configurações gerais
$router->get('/admin/configuracoes', 'SettingsController@index');
$router->post('/admin/configuracoes', 'SettingsController@update');

// Configurações do site
$router->get('/admin/configuracoes/site', 'SettingsController@site');
$router->post('/admin/configuracoes/site', 'SettingsController@updateSite');

// Configurações de SEO
$router->get('/admin/configuracoes/seo', 'SettingsController@seo');
$router->post('/admin/configuracoes/seo', 'SettingsController@updateSeo');

// Configurações de e-mail
$router->get('/admin/configuracoes/email', 'SettingsController@email');
$router->post('/admin/configuracoes/email', 'SettingsController@updateEmail');

// Testar e-mail
$router->post('/admin/configuracoes/email/testar', 'SettingsController@testEmail');

// Configurações de WhatsApp
$router->get('/admin/configuracoes/whatsapp', 'SettingsController@whatsapp');
$router->post('/admin/configuracoes/whatsapp', 'SettingsController@updateWhatsApp');

// Testar WhatsApp
$router->post('/admin/configuracoes/whatsapp/testar', 'SettingsController@testWhatsApp');

// Configurações de redes sociais
$router->get('/admin/configuracoes/redes-sociais', 'SettingsController@socialMedia');
$router->post('/admin/configuracoes/redes-sociais', 'SettingsController@updateSocialMedia');

// ==================== LOGS DE ATIVIDADES ====================

// Listar logs
$router->get('/admin/logs', 'ActivityLogController@index');

// Visualizar log específico
$router->get('/admin/logs/{id}', 'ActivityLogController@show');

// Filtrar logs
$router->get('/admin/logs/usuario/{userId}', 'ActivityLogController@byUser');
$router->get('/admin/logs/acao/{action}', 'ActivityLogController@byAction');
$router->get('/admin/logs/data/{date}', 'ActivityLogController@byDate');

// Limpar logs antigos
$router->post('/admin/logs/limpar', 'ActivityLogController@clean');

// Exportar logs
$router->get('/admin/logs/exportar', 'ActivityLogController@export');

// ==================== MÍDIA/ARQUIVOS ====================

// Biblioteca de mídia
$router->get('/admin/media', 'MediaController@index');

// Upload de arquivo
$router->post('/admin/media/upload', 'MediaController@upload');

// Deletar arquivo
$router->post('/admin/media/{id}/deletar', 'MediaController@delete');

// Buscar mídia (AJAX)
$router->get('/admin/media/buscar', 'MediaController@search');

// ==================== BACKUP ====================

// Criar backup do banco de dados
$router->post('/admin/backup/criar', 'BackupController@create');

// Listar backups
$router->get('/admin/backup', 'BackupController@index');

// Download backup
$router->get('/admin/backup/{file}/download', 'BackupController@download');

// Deletar backup
$router->post('/admin/backup/{file}/deletar', 'BackupController@delete');

// ==================== RELATÓRIOS ====================

// Dashboard de relatórios
$router->get('/admin/relatorios', 'ReportController@index');

// Relatório de posts
$router->get('/admin/relatorios/posts', 'ReportController@posts');

// Relatório de agendamentos
$router->get('/admin/relatorios/agendamentos', 'ReportController@appointments');

// Relatório de acessos
$router->get('/admin/relatorios/acessos', 'ReportController@access');

// ==================== API ADMIN (AJAX) ====================

// Dashboard - Estatísticas rápidas
$router->get('/admin/api/stats', 'AdminApiController@getStats');

// Notificações
$router->get('/admin/api/notificacoes', 'AdminApiController@getNotifications');

// Marcar notificação como lida
$router->post('/admin/api/notificacoes/{id}/ler', 'AdminApiController@markAsRead');

// Busca global
$router->get('/admin/api/buscar', 'AdminApiController@globalSearch');