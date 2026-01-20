# 📚 Bibliotecas Incluídas

Este diretório contém todas as bibliotecas de terceiros necessárias para o funcionamento do sistema.

## ✅ Não é necessário Composer!

Todas as dependências estão **incluídas diretamente** no projeto para facilitar a instalação.

---

## 📦 Bibliotecas

### PHPMailer v6.9.1
**Localização:** `src/libs/PHPMailer/`
**Licença:** LGPL 2.1
**Site:** https://github.com/PHPMailer/PHPMailer

Biblioteca para envio de emails via SMTP. Utilizada para:
- Envio de emails transacionais
- Confirmação de agendamentos
- Recuperação de senha
- Notificações administrativas

**Arquivos principais:**
- `PHPMailer.php` - Classe principal
- `SMTP.php` - Cliente SMTP
- `Exception.php` - Exceções customizadas
- `OAuth.php` - Autenticação OAuth (Gmail, etc)
- `POP3.php` - Cliente POP3
- `DSNConfigurator.php` - Configuração DSN

**Uso no projeto:**
```php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
```

### DotEnv (Custom)
**Localização:** `src/libs/DotEnv.php`
**Licença:** MIT (desenvolvido para este projeto)

Carregador simples de variáveis de ambiente do arquivo `.env`.
Substitui o pacote `vlucas/phpdotenv` para não depender do Composer.

**Recursos:**
- Carrega variáveis do `.env`
- Ignora comentários (`#`)
- Remove aspas automaticamente
- Métodos: `load()`, `get()`, `set()`, `has()`

**Uso no projeto:**
```php
require_once 'src/libs/DotEnv.php';

DotEnv::load(__DIR__);
$dbName = DotEnv::get('DB_DATABASE', 'default_db');
```

---

## 🔄 Atualizações

### Como atualizar PHPMailer:

1. Acesse: https://github.com/PHPMailer/PHPMailer/releases
2. Baixe a versão mais recente (stable)
3. Extraia os arquivos da pasta `src/` para `src/libs/PHPMailer/`
4. Teste o envio de emails no sistema
5. Commit as alterações

**Comando rápido (Linux/Mac):**
```bash
cd src/libs
curl -L https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.9.1.tar.gz -o phpmailer.tar.gz
tar -xzf phpmailer.tar.gz
rm -rf PHPMailer/*
mv PHPMailer-6.9.1/src/* PHPMailer/
rm -rf PHPMailer-6.9.1 phpmailer.tar.gz
```

---

## ℹ️ Por que não usar Composer?

**Vantagens de incluir as libs diretamente:**

1. ✅ **Instalação mais simples** - Não precisa instalar Composer
2. ✅ **Funciona em qualquer servidor** - Mesmo os mais básicos
3. ✅ **Sem conflitos de versão** - Versões testadas e estáveis
4. ✅ **Deploy facilitado** - Apenas copiar os arquivos
5. ✅ **Ideal para XAMPP** - Perfeito para desenvolvimento local
6. ✅ **Menor curva de aprendizado** - Não precisa conhecer Composer

**Quando usar Composer:**
- Projetos grandes com muitas dependências
- Necessidade de atualizar frequentemente
- Equipes familiarizadas com gerenciamento de dependências

---

## 📝 Licenças

- **PHPMailer:** LGPL 2.1 (livre para uso comercial)
- **DotEnv (custom):** MIT (livre para uso comercial)

---

## 🆘 Suporte

Em caso de problemas com as bibliotecas:

1. Verifique a versão do PHP (mínimo 7.4)
2. Consulte a documentação oficial do PHPMailer
3. Verifique os logs em `storage/logs/`

---

**Última atualização:** Janeiro 2026
