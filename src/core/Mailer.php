<?php

namespace Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/**
 * Classe Mailer - Gerencia envio de emails usando PHPMailer
 * Centraliza toda a lógica de envio de emails da aplicação
 */
class Mailer
{
    private $mailer;
    private $config;

    /**
     * Construtor - Configura PHPMailer com variáveis de ambiente
     */
    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->loadConfig();
        $this->configure();
    }

    /**
     * Carrega configurações de email do .env
     */
    private function loadConfig()
    {
        $this->config = [
            'driver' => getenv('MAIL_DRIVER') ?: 'smtp',
            'host' => getenv('MAIL_HOST') ?: 'smtp.gmail.com',
            'port' => getenv('MAIL_PORT') ?: 587,
            'username' => getenv('MAIL_USERNAME') ?: '',
            'password' => getenv('MAIL_PASSWORD') ?: '',
            'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
            'from_address' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@sistema.com.br',
            'from_name' => getenv('MAIL_FROM_NAME') ?: app_name(),
            'debug' => getenv('APP_DEBUG') === 'true'
        ];
    }

    /**
     * Configura PHPMailer com as configurações carregadas
     */
    private function configure()
    {
        try {
            // Configurações do servidor
            if ($this->config['driver'] === 'smtp') {
                $this->mailer->isSMTP();
                $this->mailer->Host = $this->config['host'];
                $this->mailer->SMTPAuth = true;
                $this->mailer->Username = $this->config['username'];
                $this->mailer->Password = $this->config['password'];

                // Configuração de criptografia
                if ($this->config['encryption'] === 'tls') {
                    $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                } elseif ($this->config['encryption'] === 'ssl') {
                    $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                }

                $this->mailer->Port = $this->config['port'];
            }

            // Debug (apenas em desenvolvimento)
            if ($this->config['debug']) {
                $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
            }

            // Configurações padrão
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->setFrom($this->config['from_address'], $this->config['from_name']);
            $this->mailer->isHTML(true);

        } catch (Exception $e) {
            error_log("Erro ao configurar PHPMailer: {$e->getMessage()}");
        }
    }

    /**
     * Envia um email
     *
     * @param string $to Email do destinatário
     * @param string $subject Assunto
     * @param string $body Corpo do email (HTML)
     * @param string $toName Nome do destinatário (opcional)
     * @param string $altBody Versão texto do email (opcional)
     * @return bool True se enviado com sucesso
     */
    public function send($to, $subject, $body, $toName = '', $altBody = '')
    {
        try {
            // Limpa destinatários anteriores
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            // Define destinatário
            $this->mailer->addAddress($to, $toName);

            // Define conteúdo
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;

            if (!empty($altBody)) {
                $this->mailer->AltBody = $altBody;
            } else {
                // Gera versão texto automaticamente
                $this->mailer->AltBody = strip_tags($body);
            }

            // Envia
            $result = $this->mailer->send();

            // Log de sucesso
            if ($result) {
                $this->logEmail($to, $subject, 'sent');
            }

            return $result;

        } catch (Exception $e) {
            // Log de erro
            $this->logEmail($to, $subject, 'failed', $e->getMessage());
            error_log("Erro ao enviar email: {$this->mailer->ErrorInfo}");
            return false;
        }
    }

    /**
     * Envia email usando um template
     *
     * @param string $to Email do destinatário
     * @param string $subject Assunto
     * @param string $template Nome do template
     * @param array $data Dados para o template
     * @return bool
     */
    public function sendTemplate($to, $subject, $template, $data = [])
    {
        $body = $this->renderTemplate($template, $data);
        return $this->send($to, $subject, $body);
    }

    /**
     * Renderiza um template de email
     *
     * @param string $template Nome do template
     * @param array $data Dados para o template
     * @return string HTML renderizado
     */
    private function renderTemplate($template, $data = [])
    {
        $templatePath = __DIR__ . "/../views/emails/{$template}.php";

        if (!file_exists($templatePath)) {
            // Template não encontrado, retorna mensagem simples
            return "Template de email não encontrado: {$template}";
        }

        // Extrai dados
        extract($data);

        // Captura output do template
        ob_start();
        include $templatePath;
        $html = ob_get_clean();

        return $html;
    }

    /**
     * Adiciona um anexo ao email
     *
     * @param string $path Caminho do arquivo
     * @param string $name Nome do arquivo (opcional)
     * @return bool
     */
    public function addAttachment($path, $name = '')
    {
        try {
            $this->mailer->addAttachment($path, $name);
            return true;
        } catch (Exception $e) {
            error_log("Erro ao adicionar anexo: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Adiciona destinatário em cópia (CC)
     *
     * @param string $email
     * @param string $name
     */
    public function addCC($email, $name = '')
    {
        $this->mailer->addCC($email, $name);
    }

    /**
     * Adiciona destinatário em cópia oculta (BCC)
     *
     * @param string $email
     * @param string $name
     */
    public function addBCC($email, $name = '')
    {
        $this->mailer->addBCC($email, $name);
    }

    /**
     * Define email de resposta
     *
     * @param string $email
     * @param string $name
     */
    public function setReplyTo($email, $name = '')
    {
        $this->mailer->addReplyTo($email, $name);
    }

    /**
     * Registra log de email enviado/falho
     *
     * @param string $to
     * @param string $subject
     * @param string $status sent|failed
     * @param string $error
     */
    private function logEmail($to, $subject, $status, $error = null)
    {
        // Cria diretório de logs se não existir
        $logDir = __DIR__ . '/../../storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/emails.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$status}: To={$to}, Subject={$subject}";

        if ($error) {
            $logMessage .= ", Error={$error}";
        }

        $logMessage .= "\n";

        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    /**
     * Testa configuração de email
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public function testConnection()
    {
        try {
            if ($this->config['driver'] === 'smtp') {
                $this->mailer->smtpConnect();
                $this->mailer->smtpClose();
                return [
                    'success' => true,
                    'message' => 'Conexão SMTP estabelecida com sucesso!'
                ];
            }
            return [
                'success' => true,
                'message' => 'Configuração válida (modo: ' . $this->config['driver'] . ')'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro na conexão: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtém instância singleton do Mailer
     *
     * @return self
     */
    public static function getInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }
}
