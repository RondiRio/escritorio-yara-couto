<?php

namespace Controllers\Admin;

use Core\Controller;
use Models\Setting;
use Models\ActivityLog;

/**
 * SettingsController - Gerencia configurações do sistema
 */
class SettingsController extends Controller
{
    protected $middlewares = ['AuthMiddleware', ['RoleMiddleware', 'admin']];

    private $settingModel;
    private $activityLogModel;

    public function __construct()
    {
        $this->settingModel = new Setting();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * Exibe página de configurações
     */
    public function index()
    {
        // Busca todas as configurações agrupadas
        $settings = [
            'general' => $this->settingModel->getByGroup('general'),
            'seo' => $this->settingModel->getByGroup('seo'),
            'email' => $this->settingModel->getByGroup('email'),
            'social' => $this->settingModel->getByGroup('social'),
            'whatsapp' => $this->settingModel->getByGroup('whatsapp')
        ];

        // Tab ativa (via query string)
        $activeTab = $this->get('tab', 'general');

        $this->view('admin/settings/index', [
            'pageTitle' => 'Configurações do Sistema',
            'settings' => $settings,
            'activeTab' => $activeTab
        ]);
    }

    /**
     * Atualiza configurações gerais
     */
    public function updateGeneral()
    {
        if (!$this->isPost()) {
            redirect('/admin/configuracoes');
        }

        $data = [
            'site_name' => $this->post('site_name'),
            'site_description' => $this->post('site_description'),
            'site_email' => $this->post('site_email'),
            'site_phone' => $this->post('site_phone'),
            'site_whatsapp' => $this->post('site_whatsapp'),
            'site_address' => $this->post('site_address'),
            'oab_number' => $this->post('oab_number'),
            'oab_state' => $this->post('oab_state')
        ];

        // Validações
        $errors = $this->validate($data, [
            'site_name' => 'required|min:3',
            'site_email' => 'required|email'
        ]);

        if (!empty($errors)) {
            flash('error', 'Corrija os erros abaixo');
            $_SESSION['errors'] = $errors;
            redirect('/admin/configuracoes?tab=general');
        }

        // Atualiza cada configuração
        foreach ($data as $key => $value) {
            $this->settingModel->set($key, $value, 'string', 'general');
        }

        // Log
        $this->activityLogModel->create([
            'user_id' => auth_id(),
            'action' => 'settings_updated',
            'entity_type' => 'settings',
            'entity_id' => null,
            'description' => 'Configurações gerais atualizadas',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        flash('success', 'Configurações gerais atualizadas com sucesso!');
        redirect('/admin/configuracoes?tab=general');
    }

    /**
     * Atualiza configurações de SEO
     */
    public function updateSeo()
    {
        if (!$this->isPost()) {
            redirect('/admin/configuracoes');
        }

        $data = [
            'meta_title' => $this->post('meta_title'),
            'meta_description' => $this->post('meta_description'),
            'meta_keywords' => $this->post('meta_keywords'),
            'google_analytics_id' => $this->post('google_analytics_id'),
            'google_tag_manager_id' => $this->post('google_tag_manager_id'),
            'facebook_pixel_id' => $this->post('facebook_pixel_id')
        ];

        // Validações
        $errors = $this->validate($data, [
            'meta_title' => 'required|min:10|max:60',
            'meta_description' => 'required|min:50|max:160'
        ]);

        if (!empty($errors)) {
            flash('error', 'Corrija os erros abaixo');
            $_SESSION['errors'] = $errors;
            redirect('/admin/configuracoes?tab=seo');
        }

        // Atualiza cada configuração
        foreach ($data as $key => $value) {
            $this->settingModel->set($key, $value, 'string', 'seo');
        }

        // Log
        $this->activityLogModel->create([
            'user_id' => auth_id(),
            'action' => 'settings_updated',
            'entity_type' => 'settings',
            'entity_id' => null,
            'description' => 'Configurações de SEO atualizadas',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        flash('success', 'Configurações de SEO atualizadas com sucesso!');
        redirect('/admin/configuracoes?tab=seo');
    }

    /**
     * Atualiza configurações de email
     */
    public function updateEmail()
    {
        if (!$this->isPost()) {
            redirect('/admin/configuracoes');
        }

        $data = [
            'mail_driver' => $this->post('mail_driver'),
            'mail_host' => $this->post('mail_host'),
            'mail_port' => $this->post('mail_port'),
            'mail_username' => $this->post('mail_username'),
            'mail_password' => $this->post('mail_password'),
            'mail_encryption' => $this->post('mail_encryption'),
            'mail_from_address' => $this->post('mail_from_address'),
            'mail_from_name' => $this->post('mail_from_name')
        ];

        // Validações
        $errors = $this->validate($data, [
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required'
        ]);

        if (!empty($errors)) {
            flash('error', 'Corrija os erros abaixo');
            $_SESSION['errors'] = $errors;
            redirect('/admin/configuracoes?tab=email');
        }

        // Atualiza cada configuração
        foreach ($data as $key => $value) {
            $this->settingModel->set($key, $value, 'string', 'email');
        }

        // Atualiza .env também (para PHPMailer)
        $this->updateEnvFile($data);

        // Log
        $this->activityLogModel->create([
            'user_id' => auth_id(),
            'action' => 'settings_updated',
            'entity_type' => 'settings',
            'entity_id' => null,
            'description' => 'Configurações de email atualizadas',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        flash('success', 'Configurações de email atualizadas com sucesso!');
        redirect('/admin/configuracoes?tab=email');
    }

    /**
     * Atualiza configurações de redes sociais
     */
    public function updateSocial()
    {
        if (!$this->isPost()) {
            redirect('/admin/configuracoes');
        }

        $data = [
            'facebook_url' => $this->post('facebook_url'),
            'instagram_url' => $this->post('instagram_url'),
            'twitter_url' => $this->post('twitter_url'),
            'linkedin_url' => $this->post('linkedin_url'),
            'youtube_url' => $this->post('youtube_url')
        ];

        // Atualiza cada configuração
        foreach ($data as $key => $value) {
            $this->settingModel->set($key, $value, 'string', 'social');
        }

        // Log
        $this->activityLogModel->create([
            'user_id' => auth_id(),
            'action' => 'settings_updated',
            'entity_type' => 'settings',
            'entity_id' => null,
            'description' => 'Configurações de redes sociais atualizadas',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        flash('success', 'Configurações de redes sociais atualizadas com sucesso!');
        redirect('/admin/configuracoes?tab=social');
    }

    /**
     * Atualiza configurações do WhatsApp
     */
    public function updateWhatsapp()
    {
        if (!$this->isPost()) {
            redirect('/admin/configuracoes');
        }

        $data = [
            'whatsapp_enabled' => $this->post('whatsapp_enabled', '0'),
            'whatsapp_phone' => $this->post('whatsapp_phone'),
            'whatsapp_api_url' => $this->post('whatsapp_api_url'),
            'whatsapp_api_token' => $this->post('whatsapp_api_token'),
            'whatsapp_message_template' => $this->post('whatsapp_message_template')
        ];

        // Atualiza cada configuração
        foreach ($data as $key => $value) {
            $type = $key === 'whatsapp_enabled' ? 'boolean' : 'string';
            $this->settingModel->set($key, $value, $type, 'whatsapp');
        }

        // Atualiza .env também
        $this->updateEnvFile([
            'whatsapp_api_url' => $data['whatsapp_api_url'],
            'whatsapp_api_token' => $data['whatsapp_api_token'],
            'whatsapp_phone' => $data['whatsapp_phone']
        ]);

        // Log
        $this->activityLogModel->create([
            'user_id' => auth_id(),
            'action' => 'settings_updated',
            'entity_type' => 'settings',
            'entity_id' => null,
            'description' => 'Configurações do WhatsApp atualizadas',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);

        flash('success', 'Configurações do WhatsApp atualizadas com sucesso!');
        redirect('/admin/configuracoes?tab=whatsapp');
    }

    /**
     * Testa conexão de email
     */
    public function testEmail()
    {
        if (!$this->isPost()) {
            return $this->json(['error' => 'Método não permitido'], 405);
        }

        $testEmail = $this->post('test_email');

        if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Email inválido'], 400);
        }

        try {
            $mailer = \Core\Mailer::getInstance();

            // Testa conexão
            $connectionTest = $mailer->testConnection();

            if (!$connectionTest['success']) {
                return $this->json([
                    'success' => false,
                    'message' => $connectionTest['message']
                ]);
            }

            // Envia email de teste
            $subject = 'Teste de Configuração de Email - ' . app_name();
            $message = "
            <html>
            <body style='font-family: Arial, sans-serif;'>
                <h2>Teste de Email</h2>
                <p>Este é um email de teste para verificar a configuração do sistema.</p>
                <p><strong>Data/Hora:</strong> " . date('d/m/Y H:i:s') . "</p>
                <p><strong>Sistema:</strong> " . app_name() . "</p>
                <hr>
                <p style='color: #666; font-size: 12px;'>
                    Se você recebeu este email, significa que suas configurações de email estão funcionando corretamente!
                </p>
            </body>
            </html>
            ";

            $sent = $mailer->send($testEmail, $subject, $message);

            if ($sent) {
                // Log
                $this->activityLogModel->create([
                    'user_id' => auth_id(),
                    'action' => 'email_test_sent',
                    'entity_type' => 'settings',
                    'entity_id' => null,
                    'description' => "Email de teste enviado para: {$testEmail}",
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
                ]);

                return $this->json([
                    'success' => true,
                    'message' => "Email de teste enviado com sucesso para {$testEmail}! Verifique sua caixa de entrada."
                ]);
            }

            return $this->json([
                'success' => false,
                'message' => 'Erro ao enviar email de teste. Verifique os logs.'
            ]);

        } catch (\Exception $e) {
            error_log("Erro no teste de email: {$e->getMessage()}");
            return $this->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Limpa cache do sistema
     */
    public function clearCache()
    {
        if (!$this->isPost()) {
            return $this->json(['error' => 'Método não permitido'], 405);
        }

        $cacheDir = __DIR__ . '/../../../storage/cache/';

        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '*');
            $count = 0;

            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                    $count++;
                }
            }

            // Log
            $this->activityLogModel->create([
                'user_id' => auth_id(),
                'action' => 'cache_cleared',
                'entity_type' => 'system',
                'entity_id' => null,
                'description' => "Cache limpo: {$count} arquivos removidos",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            return $this->json([
                'success' => true,
                'message' => "Cache limpo com sucesso! {$count} arquivos removidos."
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Diretório de cache não encontrado.'
        ]);
    }

    /**
     * Atualiza arquivo .env
     *
     * @param array $data Dados para atualizar
     */
    private function updateEnvFile($data)
    {
        $envFile = __DIR__ . '/../../../.env';

        if (!file_exists($envFile)) {
            return false;
        }

        $envContent = file_get_contents($envFile);

        // Mapeia chaves do formulário para chaves do .env
        $envMap = [
            'mail_driver' => 'MAIL_DRIVER',
            'mail_host' => 'MAIL_HOST',
            'mail_port' => 'MAIL_PORT',
            'mail_username' => 'MAIL_USERNAME',
            'mail_password' => 'MAIL_PASSWORD',
            'mail_encryption' => 'MAIL_ENCRYPTION',
            'mail_from_address' => 'MAIL_FROM_ADDRESS',
            'mail_from_name' => 'MAIL_FROM_NAME',
            'whatsapp_api_url' => 'WHATSAPP_API_URL',
            'whatsapp_api_token' => 'WHATSAPP_API_TOKEN',
            'whatsapp_phone' => 'WHATSAPP_PHONE'
        ];

        foreach ($data as $key => $value) {
            if (isset($envMap[$key])) {
                $envKey = $envMap[$key];
                $pattern = "/^{$envKey}=.*/m";
                $replacement = "{$envKey}={$value}";

                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, $replacement, $envContent);
                } else {
                    // Se não existir, adiciona no final
                    $envContent .= "\n{$replacement}";
                }
            }
        }

        return file_put_contents($envFile, $envContent);
    }

    /**
     * Obtém informações do sistema
     */
    public function systemInfo()
    {
        $info = [
            'php_version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'max_upload_size' => ini_get('upload_max_filesize'),
            'max_post_size' => ini_get('post_max_size'),
            'memory_limit' => ini_get('memory_limit'),
            'timezone' => date_default_timezone_get(),
            'disk_free_space' => $this->formatBytes(disk_free_space(__DIR__)),
            'disk_total_space' => $this->formatBytes(disk_total_space(__DIR__))
        ];

        return $this->json($info);
    }

    /**
     * Formata bytes para leitura humana
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
