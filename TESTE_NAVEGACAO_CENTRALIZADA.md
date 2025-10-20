# 🧪 TESTE RÁPIDO - NAVEGAÇÃO CENTRALIZADA

## ⏱️ Tempo: 5 minutos

### Teste 1: Abrir Aplicação

1. Abrir http://localhost:4081
2. Fazer login

**Esperar:**

- ✅ MainLayout carregue
- ✅ Barra de navegação de meses aparece no topo
- ✅ Mostra mês atual (ex: "outubro de 2024")
- ✅ Botões ← e → visíveis
- ✅ Dashboard carrega

---

### Teste 2: Navegar com Botão Anterior (←)

1. Dashboard aberto
2. Clique em ← (botão anterior)

**Esperar:**

- ✅ Mês muda para "setembro de 2024"
- ✅ Dashboard recarrega com novos dados
- ✅ KPI cards atualizam valores
- ✅ Transações filtram pelo novo mês

---

### Teste 3: Navegar com Botão Próximo (→)

1. Em "setembro de 2024"
2. Clique em → (botão próximo)

**Esperar:**

- ✅ Mês volta para "outubro de 2024"
- ✅ Dashboard recarrega
- ✅ Dados voltam ao original

---

### Teste 4: Botão "Hoje" (aparece se não for mês atual)

1. Clique em ← para ir 2 meses atrás
2. Botão "Hoje" deve aparecer
3. Clique em "Hoje"

**Esperar:**

- ✅ Botão "Hoje" aparece quando não é mês atual
- ✅ Clique retorna ao mês atual
- ✅ Botão desaparece

---

### Teste 5: Sincronização Dashboard → Receitas

1. Dashboard em "julho de 2024"
2. Clique em "Receitas" no menu

**Esperar:**

- ✅ ReceitasView abre
- ✅ MainLayout ainda mostra "julho de 2024"
- ✅ Receitas filtram por julho

---

### Teste 6: Sincronização Receitas → Despesas

1. Em ReceitasView em "julho de 2024"
2. Clique em "Despesas" no menu

**Esperar:**

- ✅ DespesasView abre
- ✅ MainLayout ainda mostra "julho de 2024"
- ✅ Despesas filtram por julho

---

### Teste 7: Mudar Mês em Receitas

1. Em ReceitasView
2. Clique ← no MainLayout

**Esperar:**

- ✅ Mês muda (ex: "junho de 2024")
- ✅ ReceitasView recarrega com novo mês
- ✅ Dados filtram corretamente

---

### Teste 8: Mudar Mês em Despesas

1. Em DespesasView
2. Clique → no MainLayout

**Esperar:**

- ✅ Mês avança
- ✅ DespesasView recarrega com novo mês
- ✅ Dados sincronizados

---

### Teste 9: Navegação e Volta ao Dashboard

1. Dashboard em "junho de 2024"
2. Ir para Receitas
3. Voltar para Dashboard

**Esperar:**

- ✅ MainLayout mantém "junho de 2024"
- ✅ Dashboard também em "junho de 2024"
- ✅ Dados persistem corretamente

---

### Teste 10: Fechar e Reabrir

1. Dashboard em "junho de 2024"
2. Fechar aba completamente
3. Reabrir http://localhost:4081
4. Fazer login
5. Abrir Dashboard

**Esperar:**

- ✅ Dashboard abre em "junho de 2024" (localStorage)
- ✅ MainLayout mostra "junho de 2024"
- ✅ Dados estão corretos

---

## 📋 Checklist Rápido

- [ ] MainLayout mostra navegação no topo
- [ ] Botão ← navega para mês anterior
- [ ] Botão → navega para mês próximo
- [ ] Botão "Hoje" aparece quando não é mês atual
- [ ] Dashboard recarrega ao mudar mês
- [ ] ReceitasView sincroniza com MainLayout
- [ ] DespesasView sincroniza com MainLayout
- [ ] Dados filtram corretamente por mês
- [ ] localStorage persiste seleção
- [ ] Sem erros no console (F12)

---

## 🎯 Resultado

Se todos os testes passarem:

```
✅ REFATORAÇÃO FUNCIONA PERFEITAMENTE
✅ PRONTO PARA PRODUÇÃO
```

Se algo falhar:

```
❌ Abrir console (F12)
❌ Procurar erro vermelho
❌ Verificar network requests
❌ Reportar com screenshot
```

---

## 📸 Visual Esperado

```
┌──────────────────────────────────────────────┐
│ MR FINANÇA   [Menu] [Notif]  [User]          │
├──────────────────────────────────────────────┤
│  [<] outubro de 2024 [>]  Hoje (hidden)      │  ← Nova barra
├──────────────────────────────────────────────┤
│                                              │
│ ┌─────────────────────────────────────────┐  │
│ │ Dashboard                               │  │
│ │                                         │  │
│ │ ┌──────────┬──────────┬──────────┐      │  │
│ │ │ Receitas │ Despesas │ Saldo    │      │  │
│ │ │ R$ 5k    │ R$ 2k    │ R$ 3k    │      │  │
│ │ └──────────┴──────────┴──────────┘      │  │
│ │                                         │  │
│ └─────────────────────────────────────────┘  │
│                                              │
└──────────────────────────────────────────────┘
```

---

**Bom teste! 🚀**

Qualquer dúvida, abra o console (F12) e procure por erros.
