<?php
/**
 * View - Home
 * Página inicial do site
 */
?>

<style>
    /* Hero Section */
    .hero {
        background: linear-gradient(135deg, var(--color-primary) 0%, #0a3a5f 100%);
        color: var(--color-white);
        padding: 100px 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><rect fill="%23CC8C5D" opacity="0.05" width="1200" height="600"/></svg>');
        opacity: 0.3;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        max-width: 800px;
        margin: 0 auto;
    }

    .hero h1 {
        font-size: 48px;
        color: var(--color-white);
        margin-bottom: 20px;
        line-height: 1.2;
    }

    .hero .highlight {
        color: var(--color-secondary);
    }

    .hero p {
        font-size: 20px;
        margin-bottom: 40px;
        opacity: 0.9;
    }

    .hero-buttons {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        padding: 15px 40px;
        border-radius: 5px;
        font-weight: 600;
        font-size: 16px;
        transition: var(--transition);
        border: 2px solid transparent;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--color-secondary);
        color: var(--color-white);
    }

    .btn-primary:hover {
        background: transparent;
        border-color: var(--color-secondary);
    }

    .btn-outline {
        background: transparent;
        border-color: var(--color-white);
        color: var(--color-white);
    }

    .btn-outline:hover {
        background: var(--color-white);
        color: var(--color-primary);
    }

    /* Stats Section */
    .stats {
        background: var(--color-white);
        padding: 60px 0;
        margin-top: -50px;
        position: relative;
        z-index: 2;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 40px;
        text-align: center;
    }

    .stat-item {
        padding: 30px;
        border-radius: 10px;
        background: var(--color-background);
        transition: var(--transition);
    }

    .stat-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .stat-number {
        font-size: 48px;
        font-weight: 700;
        color: var(--color-secondary);
        font-family: var(--font-heading);
    }

    .stat-label {
        font-size: 16px;
        color: var(--color-text-light);
        margin-top: 10px;
    }

    /* Services Section */
    .services {
        padding: 80px 0;
    }

    .section-header {
        text-align: center;
        max-width: 700px;
        margin: 0 auto 60px;
    }

    .section-header h2 {
        font-size: 40px;
        margin-bottom: 20px;
    }

    .section-header p {
        font-size: 18px;
        color: var(--color-text-light);
    }

    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }

    .service-card {
        background: var(--color-white);
        padding: 40px 30px;
        border-radius: 10px;
        text-align: center;
        transition: var(--transition);
        border: 2px solid transparent;
    }

    .service-card:hover {
        border-color: var(--color-secondary);
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .service-icon {
        font-size: 48px;
        margin-bottom: 20px;
    }

    .service-card h3 {
        font-size: 24px;
        margin-bottom: 15px;
    }

    .service-card p {
        color: var(--color-text-light);
        line-height: 1.8;
    }

    /* Team Section */
    .team {
        padding: 80px 0;
        background: var(--color-white);
    }

    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
    }

    .team-member {
        text-align: center;
        background: var(--color-background);
        border-radius: 10px;
        padding: 30px;
        transition: var(--transition);
    }

    .team-member:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .team-photo {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: var(--color-secondary);
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 60px;
        color: var(--color-white);
    }

    .team-member h3 {
        font-size: 22px;
        margin-bottom: 10px;
    }

    .team-member .role {
        color: var(--color-secondary);
        font-weight: 600;
        margin-bottom: 15px;
    }

    /* Blog Section */
    .blog {
        padding: 80px 0;
    }

    .blog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }

    .blog-card {
        background: var(--color-white);
        border-radius: 10px;
        overflow: hidden;
        transition: var(--transition);
    }

    .blog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .blog-image {
        height: 200px;
        background: var(--color-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 60px;
        color: var(--color-white);
    }

    .blog-content {
        padding: 25px;
    }

    .blog-card h3 {
        font-size: 20px;
        margin-bottom: 10px;
    }

    .blog-card p {
        color: var(--color-text-light);
        margin-bottom: 15px;
    }

    .blog-meta {
        font-size: 14px;
        color: var(--color-text-light);
    }

    /* CTA Section */
    .cta {
        background: linear-gradient(135deg, var(--color-primary) 0%, #0a3a5f 100%);
        color: var(--color-white);
        padding: 80px 0;
        text-align: center;
    }

    .cta h2 {
        color: var(--color-white);
        font-size: 36px;
        margin-bottom: 20px;
    }

    .cta p {
        font-size: 18px;
        margin-bottom: 40px;
        opacity: 0.9;
    }

    @media (max-width: 768px) {
        .hero h1 {
            font-size: 32px;
        }

        .hero p {
            font-size: 16px;
        }

        .hero-buttons {
            flex-direction: column;
        }

        .section-header h2 {
            font-size: 28px;
        }

        .stat-number {
            font-size: 36px;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1><?= $siteInfo['site_name'] ?? 'Escritório de Advocacia' ?></h1>
            <p><?= $siteInfo['site_description'] ?? 'Defendendo seus direitos com excelência, ética e compromisso' ?></p>
            <div class="hero-buttons">
                <a href="<?= base_url('agendar') ?>" class="btn btn-primary">Agendar Consulta</a>
                <a href="<?= base_url('sobre') ?>" class="btn btn-outline">Conheça-nos</a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats">
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

<!-- Services Section -->
<section class="services">
    <div class="container">
        <div class="section-header">
            <h2>Áreas de <span class="highlight">Atuação</span></h2>
            <p>Oferecemos serviços jurídicos especializados em diversas áreas do direito</p>
        </div>

        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">⚖️</div>
                <h3>Direito Previdenciário</h3>
                <p>Aposentadorias, benefícios, revisões e recursos no INSS</p>
            </div>

            <div class="service-card">
                <div class="service-icon">🏥</div>
                <h3>Benefícios por Incapacidade</h3>
                <p>Auxílio-doença, aposentadoria por invalidez e auxílio-acidente</p>
            </div>

            <div class="service-card">
                <div class="service-icon">👨‍👩‍👧</div>
                <h3>Pensões e BPC/LOAS</h3>
                <p>Pensão por morte e benefício assistencial para idosos e deficientes</p>
            </div>

            <div class="service-card">
                <div class="service-icon">👷</div>
                <h3>Direito do Trabalho</h3>
                <p>Defesa dos direitos trabalhistas e ações contra empresas</p>
            </div>

            <div class="service-card">
                <div class="service-icon">❤️</div>
                <h3>Direito de Família</h3>
                <p>Divórcio, pensão alimentícia, guarda e inventário</p>
            </div>

            <div class="service-card">
                <div class="service-icon">🏘️</div>
                <h3>Direito Imobiliário</h3>
                <p>Contratos, usucapião e regularização de imóveis</p>
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="<?= base_url('areas-de-atuacao') ?>" class="btn btn-primary">Ver Todas as Áreas</a>
        </div>
    </div>
</section>

<!-- Team Section -->
<?php if (!empty($lawyers) && count($lawyers) > 0): ?>
<section class="team">
    <div class="container">
        <div class="section-header">
            <h2>Nossa <span class="highlight">Equipe</span></h2>
            <p>Profissionais experientes e comprometidos com seus direitos</p>
        </div>

        <div class="team-grid">
            <?php foreach (array_slice($lawyers, 0, 4) as $lawyer): ?>
            <div class="team-member">
                <div class="team-photo">
                    <?php if (!empty($lawyer['photo'])): ?>
                        <img src="<?= asset('images/advogados/' . $lawyer['photo']) ?>" 
                             alt="<?= $lawyer['name'] ?>"
                             style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        👤
                    <?php endif; ?>
                </div>
                <h3><?= $lawyer['name'] ?></h3>
                <div class="role">OAB <?= $lawyer['oab_number'] ?>/<?= $lawyer['oab_state'] ?></div>
                <p><?= truncate($lawyer['bio'] ?? '', 100) ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="<?= base_url('equipe') ?>" class="btn btn-primary">Conhecer a Equipe</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Blog Section -->
<?php if (!empty($recentPosts) && count($recentPosts) > 0): ?>
<section class="blog">
    <div class="container">
        <div class="section-header">
            <h2>Últimas <span class="highlight">Notícias</span></h2>
            <p>Fique por dentro das novidades do direito</p>
        </div>

        <div class="blog-grid">
            <?php foreach ($recentPosts as $post): ?>
            <a href="<?= base_url('blog/' . $post['slug']) ?>" class="blog-card">
                <div class="blog-image">
                    <?php if (!empty($post['featured_image'])): ?>
                        <img src="<?= base_url('storage/uploads/posts/' . $post['featured_image']) ?>" 
                             alt="<?= $post['title'] ?>"
                             style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        📰
                    <?php endif; ?>
                </div>
                <div class="blog-content">
                    <h3><?= $post['title'] ?></h3>
                    <p><?= truncate($post['excerpt'] ?? strip_tags($post['content']), 120) ?></p>
                    <div class="blog-meta">
                        <?= format_date($post['published_at']) ?> • 
                        <?= $post['category_name'] ?? 'Sem categoria' ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="<?= base_url('blog') ?>" class="btn btn-primary">Ver Todos os Artigos</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <h2>Precisa de Ajuda Jurídica?</h2>
        <p>Agende uma consulta e descubra como podemos ajudá-lo</p>
        <a href="<?= base_url('agendar') ?>" class="btn btn-primary">Agendar Consulta Agora</a>
    </div>
</section>