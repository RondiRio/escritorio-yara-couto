<?php

namespace Controllers;

use Core\Controller;
use Models\Appointment;
use Models\Setting;

/**
 * AppointmentController - Gerencia agendamentos públicos
 */
class AppointmentController extends Controller
{
    private $appointmentModel;
    private $settingModel;

    public function __construct()
    {
        $this->appointmentModel = new Appointment();
        $this->settingModel = new Setting();
    }

    /**
     * Exibe formulário de agendamento
     */
    public function index()
    {
        // Busca informações do site
        $siteInfo = $this->settingModel->getSiteInfo();

        // Define dados da página
        $pageTitle = 'Agendar Consulta - Marque seu atendimento';
        $pageDescription = 'Agende uma consulta com nossos advogados especializados';

        // Carrega view
        $this->view('pages/appointment', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'siteInfo' => $siteInfo
        ]);
    }

    /**
     * Cria novo agendamento
     */
    public function create()
    {
        // Verifica se é POST
        if (!$this->isPost()) {
            $this->redirect('agendar');
        }

        // Obtém dados
        $data = [
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'phone' => $this->post('phone'),
            'whatsapp' => $this->post('whatsapp'),
            'preferred_date' => $this->post('preferred_date'),
            'preferred_time' => $this->post('preferred_time'),
            'consultation_type' => $this->post('consultation_type'),
            'message' => $this->post('message')
        ];

        // Valida dados
        $errors = $this->validate($data, [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'phone' => 'required',
            'preferred_date' => 'required',
            'preferred_time' => 'required',
            'consultation_type' => 'required'
        ]);

        // Se houver erros
        if (!empty($errors)) {
            set_old($data);
            $errorMessages = [];
            foreach ($errors as $field => $fieldErrors) {
                $errorMessages = array_merge($errorMessages, $fieldErrors);
            }
            flash('error', implode('<br>', $errorMessages));
            $this->redirect('agendar');
        }

        // Verifica disponibilidade
        $isAvailable = $this->appointmentModel->isTimeAvailable(
            $data['preferred_date'],
            $data['preferred_time']
        );

        if (!$isAvailable) {
            set_old($data);
            flash('error', 'Este horário já está ocupado. Por favor, escolha outro horário.');
            $this->redirect('agendar');
        }

        // Sanitiza dados
        $data = $this->sanitize($data);
        $data['status'] = 'pending';

        // Cria agendamento
        $appointmentId = $this->appointmentModel->create($data);

        if ($appointmentId) {
            // Envia e-mail de confirmação
            $this->sendAppointmentEmail($data, $appointmentId);

            // Envia notificação via WhatsApp (se configurado)
            $this->sendWhatsAppNotification($data);

            clear_old();
            flash('success', 'Agendamento realizado com sucesso! Você receberá uma confirmação por e-mail em breve.');
        } else {
            flash('error', 'Erro ao criar agendamento. Tente novamente mais tarde.');
        }

        $this->redirect('agendar');
    }

    /**
     * Verifica disponibilidade (AJAX)
     */
    public function checkAvailability()
    {
        // Verifica se é POST
        if (!$this->isPost()) {
            $this->json(['error' => 'Método não permitido'], 405);
        }

        $date = $this->post('date');
        $time = $this->post('time');

        if (empty($date) || empty($time)) {
            $this->json(['error' => 'Data e hora são obrigatórios'], 400);
        }

        // Verifica disponibilidade
        $isAvailable = $this->appointmentModel->isTimeAvailable($date, $time);

        $this->json([
            'available' => $isAvailable,
            'message' => $isAvailable ? 'Horário disponível' : 'Horário indisponível'
        ]);
    }

    /**
     * Envia e-mail de confirmação de agendamento
     */
    private function sendAppointmentEmail($data, $appointmentId)
    {
        $toEmail = $data['email'];
        $siteName = $this->settingModel->get('site_name', 'Escritório Yara Couto Vitoria');

        $subject = "Confirmação de Agendamento - {$siteName}";

        $message = "
        <html>
        <head>
            <title>Confirmação de Agendamento</title>
        </head>
        <body>
            <h2>Agendamento Recebido</h2>
            
            <p>Olá, <strong>{$data['name']}</strong>!</p>
            
            <p>Recebemos sua solicitação de agendamento com os seguintes dados:</p>
            
            <p><strong>Número do Agendamento:</strong> #{$appointmentId}</p>
            <p><strong>Data:</strong> " . format_date($data['preferred_date']) . "</p>
            <p><strong>Horário:</strong> {$data['preferred_time']}</p>
            <p><strong>Tipo de Consulta:</strong> {$data['consultation_type']}</p>
            
            <p>Em breve entraremos em contato para confirmar o agendamento.</p>
            
            <hr>
            <p><small>Este é um e-mail automático. Por favor, não responda.</small></p>
        </body>
        </html>
        ";

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . getenv('MAIL_FROM_ADDRESS'),
            'X-Mailer: PHP/' . phpversion()
        ];

        mail($toEmail, $subject, $message, implode("\r\n", $headers));

        // Envia também para o escritório
        $adminEmail = $this->settingModel->get('site_email', getenv('MAIL_FROM_ADDRESS'));
        $adminSubject = "Novo Agendamento - #{$appointmentId}";
        
        mail($adminEmail, $adminSubject, $message, implode("\r\n", $headers));
    }

    /**
     * Envia notificação via WhatsApp
     */
    private function sendWhatsAppNotification($data)
    {
        // Verifica se WhatsApp está configurado
        $whatsappApiUrl = getenv('WHATSAPP_API_URL');
        $whatsappPhone = getenv('WHATSAPP_PHONE');

        if (empty($whatsappApiUrl) || empty($whatsappPhone)) {
            return false;
        }

        // Monta mensagem
        $message = "🔔 *Novo Agendamento*\n\n";
        $message .= "👤 Nome: {$data['name']}\n";
        $message .= "📧 E-mail: {$data['email']}\n";
        $message .= "📱 Telefone: {$data['phone']}\n";
        $message .= "📅 Data: " . format_date($data['preferred_date']) . "\n";
        $message .= "🕐 Horário: {$data['preferred_time']}\n";
        $message .= "📋 Tipo: {$data['consultation_type']}\n";

        if (!empty($data['message'])) {
            $message .= "\n💬 Mensagem:\n{$data['message']}";
        }

        // TODO: Implementar integração com API do WhatsApp
        // Exemplo com cURL para API genérica
        
        return true;
    }
}