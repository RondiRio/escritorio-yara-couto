<?php
/**
 * View - Admin Lawyers Create
 * Formulário de cadastro de advogados
 */

require_once __DIR__ . '/../layout/header.php';
?>

<style>
    .form-container {
        background: var(--color-white);
        border-radius: 10px;
        padding: 40px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        max-width: 900px;
    }

    .form-section {
        margin-bottom: 40px;
        padding-bottom: 30px;
        border-bottom: 2px solid var(--color-background);
    }

    .form-section:last-of-type {
        border-bottom: none;
    }

    .section-title {
        font-size: 20px;
        color: var(--color-primary);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
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
        min-height: 150px;
        resize: vertical;
    }

    select.form-control {
        cursor: pointer;
    }

    .form-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }

    .form-row-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
    }

    .form-help {
        font-size: 13px;
        color: var(--color-text-light);
        margin-top: 5px;
    }

    .photo-upload {
        border: 2px dashed #e0e0e0;
        border-radius: 8px;
        padding: 40px;
        text-align: center;
        cursor: pointer;
        transition: var(--transition);
        position: relative;
    }

    .photo-upload:hover {
        border-color: var(--color-secondary);
        background: rgba(204, 140, 93, 0.05);
    }

    .photo-upload input[type="file"] {
        display: none;
    }

    .photo-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        margin: 0 auto 15px;
        background: var(--color-background);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 60px;
        color: var(--color-secondary);
        overflow: hidden;
    }

    .photo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .oab-validator {
        margin-top: 10px;
        padding: 10px;
        border-radius: 5px;
        font-size: 13px;
        display: none;
    }

    .oab-validator.valid {
        background: #d4edda;
        color: #155724;
        display: block;
    }

    .oab-validator.invalid {
        background: #f8d7da;
        color: #721c24;
        display: block;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid var(--color-background);
    }

    .btn {
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
        transition: var(--transition);
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: var(--color-secondary);
        color: var(--color-white);
    }

    .btn-primary:hover {
        background: var(--color-primary);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: var(--color-background);
        color: var(--color-text);
    }

    .btn-secondary:hover {
        background: #e0e0e0;
    }

    @media (max-width: 768px) {
        .form-row,
        .form-row-3 {
            grid-template-columns: 1fr;
        }

        .form-container {
            padding: 25px 20px;
        }
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <h1>Cadastrar Advogado</h1>
    <p>Adicione um novo advogado ao escritório</p>
</div>

<!-- Form Container -->
<div class="form-container">
    <form action="<?= base_url('admin/advogados/criar') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <!-- Seção: Informações Básicas -->
        <div class="form-section">
            <div class="section-title">
                👤 Informações Básicas
            </div>

            <div class="form-group">
                <label for="name">Nome Completo <span class="required">*</span></label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-control"
                       value="<?= old('name') ?>"
                       placeholder="Ex: Dr. João Silva"
                       required
                       autofocus>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="oab_number">Número OAB <span class="required">*</span></label>
                    <input type="text" 
                           id="oab_number" 
                           name="oab_number" 
                           class="form-control"
                           value="<?= old('oab_number') ?>"
                           placeholder="Ex: 123456"
                           required>
                    <div id="oab-validator" class="oab-validator"></div>
                </div>

                <div class="form-group">
                    <label for="oab_state">UF OAB <span class="required">*</span></label>
                    <select id="oab_state" name="oab_state" class="form-control" required>
                        <option value="">Selecione...</option>
                        <?php 
                        $estados = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
                        foreach ($estados as $estado): 
                        ?>
                        <option value="<?= $estado ?>" <?= old('oab_state') == $estado ? 'selected' : '' ?>>
                            <?= $estado ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-help" style="margin-top: -15px;">
                📚 Referência: <a href="https://cna.oab.org.br/" target="_blank">Consulta de Advogados OAB</a>
            </div>

            <div class="form-group" style="margin-top: 25px;">
                <label for="bio">Mini Biografia <span class="required">*</span></label>
                <textarea id="bio" 
                          name="bio" 
                          class="form-control"
                          placeholder="Apresentação profissional do advogado..."
                          required><?= old('bio') ?></textarea>
                <div class="form-help">Mínimo 50 caracteres. Descreva experiência e áreas de atuação.</div>
            </div>

            <div class="form-group">
                <label for="specialties">Especialidades</label>
                <input type="text" 
                       id="specialties" 
                       name="specialties" 
                       class="form-control"
                       value="<?= old('specialties') ?>"
                       placeholder="Ex: Direito Previdenciário, Trabalhista, Civil">
                <div class="form-help">Separe as especialidades por vírgula</div>
            </div>
        </div>

        <!-- Seção: Foto -->
        <div class="form-section">
            <div class="section-title">
                📷 Foto do Advogado
            </div>

            <div class="photo-upload" onclick="document.getElementById('photo').click()">
                <input type="file" 
                       id="photo" 
                       name="photo"
                       accept="image/jpeg,image/jpg,image/png,image/webp">
                <div class="photo-preview" id="photoPreview">
                    👤
                </div>
                <p><strong>Clique para selecionar uma foto</strong></p>
                <p style="font-size: 13px; color: var(--color-text-light);">
                    Formatos: JPG, PNG, WEBP • Tamanho máximo: 2MB<br>
                    Recomendado: foto profissional, fundo neutro
                </p>
            </div>
        </div>

        <!-- Seção: Contato -->
        <div class="form-section">
            <div class="section-title">
                📞 Informações de Contato
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control"
                       value="<?= old('email') ?>"
                       placeholder="advogado@seuescritorio.com.br">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Telefone</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           class="form-control"
                           value="<?= old('phone') ?>"
                           placeholder="(00) 0000-0000">
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
        </div>

        <!-- Seção: Dados Adicionais -->
        <div class="form-section">
            <div class="section-title">
                ⚙️ Configurações
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label for="cases_won">Casos Ganhos</label>
                    <input type="number" 
                           id="cases_won" 
                           name="cases_won" 
                           class="form-control"
                           value="<?= old('cases_won', 0) ?>"
                           min="0">
                </div>

                <div class="form-group">
                    <label for="display_order">Ordem de Exibição</label>
                    <input type="number" 
                           id="display_order" 
                           name="display_order" 
                           class="form-control"
                           value="<?= old('display_order', 999) ?>"
                           min="0">
                    <div class="form-help">Menor número = maior prioridade</div>
                </div>

                <div class="form-group">
                    <label for="status">Status <span class="required">*</span></label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="active" <?= old('status', 'active') == 'active' ? 'selected' : '' ?>>
                            Ativo
                        </option>
                        <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>
                            Inativo
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                ✓ Cadastrar Advogado
            </button>
            <a href="<?= base_url('admin/advogados') ?>" class="btn btn-secondary">
                ← Cancelar
            </a>
        </div>
    </form>
</div>

<script>
// Preview da foto
document.getElementById('photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').innerHTML = 
                '<img src="' + e.target.result + '" alt="Preview">';
        }
        reader.readAsDataURL(file);
    }
});

// Validação OAB (simples validação de formato)
document.getElementById('oab_number').addEventListener('input', function() {
    const oabNumber = this.value.replace(/\D/g, '');
    const validator = document.getElementById('oab-validator');
    
    if (oabNumber.length >= 3 && oabNumber.length <= 7) {
        validator.className = 'oab-validator valid';
        validator.textContent = '✓ Formato válido de OAB';
    } else if (oabNumber.length > 0) {
        validator.className = 'oab-validator invalid';
        validator.textContent = '✗ Número de OAB deve ter entre 3 e 7 dígitos';
    } else {
        validator.style.display = 'none';
    }
});

// Máscaras de telefone
function maskPhone(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length <= 10) {
        value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    } else {
        value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    }
    input.value = value;
}

document.getElementById('phone').addEventListener('input', function() {
    maskPhone(this);
});

document.getElementById('whatsapp').addEventListener('input', function() {
    maskPhone(this);
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>