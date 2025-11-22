<?php

namespace Controllers;

use Core\Controller;
use Models\Setting;

/**
 * PageController - Gerencia páginas estáticas
 */
class PageController extends Controller
{
    private $settingModel;

    public function __construct()
    {
        $this->settingModel = new Setting();
    }

    /**
     * Política de Privacidade (LGPD)
     * Referência: https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709.htm
     */
    public function privacy()
    {
        $siteInfo = $this->settingModel->getSiteInfo();

        $pageTitle = 'Política de Privacidade - LGPD';
        $pageDescription = 'Nossa política de privacidade em conformidade com a Lei Geral de Proteção de Dados';

        $this->viewWithLayout('pages/privacy', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'siteInfo' => $siteInfo
        ]);
    }

    /**
     * Termos de Uso
     */
    public function terms()
    {
        $siteInfo = $this->settingModel->getSiteInfo();

        $pageTitle = 'Termos de Uso';
        $pageDescription = 'Termos e condições de uso do site';

        $this->viewWithLayout('pages/terms', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'siteInfo' => $siteInfo
        ]);
    }

    /**
     * Aviso Legal (Publicidade OAB)
     * Referência: Provimento 205/2021 - https://www.oab.org.br/leisnormas/legislacao/provimentos/205-2021
     * Referência: Lei 8.906/94 - https://www.planalto.gov.br/ccivil_03/leis/l8906.htm
     */
    public function legalNotice()
    {
        $siteInfo = $this->settingModel->getSiteInfo();

        $pageTitle = 'Aviso Legal - Publicidade Profissional';
        $pageDescription = 'Informações sobre publicidade profissional conforme normas da OAB';

       $this->viewWithLayout('pages/legal-notice', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'siteInfo' => $siteInfo
        ]);
    }

    /**
     * FAQ - Perguntas Frequentes
     */
    public function faq()
    {
        // FAQs sobre direito previdenciário
        $faqs = [
            [
                'category' => 'Aposentadoria',
                'questions' => [
                    [
                        'question' => 'Quando posso me aposentar?',
                        'answer' => 'Depende das regras aplicáveis ao seu caso: idade mínima, tempo de contribuição, tipo de atividade exercida. Cada situação é única e requer análise específica do histórico contributivo.'
                    ],
                    [
                        'question' => 'O que é aposentadoria especial?',
                        'answer' => 'É o benefício concedido a trabalhadores expostos a agentes nocivos à saúde ou integridade física, como ruído excessivo, produtos químicos, calor intenso, entre outros.'
                    ],
                    [
                        'question' => 'Posso revisar minha aposentadoria?',
                        'answer' => 'Sim, existem diversas modalidades de revisão, como a Revisão da Vida Toda, revisão de teto, revisão por erro de cálculo, entre outras. O prazo para solicitar é de até 10 anos da concessão.'
                    ]
                ]
            ],
            [
                'category' => 'BPC/LOAS',
                'questions' => [
                    [
                        'question' => 'Quem tem direito ao BPC/LOAS?',
                        'answer' => 'Idosos com 65 anos ou mais e pessoas com deficiência de qualquer idade, desde que comprovem não possuir meios de prover a própria manutenção nem tê-la provida por sua família.'
                    ],
                    [
                        'question' => 'Precisa ter contribuído para o INSS?',
                        'answer' => 'Não. O BPC/LOAS é um benefício assistencial que não exige contribuições prévias ao INSS.'
                    ]
                ]
            ],
            [
                'category' => 'Auxílio-Doença',
                'questions' => [
                    [
                        'question' => 'Meu auxílio-doença foi negado, o que fazer?',
                        'answer' => 'É possível recorrer da decisão tanto na via administrativa quanto judicial. Muitas vezes a perícia médica pode ter equívocos que podem ser contestados com documentação médica adequada.'
                    ]
                ]
            ]
        ];

        $pageTitle = 'Perguntas Frequentes - FAQ';
        $pageDescription = 'Tire suas dúvidas sobre direitos previdenciários';

        $this->viewWithLayout('pages/faq', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'faqs' => $faqs
        ]);
    }
}