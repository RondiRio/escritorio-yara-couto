<?php
/**
 * View - About
 * Página Sobre o Escritório
 */
?>

<style>
    .page-header {
        background: linear-gradient(135deg, var(--color-primary) 0%, #0a3a5f 100%);
        color: var(--color-white);
        padding: 80px 0 60px;
        text-align: center;
    }

    .page-header h1 {
        color: var(--color-white);
        font-size: 48px;
        margin-bottom: 20px;
    }

    .page-header p {
        font-size: 20px;
        opacity: 0.9;
    }

    .about-intro {
        padding: 80px 0;
        background: var(--color-white);
    }

    .about-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
    }

    .about-text h2 {
        font-size: 36px;
        margin-bottom: 20px;
    }

    .about-text p {
        font-size: 18px;
        line-height: 1.8;
        color: var(--color-text-light);
        margin-bottom: 20px;
        text-align: justify;
    }

    .about-image {
        height: 400px;
        background: var(--color-secondary);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 120px;
        color: var(--color-white);
    }

    .values {
        padding: 80px 0;
        background: var(--color-background);
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 40px;
        margin-top: 60px;
    }

    .value-card {
        background: var(--color-white);
        padding: 40px 30px;
        border-radius: 10px;
        text-align: center;
        transition: var(--transition);
    }

    .value-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .value-icon {
        width: 80px;
        height: 80px;
        background: var(--color-secondary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        color: var(--color-white);
        margin: 0 auto 20px;
    }

    .value-card h3 {
        font-size: 24px;
        margin-bottom: 15px;
    }

    .value-card p {
        color: var(--color-text-light);
        line-height: 1.8;
    }

    .timeline {
        padding: 80px 0;
        background: var(--color-white);
    }

    .timeline-items {
        max-width: 800px;
        margin: 60px auto 0;
    }

    .timeline-item {
        display: flex;
        gap: 30px;
        margin-bottom: 40px;
        position: relative;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 30px;
        top: 60px;
        bottom: -40px;
        width: 2px;
        background: var(--color-secondary);
    }

    .timeline-item:last-child::before {
        display: none;
    }

    .timeline-year {
        width: 60px;
        height: 60px;
        background: var(--color-secondary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-white);
        font-weight: 700;
        font-size: 18px;
        flex-shrink: 0;
    }

    .timeline-content {
        flex: 1;
        padding-top: 10px;
    }

    .timeline-content h3 {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .timeline-content p {
        color: var(--color-text-light);
        line-height: 1.8;
    }

    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 32px;
        }

        .about-content {
            grid-template-columns: 1fr;
        }

        .about-image {
            height: 300px;
        }

        .timeline-item {
            flex-direction: column;
            gap: 15px;
        }

        .timeline-item::before {
            display: none;
        }
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Sobre Nós</h1>
        <p>Conheça nossa história e nossos valores</p>
    </div>
</div>

<!-- Intro Section -->
<section class="about-intro">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <h2>Quem <span style="color: var(--color-secondary)">Somos</span></h2>
                <p>Somos um escritório de advocacia comprometido com a excelência na prestação de serviços jurídicos. Nossa equipe é formada por profissionais experientes e dedicados, que atuam com ética, transparência e comprometimento.</p>
                
                <p>Acreditamos que cada cliente merece atenção personalizada e soluções jurídicas eficazes. Por isso, investimos constantemente em capacitação e nos mantemos atualizados sobre as mudanças na legislação.</p>
                
                <p>Nosso objetivo é defender os direitos de nossos clientes com competência técnica e humanização no atendimento, construindo relações de confiança duradouras.</p>
            </div>

            <div class="about-image">
                ⚖️
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="values">
    <div class="container">
        <div class="section-header">
            <h2>Nossos <span style="color: var(--color-secondary)">Valores</span></h2>
            <p>Princípios que guiam nossa atuação profissional</p>
        </div>

        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">🎯</div>
                <h3>Excelência</h3>
                <p>Buscamos constantemente a melhor solução jurídica para cada caso, com dedicação e competência técnica.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">🤝</div>
                <h3>Ética</h3>
                <p>Atuamos sempre dentro dos princípios éticos e das normas da OAB, com transparência e honestidade.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">💙</div>
                <h3>Humanização</h3>
                <p>Tratamos cada cliente com respeito, empatia e atenção personalizada, compreendendo suas necessidades.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">📚</div>
                <h3>Conhecimento</h3>
                <p>Investimos em atualização constante para oferecer as melhores estratégias jurídicas aos nossos clientes.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">⏱️</div>
                <h3>Compromisso</h3>
                <p>Comprometemo-nos com prazos, resultados e com a defesa intransigente dos direitos de nossos clientes.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">💬</div>
                <h3>Transparência</h3>
                <p>Mantemos comunicação clara e constante, informando sobre todas as etapas do processo jurídico.</p>
            </div>
        </div>
    </div>
</section>

<!-- Timeline Section -->
<section class="timeline">
    <div class="container">
        <div class="section-header">
            <h2>Nossa <span style="color: var(--color-secondary)">História</span></h2>
            <p>Uma trajetória de compromisso e resultados</p>
        </div>

        <div class="timeline-items">
            <div class="timeline-item">
                <div class="timeline-year">2010</div>
                <div class="timeline-content">
                    <h3>Fundação</h3>
                    <p>Início das atividades com foco em Direito Previdenciário, atendendo clientes com dedicação e profissionalismo.</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-year">2015</div>
                <div class="timeline-content">
                    <h3>Expansão da Equipe</h3>
                    <p>Crescimento do escritório com a chegada de novos advogados especializados em diversas áreas do direito.</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-year">2018</div>
                <div class="timeline-content">
                    <h3>Reconhecimento</h3>
                    <p>Consolidação como referência em Direito Previdenciário na região, com centenas de casos ganhos.</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-year">2020</div>
                <div class="timeline-content">
                    <h3>Transformação Digital</h3>
                    <p>Implementação de tecnologias para melhor atendimento aos clientes, incluindo consultas online e acompanhamento de processos.</p>
                </div>
            </div>

            <div class="timeline-item">
                <div class="timeline-year">2025</div>
                <div class="timeline-content">
                    <h3>Presente</h3>
                    <p>Continuamos crescendo e aprimorando nossos serviços, sempre focados na satisfação e nos resultados para nossos clientes.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats -->
<section class="stats" style="margin-top: 0;">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number"><?= $stats['total_lawyers'] ?? 0 ?>+</div>
                <div class="stat-label">Advogados Especializados</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $stats['total_cases_won'] ?? 0 ?>+</div>
                <div class="stat-label">Casos Ganhos</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">15+</div>
                <div class="stat-label">Anos de Experiência</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">98%</div>
                <div class="stat-label">Clientes Satisfeitos</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <div class="container">
        <h2>Quer fazer parte da nossa história?</h2>
        <p>Entre em contato e agende uma consulta</p>
        <a href="<?= base_url('contato') ?>" class="btn btn-primary">Fale Conosco</a>
    </div>
</section>