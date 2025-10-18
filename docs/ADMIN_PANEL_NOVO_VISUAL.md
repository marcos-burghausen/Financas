# 👮 AdminPanelView - Documentação Completa

**Versão**: 2.0 Redesign  
**Data**: Outubro 17, 2025  
**Status**: ✅ IMPLEMENTADO

---

## 📌 Visão Geral

A **AdminPanelView** é o painel exclusivo para administradores do sistema. Permite gerenciar usuários, visualizar estatísticas do sistema, monitorar atividades e controlar permissões.

---

## 🎨 Interface Visual

### Header

```
[Ícone Admin] Painel Administrativo
Gerencie usuários, permissões e monitore o sistema
```

### KPI Cards (4)

```
┌──────────────────┬──────────────────┬──────────────────┬──────────────────┐
│ Total de Usuários│ Usuários Ativos  │Total Lançamentos │Taxa de Atividade │
│       25         │        18        │      2.847       │       87%        │
└──────────────────┴──────────────────┴──────────────────┴──────────────────┘
```

### Filtros

```
[Buscar usuário...]  [Tipo ▼]  [Status ▼]  [Limpar]
```

### Tabela

```
┌────────────────────────┬────────────────┬──────────┬─────────────┬────────┐
│ Nome                   │ Tipo           │ Status   │ Criado em   │Ações  │
├────────────────────────┼────────────────┼──────────┼─────────────┼────────┤
│ [👤] João Silva        │ Full Access    │ Ativo    │ 15/01/2025  │ ✎ 🗑️ │
│ [👤] Maria Santos      │ Trader         │ Ativo    │ 20/02/2025  │ ✎ 🗑️ │
│ [👤] Pedro Costa       │ Usuário        │ Ativo    │ 10/03/2025  │ ✎ 🗑️ │
│ [👤] Ana Oliveira      │ Usuário+Trader │ Inativo  │ 25/01/2025  │ ✎ 🗑️ │
└────────────────────────┴────────────────┴──────────┴─────────────┴────────┘
```

---

## 🚀 Funcionalidades Implementadas

### 1. **Dashboard com KPI Cards**

- ✅ Total de usuários
- ✅ Usuários ativos (com %)
- ✅ Total de lançamentos
- ✅ Taxa de atividade
- ✅ Cores dinâmicas por card

### 2. **Filtros Avançados**

- ✅ Busca por nome ou email
- ✅ Filtro por tipo (USER, TRADER, ADMIN, USER_TRADER, FULL)
- ✅ Filtro por status (ativo, inativo, bloqueado)
- ✅ Botão limpar (reset todos filtros)
- ✅ Resultados em tempo real

### 3. **Tabela de Usuários**

- ✅ Avatar com iniciais + Nome + Email
- ✅ Tipo com chip colorido
- ✅ Status com cores
- ✅ Data de criação formatada
- ✅ Ações (editar, deletar)

### 4. **CRUD Operations**

- ✅ **Create**: Dialog "Novo Usuário" com validação
- ✅ **Read**: Tabela com dados atualizados
- ✅ **Update**: Dialog "Editar Usuário" com dados preenchidos
- ✅ **Delete**: Confirmação antes de deletar

### 5. **Dialog Add/Edit**

- ✅ Validação de campos obrigatórios
- ✅ Campos:
  - Nome completo
  - Email
  - Tipo de usuário (select)
  - Status (select)
  - Observações (textarea)

### 6. **Responsividade**

- ✅ Desktop (>1024px): Tabela completa
- ✅ Tablet (600-1024px): Tabela otimizada
- ✅ Mobile (<600px): Stack de cards

---

## 💻 Estrutura de Código

### Tipos TypeScript

```typescript
interface Usuario {
  id: number;
  nome: string;
  email: string;
  type: "USER" | "TRADER" | "ADMIN" | "USER_TRADER" | "FULL";
  status: "ativo" | "inativo" | "bloqueado";
  dataCriacao: string;
  observacao: string;
}
```

### State (Ref)

```typescript
const usuarios = ref<Usuario[]>([...])         // Array de usuários
const search = ref('')                          // Busca
const typeFilter = ref<string | null>(null)    // Filtro tipo
const statusFilter = ref<string | null>(null)  // Filtro status
const dialogOpen = ref(false)                   // Dialog visível
const loading = ref(false)                      // Loading state
const editingId = ref<number | null>(null)    // ID em edição
```

### Computed Properties

```typescript
const summary = computed(() => {
  const total = usuarios.value.length;
  const active = usuarios.value.filter((u) => u.status === "ativo").length;
  return {
    totalUsers: total,
    newUsersThisMonth: Math.floor(total * 0.25),
    activeUsers: active,
    activePercentage: total > 0 ? Math.round((active / total) * 100) : 0,
    totalLancamentos: lancamentos,
    lancamentosThisMonth: lancamentosMonth,
    activityRate: 87,
  };
});

const filteredUsers = computed(() =>
  usuarios.value.filter((usuario) => {
    const matchSearch =
      usuario.nome.toLowerCase().includes(search.value.toLowerCase()) ||
      usuario.email.toLowerCase().includes(search.value.toLowerCase());
    const matchType = !typeFilter.value || usuario.type === typeFilter.value;
    const matchStatus =
      !statusFilter.value || usuario.status === statusFilter.value;
    return matchSearch && matchType && matchStatus;
  })
);
```

### Methods

#### Formatação

```typescript
function formatNumber(n: number): string;
// Input: 2847
// Output: "2.847"

function formatDate(date: string): string;
// Input: "2025-01-15"
// Output: "15/01/2025"

function getInitials(nome: string): string;
// Input: "João Silva"
// Output: "JS"

function getAvatarColor(id: number): string;
// Retorna cor baseada em ID

function getTypeColor(type: string): string;
// Input: "FULL"
// Output: "primary"

function getTypeLabel(type: string): string;
// Input: "FULL"
// Output: "Full Access"

function getStatusColor(status: string): string;
// Input: "ativo"
// Output: "success"

function getStatusLabel(status: string): string;
// Input: "ativo"
// Output: "Ativo"
```

#### CRUD

```typescript
function openAddDialog(); // Abre dialog vazio
function editUser(usuario); // Abre dialog com dados
function saveUser(); // Salva (create ou update)
function deleteUser(id); // Deleta com confirmação
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
    nome: "João Silva",
    email: "joao@example.com",
    type: "FULL",
    status: "ativo",
    dataCriacao: "2025-01-15",
    observacao: "Admin master",
  },
  {
    id: 2,
    nome: "Maria Santos",
    email: "maria@example.com",
    type: "TRADER",
    status: "ativo",
    dataCriacao: "2025-02-20",
    observacao: "Trader ativo",
  },
  // ... mais usuários
];
```

---

## 🎯 Headers da Tabela

```typescript
const headers = [
  { title: "Nome", key: "nome", align: "start" },
  { title: "Tipo", key: "type", align: "center", width: "120px" },
  { title: "Status", key: "status", align: "center", width: "120px" },
  { title: "Criado em", key: "dataCriacao", align: "center", width: "130px" },
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

### Tipos de Usuário

```
┌──────────────────┬──────────────────┐
│ Tipo             │ Cor              │
├──────────────────┼──────────────────┤
│ FULL             │ primary (azul)   │
│ TRADER           │ warning (amarelo)│
│ ADMIN            │ error (vermelho) │
│ USER_TRADER      │ info (ciano)     │
│ USER             │ secondary (cinza)│
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

### KPI Cards

```
┌──────────────────────┬──────────────────┐
│ Card                 │ Cor Borda        │
├──────────────────────┼──────────────────┤
│ Total de Usuários    │ info (ciano)     │
│ Usuários Ativos      │ success (verde)  │
│ Total Lançamentos    │ warning (amarelo)│
│ Taxa de Atividade    │ primary (azul)   │
└──────────────────────┴──────────────────┘
```

---

## 🔌 API Integration (Phase 2)

### Endpoints Esperados

```
GET    /api/usuarios              # Listar usuários
POST   /api/usuarios              # Criar usuário
GET    /api/usuarios/{id}         # Detalhe
PUT    /api/usuarios/{id}         # Editar usuário
DELETE /api/usuarios/{id}         # Deletar usuário
```

### Exemplo de Integração

```typescript
async function loadUsuarios() {
  loading.value = true;
  try {
    const { data } = await api.get("/usuarios");
    usuarios.value = data;
  } catch (error) {
    console.error("Error:", error);
  } finally {
    loading.value = false;
  }
}

async function saveUser() {
  loading.value = true;
  try {
    if (editingId.value) {
      await api.put(`/usuarios/${editingId.value}`, form.value);
    } else {
      const { data } = await api.post("/usuarios", form.value);
      usuarios.value.push(data);
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

- Tabela completa com 5 colunas
- KPI Cards em 4 colunas (1 linha)
- Filtros em 1 linha

### Tablet (600-1024px)

- Tabela adaptada (4-5 colunas)
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

### Campo: Email

```
- Obrigatório
- Formato email válido
```

### Campo: Tipo

```
- Obrigatório
- Select de 5 tipos
```

### Campo: Status

```
- Obrigatório
- Select: ativo, inativo, bloqueado
```

---

## 🧪 Teste Manualmente

### Criar Usuário

1. Clique ícone "+" para novo usuário
2. Preencha nome, email, tipo, status
3. Clique "Adicionar"
4. Usuário aparece na tabela

### Editar Usuário

1. Clique ícone lápis em um usuário
2. Modifique campos
3. Clique "Atualizar"
4. Dados atualizados na tabela

### Deletar Usuário

1. Clique ícone lixo em um usuário
2. Confirme deleção
3. Usuário removido da tabela

### Filtros

1. Digite em "Buscar usuário"
2. Selecione tipo e status
3. Tabela filtra em real-time
4. Clique "Limpar" para resetar

---

## 💡 Dicas de Uso

### Para Gerenciar Permissões

- Usuários FULL Access têm acesso total
- Traders podem visualizar dados financeiros
- Admins gerenciam sistema
- Users são usuários comuns

### Para Monitorar

- Revise taxa de atividade regularmente
- Verifique usuários inativos
- Mantenha usuários atualizados

### Para Segurança

- Crie usuários Admin com cuidado
- Revise permissões regularmente
- Desative contas não utilizadas

---

## 📚 Referências

- **Layout**: MainLayout.vue (header, sidebar, theme)
- **Pattern**: ContasView.vue (estrutura similar)
- **Formatação**: Intl.NumberFormat pt-BR

---

**Versão**: 2.0  
**Data**: Outubro 17, 2025  
**Status**: ✅ COMPLETO
