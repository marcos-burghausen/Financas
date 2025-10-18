# 🏗️ Arquitetura Visual - MrFinancas v2.0

## 📐 Estrutura do Layout

```
┌─────────────────────────────────────────────────────────────────┐
│                     HEADER FIXO (64px)                          │
│  [☰] MrFinancas    │ Título da Página │  ☀️  🔔  👤             │
└─────────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────────┐
│          MONTH SELECTOR (56px)                                  │
│                 < Outubro > ou < Out.2024 >                     │
│                   [Hoje]                                         │
└─────────────────────────────────────────────────────────────────┘
┌──────────────────┬──────────────────────────────────────────────┐
│                  │                                              │
│  SIDEBAR (250px) │       MAIN CONTENT                          │
│                  │       (flex-grow)                           │
│  📊 Dashboard    │                                              │
│  💸 Despesas     │  ┌──────────────────────────────────────┐   │
│  💰 Receitas     │  │  [View Content]                      │   │
│  🏦 Contas       │  │                                      │   │
│  💳 Cartões      │  │  - Cards KPI                         │   │
│  ───────────────  │  │  - Tabela com dados                 │   │
│  📋 Categorias   │  │  - Gráficos                         │   │
│  🔔 Notificações │  │  - Dialogs                          │   │
│  ───────────────  │  │                                      │   │
│  👤 Perfil       │  │  - Responsive layout                │   │
│  🛡️  Admin       │  │  - Dark mode support                │   │
│  📊 Trader       │  └──────────────────────────────────────┘   │
│  ───────────────  │                                              │
│  👤 [Avatar]     │                                              │
│     [Nome]       │                                              │
│     [Type]       │                                              │
│  [Sair]          │                                              │
│                  │                                              │
└──────────────────┴──────────────────────────────────────────────┘
```

---

## 📱 Responsividade

### Desktop (>1024px)

```
┌─────────────────────────────────────┐
│      HEADER FIXO                    │
├─────────┬───────────────────────────┤
│ SIDEBAR │     MAIN CONTENT          │
│ (250px) │     (flex-grow)           │
│ visível │     - Muita espaço       │
│         │     - 4 colunas max      │
│         │     - Confortável        │
└─────────┴───────────────────────────┘
```

### Tablet (600-1024px)

```
┌─────────────────────────────────────┐
│      HEADER FIXO                    │
├─ ─ ─ ─ ─┬───────────────────────────┤
│ DRAWER  │     MAIN CONTENT          │
│ collap  │     - Espaço médio       │
│ (oculto)│     - 2-3 colunas        │
│ [☰]     │     - Overlay ao abrir   │
│         │                           │
└─────────┴───────────────────────────┘
```

### Mobile (<600px)

```
┌─────────────────┐
│   HEADER FIXO   │
├─ ─ ─ ─ ─ ─ ─ ─ ┤
│ MAIN CONTENT    │
│ (full width)    │
│ - 1 coluna      │
│ - Compacto      │
│ - Drawer [☰]    │
│   (overlay)     │
│                 │
└─────────────────┘
```

---

## 🎨 Componentes Principais

### **Header (64px)**

```
┌───────────────────────────────────────────────┐
│ [☰] MrFinancas  │  Página Atual  │ ☀️ 🔔 👤  │
└───────────────────────────────────────────────┘
```

**Componentes:**

- Menu toggle (mobile)
- Logo + título
- Theme toggle
- Notifications menu
- Profile menu

---

### **Month Selector (56px)**

```
┌───────────────────────────────────────────────┐
│     < Outubro >  ou  < Out.2024 >             │
│                [Hoje]                          │
└───────────────────────────────────────────────┘
```

**Componentes:**

- Botão anterior
- Mês/ano display
- Botão próximo
- Botão hoje

---

### **Sidebar (250px)**

```
┌────────────────────────────┐
│ 📊 Dashboard               │
│ 💸 Despesas                │
│ 💰 Receitas                │
│ 🏦 Contas                  │
│ 💳 Cartões                 │
├────────────────────────────┤
│ 📋 Categorias              │
│ 🔔 Notificações            │
├────────────────────────────┤
│ 👤 Perfil                  │
│ 🛡️  Painel Admin           │
│ 📊 Painel Trader           │
├────────────────────────────┤
│ [Avatar] [Nome]            │
│ [Type]   [Sair]            │
└────────────────────────────┘
```

---

### **Cards KPI**

```
┌─────────────────────────────┐
│ [Icon]  Total do Mês        │
│         R$ 15.000,00        │
│         ↑ +5.2%             │
└─────────────────────────────┘
```

**Variações:**

- Success (verde) - Receitas
- Error (vermelho) - Despesas
- Warning (amarelo) - Pendente
- Info (azul) - Recebidas/Pagas

---

### **Tabela de Dados**

```
┌──────────────────────────────────────────────┐
│ Descrição          │ Valor    │ Status       │
├──────────────────────────────────────────────┤
│ [Avatar] Salário   │ +5000    │ [Recebida]   │
│ [Avatar] Aluguel   │ -1500    │ [Paga]       │
│ [Avatar] Bonus     │ +800     │ [Pendente]   │
└──────────────────────────────────────────────┘
```

**Componentes:**

- Coluna descrição com avatar
- Coluna valor com formatação
- Coluna categoria em chip
- Coluna status em chip colorido
- Coluna ações com botões

---

### **Dialog Add/Edit**

```
┌─────────────────────────────┐
│ ■ Nova Receita       [✕]    │
├─────────────────────────────┤
│ Descrição                   │
│ [________________]          │
│ Categoria    │ Conta        │
│ [__________] │ [________]   │
│ Valor        │ Data         │
│ [__________] │ [________]   │
│ Status                      │
│ [________________]          │
│ Observação                  │
│ [________________]          │
│ [Cancelar]  [Adicionar]     │
└─────────────────────────────┘
```

---

### **Filtros**

```
┌────────────────────────────────────────────────────────┐
│ [Buscar]    │ [Status]   │ [Categoria]  │ [Limpar]     │
├────────────────────────────────────────────────────────┤
│ Mostrando X de Y resultados                            │
└────────────────────────────────────────────────────────┘
```

---

## 🎯 Fluxo de Dados

### Dashboard

```
Dashboard View
    ├─ MainLayout (header, sidebar)
    ├─ Header Section
    ├─ Cards KPI
    │   ├─ Total receitas
    │   ├─ Total despesas
    │   ├─ Média categorias
    │   └─ Últimas movimentações
    ├─ Charts (tendência)
    ├─ Transações recentes
    └─ Categorias populares
```

### Receitas/Despesas

```
ReceitasView / DespesasView
    ├─ MainLayout (header, sidebar)
    ├─ View Header
    ├─ Cards KPI (Total, Recebidas, Pendentes, Atrasadas)
    ├─ Filtros
    │   ├─ Busca
    │   ├─ Status
    │   ├─ Categoria
    │   └─ Limpar
    ├─ Tabela
    │   ├─ Descrição
    │   ├─ Categoria
    │   ├─ Valor
    │   ├─ Status
    │   └─ Ações (edit, delete)
    └─ Dialog (add/edit)
```

---

## 🎨 Paleta de Cores

### Cores Principais

```
┌──────────────────┐
│ Primary (Azul)   │  #2196F3
│ Used: Headers    │
└──────────────────┘

┌──────────────────┐
│ Success (Verde)  │  #4CAF50
│ Used: Receitas   │
└──────────────────┘

┌──────────────────┐
│ Error (Vermelho) │  #F44336
│ Used: Despesas   │
└──────────────────┘

┌──────────────────┐
│ Warning (Amarelo)│  #FFC107
│ Used: Pendente   │
└──────────────────┘

┌──────────────────┐
│ Info (Azul Claro)│  #00BCD4
│ Used: Info       │
└──────────────────┘
```

---

## 📊 Fluxo de Navegação

```
Home/Login
    ↓
Dashboard (default)
    ├─ Receitas
    │   ├─ Add Receita → Dialog
    │   ├─ Edit Receita → Dialog
    │   └─ Delete Receita
    ├─ Despesas
    │   ├─ Add Despesa → Dialog
    │   ├─ Edit Despesa → Dialog
    │   └─ Delete Despesa
    ├─ Contas
    ├─ Cartões
    ├─ Categorias
    ├─ Notificações
    │   └─ Menu Dropdown
    ├─ Perfil
    ├─ Admin Panel (se ADMIN/FULL)
    ├─ Trader Panel (se TRADER/FULL)
    └─ Logout
```

---

## 🔐 Controle de Acesso

```
Usuário Logado
    ├─ Tipo = USER
    │   └─ Ver: Dashboard, Receitas, Despesas, Contas, Cartões
    │
    ├─ Tipo = TRADER
    │   ├─ Ver: Dashboard, Receitas, Despesas, Contas, Cartões
    │   └─ Ver: Painel Trader
    │
    ├─ Tipo = USER_TRADER
    │   ├─ Ver: Dashboard, Receitas, Despesas, Contas, Cartões
    │   └─ Ver: Painel Trader
    │
    ├─ Tipo = ADMIN
    │   ├─ Ver: Dashboard, Receitas, Despesas, Contas, Cartões
    │   └─ Ver: Painel Admin
    │
    └─ Tipo = FULL
        ├─ Ver: Tudo
        ├─ Ver: Painel Admin
        └─ Ver: Painel Trader
```

---

## 📱 Breakpoints

```
XS  < 600px    │ Celular pequeno
SM  600-960px  │ Tablet
MD  960-1264px │ Desktop pequeno
LG  > 1264px   │ Desktop grande
```

---

## ⚡ Performance

```
Loading Time:
├─ HTML/CSS: ~0.5s
├─ JavaScript: ~1.5s
├─ Dados Mock: ~0s (local)
├─ API Real: ~0.5-1s (depende servidor)
└─ Total: ~2-3s
```

---

## 🎓 Componentes Reutilizáveis

```
MainLayout
├─ Header
│   ├─ Theme Toggle
│   ├─ Notifications Menu
│   └─ Profile Menu
├─ Month Selector
├─ Sidebar
│   ├─ Menu Items
│   └─ Profile Section
└─ Main Content (slot)

Cards KPI (reutilizável)
├─ Cor customizável
├─ Ícone customizável
├─ Valor customizável
└─ Variação customizável

Dialog Add/Edit (reutilizável)
├─ Título dinâmico
├─ Formulário customizável
├─ Validação customizável
└─ Actions customizável

Tabela (v-data-table)
├─ Headers customizáveis
├─ Dados customizáveis
├─ Filtros customizáveis
└─ Actions customizáveis
```

---

## 🚀 Escalabilidade

```
Estrutura permite adicionar:
├─ Novas views (mesma estrutura)
├─ Novos menu items
├─ Novas actions
├─ Novos filtros
├─ Novos gráficos
├─ Novas integrações
└─ Sem quebrar layout existente
```

---

**Versão**: 2.0
**Data**: Outubro 17, 2025
