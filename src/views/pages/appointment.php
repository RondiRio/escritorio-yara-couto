<?php
/**
 * View - Appointment
 * Página de Agendamento de Consultas
 */
?>

<style>
    .appointment-section {
        padding: 80px 0;
        background: var(--color-white);
    }

    .appointment-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        margin-top: 60px;
    }

    .appointment-info {
        background: var(--color-background);
        padding: 40px;
        border-radius: 10px;
    }

    .appointment-info h3 {
        font-size: 28px;
        margin-bottom: 30px;
        color: var(--color-primary);
    }

    .info-box {
        background: var(--color-white);
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 25px;
        border-left: 4px solid var(--color-secondary);
    }

    .info-box h4 {
        color: var(--color-primary);
        margin-bottom: 15px;
        font-size: 18px;
    }

    .info-box ul {
        list-style: none;
        padding: 0;
    }

    .info-box li {
        padding: 8px 0;
        color: var(--color-text-light);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-box li::before {
        content: "✓";
        color: var(--color-secondary);
        font-weight: bold;
        font-size: 18px;
    }

    .appointment-form {
        background: var(--color-background);
        padding: 40px;
        border-radius: 10px;
    }

    .appointment-form h3 {
        font-size: 28px;
        margin-bottom: 30px;
        color: var(--color-primary);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--color-primary);
        font-size: 14px;
    }

    .required {
        color: #e74c3c;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 15px;
        font-family: var(--font-body);
        transition: all 0.3s ease;
        background: var(--color-white);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--color-secondary);
        box-shadow: 0 0 0 3px rgba(204, 140, 93, 0.1);
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    select.form-control {
        cursor: pointer;
    }

    .form-help {
        font-size: 13px;
        color: var(--color-text-light);
        margin-top: 5px;
    }

    .btn-submit {
        width: 100%;
        padding: 15px;
        background: var(--color-secondary);
        color: var(--color-white);
        border: none;
        border-radius: 8px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        background: var(--color-primary);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    @media (max-width: 768px) {
        .appointment-grid {
            grid-template-columns: 1fr;
        }

        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Agendar Consulta</h1>
        <p>Preencha o formulário e entraremos em contato</p>
    </div>
</div>

<!-- Appointment Section -->
<section class="appointment-section">
    <div class="container">
        <div class="appointment-grid">
            <!-- Informações -->
            <div class="appointment-info">
                <h3>Como Funciona?</h3>

                <div class="info-box">
                    <h4>1️⃣ Preencha o Formulário</h4>
                    <ul>
                        <li>Informe seus dados pessoais</li>
                        <li>Escolha data e horário de preferência</li>
                        <li>Descreva brevemente seu caso</li>
                    </ul>
                </div>

                <div class="info-box">
                    <h4>2️⃣ Confirmação</h4>
                    <ul>
                        <li>Receba confirmação por e-mail</li>
                        <li>Nossa equipe entrará em contato</li>
                        <li>Confirmaremos o agendamento</li>
                    </ul>
                </div>

                <div class="info-box">
                    <h4>3️⃣ Consulta</h4>
                    <ul>
                        <li>Compareça no horário marcado</li>
                        <li>Traga documentos relevantes</li>
                        <li>Receba orientação especializada</li>
                    </ul>
                </div>

                <div style="margin-top: 30px; padding: 20px; background: var(--color-white); border-radius: 8px;">
                    <strong style="color: var(--color-primary);">⏰ Horário de Atendimento:</strong><br>
                    <span style="color: var(--color-text-light);">
                        Segunda a Sexta: 9h às 18h<br>
                        Sábado: 9h às 13h
                    </span>
                </div>
            </div>

            <!-- Formulário -->
            <div class="appointment-form">
                <h3>Agende sua Consulta</h3>

                <form action="<?= base_url('agendamento/criar') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="form-row">
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
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Telefone <span class="required">*</span></label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   class="form-control"
                                   value="<?= old('phone') ?>"
                                   placeholder="(00) 00000-0000"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="whatsapp">WhatsApp</label>
                            <input type="tel" 
                                   id="whatsapp" 
                                   name="whatsapp" 
                                   class="form-control"
                                   value="<?= old('whatsapp') ?>"
                                   placeholder="(00) 00000-0000">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="preferred_date">Data Preferida <span class="required">*</span></label>
                            <input type="date" 
                                   id="preferred_date" 
                                   name="preferred_date" 
                                   class="form-control"
                                   value="<?= old('preferred_date') ?>"
                                   min="<?= date('Y-m-d') ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="preferred_time">Horário Preferido <span class="required">*</span></label>
                            <select id="preferred_time" 
                                    name="preferred_time" 
                                    class="form-control"
                                    required>
                                <option value="">Selecione...</option>
                                <option value="08:00">08:00</option>
                                <option value="09:00">09:00</option>
                                <option value="10:00">10:00</option>
                                <option value="11:00">11:00</option>
                                <option value="14:00">14:00</option>
                                <option value="15:00">15:00</option>
                                <option value="16:00">16:00</option>
                                <option value="17:00">17:00</option>
                                <option value="18:00">18:00</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="consultation_type">Tipo de Consulta <span class="required">*</span></label>
                        <select id="consultation_type" 
                                name="consultation_type" 
                                class="form-control"
                                required>
                            <option value="">Selecione...</option>
                            <option value="Direito Previdenciário">Direito Previdenciário</option>
                            <option value="Aposentadoria">Aposentadoria</option>
                            <option value="Auxílio-Doença">Auxílio-Doença</option>
                            <option value="BPC/LOAS">BPC/LOAS</option>
                            <option value="Pensão por Morte">Pensão por Morte</option>
                            <option value="Direito de Família">Direito de Família</option>
                            <option value="Direito do Trabalho">Direito do Trabalho</option>
                            <option value="Direito Imobiliário">Direito Imobiliário</option>
                            <option value="Direito Criminal">Direito Criminal</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">Mensagem (opcional)</label>
                        <textarea id="message" 
                                  name="message" 
                                  class="form-control"
                                  placeholder="Descreva brevemente seu caso..."><?= old('message') ?></textarea>
                        <div class="form-help">
                            Conte-nos brevemente sobre sua situação para melhor atendê-lo
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        📅 Confirmar Agendamento
                    </button>

                    <p style="font-size: 13px; color: var(--color-text-light); margin-top: 15px; text-align: center;">
                        Ao enviar este formulário, você concorda com nossa 
                        <a href="<?= base_url('politica-de-privacidade') ?>" style="color: var(--color-secondary);">
                            Política de Privacidade
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>


escritorio-yara-couto/
│
├── 📁 public/                          # Arquivos públicos estáticos
│   ├── css/
│   │   ├── main.css 
│   │   ├── admin.css
│   │   └── responsive.css
│   ├── js/
│   │   ├── main.js
│   │   ├── form-validation.js
│   │   └── admin.js
│   ├── images/
│   │   ├── logo.png
│   │   ├── advogados/
│   │   └── posts/
│   ├── fonts/
│   └── favicon.ico
│
├── 📁 src/                             # Código-fonte principal
│   │
│   ├── 📁 config/                      # Configurações
│   │   ├── database.php               # Configuração do banco
│   │   ├── app.php                    # Configurações gerais
│   │   ├── mail.php                   # Config SMTP
│   │   ├── whatsapp.php               # Config WhatsApp API
│   │   └── constants.php              # Constantes do sistema
│   │
│   ├── 📁 controllers/                 # CONTROLLERS (MVC)
│   │   ├── HomeController.php
│   │   ├── AboutController.php
│   │   ├── AreasController.php
│   │   ├── TeamController.php
│   │   ├── BlogController.php
│   │   ├── ContactController.php
│   │   ├── AppointmentController.php
│   │   │
│   │   └── 📁 admin/                   # Controllers Admin
│   │       ├── AdminController.php
│   │       ├── AuthController.php
│   │       ├── DashboardController.php
│   │       ├── PostController.php
│   │       ├── LawyerController.php
│   │       ├── AppointmentAdminController.php
│   │       └── SettingsController.php
│   │
│   ├── 📁 models/                      # MODELS (MVC)
│   │   ├── User.php
│   │   ├── Post.php
│   │   ├── Category.php
│   │   ├── Tag.php
│   │   ├── Lawyer.php
│   │   ├── Appointment.php
│   │   ├── Setting.php
│   │   └── ActivityLog.php
│   │
│   ├── 📁 views/                       # VIEWS (MVC)
│   │   │
│   │   ├── 📁 layout/                  # Layouts base
│   │   │   ├── header.php
│   │   │   ├── footer.php
│   │   │   ├── navigation.php
│   │   │   └── admin-layout.php
│   │   │
│   │   ├── 📁 pages/                   # Páginas públicas
│   │   │   ├── home.php
│   │   │   ├── about.php
│   │   │   ├── areas.php
│   │   │   ├── team.php
│   │   │   ├── contact.php
│   │   │   ├── appointment.php
│   │   │   └── privacy-policy.php
│   │   │
│   │   ├── 📁 blog/                    # Views do Blog
│   │   │   ├── index.php
│   │   │   ├── single.php
│   │   │   ├── category.php
│   │   │   └── search.php
│   │   │
│   │   └── 📁 admin/                   # Views Admin
│   │       ├── login.php
│   │       ├── dashboard.php
│   │       │
│   │       ├── 📁 posts/
│   │       │   ├── index.php
│   │       │   ├── create.php
│   │       │   ├── edit.php
│   │       │   └── delete.php
│   │       │
│   │       ├── 📁 lawyers/
│   │       │   ├── index.php
│   │       │   ├── create.php
│   │       │   ├── edit.php
│   │       │   └── delete.php
│   │       │
│   │       ├── 📁 appointments/
│   │       │   ├── index.php
│   │       │   └── view.php
│   │       │
│   │       └── 📁 settings/
│   │           └── index.php
│   │
│   ├── 📁 core/                        # Classes core do sistema
│   │   ├── Database.php               # Conexão com banco
│   │   ├── Model.php                  # Model base
│   │   ├── Controller.php             # Controller base
│   │   ├── Router.php                 # Sistema de rotas
│   │   ├── Request.php                # Tratamento de requisições
│   │   ├── Response.php               # Tratamento de respostas
│   │   ├── Session.php                # Gerenciamento de sessão
│   │   ├── Auth.php                   # Autenticação
│   │   └── Validator.php              # Validação de dados
│   │
│   ├── 📁 middleware/                  # Middlewares
│   │   ├── AuthMiddleware.php
│   │   ├── AdminMiddleware.php
│   │   └── CsrfMiddleware.php
│   │
│   ├── 📁 services/                    # Serviços/Lógica de negócio
│   │   ├── EmailService.php           # Envio de e-mails
│   │   ├── WhatsAppService.php        # Integração WhatsApp
│   │   ├── UploadService.php          # Upload de arquivos
│   │   ├── ValidationService.php      # Validações customizadas
│   │   ├── OABValidationService.php   # Validação de OAB
│   │   └── SEOService.php             # Otimização SEO
│   │
│   ├── 📁 helpers/                     # Funções auxiliares
│   │   ├── functions.php              # Funções gerais
│   │   ├── string_helpers.php
│   │   ├── date_helpers.php
│   │   └── url_helpers.php
│   │
│   ├── 📁 repositories/                # Camada de acesso a dados
│   │   ├── PostRepository.php
│   │   ├── LawyerRepository.php
│   │   ├── AppointmentRepository.php
│   │   └── UserRepository.php
│   │
│   └── 📁 routes/                      # Definição de rotas
│       ├── web.php                    # Rotas públicas
│       └── admin.php                  # Rotas admin
│
├── 📁 database/                        # Banco de dados
│   ├── 📁 migrations/                  # Migrações
│   │   ├── 001_create_users_table.sql
│   │   ├── 002_create_posts_table.sql
│   │   ├── 003_create_lawyers_table.sql
│   │   ├── 004_create_appointments_table.sql
│   │   ├── 005_create_categories_table.sql
│   │   ├── 006_create_tags_table.sql
│   │   ├── 007_create_settings_table.sql
│   │   └── 008_create_activity_logs_table.sql
│   │
│   ├── 📁 seeds/                       # Dados iniciais
│   │   ├── UsersSeeder.sql
│   │   ├── CategoriesSeeder.sql
│   │   └── SettingsSeeder.sql
│   │
│   └── schema.sql                     # Schema completo
│
├── 📁 storage/                         # Armazenamento
│   ├── 📁 logs/                        # Logs do sistema
│   │   ├── app.log
│   │   ├── error.log
│   │   └── activity.log
│   │
│   ├── 📁 uploads/                     # Uploads
│   │   ├── posts/
│   │   ├── lawyers/
│   │   └── temp/
│   │
│   └── 📁 cache/                       # Cache
│       └── views/
│
├── 📁 tests/                           # Testes
│   ├── 📁 unit/
│   ├── 📁 integration/
│   └── 📁 functional/
│
├── 📁 docs/                            # Documentação
│   ├── API.md
│   ├── INSTALACAO.md
│   ├── CONFIGURACAO.md
│   └── REQUISITOS_LEGAIS.md
│
├── .htaccess                          # Configuração Apache
├── .env.example                       # Exemplo de variáveis de ambiente
├── .gitignore
├── index.php                          # Ponto de entrada
├── composer.json                      # Dependências PHP
├── README.md
└── LICENSE