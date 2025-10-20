# 🏗️ DIAGRAMA ARQUITETURAL - NAVEGAÇÃO DE MESES

## 📐 Arquitetura Geral

```
┌─────────────────────────────────────────────────────────────────┐
│                         APLICAÇÃO MR FINANÇA                     │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────────┐         ┌──────────────────────────────┐   │
│  │   USER STORE     │         │       VIEWS                  │   │
│  │   (Pinia)        │         │                              │   │
│  │                  │         │  ┌─────────────────────────┐ │   │
│  │  mesAno: Ref     │◄────────┼─►│  DashboardView ✨       │ │   │
│  │  setMesAno()     │         │  │  ReceitasView           │ │   │
│  │  getMesAno()     │         │  │  DespesasView           │ │   │
│  │                  │         │  └─────────────────────────┘ │   │
│  │  localStorage    │         │                              │   │
│  │  persistence     │         │  All views synced by        │   │
│  │                  │         │  userStore.mesAno          │   │
│  └──────────────────┘         │                              │   │
│          ▲                     └──────────────────────────────┘   │
│          │                                                        │
│          └────────────────────────────────────────────────────────┘
│                       watch() triggers reload
│
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔄 Fluxo de Navegação de Mês

```
┌──────────────┐
│ Usuário      │
│ Clica em ←   │
└──────┬───────┘
       │
       ▼
┌──────────────────────────┐
│ navigationMonth('prev')   │
│ ┌────────────────────┐   │
│ │ Parse mesAno       │   │
│ │ "2024-10" → Date   │   │
│ │ Subtract 1 month   │   │
│ │ "2024-09"          │   │
│ └────────────────────┘   │
└──────┬───────────────────┘
       │
       ▼
┌──────────────────────────┐
│ userStore.setMesAno()    │
│ ┌────────────────────┐   │
│ │ Update ref         │   │
│ │ mesAno = "2024-09" │   │
│ │ Save to localStorage   │
│ └────────────────────┘   │
└──────┬───────────────────┘
       │
       ▼
┌──────────────────────────┐
│ watch() detecta mudança  │
└──────┬───────────────────┘
       │
       ▼
┌──────────────────────────┐
│ loadDashboardData()      │
│ ┌────────────────────┐   │
│ │ Fetch from API     │   │
│ │ Filter by mesAno   │   │
│ │ Update summary     │   │
│ └────────────────────┘   │
└──────┬───────────────────┘
       │
       ▼
┌──────────────────────────┐
│ monthDisplay recomputes  │
│ Format: "setembro 2024"  │
└──────┬───────────────────┘
       │
       ▼
┌──────────────────────────┐
│ Vue re-renders           │
│ - Navigation buttons     │
│ - KPI Cards             │
│ - Charts                │
│ - Transactions          │
│ - Alerts                │
└──────────────────────────┘
```

---

## 🧩 Componentes e Dependências

```
DashboardView.vue
├── Template
│   ├── Navigation Block (NEW)
│   │   ├── Button ←
│   │   ├── Month Display
│   │   ├── Button →
│   │   └── Button "Mês Atual"
│   │
│   ├── KPI Cards Section
│   │   ├── Receitas
│   │   ├── Despesas
│   │   ├── Saldo
│   │   └── Pendências
│   │
│   ├── Charts Section
│   │   ├── Bar Chart
│   │   └── Pie Chart
│   │
│   ├── Recent Transactions
│   │
│   └── Dynamic Alerts
│
├── Script
│   ├── Imports
│   │   ├── dashboardService
│   │   ├── useUserStore()
│   │   ├── useToastStore()
│   │   ├── { watch } (NEW)
│   │   └── { computed, onMounted, ref }
│   │
│   ├── Refs
│   │   ├── loading
│   │   ├── counters
│   │   ├── summary
│   │   ├── chartOptions
│   │   ├── chartSeries
│   │   ├── recentTransactions
│   │   └── alerts
│   │
│   ├── Computed Properties
│   │   ├── monthDisplay (UPDATED)
│   │   ├── monthYearLabel
│   │   ├── currentMonthFormatted (NEW)
│   │   ├── mesAnoFormatted (NEW)
│   │   ├── formatCurrency
│   │   ├── receitasVariacao
│   │   ├── despesasVariacao
│   │   └── saldoVariacao
│   │
│   ├── Methods
│   │   ├── navigationMonth() (NEW)
│   │   ├── loadDashboardData()
│   │   └── generateAlerts()
│   │
│   ├── Watchers
│   │   └── watch(userStore.mesAno) (NEW)
│   │
│   └── Lifecycle
│       └── onMounted()
│
└── Styles
    └── CSS/SCSS
```

---

## 📊 Estado Gerenciado

```
LocalStorage
    ↓
    └─ mesAno: "2024-09"

Pinia UserStore (user.ts)
    ├─ mesAno: Ref<string> = "2024-09"
    ├─ setMesAno(value)
    │   ├─ mesAno.value = value
    │   └─ localStorage.setItem("mesAno", value)
    │
    └─ getMesAno(): string
        └─ return mesAno.value

All Views (Dashboard, Receitas, Despesas)
    └─ watch(() => userStore.mesAno)
        └─ reloadData()
```

---

## 🔗 Ligações Entre Componentes

```
┌────────────────────────────────────────────┐
│          ReceitasView                      │
│  ├─ watch(userStore.mesAno) ──┐           │
│  │                             │           │
│  └─ navigationMonth() ◄────┐   │           │
└────────────────────────────┼───┼───────────┘
                             │   │
                    ┌────────┼───┼────────┐
                    │        │   │        │
┌───────────────────┼────────┼───┼────────┼──────────────┐
│    DashboardView  │        │   │        │  (NOVA)      │
│                   │        │   │        │              │
│  navigationMonth()│        │   │        │              │
│      ────────────►│        │   │        │              │
│                   │        │   │        │              │
│  userStore.mesAno│        │   │        │              │
│      ◄──────────┼────────┘   │        │              │
│                   │            │        │              │
│  watch() ◄───────┴────────────┘        │              │
│      │                                  │              │
│      └──► loadDashboardData()          │              │
│      │                                  │              │
│      └──► monthDisplay recomputes      │              │
│      │                                  │              │
│      └──► UI re-renders                │              │
└───────────────────────────────────────┼──────────────┘
                                        │
                    ┌───────────────────┘
                    │
┌───────────────────┼──────────────────────┐
│      DespesasView │                      │
│  ├─ watch(userStore.mesAno) ──┐        │
│  │                             │        │
│  └─ navigationMonth() ◄────────┘        │
└────────────────────────────────────────┘
```

---

## 🧠 Estado e Props Flow

```
Parent: UserStore (Pinia)
│
│  mesAno = "2024-09"
│
├─► DashboardView
│   ├─ Consumes: userStore.getMesAno()
│   ├─ Computes: currentMonthFormatted
│   ├─ Computes: mesAnoFormatted
│   ├─ Calls: userStore.setMesAno()
│   ├─ Calls: loadDashboardData()
│   └─ Renders: Navigation UI
│
├─► ReceitasView
│   ├─ Consumes: userStore.getMesAno()
│   ├─ Calls: userStore.setMesAno()
│   └─ Watches: userStore.mesAno
│
└─► DespesasView
    ├─ Consumes: userStore.getMesAno()
    ├─ Calls: userStore.setMesAno()
    └─ Watches: userStore.mesAno

All mutually synchronized via shared userStore
```

---

## ⚙️ Sequência de Operações

### Operação 1: Navegação Anterior

```
Step 1: navigationMonth('prev')
Step 2: Parse mesAno "2024-10" → new Date("2024-10-01")
Step 3: setMonth(getMonth() - 1) → September
Step 4: Format to ISO "2024-09"
Step 5: userStore.setMesAno("2024-09")
Step 6: Update ref and localStorage
Step 7: watch() triggers → loadDashboardData()
Step 8: Fetch data filtered by "2024-09"
Step 9: monthDisplay recomputed: "setembro de 2024"
Step 10: Vue re-renders with new data
```

### Operação 2: Retornar ao Mês Atual

```
Step 1: navigationMonth('today')
Step 2: Get today's date
Step 3: Extract year-month: "2024-10"
Step 4: userStore.setMesAno("2024-10")
Step 5: loadDashboardData() (called explicitly)
Step 6: monthDisplay recomputed
Step 7: Vue re-renders with current month data
```

### Operação 3: Cross-View Synchronization

```
DashboardView             ReceitasView
─────────────             ────────────
mesAno: "2024-09"
                          watch(userStore.mesAno)
                          Detects change
                          loadReceitasData()
                          Re-renders with "2024-09"
```

---

## 🎯 Pontos de Contato (Integration Points)

```
1. UserStore Integration
   └─ setMesAno() / getMesAno()

2. Watch Integration
   └─ watch(() => userStore.mesAno)

3. LocalStorage Integration
   └─ localStorage.setItem/getItem("mesAno")

4. Date Manipulation
   └─ Date API (setMonth, getMonth, toISOString)

5. Data Loading
   └─ loadDashboardData()

6. UI Rendering
   └─ Vue 3 reactivity (@click, {{ }})
```

---

## 🚦 State Transitions

```
Initial State
    mesAno = "2024-10" (today)
    Dashboard shows October data

User clicks ←
    ↓
Previous Month
    mesAno = "2024-09"
    Dashboard shows September data

User clicks ←
    ↓
Previous Month
    mesAno = "2024-08"
    Dashboard shows August data

User clicks "Mês Atual"
    ↓
Current Month
    mesAno = "2024-10" (reset to today)
    Dashboard shows October data

User navigates to Receitas
    ↓
Receitas also shows October (synced!)
    ↓
User clicks ← in ReceitasView
    ↓
mesAno = "2024-09"
    ↓
User returns to Dashboard
    ↓
Dashboard also shows September (synced!)
```

---

## 🔐 Data Persistence Layer

```
┌─────────────────────────────────────┐
│    Browser LocalStorage             │
├─────────────────────────────────────┤
│                                     │
│  Key: "mesAno"                      │
│  Value: "2024-09"                   │
│  Scope: Same origin (localhost)     │
│  Lifetime: Until cleared by user    │
│                                     │
└─────────────────────────────────────┘
           ▲
           │
           └─ userStore.loadFromSession()
              (On app initialization)

           ┌─ userStore.setMesAno()
              (On navigation)
           │
           ▼
┌─────────────────────────────────────┐
│    Pinia Store (Runtime Memory)     │
├─────────────────────────────────────┤
│                                     │
│  mesAno: Ref<string>                │
│  Value: "2024-09"                   │
│  Scope: Component reactivity        │
│  Lifetime: Page lifetime            │
│                                     │
└─────────────────────────────────────┘
```

---

## 📱 Responsive Layout

```
DESKTOP (1200px+)
┌─────────────────────────────────────┐
│ [<] outubro de 2024 [>]  [Mês Atual]│
│      out/2024                       │
└─────────────────────────────────────┘

TABLET (768px - 1200px)
┌────────────────────────┐
│ [<] out/2024 [>]       │
│ [Mês Atual]            │
└────────────────────────┘

MOBILE (< 768px)
┌──────────────┐
│ [<] out [>]  │
│ [Mês Atual]  │
└──────────────┘
```

---

## 🎨 Component Hierarchy

```
DashboardView (Root)
│
├── Navigation Row (NEW)
│   ├── Button: ← (Previous)
│   ├── Display Group
│   │   ├── currentMonthFormatted: "outubro de 2024"
│   │   └── mesAnoFormatted: "out/2024"
│   ├── Button: → (Next)
│   └── Button: "Mês Atual"
│
├── KPI Cards Row
│   ├── Card: Receitas
│   │   └── Value: formatCurrency(summary.receitasMes)
│   ├── Card: Despesas
│   │   └── Value: formatCurrency(summary.despesasMes)
│   ├── Card: Saldo
│   │   └── Value: formatCurrency(summary.saldoAtual)
│   └── Card: Pendências
│       └── Value: summary.totalPendencias
│
├── Charts Row
│   ├── BarChart
│   │   └── Data: months vs Receitas/Despesas
│   └── PieChart
│       └── Data: Category distribution
│
├── Transactions Row
│   └── TransactionList
│       └── Filtered by mesAno
│
└── Alerts Row
    └── AlertsList
        └── Dynamic based on counters
```

---

**Esta arquitetura garante sincronização perfeita entre todas as views e persistência de dados entre sessões!** ✅
