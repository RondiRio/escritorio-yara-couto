<?php
/**
 * View - Team
 * Página da Equipe de Advogados
 */
?>

<style>
    .team-page {
        padding: 80px 0;
        background: var(--color-white);
    }

    .team-grid-full {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 40px;
        margin-top: 60px;
    }

    .lawyer-card {
        background: var(--color-background);
        border-radius: 10px;
        overflow: hidden;
        transition: var(--transition);
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .lawyer-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    }

    .lawyer-header {
        height: 250px;
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .lawyer-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="white" opacity="0.05"/></svg>');
        opacity: 0.3;
    }

    .lawyer-photo-wrapper {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        border: 5px solid var(--color-white);
        overflow: hidden;
        background: var(--color-white);
        position: relative;
        z-index: 1;
    }

    .lawyer-photo {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 80px;
        color: var(--color-secondary);
    }

    .lawyer-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .lawyer-body {
        padding: 30px;
        text-align: center;
    }

    .lawyer-name {
        font-size: 24px;
        color: var(--color-primary);
        margin-bottom: 10px;
        font-family: var(--font-heading);
    }

    .lawyer-oab {
        color: var(--color-secondary);
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 16px;
    }

    .lawyer-specialties {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
        margin-bottom: 20px;
    }

    .specialty-tag {
        background: var(--color-secondary);
        color: var(--color-white);
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .lawyer-bio {
        color: var(--color-text-light);
        line-height: 1.8;
        margin-bottom: 20px;
        text-align: justify;
        font-size: 15px;
    }

    .lawyer-stats {
        display: flex;
        justify-content: space-around;
        padding: 20px 0;
        border-top: 1px solid #e0e0e0;
    }

    .stat-mini {
        text-align: center;
    }

    .stat-mini-number {
        font-size: 28px;
        font-weight: 700;
        color: var(--color-secondary);
        font-family: var(--font-heading);
    }

    .stat-mini-label {
        font-size: 12px;
        color: var(--color-text-light);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .lawyer-contact {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 20px;
    }

    .contact-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--color-primary);
        color: var(--color-white);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
    }

    .contact-btn:hover {
        background: var(--color-secondary);
        transform: scale(1.1);
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: var(--color-text-light);
    }

    .empty-state-icon {
        font-size: 100px;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    @media (max-width: 768px) {
        .team-grid-full {
            grid-template-columns: 1fr;
        }

        .lawyer-header {
            height: 200px;
        }

        .lawyer-photo-wrapper {
            width: 150px;
            height: 150px;
        }
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Nossa Equipe</h1>
        <p>Conheça os profissionais que defendem seus direitos</p>
    </div>
</div>

<!-- Team Section -->
<section class="team-page">
    <div class="container">
        <div class="section-header">
            <h2>Advogados <span style="color: var(--color-secondary)">Especializados</span></h2>
            <p>Profissionais experientes e comprometidos com resultados</p>
        </div>

        <?php if (!empty($lawyers) && count($lawyers) > 0): ?>
        <div class="team-grid-full">
            <?php foreach ($lawyers as $lawyer): ?>
            <div class="lawyer-card">
                <!-- Header com foto -->
                <div class="lawyer-header">
                    <div class="lawyer-photo-wrapper">
                        <div class="lawyer-photo">
                            <?php if (!empty($lawyer['photo'])): ?>
                                <img src="<?= asset('images/advogados/' . $lawyer['photo']) ?>" 
                                     alt="<?= $lawyer['name'] ?>">
                            <?php else: ?>
                                👤
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Corpo do card -->
                <div class="lawyer-body">
                    <h3 class="lawyer-name"><?= $lawyer['name'] ?></h3>
                    <div class="lawyer-oab">
                        OAB <?= $lawyer['oab_number'] ?>/<?= $lawyer['oab_state'] ?>
                    </div>

                    <!-- Especialidades -->
                    <?php if (!empty($lawyer['specialties'])): ?>
                    <div class="lawyer-specialties">
                        <?php 
                        $specialties = explode(',', $lawyer['specialties']);
                        foreach (array_slice($specialties, 0, 3) as $specialty): 
                        ?>
                            <span class="specialty-tag"><?= trim($specialty) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Bio -->
                    <p class="lawyer-bio">
                        <?= truncate($lawyer['bio'], 150) ?>
                    </p>

                    <!-- Estatísticas -->
                    <div class="lawyer-stats">
                        <div class="stat-mini">
                            <div class="stat-mini-number"><?= $lawyer['cases_won'] ?? 0 ?>+</div>
                            <div class="stat-mini-label">Casos Ganhos</div>
                        </div>
                        <div class="stat-mini">
                            <div class="stat-mini-number">
                                <?php
                                // Calcula anos de experiência baseado na data de criação
                                $yearsExp = isset($lawyer['created_at']) 
                                    ? date('Y') - date('Y', strtotime($lawyer['created_at'])) 
                                    : 5;
                                echo max($yearsExp, 1);
                                ?>+
                            </div>
                            <div class="stat-mini-label">Anos Exp.</div>
                        </div>
                    </div>

                    <!-- Contato -->
                    <div class="lawyer-contact">
                        <?php if (!empty($lawyer['email'])): ?>
                        <a href="mailto:<?= $lawyer['email'] ?>" 
                           class="contact-btn" 
                           title="Enviar e-mail">
                            ✉
                        </a>
                        <?php endif; ?>

                        <?php if (!empty($lawyer['whatsapp'])): ?>
                        <a href="https://wa.me/55<?= preg_replace('/[^0-9]/', '', $lawyer['whatsapp']) ?>" 
                           target="_blank"
                           class="contact-btn" 
                           title="WhatsApp">
                            📱
                        </a>
                        <?php endif; ?>

                        <?php if (!empty($lawyer['phone'])): ?>
                        <a href="tel:<?= preg_replace('/[^0-9]/', '', $lawyer['phone']) ?>" 
                           class="contact-btn" 
                           title="Ligar">
                            ☎
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <!-- Estado vazio -->
        <div class="empty-state">
            <div class="empty-state-icon">👥</div>
            <h3>Nenhum advogado cadastrado</h3>
            <p>Em breve apresentaremos nossa equipe de profissionais.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <div class="container">
        <h2>Pronto para conhecer nosso trabalho?</h2>
        <p>Agende uma consulta e descubra como podemos ajudá-lo</p>
        <a href="<?= base_url('agendar') ?>" class="btn btn-primary">Agendar Consulta</a>
    </div>
</section>