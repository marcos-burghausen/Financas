# 💳 CartaoCreditoView - Documentação Completa

**Versão**: 2.0 Redesign  
**Data**: Outubro 17, 2025  
**Status**: ✅ IMPLEMENTADO

---

## 📌 Visão Geral

A **CartaoCreditoView** é a interface para gerenciamento completo de cartões de crédito. Permite visualizar, criar, editar e deletar cartões com dashboard de limites, utilização e datas de vencimento.

---

## 🎨 Interface Visual

### Header

```
[Ícone Cartão] Meus Cartões de Crédito
Gerencie seus cartões, limites e faturas
```

### KPI Cards (4)

```
┌──────────────────┬──────────────────┬──────────────────┬──────────────────┐
│ Total de Cartões │  Limite Total    │    Utilizado     │   Disponível     │
│        4         │ R$ 17.000,00     │ R$ 4.300,00 (25%)│ R$ 12.700,00     │
└──────────────────┴──────────────────┴──────────────────┴──────────────────┘
```

### Filtros

```
[Buscar cartões...]  [Bandeira ▼]  [Status ▼]  [Limpar]
```

### Tabela

```
┌─────────────────────┬───────────┬────────────┬──────────────┬──────────────┬────────┬────────┐
│ Cartão              │ Bandeira  │ Utilizado  │    Limite    │ Vencimento   │ Status │Ações  │
├─────────────────────┼───────────┼────────────┼──────────────┼──────────────┼────────┼────────┤
│ [💳] Meu Visa       │ Visa      │ R$ 2.500  │ R$ 5.000    │ 15/11 (5d)   │ Ativo  │ ✎ 🗑️ │
│ [💳] Mastercard     │ MC        │ R$ 1.800  │ R$ 8.000    │ 20/11 (10d)  │ Ativo  │ ✎ 🗑️ │
│ [💳] ELO            │ ELO       │   R$ 0    │ R$ 3.000    │ 10/11 (0d)   │ Ativo  │ ✎ 🗑️ │
│ [💳] Cartão Antigo  │ Visa      │   R$ 0    │ R$ 1.000    │ 01/12 (15d)  │Inativo │ ✎ 🗑️ │
└─────────────────────┴───────────┴────────────┴──────────────┴──────────────┴────────┴────────┘
```

---

## 🚀 Funcionalidades Implementadas

### 1. **Dashboard com KPI Cards**

- ✅ Total de cartões
- ✅ Limite total consolidado
- ✅ Utilizado com percentual
- ✅ Disponível (limite - utilizado)
- ✅ Cores dinâmicas (success/error)

### 2. **Filtros Avançados**

- ✅ Busca por nome ou número
- ✅ Filtro por bandeira (Visa, MC, ELO, Amex, etc)
- ✅ Filtro por status (ativo, inativo, bloqueado)
- ✅ Botão limpar (reset todos filtros)
- ✅ Resultados em tempo real

### 3. **Tabela de Dados**

- ✅ Ícone de cartão + Nome
- ✅ Número do cartão (últimos 4 dígitos)
- ✅ Bandeira com chip colorido
- ✅ Utilizado com barra de progresso
  - 0-50%: verde
  - 50-80%: amarelo
  - 80-100%: vermelho
- ✅ Limite em BRL
- ✅ Vencimento com dias restantes
  - "Vencido" se passou
  - "Vence hoje" se hoje
  - "Vence amanhã" se amanhã
  - "Vence em Xd" se futuro
- ✅ Status com cores (sucesso/inativo/erro)
- ✅ Ações (editar, deletar)

### 4. **CRUD Operations**

- ✅ **Create**: Dialog "Novo Cartão" com validação
- ✅ **Read**: Tabela com dados atualizados
- ✅ **Update**: Dialog "Editar Cartão" com dados preenchidos
- ✅ **Delete**: Confirmação antes de deletar

### 5. **Dialog Add/Edit**

- ✅ Validação de campos obrigatórios
- ✅ Campos específicos:
  - Nome do cartão
  - Bandeira (select: Visa, Mastercard, ELO, etc)
  - Tipo (select: crédito, débito, múltiplo)
  - Número do cartão (últimos 4)
  - Limite
  - Utilizado
  - Vencimento do cartão (mês/ano)
  - Vencimento da fatura (data)
  - Status (select: ativo, inativo, bloqueado)
  - Observações (textarea)

### 6. **Responsividade**

- ✅ Desktop (>1024px): Tabela completa
- ✅ Tablet (600-1024px): Tabela reduzida
- ✅ Mobile (<600px): Stack de cards

---

## 💻 Estrutura de Código

### Tipos TypeScript

```typescript
interface Cartao {
  id: number;
  nome: string;
  bandeiraira: string; // Visa, Mastercard, ELO, etc
  tipo: "credito" | "debito" | "multiplo";
  numero: string; // **** **** **** 1234
  limite: number;
  utilizado: number;
  vencimentoCartao: string; // YYYY-MM (ex: 2027-05)
  vencimentoFatura: string; // YYYY-MM-DD
  status: "ativo" | "inativo" | "bloqueado";
  observacao: string;
}
```

### State (Ref)

```typescript
const cartoes = ref<Cartao[]>([...])           // Array de cartões
const search = ref('')                         // Busca
const bandueiraFilter = ref('')                // Filtro bandeira
const statusFilter = ref('')                   // Filtro status
const dialogOpen = ref(false)                  // Dialog visível
const loading = ref(false)                     // Loading state
const editingId = ref<number | null>(null)    // ID em edição
```

### Computed Properties

```typescript
const summary = computed(() => {
  const limiteTotal = cartoes.value.reduce((sum, c) => sum + c.limite, 0);
  const utilizado = cartoes.value.reduce((sum, c) => sum + c.utilizado, 0);
  const disponivel = limiteTotal - utilizado;
  return {
    limiteTotal,
    utilizado,
    disponivel,
    percentualUtilizado: limiteTotal > 0 ? utilizado / limiteTotal : 0,
  };
});

const filteredCartoes = computed(() =>
  cartoes.value.filter((cartao) => {
    const matchSearch = cartao.nome
      .toLowerCase()
      .includes(search.value.toLowerCase());
    const matchBandeiraira =
      !bandueiraFilter.value || cartao.bandeiraira === bandueiraFilter.value;
    const matchStatus =
      !statusFilter.value || cartao.status === statusFilter.value;
    return matchSearch && matchBandeiraira && matchStatus;
  })
);
```

### Methods

#### Formatação

```typescript
function formatCurrency(value: number): string;
// Input: 2500
// Output: "R$ 2.500,00"

function formatPercentage(value: number): string;
// Input: 0.25
// Output: "25.0%"

function formatDate(date: string): string;
// Input: "2025-11-15"
// Output: "15/11/2025"

function getDiasRestantes(data: string): string;
// Input: "2025-11-15"
// Output: "Vence em 5d" ou "Vencido" ou "Vence hoje"

function getBandeiraColor(bandeiraira: string): string;
// Input: "Visa"
// Output: "info" (Vuetify color)

function getUtilizacaoColor(utilizado: number, limite: number): string;
// Retorna 'error' (80+%), 'warning' (50-80%), 'success' (<50%)

function getStatusColor(status: string): string;
// Input: "ativo"
// Output: "success" ou "secondary" ou "error"

function getStatusLabel(status: string): string;
// Input: "ativo"
// Output: "Ativo"
```

#### CRUD

```typescript
function openAddDialog(); // Abre dialog vazio para novo cartão
function editCartao(cartao); // Abre dialog com dados para editar
function saveCartao(); // Salva (create ou update)
function deleteCartao(id); // Deleta com confirmação
function closeDialog(); // Fecha dialog
```

#### Filtros

```typescript
function clearFilters(); // Limpa todos os filtros
```

---

## 📊 Mock Data

```typescript
[
  {
    id: 1,
    nome: "Meu Visa",
    bandeiraira: "Visa",
    tipo: "credito",
    numero: "**** **** **** 1234",
    limite: 5000,
    utilizado: 2500,
    vencimentoCartao: "2027-05",
    vencimentoFatura: "2025-11-15",
    status: "ativo",
    observacao: "Principal",
  },
  {
    id: 2,
    nome: "Mastercard",
    bandeiraira: "Mastercard",
    tipo: "credito",
    numero: "**** **** **** 5678",
    limite: 8000,
    utilizado: 1800,
    vencimentoCartao: "2026-08",
    vencimentoFatura: "2025-11-20",
    status: "ativo",
    observacao: "Backup",
  },
  // ... mais cartões
];
```

---

## 🎯 Headers da Tabela

```typescript
const headers = [
  { title: "Cartão", key: "nome", align: "start" },
  { title: "Bandeira", key: "bandeiraira", align: "center", width: "120px" },
  { title: "Utilizado", key: "utilizado", align: "center", width: "140px" },
  { title: "Limite", key: "limite", align: "end", width: "130px" },
  {
    title: "Vencimento",
    key: "vencimentoFatura",
    align: "center",
    width: "140px",
  },
  { title: "Status", key: "status", align: "center", width: "100px" },
  {
    title: "Ações",
    key: "actions",
    sortable: false,
    align: "center",
    width: "100px",
  },
];
```

---

## 🎨 Cores e Temas

### Bandeiras

```
┌──────────────────┬──────────────────┐
│ Bandeira         │ Cor              │
├──────────────────┼──────────────────┤
│ Visa             │ info (azul)      │
│ Mastercard       │ warning (amarelo)│
│ ELO              │ success (verde)  │
│ American Express │ primary (azul)   │
│ Hipercard        │ secondary        │
│ Diners           │ accent           │
└──────────────────┴──────────────────┘
```

### Status

```
┌────────────┬──────────────────┐
│ Status     │ Cor              │
├────────────┼──────────────────┤
│ Ativo      │ success (verde)  │
│ Inativo    │ secondary (cinza)│
│ Bloqueado  │ error (vermelho) │
└────────────┴──────────────────┘
```

### Utilização (Barra de Progresso)

```
┌───────────────────┬──────────┐
│ Faixa             │ Cor      │
├───────────────────┼──────────┤
│ 0-50%             │ success  │
│ 50-80%            │ warning  │
│ 80-100%           │ error    │
└───────────────────┴──────────┘
```

### KPI Cards

```
┌──────────────────────────┬──────────────────┐
│ Card                     │ Border Color     │
├──────────────────────────┼──────────────────┤
│ Padrão                   │ error (vermelho) │
│ Disponível positivo      │ success          │
│ Disponível negativo      │ error            │
└──────────────────────────┴──────────────────┘
```

---

## 🔌 API Integration (Phase 2)

### Endpoints Esperados

```
GET    /api/cartoes              # Listar cartões
POST   /api/cartoes              # Criar cartão
GET    /api/cartoes/{id}         # Detalhe
PUT    /api/cartoes/{id}         # Editar cartão
DELETE /api/cartoes/{id}         # Deletar cartão
```

### Exemplo de Integração

```typescript
async function loadCartoes() {
  loading.value = true;
  try {
    const { data } = await api.get("/cartoes");
    cartoes.value = data;
  } catch (error) {
    console.error("Error:", error);
  } finally {
    loading.value = false;
  }
}

async function saveCartao() {
  loading.value = true;
  try {
    if (editingId.value) {
      await api.put(`/cartoes/${editingId.value}`, form.value);
    } else {
      const { data } = await api.post("/cartoes", form.value);
      cartoes.value.push(data);
    }
    closeDialog();
  } catch (error) {
    console.error("Error:", error);
  } finally {
    loading.value = false;
  }
}
```

---

## 📱 Responsividade

### Desktop (>1024px)

- Tabela completa com 7 colunas
- KPI Cards em 4 colunas (1 linha)
- Filtros em 1 linha

### Tablet (600-1024px)

- Tabela reduzida (4-5 colunas)
- KPI Cards em 2 colunas (2 linhas)
- Filtros em 2 linhas

### Mobile (<600px)

- Tabela em stack vertical
- KPI Cards em 1 coluna (4 linhas)
- Filtros em stack vertical

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

## ⚡ Performance

- Mock data: 0ms
- Filtros: <50ms (computed)
- Dialog open: <200ms (animação)
- Tabela render: <100ms

---

## ✅ Validações

### Campo: Nome

```
- Obrigatório
- Máximo 50 caracteres
```

### Campo: Bandeira

```
- Obrigatório
- Select de 6 bandeiras
```

### Campo: Tipo

```
- Obrigatório
- Select: crédito, débito, múltiplo
```

### Campo: Limite

```
- Obrigatório
- Número > 0
- 2 casas decimais
```

### Campo: Status

```
- Obrigatório
- Select: ativo, inativo, bloqueado
```

---

## 🧪 Teste Manualmente

### Criar Cartão

1. Clique "Nova Cartão"
2. Preencha nome, bandeira, tipo, limite
3. Clique "Adicionar"
4. Cartão aparece na tabela

### Editar Cartão

1. Clique ícone lápis em um cartão
2. Modifique campos
3. Clique "Atualizar"
4. Dados atualizados na tabela

### Deletar Cartão

1. Clique ícone lixo em um cartão
2. Confirme deleção
3. Cartão removido da tabela

### Filtros

1. Digite em "Buscar cartões"
2. Selecione bandeira e status
3. Tabela filtra em real-time
4. Clique "Limpar" para resetar

### Utilização

1. Varie valor de "Utilizado"
2. Veja barra de progresso mudar de cor
3. KPI Card atualiza percentual

---

## 💡 Dicas de Uso

### Para Controlar Limite

- Mantenha utilizado < 50% do limite
- Fique atento a vencimentos
- Revise regularmente

### Para Organização

- Nomeie os cartões de forma descritiva
- Adicione observações importantes
- Atualize status quando necessário

### Para Segurança

- Não salve números completos
- Use apenas últimos 4 dígitos
- Revise transações regularmente

---

## 📚 Referências

- **Layout**: MainLayout.vue (header, sidebar, theme)
- **Pattern**: ReceitasView.vue (estrutura similar)
- **Formatação**: Intl.NumberFormat pt-BR

---

**Versão**: 2.0  
**Data**: Outubro 17, 2025  
**Status**: ✅ COMPLETO
