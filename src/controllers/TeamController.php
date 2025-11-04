<?php

namespace Controllers;

use Core\Controller;
use Models\Lawyer;

/**
 * TeamController - Gerencia página da equipe
 */
class TeamController extends Controller
{
    private $lawyerModel;

    public function __construct()
    {
        $this->lawyerModel = new Lawyer();
    }

    /**
     * Exibe lista de advogados
     */
    public function index()
    {
        // Busca todos os advogados ativos
        $lawyers = $this->lawyerModel->getActive('display_order ASC');

        // Agrupa por especialidade (opcional)
        $lawyersBySpecialty = [];
        foreach ($lawyers as $lawyer) {
            $specialties = explode(',', $lawyer['specialties'] ?? '');
            foreach ($specialties as $specialty) {
                $specialty = trim($specialty);
                if (!empty($specialty)) {
                    $lawyersBySpecialty[$specialty][] = $lawyer;
                }
            }
        }

        // Define dados da página
        $pageTitle = 'Nossa Equipe - Advogados Especializados';
        $pageDescription = 'Conheça nossos advogados especializados em Direito Previdenciário e outras áreas';

        // Carrega view
        $this->view('pages/team', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'lawyers' => $lawyers,
            'lawyersBySpecialty' => $lawyersBySpecialty
        ]);
    }

    /**
     * Exibe perfil de um advogado específico
     */
    public function show($id)
    {
        // Busca advogado
        $lawyer = $this->lawyerModel->find($id);

        // Verifica se existe e está ativo
        if (!$lawyer || $lawyer['status'] !== 'active') {
            $this->redirect('equipe');
        }

        // Define dados da página
        $pageTitle = $lawyer['name'] . ' - Advogado(a)';
        $pageDescription = truncate($lawyer['bio'] ?? '', 160);

        // Carrega view
        $this->view('pages/lawyer-profile', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'lawyer' => $lawyer
        ]);
    }
}