# ✅ ADIÇÃO DE LANÇAMENTOS PENDENTES AO DASHBOARD

## 📋 O QUE FOI ADICIONADO

Um novo objeto `pendentes` foi adicionado à resposta do `DashboardController`, contendo:

### 📊 **Novo Objeto: `pendentes`**

```json
{
  "pendentes": {
    "qtd_receitas_pendentes": 2,
    "qtd_despesas_pendentes": 3,
    "valor_total_pendente": 2500,
    "receitas": [
      {
        "id": 1,
        "descricao": "Freelance Website",
        "valor": 1500,
        "data_vencimento": "2025-10-30",
        "categoria": "Renda Extra",
        "status_lancamento": "PENDENTE",
        "tipo_lancamento": "RECEITA"
      },
      {
        "id": 2,
        "descricao": "Consultoria",
        "valor": 1000,
        "data_vencimento": "2025-10-25",
        "categoria": "Renda Extra",
        "status_lancamento": "PENDENTE",
        "tipo_lancamento": "RECEITA"
      }
    ],
    "despesas": [
      {
        "id": 5,
        "descricao": "Internet",
        "valor": 100,
        "data_vencimento": "2025-10-28",
        "categoria": "Utilidades",
        "status_lancamento": "PENDENTE",
        "tipo_lancamento": "DESPESA"
      },
      {
        "id": 6,
        "descricao": "Aluguel",
        "valor": 1200,
        "data_vencimento": "2025-10-01",
        "categoria": "Habitação",
        "status_lancamento": "PENDENTE",
        "tipo_lancamento": "DESPESA"
      },
      {
        "id": 7,
        "descricao": "Supermercado",
        "valor": 300,
        "data_vencimento": "2025-10-22",
        "categoria": "Alimentação",
        "status_lancamento": "PENDENTE",
        "tipo_lancamento": "DESPESA"
      }
    ]
  }
}
```

---

## 🔧 MUDANÇAS TÉCNICAS

### 1️⃣ **Buscar Receitas Pendentes**

```php
$receitasPendentes = DB::table('lancamentos')
    ->where('user_id', $user->id)
    ->where('tipo_lancamento', 'RECEITA')
    ->where('status_lancamento', 'PENDENTE')
    ->whereYear('data_vencimento', $ano)
    ->whereMonth('data_vencimento', $mes)
    ->select('id', 'descricao', 'valor', 'data_vencimento', 'categoria', 'status_lancamento', 'tipo_lancamento')
    ->orderBy('data_vencimento', 'desc')
    ->get();
```

### 2️⃣ **Buscar Despesas Pendentes**

```php
$despesasPendentes = DB::table('lancamentos')
    ->where('user_id', $user->id)
    ->where('tipo_lancamento', 'DESPESA')
    ->where('status_lancamento', 'PENDENTE')
    ->whereYear('data_vencimento', $ano)
    ->whereMonth('data_vencimento', $mes)
    ->select('id', 'descricao', 'valor', 'data_vencimento', 'categoria', 'status_lancamento', 'tipo_lancamento')
    ->orderBy('data_vencimento', 'desc')
    ->get();
```

### 3️⃣ **Calcular Total Pendente**

```php
$totalPendentes = $receitasPendentes->sum('valor') + $despesasPendentes->sum('valor');
```

---

## 📊 RESPOSTA COMPLETA AGORA RETORNA

```json
{
  "success": true,
  "mesAno": "2025-10",

  "receitas": {
    "qtd_total": 5,
    "qtd_efetivada": 3,
    "qtd_pendente": 2,
    "valor_total": 5000,
    "valor_recebido": 3000,
    "valor_pendente": 2000,
    "variacao": 20
  },

  "despesas": {
    "qtd_total": 8,
    "qtd_efetivada": 5,
    "qtd_pendente": 3,
    "valor_total": 3000,
    "valor_pago": 2500,
    "valor_pendente": 500,
    "variacao": -15
  },

  "pendentes": {
    "qtd_receitas_pendentes": 2,      // ✅ NOVO
    "qtd_despesas_pendentes": 3,      // ✅ NOVO
    "valor_total_pendente": 2500,     // ✅ NOVO
    "receitas": [...],                 // ✅ NOVO - Lista completa
    "despesas": [...]                  // ✅ NOVO - Lista completa
  },

  "contas": [...],
  "saldos": {...}
}
```

---

## 📈 CAMPOS DO OBJETO `pendentes`

| Campo                    | Tipo  | Descrição                   | Exemplo |
| ------------------------ | ----- | --------------------------- | ------- |
| `qtd_receitas_pendentes` | int   | Total de receitas pendentes | 2       |
| `qtd_despesas_pendentes` | int   | Total de despesas pendentes | 3       |
| `valor_total_pendente`   | float | Soma de todas as pendências | 2500    |
| `receitas`               | array | Lista de receitas pendentes | [...]   |
| `despesas`               | array | Lista de despesas pendentes | [...]   |

---

## 📋 CAMPOS RETORNADOS EM CADA LANÇAMENTO

```json
{
  "id": 1,
  "descricao": "Descricao do lancamento",
  "valor": 1500,
  "data_vencimento": "2025-10-30",
  "categoria": "Nome da categoria",
  "status_lancamento": "PENDENTE",
  "tipo_lancamento": "RECEITA"
}
```

---

## 🎯 CASOS DE USO

### 1️⃣ **Dashboard Card: Pendências**

Exibir na dashboard o resumo:

```
Pendências: 5 transações
Valor total: R$ 2.500,00
```

### 2️⃣ **Dialog/Modal: Ver Todas as Pendências**

Mostrar a lista completa de receitas e despesas pendentes:

```
Receitas Pendentes:
  - Freelance Website: R$ 1.500 (30/10)
  - Consultoria: R$ 1.000 (25/10)

Despesas Pendentes:
  - Internet: R$ 100 (28/10)
  - Aluguel: R$ 1.200 (01/10) ⚠️ ATRASADA
  - Supermercado: R$ 300 (22/10)
```

### 3️⃣ **Alert/Notificação: Pendências Vencidas**

Usar `data_vencimento` para alertar lançamentos atrasados:

```php
$hoje = today();
$lancamento->data_vencimento < $hoje ? 'ATRASADA' : 'PENDENTE'
```

---

## ✨ BENEFÍCIOS

✅ **Tudo em uma chamada:**

- Estatísticas de receitas
- Estatísticas de despesas
- Lista de pendências
- Dados das contas
- Saldos

❌ **Sem necessidade de:**

- Chamadas adicionais ao backend
- Lógica complexa no frontend
- Múltiplas requisições

✅ **Cache mantido:**

- 5 minutos de cache no servidor
- Reduz carga do banco de dados

---

## 🧪 EXEMPLO DE TESTE

```bash
# Teste com curl
curl "http://localhost/api/dashboard/summary?mesAno=2025-10" \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json"

# Resposta esperada:
{
  "success": true,
  "mesAno": "2025-10",
  "receitas": {...},
  "despesas": {...},
  "pendentes": {
    "qtd_receitas_pendentes": 2,
    "qtd_despesas_pendentes": 3,
    "valor_total_pendente": 2500,
    "receitas": [...],
    "despesas": [...]
  },
  "contas": [...],
  "saldos": {...}
}
```

---

## 📝 NOTAS IMPORTANTES

1. **Ordenação:** Lançamentos ordenados por `data_vencimento` DESC (mais recentes primeiro)
2. **Campos:** Incluídos apenas campos necessários para a dashboard
3. **Status:** Apenas `PENDENTE` é incluído
4. **Período:** Apenas lançamentos do mês/ano solicitado
5. **Segurança:** Filtrado por `user_id` do usuário autenticado

---

## 🔄 FLUXO DE DADOS

```
Dashboard Controller
    ↓
1. Agregações de RECEITAS (qtd, valores, variação)
2. Agregações de DESPESAS (qtd, valores, variação)
3. Lista de RECEITAS PENDENTES do mês
4. Lista de DESPESAS PENDENTES do mês
5. Total de PENDÊNCIAS
6. Contas e Saldos
    ↓
Retorna JSON com tudo acima
    ↓
Frontend (DashboardView.vue)
    ↓
- Renderiza KPI Cards
- Renderiza Gráficos
- Renderiza Tabela de Pendências
```

---

✅ **Implementação completa! Dashboard agora retorna tudo necessário.**
