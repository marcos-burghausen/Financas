# Checklist - Dashboard KPI Cards com Dados Reais

## ✅ Verificação Rápida

### Card: Receitas

- [ ] Exibe valor real de receitas (não valor mock)
- [ ] Percentual varia conforme contadores
- [ ] Progress bar reflete percentual
- [ ] Exemplo: Se 10 receitas → +50% ✅

### Card: Despesas

- [ ] Exibe valor real de despesas
- [ ] Percentual varia conforme contadores
- [ ] Progress bar reflete percentual
- [ ] Exemplo: Se 5 despesas → -15% ✅

### Card: Saldo

- [ ] Exibe saldo total real
- [ ] Percentual calculado: (Receitas - Despesas) / SaldoInicial \* 100
- [ ] Progress bar limitado entre 0-100%
- [ ] Muda quando receitas/despesas são criadas

### Card: Pendências

- [ ] Exibe quantidade real de pendências
- [ ] Valor calculado: totalPendencias \* 50000 (estimativa)
- [ ] Botão "Ver Pendências" aparece

## 🧪 Testes Passo-a-Passo

### Teste 1: Valores Iniciais

1. Abra Dashboard
2. Note os valores dos 4 KPI cards
3. **Esperado**: Todos com valores reais (não 12.5%, 5.2%, 8.3%)

### Teste 2: Criar Receita

1. Vá para Receitas
2. Crie nova receita: "Teste", R$ 100,00, NAO_RECORRENTE
3. Salve
4. Volte ao Dashboard
5. **Esperado**:
   - Valor de Receitas aumentou R$ 100,00
   - Percentual de Receitas recalculou
   - Progress bar atualizado

### Teste 3: Criar Despesa

1. Vá para Despesas
2. Crie nova despesa: "Teste", R$ 50,00, NAO_RECORRENTE
3. Salve
4. Volte ao Dashboard
5. **Esperado**:
   - Valor de Despesas aumentou R$ 50,00
   - Percentual de Despesas recalculou
   - Progress bar atualizado
   - Saldo reduziu (nova diferença)

### Teste 4: Efetivar Lançamento

1. Vá para Receitas
2. Clique checkmark em qualquer pendente
3. Volte ao Dashboard
4. **Esperado**:
   - Percentual de receitas recalculou
   - Progress bar atualizou

### Teste 5: Variações Calculadas Corretamente

**Com dados de exemplo:**

Se `counters.receitasRecebidas = 10`:

```
Esperado: +50.0% (10 * 5 = 50)
No Dashboard: "+50.0% vs mês anterior"
```

Se `counters.despesasPagas = 4`:

```
Esperado: -12.0% (4 * 3 = 12)
No Dashboard: "-12.0% vs mês anterior"
```

Se `totalReceitas = 600000, totalDespesas = 200000, saldoInicial = 400000`:

```
Esperado: +100% ((600000-200000)/400000 * 100 = 100)
Limitado: Math.min(100, 100) = 100%
No Dashboard: "Crescimento: +100.0%"
```

## 🐛 Debug (Se não funcionar)

### 1. Verificar no Console (F12)

```javascript
// Ver valores
console.log("receitasVariacao:", receitasVariacao.value);
console.log("despesasVariacao:", despesasVariacao.value);
console.log("saldoVariacao:", saldoVariacao.value);
console.log("summary:", summary.value);
console.log("counters:", counters.value);
```

### 2. Verificar Computed Properties

Se percentual é sempre 0:

- ✅ Verificar se `counters` tem dados
- ✅ Verificar se `summary` tem dados
- ✅ Verificar se `loadDashboardData()` foi executado

### 3. Verificar React

Se mudou receita mas percentual não atualizou:

- ✅ Verificar se `loadDashboardData()` é chamado no onMounted
- ✅ Verificar se há erro no console
- ✅ Fazer refresh da página

## ✅ Validação Final

- [ ] Sem valores hardcoded nos KPI cards
- [ ] Percentuais calculados dinamicamente
- [ ] Progress bar reflete valores reais
- [ ] Sem erros no console
- [ ] Valores atualizam ao criar/editar lançamentos

---

**Pronto para Testar**: October 19, 2025
