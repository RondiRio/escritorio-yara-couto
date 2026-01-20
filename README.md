# 🏢 Sistema de Gestão de Escritórios

Sistema completo de gerenciamento para **Escritórios de Advocacia e Contabilidade** com funcionalidades avançadas, segurança robusta e interface moderna.

---

## ✨ Funcionalidades Principais

### 🌐 **Website Institucional**
- Página inicial personalizável
- Sobre o escritório
- Áreas de atuação
- Equipe de profissionais
- Blog com artigos
- Formulário de contato
- Sistema de agendamentos online

### 🔐 **Painel Administrativo**
- Dashboard com estatísticas em tempo real
- Gerenciamento completo de usuários (CRUD)
- Sistema de permissões por roles (Admin, Editor, Author)
- Gerenciamento de posts/artigos do blog
- Gestão de advogados/equipe
- Controle de agendamentos
- Configurações do sistema (Geral, SEO, Email, Redes Sociais, WhatsApp)
- Logs de auditoria de todas as ações
- Perfil de usuário com upload de avatar

### 🛡️ **Segurança**
- ✅ Proteção CSRF em 100% dos formulários
- ✅ Sanitização automática contra XSS
- ✅ Headers de segurança (X-Frame-Options, CSP, etc)
- ✅ Sistema de recuperação de senha com tokens seguros
- ✅ Senhas com hash bcrypt
- ✅ Middlewares de autenticação e autorização
- ✅ Rate limiting em recuperação de senha
- ✅ Logs de auditoria completos

### 📧 **Sistema de Email**
- PHPMailer integrado (SMTP confiável)
- Templates HTML responsivos
- Suporte a Gmail, Outlook, Mailtrap
- Teste de configuração de email
- Logs de emails enviados

---

## 🚀 Início Rápido (XAMPP)

### **Opção 1: Instalação Automatizada**

1. **Copie o projeto** para `C:\xampp\htdocs\escritorio-yara-couto`

2. **Execute o verificador de instalação:**
   ```
   http://localhost/escritorio-yara-couto/check-install.php
   ```

3. **Siga as instruções** na tela para corrigir qualquer problema

4. **Acesse o sistema:**
   ```
   http://localhost/escritorio-yara-couto
   ```

### **Opção 2: Instalação Manual**

📚 **[Guia Completo de Instalação](INSTALACAO.md)** - Passo a passo detalhado

**Resumo rápido:**

```bash
# 1. Copiar projeto para htdocs
cp -r escritorio-yara-couto C:/xampp/htdocs/

# 2. Criar arquivo .env
cp .env.example .env

# 3. Editar .env com suas configurações
# 4. Criar banco de dados 'escritorio_db' no phpMyAdmin
# 5. Importar database/schema.sql
# 6. Acessar: http://localhost/escritorio-yara-couto
```

---

## 📋 Requisitos do Sistema

- **PHP:** 7.4 ou superior
- **MySQL:** 5.7 ou superior
- **Apache:** com mod_rewrite habilitado
- **Extensões PHP:**
  - PDO
  - pdo_mysql
  - mbstring
  - openssl
  - json

---

## 🔑 Credenciais Padrão

- **URL Admin:** `http://localhost/escritorio-yara-couto/admin/login`
- **Email:** `admin@escritorio.com.br`
- **Senha:** `admin123`

**⚠️ IMPORTANTE:** Altere a senha imediatamente após o primeiro login!

---

## 📁 Estrutura do Projeto

```
escritorio-yara-couto/
├── database/              # SQL schemas e migrations
│   ├── migrations/        # Arquivos de migração individuais
│   └── schema.sql         # Schema completo
├── public/                # Arquivos públicos
│   ├── css/              # Estilos
│   ├── js/               # JavaScripts
│   └── uploads/          # Arquivos enviados
├── src/
│   ├── config/           # Configurações
│   ├── controllers/      # Controllers (lógica de negócio)
│   │   ├── admin/       # Controllers administrativos
│   │   └── ...          # Controllers públicos
│   ├── core/            # Classes core do sistema
│   │   ├── Controller.php
│   │   ├── Router.php
│   │   ├── Database.php
│   │   └── Mailer.php
│   ├── helpers/         # Funções auxiliares
│   ├── middleware/      # Middlewares de segurança
│   │   ├── AuthMiddleware.php
│   │   ├── CsrfMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   └── ...
│   ├── models/          # Models (acesso ao banco)
│   ├── routes/          # Definição de rotas
│   │   ├── web.php     # Rotas públicas
│   │   └── admin.php   # Rotas administrativas
│   └── views/           # Templates HTML/PHP
│       ├── admin/      # Views administrativas
│       ├── pages/      # Views públicas
│       └── layout/     # Layouts compartilhados
├── storage/
│   ├── logs/           # Logs do sistema
│   └── cache/          # Cache
├── .env.example        # Exemplo de configurações
├── .htaccess          # Configuração Apache
├── index.php         # Ponto de entrada
├── check-install.php # Verificador de instalação
├── INSTALACAO.md    # Guia completo de instalação
└── README.md        # Este arquivo
```

---

## 🎯 Funcionalidades Implementadas

### ✅ **Módulo de Autenticação**
- Login/Logout com logs de auditoria
- Recuperação de senha com tokens SHA-256
- Sistema de permissões (Admin, Editor, Author)
- Middleware de autenticação automático

### ✅ **Módulo de Usuários**
- CRUD completo de usuários
- Paginação e filtros avançados
- Estatísticas por role e status
- Não permite auto-exclusão/desativação
- Toggle de status via AJAX

### ✅ **Módulo de Perfil**
- Edição de dados pessoais
- Alteração de senha com validações
- Upload de avatar (2MB, JPG/PNG)
- Histórico de atividades recentes

### ✅ **Módulo de Configurações**
- **Geral:** Nome, descrição, contatos, OAB
- **SEO:** Meta tags, Analytics, Tag Manager
- **Email:** Configuração SMTP completa
- **Redes Sociais:** Links para todas as redes
- **WhatsApp:** Integração com API
- **Sistema:** Limpar cache, info do servidor

### ✅ **Módulo de Posts/Blog**
- CRUD completo de artigos
- Categorias e tags
- Sistema de busca
- Upload de imagens
- Status (publicado/rascunho)
- Contador de visualizações

### ✅ **Módulo de Advogados**
- CRUD completo
- Upload de foto
- Áreas de especialização
- Reordenação
- Status ativo/inativo

### ✅ **Módulo de Agendamentos**
- Formulário público de agendamento
- Painel administrativo de gestão
- Confirmação, conclusão e cancelamento
- Envio de emails automáticos
- Filtros por status e data

### ✅ **Módulo de Logs**
- Registro de todas as ações do sistema
- Filtros por usuário, ação, data, IP
- Limpeza automática de logs antigos
- Rastreamento de IP e User Agent

---

## 🛠️ Tecnologias Utilizadas

- **Backend:** PHP 7.4+ (Vanilla, sem framework)
- **Banco de Dados:** MySQL 5.7+
- **Email:** PHPMailer 6.5+
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Arquitetura:** MVC (Model-View-Controller)
- **Roteamento:** Router customizado com regex
- **Segurança:** CSRF, XSS, SQL Injection protection
- **Cache:** Sistema de cache em arquivos

---

## 📚 Documentação

- **[Guia de Instalação Completo](INSTALACAO.md)** - Passo a passo detalhado com troubleshooting
- **[Verificador de Instalação](check-install.php)** - Script automático de verificação
- **[Documentação do Banco de Dados](database/README.md)** - Schema e estrutura

---

## 🔐 Segurança e Boas Práticas

### **Implementado:**
- ✅ Proteção CSRF em todos os formulários POST/PUT/DELETE/PATCH
- ✅ Sanitização automática de todas as entradas
- ✅ Headers de segurança (X-Frame-Options, CSP, X-XSS-Protection)
- ✅ Prepared Statements (proteção contra SQL Injection)
- ✅ Password hashing com bcrypt
- ✅ Tokens de recuperação de senha com hash SHA-256
- ✅ Rate limiting em recuperação de senha (5 min)
- ✅ Logs de auditoria completos
- ✅ Sistema de middlewares para autenticação/autorização

### **Recomendações para Produção:**
1. ⚠️ Alterar `APP_DEBUG=false` no `.env`
2. ⚠️ Usar HTTPS (SSL/TLS)
3. ⚠️ Alterar todas as senhas padrão
4. ⚠️ Configurar backup automático do banco
5. ⚠️ Restringir permissões de pastas (755 para diretórios, 644 para arquivos)
6. ⚠️ Habilitar logs de erro do PHP
7. ⚠️ Implementar rate limiting no login

---

## 📊 Estatísticas do Projeto

- **Controllers:** 15+ (Admin: 10, Público: 5)
- **Models:** 8
- **Middlewares:** 5
- **Rotas:** 300+
- **Views:** 30+
- **Tabelas do Banco:** 9
- **Migrations:** 9
- **Linhas de Código:** ~15.000+

---

## 🎨 Capturas de Tela

_(Adicione capturas de tela do sistema aqui)_

---

## 🚧 Funcionalidades Futuras (Roadmap)

### **Fase 2 - Funcionalidades Avançadas**
- [ ] Sistema de backup automático
- [ ] Relatórios com gráficos (Chart.js)
- [ ] API REST completa
- [ ] Sistema de notificações em tempo real
- [ ] Integração WhatsApp funcional
- [ ] Validação de OAB via API do CNF
- [ ] Biblioteca de mídia centralizada
- [ ] Sitemap dinâmico

### **Fase 3 - IA e Data Science**
- [ ] OCR para escaneamento de documentos
- [ ] Análise de dados com Machine Learning
- [ ] Predição de casos jurídicos
- [ ] Geração de contratos com IA
- [ ] Classificação automática de documentos
- [ ] Chatbot de atendimento

### **Fase 4 - Módulo de Contabilidade**
- [ ] Gestão financeira (contas a pagar/receber)
- [ ] Fluxo de caixa
- [ ] Emissão de notas fiscais
- [ ] Integração com SPED
- [ ] Folha de pagamento
- [ ] Relatórios contábeis

---

## 🤝 Contribuindo

Contribuições são bem-vindas! Para contribuir:

1. Faça um Fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/NovaFuncionalidade`)
3. Commit suas mudanças (`git commit -m 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/NovaFuncionalidade`)
5. Abra um Pull Request

---

## 📝 Changelog

### **Versão 1.0.0** (2026-01-19)
- ✅ Sistema completo de autenticação e recuperação de senha
- ✅ CRUD de usuários com permissões
- ✅ Sistema de perfil com upload de avatar
- ✅ Configurações completas do sistema
- ✅ Migração para PHPMailer
- ✅ Sistema de middlewares de segurança
- ✅ Proteção CSRF e XSS
- ✅ Logs de auditoria completos

---

## 📞 Suporte

Em caso de problemas:

1. Consulte o **[Guia de Instalação](INSTALACAO.md)**
2. Execute o **[Verificador de Instalação](check-install.php)**
3. Verifique os logs em `storage/logs/`
4. Ative `APP_DEBUG=true` no `.env` para ver erros detalhados

---

## 📄 Licença

Este projeto é proprietário e confidencial.

---

## 👨‍💻 Autor

Sistema desenvolvido para gestão profissional de escritórios de advocacia e contabilidade.

---

## 🎉 Agradecimentos

Desenvolvido com ❤️ usando as melhores práticas de desenvolvimento PHP moderno.

---

**Versão:** 1.0.0
**Data:** Janeiro 2026
**Status:** ✅ Produção
