# 🚀 Guia de Deploy - Sistema de Gestão de Escritórios

## 📋 Pré-requisitos de Produção

- Servidor Linux (Ubuntu 20.04+ recomendado)
- PHP 7.4+ com extensões necessárias
- MySQL 5.7+ ou MariaDB 10.3+
- Apache 2.4+ ou Nginx
- SSL/TLS certificado (Let's Encrypt recomendado)
- Domínio configurado

---

## 🔧 1. Preparação do Servidor

### 1.1. Atualizar Sistema

```bash
sudo apt update && sudo apt upgrade -y
```

### 1.2. Instalar Apache, PHP e MySQL

```bash
# Apache
sudo apt install apache2 -y

# PHP e extensões
sudo apt install php7.4 php7.4-cli php7.4-fpm php7.4-mysql \
  php7.4-mbstring php7.4-xml php7.4-curl php7.4-zip \
  php7.4-gd php7.4-intl php7.4-json -y

# MySQL
sudo apt install mysql-server -y
```

### 1.3. Configurar MySQL

```bash
sudo mysql_secure_installation
```

Responda:
- Remove anonymous users? **Yes**
- Disallow root login remotely? **Yes**
- Remove test database? **Yes**
- Reload privilege tables? **Yes**

---

## 📂 2. Deploy da Aplicação

### 2.1. Clonar/Copiar Projeto

```bash
cd /var/www/
sudo git clone [URL_DO_REPOSITORIO] escritorio
# OU
sudo rsync -avz /local/path/ /var/www/escritorio/
```

### 2.2. Configurar Permissões

```bash
cd /var/www/escritorio

# Define propriedade
sudo chown -R www-data:www-data .

# Define permissões
sudo find . -type d -exec chmod 755 {} \;
sudo find . -type f -exec chmod 644 {} \;

# Permissões especiais para uploads e logs
sudo chmod -R 775 public/uploads/
sudo chmod -R 775 storage/
```

### 2.3. Criar Diretórios Necessários

```bash
mkdir -p public/uploads/posts
mkdir -p public/uploads/lawyers
mkdir -p public/uploads/avatars
mkdir -p storage/logs
mkdir -p storage/cache

chmod -R 775 public/uploads
chmod -R 775 storage
```

---

## 🗄️ 3. Configurar Banco de Dados

### 3.1. Criar Banco e Usuário

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE escritorio_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'escritorio_user'@'localhost' IDENTIFIED BY 'SENHA_FORTE_AQUI';

GRANT ALL PRIVILEGES ON escritorio_prod.* TO 'escritorio_user'@'localhost';

FLUSH PRIVILEGES;

EXIT;
```

### 3.2. Importar Schema

```bash
mysql -u escritorio_user -p escritorio_prod < database/schema.sql
```

---

## ⚙️ 4. Configurar Aplicação

### 4.1. Criar arquivo .env

```bash
cp .env.example .env
nano .env
```

### 4.2. Editar configurações de produção

```env
# ==================== APLICAÇÃO ====================
APP_NAME=Sistema de Gestão de Escritórios
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com.br

# ==================== BANCO DE DADOS ====================
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=escritorio_prod
DB_USERNAME=escritorio_user
DB_PASSWORD=SENHA_FORTE_AQUI

# ==================== EMAIL ====================
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=senha-de-app-gmail
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=contato@seudominio.com.br
MAIL_FROM_NAME=Sistema de Gestão de Escritórios

# ==================== WHATSAPP (OPCIONAL) ====================
WHATSAPP_API_URL=
WHATSAPP_API_TOKEN=
WHATSAPP_PHONE=
```

**⚠️ IMPORTANTE:**
- Use `APP_DEBUG=false` em produção
- Use senhas fortes (mínimo 16 caracteres)
- Mantenha o arquivo `.env` fora do controle de versão

---

## 🌐 5. Configurar Apache

### 5.1. Criar VirtualHost

```bash
sudo nano /etc/apache2/sites-available/escritorio.conf
```

```apache
<VirtualHost *:80>
    ServerName seudominio.com.br
    ServerAlias www.seudominio.com.br
    ServerAdmin admin@seudominio.com.br

    DocumentRoot /var/www/escritorio

    <Directory /var/www/escritorio>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Logs
    ErrorLog ${APACHE_LOG_DIR}/escritorio-error.log
    CustomLog ${APACHE_LOG_DIR}/escritorio-access.log combined
</VirtualHost>
```

### 5.2. Habilitar Site e Módulos

```bash
# Habilita mod_rewrite
sudo a2enmod rewrite

# Habilita site
sudo a2ensite escritorio.conf

# Desabilita site padrão (opcional)
sudo a2dissite 000-default.conf

# Testa configuração
sudo apache2ctl configtest

# Reinicia Apache
sudo systemctl restart apache2
```

---

## 🔒 6. Configurar SSL com Let's Encrypt

### 6.1. Instalar Certbot

```bash
sudo apt install certbot python3-certbot-apache -y
```

### 6.2. Obter Certificado

```bash
sudo certbot --apache -d seudominio.com.br -d www.seudominio.com.br
```

Siga as instruções:
- Digite seu email
- Aceite os termos
- Escolha redirecionar HTTP para HTTPS: **Yes**

### 6.3. Auto-renovação

```bash
# Testa renovação
sudo certbot renew --dry-run

# Configura cron para renovação automática
sudo crontab -e

# Adicione esta linha:
0 3 * * * certbot renew --quiet
```

---

## 🔐 7. Segurança Adicional

### 7.1. Proteger arquivo .env

```bash
chmod 600 .env
```

### 7.2. Configurar Firewall

```bash
# Instala UFW
sudo apt install ufw -y

# Permite SSH
sudo ufw allow 22/tcp

# Permite HTTP e HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Ativa firewall
sudo ufw enable
```

### 7.3. Configurar Fail2Ban

```bash
# Instala Fail2Ban
sudo apt install fail2ban -y

# Cria configuração local
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local

# Edita configuração
sudo nano /etc/fail2ban/jail.local
```

Adicione:

```ini
[apache-auth]
enabled = true
port = http,https
logpath = /var/log/apache2/*error.log

[apache-badbots]
enabled = true
port = http,https
logpath = /var/log/apache2/*access.log
```

```bash
# Reinicia Fail2Ban
sudo systemctl restart fail2ban
```

### 7.4. Adicionar Headers de Segurança no Apache

Edite o VirtualHost:

```apache
<IfModule mod_headers.c>
    # Previne XSS
    Header set X-XSS-Protection "1; mode=block"

    # Previne clickjacking
    Header set X-Frame-Options "SAMEORIGIN"

    # Previne MIME sniffing
    Header set X-Content-Type-Options "nosniff"

    # Referrer Policy
    Header set Referrer-Policy "strict-origin-when-cross-origin"

    # Content Security Policy
    Header set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';"
</IfModule>
```

---

## 📧 8. Configurar Email (Gmail)

### 8.1. Criar Senha de App no Gmail

1. Acesse: https://myaccount.google.com/apppasswords
2. Selecione "App: Mail" e "Device: Other"
3. Digite: "Sistema Escritório"
4. Copie a senha gerada (16 caracteres)

### 8.2. Atualizar .env

```env
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx
```

---

## 🧪 9. Testar Instalação

### 9.1. Verificar Script de Instalação

Acesse: `https://seudominio.com.br/check-install.php`

Verifique todos os itens:
- ✅ PHP e extensões
- ✅ Banco de dados
- ✅ Permissões de diretórios
- ✅ Configurações

### 9.2. Fazer Primeiro Login

1. Acesse: `https://seudominio.com.br/admin/login`
2. Use credenciais padrão:
   - Email: `admin@escritorio.com.br`
   - Senha: `admin123`
3. **IMEDIATAMENTE** altere a senha em "Meu Perfil"

### 9.3. Configurar Sistema

1. Vá em **Configurações** → **Geral**
2. Preencha dados do escritório
3. Configure email (aba Email)
4. Teste email
5. Configure redes sociais

---

## 🔄 10. Backup e Manutenção

### 10.1. Backup Automático do Banco

```bash
# Cria script de backup
sudo nano /usr/local/bin/backup-escritorio.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/backups/escritorio"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="escritorio_prod"
DB_USER="escritorio_user"
DB_PASS="SENHA_AQUI"

mkdir -p $BACKUP_DIR

# Backup do banco
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup dos uploads
tar -czf $BACKUP_DIR/uploads_$DATE.tar.gz /var/www/escritorio/public/uploads/

# Remove backups com mais de 7 dias
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup concluído: $DATE"
```

```bash
# Torna executável
sudo chmod +x /usr/local/bin/backup-escritorio.sh

# Agenda backup diário às 3h
sudo crontab -e

# Adicione:
0 3 * * * /usr/local/bin/backup-escritorio.sh >> /var/log/backup-escritorio.log 2>&1
```

### 10.2. Monitoramento de Logs

```bash
# Logs do Apache
sudo tail -f /var/log/apache2/escritorio-error.log

# Logs da aplicação
sudo tail -f /var/www/escritorio/storage/logs/app.log
```

### 10.3. Limpeza de Cache

```bash
# Via navegador (admin)
Admin → Configurações → Botão "Limpar Cache"

# Via SSH
rm -rf /var/www/escritorio/storage/cache/*
```

---

## 🚨 11. Troubleshooting

### Problema: "500 Internal Server Error"

```bash
# Verifica logs
sudo tail -100 /var/log/apache2/escritorio-error.log

# Verifica permissões
ls -la /var/www/escritorio

# Verifica .htaccess
cat /var/www/escritorio/.htaccess

# Testa sintaxe Apache
sudo apache2ctl configtest
```

### Problema: "Database connection failed"

```bash
# Testa conexão MySQL
mysql -u escritorio_user -p escritorio_prod

# Verifica credenciais no .env
cat .env | grep DB_
```

### Problema: "Permissão negada em uploads"

```bash
# Corrige permissões
sudo chown -R www-data:www-data /var/www/escritorio/public/uploads
sudo chmod -R 775 /var/www/escritorio/public/uploads
```

---

## 📊 12. Performance e Otimização

### 12.1. Configurar OPcache (PHP)

```bash
sudo nano /etc/php/7.4/apache2/conf.d/10-opcache.ini
```

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
```

### 12.2. Habilitar Compressão

```bash
sudo a2enmod deflate
sudo systemctl restart apache2
```

### 12.3. Configurar Cache do Navegador

Adicione no .htaccess:

```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

---

## ✅ Checklist Final de Deploy

- [ ] Servidor Linux configurado e atualizado
- [ ] Apache/Nginx instalado e funcionando
- [ ] PHP 7.4+ com todas as extensões
- [ ] MySQL configurado com usuário dedicado
- [ ] Banco de dados criado e schema importado
- [ ] Projeto copiado para /var/www/escritorio
- [ ] Permissões corretas (755 para dirs, 644 para arquivos)
- [ ] Diretórios uploads e storage criados (775)
- [ ] Arquivo .env configurado para produção
- [ ] APP_DEBUG=false no .env
- [ ] VirtualHost configurado no Apache
- [ ] mod_rewrite habilitado
- [ ] SSL/TLS configurado (Let's Encrypt)
- [ ] Firewall (UFW) configurado
- [ ] Fail2Ban instalado e configurado
- [ ] Headers de segurança adicionados
- [ ] Email configurado e testado
- [ ] Senha do admin alterada
- [ ] Backup automático configurado
- [ ] Cron para renovação SSL configurado
- [ ] Sistema testado e funcionando

---

## 📞 Suporte

- **Documentação Completa:** `INSTALACAO.md`
- **Logs da Aplicação:** `storage/logs/`
- **Logs do Apache:** `/var/log/apache2/`

---

## 🎉 Deploy Concluído!

Seu sistema está agora em produção e pronto para uso!

**Próximos passos recomendados:**
1. ✅ Configurar backup off-site
2. ✅ Configurar monitoramento (Uptime Robot, etc)
3. ✅ Treinar usuários
4. ✅ Documentar processos internos
5. ✅ Estabelecer política de senhas
6. ✅ Configurar alertas por email

**Bom uso do sistema!** 🚀
