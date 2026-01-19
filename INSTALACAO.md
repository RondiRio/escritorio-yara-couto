# 🚀 Guia de Instalação - Sistema de Gestão de Escritórios

## 📋 Pré-requisitos

- **XAMPP** instalado (PHP 7.4+ e MySQL)
- **Composer** (opcional, para dependências)
- Navegador web moderno

---

## 🔧 Instalação Passo a Passo

### **1. Preparar o Projeto**

#### 1.1. Clonar/Copiar o Projeto
```bash
# Copie a pasta do projeto para o diretório htdocs do XAMPP
# Windows: C:\xampp\htdocs\escritorio-yara-couto
# Linux: /opt/lampp/htdocs/escritorio-yara-couto
# Mac: /Applications/XAMPP/htdocs/escritorio-yara-couto
```

---

### **2. Configurar o Banco de Dados**

#### 2.1. Iniciar MySQL no XAMPP
1. Abra o **XAMPP Control Panel**
2. Clique em **Start** para Apache e MySQL
3. Aguarde os serviços iniciarem (ficam verdes)

#### 2.2. Acessar o phpMyAdmin
1. Abra seu navegador
2. Acesse: `http://localhost/phpmyadmin`

#### 2.3. Criar o Banco de Dados
1. No phpMyAdmin, clique em **"Novo"** na lateral esquerda
2. Nome do banco: `escritorio_db`
3. Collation: `utf8mb4_unicode_ci`
4. Clique em **Criar**

#### 2.4. Importar o Schema
1. Selecione o banco `escritorio_db` na lateral esquerda
2. Clique na aba **"Importar"**
3. Clique em **"Escolher arquivo"**
4. Navegue até: `escritorio-yara-couto/database/schema.sql`
5. Clique em **"Executar"**
6. Aguarde a mensagem de sucesso

**OU execute manualmente as migrations:**
1. Abra a aba **SQL**
2. Cole o conteúdo de cada arquivo em `database/migrations/` na ordem:
   - `001_create_users_table.sql`
   - `002_create_categories_table.sql`
   - `003_create_posts_table.sql`
   - E assim por diante...
3. Execute cada um clicando em **"Executar"**

---

### **3. Configurar o Arquivo .env**

#### 3.1. Copiar o arquivo exemplo
```bash
# No diretório do projeto
cp .env.example .env
```

**OU no Windows:**
- Copie o arquivo `.env.example`
- Cole na mesma pasta
- Renomeie para `.env` (sem "example")

#### 3.2. Editar o arquivo .env
Abra o arquivo `.env` com um editor de texto e configure:

```env
# ==================== APLICAÇÃO ====================
APP_NAME=Sistema de Gestão de Escritórios
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/escritorio-yara-couto

# ==================== BANCO DE DADOS ====================
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=escritorio_db
DB_USERNAME=root
DB_PASSWORD=

# ==================== EMAIL (OPCIONAL NO INÍCIO) ====================
# Configurar depois para funcionalidades de email
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-de-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=contato@seuescritorio.com.br
MAIL_FROM_NAME=Sistema de Gestão de Escritórios

# ==================== WHATSAPP (OPCIONAL) ====================
WHATSAPP_API_URL=
WHATSAPP_API_TOKEN=
WHATSAPP_PHONE=
```

**⚠️ IMPORTANTE:** Ajuste o `APP_URL` se seu projeto estiver em uma subpasta diferente!

---

### **4. Configurar o .htaccess**

#### 4.1. Abrir o arquivo .htaccess
Localize o arquivo `.htaccess` na raiz do projeto.

#### 4.2. Ajustar o RewriteBase
Se o projeto estiver na raiz do htdocs:
```apache
# Manter comentado
# RewriteBase /
```

Se estiver em uma subpasta (como `htdocs/escritorio-yara-couto/`):
```apache
# Descomentar e ajustar:
RewriteBase /escritorio-yara-couto/
```

---

### **5. Criar Diretórios Necessários**

Crie as seguintes pastas se não existirem:

```bash
escritorio-yara-couto/
├── public/
│   └── uploads/
│       ├── posts/
│       ├── lawyers/
│       └── avatars/
└── storage/
    ├── logs/
    └── cache/
```

**No Windows:**
1. Entre na pasta `public` e crie a pasta `uploads`
2. Dentro de `uploads`, crie: `posts`, `lawyers`, `avatars`
3. Na raiz do projeto, crie a pasta `storage`
4. Dentro de `storage`, crie: `logs`, `cache`

#### 5.1. Definir Permissões (Linux/Mac)
```bash
chmod -R 755 public/uploads/
chmod -R 755 storage/
```

---

### **6. Instalar Dependências (Opcional)**

Se você tiver o **Composer** instalado:

```bash
cd escritorio-yara-couto
composer install
```

**Não tem Composer?** Tudo bem! As bibliotecas principais já estão incluídas.

---

### **7. Acessar o Sistema**

#### 7.1. Abrir no Navegador
```
http://localhost/escritorio-yara-couto
```

Você deve ver a **página inicial** do site (área pública).

#### 7.2. Acessar o Painel Administrativo
```
http://localhost/escritorio-yara-couto/admin/login
```

#### 7.3. Credenciais Padrão
- **Email:** `admin@escritorio.com.br`
- **Senha:** `admin123`

**⚠️ IMPORTANTE:** Altere a senha imediatamente após o primeiro login!

---

## 🎯 Próximos Passos Após Instalação

### 1. Alterar Senha do Admin
1. Faça login
2. Vá em **Meu Perfil** > **Alterar Senha**
3. Defina uma senha segura

### 2. Configurar o Sistema
1. Acesse **Configurações**
2. Aba **Geral**: Preencha dados do escritório
3. Aba **SEO**: Configure meta tags
4. Aba **Email**: Configure SMTP (se quiser emails funcionando)
5. Aba **Redes Sociais**: Links das redes

### 3. Testar Email (Opcional)
1. Configure o SMTP em **Configurações** > **Email**
2. Clique em **"Testar Email"**
3. Insira seu email
4. Verifique se recebeu

### 4. Criar Usuários
1. Acesse **Usuários** > **Novo Usuário**
2. Preencha os dados
3. Defina o **role** (admin, editor, author)

---

## 🔍 Verificação de Instalação

### ✅ Checklist
- [ ] XAMPP Apache e MySQL rodando
- [ ] Banco de dados `escritorio_db` criado
- [ ] Schema importado (tabelas visíveis no phpMyAdmin)
- [ ] Arquivo `.env` configurado
- [ ] `.htaccess` com RewriteBase correto
- [ ] Pastas `public/uploads` e `storage` criadas
- [ ] Página inicial abre corretamente
- [ ] Login admin funciona
- [ ] Dashboard aparece após login

---

## 🐛 Solução de Problemas

### Problema 1: "404 - Página não encontrada"
**Causa:** RewriteBase incorreto no .htaccess

**Solução:**
```apache
# Se projeto está em: http://localhost/escritorio-yara-couto
RewriteBase /escritorio-yara-couto/
```

---

### Problema 2: "Erro ao conectar com banco de dados"
**Causa:** Credenciais ou nome do banco incorretos

**Solução:**
1. Verifique o arquivo `.env`:
   ```env
   DB_HOST=127.0.0.1
   DB_DATABASE=escritorio_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```
2. Confirme que o banco `escritorio_db` existe no phpMyAdmin
3. Verifique se o MySQL está rodando no XAMPP

---

### Problema 3: "Erro 500 - Internal Server Error"
**Causas possíveis:**

**1. mod_rewrite não habilitado:**
- Abra: `C:\xampp\apache\conf\httpd.conf`
- Procure por: `#LoadModule rewrite_module modules/mod_rewrite.so`
- Remova o `#` no início da linha
- Reinicie o Apache no XAMPP

**2. Erros de PHP:**
- Habilite debug no `.env`:
  ```env
  APP_DEBUG=true
  ```
- Veja os erros na tela

**3. Permissões:**
- Garanta que as pastas `storage` e `public/uploads` existem

---

### Problema 4: "Email não está sendo enviado"
**Causa:** Configuração SMTP incorreta

**Solução:**
1. Use **Mailtrap** para testes (não precisa de email real):
   - Acesse: https://mailtrap.io (crie conta grátis)
   - Pegue as credenciais SMTP
   - Configure no `.env`:
     ```env
     MAIL_HOST=smtp.mailtrap.io
     MAIL_PORT=2525
     MAIL_USERNAME=seu-username-mailtrap
     MAIL_PASSWORD=sua-senha-mailtrap
     MAIL_ENCRYPTION=tls
     ```

2. Para Gmail:
   - Use **Senha de App** (não a senha normal)
   - Acesse: https://myaccount.google.com/apppasswords
   - Gere uma senha de app
   - Use essa senha no `.env`

---

### Problema 5: "Estilos não estão carregando"
**Causa:** Caminhos incorretos

**Solução:**
1. Verifique o `APP_URL` no `.env`
2. Garanta que `public/css` e `public/js` existem
3. Limpe o cache do navegador (Ctrl + Shift + R)

---

### Problema 6: "Upload de imagens não funciona"
**Causa:** Pastas não existem ou sem permissão

**Solução:**
```bash
# Criar pastas
mkdir -p public/uploads/posts
mkdir -p public/uploads/lawyers
mkdir -p public/uploads/avatars

# Linux/Mac: Dar permissões
chmod -R 755 public/uploads/
```

**Windows:** Clique com botão direito > Propriedades > Desmarque "Somente leitura"

---

## 📚 Estrutura do Projeto

```
escritorio-yara-couto/
├── database/           # SQL schemas e migrations
├── public/            # Arquivos públicos (CSS, JS, uploads)
├── src/
│   ├── config/        # Configurações
│   ├── controllers/   # Controllers (lógica)
│   ├── core/          # Classes core (Router, Controller, DB)
│   ├── helpers/       # Funções auxiliares
│   ├── middleware/    # Middlewares (Auth, CSRF, etc)
│   ├── models/        # Models (acesso ao banco)
│   ├── routes/        # Definição de rotas
│   └── views/         # Templates HTML/PHP
├── storage/           # Logs e cache
├── .env               # Configurações (NÃO COMMITAR)
├── .env.example       # Exemplo de configurações
├── .htaccess          # Configuração Apache
├── composer.json      # Dependências PHP
└── index.php          # Ponto de entrada
```

---

## 🎓 Primeiros Passos no Sistema

### Área Pública (Website)
- **Home:** `http://localhost/escritorio-yara-couto`
- **Sobre:** `/sobre`
- **Áreas de Atuação:** `/areas`
- **Equipe:** `/equipe`
- **Blog:** `/blog`
- **Contato:** `/contato`
- **Agendamento:** `/agendamento`

### Área Administrativa
- **Login:** `/admin/login`
- **Dashboard:** `/admin`
- **Posts:** `/admin/posts`
- **Advogados:** `/admin/advogados`
- **Agendamentos:** `/admin/agendamentos`
- **Usuários:** `/admin/usuarios`
- **Meu Perfil:** `/admin/perfil`
- **Configurações:** `/admin/configuracoes`

---

## 🔒 Segurança

### Funcionalidades Implementadas:
- ✅ Proteção CSRF em todos os formulários
- ✅ Sanitização automática de entradas (XSS)
- ✅ Headers de segurança (X-Frame-Options, CSP, etc)
- ✅ Senhas com hash bcrypt
- ✅ Sistema de recuperação de senha com tokens
- ✅ Logs de auditoria de todas as ações
- ✅ Sistema de permissões por roles

### Recomendações:
1. **Altere a senha padrão imediatamente**
2. **Use senhas fortes** (mínimo 8 caracteres, letras, números, símbolos)
3. **Não exponha o `.env`** (já está no .gitignore)
4. **Mantenha o PHP atualizado**
5. **Faça backups regulares** do banco de dados

---

## 📞 Suporte

Em caso de dúvidas:
1. Verifique a seção **"Solução de Problemas"** acima
2. Veja os logs em `storage/logs/`
3. Ative `APP_DEBUG=true` no `.env` para ver erros detalhados
4. Verifique o console do navegador (F12) para erros JavaScript

---

## 📝 Credenciais Padrão

### Usuário Admin
- **Email:** admin@escritorio.com.br
- **Senha:** admin123
- **Role:** admin

**⚠️ IMPORTANTE:** Altere essas credenciais imediatamente após a instalação!

---

## 🎉 Pronto!

Seu sistema está instalado e funcionando!

Próximos passos recomendados:
1. ✅ Alterar senha do admin
2. ✅ Configurar dados do escritório
3. ✅ Adicionar advogados da equipe
4. ✅ Criar posts no blog
5. ✅ Testar agendamentos
6. ✅ Configurar emails (opcional)

**Bom uso do sistema!** 🚀
