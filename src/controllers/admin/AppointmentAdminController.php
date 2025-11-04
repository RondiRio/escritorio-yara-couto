<?php

namespace Controllers\Admin;

use Core\Controller;
use Models\Appointment;
use Models\ActivityLog;

/**
 * AppointmentAdminController - Gerencia agendamentos (Admin)
 */
class AppointmentAdminController extends Controller
{
    private $appointmentModel;
    private $activityLogModel;

    public function __construct()
    {
        $this->appointmentModel = new Appointment();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * Lista agendamentos
     */
    public function index()
    {
        $this->requireAuth();

        // Filtro por status
        $status = $this->get('status');
        
        if ($status) {
            $appointments = $this->appointmentModel->getByStatus($status);
        } else {
            $appointments = $this->appointmentModel->all('created_at DESC');
        }

        // Estatísticas
        $stats = $this->appointmentModel->getStatistics();

        $pageTitle = 'Gerenciar Agendamentos';

        $this->view('admin/appointments/index', [
            'pageTitle' => $pageTitle,
            'appointments' => $appointments,
            'stats' => $stats,
            'currentStatus' => $status
        ]);
    }

    /**
     * Visualiza agendamento
     */
    public function show($id)
    {
        $this->requireAuth();

        $appointment = $this->appointmentModel->find($id);

        if (!$appointment) {
            flash('error', 'Agendamento não encontrado');
            $this->redirect('admin/agendamentos');
        }

        $pageTitle = 'Agendamento #' . $id;

        $this->view('admin/appointments/show', [
            'pageTitle' => $pageTitle,
            'appointment' => $appointment
        ]);
    }

    /**
     * Confirma agendamento
     */
    public function confirm($id)
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->redirect('admin/agendamentos');
        }

        $appointment = $this->appointmentModel->find($id);

        if (!$appointment) {
            flash('error', 'Agendamento não encontrado');
            $this->redirect('admin/agendamentos');
        }

        $adminNotes = $this->post('admin_notes');

        $confirmed = $this->appointmentModel->confirm($id, $adminNotes);

        if ($confirmed !== false) {
            // Envia e-mail de confirmação
            $this->sendConfirmationEmail($appointment);

            // Log
            $this->activityLogModel->logUpdate(
                'appointment',
                $id,
                "Agendamento confirmado: {$appointment['name']}"
            );

            flash('success', 'Agendamento confirmado com sucesso!');
        } else {
            flash('error', 'Erro ao confirmar agendamento');
        }

        $this->redirect('admin/agendamentos/' . $id);
    }

    /**
     * Completa agendamento
     */
    public function complete($id)
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->redirect('admin/agendamentos');
        }

        $appointment = $this->appointmentModel->find($id);

        if (!$appointment) {
            flash('error', 'Agendamento não encontrado');
            $this->redirect('admin/agendamentos');
        }

        $adminNotes = $this->post('admin_notes');

        $completed = $this->appointmentModel->complete($id, $adminNotes);

        if ($completed !== false) {
            // Log
            $this->activityLogModel->logUpdate(
                'appointment',
                $id,
                "Agendamento completado: {$appointment['name']}"
            );

            flash('success', 'Agendamento marcado como completado!');
        } else {
            flash('error', 'Erro ao completar agendamento');
        }

        $this->redirect('admin/agendamentos/' . $id);
    }

    /**
     * Cancela agendamento
     */
    public function cancel($id)
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->redirect('admin/agendamentos');
        }

        $appointment = $this->appointmentModel->find($id);

        if (!$appointment) {
            flash('error', 'Agendamento não encontrado');
            $this->redirect('admin/agendamentos');
        }

        $reason = $this->post('reason');

        $cancelled = $this->appointmentModel->cancel($id, $reason);

        if ($cancelled !== false) {
            // Envia e-mail de cancelamento
            $this->sendCancellationEmail($appointment, $reason);

            // Log
            $this->activityLogModel->logUpdate(
                'appointment',
                $id,
                "Agendamento cancelado: {$appointment['name']}"
            );

            flash('success', 'Agendamento cancelado com sucesso!');
        } else {
            flash('error', 'Erro ao cancelar agendamento');
        }

        $this->redirect('admin/agendamentos/' . $id);
    }

    /**
     * Adiciona notas
     */
    public function addNotes($id)
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->json(['error' => 'Método não permitido'], 405);
        }

        $notes = $this->post('notes');

        if (empty($notes)) {
            $this->json(['error' => 'Notas são obrigatórias'], 400);
        }

        $updated = $this->appointmentModel->addNotes($id, $notes);

        if ($updated !== false) {
            $this->json([
                'success' => true,
                'message' => 'Notas adicionadas'
            ]);
        } else {
            $this->json(['error' => 'Erro ao adicionar notas'], 500);
        }
    }

    /**
     * Deleta agendamento
     */
    public function delete($id)
    {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->redirect('admin/agendamentos');
        }

        $appointment = $this->appointmentModel->find($id);

        if (!$appointment) {
            flash('error', 'Agendamento não encontrado');
            $this->redirect('admin/agendamentos');
        }

        $deleted = $this->appointmentModel->delete($id);

        if ($deleted) {
            // Log
            $this->activityLogModel->logDelete(
                'appointment',
                $id,
                "Agendamento deletado: {$appointment['name']}"
            );

            flash('success', 'Agendamento deletado com sucesso!');
        } else {
            flash('error', 'Erro ao deletar agendamento');
        }

        $this->redirect('admin/agendamentos');
    }

    /**
     * Filtra por status
     */
    public function filterByStatus($status)
    {
        $this->requireAuth();

        if (!in_array($status, ['pending', 'confirmed', 'completed', 'cancelled'])) {
            flash('error', 'Status inválido');
            $this->redirect('admin/agendamentos');
        }

        $this->redirect('admin/agendamentos?status=' . $status);
    }

    /**
     * Filtra por data
     */
    public function filterByDate($date)
    {
        $this->requireAuth();

        // Busca agendamentos da data
        $appointments = $this->appointmentModel->where('preferred_date', '=', $date);

        $pageTitle = 'Agendamentos de ' . format_date($date);

        $this->view('admin/appointments/index', [
            'pageTitle' => $pageTitle,
            'appointments' => $appointments,
            'filterDate' => $date
        ]);
    }

    /**
     * Exporta agendamentos
     */
    public function export()
    {
        $this->requireAuth();

        $appointments = $this->appointmentModel->all('created_at DESC');

        // Gera CSV
        $filename = 'agendamentos_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cabeçalho
        fputcsv($output, [
            'ID', 'Nome', 'E-mail', 'Telefone', 'Data', 'Horário', 
            'Tipo de Consulta', 'Status', 'Criado em'
        ]);
        
        // Dados
        foreach ($appointments as $appointment) {
            fputcsv($output, [
                $appointment['id'],
                $appointment['name'],
                $appointment['email'],
                $appointment['phone'],
                format_date($appointment['preferred_date']),
                $appointment['preferred_time'],
                $appointment['consultation_type'],
                $appointment['status'],
                format_datetime($appointment['created_at'])
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Envia e-mail de confirmação
     */
    private function sendConfirmationEmail($appointment)
    {
        $subject = "Agendamento Confirmado - Escritório Yara Couto Vitoria";
        
        $message = "
        <html>
        <body>
            <h2>Agendamento Confirmado</h2>
            <p>Olá, <strong>{$appointment['name']}</strong>!</p>
            <p>Seu agendamento foi confirmado:</p>
            <p><strong>Data:</strong> " . format_date($appointment['preferred_date']) . "</p>
            <p><strong>Horário:</strong> {$appointment['preferred_time']}</p>
            <p>Aguardamos você!</p>
        </body>
        </html>
        ";
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . getenv('MAIL_FROM_ADDRESS')
        ];
        
        mail($appointment['email'], $subject, $message, implode("\r\n", $headers));
    }

    /**
     * Envia e-mail de cancelamento
     */
    private function sendCancellationEmail($appointment, $reason)
    {
        $subject = "Agendamento Cancelado - Escritório Yara Couto Vitoria";
        
        $message = "
        <html>
        <body>
            <h2>Agendamento Cancelado</h2>
            <p>Olá, <strong>{$appointment['name']}</strong>!</p>
            <p>Informamos que seu agendamento foi cancelado.</p>
            " . ($reason ? "<p><strong>Motivo:</strong> {$reason}</p>" : "") . "
            <p>Entre em contato conosco para reagendar.</p>
        </body>
        </html>
        ";
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . getenv('MAIL_FROM_ADDRESS')
        ];
        
        mail($appointment['email'], $subject, $message, implode("\r\n", $headers));
    }
}