# 🏷️ CategoriasView - Documentação Completa

**Versão**: 2.0 Redesign  
**Data**: Outubro 17, 2025  
**Status**: ✅ IMPLEMENTADO  
**Tipo**: View Autenticada (com MainLayout)

---

## 📌 Visão Geral

A **CategoriasView** permite que usuários criem e gerenciem categorias para organizar suas receitas e despesas.

---

## 🎨 Interface Visual

### Header + KPI Cards

```
┌─────────────────────────────────────┐
│ [🏷️] Categorias                     │
│ Gerencie suas categorias            │
│
│ ┌────────┬────────┬────────┬────────┐
│ │ Total  │Receitas│Despesas│Em Uso  │
│ │   6    │   3    │   3    │6 (100%)│
│ └────────┴────────┴────────┴────────┘
```

### Filtros

```
[🔍 Buscar...] [Tipo ▼] [Limpar]
```

### Cards Layout

```
┌────────────┬────────────┬────────────┐
│ [💚]Salário│ [💛]Freelan│ [💜]Invst. │
│ Renda mensal│Trabalhos ad│Rendimento │
│            │            │            │
│ Receita    │ Receita    │ Receita    │
│ 12 usos    │ 8 usos     │ 5 usos     │
└────────────┴────────────┴────────────┘
```

---

## 🚀 Funcionalidades Implementadas

### 1. **Dashboard com KPI Cards**

- ✅ Total de Categorias
- ✅ Categorias de Receita
- ✅ Categorias de Despesa
- ✅ Em Uso (com % utilização)
- ✅ Cores dinâmicas por tipo

### 2. **Filtros Avançados**

- ✅ Busca por nome ou descrição
- ✅ Filtro por tipo (receita, despesa)
- ✅ Botão limpar (reset filtros)
- ✅ Resultados em tempo real

### 3. **Cards Layout**

- ✅ Avatar com cor do tipo
- ✅ Nome e descrição
- ✅ Tipo com chip colorido
- ✅ Contador de usos
- ✅ Menu de ações (editar, deletar)
- ✅ Hover effects

### 4. **CRUD Operations**

- ✅ **Create**: Dialog "Nova Categoria"
- ✅ **Read**: Cards com informações
- ✅ **Update**: Dialog "Editar Categoria"
- ✅ **Delete**: Confirmação antes de deletar

### 5. **Dialog Add/Edit**

- ✅ Nome (obrigatório)
- ✅ Descrição (textarea)
- ✅ Tipo (select: receita, despesa)
- ✅ Color Picker para cor
- ✅ Validação de campos
- ✅ Botões Salvar/Cancelar

### 6. **Responsividade**

- ✅ Desktop (>1024px): 4 colunas
- ✅ Tablet (600-1024px): 2 colunas
- ✅ Mobile (<600px): 1 coluna

---

## 💻 Estrutura de Código

### Tipos TypeScript

```typescript
interface Categoria {
  id: number;
  nome: string;
  descricao: string;
  tipo: "receita" | "despesa";
  cor: string;
  usos: number;
}
```

### State

```typescript
const categorias = ref<Categoria[]>([...])
const search = ref('')
const tipoFilter = ref<string | null>(null)
const dialogOpen = ref(false)
const loading = ref(false)
const editingId = ref<number | null>(null)

const form = ref({
  nome: '',
  descricao: '',
  tipo: 'receita',
  cor: '#2196F3'
})
```

### Computed Properties

```typescript
const receitas = computed(() =>
  categorias.value.filter((c) => c.tipo === "receita")
);

const despesas = computed(() =>
  categorias.value.filter((c) => c.tipo === "despesa")
);

const emUso = computed(() => categorias.value.filter((c) => c.usos > 0).length);

const percentualUso = computed(() =>
  categorias.value.length > 0
    ? Math.round((emUso.value / categorias.value.length) * 100)
    : 0
);

const filteredCategorias = computed(() =>
  categorias.value.filter((categoria) => {
    const matchSearch =
      categoria.nome.toLowerCase().includes(search.value.toLowerCase()) ||
      categoria.descricao.toLowerCase().includes(search.value.toLowerCase());
    const matchTipo = !tipoFilter.value || categoria.tipo === tipoFilter.value;
    return matchSearch && matchTipo;
  })
);
```

### Methods

```typescript
function getCategoriaColor(tipo: string): string;
function getCategoriaIcon(tipo: string): string;
function getTipoColor(tipo: string): string;
function getTipoLabel(tipo: string): string;
function openAddDialog(): void;
function editCategoria(categoria: Categoria): void;
function saveCategoria(): void;
function deleteCategoria(id: number): void;
function clearFilters(): void;
function closeDialog(): void;
```

---

## 📊 Mock Data

```typescript
[
  {
    id: 1,
    nome: "Salário",
    descricao: "Renda mensal",
    tipo: "receita",
    cor: "#4caf50",
    usos: 12,
  },
  {
    id: 2,
    nome: "Freelance",
    descricao: "Trabalhos adicionais",
    tipo: "receita",
    cor: "#66bb6a",
    usos: 8,
  },
  {
    id: 3,
    nome: "Investimentos",
    descricao: "Rendimento de investimentos",
    tipo: "receita",
    cor: "#81c784",
    usos: 5,
  },
  {
    id: 4,
    nome: "Aluguel",
    descricao: "Despesa de aluguel",
    tipo: "despesa",
    cor: "#f44336",
    usos: 12,
  },
  {
    id: 5,
    nome: "Alimentação",
    descricao: "Compras de comida",
    tipo: "despesa",
    cor: "#ff7043",
    usos: 28,
  },
  {
    id: 6,
    nome: "Transporte",
    descricao: "Uber, táxi, combustível",
    tipo: "despesa",
    cor: "#ff6f00",
    usos: 15,
  },
];
```

---

## 🎨 Cores e Temas

### Tipos de Categoria

#### Receita

- **Icon**: mdi-plus-circle
- **Color**: success (verde)
- **Default**: #4caf50

#### Despesa

- **Icon**: mdi-minus-circle
- **Color**: error (vermelho)
- **Default**: #f44336

### KPI Cards

- **Receitas**: Success gradient
- **Despesas**: Error gradient
- **Total**: Primary
- **Em Uso**: Info

---

## 📱 Responsividade

### Desktop (>1024px)

- 4 colunas de cards
- KPI cards em 1 linha
- Filtros em 1 linha

### Tablet (600-1024px)

- 2 colunas de cards
- KPI cards em 2 linhas
- Filtros em 2 linhas

### Mobile (<600px)

- 1 coluna de cards
- KPI cards em stack
- Filtros em stack

---

## 🌓 Dark Mode Support

```scss
// Background adapta automaticamente
background: rgb(var(--v-theme-background));

// Textos com opacity para mode
.text-medium-emphasis {
  opacity: 0.7;
}

// Cards com elevação apropriada
.kpi-card {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
}
```

---

## ✅ Validações

### Campo: Nome

```
- Obrigatório
- Máximo 50 caracteres
```

### Campo: Descrição

```
- Opcional
- Máximo 200 caracteres
```

### Campo: Tipo

```
- Obrigatório
- Select: receita, despesa
```

### Campo: Cor

```
- Opcional
- Color Picker HEX
- Default: #2196F3
```

---

## 🧪 Teste Manualmente

### Criar Categoria

1. Clique "Nova Categoria"
2. Preencha Nome, Descrição, Tipo, Cor
3. Clique "Adicionar"
4. Categoria aparece nos cards

### Editar Categoria

1. Clique ⋮ em um card
2. Selecione "Editar"
3. Modifique campos
4. Clique "Atualizar"

### Deletar Categoria

1. Clique ⋮ em um card
2. Selecione "Deletar"
3. Confirme deleção

### Filtros

1. Digite em "Buscar categoria"
2. Selecione "Tipo"
3. Cards filtram em real-time
4. Clique "Limpar" para resetar

---

## 💡 Casos de Uso

### Receitas Padrão

- Salário
- Freelance
- Investimentos
- Aluguéis
- Bônus

### Despesas Padrão

- Aluguel
- Alimentação
- Transporte
- Saúde
- Lazer

---

## 📚 Referências

- **Layout**: MainLayout.vue
- **Pattern**: ContasView.vue, CartaoCreditoView.vue
- **Formatação**: Intl.NumberFormat pt-BR

---

**Versão**: 2.0  
**Status**: ✅ COMPLETO
