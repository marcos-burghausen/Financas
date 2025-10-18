<template>
  <div class="receitas-view">
    <!-- Header Section -->
    <div class="view-header mb-6">
      <div class="d-flex align-center justify-space-between flex-wrap gap-3">
        <div>
          <h1 class="text-h4 font-weight-bold d-flex align-center gap-2 mb-2">
            <v-icon icon="mdi-cash-plus" size="32" color="success" />
            Minhas Receitas
          </h1>
          <p class="text-subtitle-2 text-medium-emphasis mb-0">
            Gerencie suas receitas e ganhos
          </p>
        </div>
        <v-btn
          color="success"
          size="large"
          prepend-icon="mdi-plus"
          @click="openAddDialog"
          class="flex-shrink-0"
        >
          Nova Receita
        </v-btn>
      </div>
    </div>

    <!-- Summary Cards -->
    <v-row class="mb-6">
      <v-col cols="12" sm="6" md="3">
        <v-card class="h-100 summary-card success-card" elevation="2">
          <v-card-item>
            <template #prepend>
              <v-avatar color="success" icon="mdi-cash-plus" />
            </template>
            <v-card-title class="text-caption text-medium-emphasis">
              Total do Mês
            </v-card-title>
          </v-card-item>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-success">
              {{ formatCurrency(summary.totalMes || 0) }}
            </div>
            <p class="text-caption text-medium-emphasis mt-2 mb-0">
              {{ formatPercentage(summary.variacaoMes) }}
              <v-icon :icon="summary.variacaoMes >= 0 ? 'mdi-trending-up' : 'mdi-trending-down'" :color="summary.variacaoMes >= 0 ? 'success' : 'error'" size="x-small" />
            </p>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card class="h-100 summary-card info-card" elevation="2">
          <v-card-item>
            <template #prepend>
              <v-avatar color="info" icon="mdi-calendar-check" />
            </template>
            <v-card-title class="text-caption text-medium-emphasis">
              Recebidas
            </v-card-title>
          </v-card-item>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-info">
              {{ receitasRecebidas }}
            </div>
            <p class="text-caption text-medium-emphasis mt-2 mb-0">
              {{ formatCurrency(somaRecebidas) }}
            </p>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card class="h-100 summary-card warning-card" elevation="2">
          <v-card-item>
            <template #prepend>
              <v-avatar color="warning" icon="mdi-clock-outline" />
            </template>
            <v-card-title class="text-caption text-medium-emphasis">
              Pendentes
            </v-card-title>
          </v-card-item>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-warning">
              {{ receitasPendentes }}
            </div>
            <p class="text-caption text-medium-emphasis mt-2 mb-0">
              {{ formatCurrency(somaPendentes) }}
            </p>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card class="h-100 summary-card error-card" elevation="2">
          <v-card-item>
            <template #prepend>
              <v-avatar color="error" icon="mdi-calendar-remove" />
            </template>
            <v-card-title class="text-caption text-medium-emphasis">
              Atrasadas
            </v-card-title>
          </v-card-item>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-error">
              {{ receitasAtrasadas }}
            </div>
            <p class="text-caption text-medium-emphasis mt-2 mb-0">
              {{ formatCurrency(somaAtrasadas) }}
            </p>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Filters and Controls -->
    <v-card class="mb-6" elevation="1">
      <v-card-text class="pa-4">
        <v-row class="align-center" dense>
          <v-col cols="12" sm="6" md="3">
            <v-text-field
              v-model="searchText"
              label="Buscar"
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              density="compact"
              clearable
            />
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-select
              v-model="selectedStatus"
              label="Status"
              :items="statusOptions"
              variant="outlined"
              density="compact"
              clearable
            />
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-select
              v-model="selectedCategoria"
              label="Categoria"
              :items="categorias"
              variant="outlined"
              density="compact"
              clearable
            />
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-btn
              color="primary"
              variant="outlined"
              block
              @click="resetFilters"
            >
              Limpar Filtros
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Receitas Table -->
    <v-card elevation="1">
      <v-data-table
        :headers="headers"
        :items="filteredReceitas"
        :loading="loading"
        :items-per-page="itemsPerPage"
        class="receitas-table"
      >
        <!-- Data columns -->
        <template #item.descricao="{ item }">
          <div class="d-flex align-center gap-2">
            <v-avatar size="32" color="success" variant="tonal" icon="mdi-receipt" />
            <div>
              <div class="font-weight-500">{{ item.descricao }}</div>
              <div class="text-caption text-medium-emphasis">
                {{ formatDate(item.data_vencimento) }}
              </div>
            </div>
          </div>
        </template>

        <template #item.valor="{ item }">
          <div class="text-right font-weight-bold text-success">
            {{ formatCurrency(item.valor) }}
          </div>
        </template>

        <template #item.categoria="{ item }">
          <v-chip size="small" variant="outlined">
            {{ item.categoria }}
          </v-chip>
        </template>

        <template #item.status="{ item }">
          <v-chip
            :color="getStatusColor(item.status)"
            :text-color="getStatusTextColor(item.status)"
            size="small"
          >
            {{ getStatusLabel(item.status) }}
          </v-chip>
        </template>

        <template #item.acoes="{ item }">
          <div class="d-flex gap-2 justify-end">
            <v-btn
              icon="mdi-pencil"
              variant="text"
              size="small"
              color="primary"
              @click="editReceita(item)"
            />
            <v-btn
              icon="mdi-delete"
              variant="text"
              size="small"
              color="error"
              @click="deleteReceita(item.id)"
            />
          </div>
        </template>

        <!-- No data template -->
        <template #no-data>
          <div class="text-center py-8">
            <v-icon icon="mdi-inbox" size="48" color="medium-emphasis" class="mb-4 d-block" />
            <p class="text-subtitle-1 text-medium-emphasis">Nenhuma receita encontrada</p>
          </div>
        </template>
      </v-data-table>
    </v-card>

    <!-- Add/Edit Dialog -->
    <v-dialog v-model="dialog" max-width="600">
      <v-card>
        <!-- Dialog Header -->
        <div class="dialog-header bg-success">
          <div class="d-flex justify-space-between align-center">
            <h2 class="text-h6 text-white font-weight-bold">
              {{ editingId ? 'Editar Receita' : 'Nova Receita' }}
            </h2>
            <v-btn
              icon="mdi-close"
              variant="text"
              @click="dialog = false"
            />
          </div>
        </div>

        <!-- Dialog Content -->
        <v-card-text class="pa-6">
          <v-form @submit.prevent="saveReceita">
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="formData.descricao"
                  label="Descrição *"
                  prepend-inner-icon="mdi-text"
                  variant="outlined"
                  :rules="[rules.required]"
                />
              </v-col>

              <v-col cols="12" md="6">
                <v-select
                  v-model="formData.categoria"
                  label="Categoria *"
                  prepend-inner-icon="mdi-tag"
                  variant="outlined"
                  :items="categorias"
                  :rules="[rules.required]"
                />
              </v-col>

              <v-col cols="12" md="6">
                <v-select
                  v-model="formData.conta"
                  label="Conta *"
                  prepend-inner-icon="mdi-bank"
                  variant="outlined"
                  :items="contas"
                  :rules="[rules.required]"
                />
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model.number="formData.valor"
                  label="Valor *"
                  prepend-inner-icon="mdi-currency-brl"
                  variant="outlined"
                  type="number"
                  step="0.01"
                  :rules="[rules.required]"
                />
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="formData.data_vencimento"
                  label="Data de Vencimento *"
                  prepend-inner-icon="mdi-calendar"
                  variant="outlined"
                  type="date"
                  :rules="[rules.required]"
                />
              </v-col>

              <v-col cols="12">
                <v-select
                  v-model="formData.status"
                  label="Status *"
                  prepend-inner-icon="mdi-checkbox-marked-circle"
                  variant="outlined"
                  :items="statusOptions"
                  :rules="[rules.required]"
                />
              </v-col>

              <v-col cols="12">
                <v-textarea
                  v-model="formData.observacao"
                  label="Observação"
                  prepend-inner-icon="mdi-note"
                  variant="outlined"
                  rows="3"
                />
              </v-col>
            </v-row>

            <div class="d-flex gap-2 justify-end mt-6">
              <v-btn variant="outlined" @click="dialog = false">
                Cancelar
              </v-btn>
              <v-btn color="success" type="submit">
                {{ editingId ? 'Atualizar' : 'Adicionar' }}
              </v-btn>
            </div>
          </v-form>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';

// State
const dialog = ref(false);
const loading = ref(false);
const searchText = ref('');
const selectedStatus = ref('');
const selectedCategoria = ref('');
const editingId = ref<number | null>(null);
const itemsPerPage = ref(10);

// Mock data
const receitas = ref([
  { id: 1, descricao: 'Salário', valor: 5000, categoria: 'Salário', conta: 'Conta Principal', data_vencimento: '2025-10-01', status: 'recebida', observacao: 'Salário mensal' },
  { id: 2, descricao: 'Freelancer', valor: 1200, categoria: 'Freelancer', conta: 'Conta Principal', data_vencimento: '2025-10-05', status: 'recebida', observacao: 'Projeto web' },
  { id: 3, descricao: 'Bonus', valor: 800, categoria: 'Bonus', conta: 'Conta Principal', data_vencimento: '2025-10-20', status: 'pendente', observacao: 'Bonus do mês' },
  { id: 4, descricao: 'Investimento', valor: 500, categoria: 'Investimento', conta: 'Conta Investimento', data_vencimento: '2025-10-10', status: 'pendente', observacao: 'Rendimento' },
]);

const categorias = ref(['Salário', 'Freelancer', 'Bonus', 'Investimento', 'Outros']);
const contas = ref(['Conta Principal', 'Conta Investimento', 'Poupança']);
const statusOptions = ref([
  { title: 'Recebida', value: 'recebida' },
  { title: 'Pendente', value: 'pendente' },
  { title: 'Cancelada', value: 'cancelada' },
]);

// Form data
const formData = ref({
  descricao: '',
  categoria: '',
  conta: '',
  valor: 0,
  data_vencimento: '',
  status: 'pendente',
  observacao: '',
});

// Validation rules
const rules = {
  required: (v: any) => !!v || 'Campo obrigatório',
};

// Headers da tabela
const headers = [
  { title: 'Descrição', align: 'start', key: 'descricao', width: '35%' },
  { title: 'Categoria', align: 'start', key: 'categoria', width: '15%' },
  { title: 'Valor', align: 'end', key: 'valor', width: '15%' },
  { title: 'Status', align: 'center', key: 'status', width: '15%' },
  { title: 'Ações', align: 'end', key: 'acoes', width: '10%', sortable: false },
];

// Summary computed
const summary = computed(() => ({
  totalMes: receitas.value.reduce((sum, r) => sum + r.valor, 0),
  variacaoMes: 5.2,
}));

const receitasRecebidas = computed(() => receitas.value.filter(r => r.status === 'recebida').length);
const somaRecebidas = computed(() => receitas.value.filter(r => r.status === 'recebida').reduce((sum, r) => sum + r.valor, 0));

const receitasPendentes = computed(() => receitas.value.filter(r => r.status === 'pendente').length);
const somaPendentes = computed(() => receitas.value.filter(r => r.status === 'pendente').reduce((sum, r) => sum + r.valor, 0));

const receitasAtrasadas = computed(() => receitas.value.filter(r => r.status === 'cancelada').length);
const somaAtrasadas = computed(() => receitas.value.filter(r => r.status === 'cancelada').reduce((sum, r) => sum + r.valor, 0));

// Filtered receitas
const filteredReceitas = computed(() => {
  return receitas.value.filter(r => {
    const matchText = !searchText.value || r.descricao.toLowerCase().includes(searchText.value.toLowerCase());
    const matchStatus = !selectedStatus.value || r.status === selectedStatus.value;
    const matchCategoria = !selectedCategoria.value || r.categoria === selectedCategoria.value;
    return matchText && matchStatus && matchCategoria;
  });
});

// Methods
const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(value);
};

const formatPercentage = (value: number) => {
  return `${value >= 0 ? '+' : ''}${value.toFixed(1)}%`;
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('pt-BR');
};

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    recebida: 'success',
    pendente: 'warning',
    cancelada: 'error',
  };
  return colors[status] || 'default';
};

const getStatusTextColor = (status: string) => {
  return 'white';
};

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    recebida: 'Recebida',
    pendente: 'Pendente',
    cancelada: 'Cancelada',
  };
  return labels[status] || status;
};

const openAddDialog = () => {
  editingId.value = null;
  formData.value = {
    descricao: '',
    categoria: '',
    conta: '',
    valor: 0,
    data_vencimento: '',
    status: 'pendente',
    observacao: '',
  };
  dialog.value = true;
};

const editReceita = (receita: any) => {
  editingId.value = receita.id;
  formData.value = { ...receita };
  dialog.value = true;
};

const deleteReceita = (id: number) => {
  if (confirm('Tem certeza que deseja deletar esta receita?')) {
    receitas.value = receitas.value.filter(r => r.id !== id);
  }
};

const saveReceita = () => {
  if (editingId.value) {
    // Update
    const index = receitas.value.findIndex(r => r.id === editingId.value);
    if (index !== -1) {
      receitas.value[index] = { ...formData.value, id: editingId.value };
    }
  } else {
    // Create
    const newId = Math.max(...receitas.value.map(r => r.id), 0) + 1;
    receitas.value.push({ ...formData.value, id: newId });
  }
  dialog.value = false;
};

const resetFilters = () => {
  searchText.value = '';
  selectedStatus.value = '';
  selectedCategoria.value = '';
};

onMounted(() => {
  // Carregar dados da API aqui
});
</script>

<style scoped lang="scss">
.receitas-view {
  .view-header {
    @media (max-width: 600px) {
      .d-flex {
        flex-direction: column;
        align-items: flex-start;
        
        .v-btn {
          width: 100%;
        }
      }
    }
  }

  .summary-card {
    transition: all 0.3s ease;
    border-left: 4px solid;

    &.success-card {
      border-left-color: rgb(var(--v-theme-success));
    }

    &.info-card {
      border-left-color: rgb(var(--v-theme-info));
    }

    &.warning-card {
      border-left-color: rgb(var(--v-theme-warning));
    }

    &.error-card {
      border-left-color: rgb(var(--v-theme-error));
    }

    &:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
  }

  .dialog-header {
    padding: 1.5rem;
    border-radius: 4px 4px 0 0;
  }

  .receitas-table {
    :deep(.v-data-table__wrapper) {
      border-radius: 4px;
    }
  }
}
</style>
