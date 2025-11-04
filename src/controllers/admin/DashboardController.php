<?php

namespace Controllers\Admin;

use Core\Controller;
use Models\Post;
use Models\Lawyer;
use Models\Appointment;
use Models\User;
use Models\ActivityLog;

/**
 * DashboardController - Dashboard administrativo
 */
class DashboardController extends Controller
{
    private $postModel;
    private $lawyerModel;
    private $appointmentModel;
    private $userModel;
    private $activityLogModel;

    public function __construct()
    {
        $this->postModel = new Post();
        $this->lawyerModel = new Lawyer();
        $this->appointmentModel = new Appointment();
        $this->userModel = new User();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * Exibe dashboard principal
     */
    public function index()
    {
        // Requer autenticação
        $this->requireAuth();

        // Busca estatísticas gerais
        $stats = $this->getStatistics();

        // Busca agendamentos recentes
        $recentAppointments = $this->appointmentModel->getRecent(5);

        // Busca posts recentes
        $recentPosts = $this->postModel->latest(5);

        // Busca atividades recentes
        $recentActivities = $this->activityLogModel->getRecent(10);

        // Busca agendamentos de hoje
        $todayAppointments = $this->appointmentModel->getToday();

        // Usuário autenticado
        $currentUser = $this->getAuthUser();

        $pageTitle = 'Dashboard - Painel Administrativo';

        $this->view('admin/dashboard', [
            'pageTitle' => $pageTitle,
            'stats' => $stats,
            'recentAppointments' => $recentAppointments,
            'recentPosts' => $recentPosts,
            'recentActivities' => $recentActivities,
            'todayAppointments' => $todayAppointments,
            'currentUser' => $currentUser
        ]);
    }

    /**
     * Busca estatísticas gerais (AJAX)
     */
    public function getStats()
    {
        $this->requireAuth();

        $stats = $this->getStatistics();

        $this->json($stats);
    }

    /**
     * Coleta estatísticas do sistema
     */
    private function getStatistics()
    {
        // Posts
        $totalPosts = $this->postModel->count();
        $publishedPosts = $this->postModel->countByStatus('published');
        $draftPosts = $this->postModel->countByStatus('draft');

        // Advogados
        $totalLawyers = $this->lawyerModel->count();
        $activeLawyers = $this->lawyerModel->count("status = 'active'");
        $totalCasesWon = $this->lawyerModel->getTotalCasesWon();

        // Agendamentos
        $totalAppointments = $this->appointmentModel->count();
        $pendingAppointments = $this->appointmentModel->countByStatus('pending');
        $confirmedAppointments = $this->appointmentModel->countByStatus('confirmed');
        $completedAppointments = $this->appointmentModel->countByStatus('completed');
        $todayAppointmentsCount = $this->appointmentModel->getTodayCount();

        // Usuários
        $totalUsers = $this->userModel->count();
        $activeUsers = $this->userModel->count("status = 'active'");

        // Estatísticas de agendamentos do mês
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');
        $monthStats = $this->appointmentModel->getStatistics($monthStart, $monthEnd);

        return [
            'posts' => [
                'total' => $totalPosts,
                'published' => $publishedPosts,
                'draft' => $draftPosts
            ],
            'lawyers' => [
                'total' => $totalLawyers,
                'active' => $activeLawyers,
                'cases_won' => $totalCasesWon
            ],
            'appointments' => [
                'total' => $totalAppointments,
                'pending' => $pendingAppointments,
                'confirmed' => $confirmedAppointments,
                'completed' => $completedAppointments,
                'today' => $todayAppointmentsCount,
                'month' => $monthStats
            ],
            'users' => [
                'total' => $totalUsers,
                'active' => $activeUsers
            ]
        ];
    }
}