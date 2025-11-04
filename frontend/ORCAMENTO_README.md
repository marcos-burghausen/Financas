# 📊 Funcionalidade de Orçamento - MrFinanças

## 📋 Visão Geral

A funcionalidade de orçamento permite aos usuários planejar e acompanhar seus gastos por categoria, oferecendo controle financeiro detalhado e visualizações intuitivas.

## ✨ Funcionalidades Implementadas

### 🎯 Principais Recursos

- **Criação de Orçamentos**: Definir orçamentos mensais por categoria
- **Acompanhamento em Tempo Real**: Visualizar progresso de gastos vs orçamento
- **Navegação por Mês**: Visualizar orçamentos de diferentes períodos
- **Alertas Visuais**: Indicadores de status (normal, alerta, excedido)
- **Detalhamento por Categoria**: Ver transações específicas de cada categoria
- **CRUD Completo**: Criar, visualizar, editar e excluir orçamentos

### 📊 Visualizações

1. **Cards de Resumo**:

   - Orçamento Total
   - Total Gasto
   - Saldo Restante
   - Meta de Economia

2. **Gráfico de Barras**:

   - Comparativo Orçado vs Gasto por categoria

3. **Cards por Categoria**:
   - Progresso visual com barras
   - Status colorido (verde/amarelo/vermelho)
   - Valores detalhados (orçado, gasto, restante)

## 🏗️ Arquitetura Implementada

### Frontend

```
frontend/src/
├── views/orcamento/
│   └── OrcamentoView.vue       # Componente principal
├── store/
│   └── budget.ts               # Store Pinia para orçamentos
├── types/
│   └── budget.types.ts         # Tipagens TypeScript
└── router/
    └── index.ts                # Rota /orcamento adicionada
```

### Componente Principal (`OrcamentoView.vue`)

**Características:**

- Componente Vue 3 com Composition API
- Integração completa com Vuetify 3
- Responsive design (mobile-first)
- Dados hardcoded para prototipação
- Gráficos com ApexCharts

**Seções:**

1. Header com botão "Novo Orçamento"
2. Navegação de mês
3. Cards de resumo (4 KPIs principais)
4. Gráfico de progresso
5. Grid de cards por categoria
6. Dialogs para criar/editar/visualizar detalhes

### Store (`budget.ts`)

**Responsabilidades:**

- Gerenciamento de estado dos orçamentos
- Cálculos automáticos de totais e percentuais
- Persistência em localStorage
- Funções utilitárias (CRUD)

**Principais métodos:**

```typescript
// Estado
budgetData: BudgetData
summary: ComputedRef<BudgetSummary>

// Actions
setBudgets(budgets: Budget[])
addBudget(budget: Budget)
updateBudget(id: number, data: Partial<Budget>)
removeBudget(id: number)
updateBudgetSpent(categoria: string, valor: number)
```

### Tipagens (`budget.types.ts`)

**Interfaces principais:**

- `Budget`: Estrutura principal do orçamento
- `BudgetData`: Dados agregados do store
- `BudgetTransaction`: Transações da categoria
- `BudgetFormData`: Dados do formulário

## 🎨 Design System

### Cores e Status

| Status   | Cor                 | Condição             |
| -------- | ------------------- | -------------------- |
| Normal   | Verde (`success`)   | < 80% do orçamento   |
| Alerta   | Amarelo (`warning`) | 80-100% do orçamento |
| Excedido | Vermelho (`error`)  | > 100% do orçamento  |

### Ícones por Categoria

| Categoria     | Ícone                 |
| ------------- | --------------------- |
| Alimentação   | `mdi-food`            |
| Transporte    | `mdi-car`             |
| Saúde         | `mdi-medical-bag`     |
| Educação      | `mdi-school`          |
| Lazer         | `mdi-gamepad-variant` |
| Moradia       | `mdi-home`            |
| Vestuário     | `mdi-tshirt-crew`     |
| Utilidades    | `mdi-tools`           |
| Investimentos | `mdi-trending-up`     |
| Outros        | `mdi-dots-horizontal` |

## 📱 Responsividade

### Breakpoints

- **Mobile** (< 600px): Layout em coluna única
- **Tablet** (600px - 960px): 2 colunas
- **Desktop** (> 960px): 3+ colunas

### Adaptações Mobile

- Cards empilhados verticalmente
- Navegação de mês simplificada
- Botões com tamanhos menores
- Texto e espaçamentos otimizados

## 🔧 Dados Mock

### Categorias Pré-definidas

```typescript
const mockBudgets = [
  {
    categoria: "Alimentação", // R$ 800 orçado, R$ 650 gasto (81.25%)
    categoria: "Transporte", // R$ 300 orçado, R$ 325 gasto (108.33%) ⚠️
    categoria: "Lazer", // R$ 400 orçado, R$ 150 gasto (37.5%)
    categoria: "Saúde", // R$ 250 orçado, R$ 180 gasto (72%)
    categoria: "Educação", // R$ 500 orçado, R$ 50 gasto (10%)
  },
];
```

### Dados de Transações

Cada categoria inclui transações de exemplo:

- Descrição da transação
- Valor em centavos
- Data no formato YYYY-MM-DD

## 🚀 Integração Futura com API

### Endpoints Necessários

```http
GET    /api/budgets?month=2024-11          # Listar orçamentos do mês
POST   /api/budgets                        # Criar orçamento
PUT    /api/budgets/{id}                   # Atualizar orçamento
DELETE /api/budgets/{id}                   # Excluir orçamento
GET    /api/budgets/{id}/transactions      # Transações da categoria
```

### Estrutura Backend (Laravel)

```php
// Tabela sugerida
Schema::create('budgets', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->string('categoria');
    $table->integer('valor_orcado'); // em centavos
    $table->string('mes_ano'); // YYYY-MM
    $table->text('observacao')->nullable();
    $table->timestamps();

    $table->unique(['user_id', 'categoria', 'mes_ano']);
});
```

## 📋 Como Usar

### 1. Acessar a Página

Navegue para `/orcamento` ou clique em "Orçamento" no menu lateral.

### 2. Criar Orçamento

1. Clique em "Novo Orçamento"
2. Selecione a categoria
3. Defina o valor mensal
4. Adicione observações (opcional)
5. Clique em "Criar"

### 3. Acompanhar Progresso

- Visualize os cards por categoria
- Verde: tudo sob controle
- Amarelo: atenção, próximo do limite
- Vermelho: orçamento excedido

### 4. Ver Detalhes

Clique em "Detalhes" em qualquer card para ver:

- Resumo visual do orçamento
- Lista de transações da categoria
- Progressão detalhada

### 5. Editar/Excluir

- **Editar**: Botão "Editar" no card
- **Excluir**: Botão "Excluir" (com confirmação)

## 🔄 Próximos Passos

### Funcionalidades Prioritárias

1. **Integração com API**

   - Criar endpoints no Laravel
   - Conectar frontend com backend
   - Implementar sincronização

2. **Cálculo Automático de Gastos**

   - Integrar com transações existentes
   - Atualizar gastos em tempo real
   - Notificações de limite excedido

3. **Funcionalidades Avançadas**
   - Copiar orçamento do mês anterior
   - Metas de economia personalizadas
   - Relatórios de performance
   - Exportação de dados

### Melhorias de UX

1. **Animações e Transições**

   - Loading states
   - Transições suaves
   - Feedback visual

2. **Personalização**
   - Cores personalizadas por categoria
   - Metas individuais
   - Alertas configuráveis

## 📊 Status de Desenvolvimento

| Componente         | Status   | Observações                             |
| ------------------ | -------- | --------------------------------------- |
| ✅ Interface (Vue) | Completo | Layout responsivo, todos os componentes |
| ✅ Store (Pinia)   | Completo | CRUD completo, persistência local       |
| ✅ Tipagens (TS)   | Completo | Interfaces bem definidas                |
| ✅ Roteamento      | Completo | Rota /orcamento integrada               |
| ✅ Menu            | Completo | Link no menu lateral                    |
| ❌ API Backend     | Pendente | Criar endpoints Laravel                 |
| ❌ Integração      | Pendente | Conectar frontend ↔ backend             |
| ❌ Cálculo Auto    | Pendente | Integrar com transações                 |

---

**Desenvolvido por:** GitHub Copilot  
**Framework:** Vue 3 + Vuetify 3 + TypeScript  
**Estado:** Pronto para integração com API  
**Última atualização:** Novembro 2024
