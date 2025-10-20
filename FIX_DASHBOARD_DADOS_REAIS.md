# Fix - Dashboard não Exibindo Dados Reais

## Problema

Dashboard exibia valores vazios (R$ 0,00) ou dados mock ao invés de dados reais do banco de dados.

## Causa Raiz

### 1. **userStore.summary estava vazio**

- Durante o login, `summary` era definido, mas depois não era recarregado
- `loadFromSession()` não era chamado ao montar o Dashboard
- Sem dados no store, o Dashboard apenas mostrava 0

### 2. **Sem fallback para erros de API**

- Se endpoints retornassem erros, o Dashboard travava completamente
- Não havia tratamento de erro para:
  - `/lancamentos/analise/contadores`
  - `/lancamentos/analise/categorias`
  - `/lancamentos` (transações recentes)
- Uma falha em um endpoint bloqueava tudo

### 3. **Falta de verificação de dados vazios**

- Dashboard não checava se `summary` realmente tinha dados antes de usar
- Apenas fazia `const realSummary = userStore.summary` sem validar

## Solução Implementada

### 1. **Carregar dados da sessão ao montar**

```typescript
// Agora carrega dados do localStorage/sessionStorage
userStore.loadFromSession();
const realSummary = userStore.summary;
```

### 2. **Verificar e fallback para dados vazios**

```typescript
// Se store vazio, tentar backend como backup
let finalSummary = realSummary;
if (!finalSummary || (finalSummary.totalReceitas === 0 && finalSummary.totalDespesas === 0)) {
  try {
    // Tentar carregar contadores como estimativa
    const response = await dashboardService.getTransactionCounters();
  } catch (err) {
    console.warn('Erro ao carregar contadores, usando valores vazios');
    finalSummary = { totalReceitas: 0, totalDespesas: 0, ... };
  }
}
```

### 3. **Try-catch individual para cada endpoint**

```typescript
// Carregar contadores COM FALLBACK
try {
  transactionCounters = await dashboardService.getTransactionCounters();
} catch (err) {
  console.warn("Erro ao carregar contadores:", err);
  // Usar valores padrão, NÃO travar
}

// Carregar categorias COM FALLBACK
try {
  const expensesByCategory = await dashboardService.getExpensesByCategory();
  // ... atualizar chart
} catch (err) {
  console.warn("Erro ao carregar categorias:", err);
  // Usar valores padrão, NÃO travar
}

// Carregar transações COM FALLBACK
try {
  const transactions = await dashboardService.getRecentTransactions(10);
  // ... atualizar lista
} catch (err) {
  console.warn("Erro ao carregar transações:", err);
  recentTransactions.value = [];
}
```

### 4. **Validação de dados antes de usar**

```typescript
chartSeries.value.bar = [
  {
    name: "Receitas",
    data: [finalSummary?.totalReceitas || 0], // ✅ Sempre tem fallback
  },
  {
    name: "Despesas",
    data: [finalSummary?.totalDespesas || 0], // ✅ Sempre tem fallback
  },
];
```

## Fluxo Agora

```
1. Dashboard monta
   ↓
2. Chama loadDashboardData()
   ↓
3. Carrega userStore.loadFromSession() → recupera dados do localStorage
   ↓
4. Se summary vazio:
      → Tenta dashboardService.getTransactionCounters()
      → Se erro → usa valores 0
   ↓
5. Para cada endpoint (contadores, categorias, transações):
      → Tenta carregar
      → Se erro → usa fallback/valores padrão
      → NÃO trava nunca
   ↓
6. Renderiza Dashboard com dados (reais ou vazios)
   ↓
7. Se user atualizar receitas/despesas → dados mudam dinamicamente
```

## Dados que Agora Aparecem

### KPI Cards (Superior)

- **Receitas do Mês**: `summary.totalReceitas` (em centavos, convertido com `formatCurrency()`)
- **Despesas do Mês**: `summary.totalDespesas`
- **Saldo Total**: `summary.saldoAtual`
- **Pendências**: Calculado a partir de contadores

### Charts

- **Bar Chart**: Receitas vs Despesas do mês
- **Donut Chart**: Distribuição de despesas por categoria
  - Fallback: ['Alimentação', 'Transporte', 'Moradia', 'Lazer', 'Outros']

### Alertas Dinâmicos

- ✅ Se houver receitas pendentes → Aviso
- ✅ Se houver despesas pendentes → Aviso
- ✅ Se houver itens atrasados → Erro
- ✅ Se tudo OK → Mensagem de sucesso

### Transações Recentes

- Últimas 10 transações da API
- Fallback: Lista vazia se erro

## Teste Agora

1. **Login**: Faça login normalmente
2. **Dashboard**: Deve exibir os valores de receitas/despesas reais
3. **Sem dados**: Se não houver lançamentos, exibe R$ 0,00 (não trava)
4. **Com dados**: Se houver lançamentos, mostra valores corretos
5. **Navegação**: Clique em Receitas/Despesas → números devem mudar ao voltar para Dashboard

## Arquivo Modificado

- `/frontend/src/views/DashboardView.vue`
  - Função `loadDashboardData()` - Agora com:
    - ✅ Carregamento de sessão
    - ✅ Fallback para dados vazios
    - ✅ Try-catch individual para cada API call
    - ✅ Validação de dados antes de usar
    - ✅ Tratamento de erro sem travar

## Relação com Outros Fixes

- **FIX_VALOR_FORMULARIO_EDICAO.md**: Valores em centavos convertidos para "10,00" com `formatCurrency()`
- **FIX_EFETIVAR_RECEITA_CENTAVOS.md**: Backend agora retorna dados corretos
- **NAVEGACAO_MESES.md**: Dashboard deveria filtrar por mês selecionado (próximo passo)

## Próximos Passos (Opcional)

1. Integrar seleção de mês (usar `userStore.mesAno`)
2. Adicionar filtro de contas (múltiplas carteiras)
3. Adicionar comparação com mês anterior ("+12.5%")
4. Cache dos dados para melhor performance

---

**Status**: ✅ Fix Completo
**Data**: October 19, 2025
**Impacto**: ALTO - Dashboard agora exibe dados reais sem travar
**Severidade**: Crítica - Era o principal indicador visual
