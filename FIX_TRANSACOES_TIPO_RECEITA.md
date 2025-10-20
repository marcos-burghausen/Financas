# Fix - Transações Recentes Exibindo Receitas como Despesas

## Problema

Na seção "Transações Recentes" do Dashboard, as receitas cadastradas estavam sendo exibidas com ícone de despesa (🔴 X) e cor vermelha, quando deveriam aparecer com ícone de receita (🟢 +) e cor verde.

## Causa Raiz

### Incompatibilidade entre Backend e Frontend

**Backend retorna:**

```javascript
{
  "id": 1,
  "descricao": "Salário",
  "valor": 500000,
  "tipo_lancamento": "RECEITA",  // ← MAIÚSCULA
  "tipo": undefined
}
```

**Frontend esperava:**

```typescript
transaction.type === "receita"; // ← minúscula
```

### Dois Problemas Combinados

1. **Dashboard.service.ts** (linha 43)

   - Verificava `item.tipo === 'receita'`
   - Mas backend retorna `item.tipo_lancamento = 'RECEITA'` (maiúscula)
   - Resultado: Sempre retornava `'despesa'` como fallback

2. **DashboardView.vue** (linhas 238, 239, 252, 254)
   - Usava `transaction.type`
   - Mas o serviço retorna `transaction.tipo`
   - Resultado: `type` era `undefined`, então a cor/ícone era sempre vermelho

```
item.tipo_lancamento = 'RECEITA'
        ↓
Verifica: item.tipo === 'receita' ? false (RECEITA ≠ receita)
        ↓
Retorna como fallback: 'despesa'
        ↓
template usa: transaction.type (undefined)
        ↓
Cond: transaction.type === 'receita' ? false
        ↓
Exibe: ícone de despesa (vermelho)
```

## Solução

### 1. Corrigir Dashboard.service.ts - Linha 43-50

**Antes:**

```typescript
tipo: item.tipo === 'receita' || item.tipo === 'R' ? 'receita' : 'despesa',
```

**Depois:**

```typescript
// Converter para minúscula: backend retorna 'RECEITA', 'Receita', ou 'receita'
tipo: (item.tipo_lancamento && item.tipo_lancamento.toLowerCase() === 'receita') ||
      (item.tipo && (item.tipo === 'receita' || item.tipo === 'R')) ? 'receita' : 'despesa',
```

**O que faz:**

- ✅ Primeiro tenta `item.tipo_lancamento` (vem do backend)
- ✅ Converte para minúscula com `.toLowerCase()`
- ✅ Compara: 'RECEITA'.toLowerCase() === 'receita' ✅
- ✅ Como fallback, tenta `item.tipo` (para compatibilidade)
- ✅ Sempre retorna 'receita' ou 'despesa' (minúsculo)

### 2. Corrigir DashboardView.vue - Linhas 238-254

**Antes:**

```vue
<v-avatar :color="transaction.type === 'receita' ? 'success' : 'error'">
  <v-icon :icon="transaction.type === 'receita' ? 'mdi-cash-plus' : 'mdi-cash-remove'" />
</v-avatar>
... :class="{ 'text-success': transaction.type === 'receita', 'text-error':
transaction.type !== 'receita' }"
{{ transaction.type === "receita" ? "+" : "-"
}}{{ formatCurrency(transaction.valor) }}
```

**Depois:**

```vue
<v-avatar :color="transaction.tipo === 'receita' ? 'success' : 'error'">
  <v-icon :icon="transaction.tipo === 'receita' ? 'mdi-cash-plus' : 'mdi-cash-remove'" />
</v-avatar>
... :class="{ 'text-success': transaction.tipo === 'receita', 'text-error':
transaction.tipo !== 'receita' }"
{{ transaction.tipo === "receita" ? "+" : "-"
}}{{ formatCurrency(transaction.valor) }}
```

**O que faz:**

- ✅ Usa `transaction.tipo` (vem do serviço)
- ✅ Verifica se é 'receita' (minúsculo)
- ✅ Se yes → ícone verde (+) com cor success
- ✅ Se no → ícone vermelho (-) com cor error

## Fluxo Correto Agora

```
Backend API (/lancamentos)
  └─ tipo_lancamento: 'RECEITA'
  └─ tipo: undefined
      ↓
Dashboard.service.getRecentTransactions()
  └─ tipo_lancamento.toLowerCase() === 'receita' ? true
  └─ Retorna: { ..., tipo: 'receita' }
      ↓
DashboardView - recentTransactions array
  └─ [{ id: 1, tipo: 'receita', descricao: 'Salário', ... }]
      ↓
Template renderiza:
  └─ <v-avatar :color="transaction.tipo === 'receita' ? 'success' : 'error'">
  └─ :color = 'success'
  └─ <v-icon icon="mdi-cash-plus" />  ← Ícone +
      ↓
RESULTADO: ✅ Verde com ícone + (Receita correta)
```

## Resultado

| Tipo    | Antes         | Depois        |
| ------- | ------------- | ------------- |
| Receita | 🔴 X Vermelho | 🟢 + Verde    |
| Despesa | 🔴 X Vermelho | 🔴 - Vermelho |

### Exemplo Visual

**Antes (Errado):**

```
Transações Recentes
├─ 🔴 Salário          -R$ 5.000,00  ← ERRADO! Receita como despesa
├─ 🔴 Freelance        -R$ 1.000,00  ← ERRADO!
└─ 🔴 Aluguel          -R$ 1.500,00  ← OK, é despesa
```

**Depois (Correto):**

```
Transações Recentes
├─ 🟢 Salário          +R$ 5.000,00  ← CORRETO! Receita
├─ 🟢 Freelance        +R$ 1.000,00  ← CORRETO!
└─ 🔴 Aluguel          -R$ 1.500,00  ← CORRETO! Despesa
```

## Teste Agora

1. **Dashboard** → Seção "Transações Recentes"
2. **Verificar**:
   - Receitas aparecem com ✅ ícone verde (+)
   - Despesas aparecem com ❌ ícone vermelho (-)
   - Cores correspondem (receita verde, despesa vermelho)

## Arquivos Modificados

- `frontend/src/services/dashboard.service.ts` (linha 43-50)

  - ✅ Convertendo `tipo_lancamento` para minúsculo
  - ✅ Suporte a múltiplos formatos (RECEITA, Receita, receita)

- `frontend/src/views/DashboardView.vue` (linhas 238-254)
  - ✅ Usando `transaction.tipo` em vez de `transaction.type`
  - ✅ 3 correções (ícone, cor, símbolo)

## Relacionado

- **FIX_VALOR_FORMULARIO_EDICAO.md** - Conversão de centavos
- **FIX_EFETIVAR_RECEITA_CENTAVOS.md** - Erro ao efetivar
- **FIX_DASHBOARD_DADOS_REAIS.md** - Dashboard carregando dados

---

**Status**: ✅ Fix Completo
**Data**: October 19, 2025
**Impacto**: Médio - Apenas visual, mas importante para UX
**Causa**: Incompatibilidade Backend/Frontend (maiúscula vs minúscula)
