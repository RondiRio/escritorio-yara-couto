<?php

namespace Controllers\Admin;

use Core\Controller;
use Models\User;
use Models\ActivityLog;

/**
 * AuthController - Gerencia autenticação administrativa
 */
class AuthController extends Controller
{
    private $userModel;
    private $activityLogModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * Exibe formulário de login
     */
    public function showLoginForm()
    {
        // Se já está autenticado, redireciona para dashboard
        if ($this->isAuthenticated()) {
            $this->redirect('admin/dashboard');
        }

        $pageTitle = 'Login - Área Administrativa';

        $this->view('admin/login', [
            'pageTitle' => $pageTitle
        ]);
    }

    /**
     * Processa login
     */
    public function login()
    {
        // Verifica se é POST
        if (!$this->isPost()) {
            $this->redirect('admin/login');
        }

        // Obtém dados
        $email = $this->post('email');
        $password = $this->post('password');
        $remember = $this->post('remember');

        // Valida dados
        if (empty($email) || empty($password)) {
            flash('error', 'E-mail e senha são obrigatórios');
            $this->redirect('admin/login');
        }

        // Verifica credenciais
        $user = $this->userModel->verifyCredentials($email, $password);

        if (!$user) {
            // Log de tentativa falha
            $this->activityLogModel->log(
                'login_failed',
                'Tentativa de login falhou para: ' . $email,
                'user',
                null
            );

            flash('error', 'E-mail ou senha incorretos');
            $this->redirect('admin/login');
        }

        // Define sessão
        set_auth($user);

        // Atualiza último login
        $this->userModel->updateLastLogin($user['id']);

        // Log de sucesso
        $this->activityLogModel->logLogin($user['id'], true);

        // Redireciona
        flash('success', 'Bem-vindo, ' . $user['name'] . '!');
        $this->redirect('admin/dashboard');
    }

    /**
     * Logout
     */
    public function logout()
    {
        if ($this->isAuthenticated()) {
            $userId = auth_id();
            
            // Log de logout
            $this->activityLogModel->logLogout($userId);
        }

        // Remove sessão
        logout();

        flash('success', 'Você saiu do sistema');
        $this->redirect('admin/login');
    }

    /**
     * Exibe formulário de recuperação de senha
     */
    public function showForgotPasswordForm()
    {
        $pageTitle = 'Recuperar Senha';

        $this->view('admin/forgot-password', [
            'pageTitle' => $pageTitle
        ]);
    }

    /**
     * Processa recuperação de senha
     */
    public function forgotPassword()
    {
        if (!$this->isPost()) {
            $this->redirect('admin/recuperar-senha');
        }

        $email = $this->post('email');

        if (empty($email)) {
            flash('error', 'E-mail é obrigatório');
            $this->redirect('admin/recuperar-senha');
        }

        // Busca usuário
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            // Por segurança, não informar se email existe ou não
            flash('success', 'Se o e-mail existir, você receberá instruções para redefinir sua senha');
            $this->redirect('admin/recuperar-senha');
        }

        // Gera token
        $token = bin2hex(random_bytes(32));
        
        // TODO: Salvar token no banco com expiração
        // TODO: Enviar e-mail com link de reset

        flash('success', 'Instruções enviadas para seu e-mail');
        $this->redirect('admin/login');
    }

    /**
     * Exibe formulário de redefinir senha
     */
    public function showResetPasswordForm($token)
    {
        // TODO: Verificar se token é válido

        $pageTitle = 'Redefinir Senha';

        $this->view('admin/reset-password', [
            'pageTitle' => $pageTitle,
            'token' => $token
        ]);
    }

    /**
     * Processa redefinição de senha
     */
    public function resetPassword()
    {
        if (!$this->isPost()) {
            $this->redirect('admin/login');
        }

        $token = $this->post('token');
        $password = $this->post('password');
        $passwordConfirm = $this->post('password_confirm');

        // Validações
        if (empty($password) || empty($passwordConfirm)) {
            flash('error', 'Preencha todos os campos');
            back();
        }

        if ($password !== $passwordConfirm) {
            flash('error', 'As senhas não coincidem');
            back();
        }

        if (strlen($password) < 6) {
            flash('error', 'A senha deve ter no mínimo 6 caracteres');
            back();
        }

        // TODO: Verificar token e atualizar senha

        flash('success', 'Senha redefinida com sucesso');
        $this->redirect('admin/login');
    }
}