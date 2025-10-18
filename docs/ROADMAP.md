# 🚀 Roadmap e Próximos Passos - MrFinancas v2.0

## 📋 Status Atual

```
✅ CONCLUÍDO (Semana 1)
├─ Análise completa do projeto
├─ Design visual do layout
├─ MainLayout global
├─ ReceitasView modernizada
├─ DespesasView modernizada
├─ Mock data para testes
├─ Documentação completa
└─ Sistema de controle de acesso (ADMIN/TRADER)

🔄 EM PROGRESSO (Semana 2-3)
├─ Integração com API real
├─ Testes unitários
├─ Testes de integração
└─ Performance optimization

⏳ PLANEJADO (Semana 4-6)
├─ Outros views (Contas, Categorias, Perfil)
├─ Dashboard avançado com gráficos
├─ Painel Admin
├─ Painel Trader
└─ Notificações em tempo real

📅 FUTURO (Semana 7+)
├─ Export PDF/Excel
├─ Agendamento automático
├─ Reports avançados
├─ Mobile app nativa
└─ Integrações externas
```

---

## 🎯 Fase 1: Integração com API (Semana 2-3)

### Objetivo

Conectar ReceitasView e DespesasView com endpoints reais de API

### Tarefas

#### 1.1 Preparar Endpoints na API

**Que fazer:**

- [ ] Criar endpoints REST em Laravel
- [ ] Adicionar autenticação Sanctum
- [ ] Implementar validação
- [ ] Adicionar paginação

**Endpoints Necessários:**

```
GET    /api/receitas              # Listar (com filtros)
POST   /api/receitas              # Criar
GET    /api/receitas/{id}         # Detalhe
PUT    /api/receitas/{id}         # Editar
DELETE /api/receitas/{id}         # Deletar

GET    /api/despesas              # Listar (com filtros)
POST   /api/despesas              # Criar
GET    /api/despesas/{id}         # Detalhe
PUT    /api/despesas/{id}         # Editar
DELETE /api/despesas/{id}         # Deletar

GET    /api/categorias            # Listar categorias
GET    /api/contas                # Listar contas
GET    /api/resumo                # Resumo para dashboard
```

**Exemplo Laravel:**

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('receitas', ReceitaController::class);
    Route::apiResource('despesas', DespesaController::class);
    Route::get('categorias', CategoriaController::class . '@index');
    Route::get('contas', ContaController::class . '@index');
    Route::get('resumo', DashboardController::class . '@resumo');
});
```

---

#### 1.2 Atualizar ReceitasView

**Que fazer:**

- [ ] Substituir mock data por fetch real
- [ ] Adicionar loading state
- [ ] Implementar error handling
- [ ] Adicionar paginação

**Código:**

```vue
<script setup lang="ts">
import { ref, onMounted, computed } from "vue";

interface Receita {
  id: number;
  descricao: string;
  valor: number;
  categoria: string;
  conta: string;
  data_vencimento: string;
  status: string;
  observacao: string;
}

const receitas = ref<Receita[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);
const currentPage = ref(1);
const pageSize = ref(10);

// ✅ Carregador de dados
async function loadReceitas(page = 1) {
  loading.value = true;
  error.value = null;

  try {
    const token = localStorage.getItem("token");
    const response = await fetch(
      `/api/receitas?page=${page}&per_page=${pageSize.value}`,
      {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      }
    );

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }

    const data = await response.json();
    receitas.value = data.data || data;
    currentPage.value = page;
  } catch (err) {
    error.value = `Erro ao carregar receitas: ${err.message}`;
    console.error("Load error:", err);
  } finally {
    loading.value = false;
  }
}

// ✅ Carregar ao montar
onMounted(() => {
  loadReceitas();
});

// ✅ Criar receita
async function addReceita(form: Omit<Receita, "id">) {
  loading.value = true;
  try {
    const token = localStorage.getItem("token");
    const response = await fetch("/api/receitas", {
      method: "POST",
      headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/json",
      },
      body: JSON.stringify(form),
    });

    if (!response.ok) throw new Error("Erro ao criar");

    const newReceita = await response.json();
    receitas.value.push(newReceita.data || newReceita);
    return true;
  } catch (err) {
    error.value = `Erro: ${err.message}`;
    return false;
  } finally {
    loading.value = false;
  }
}

// ✅ Editar receita
async function editReceita(id: number, form: Omit<Receita, "id">) {
  loading.value = true;
  try {
    const token = localStorage.getItem("token");
    const response = await fetch(`/api/receitas/${id}`, {
      method: "PUT",
      headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/json",
      },
      body: JSON.stringify(form),
    });

    if (!response.ok) throw new Error("Erro ao editar");

    const updated = await response.json();
    const index = receitas.value.findIndex((r) => r.id === id);
    if (index !== -1) {
      receitas.value[index] = updated.data || updated;
    }
    return true;
  } catch (err) {
    error.value = `Erro: ${err.message}`;
    return false;
  } finally {
    loading.value = false;
  }
}

// ✅ Deletar receita
async function deleteReceita(id: number) {
  loading.value = true;
  try {
    const token = localStorage.getItem("token");
    const response = await fetch(`/api/receitas/${id}`, {
      method: "DELETE",
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    if (!response.ok) throw new Error("Erro ao deletar");

    receitas.value = receitas.value.filter((r) => r.id !== id);
    return true;
  } catch (err) {
    error.value = `Erro: ${err.message}`;
    return false;
  } finally {
    loading.value = false;
  }
}
</script>

<!-- Template -->
<template>
  <div class="receitas-view">
    <!-- ✅ Error alert -->
    <v-alert v-if="error" type="error" dismissible @click:close="error = null">
      {{ error }}
    </v-alert>

    <!-- ✅ Loading indicator -->
    <v-progress-linear v-if="loading" indeterminate color="primary" />

    <!-- Rest of template... -->
  </div>
</template>
```

---

#### 1.3 Adicionar Interceptor para Token

**Que fazer:**

- [ ] Criar interceptor axios
- [ ] Adicionar token em todas requisições
- [ ] Refresh token automático
- [ ] Redirect a login se expirado

**Código:**

```ts
// lib/api.ts
import axios from "axios";
import { useAuthStore } from "@/stores/auth";
import { useUserStore } from "@/stores/user";

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || "http://localhost/api",
});

// ✅ Request interceptor
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// ✅ Response interceptor
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expirado
      const authStore = useAuthStore();
      const userStore = useUserStore();
      authStore.clear();
      userStore.clear();
      window.location.href = "/login";
    }
    return Promise.reject(error);
  }
);

export default api;
```

**Usar em componentes:**

```vue
<script setup lang="ts">
import api from "@/lib/api";

async function loadReceitas() {
  try {
    const { data } = await api.get("/receitas");
    receitas.value = data.data || data;
  } catch (error) {
    console.error("Error:", error);
  }
}
</script>
```

---

### Critérios de Sucesso Fase 1

- [ ] ReceitasView carrega dados da API
- [ ] DespesasView carrega dados da API
- [ ] Criar, editar, deletar funcionam com API real
- [ ] Filtros funcionam com dados reais
- [ ] Paginação implementada
- [ ] Error handling funciona
- [ ] Token refresh automático
- [ ] Sem erros no console
- [ ] Testes passando

---

## 🎨 Fase 2: Dashboard Avançado (Semana 4)

### Objetivo

Adicionar gráficos e visualizações avançadas ao Dashboard

### Tarefas

#### 2.1 Adicionar Gráficos

**Que fazer:**

- [ ] Instalar Chart.js / ApexCharts
- [ ] Adicionar gráfico de tendência mensal
- [ ] Adicionar gráfico de distribuição por categoria
- [ ] Adicionar gráfico de receitas vs despesas

**Exemplo com Chart.js:**

```vue
<script setup lang="ts">
import { ref, onMounted } from "vue";
import {
  Chart as ChartJS,
  ArcElement,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
} from "chart.js";
import { Line, Doughnut } from "vue-chartjs";

ChartJS.register(
  ArcElement,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
);

const chartData = ref({
  labels: [],
  datasets: [],
});

onMounted(() => {
  // Carregar dados e popular chartData
});
</script>

<template>
  <v-card>
    <v-card-title>Tendência Mensal</v-card-title>
    <Line :data="chartData" :options="chartOptions" />
  </v-card>
</template>
```

---

#### 2.2 Adicionar Resumo

**Que fazer:**

- [ ] Endpoint `/api/resumo` no backend
- [ ] Componente de resumo no dashboard
- [ ] Comparação mês anterior
- [ ] Variação percentual

---

### Critérios de Sucesso Fase 2

- [ ] Dashboard mostra gráficos
- [ ] Gráficos com dados reais
- [ ] Interatividade dos gráficos
- [ ] Responsividade em mobile
- [ ] Performance otimizada

---

## 👥 Fase 3: Views Adicionais (Semana 5)

### Objetivo

Implementar remaining views seguindo mesmo padrão

### Tarefas

#### 3.1 ContasView

- [ ] Listar contas
- [ ] Adicionar conta
- [ ] Editar conta
- [ ] Deletar conta
- [ ] Saldo por conta
- [ ] Filtros e busca

#### 3.2 CategoriasView

- [ ] Listar categorias
- [ ] Adicionar categoria
- [ ] Editar categoria
- [ ] Deletar categoria
- [ ] Cor customizável
- [ ] Ícone customizável

#### 3.3 PerfilView

- [ ] Editar nome
- [ ] Editar email
- [ ] Alterar senha
- [ ] Foto de perfil
- [ ] Preferences de tema
- [ ] Validação de email

#### 3.4 PainelAdmin

- [ ] Listar usuários
- [ ] Editar role do usuário
- [ ] Ativar/desativar usuário
- [ ] Ver logs de atividade
- [ ] Statisticas do sistema
- [ ] Backup de dados

#### 3.5 PainelTrader

- [ ] Dashboard trader
- [ ] Seus trades
- [ ] Performance
- [ ] Relatórios
- [ ] Exportar dados

---

## 🧪 Fase 4: Testes (Semana 6)

### Objetivo

Adicionar testes unitários e integração

### Tarefas

#### 4.1 Testes Unitários

- [ ] Testes para cada componente Vue
- [ ] Testes para stores (Pinia)
- [ ] Testes para utilities
- [ ] Cobertura >80%

**Exemplo:**

```ts
// ReceitasView.spec.ts
import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import ReceitasView from "@/views/receitas/ReceitasView.vue";

describe("ReceitasView", () => {
  it("renders correctly", () => {
    const wrapper = mount(ReceitasView);
    expect(wrapper.exists()).toBe(true);
  });

  it("loads receitas on mount", async () => {
    const wrapper = mount(ReceitasView);
    await wrapper.vm.$nextTick();
    expect(wrapper.vm.receitas.length).toBeGreaterThan(0);
  });

  it("filters receitas by search", async () => {
    const wrapper = mount(ReceitasView);
    wrapper.vm.search = "Salário";
    await wrapper.vm.$nextTick();
    expect(wrapper.vm.filteredReceitas.length).toBeGreaterThan(0);
  });
});
```

#### 4.2 Testes E2E

- [ ] Testes com Cypress/Playwright
- [ ] Login flow
- [ ] CRUD operations
- [ ] Navegação

---

## 📦 Fase 5: Otimização e Deploy (Semana 7)

### Objective

Preparar para produção

### Tarefas

#### 5.1 Performance

- [ ] Lazy loading de rotas
- [ ] Code splitting
- [ ] Image optimization
- [ ] CSS minification
- [ ] JS minification

#### 5.2 Security

- [ ] HTTPS obrigatório
- [ ] CORS configurado
- [ ] CSRF protection
- [ ] XSS prevention
- [ ] SQL injection prevention

#### 5.3 Deployment

- [ ] Build otimizado (`npm run build`)
- [ ] Variáveis de ambiente
- [ ] Docker support
- [ ] CI/CD pipeline
- [ ] Monitoring

**Checklist Pre-Deploy:**

```bash
# Build
npm run build

# Testar produção localmente
npm run preview

# Verificar erros
npm run lint

# Executar testes
npm run test

# Verificar segurança
npm audit
```

---

## 📊 Timeline Estimado

```
Semana 1 (✅ CONCLUÍDO)
├─ Design e arquitetura: 2 dias
├─ MainLayout: 1 dia
├─ ReceitasView: 1 dia
├─ DespesasView: 1 dia
└─ Documentação: 1 dia

Semana 2-3 (🔄 EM PROGRESSO)
├─ Integração API: 3 dias
├─ Testes: 2 dias
└─ Bug fixes: 1 dia

Semana 4 (⏳ PRÓXIMO)
├─ Dashboard avançado: 3 dias
├─ Gráficos: 2 dias
└─ Testes: 1 dia

Semana 5
├─ ContasView: 2 dias
├─ CategoriasView: 2 dias
├─ PerfilView: 1 dia
└─ PainelAdmin/Trader: 2 dias

Semana 6
├─ Testes unitários: 3 dias
├─ Testes E2E: 2 dias
└─ Bug fixes: 1 dia

Semana 7
├─ Otimização: 2 dias
├─ Security: 2 dias
└─ Deploy: 2 dias
```

---

## 🎓 Como Contribuir

### Setup Ambiente

```bash
# Clone
git clone ...
cd Financas

# Backend
cd backend
composer install
php artisan migrate
php artisan serve

# Frontend (nova aba)
cd frontend
npm install
npm run dev
```

### Convenções de Código

**Vue Components:**

- Use `<script setup>` syntax
- TypeScript obrigatório
- Naming: PascalCase
- Props tipadas
- Emits tipadas

**Stores (Pinia):**

- Naming: camelCase
- State, getters, actions
- Sem mutations

**Estilos:**

- SCSS scoped
- BEM naming convention
- Variedadáveis de tema

**Commits:**

```
feat: Adicionar ReceitasView
fix: Corrigir bug de autenticação
docs: Atualizar README
refactor: Refatorar MainLayout
test: Adicionar testes para ReceitasView
```

---

## 📞 Suporte

### Documentação

- `QUICKSTART_NOVO_VISUAL.md` - Getting started
- `TROUBLESHOOTING.md` - Problemas comuns
- `ARQUITETURA_VISUAL.md` - Diagrama de arquitetura
- `INDICE_DOCUMENTACAO.md` - Índice completo

### Comunicação

- [ ] Issues no GitHub
- [ ] Pull requests com review
- [ ] Discussões no Discord

---

## ✅ Checklist Geral

### Antes de cada sessão

- [ ] Git fetch/pull
- [ ] `npm install`
- [ ] `npm run dev`
- [ ] Verificar console
- [ ] Abrir Vue DevTools

### Depois de cada task

- [ ] Commit com mensagem clara
- [ ] Push para branch
- [ ] Pull request se necessary
- [ ] Testes passando
- [ ] Documentação atualizada

### Antes de merge

- [ ] Code review
- [ ] Tests passing
- [ ] No console errors
- [ ] Documentation updated
- [ ] Backward compatible

---

**Versão**: 1.0
**Última atualização**: Outubro 17, 2025
**Próxima revisão**: Outubro 24, 2025
