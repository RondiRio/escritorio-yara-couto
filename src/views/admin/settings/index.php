<?php
/**
 * View - Configurações do Sistema
 * Página de configurações com tabs para diferentes grupos
 */

$pageTitle = $pageTitle ?? 'Configurações do Sistema';
$settings = $settings ?? [];
$activeTab = $activeTab ?? 'general';
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

// Função auxiliar para pegar valor de configuração
function getSetting($settings, $group, $key, $default = '') {
    return $settings[$group][$key]['value'] ?? $default;
}

require_once __DIR__ . '/../layout/header.php';
?>

<style>
    .settings-container {
        max-width: 1000px;
    }

    .settings-header {
        background: white;
        padding: 25px 30px;
        border-radius: 10px 10px 0 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-bottom: 1px solid var(--color-border);
    }

    .settings-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--color-primary);
        margin: 0;
    }

    .tabs-container {
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .tabs-nav {
        display: flex;
        border-bottom: 2px solid var(--color-border);
        overflow-x: auto;
    }

    .tab-button {
        padding: 15px 25px;
        border: none;
        background: none;
        cursor: pointer;
        font-size: 15px;
        font-weight: 500;
        color: var(--color-text-light);
        transition: var(--transition);
        border-bottom: 3px solid transparent;
        white-space: nowrap;
    }

    .tab-button:hover {
        color: var(--color-primary);
        background: #f8f9fa;
    }

    .tab-button.active {
        color: var(--color-primary);
        border-bottom-color: var(--color-primary);
        background: white;
    }

    .tab-content {
        padding: 30px;
        background: white;
        border-radius: 0 0 10px 10px;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--color-text);
    }

    .form-group label span {
        color: #dc3545;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid var(--color-border);
        border-radius: 6px;
        font-size: 15px;
        transition: var(--transition);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(6, 37, 61, 0.1);
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
    }

    .form-help {
        font-size: 13px;
        color: var(--color-text-light);
        margin-top: 5px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 6px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        border: none;
        cursor: pointer;
        font-size: 15px;
    }

    .btn-primary {
        background: var(--color-primary);
        color: white;
    }

    .btn-primary:hover {
        background: #04192b;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #545b62;
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-success:hover {
        background: #218838;
    }

    .btn-warning {
        background: #ffc107;
        color: #000;
    }

    .btn-warning:hover {
        background: #e0a800;
    }

    .section-divider {
        border-top: 2px solid var(--color-border);
        margin: 30px 0;
        padding-top: 20px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--color-primary);
        margin-bottom: 15px;
    }

    .test-email-box {
        background: #e7f3ff;
        border: 1px solid var(--color-primary);
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .test-email-box h3 {
        margin: 0 0 15px 0;
        font-size: 16px;
        color: var(--color-primary);
    }

    .test-email-form {
        display: flex;
        gap: 10px;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .tabs-nav {
            flex-wrap: wrap;
        }

        .tab-button {
            flex: 1;
            min-width: 120px;
        }
    }
</style>

<div class="settings-container">
    <?php if (isset($_SESSION['flash'])): ?>
        <?php foreach ($_SESSION['flash'] as $type => $message): ?>
            <div class="alert alert-<?= $type ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 8px; background: <?= $type === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $type === 'success' ? '#155724' : '#721c24' ?>;">
                <?= htmlspecialchars($message) ?>
            </div>
            <?php unset($_SESSION['flash'][$type]); ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="settings-header">
        <h1 class="settings-title">⚙️ Configurações do Sistema</h1>
    </div>

    <div class="tabs-container">
        <div class="tabs-nav">
            <button class="tab-button <?= $activeTab === 'general' ? 'active' : '' ?>" data-tab="general">
                🏢 Geral
            </button>
            <button class="tab-button <?= $activeTab === 'seo' ? 'active' : '' ?>" data-tab="seo">
                🔍 SEO
            </button>
            <button class="tab-button <?= $activeTab === 'email' ? 'active' : '' ?>" data-tab="email">
                📧 E-mail
            </button>
            <button class="tab-button <?= $activeTab === 'social' ? 'active' : '' ?>" data-tab="social">
                📱 Redes Sociais
            </button>
            <button class="tab-button <?= $activeTab === 'whatsapp' ? 'active' : '' ?>" data-tab="whatsapp">
                💬 WhatsApp
            </button>
        </div>

        <div class="tab-content">
            <!-- TAB: GERAL -->
            <div class="tab-pane <?= $activeTab === 'general' ? 'active' : '' ?>" id="general">
                <form method="POST" action="<?= base_url('admin/configuracoes/geral') ?>">
                    <?= csrf_field() ?>

                    <h3 class="section-title">Informações do Escritório</h3>

                    <div class="form-group">
                        <label for="site_name">Nome do Escritório <span>*</span></label>
                        <input
                            type="text"
                            class="form-control <?= isset($errors['site_name']) ? 'is-invalid' : '' ?>"
                            id="site_name"
                            name="site_name"
                            value="<?= htmlspecialchars(getSetting($settings, 'general', 'site_name')) ?>"
                            required
                        >
                        <?php if (isset($errors['site_name'])): ?>
                            <div class="invalid-feedback"><?= $errors['site_name'][0] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="site_description">Descrição</label>
                        <textarea
                            class="form-control"
                            id="site_description"
                            name="site_description"
                            rows="3"
                        ><?= htmlspecialchars(getSetting($settings, 'general', 'site_description')) ?></textarea>
                        <div class="form-help">Breve descrição do escritório</div>
                    </div>

                    <div class="section-divider"></div>
                    <h3 class="section-title">Contato</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="site_email">E-mail <span>*</span></label>
                            <input
                                type="email"
                                class="form-control <?= isset($errors['site_email']) ? 'is-invalid' : '' ?>"
                                id="site_email"
                                name="site_email"
                                value="<?= htmlspecialchars(getSetting($settings, 'general', 'site_email')) ?>"
                                required
                            >
                            <?php if (isset($errors['site_email'])): ?>
                                <div class="invalid-feedback"><?= $errors['site_email'][0] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="site_phone">Telefone</label>
                            <input
                                type="text"
                                class="form-control"
                                id="site_phone"
                                name="site_phone"
                                value="<?= htmlspecialchars(getSetting($settings, 'general', 'site_phone')) ?>"
                                placeholder="(11) 1234-5678"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="site_whatsapp">WhatsApp</label>
                        <input
                            type="text"
                            class="form-control"
                            id="site_whatsapp"
                            name="site_whatsapp"
                            value="<?= htmlspecialchars(getSetting($settings, 'general', 'site_whatsapp')) ?>"
                            placeholder="5511987654321"
                        >
                        <div class="form-help">Formato: código do país + DDD + número (apenas números)</div>
                    </div>

                    <div class="form-group">
                        <label for="site_address">Endereço Completo</label>
                        <textarea
                            class="form-control"
                            id="site_address"
                            name="site_address"
                            rows="2"
                        ><?= htmlspecialchars(getSetting($settings, 'general', 'site_address')) ?></textarea>
                    </div>

                    <div class="section-divider"></div>
                    <h3 class="section-title">Registro OAB</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="oab_number">Número OAB</label>
                            <input
                                type="text"
                                class="form-control"
                                id="oab_number"
                                name="oab_number"
                                value="<?= htmlspecialchars(getSetting($settings, 'general', 'oab_number')) ?>"
                                placeholder="123456"
                            >
                        </div>

                        <div class="form-group">
                            <label for="oab_state">Estado OAB</label>
                            <select class="form-control" id="oab_state" name="oab_state">
                                <option value="">Selecione...</option>
                                <?php
                                $states = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
                                $currentState = getSetting($settings, 'general', 'oab_state');
                                foreach ($states as $state):
                                ?>
                                    <option value="<?= $state ?>" <?= $currentState === $state ? 'selected' : '' ?>><?= $state ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">💾 Salvar Configurações Gerais</button>
                </form>
            </div>

            <!-- TAB: SEO -->
            <div class="tab-pane <?= $activeTab === 'seo' ? 'active' : '' ?>" id="seo">
                <form method="POST" action="<?= base_url('admin/configuracoes/seo') ?>">
                    <?= csrf_field() ?>

                    <h3 class="section-title">Meta Tags</h3>

                    <div class="form-group">
                        <label for="meta_title">Título SEO <span>*</span></label>
                        <input
                            type="text"
                            class="form-control <?= isset($errors['meta_title']) ? 'is-invalid' : '' ?>"
                            id="meta_title"
                            name="meta_title"
                            value="<?= htmlspecialchars(getSetting($settings, 'seo', 'meta_title')) ?>"
                            maxlength="60"
                            required
                        >
                        <?php if (isset($errors['meta_title'])): ?>
                            <div class="invalid-feedback"><?= $errors['meta_title'][0] ?></div>
                        <?php endif; ?>
                        <div class="form-help">Máximo 60 caracteres (ideal: 50-60)</div>
                    </div>

                    <div class="form-group">
                        <label for="meta_description">Descrição SEO <span>*</span></label>
                        <textarea
                            class="form-control <?= isset($errors['meta_description']) ? 'is-invalid' : '' ?>"
                            id="meta_description"
                            name="meta_description"
                            rows="3"
                            maxlength="160"
                            required
                        ><?= htmlspecialchars(getSetting($settings, 'seo', 'meta_description')) ?></textarea>
                        <?php if (isset($errors['meta_description'])): ?>
                            <div class="invalid-feedback"><?= $errors['meta_description'][0] ?></div>
                        <?php endif; ?>
                        <div class="form-help">Máximo 160 caracteres (ideal: 120-160)</div>
                    </div>

                    <div class="form-group">
                        <label for="meta_keywords">Palavras-chave</label>
                        <input
                            type="text"
                            class="form-control"
                            id="meta_keywords"
                            name="meta_keywords"
                            value="<?= htmlspecialchars(getSetting($settings, 'seo', 'meta_keywords')) ?>"
                            placeholder="advocacia, direito, jurídico, advogado"
                        >
                        <div class="form-help">Separe por vírgulas</div>
                    </div>

                    <div class="section-divider"></div>
                    <h3 class="section-title">Analytics e Tracking</h3>

                    <div class="form-group">
                        <label for="google_analytics_id">Google Analytics ID</label>
                        <input
                            type="text"
                            class="form-control"
                            id="google_analytics_id"
                            name="google_analytics_id"
                            value="<?= htmlspecialchars(getSetting($settings, 'seo', 'google_analytics_id')) ?>"
                            placeholder="G-XXXXXXXXXX ou UA-XXXXXXXXX-X"
                        >
                        <div class="form-help">Formato: G-XXXXXXXXXX (GA4) ou UA-XXXXXXXXX-X (Universal)</div>
                    </div>

                    <div class="form-group">
                        <label for="google_tag_manager_id">Google Tag Manager ID</label>
                        <input
                            type="text"
                            class="form-control"
                            id="google_tag_manager_id"
                            name="google_tag_manager_id"
                            value="<?= htmlspecialchars(getSetting($settings, 'seo', 'google_tag_manager_id')) ?>"
                            placeholder="GTM-XXXXXXX"
                        >
                    </div>

                    <div class="form-group">
                        <label for="facebook_pixel_id">Facebook Pixel ID</label>
                        <input
                            type="text"
                            class="form-control"
                            id="facebook_pixel_id"
                            name="facebook_pixel_id"
                            value="<?= htmlspecialchars(getSetting($settings, 'seo', 'facebook_pixel_id')) ?>"
                            placeholder="123456789012345"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">💾 Salvar Configurações de SEO</button>
                </form>
            </div>

            <!-- TAB: EMAIL -->
            <div class="tab-pane <?= $activeTab === 'email' ? 'active' : '' ?>" id="email">
                <form method="POST" action="<?= base_url('admin/configuracoes/email') ?>">
                    <?= csrf_field() ?>

                    <h3 class="section-title">Configuração SMTP</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="mail_driver">Driver</label>
                            <select class="form-control" id="mail_driver" name="mail_driver">
                                <option value="smtp" <?= getSetting($settings, 'email', 'mail_driver') === 'smtp' ? 'selected' : '' ?>>SMTP</option>
                                <option value="sendmail" <?= getSetting($settings, 'email', 'mail_driver') === 'sendmail' ? 'selected' : '' ?>>Sendmail</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="mail_host">Host SMTP</label>
                            <input
                                type="text"
                                class="form-control"
                                id="mail_host"
                                name="mail_host"
                                value="<?= htmlspecialchars(getSetting($settings, 'email', 'mail_host')) ?>"
                                placeholder="smtp.gmail.com"
                            >
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="mail_port">Porta</label>
                            <input
                                type="number"
                                class="form-control"
                                id="mail_port"
                                name="mail_port"
                                value="<?= htmlspecialchars(getSetting($settings, 'email', 'mail_port', '587')) ?>"
                            >
                            <div class="form-help">587 (TLS) ou 465 (SSL)</div>
                        </div>

                        <div class="form-group">
                            <label for="mail_encryption">Criptografia</label>
                            <select class="form-control" id="mail_encryption" name="mail_encryption">
                                <option value="tls" <?= getSetting($settings, 'email', 'mail_encryption') === 'tls' ? 'selected' : '' ?>>TLS</option>
                                <option value="ssl" <?= getSetting($settings, 'email', 'mail_encryption') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="mail_username">Usuário SMTP</label>
                            <input
                                type="text"
                                class="form-control"
                                id="mail_username"
                                name="mail_username"
                                value="<?= htmlspecialchars(getSetting($settings, 'email', 'mail_username')) ?>"
                                placeholder="seu-email@gmail.com"
                            >
                        </div>

                        <div class="form-group">
                            <label for="mail_password">Senha SMTP</label>
                            <input
                                type="password"
                                class="form-control"
                                id="mail_password"
                                name="mail_password"
                                value="<?= htmlspecialchars(getSetting($settings, 'email', 'mail_password')) ?>"
                                placeholder="••••••••"
                            >
                            <div class="form-help">Gmail: use senha de app</div>
                        </div>
                    </div>

                    <div class="section-divider"></div>
                    <h3 class="section-title">Remetente Padrão</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="mail_from_address">E-mail Remetente <span>*</span></label>
                            <input
                                type="email"
                                class="form-control <?= isset($errors['mail_from_address']) ? 'is-invalid' : '' ?>"
                                id="mail_from_address"
                                name="mail_from_address"
                                value="<?= htmlspecialchars(getSetting($settings, 'email', 'mail_from_address')) ?>"
                                required
                            >
                            <?php if (isset($errors['mail_from_address'])): ?>
                                <div class="invalid-feedback"><?= $errors['mail_from_address'][0] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="mail_from_name">Nome Remetente <span>*</span></label>
                            <input
                                type="text"
                                class="form-control <?= isset($errors['mail_from_name']) ? 'is-invalid' : '' ?>"
                                id="mail_from_name"
                                name="mail_from_name"
                                value="<?= htmlspecialchars(getSetting($settings, 'email', 'mail_from_name')) ?>"
                                required
                            >
                            <?php if (isset($errors['mail_from_name'])): ?>
                                <div class="invalid-feedback"><?= $errors['mail_from_name'][0] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">💾 Salvar Configurações de E-mail</button>

                    <!-- Teste de E-mail -->
                    <div class="test-email-box">
                        <h3>📧 Testar Configuração de E-mail</h3>
                        <p style="margin: 0 0 15px 0; font-size: 14px; color: #666;">
                            Envie um e-mail de teste para verificar se as configurações estão corretas.
                        </p>
                        <div class="test-email-form">
                            <input
                                type="email"
                                class="form-control"
                                id="test_email"
                                placeholder="seu-email@exemplo.com"
                                style="flex: 1;"
                            >
                            <button type="button" class="btn btn-success" onclick="testEmail()">
                                🚀 Enviar Teste
                            </button>
                        </div>
                        <div id="testEmailResult" style="margin-top: 10px;"></div>
                    </div>
                </form>
            </div>

            <!-- TAB: REDES SOCIAIS -->
            <div class="tab-pane <?= $activeTab === 'social' ? 'active' : '' ?>" id="social">
                <form method="POST" action="<?= base_url('admin/configuracoes/redes-sociais') ?>">
                    <?= csrf_field() ?>

                    <h3 class="section-title">Links das Redes Sociais</h3>

                    <div class="form-group">
                        <label for="facebook_url">Facebook</label>
                        <input
                            type="url"
                            class="form-control"
                            id="facebook_url"
                            name="facebook_url"
                            value="<?= htmlspecialchars(getSetting($settings, 'social', 'facebook_url')) ?>"
                            placeholder="https://facebook.com/seu-escritorio"
                        >
                    </div>

                    <div class="form-group">
                        <label for="instagram_url">Instagram</label>
                        <input
                            type="url"
                            class="form-control"
                            id="instagram_url"
                            name="instagram_url"
                            value="<?= htmlspecialchars(getSetting($settings, 'social', 'instagram_url')) ?>"
                            placeholder="https://instagram.com/seu-escritorio"
                        >
                    </div>

                    <div class="form-group">
                        <label for="twitter_url">Twitter / X</label>
                        <input
                            type="url"
                            class="form-control"
                            id="twitter_url"
                            name="twitter_url"
                            value="<?= htmlspecialchars(getSetting($settings, 'social', 'twitter_url')) ?>"
                            placeholder="https://twitter.com/seu-escritorio"
                        >
                    </div>

                    <div class="form-group">
                        <label for="linkedin_url">LinkedIn</label>
                        <input
                            type="url"
                            class="form-control"
                            id="linkedin_url"
                            name="linkedin_url"
                            value="<?= htmlspecialchars(getSetting($settings, 'social', 'linkedin_url')) ?>"
                            placeholder="https://linkedin.com/company/seu-escritorio"
                        >
                    </div>

                    <div class="form-group">
                        <label for="youtube_url">YouTube</label>
                        <input
                            type="url"
                            class="form-control"
                            id="youtube_url"
                            name="youtube_url"
                            value="<?= htmlspecialchars(getSetting($settings, 'social', 'youtube_url')) ?>"
                            placeholder="https://youtube.com/@seu-escritorio"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">💾 Salvar Redes Sociais</button>
                </form>
            </div>

            <!-- TAB: WHATSAPP -->
            <div class="tab-pane <?= $activeTab === 'whatsapp' ? 'active' : '' ?>" id="whatsapp">
                <form method="POST" action="<?= base_url('admin/configuracoes/whatsapp') ?>">
                    <?= csrf_field() ?>

                    <h3 class="section-title">Integração com WhatsApp</h3>

                    <div class="form-group">
                        <label>
                            <input
                                type="checkbox"
                                name="whatsapp_enabled"
                                value="1"
                                <?= getSetting($settings, 'whatsapp', 'whatsapp_enabled') === '1' ? 'checked' : '' ?>
                            >
                            Habilitar integração com WhatsApp
                        </label>
                        <div class="form-help">Permite enviar notificações automáticas via WhatsApp</div>
                    </div>

                    <div class="form-group">
                        <label for="whatsapp_phone">Número WhatsApp</label>
                        <input
                            type="text"
                            class="form-control"
                            id="whatsapp_phone"
                            name="whatsapp_phone"
                            value="<?= htmlspecialchars(getSetting($settings, 'whatsapp', 'whatsapp_phone')) ?>"
                            placeholder="5511987654321"
                        >
                        <div class="form-help">Formato: código do país + DDD + número (apenas números)</div>
                    </div>

                    <div class="section-divider"></div>
                    <h3 class="section-title">API WhatsApp (Opcional)</h3>

                    <div class="form-group">
                        <label for="whatsapp_api_url">URL da API</label>
                        <input
                            type="url"
                            class="form-control"
                            id="whatsapp_api_url"
                            name="whatsapp_api_url"
                            value="<?= htmlspecialchars(getSetting($settings, 'whatsapp', 'whatsapp_api_url')) ?>"
                            placeholder="https://api.whatsapp.com/..."
                        >
                        <div class="form-help">Ex: Twilio, WhatsApp Business API, etc.</div>
                    </div>

                    <div class="form-group">
                        <label for="whatsapp_api_token">Token da API</label>
                        <input
                            type="password"
                            class="form-control"
                            id="whatsapp_api_token"
                            name="whatsapp_api_token"
                            value="<?= htmlspecialchars(getSetting($settings, 'whatsapp', 'whatsapp_api_token')) ?>"
                            placeholder="••••••••••••••••"
                        >
                    </div>

                    <div class="form-group">
                        <label for="whatsapp_message_template">Template de Mensagem</label>
                        <textarea
                            class="form-control"
                            id="whatsapp_message_template"
                            name="whatsapp_message_template"
                            rows="4"
                            placeholder="Olá {nome}, seu agendamento foi confirmado para {data} às {hora}."
                        ><?= htmlspecialchars(getSetting($settings, 'whatsapp', 'whatsapp_message_template')) ?></textarea>
                        <div class="form-help">Variáveis disponíveis: {nome}, {data}, {hora}, {servico}</div>
                    </div>

                    <button type="submit" class="btn btn-primary">💾 Salvar Configurações WhatsApp</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Botão Limpar Cache -->
    <div style="margin-top: 20px; text-align: right;">
        <button type="button" class="btn btn-warning" onclick="clearCache()">
            🗑️ Limpar Cache do Sistema
        </button>
    </div>
</div>

<script>
// Gerenciamento de Tabs
document.querySelectorAll('.tab-button').forEach(button => {
    button.addEventListener('click', function() {
        const tabName = this.getAttribute('data-tab');

        // Remove active de todos
        document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));

        // Ativa o clicado
        this.classList.add('active');
        document.getElementById(tabName).classList.add('active');

        // Atualiza URL (opcional)
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.pushState({}, '', url);
    });
});

// Teste de Email
function testEmail() {
    const emailInput = document.getElementById('test_email');
    const resultDiv = document.getElementById('testEmailResult');
    const email = emailInput.value.trim();

    if (!email) {
        alert('Por favor, digite um e-mail');
        return;
    }

    resultDiv.innerHTML = '<p style="color: #666;">Enviando teste...</p>';

    fetch('<?= base_url('admin/configuracoes/testar-email') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'test_email=' + encodeURIComponent(email) + '&csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = '<p style="color: #28a745; font-weight: 500;">✅ ' + data.message + '</p>';
        } else {
            resultDiv.innerHTML = '<p style="color: #dc3545; font-weight: 500;">❌ ' + (data.error || data.message) + '</p>';
        }
    })
    .catch(error => {
        resultDiv.innerHTML = '<p style="color: #dc3545;">❌ Erro ao testar e-mail</p>';
        console.error('Erro:', error);
    });
}

// Limpar Cache
function clearCache() {
    if (!confirm('Tem certeza que deseja limpar o cache do sistema?')) {
        return;
    }

    fetch('<?= base_url('admin/configuracoes/limpar-cache') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'csrf_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
        } else {
            alert('❌ ' + (data.error || data.message));
        }
    })
    .catch(error => {
        alert('❌ Erro ao limpar cache');
        console.error('Erro:', error);
    });
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
