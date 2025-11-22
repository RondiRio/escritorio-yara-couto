<?php
/**
 * View - Admin Posts Edit
 * Formulário de edição de posts
 */

require_once __DIR__ . '/../layout/header.php';
?>

<style>
    .form-container {
        background: var(--color-white);
        border-radius: 10px;
        padding: 40px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        max-width: 1000px;
    }

    .post-meta-info {
        background: var(--color-background);
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
        font-size: 14px;
    }

    .post-meta-info span {
        display: flex;
        align-items: center;
        gap: 5px;
        color: var(--color-text-light);
    }

    .post-meta-info strong {
        color: var(--color-primary);
    }

    .current-image {
        margin-bottom: 15px;
        text-align: center;
    }

    .current-image img {
        max-width: 300px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
        min-height: 400px;
        resize: vertical;
        font-family: var(--font-body);
    }

    select.form-control {
        cursor: pointer;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }

    .form-help {
        font-size: 13px;
        color: var(--color-text-light);
        margin-top: 5px;
    }

    .file-upload {
        border: 2px dashed #e0e0e0;
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: var(--transition);
    }

    .file-upload:hover {
        border-color: var(--color-secondary);
        background: rgba(204, 140, 93, 0.05);
    }

    .file-upload input[type="file"] {
        display: none;
    }

    .file-upload-icon {
        font-size: 48px;
        margin-bottom: 10px;
        color: var(--color-secondary);
    }

    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 10px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        min-height: 50px;
    }

    .tag-checkbox {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 8px 15px;
        background: var(--color-background);
        border-radius: 20px;
        cursor: pointer;
        transition: var(--transition);
    }

    .tag-checkbox:hover {
        background: var(--color-secondary);
        color: var(--color-white);
    }

    .tag-checkbox input[type="checkbox"] {
        cursor: pointer;
    }

    .tag-checkbox.checked {
        background: var(--color-secondary);
        color: var(--color-white);
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid var(--color-background);
        flex-wrap: wrap;
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

    .btn-danger {
        background: #dc3545;
        color: white;
        margin-left: auto;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }

        .form-container {
            padding: 25px 20px;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-danger {
            margin-left: 0;
        }
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <h1>Editar Post</h1>
    <p>Atualize as informações do post</p>
</div>

<!-- Form Container -->
<div class="form-container">
    <!-- Post Meta Info -->
    <div class="post-meta-info">
        <span>
            <strong>ID:</strong> #<?= $post['id'] ?>
        </span>
        <span>
            <strong>Slug:</strong> <?= $post['slug'] ?>
        </span>
        <span>
            <strong>Visualizações:</strong> <?= $post['views'] ?? 0 ?>
        </span>
        <span>
            <strong>Criado em:</strong> <?= format_datetime($post['created_at']) ?>
        </span>
        <?php if ($post['status'] === 'published' && !empty($post['published_at'])): ?>
        <span>
            <strong>Publicado em:</strong> <?= format_datetime($post['published_at']) ?>
        </span>
        <?php endif; ?>
    </div>

    <form action="<?= base_url('admin/posts/' . $post['id'] . '/editar') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <!-- Título -->
        <div class="form-group">
            <label for="title">Título do Post <span class="required">*</span></label>
            <input type="text" 
                   id="title" 
                   name="title" 
                   class="form-control"
                   value="<?= $post['title'] ?>"
                   required
                   autofocus>
            <div class="form-help">Alterar o título gerará um novo slug</div>
        </div>

        <!-- Categoria e Status -->
        <div class="form-row">
            <div class="form-group">
                <label for="category_id">Categoria <span class="required">*</span></label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">Selecione uma categoria...</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= $post['category_id'] == $category['id'] ? 'selected' : '' ?>>
                        <?= $category['name'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status <span class="required">*</span></label>
                <select id="status" name="status" class="form-control" required>
                    <option value="draft" <?= $post['status'] == 'draft' ? 'selected' : '' ?>>Rascunho</option>
                    <option value="published" <?= $post['status'] == 'published' ? 'selected' : '' ?>>Publicado</option>
                </select>
            </div>
        </div>

        <!-- Resumo/Excerpt -->
        <div class="form-group">
            <label for="excerpt">Resumo (Excerpt)</label>
            <textarea id="excerpt" 
                      name="excerpt" 
                      class="form-control"
                      style="min-height: 100px;"><?= $post['excerpt'] ?? '' ?></textarea>
            <div class="form-help">Breve resumo do post. Máximo 200 caracteres.</div>
        </div>

        <!-- Conteúdo -->
        <div class="form-group">
            <label for="content">Conteúdo <span class="required">*</span></label>
            <textarea id="content" 
                      name="content" 
                      class="form-control"
                      required><?= $post['content'] ?></textarea>
            <div class="form-help">
                Você pode usar HTML básico: &lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;h2&gt;, &lt;h3&gt;
            </div>
        </div>

        <!-- Imagem Destacada Atual -->
        <?php if (!empty($post['featured_image'])): ?>
        <div class="form-group">
            <label>Imagem Destacada Atual</label>
            <div class="current-image">
                <img src="<?= base_url('storage/uploads/posts/' . $post['featured_image']) ?>" 
                     alt="Imagem atual">
                <div class="form-help">Faça upload de uma nova imagem para substituir</div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Nova Imagem Destacada -->
        <div class="form-group">
            <label for="featured_image">
                <?= !empty($post['featured_image']) ? 'Substituir Imagem' : 'Imagem Destacada' ?>
            </label>
            <div class="file-upload" onclick="document.getElementById('featured_image').click()">
                <input type="file" 
                       id="featured_image" 
                       name="featured_image"
                       accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                <div class="file-upload-icon">🖼️</div>
                <p><strong>Clique para selecionar uma imagem</strong></p>
                <p style="font-size: 13px; color: var(--color-text-light);">
                    Formatos: JPG, PNG, GIF, WEBP • Tamanho máximo: 2MB
                </p>
            </div>
        </div>

        <!-- Tags -->
        <div class="form-group">
            <label>Tags</label>
            <div class="tags-container">
                <?php if (!empty($tags)): ?>
                    <?php foreach ($tags as $tag): ?>
                    <label class="tag-checkbox <?= in_array($tag['id'], $postTagIds) ? 'checked' : '' ?>">
                        <input type="checkbox" 
                               name="tags[]" 
                               value="<?= $tag['id'] ?>"
                               <?= in_array($tag['id'], $postTagIds) ? 'checked' : '' ?>>
                        <span><?= $tag['name'] ?></span>
                    </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--color-text-light); padding: 10px;">
                        Nenhuma tag cadastrada. <a href="<?= base_url('admin/tags') ?>">Criar tags</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                ✓ Atualizar Post
            </button>
            <a href="<?= base_url('admin/posts') ?>" class="btn btn-secondary">
                ← Voltar
            </a>
            
            <!-- Delete Button -->
            <form action="<?= base_url('admin/posts/' . $post['id'] . '/deletar') ?>" 
                  method="POST" 
                  style="display: inline;"
                  onsubmit="return confirm('Tem certeza que deseja excluir este post? Esta ação não pode ser desfeita.')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger">
                    🗑️ Excluir Post
                </button>
            </form>
        </div>
    </form>
</div>

<script>
// Preview do nome do arquivo selecionado
document.getElementById('featured_image').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    if (fileName) {
        const uploadDiv = this.parentElement;
        uploadDiv.querySelector('p strong').textContent = 'Arquivo: ' + fileName;
    }
});

// Highlight checked tags
document.querySelectorAll('.tag-checkbox input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            this.parentElement.classList.add('checked');
        } else {
            this.parentElement.classList.remove('checked');
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>