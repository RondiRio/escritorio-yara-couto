<?php
/**
 * View - Areas
 * Página de Áreas de Atuação
 */
?>

<style>
    .areas-section {
        padding: 80px 0;
        background: var(--color-white);
    }

    .areas-grid-full {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 40px;
        margin-top: 60px;
    }

    .area-card-full {
        background: var(--color-background);
        border-radius: 10px;
        overflow: hidden;
        transition: var(--transition);
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .area-card-full:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }

    .area-header {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
        color: var(--color-white);
        padding: 30px;
        text-align: center;
    }

    .area-icon {
        font-size: 60px;
        margin-bottom: 15px;
    }

    .area-card-full h3 {
        font-size: 24px;
        color: var(--color-white);
        margin-bottom: 10px;
    }

    .area-body {
        padding: 30px;
    }

    .area-description {
        color: var(--color-text-light);
        line-height: 1.8;
        margin-bottom: 20px;
    }

    .area-items {
        list-style: none;
        padding: 0;
        margin-bottom: 25px;
    }

    .area-items li {
        padding: 10px 0;
        padding-left: 30px;
        position: relative;
        color: var(--color-text);
        border-bottom: 1px solid #e0e0e0;
    }

    .area-items li:last-child {
        border-bottom: none;
    }

    .area-items li::before {
        content: "✓";
        position: absolute;
        left: 0;
        color: var(--color-secondary);
        font-weight: bold;
        font-size: 18px;
    }

    .area-reference {
        padding: 15px;
        background: rgba(204, 140, 93, 0.1);
        border-left: 3px solid var(--color-secondary);
        border-radius: 5px;
        font-size: 13px;
    }

    .area-reference strong {
        color: var(--color-primary);
        display: block;
        margin-bottom: 5px;
    }

    .area-reference a {
        color: var(--color-secondary);
        word-break: break-all;
    }

    @media (max-width: 768px) {
        .areas-grid-full {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Áreas de Atuação</h1>
        <p>Conheça nossas especialidades jurídicas</p>
    </div>
</div>

<!-- Areas Section -->
<section class="areas-section">
    <div class="container">
        <div class="section-header">
            <h2>Especialidades <span style="color: var(--color-secondary)">Jurídicas</span></h2>
            <p>Oferecemos serviços especializados nas seguintes áreas do direito</p>
        </div>

        <div class="areas-grid-full">
            <?php foreach ($areas as $area): ?>
            <div class="area-card-full">
                <div class="area-header">
                    <div class="area-icon"><?= $area['icon'] ?></div>
                    <h3><?= $area['title'] ?></h3>
                </div>

                <div class="area-body">
                    <p class="area-description"><?= $area['description'] ?></p>

                    <ul class="area-items">
                        <?php foreach ($area['items'] as $item): ?>
                        <li><?= $item ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if (!empty($area['reference'])): ?>
                    <div class="area-reference">
                        <strong>📚 Referência Legal:</strong>
                        <a href="<?= $area['reference'] ?>" target="_blank" rel="noopener">
                            <?= $area['reference'] ?>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <div class="container">
        <h2>Precisa de Orientação Jurídica?</h2>
        <p>Agende uma consulta e descubra como podemos ajudá-lo</p>
        <a href="<?= base_url('agendar') ?>" class="btn btn-primary">Agendar Consulta</a>
    </div>
</section>