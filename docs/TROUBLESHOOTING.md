# 🔧 Troubleshooting - Problemas Comuns e Soluções

## ❌ Problemas no MainLayout

### 1. "Menu lateral não aparece"

**Sintomas:**

- Sidebar não aparece ao lado do conteúdo
- Apenas header visível

**Causas Possíveis:**

1. Router não tem meta.layout configurado
2. MainLayout não está importado no router
3. CSS do sidebar está com `display: none`

**Solução:**

```js
// router/index.ts - Verificar se todas as rotas têm meta.layout
{
  path: '/dashboard',
  component: () => import('../views/DashboardView.vue'),
  meta: {
    layout: 'main',  // ✅ Necessário
    requiresAuth: true
  }
}
```

---

### 2. "Admin/Trader não aparecem no menu"

**Sintomas:**

- Menu lateral mostra, mas Admin/Trader faltam
- Usuário FULL não consegue acessar painel admin

**Causas Possíveis:**

1. `userData.type` é null ou undefined
2. `canAccessAdmin` ou `canAccessTrader` retornam false
3. Propriedade errada sendo acessada (`role` em vez de `type`)

**Solução:**

```vue
<!-- MainLayout.vue -->
<script setup lang="ts">
import { onMounted, computed } from 'vue'

const userStore = useUserStore()
const userData = computed(() => userStore.userData)

// ✅ Verificar que está acessando "type" não "role"
const canAccessAdmin = computed(() => {
  console.log('userData:', userData.value)  // Debug
  return userData.value?.type === 'ADMIN' || userData.value?.type === 'FULL'
})

onMounted(() => {
  userStore.loadFromSession()  // ✅ Necessário
})
</script>

<!-- Template -->
<v-list-item
  v-if="canAccessAdmin"  <!-- ✅ Não usar função -->
  title="Painel Admin"
  prepend-icon="mdi-shield-admin"
/>
```

**Debug:**

```bash
# No console do navegador:
> localStorage.getItem('user')
> JSON.parse(localStorage.getItem('user'))
> // Verificar se tem "type" (não "role")
```

---

### 3. "Botão logout não funciona"

**Sintomas:**

- Clica em logout mas nada acontece
- Erro: "userStore.logout is not a function"

**Causas Possíveis:**

1. userStore não tem método `logout()`
2. Chamando wrong store (useAuthStore ou useUserStore?)

**Solução:**

```vue
<!-- ✅ CORRETO -->
<script setup lang="ts">
const authStore = useAuthStore();
const userStore = useUserStore();

function handleLogout() {
  authStore.clear(); // ✅ Limpar token
  userStore.clear(); // ✅ Limpar userData
  router.push("/login"); // ✅ Redirecionar
}
</script>

<!-- ✅ NÃO FAZER -->
// ❌ userStore.logout() - método não existe // ❌ authStore.logout() - também
não existe
```

---

### 4. "Theme não muda o fundo do conteúdo"

**Sintomas:**

- Clica theme toggle, header muda mas conteúdo não
- Dark mode não aplica fundo escuro no main content

**Causas Possíveis:**

1. CSS não tem background no .content-wrapper
2. Background hardcoded em vez de usar variáveis

**Solução:**

```css
/* MainLayout.vue - styles -->

.content-wrapper {
  background: rgb(var(--v-theme-background));  /* ✅ Dinâmico */
  padding: 24px;
}

/* NÃO FAZER */
/* ❌ background: #ffffff; - hardcoded */
/* ❌ background: white; - não segue tema */
```

---

### 5. "Month selector não navega"

**Sintomas:**

- Botões < > não funcionam
- Mês não muda ao clicar

**Causas Possíveis:**

1. Função não atualiza state corretamente
2. userStore.mesAno não existe
3. Formato de data incorreto

**Solução:**

```vue
<script setup lang="ts">
const userStore = useUserStore();

const mesAnoAtual = computed(() => userStore.mesAno);

function proximoMes() {
  // ✅ Parse corretamente
  const [ano, mes] = userStore.mesAno.split("-");
  let proximoMes = parseInt(mes) + 1;
  let proximoAno = parseInt(ano);

  if (proximoMes > 12) {
    proximoMes = 1;
    proximoAno += 1;
  }

  const novoMesAno = `${proximoAno}-${String(proximoMes).padStart(2, "0")}`;
  userStore.setMesAno(novoMesAno);
}

function mesAnterior() {
  const [ano, mes] = userStore.mesAno.split("-");
  let mesAnterior = parseInt(mes) - 1;
  let anoAnterior = parseInt(ano);

  if (mesAnterior < 1) {
    mesAnterior = 12;
    anoAnterior -= 1;
  }

  const novoMesAno = `${anoAnterior}-${String(mesAnterior).padStart(2, "0")}`;
  userStore.setMesAno(novoMesAno);
}

function hoje() {
  const agora = new Date();
  const ano = agora.getFullYear();
  const mes = String(agora.getMonth() + 1).padStart(2, "0");
  userStore.setMesAno(`${ano}-${mes}`);
}
</script>
```

---

## ❌ Problemas em ReceitasView / DespesasView

### 1. "Tabela não mostra dados"

**Sintomas:**

- v-data-table vazia
- Sem linhas de dados

**Causas Possíveis:**

1. Mock data não inicializado
2. `computed filteredReceitas` retorna array vazio
3. v-data-table headers não correspondem às propriedades

**Solução:**

```vue
<script setup lang="ts">
// ✅ Dados inicializados
const receitas = ref([
  {
    id: 1,
    descricao: 'Salário',
    valor: 5000,
    categoria: 'Renda',
    conta: 'Minha Conta',
    data_vencimento: '2024-10-15',
    status: 'recebida',
    observacao: ''
  }
])

// ✅ Computed retorna dados corretamente
const filteredReceitas = computed(() => {
  return receitas.value.filter(r => {
    const matchSearch = r.descricao.toLowerCase().includes(search.value.toLowerCase())
    const matchStatus = !statusFilter.value || r.status === statusFilter.value
    const matchCategory = !categoryFilter.value || r.categoria === categoryFilter.value
    return matchSearch && matchStatus && matchCategory
  })
})
</script>

<!-- ✅ Headers correspondem aos dados -->
<v-data-table
  :items="filteredReceitas"
  :headers="[
    { title: 'Descrição', key: 'descricao', align: 'start', width: '40%' },
    { title: 'Categoria', key: 'categoria', width: '20%' },
    { title: 'Valor', key: 'valor', align: 'end', width: '15%' },
    { title: 'Status', key: 'status', width: '15%' },
    { title: 'Ações', key: 'actions', sortable: false, width: '10%' }
  ]"
>
```

---

### 2. "Filtros não funcionam"

**Sintomas:**

- Digita na busca mas tabela não filtra
- Seleciona status/categoria mas nada acontece

**Causas Possíveis:**

1. `search`, `statusFilter`, `categoryFilter` não são reactive (ref)
2. `computed filteredReceitas` não observa os filtros
3. Event listeners não atualizam state

**Solução:**

```vue
<script setup lang="ts">
// ✅ Usar ref, não let
const search = ref("");
const statusFilter = ref("");
const categoryFilter = ref("");

// ✅ Computed inclui todos os filtros
const filteredReceitas = computed(() => {
  return receitas.value.filter((r) => {
    const matchSearch = r.descricao
      .toLowerCase()
      .includes(search.value.toLowerCase());
    const matchStatus = !statusFilter.value || r.status === statusFilter.value;
    const matchCategory =
      !categoryFilter.value || r.categoria === categoryFilter.value;
    return matchSearch && matchStatus && matchCategory;
  });
});

// ✅ Limpar filtros
function clearFilters() {
  search.value = "";
  statusFilter.value = "";
  categoryFilter.value = "";
}
</script>

<!-- ✅ v-model nos inputs -->
<v-text-field
  v-model="search"
  label="Buscar..."
  prepend-inner-icon="mdi-magnify"
  clearable
/>

<v-select
  v-model="statusFilter"
  :items="['recebida', 'pendente', 'cancelada']"
  label="Status"
/>
```

---

### 3. "Dialog de add/edit não abre"

**Sintomas:**

- Clica botão "Adicionar" mas dialog não aparece
- Dialog abre mas não fecha

**Causas Possíveis:**

1. `dialogOpen` não é ref
2. Função não atualiza `dialogOpen`
3. v-model não atualiza corretamente

**Solução:**

```vue
<script setup lang="ts">
// ✅ Usar ref
const dialogOpen = ref(false);
const editingId = ref<number | null>(null);
const form = ref({
  descricao: "",
  categoria: "",
  conta: "",
  valor: 0,
  data_vencimento: "",
  status: "recebida",
  observacao: "",
});

function openAddDialog() {
  form.value = {
    // ✅ Reset form
    descricao: "",
    categoria: "",
    conta: "",
    valor: 0,
    data_vencimento: "",
    status: "recebida",
    observacao: "",
  };
  editingId.value = null;
  dialogOpen.value = true; // ✅ Abrir
}

function closeDialog() {
  dialogOpen.value = false; // ✅ Fechar
}

function saveReceita() {
  // ... validar e salvar
  closeDialog(); // ✅ Fechar após salvar
}
</script>

<!-- ✅ v-model vinculado -->
<v-dialog v-model="dialogOpen" max-width="600px">
  <template #default="{ isActive }">
    <v-card>
      <v-card-title>{{ editingId ? 'Editar' : 'Adicionar' }} Receita</v-card-title>
      <v-card-text>
        <v-text-field
          v-model="form.descricao"
          label="Descrição"
          required
        />
      </v-card-text>
      <v-card-actions>
        <v-btn @click="closeDialog">Cancelar</v-btn>
        <v-btn @click="saveReceita" color="primary">Salvar</v-btn>
      </v-card-actions>
    </v-card>
  </template>
</v-dialog>
```

---

### 4. "Formatação de moeda errada"

**Sintomas:**

- Valores mostram como "5000" em vez de "R$ 5.000,00"
- Datas mostram formato ISO em vez de "DD/MM/YYYY"

**Causas Possíveis:**

1. Função `formatCurrency` não está sendo chamada
2. Formato de locale errado
3. Conversão de tipo string em vez de number

**Solução:**

```vue
<script setup lang="ts">
// ✅ Função correta com pt-BR
function formatCurrency(value: number | string): string {
  const num = typeof value === "string" ? parseFloat(value) : value;
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
  }).format(num);
}

// ✅ Data em DD/MM/YYYY
function formatDate(date: string | Date): string {
  const d = new Date(date);
  return d.toLocaleDateString("pt-BR");
}

// ✅ Percentual
function formatPercentage(value: number): string {
  return `${(value * 100).toFixed(1)}%`;
}
</script>

<!-- ✅ Usar em template -->
<template>
  <td class="text-right">
    {{ formatCurrency(receita.valor) }}
  </td>
  <td>
    {{ formatDate(receita.data_vencimento) }}
  </td>
  <td>
    {{ formatPercentage(0.052) }}
    <!-- 5.2% -->
  </td>
</template>
```

---

### 5. "Cores de status não aparecem"

**Sintomas:**

- Status chips mostram sem cor
- Background do chip é branco

**Causas Possíveis:**

1. Função `getStatusColor` não retorna cor válida
2. Vuetify color name errado
3. Chip não tem propriedade :color

**Solução:**

```vue
<script setup lang="ts">
// ✅ Cores Vuetify válidas
function getStatusColor(status: string): string {
  const colors: Record<string, string> = {
    recebida: "success", // Verde
    paga: "success", // Verde
    pendente: "warning", // Amarelo
    cancelada: "error", // Vermelho
    atrasada: "error", // Vermelho
  };
  return colors[status] || "info";
}

function getStatusLabel(status: string): string {
  const labels: Record<string, string> = {
    recebida: "Recebida",
    paga: "Paga",
    pendente: "Pendente",
    cancelada: "Cancelada",
    atrasada: "Atrasada",
  };
  return labels[status] || status;
}
</script>

<!-- ✅ Usar :color -->
<v-chip :color="getStatusColor(receita.status)" label>
  {{ getStatusLabel(receita.status) }}
</v-chip>

<!-- ❌ NÃO FAZER -->
<!-- <v-chip :color="'red'">  - string, não é válida -->
<!-- <v-chip color="red">   - color hardcoded -->
```

---

## ❌ Problemas de Performance

### 1. "Página carrega lentamente"

**Sintomas:**

- Demora muito para aparecer
- Travamentos ao interagir

**Causas Possíveis:**

1. Muitos dados na tabela (100+)
2. Computed properties recalculando desnecessariamente
3. Sem virtualização na tabela

**Solução:**

```vue
<!-- ✅ Usar v-virtual-scroll para listas grandes -->
<v-virtual-scroll :items="filteredReceitas" height="400" item-height="48">
  <template #default="{ item }">
    <!-- Renderizar item -->
  </template>
</v-virtual-scroll>

<!-- ✅ Lazy loading da tabela -->
<v-data-table
  :items="filteredReceitas"
  :items-per-page="10"
  :page.sync="currentPage"
/>

<!-- ✅ Memorizar computed expensive -->
<script setup lang="ts">
const summary = computed(() => {
  // Esta função é chamada apenas quando receitas mudam
  return {
    total: receitas.value.reduce((sum, r) => sum + r.valor, 0),
    recebidas: receitas.value.filter((r) => r.status === "recebida"),
    pendentes: receitas.value.filter((r) => r.status === "pendente"),
  };
});
</script>
```

---

### 2. "API chamadas muito lentas"

**Sintomas:**

- Aguardando resposta da API
- Loading spinner fica muito tempo

**Causas Possíveis:**

1. Servidor lento
2. Muitas chamadas simultâneas
3. Sem cache

**Solução:**

```vue
<script setup lang="ts">
const loading = ref(false);

// ✅ Com tratamento de erro
async function loadReceitas() {
  loading.value = true;
  try {
    const response = await fetch("/api/receitas");
    receitas.value = await response.json();
  } catch (error) {
    console.error("Erro ao carregar:", error);
    showError("Erro ao carregar receitas");
  } finally {
    loading.value = false;
  }
}

// ✅ Usar paginação
async function loadReceitas(page = 1, perPage = 10) {
  const response = await fetch(
    `/api/receitas?page=${page}&per_page=${perPage}`
  );
  return response.json();
}
</script>

<!-- ✅ Loading state -->
<v-data-table :items="filteredReceitas" :loading="loading" />
```

---

## ❌ Problemas de Compatibilidade

### 1. "Não funciona em navegador antigo"

**Sintomas:**

- Layout quebrado em IE11
- Estilos CSS não aplicam

**Solução:**

- Browser suportados: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- IE11 não é suportado (use polyfills se necessário)

---

### 2. "Responsividade quebrada no mobile"

**Sintomas:**

- Layout não adapta em celular
- Elementos sobrepostos

**Causas Possíveis:**

1. Breakpoints CSS errados
2. Viewport meta tag ausente
3. Fixed positioning conflitando

**Solução:**

```html
<!-- index.html -->
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<!-- Verificar breakpoints -->
<!-- xs: max-width: 599px   -->
<!-- sm: 600px to 959px     -->
<!-- md: 960px to 1263px    -->
<!-- lg: 1264px and above   -->
```

---

## 🔍 Como Debugar

### Console Log

```js
// MainLayout.vue
console.log("userData:", userData.value);
console.log("canAccessAdmin:", canAccessAdmin.value);
console.log("filteredAdminMenuItems:", filteredAdminMenuItems.value);
```

### Vue DevTools

```
1. Instalar extensão "Vue DevTools 6"
2. Abrir Components tab
3. Selecionar MainLayout
4. Ver props, data, computed em real-time
```

### Network Tab

```
1. F12 → Network tab
2. Filter por "api"
3. Ver requests/responses
4. Verificar status (200, 400, 500)
```

### Local Storage

```js
// Verificar dados salvos
localStorage.getItem("user");
localStorage.getItem("token");
localStorage.getItem("theme");

// Limpar
localStorage.clear();
```

---

## ✅ Checklist de Verificação

### Ao começar o dia

- [ ] `npm run dev` - Projeto inicia sem erros
- [ ] Console limpo (sem erros vermelhos)
- [ ] Layout mostra corretamente
- [ ] Sidebar com todos os itens

### Ao debugar

- [ ] Abrir Vue DevTools
- [ ] Verificar localStorage
- [ ] Abrir Network tab
- [ ] Verificar console para warnings

### Antes de deployr

- [ ] Sem console.error
- [ ] Sem console.warn não tratados
- [ ] Dados mock substituídos por API real
- [ ] Todas as imagens carregam
- [ ] Links funcionam
- [ ] Responsividade testada (xs, sm, md, lg)
- [ ] Dark mode funciona
- [ ] Logout funciona e redireciona

---

**Versão**: 1.0
**Última atualização**: Outubro 17, 2025
