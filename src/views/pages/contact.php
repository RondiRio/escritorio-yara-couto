<?php
/**
 * View - Contact
 * Página de Contato
 */
?>

<style>
    .contact-section {
        padding: 80px 0;
        background: var(--color-white);
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        margin-top: 60px;
    }

    .contact-info {
        background: var(--color-background);
        padding: 40px;
        border-radius: 10px;
    }

    .contact-info h3 {
        font-size: 28px;
        margin-bottom: 30px;
    }

    .info-item {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
        align-items: flex-start;
    }

    .info-icon {
        width: 50px;
        height: 50px;
        background: var(--color-secondary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: var(--color-white);
        flex-shrink: 0;
    }

    .info-content h4 {
        font-size: 18px;
        margin-bottom: 5px;
        color: var(--color-primary);
    }

    .info-content p {
        color: var(--color-text-light);
        line-height: 1.8;
    }

    .info-content a {
        color: var(--color-secondary);
    }

    .contact-form {
        background: var(--color-background);
        padding: 40px;
        border-radius: 10px;
    }

    .contact-form h3 {
        font-size: 28px;
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--color-primary);
    }

    .form-group label .required {
        color: #e74c3c;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 5px;
        font-size: 16px;
        font-family: var(--font-body);
        transition: var(--transition);
        background: var(--color-white);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--color-secondary);
    }

    textarea.form-control {
        min-height: 150px;
        resize: vertical;
    }

    .btn-submit {
        width: 100%;
        padding: 15px;
        background: var(--color-secondary);
        color: var(--color-white);
        border: none;
        border-radius: 5px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-submit:hover {
        background: var(--color-primary);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .map-section {
        padding: 80px 0;
        background: var(--color-background);
    }

    .map-container {
        width: 100%;
        height: 450px;
        background: var(--color-white);
        border-radius: 10px;
        overflow: hidden;
        margin-top: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #e0e0e0;
    }

    .map-placeholder {
        text-align: center;
        color: var(--color-text-light);
    }

    .map-placeholder-icon {
        font-size: 80px;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }

        .contact-info,
        .contact-form {
            padding: 30px 20px;
        }

        .map-container {
            height: 300px;
        }
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Entre em Contato</h1>
        <p>Estamos prontos para atender você</p>
    </div>
</div>

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <div class="section-header">
            <h2>Fale <span style="color: var(--color-secondary)">Conosco</span></h2>
            <p>Envie sua mensagem ou entre em contato através de nossos canais</p>
        </div>

        <div class="contact-grid">
            <!-- Informações de Contato -->
            <div class="contact-info">
                <h3>Informações de Contato</h3>

                <?php if (!empty($siteInfo['site_address'])): ?>
                <div class="info-item">
                    <div class="info-icon">📍</div>
                    <div class="info-content">
                        <h4>Endereço</h4>
                        <p><?= nl2br($siteInfo['site_address']) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($siteInfo['site_phone'])): ?>
                <div class="info-item">
                    <div class="info-icon">☎</div>
                    <div class="info-content">
                        <h4>Telefone</h4>
                        <p>
                            <a href="tel:<?= preg_replace('/[^0-9]/', '', $siteInfo['site_phone']) ?>">
                                <?= format_phone($siteInfo['site_phone']) ?>
                            </a>
                        </p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($siteInfo['site_whatsapp'])): ?>
                <div class="info-item">
                    <div class="info-icon">📱</div>
                    <div class="info-content">
                        <h4>WhatsApp</h4>
                        <p>
                            <a href="https://wa.me/55<?= preg_replace('/[^0-9]/', '', $siteInfo['site_whatsapp']) ?>" target="_blank">
                                <?= format_phone($siteInfo['site_whatsapp']) ?>
                            </a>
                        </p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($siteInfo['site_email'])): ?>
                <div class="info-item">
                    <div class="info-icon">✉</div>
                    <div class="info-content">
                        <h4>E-mail</h4>
                        <p>
                            <a href="mailto:<?= $siteInfo['site_email'] ?>">
                                <?= $siteInfo['site_email'] ?>
                            </a>
                        </p>
                    </div>
                </div>
                <?php endif; ?>

                <div class="info-item">
                    <div class="info-icon">🕐</div>
                    <div class="info-content">
                        <h4>Horário de Atendimento</h4>
                        <p>
                            Segunda a Sexta: 9h às 18h<br>
                            Sábado: 9h às 13h
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulário de Contato -->
            <div class="contact-form">
                <h3>Envie sua Mensagem</h3>

                <form action="<?= base_url('contato/enviar') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="name">Nome Completo <span class="required">*</span></label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-control" 
                               value="<?= old('name') ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail <span class="required">*</span></label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control"
                               value="<?= old('email') ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Telefone</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               class="form-control"
                               value="<?= old('phone') ?>"
                               placeholder="(00) 00000-0000">
                    </div>

                    <div class="form-group">
                        <label for="subject">Assunto <span class="required">*</span></label>
                        <input type="text" 
                               id="subject" 
                               name="subject" 
                               class="form-control"
                               value="<?= old('subject') ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="message">Mensagem <span class="required">*</span></label>
                        <textarea id="message" 
                                  name="message" 
                                  class="form-control" 
                                  required><?= old('message') ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        Enviar Mensagem
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<?php if (!empty($siteInfo['site_address'])): ?>
<section class="map-section">
    <div class="container">
        <div class="section-header">
            <h2>Nossa <span style="color: var(--color-secondary)">Localização</span></h2>
            <p>Visite nosso escritório</p>
        </div>

        <div class="map-container">
            <!-- Google Maps Embed pode ser inserido aqui -->
            <div class="map-placeholder">
                <div class="map-placeholder-icon">🗺️</div>
                <p>Mapa da localização</p>
                <p style="font-size: 14px;">
                    <?= nl2br($siteInfo['site_address']) ?>
                </p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>