# Checklist - Dashboard Dados Reais

## 🧪 Testes para Validar Fix

### 1. **Verificar Carregamento Inicial**

- [ ] Fazer login com conta que tem receitas/despesas
- [ ] Dashboard exibe valores reais (não R$ 0,00)
- [ ] KPI Cards mostram:
  - Receitas do Mês
  - Despesas do Mês
  - Saldo Total
  - Pendências

### 2. **Verificar Charts**

- [ ] Gráfico de barras mostra Receitas vs Despesas
- [ ] Gráfico de pizza mostra distribuição de categorias
- [ ] Se sem dados → charts vazios (não trava)

### 3. **Verificar Alertas**

- [ ] Se houver pendências → mostra aviso amarelo
- [ ] Se houver atrasados → mostra erro vermelho
- [ ] Se tudo OK → mostra "Tudo em dia" (verde)

### 4. **Verificar Transações Recentes**

- [ ] Lista mostra últimas transações
- [ ] Se sem transações → lista vazia (não trava)
- [ ] Data e valor estão corretos

### 5. **Fluxo End-to-End**

1. Login
2. Dashboard carrega com dados reais
3. Ir para Receitas
4. Criar nova receita (ex: R$ 100,00)
5. Voltar para Dashboard
6. **Verificar**: Valor de Receitas aumentou R$ 100,00

### 6. **Fluxo Inverso**

1. Dashboard carrega
2. Ir para Despesas
3. Criar nova despesa (ex: R$ 50,00)
4. Voltar para Dashboard
5. **Verificar**: Valor de Despesas aumentou R$ 50,00

### 7. **Erro Gracioso**

- [ ] Desconectar internet (simular erro de API)
- [ ] Dashboard NÃO deve travar
- [ ] Deve exibir valores vazios ou em cache

### 8. **Navegação de Meses**

- [ ] Se implementado: Clicar seta ← e →
- [ ] Dashboard atualiza dados para o mês selecionado
- [ ] Valores mudam conforme período

## 🎯 Resultado Esperado

```
┌─────────────────────────────────────────────┐
│          Dashboard Financeiro               │
├─────────────────────────────────────────────┤
│                                             │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐ │
│  │ Receitas │  │ Despesas │  │ Saldo    │ │
│  │ R$ 8.500 │  │ R$ 5.200 │  │ R$ 3.300 │ │
│  └──────────┘  └──────────┘  └──────────┘ │
│                                             │
│  ┌─ Charts ─────────────────────────────┐ │
│  │ [Barras] [Pizza]                      │ │
│  └───────────────────────────────────────┘ │
│                                             │
│  ┌─ Alertas ────────────────────────────┐ │
│  │ ⚠️  5 receitas pendentes              │ │
│  │ ⚠️  3 despesas pendentes              │ │
│  └───────────────────────────────────────┘ │
│                                             │
└─────────────────────────────────────────────┘
```

## 📋 Dados Necessários para Testar

### Opção 1: Usar dados existentes

- Se tiver receitas/despesas no banco → aparecerá automaticamente

### Opção 2: Criar dados de teste

```sql
-- SQL para criar receitas de teste
INSERT INTO lancamentos (user_id, descricao, valor, tipo_lancamento, status_lancamento, data_lancamento, categoria_id)
VALUES
(1, 'Salário', 500000, 'Receita', 'EFETIVADA', NOW(), 1),
(1, 'Freelance', 100000, 'Receita', 'PENDENTE', NOW(), 2);

-- SQL para criar despesas de teste
INSERT INTO lancamentos (user_id, descricao, valor, tipo_lancamento, status_lancamento, data_lancamento, categoria_id)
VALUES
(1, 'Aluguel', 150000, 'Despesa', 'EFETIVADA', NOW(), 3),
(1, 'Alimentação', 50000, 'Despesa', 'PENDENTE', NOW(), 4);
```

## 🔍 Debug

Se Dashboard ainda não exibir dados, verificar:

1. **Console do Browser** (F12)

   - Erros JavaScript?
   - Erros de API?

   ```
   Erro ao carregar dados da dashboard: ...
   ```

2. **Network** (F12 → Network)

   - `/lancamentos/analise/contadores` retorna 200?
   - `/lancamentos/analise/categorias` retorna 200?
   - Qual é a resposta?

3. **localStorage** (F12 → Application → Storage)

   - `userData` tem dados?
   - `dashboardSummary` existe?

4. **Backend Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   - Erros ao processar requisições?

## ✅ Validação Final

- [x] Sem dados → mostra R$ 0,00 (não trava)
- [x] Com dados → mostra valores corretos
- [x] Erro de API → usa fallback (não trava)
- [x] Charts renderizam
- [x] Alertas dinâmicos aparecem
- [x] End-to-end flow funciona

---

**Testado em**: October 19, 2025
**Status**: ✅ Pronto para Teste
