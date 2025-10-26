# Transações Recentes no Dashboard

## 📋 Resumo das Mudanças

Adicionado retorno de transações recentes (5 últimas) no endpoint `/dashboard/summary` do backend.

## 🔧 Alterações no Backend

### DashboardController.php

**Adicionado:** Queries para buscar as 5 últimas transações de cada tipo

```php
// ========== TRANSAÇÕES RECENTES (5 ÚLTIMAS DE CADA TIPO) ==========
$receitasRecentes = DB::table('lancamentos')
    ->where('user_id', $user->id)
    ->where('tipo_lancamento', 'RECEITA')
    ->select('id', 'descricao', 'valor', 'data_vencimento', 'categoria', 'status_lancamento', 'tipo_lancamento')
    ->orderBy('data_vencimento', 'desc')
    ->limit(5)
    ->get();

$despesasRecentes = DB::table('lancamentos')
    ->where('user_id', $user->id)
    ->where('tipo_lancamento', 'DESPESA')
    ->select('id', 'descricao', 'valor', 'data_vencimento', 'categoria', 'status_lancamento', 'tipo_lancamento')
    ->orderBy('data_vencimento', 'desc')
    ->limit(5)
    ->get();
```

**Retorno JSON:** Nova seção no response

```json
{
  "transacoes_recentes": {
    "receitas": [
      {
        "id": 123,
        "descricao": "Salário",
        "valor": 500000,
        "data_vencimento": "2025-10-26",
        "categoria": "Trabalho",
        "status_lancamento": "EFETIVADA",
        "tipo_lancamento": "RECEITA"
      }
    ],
    "despesas": [
      {
        "id": 456,
        "descricao": "Aluguel",
        "valor": 150000,
        "data_vencimento": "2025-10-25",
        "categoria": "Moradia",
        "status_lancamento": "EFETIVADA",
        "tipo_lancamento": "DESPESA"
      }
    ]
  }
}
```

## 📊 Response Completo

O endpoint `/dashboard/summary?mesAno=2025-10` agora retorna:

```json
{
  "success": true,
  "mesAno": "2025-10",
  "receitas": { ... },
  "despesas": { ... },
  "pendentes": { ... },
  "transacoes_recentes": {
    "receitas": [ /* 5 últimas receitas ordenadas por data (desc) */ ],
    "despesas": [ /* 5 últimas despesas ordenadas por data (desc) */ ]
  },
  "lancamentos": {
    "receitas": [ /* TODOS os lançamentos de receita, ordenados por data (desc) */ ],
    "despesas": [ /* TODOS os lançamentos de despesa, ordenados por data (desc) */ ]
  },
  "contas": { ... },
  "saldos": { ... }
}
```

## 🚀 Como Usar no Frontend

```typescript
// Após chamar loadDashboardData()
const receitasRecentes = response.data.transacoes_recentes.receitas;
const despesasRecentes = response.data.transacoes_recentes.despesas;

// Exibir em uma seção de "Transações Recentes"
receitasRecentes.forEach((transacao) => {
  console.log(`${transacao.descricao}: R$ ${transacao.valor / 100}`);
});
```

## ✨ Características

- ✅ Busca as **5 últimas transações** de cada tipo (receita/despesa)
- ✅ Ordenadas por `data_vencimento` descendente (mais recentes primeiro)
- ✅ Inclui informações: id, descricao, valor, data, categoria, status, tipo
- ✅ Respeitam permissões de usuário (`user_id`)
- ✅ Beneficiam-se do cache de 5 minutos do dashboard
- ✅ Sem filtro de mês/ano (mostra histórico completo)

## 📝 Notas

- As transações recentes não são filtradas por mês, mostram o histórico completo
- Ordenadas por data descendente (mais recentes vêm primeiro)
- Campo `valor` em centavos (dividir por 100 para exibição)
- Incluem transações PENDENTE e EFETIVADA
