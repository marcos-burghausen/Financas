# 🧪 TESTE DE NAVEGAÇÃO DE MESES - DASHBOARD

## 📋 Plano de Testes

### Cenário 1: Abrir Dashboard (Deve Mostrar Mês Atual)

**Passos:**

1. Abrir browser em http://localhost:4081
2. Fazer login com credenciais
3. Navegar para Dashboard

**Resultado Esperado:**

```
┌─────────────────────────────────────────────────────┐
│              DASHBOARD - MÊS ATUAL                  │
├─────────────────────────────────────────────────────┤
│                                                     │
│  [<] outubro de 2024    [>] [Mês Atual]            │
│        out/2024                                     │
│                                                     │
│  ┌─────────────┬─────────────┬─────────────┐       │
│  │  Receitas   │  Despesas   │    Saldo    │       │
│  │ R$ 5.000,00 │ R$ 2.500,00 │ R$ 2.500,00 │       │
│  │   +5.2%     │   -2.1%     │   +8.3%     │       │
│  └─────────────┴─────────────┴─────────────┘       │
│                                                     │
└─────────────────────────────────────────────────────┘
```

✅ **Verificações:**

- [ ] Mês exibido é "outubro de 2024" (ou mês atual)
- [ ] Sub-label mostra "out/2024"
- [ ] Botões ← e → estão visíveis
- [ ] Botão "Mês Atual" está visível
- [ ] KPI cards mostram valores

---

### Cenário 2: Clicar em Botão Anterior (←)

**Passos:**

1. Com Dashboard aberta em "outubro de 2024"
2. Clicar no botão ← (anterior)

**Resultado Esperado:**

```
Antes:  [<] outubro de 2024    [>] [Mês Atual]
             out/2024

Depois: [<] setembro de 2024   [>] [Mês Atual]
             set/2024
```

✅ **Verificações:**

- [ ] Mês mudou para "setembro de 2024"
- [ ] Sub-label mudou para "set/2024"
- [ ] Dashboard não ficou em loading por muito tempo
- [ ] KPI cards atualizaram valores (se houver dados em set/2024)
- [ ] Transações recentes mostram apenas set/2024

---

### Cenário 3: Clicar em Botão Próximo (→)

**Passos:**

1. Com Dashboard em "setembro de 2024" (do teste anterior)
2. Clicar no botão → (próximo)

**Resultado Esperado:**

```
Antes:  [<] setembro de 2024   [>] [Mês Atual]
             set/2024

Depois: [<] outubro de 2024     [>] [Mês Atual]
             out/2024
```

✅ **Verificações:**

- [ ] Mês voltou para "outubro de 2024"
- [ ] Sub-label voltou para "out/2024"
- [ ] Valores voltaram ao estado anterior

---

### Cenário 4: Múltiplas Navegações (Anterior Vários Meses)

**Passos:**

1. Com Dashboard em "outubro de 2024"
2. Clicar ← 3 vezes

**Resultado Esperado:**

```
Oct → Sep → Aug → Jul
```

✅ **Verificações:**

- [ ] Navegou com sucesso: out → set → ago → jul
- [ ] Sem erros no console
- [ ] Dados atualizam corretamente cada mês

---

### Cenário 5: Clicar em "Mês Atual"

**Passos:**

1. Com Dashboard em "julho de 2024"
2. Clicar no botão "Mês Atual"

**Resultado Esperado:**

```
Antes:  [<] julho de 2024       [>] [Mês Atual]
             jul/2024

Depois: [<] outubro de 2024     [>] [Mês Atual]  ← retornou
             out/2024
```

✅ **Verificações:**

- [ ] Retornou ao mês corrente
- [ ] Botão "Mês Atual" funcionou corretamente
- [ ] Dados voltaram aos valores atuais

---

### Cenário 6: Sincronização com ReceitasView

**Passos:**

1. Dashboard mostrando "julho de 2024"
2. Clicar em "Receitas" no menu lateral
3. Verificar o mês em ReceitasView

**Resultado Esperado:**

```
Dashboard: [<] julho de 2024    [>]
             jul/2024

ReceitasView: [<] julho de 2024  [>]  ← MESMO MÊS!
               jul/2024
```

✅ **Verificações:**

- [ ] ReceitasView também mostra "julho de 2024"
- [ ] Mês foi sincronizado automaticamente
- [ ] Sem necessidade de re-selecionar

---

### Cenário 7: Sincronização com DespesasView

**Passos:**

1. ReceitasView mostrando "julho de 2024"
2. Clicar em "Despesas" no menu lateral
3. Verificar o mês em DespesasView

**Resultado Esperado:**

```
ReceitasView: [<] julho de 2024  [>]

DespesasView: [<] julho de 2024  [>]  ← MESMO MÊS!
```

✅ **Verificações:**

- [ ] DespesasView também mostra "julho de 2024"
- [ ] Sincronização através de todas as views

---

### Cenário 8: Persistência (Fechar e Reabrir)

**Passos:**

1. Dashboard mostrando "julho de 2024"
2. Abrir console do browser (F12)
3. Verificar localStorage
4. Fechar aba
5. Reabrir browser em http://localhost:4081
6. Fazer login
7. Ir para Dashboard

**Resultado Esperado:**

```
Antes de fechar: Dashboard → julho de 2024 → F12 (Console)
  localStorage: mesAno = "2024-07"

Depois de reabrir: Dashboard → ainda mostra julho de 2024
```

✅ **Verificações:**

- [ ] localStorage contém "mesAno": "2024-07"
- [ ] Ao reabrir, mantém mês anterior
- [ ] Não volta ao mês atual automaticamente

---

### Cenário 9: Dados Atualizam Corretamente

**Passos:**

1. Dashboard em "setembro de 2024"
2. Observar valores de:
   - KPI Cards (Receitas, Despesas, Saldo)
   - Gráfico de Barras
   - Gráfico de Pizza (Categorias)
   - Transações Recentes
   - Alertas

**Resultado Esperado:**

```
Quando muda de setembro para agosto:

ANTES (setembro):
  KPI: R$ 5.000,00 (Receitas)
  Transações: [Sep 15] Depósito | [Sep 10] Aluguel | [Sep 05] Compras

DEPOIS (agosto):
  KPI: R$ 4.500,00 (Receitas) - mudou!
  Transações: [Aug 28] Salário | [Aug 15] Água | [Aug 10] Mercado
```

✅ **Verificações:**

- [ ] KPI Cards mostram valores corretos do mês
- [ ] Gráfico de Barras atualiza dados
- [ ] Gráfico de Pizza atualiza categorias
- [ ] Transações recentes filtraram pelo mês
- [ ] Alertas dinâmicos se ajustam

---

### Cenário 10: Sem Erros no Console

**Passos:**

1. Abrir DevTools (F12)
2. Ir para aba "Console"
3. Navegar entre meses vários vezes
4. Mudar para ReceitasView/DespesasView
5. Voltar para Dashboard

**Resultado Esperado:**

```
Console: sem erros vermelhos ❌
Apenas logs informativos (amarelos) ⚠️
```

✅ **Verificações:**

- [ ] Sem erros de JavaScript
- [ ] Sem erros de network (404, 500)
- [ ] Sem avisos críticos

---

## 📊 Matriz de Testes

| #   | Cenário           | Entrada          | Saída Esperada           | Status |
| --- | ----------------- | ---------------- | ------------------------ | ------ |
| 1   | Dashboard Inicial | Abrir Dashboard  | Mês atual                | ⏳     |
| 2   | Clique ←          | Clique anterior  | Mês - 1                  | ⏳     |
| 3   | Clique →          | Clique próximo   | Mês + 1                  | ⏳     |
| 4   | Múltiplos ←       | 3 cliques ←      | Mês - 3                  | ⏳     |
| 5   | "Mês Atual"       | Clique botão     | Retorna hoje             | ⏳     |
| 6   | Sync Receitas     | Ir para Receitas | Mesmo mês                | ⏳     |
| 7   | Sync Despesas     | Ir para Despesas | Mesmo mês                | ⏳     |
| 8   | Persistência      | Reabrir browser  | Mesmo mês                | ⏳     |
| 9   | Dados Atualizam   | Mudar mês        | KPI/Gráficos/Trans mudam | ⏳     |
| 10  | Sem Erros         | Navegar          | Console limpo            | ⏳     |

---

## 🎬 Guia de Reprodução Rápida

### Se tudo der certo (Happy Path):

```
1. Abrir Dashboard
2. Clique ← (vê setembro)
3. Clique → (volta outubro)
4. Clique "Mês Atual" (confirma outubro)
5. Abrir Receitas (vê outubro também)
6. Fechar browser, reabrir
7. Dashboard ainda em outubro ✅
```

### Se houver erro:

```
1. Abrir Console (F12)
2. Procurar erro vermelho
3. Verificar se é relacionado a:
   - navigationMonth não definido?
   - userStore.mesAno undefined?
   - loadDashboardData erro?
   - Watch não dispara?
```

---

## 📝 Notas

- **Timezone**: Testes consideram timezone local
- **Dados**: Resultados dependem de dados reais no banco
- **Meses Futuros**: Pode navegar além do mês atual se houver dados
- **Meses Passados**: Pode navegar para trás indefinidamente

---

## ✅ Assinatura de Teste

```
Data: _______/_______/_______
Executor: ____________________
Status: [ ] PASSOU  [ ] FALHOU
Observações:
__________________________________
__________________________________
__________________________________
```
