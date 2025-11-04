<?php

namespace Controllers;

use Core\Controller;
use Models\Post;
use Models\Lawyer;
use Models\Setting;

/**
 * HomeController - Gerencia a página inicial
 */
class HomeController extends Controller
{
    private $postModel;
    private $lawyerModel;
    private $settingModel;

    public function __construct()
    {
        $this->postModel = new Post();
        $this->lawyerModel = new Lawyer();
        $this->settingModel = new Setting();
    }

    /**
     * Exibe página inicial
     */
    public function index()
    {
        // Busca posts recentes
        $recentPosts = $this->postModel->getPublished(3);

        // Busca advogados ativos
        $lawyers = $this->lawyerModel->getActive();

        // Busca configurações do site
        $siteInfo = $this->settingModel->getSiteInfo();

        // Busca estatísticas
        $totalLawyers = count($lawyers);
        $totalCasesWon = $this->lawyerModel->getTotalCasesWon();

        // Define título da página
        $pageTitle = $siteInfo['site_name'] ?? 'Escritório Yara Couto Vitoria';
        $pageDescription = $siteInfo['site_description'] ?? 'Advocacia Previdenciária com Excelência';

        // Carrega view
        $this->view('pages/home', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'recentPosts' => $recentPosts,
            'lawyers' => $lawyers,
            'siteInfo' => $siteInfo,
            'stats' => [
                'total_lawyers' => $totalLawyers,
                'total_cases_won' => $totalCasesWon
            ]
        ]);
    }
}