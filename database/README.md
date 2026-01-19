# 🗄️ Banco de Dados - Sistema de Gestão de Escritórios

## 📋 Estrutura do Banco de Dados

Este banco de dados foi projetado para gerenciar todas as funcionalidades do sistema de gestão para escritórios de advocacia e contabilidade.

### Tabelas Principais

| Tabela | Descrição | Registros Iniciais |
|--------|-----------|-------------------|
| `users` | Usuários administradores | 1 (admin padrão) |
| `categories` | Categorias de posts | 7 categorias |
| `posts` | Posts/Artigos do blog | - |
| `tags` | Tags para posts | 10 tags |
| `post_tags` | Relação posts ↔ tags | - |
| `lawyers` | Advogados do escritório | - |
| `appointments` | Agendamentos de consultas | - |
| `settings` | Configurações do sistema | 15 configs |
| `activity_logs` | Logs de auditoria | - |

## 🚀 Instalação

### Opção 1: Script Automático (Recomendado)

```bash
php database/install.php
```

### Opção 2: Via Navegador

Acesse: `http://localhost/database/install.php`

### Opção 3: Schema Completo

```bash
mysql -u root -p < database/schema.sql
```

### Opção 4: Migrations Individuais

```bash
mysql -u root -p escritorio_db < database/migrations/001_create_users_table.sql
mysql -u root -p escritorio_db < database/migrations/002_create_categories_table.sql
# ... e assim por diante
```

## 📊 Diagrama de Relacionamentos

```
users (1) ←──→ (N) posts
              ↓
categories (1) ←──→ (N) posts
              ↓
posts (N) ←──→ (N) tags [post_tags]

lawyers (independente)
appointments (independente)
settings (independente)
activity_logs (N) ←──→ (1) users
```

## 🔐 Credenciais Padrão

**⚠️ IMPORTANTE: Altere após o primeiro login!**

- **Email:** `admin@seuescritorio.com.br`
- **Senha:** `admin123`
- **URL Admin:** `http://localhost/admin`

## 📝 Migrations

As migrations estão organizadas numericamente para execução em ordem:

1. `001_create_users_table.sql` - Usuários
2. `002_create_categories_table.sql` - Categorias
3. `003_create_posts_table.sql` - Posts
4. `004_create_tags_tables.sql` - Tags e relacionamentos
5. `005_create_lawyers_table.sql` - Advogados
6. `006_create_appointments_table.sql` - Agendamentos
7. `007_create_settings_table.sql` - Configurações
8. `008_create_activity_logs_table.sql` - Logs

## 🔍 Views Criadas

### `v_posts_published`
Lista posts publicados com informações de categoria e autor.

```sql
SELECT * FROM v_posts_published;
```

### `v_appointments_stats`
Estatísticas rápidas de agendamentos.

```sql
SELECT * FROM v_appointments_stats;
```

### `v_lawyers_active`
Advogados ativos com informações formatadas.

```sql
SELECT * FROM v_lawyers_active;
```

## 🛠️ Stored Procedures

### `sp_clean_old_logs(dias)`
Limpa logs com mais de X dias.

```sql
CALL sp_clean_old_logs(90);
```

### `sp_get_dashboard_stats()`
Retorna estatísticas do dashboard.

```sql
CALL sp_get_dashboard_stats();
```

## 📚 Referências Legais

Este schema foi desenvolvido em conformidade com:

### Lei 8.906/94 - Estatuto da OAB
**Link:** https://www.planalto.gov.br/ccivil_03/leis/l8906.htm
- Artigos 28 a 34: Publicidade profissional

### Provimento 205/2021 - Publicidade OAB
**Link:** https://www.oab.org.br/leisnormas/legislacao/provimentos/205-2021
- Regras sobre anúncios e conteúdo

### Lei 13.709/18 - LGPD
**Link:** https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2018/lei/l13709.htm
- Proteção de dados pessoais
- Consentimento e privacidade

### Validação OAB
**Link:** https://cna.oab.org.br/
- Consulta de advogados
- Validação de números de OAB

## 🔧 Configurações Iniciais

Após instalação, o sistema vem com estas configurações:

| Chave | Valor Padrão |
|-------|-------------|
| `site_name` | Sistema de Gestão de Escritórios |
| `site_description` | Sistema de Gestão para Escritórios de Advocacia e Contabilidade |
| `site_email` | contato@seuescritorio.com.br |
| `oab_state` | RJ |
| `facebook_url` | (vazio) |

## 📈 Índices e Performance

### Índices Principais
- `posts`: Status + Data de publicação
- `appointments`: Data + Status
- `activity_logs`: Data de criação
- Fulltext search em `posts` (title, content, excerpt)

### Otimizações
- InnoDB para todas as tabelas
- UTF-8 (utf8mb4) para suporte completo de caracteres
- Foreign Keys com CASCADE/SET NULL apropriados
- Índices compostos para queries frequentes

## 🗑️ Backup e Manutenção

### Backup Manual

```bash
mysqldump -u root -p escritorio_db > backup_$(date +%Y%m%d).sql
```

### Restauração

```bash
mysql -u root -p escritorio_db < backup_20250131.sql
```

### Limpeza de Logs Antigos

```sql
CALL sp_clean_old_logs(90); -- Remove logs com mais de 90 dias
```

## ⚠️ Troubleshooting

### Erro: "Access denied"
- Verifique credenciais no `.env`
- Confirme se o usuário MySQL existe

### Erro: "Database already exists"
- Normal se executar novamente
- Use `DROP DATABASE escritorio_db;` para recriar

### Erro: "Table already exists"
- As migrations usam `IF NOT EXISTS`
- Seguro executar múltiplas vezes

### Performance lenta
- Execute `OPTIMIZE TABLE nome_tabela;`
- Verifique índices com `EXPLAIN SELECT ...;`

## 📞 Suporte

Para dúvidas sobre o banco de dados, consulte:
- Documentação técnica em `/docs`
- Issues no repositório
- Time de desenvolvimento

---

**Versão:** 1.0  
**Última Atualização:** 2025-10-31  
**Charset:** UTF-8 (utf8mb4)  
**Engine:** InnoDB