<?php

namespace Controllers\Admin;

use Core\Controller;
use Models\User;
use Models\ActivityLog;
use Models\PasswordReset;

/**
 * AuthController - Gerencia autenticação administrativa
 */
class AuthController extends Controller
{
    private $userModel;
    private $activityLogModel;
    private $passwordResetModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->activityLogModel = new ActivityLog();
        $this->passwordResetModel = new PasswordReset();
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

        // Obtém e sanitiza dados
        $email = $this->sanitize($this->post('email'));
        $password = $this->post('password'); // Senha não deve ser sanitizada, será hasheada
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

        // Verifica se pode solicitar novo token (rate limiting)
        $waitTime = $this->passwordResetModel->getTimeUntilNextRequest($email);
        if ($waitTime > 0) {
            $minutes = ceil($waitTime / 60);
            flash('error', "Aguarde {$minutes} minutos antes de solicitar novo link");
            $this->redirect('admin/recuperar-senha');
        }

        // Cria token
        $token = $this->passwordResetModel->createToken($email);

        // Envia e-mail com link de reset
        $resetLink = base_url("admin/redefinir-senha/{$token}");
        $this->sendPasswordResetEmail($user, $resetLink);

        // Log da ação
        $this->activityLogModel->create([
            'user_id' => $user['id'],
            'action' => 'password_reset_requested',
            'entity_type' => 'user',
            'entity_id' => $user['id'],
            'description' => 'Solicitação de redefinição de senha',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        flash('success', 'Instruções enviadas para seu e-mail');
        $this->redirect('admin/login');
    }

    /**
     * Exibe formulário de redefinir senha
     */
    public function showResetPasswordForm($token)
    {
        // Verifica se token é válido
        $tokenData = $this->passwordResetModel->validateToken($token);

        if (!$tokenData) {
            flash('error', 'Link de redefinição inválido ou expirado');
            $this->redirect('admin/recuperar-senha');
        }

        $pageTitle = 'Redefinir Senha';

        $this->view('admin/reset-password', [
            'pageTitle' => $pageTitle,
            'token' => $token,
            'email' => $tokenData['email']
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
        if (empty($token) || empty($password) || empty($passwordConfirm)) {
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

        // Verifica token
        $tokenData = $this->passwordResetModel->validateToken($token);

        if (!$tokenData) {
            flash('error', 'Link de redefinição inválido ou expirado');
            $this->redirect('admin/recuperar-senha');
        }

        // Busca usuário
        $user = $this->userModel->findByEmail($tokenData['email']);

        if (!$user) {
            flash('error', 'Usuário não encontrado');
            $this->redirect('admin/login');
        }

        // Atualiza senha
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $this->userModel->update($user['id'], ['password' => $hashedPassword]);

        // Marca token como usado
        $this->passwordResetModel->markAsUsed($token);

        // Log da ação
        $this->activityLogModel->create([
            'user_id' => $user['id'],
            'action' => 'password_reset_completed',
            'entity_type' => 'user',
            'entity_id' => $user['id'],
            'description' => 'Senha redefinida com sucesso',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        flash('success', 'Senha redefinida com sucesso! Faça login com sua nova senha');
        $this->redirect('admin/login');
    }

    /**
     * Envia e-mail de recuperação de senha
     *
     * @param array $user
     * @param string $resetLink
     */
    private function sendPasswordResetEmail($user, $resetLink)
    {
        $to = $user['email'];
        $subject = 'Recuperação de Senha - ' . app_name();

        $message = "
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #f9f9f9;
                }
                .content {
                    background-color: #ffffff;
                    padding: 30px;
                    border-radius: 5px;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                }
                .button {
                    display: inline-block;
                    padding: 12px 30px;
                    background-color: #06253D;
                    color: #ffffff;
                    text-decoration: none;
                    border-radius: 5px;
                    margin: 20px 0;
                }
                .footer {
                    margin-top: 20px;
                    padding-top: 20px;
                    border-top: 1px solid #ddd;
                    font-size: 12px;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='content'>
                    <h2>Recuperação de Senha</h2>
                    <p>Olá, <strong>{$user['name']}</strong>!</p>
                    <p>Recebemos uma solicitação para redefinir a senha da sua conta.</p>
                    <p>Clique no botão abaixo para redefinir sua senha:</p>
                    <p style='text-align: center;'>
                        <a href='{$resetLink}' class='button'>Redefinir Senha</a>
                    </p>
                    <p>Ou copie e cole este link no seu navegador:</p>
                    <p style='word-break: break-all; color: #06253D;'>{$resetLink}</p>
                    <p><strong>Este link expira em 1 hora.</strong></p>
                    <p>Se você não solicitou esta redefinição, ignore este e-mail.</p>
                    <div class='footer'>
                        <p>Este é um e-mail automático, por favor não responda.</p>
                        <p>&copy; " . date('Y') . " " . app_name() . ". Todos os direitos reservados.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . (getenv('MAIL_FROM_ADDRESS') ?: 'noreply@sistema.com.br'),
            'Reply-To: ' . (getenv('MAIL_FROM_ADDRESS') ?: 'noreply@sistema.com.br')
        ];

        // Envia email
        mail($to, $subject, $message, implode("\r\n", $headers));
    }
}