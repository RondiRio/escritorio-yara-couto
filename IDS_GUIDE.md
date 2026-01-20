# Intelligence Decision System (IDS) - Guia Completo

## Visão Geral

O Intelligence Decision System transforma o sistema de gestão em uma plataforma ativa de suporte à decisão, utilizando 5 camadas de inteligência para otimizar operações, recomendar advogados, analisar sentimentos, garantir compliance LGPD e prever receitas.

---

## Arquitetura das 5 Camadas

### **Camada 1: Background Check Service**
**Objetivo:** Verificar histórico de clientes antes de compromissos importantes

**Features:**
- Validação de CPF via API Serpro
- Busca de processos judiciais via API Jusbrasil
- Verificação de restrições financeiras
- Cálculo de score de risco (0-100)
- Modo fallback (simulação) quando APIs não configuradas

**Uso:**
```php
use Services\Intelligence\BackgroundCheckService;

$service = new BackgroundCheckService();
$result = $service->executeForAppointment($appointmentId, $cpf);

// Retorna:
// - cadastral_status: regular/irregular
// - legal_processes: array de processos encontrados
// - restrictions: restrições financeiras
// - risk_score: 0-100 (0 = sem riscos, 100 = alto risco)
// - recommendation: recomendação de ação
```

**Configuração (.env):**
```env
SERPRO_API_KEY=sua_chave_serpro
JUSBRASIL_API_KEY=sua_chave_jusbrasil
```

---

### **Camada 2: Lawyer Recommendation Service**
**Objetivo:** Recomendar o melhor advogado para cada tipo de caso

**Algoritmo de Scoring:**
- 40% - Match de especialização
- 30% - Taxa de sucesso histórica
- 15% - Experiência (casos vencidos)
- 10% - Performance recente no tipo de caso
- 5% - Avaliação de clientes

**Uso:**
```php
use Services\Intelligence\LawyerRecommendationService;

$service = new LawyerRecommendationService();
$result = $service->recommendLawyer('Direito Civil', $appointmentId);

// Retorna:
// - recommended: melhor advogado (score + breakdown)
// - alternatives: top 2-4 alternativas
// - full_ranking: ranking completo
```

**Atualização de Performance:**
```php
$service->updatePerformanceHistory(
    $lawyerId,
    'Direito Civil',
    $won = true,
    $durationDays = 45,
    $revenue = 2500.00
);
```

---

### **Camada 3: NLP Sentiment Analysis**
**Objetivo:** Analisar sentimento e urgência de mensagens de clientes

**Features:**
- Análise de sentimento (positive/negative/neutral/urgent)
- Score de urgência (1-10)
- Detecção de emoção (calm/worried/anxious/desperate)
- Identificação de área do direito
- Keywords extraction
- Modo fallback (análise por palavras-chave)

**Uso:**
```php
use Services\Intelligence\NLPSentimentService;

$service = new NLPSentimentService();
$result = $service->analyzeAppointmentMessage(
    $appointmentId,
    $message,
    $consultationType
);

// Retorna:
// - sentiment: positive/neutral/negative/urgent
// - urgency_score: 1-10
// - priority_level: 1-10
// - emotion: calm/worried/anxious/desperate/angry/hopeful
// - keywords: array de palavras-chave
// - legal_area_detected: área identificada
// - requires_immediate_attention: boolean
// - reasoning: explicação da análise
```

**Configuração (.env):**
```env
OPENAI_API_KEY=sk-...
```

**Análise em Lote:**
```php
$appointments = [
    ['id' => 1, 'message' => '...', 'type' => 'Civil'],
    ['id' => 2, 'message' => '...', 'type' => 'Trabalhista']
];

$result = $service->analyzeBatch($appointments);
// Retorna: total_processed, total_cost, results[]
```

---

### **Camada 4: LGPD Compliance Service**
**Objetivo:** Garantir conformidade com LGPD e proteção de dados

**Features:**
- Anonimização automática após período de retenção (24 meses padrão)
- Direito ao esquecimento (Right to be Forgotten)
- Portabilidade de dados (Data Portability)
- Direito de acesso (Right to Access)
- Mascaramento de dados sensíveis
- Log completo de todas as operações

**Uso - Anonimização Automática (CRON):**
```php
use Services\Intelligence\LGPDComplianceService;

$service = new LGPDComplianceService();
$result = $service->autoAnonymize();

// Anonimiza todos os registros com mais de 24 meses
```

**Uso - Direito ao Esquecimento:**
```php
$result = $service->rightToBeForgotten(
    'cliente@email.com',
    'Solicitação do titular via formulário'
);

// Anonimiza TODOS os dados deste cliente imediatamente
```

**Uso - Portabilidade de Dados:**
```php
$result = $service->rightToDataPortability('cliente@email.com');

// Retorna JSON com todos os dados do cliente
```

**Uso - Mascaramento:**
```php
$maskedEmail = $service->maskSensitiveData('joao@email.com', 'email');
// Retorna: j***o@email.com

$maskedCPF = $service->maskSensitiveData('123.456.789-00', 'cpf');
// Retorna: ***.456.789-**

$maskedName = $service->maskSensitiveData('João Silva Santos', 'name');
// Retorna: João S. S.
```

**Configuração (.env):**
```env
LGPD_RETENTION_MONTHS=24
```

**CRON Job Recomendado:**
```bash
# Executar diariamente às 3h da manhã
0 3 * * * php /path/to/escritorio/cron_lgpd_anonymize.php
```

---

### **Camada 5: Business Intelligence & Revenue Prediction**
**Objetivo:** Prever receitas e fornecer insights de negócio

**Stored Procedures Criadas:**

#### `sp_calculate_revenue_prediction`
Calcula predições de receita para os próximos N meses
```sql
CALL sp_calculate_revenue_prediction(6, 3);
-- Parâmetros:
-- 6 = próximos 6 meses
-- 3 = baseado em 3 meses de histórico
```

#### `sp_get_advanced_dashboard_stats`
Retorna estatísticas completas para o dashboard
```sql
CALL sp_get_advanced_dashboard_stats();
-- Retorna 6 result sets:
-- 1. Estatísticas do mês atual
-- 2. Estatísticas gerais
-- 3. Top 5 tipos de consulta
-- 4. Top 5 advogados por performance
-- 5. Uso de inteligência (últimos 30 dias)
-- 6. Predições de receita (próximos 3 meses)
```

#### `sp_get_conversion_funnel`
Analisa funil de conversão
```sql
CALL sp_get_conversion_funnel('2026-01-01', '2026-01-31');
-- Retorna taxas de conversão por estágio
```

#### `sp_analyze_appointment_trends`
Analisa tendências ao longo do tempo
```sql
CALL sp_analyze_appointment_trends(12);
-- Parâmetros: 12 = últimos 12 meses
```

**Uso em PHP:**
```php
// Predição de receita
$sql = "CALL sp_calculate_revenue_prediction(6, 3)";
$predictions = $db->select($sql);

// Dashboard stats
$sql = "CALL sp_get_advanced_dashboard_stats()";
$stats = $db->selectMultipleResultSets($sql);
```

---

## Instalação e Configuração

### 1. Executar Migrations

```bash
# Migration principal do IDS
mysql -u root -p escritorio_db < database/migrations/010_intelligence_decision_system.sql

# Migration de stored procedures
mysql -u root -p escritorio_db < database/migrations/011_revenue_prediction_procedures.sql
```

### 2. Configurar Variáveis de Ambiente

Edite o arquivo `.env`:

```env
# ========== APIs de Inteligência ==========

# Serpro - Validação de CPF
SERPRO_API_KEY=

# Jusbrasil - Busca de Processos
JUSBRASIL_API_KEY=

# OpenAI - Análise de Sentimento
OPENAI_API_KEY=sk-...

# ========== LGPD ==========
LGPD_RETENTION_MONTHS=24
```

### 3. Configurar CRON Jobs

Crie o arquivo `/path/to/escritorio/cron_lgpd_anonymize.php`:

```php
<?php
require_once __DIR__ . '/index.php';

use Services\Intelligence\LGPDComplianceService;

$service = new LGPDComplianceService();
$result = $service->autoAnonymize();

echo date('Y-m-d H:i:s') . " - LGPD Anonymization: " .
     $result['data']['total_records'] . " records processed\n";
```

Configure no crontab:
```bash
0 3 * * * php /var/www/html/escritorio-yara-couto/cron_lgpd_anonymize.php >> /var/log/lgpd_cron.log 2>&1
```

### 4. Atualizar Controllers

Exemplo de integração no **AppointmentController**:

```php
use Services\Intelligence\BackgroundCheckService;
use Services\Intelligence\NLPSentimentService;
use Services\Intelligence\LawyerRecommendationService;

public function store()
{
    // ... código existente de validação ...

    // Criar agendamento
    $appointmentId = $this->appointmentModel->create($data);

    if ($appointmentId) {
        // 1. Análise de Sentimento (se houver mensagem)
        if (!empty($data['message'])) {
            $nlpService = new NLPSentimentService();
            $nlpService->analyzeAppointmentMessage(
                $appointmentId,
                $data['message'],
                $data['type']
            );
        }

        // 2. Background Check (se houver CPF)
        if (!empty($data['cpf'])) {
            $bgService = new BackgroundCheckService();
            $bgService->executeForAppointment($appointmentId, $data['cpf']);
        }

        // 3. Recomendação de Advogado
        if (!empty($data['type'])) {
            $recommendService = new LawyerRecommendationService();
            $recommendService->recommendLawyer($data['type'], $appointmentId);
        }

        flash('success', 'Agendamento criado com sucesso!');
        redirect('/agendamentos/sucesso');
    }
}
```

---

## Custos Estimados de API

### OpenAI GPT-3.5-turbo
- Análise de sentimento: ~$0.001 - $0.003 por mensagem
- 1000 análises: ~$2 USD

### Serpro (CPF Consulta)
- Consulta de CPF: ~R$ 0,07 por consulta
- Pacotes disponíveis: https://serpro.gov.br

### Jusbrasil API
- Consulta de processos: Variável por plano
- Consulte: https://api.jusbrasil.com.br

**Modo Fallback:**
Todas as APIs possuem modo fallback (simulação/análise local) quando não configuradas, permitindo desenvolvimento e testes sem custos.

---

## Tabelas do IDS

### `intelligence_logs`
Registra todas as operações de inteligência

**Campos principais:**
- `service_type`: background_check, lawyer_recommendation, sentiment_analysis, lgpd_compliance, revenue_prediction
- `entity_type` e `entity_id`: entidade afetada
- `input_data` e `output_data`: JSON com dados
- `execution_time`: tempo de execução
- `status`: success/error/partial
- `cost`: custo da operação (APIs pagas)

### `lawyer_performance_history`
Histórico de performance dos advogados por tipo de caso

**Campos principais:**
- `lawyer_id`: ID do advogado
- `consultation_type`: tipo de caso
- `cases_handled` e `cases_won`: estatísticas
- `success_rate`: taxa de sucesso específica
- `avg_duration_days` e `avg_revenue`: médias

### `lgpd_anonymization_log`
Log de anonimizações realizadas

**Campos principais:**
- `table_name` e `record_id`: registro anonimizado
- `fields_anonymized`: campos afetados (JSON)
- `reason`: motivo da anonimização
- `original_created_at`: data original

### `revenue_predictions`
Predições de receita calculadas

**Campos principais:**
- `prediction_month`: mês sendo previsto
- `predicted_appointments`: agendamentos esperados
- `conversion_rate`: taxa de conversão
- `predicted_revenue`: receita prevista
- `confidence_level`: nível de confiança (%)
- `actual_revenue`: receita real (preenchido depois)

---

## Monitoramento e Logs

### Visualizar Logs de Inteligência

```sql
-- Últimas 50 operações
SELECT * FROM intelligence_logs ORDER BY created_at DESC LIMIT 50;

-- Operações com erro
SELECT * FROM intelligence_logs WHERE status = 'error' ORDER BY created_at DESC;

-- Custo total por serviço (últimos 30 dias)
SELECT
    service_type,
    COUNT(*) as operations,
    SUM(cost) as total_cost,
    AVG(execution_time) as avg_time
FROM intelligence_logs
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY service_type;
```

### Visualizar Anonimizações LGPD

```sql
-- Últimas anonimizações
SELECT * FROM lgpd_anonymization_log ORDER BY created_at DESC LIMIT 20;

-- Total por motivo
SELECT reason, COUNT(*) as total
FROM lgpd_anonymization_log
GROUP BY reason;
```

---

## Segurança e Boas Práticas

### ✅ Boas Práticas

1. **Nunca commite chaves de API no Git**
   - Use `.env` para todas as credenciais
   - Adicione `.env` no `.gitignore`

2. **Respeite os limites de rate das APIs**
   - OpenAI: 3500 requisições/min (tier 1)
   - Serpro: Varia por contrato
   - Jusbrasil: Varia por plano

3. **Monitore custos regularmente**
   ```sql
   SELECT SUM(cost) as monthly_cost
   FROM intelligence_logs
   WHERE MONTH(created_at) = MONTH(NOW());
   ```

4. **Configure CRON para LGPD**
   - Execute `autoAnonymize()` diariamente
   - Monitore logs de anonimização

5. **Teste modo fallback**
   - Sistema deve funcionar sem APIs configuradas
   - Análises locais são menos precisas mas gratuitas

### 🔒 Segurança LGPD

- Período de retenção configurável via `.env`
- Anonimização irreversível (não pode ser desfeita)
- Log completo de todas as operações
- Mascaramento de dados sensíveis em exibição
- Direitos do titular totalmente implementados

---

## FAQ

**Q: O sistema funciona sem configurar as APIs?**
A: Sim! Todas as camadas possuem modo fallback:
- Background Check: simulação de dados
- NLP: análise por palavras-chave
- As outras camadas não dependem de APIs externas

**Q: Como atualizo o período de retenção LGPD?**
A: Edite `.env` e adicione: `LGPD_RETENTION_MONTHS=36` (exemplo para 36 meses)

**Q: Posso usar GPT-4 ao invés de GPT-3.5?**
A: Sim, edite `NLPSentimentService.php` linha 18:
```php
private $model = 'gpt-4'; // Mais preciso, porém mais caro
```

**Q: Como testo as stored procedures?**
A: Conecte no MySQL e execute:
```sql
CALL sp_get_advanced_dashboard_stats();
```

**Q: O sistema calcula receita real automaticamente?**
A: Não, você deve atualizar manualmente a coluna `actual_revenue` na tabela `revenue_predictions` quando o mês finalizar, para comparar com a predição.

---

## Próximos Passos

1. ✅ Executar migrations
2. ✅ Configurar `.env` com chaves de API
3. ✅ Atualizar controllers para usar os serviços
4. ✅ Configurar CRON para LGPD
5. ✅ Criar dashboard administrativo para visualizar insights
6. ✅ Testar análises em modo fallback
7. ✅ Validar com APIs reais (quando disponíveis)

---

## Suporte

Para dúvidas sobre:
- **APIs Serpro**: https://servicos.serpro.gov.br/
- **APIs Jusbrasil**: suporte@jusbrasil.com.br
- **OpenAI API**: https://platform.openai.com/docs

Para issues do sistema: abra um issue no repositório do projeto.

---

**Desenvolvido com PHP + MySQL**
**Versão:** 1.0.0
**Data:** Janeiro 2026
