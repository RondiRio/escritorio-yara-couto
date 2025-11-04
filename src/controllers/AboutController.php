<?php

namespace Controllers;

use Core\Controller;
use Models\Lawyer;
use Models\Setting;

/**
 * AboutController - Gerencia página Sobre
 */
class AboutController extends Controller
{
    private $lawyerModel;
    private $settingModel;

    public function __construct()
    {
        $this->lawyerModel = new Lawyer();
        $this->settingModel = new Setting();
    }

    /**
     * Exibe página sobre o escritório
     */
    public function index()
    {
        // Busca informações do site
        $siteInfo = $this->settingModel->getSiteInfo();

        // Busca advogados ativos
        $lawyers = $this->lawyerModel->getActive();

        // Busca estatísticas
        $stats = $this->lawyerModel->getStatistics();

        // Define dados da página
        $pageTitle = 'Sobre o Escritório - ' . ($siteInfo['site_name'] ?? 'Yara Couto Vitoria');
        $pageDescription = 'Conheça nossa história, missão e equipe de advogados especializados';

        // Carrega view
        $this->view('pages/about', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'siteInfo' => $siteInfo,
            'lawyers' => $lawyers,
            'stats' => $stats
        ]);
    }
}