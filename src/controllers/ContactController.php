<?php

namespace Controllers;

use Core\Controller;
use Models\Setting;

/**
 * ContactController - Gerencia formulário de contato
 */
class ContactController extends Controller
{
    private $settingModel;

    public function __construct()
    {
        $this->settingModel = new Setting();
    }

    /**
     * Exibe página de contato
     */
    public function index()
    {
        // Busca informações de contato
        $siteInfo = $this->settingModel->getSiteInfo();

        // Define dados da página
        $pageTitle = 'Contato - Entre em contato conosco';
        $pageDescription = 'Entre em contato com nosso escritório de advocacia';

        // Carrega view
        $this->viewWithLayout('pages/contact', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'siteInfo' => $siteInfo
        ]);
    }

    /**
     * Processa envio do formulário
     */
    public function send()
    {
        // Verifica se é POST
        if (!$this->isPost()) {
            $this->redirect('contato');
        }

        // Obtém dados
        $data = [
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'phone' => $this->post('phone'),
            'subject' => $this->post('subject'),
            'message' => $this->post('message')
        ];

        // Valida dados
        $errors = $this->validate($data, [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'subject' => 'required|min:5',
            'message' => 'required|min:10'
        ]);

        // Se houver erros
        if (!empty($errors)) {
            set_old($data);
            $errorMessages = [];
            foreach ($errors as $field => $fieldErrors) {
                $errorMessages = array_merge($errorMessages, $fieldErrors);
            }
            flash('error', implode('<br>', $errorMessages));
            $this->redirect('contato');
        }

        // Sanitiza dados
        $data = $this->sanitize($data);

        // Envia e-mail
        $emailSent = $this->sendContactEmail($data);

        if ($emailSent) {
            flash('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
        } else {
            flash('error', 'Erro ao enviar mensagem. Tente novamente mais tarde.');
        }

        clear_old();
        $this->redirect('contato');
    }

    /**
     * Envia e-mail de contato
     */
    private function sendContactEmail($data)
    {
        // Obtém e-mail de destino
        $toEmail = $this->settingModel->get('site_email', getenv('MAIL_FROM_ADDRESS'));

        // Assunto
        $subject = 'Contato via Site: ' . $data['subject'];

        // Corpo do e-mail
        $message = "
        <html>
        <head>
            <title>Nova mensagem de contato</title>
        </head>
        <body>
            <h2>Nova mensagem recebida via formulário de contato</h2>
            
            <p><strong>Nome:</strong> {$data['name']}</p>
            <p><strong>E-mail:</strong> {$data['email']}</p>
            <p><strong>Telefone:</strong> " . ($data['phone'] ?? 'Não informado') . "</p>
            <p><strong>Assunto:</strong> {$data['subject']}</p>
            
            <h3>Mensagem:</h3>
            <p>" . nl2br($data['message']) . "</p>
            
            <hr>
            <p><small>Esta mensagem foi enviada através do formulário de contato do site.</small></p>
        </body>
        </html>
        ";

        // Headers
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $data['email'],
            'Reply-To: ' . $data['email'],
            'X-Mailer: PHP/' . phpversion()
        ];

        // Envia e-mail
        return mail($toEmail, $subject, $message, implode("\r\n", $headers));
    }
}