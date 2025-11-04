<?php

namespace Controllers;

use Core\Controller;

/**
 * AreasController - Gerencia áreas de atuação
 */
class AreasController extends Controller
{
    /**
     * Lista áreas de atuação
     */
    public function index()
    {
        // Define áreas de atuação
        // Referências: INSS e Planalto conforme requisitos
        $areas = [
            [
                'slug' => 'aposentadorias',
                'title' => 'Aposentadorias',
                'icon' => '👴',
                'description' => 'Aposentadoria por Idade, Tempo de Contribuição, Especial e Pessoa com Deficiência',
                'items' => [
                    'Aposentadoria por Idade',
                    'Aposentadoria por Tempo de Contribuição',
                    'Aposentadoria Especial',
                    'Aposentadoria da Pessoa com Deficiência',
                    'Revisão de Aposentadorias'
                ],
                'reference' => 'https://www.gov.br/inss/pt-br/assuntos/aposentadorias'
            ],
            [
                'slug' => 'beneficios-incapacidade',
                'title' => 'Benefícios por Incapacidade',
                'icon' => '🏥',
                'description' => 'Auxílio-Doença, Aposentadoria por Invalidez e Auxílio-Acidente',
                'items' => [
                    'Auxílio por Incapacidade Temporária (Auxílio-Doença)',
                    'Aposentadoria por Incapacidade Permanente',
                    'Auxílio-Acidente',
                    'Recurso de Perícia Médica'
                ],
                'reference' => 'https://www.gov.br/inss/pt-br/assuntos/beneficios-por-incapacidade'
            ],
            [
                'slug' => 'pensoes-bpc-loas',
                'title' => 'Pensões e BPC/LOAS',
                'icon' => '👨‍👩‍👧',
                'description' => 'Pensão por Morte e Benefício Assistencial',
                'items' => [
                    'Pensão por Morte',
                    'BPC/LOAS - Idoso',
                    'BPC/LOAS - Pessoa com Deficiência',
                    'Auxílio-Reclusão'
                ],
                'reference' => 'https://www.gov.br/inss/pt-br/assuntos/pensao-por-morte'
            ],
            [
                'slug' => 'planejamento-previdenciario',
                'title' => 'Planejamento Previdenciário',
                'icon' => '📊',
                'description' => 'Análise de tempo de contribuição e melhor momento para aposentar',
                'items' => [
                    'Análise de Tempo de Contribuição',
                    'Melhor Momento para Aposentar',
                    'Estratégias de Contribuição',
                    'Cálculo de Benefícios'
                ],
                'reference' => 'https://www.planalto.gov.br/ccivil_03/leis/l8213cons.htm'
            ],
            [
                'slug' => 'revisoes-recursos',
                'title' => 'Revisões e Recursos',
                'icon' => '⚖️',
                'description' => 'Revisão de benefícios e recursos administrativos',
                'items' => [
                    'Revisão da Vida Toda',
                    'Revisão de Teto',
                    'Recursos Administrativos',
                    'Ações Judiciais Previdenciárias'
                ],
                'reference' => 'https://www.planalto.gov.br/ccivil_03/leis/l8213cons.htm'
            ],
            [
                'slug' => 'direito-trabalhador',
                'title' => 'Direito do Trabalhador',
                'icon' => '👷',
                'description' => 'Reconhecimento de tempo e conversão especial',
                'items' => [
                    'Reconhecimento de Tempo de Serviço',
                    'Averbação de Tempo Rural',
                    'Conversão de Tempo Especial',
                    'Contagem Recíproca'
                ],
                'reference' => 'https://www.planalto.gov.br/ccivil_03/leis/l8213cons.htm'
            ],
            [
                'slug' => 'direito-consumidor',
                'title' => 'Direito do Consumidor',
                'icon' => '🛒',
                'description' => 'Defesa dos direitos do consumidor',
                'items' => [
                    'Defesa em ações contra fornecedores',
                    'Danos morais e materiais',
                    'Contratos abusivos',
                    'Relações de consumo'
                ],
                'reference' => null
            ],
            [
                'slug' => 'direito-familia',
                'title' => 'Direito de Família',
                'icon' => '❤️',
                'description' => 'Divórcio, pensão alimentícia e guarda',
                'items' => [
                    'Divórcio Consensual e Litigioso',
                    'Pensão Alimentícia',
                    'Guarda de Filhos',
                    'Inventário e Partilha'
                ],
                'reference' => null
            ],
            [
                'slug' => 'direito-imobiliario',
                'title' => 'Direito Imobiliário',
                'icon' => '🏘️',
                'description' => 'Contratos, usucapião e despejo',
                'items' => [
                    'Contratos de Compra e Venda',
                    'Usucapião',
                    'Ações de Despejo',
                    'Regularização Imobiliária'
                ],
                'reference' => null
            ],
            [
                'slug' => 'direito-criminal',
                'title' => 'Direito Criminal',
                'icon' => '⚖️',
                'description' => 'Defesa criminal em todas as instâncias',
                'items' => [
                    'Defesa em Inquéritos Policiais',
                    'Defesa em Processos Criminais',
                    'Recursos em Tribunais',
                    'Habeas Corpus'
                ],
                'reference' => null
            ]
        ];

        // Define dados da página
        $pageTitle = 'Áreas de Atuação - Especialidades Jurídicas';
        $pageDescription = 'Conheça todas as áreas de atuação do nosso escritório';

        // Carrega view
        $this->view('pages/areas', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'areas' => $areas
        ]);
    }

    /**
     * Exibe área específica
     */
    public function show($slug)
    {
        // Busca área específica
        // (Poderia vir de banco de dados, mas aqui está hardcoded)
        $areas = $this->getAreasData();
        
        $area = null;
        foreach ($areas as $item) {
            if ($item['slug'] === $slug) {
                $area = $item;
                break;
            }
        }

        if (!$area) {
            $this->redirect('areas-de-atuacao');
        }

        // Define dados da página
        $pageTitle = $area['title'] . ' - Área de Atuação';
        $pageDescription = $area['description'];

        // Carrega view
        $this->view('pages/area-single', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'area' => $area
        ]);
    }

    /**
     * Retorna dados das áreas (poderia vir do banco)
     */
    private function getAreasData()
    {
        return [
            // ... mesmo array de áreas do método index()
        ];
    }
}