<template>
  <v-layout>
    <!-- Navigation Drawer -->
    <v-navigation-drawer
      v-model="drawer"
      temporary
      color="#212529"
      width="280"
    >
      <v-list>
        <v-list-item
          v-for="(item, index) in filteredItensSideBar"
          :key="index"
          :to="{ name: item.route }"
          :class="{ 'bg-primary': isActiveRoute(item.route) }"
        >
          <template #prepend>
            <v-icon :icon="item.icon" />
          </template>
          <v-list-item-title>{{ item.name }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <!-- Main Content -->
    <v-main>
      <v-container
        fluid
        class="receitas-view pa-6"
      >
        <!-- Header -->
        <v-row class="mb-4">
          <v-col cols="12">
            <div class="d-flex align-center justify-space-between flex-wrap gap-3 mb-2">
              <div class="d-flex align-center flex-grow-1">
                <v-btn
                  icon
                  variant="text"
                  class="mr-2 d-lg-none menu-button"
                  @click="drawer = !drawer"
                >
                  <v-icon
                    icon="mdi-menu"
                    size="28"
                  />
                </v-btn>
                <div class="header-content">
                  <h1 class="receitas-title mb-1 d-flex align-center">
                    <v-icon 
                      icon="mdi-cash-plus" 
                      :size="$vuetify.display.xs ? '24' : '36'" 
                      class="mr-2 mr-md-3" 
                      color="success" 
                    />
                    <span class="d-none d-sm-inline">Minhas Receitas</span>
                    <span class="d-sm-none">Receitas</span>
                  </h1>
                  <p class="text-caption text-sm-subtitle-1 text-grey mb-0 d-none d-sm-block">
                    Gerencie suas receitas e ganhos
                  </p>
                </div>
              </div>
              <v-btn
                color="success"
                :prepend-icon="$vuetify.display.xs ? '' : 'mdi-plus'"
                :icon="$vuetify.display.xs ? 'mdi-plus' : false"
                :size="$vuetify.display.xs ? 'default' : 'large'"
                class="flex-shrink-0"
                @click="openAddDialog"
              >
                <span v-if="!$vuetify.display.xs">Nova Receita</span>
              </v-btn>
            </div>
          </v-col>
        </v-row>

        <!-- Summary Cards -->
        <v-row class="mb-4">
          <v-col
            cols="12"
            sm="6"
            md="3"
          >
            <v-card
              elevation="4"
              class="summary-card h-100"
            >
              <div class="card-gradient card-gradient-success pa-4">
                <div class="d-flex justify-space-between align-start mb-3">
                  <div>
                    <p class="text-body-2 text-white mb-1">
                      Total do Mês
                    </p>
                    <h2 class="text-h5 text-white font-weight-bold">
                      {{ formatCurrency(summary.totalMes) }}
                    </h2>
                  </div>
                  <v-avatar
                    color="rgba(255,255,255,0.2)"
                    size="48"
                  >
                    <v-icon
                      icon="mdi-cash-multiple"
                      color="white"
                      size="28"
                    />
                  </v-avatar>
                </div>
                <v-chip
                  size="small"
                  color="white"
                  text-color="success"
                  class="font-weight-medium"
                >
                  <v-icon
                    icon="mdi-calendar-month"
                    start
                    size="16"
                  />
                  {{ summary.qtdMes }} receitas
                </v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col
            cols="12"
            sm="6"
            md="3"
          >
            <v-card
              elevation="4"
              class="summary-card h-100"
            >
              <div class="card-gradient card-gradient-info pa-4">
                <div class="d-flex justify-space-between align-start mb-3">
                  <div>
                    <p class="text-body-2 text-white mb-1">
                      Recebido
                    </p>
                    <h2 class="text-h5 text-white font-weight-bold">
                      {{ formatCurrency(summary.recebido) }}
                    </h2>
                  </div>
                  <v-avatar
                    color="rgba(255,255,255,0.2)"
                    size="48"
                  >
                    <v-icon
                      icon="mdi-check-circle"
                      color="white"
                      size="28"
                    />
                  </v-avatar>
                </div>
                <v-chip
                  size="small"
                  color="white"
                  text-color="info"
                  class="font-weight-medium"
                >
                  <v-icon
                    icon="mdi-trending-up"
                    start
                    size="16"
                  />
                  {{ summary.qtdRecebido }} recebidas
                </v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col
            cols="12"
            sm="6"
            md="3"
          >
            <v-card
              elevation="4"
              class="summary-card h-100"
            >
              <div class="card-gradient card-gradient-warning pa-4">
                <div class="d-flex justify-space-between align-start mb-3">
                  <div>
                    <p class="text-body-2 text-white mb-1">
                      Pendente
                    </p>
                    <h2 class="text-h5 text-white font-weight-bold">
                      {{ formatCurrency(summary.pendente) }}
                    </h2>
                  </div>
                  <v-avatar
                    color="rgba(255,255,255,0.2)"
                    size="48"
                  >
                    <v-icon
                      icon="mdi-clock-alert"
                      color="white"
                      size="28"
                    />
                  </v-avatar>
                </div>
                <v-chip
                  size="small"
                  color="white"
                  text-color="warning"
                  class="font-weight-medium"
                >
                  <v-icon
                    icon="mdi-alert-circle"
                    start
                    size="16"
                  />
                  {{ summary.qtdPendente }} a receber
                </v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col
            cols="12"
            sm="6"
            md="3"
          >
            <v-card
              elevation="4"
              class="summary-card h-100"
            >
              <div class="card-gradient card-gradient-primary pa-4">
                <div class="d-flex justify-space-between align-start mb-3">
                  <div>
                    <p class="text-body-2 text-white mb-1">
                      Média Mensal
                    </p>
                    <h2 class="text-h5 text-white font-weight-bold">
                      {{ formatCurrency(summary.mediaMensal) }}
                    </h2>
                  </div>
                  <v-avatar
                    color="rgba(255,255,255,0.2)"
                    size="48"
                  >
                    <v-icon
                      icon="mdi-chart-line"
                      color="white"
                      size="28"
                    />
                  </v-avatar>
                </div>
                <v-chip
                  size="small"
                  color="white"
                  text-color="primary"
                  class="font-weight-medium"
                >
                  <v-icon
                    icon="mdi-chart-timeline-variant"
                    start
                    size="16"
                  />
                  Últimos 3 meses
                </v-chip>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <!-- Filters and Actions -->
        <v-row class="mb-4">
          <v-col cols="12">
            <v-card
              elevation="2"
              class="pa-4"
            >
              <v-row>
                <v-col
                  cols="12"
                  md="3"
                >
                  <v-text-field
                    v-model="filters.search"
                    label="Buscar"
                    prepend-inner-icon="mdi-magnify"
                    variant="outlined"
                    density="compact"
                    clearable
                    hide-details
                  />
                </v-col>
                <v-col
                  cols="12"
                  md="2"
                >
                  <v-select
                    v-model="filters.status"
                    label="Status"
                    prepend-inner-icon="mdi-filter"
                    variant="outlined"
                    density="compact"
                    :items="statusOptions"
                    clearable
                    hide-details
                  />
                </v-col>
                <v-col
                  cols="12"
                  md="3"
                >
                  <v-select
                    v-model="filters.categoria"
                    label="Categoria"
                    prepend-inner-icon="mdi-tag"
                    variant="outlined"
                    density="compact"
                    :items="categorias"
                    clearable
                    hide-details
                  />
                </v-col>
                <v-col
                  cols="12"
                  md="2"
                >
                  <v-text-field
                    v-model="filters.dataInicio"
                    label="Data Início"
                    type="date"
                    variant="outlined"
                    density="compact"
                    hide-details
                  />
                </v-col>
                <v-col
                  cols="12"
                  md="2"
                >
                  <v-text-field
                    v-model="filters.dataFim"
                    label="Data Fim"
                    type="date"
                    variant="outlined"
                    density="compact"
                    hide-details
                  />
                </v-col>
              </v-row>
            </v-card>
          </v-col>
        </v-row>

        <!-- Loading -->
        <v-row v-if="loading">
          <v-col
            cols="12"
            class="text-center py-12"
          >
            <v-progress-circular
              indeterminate
              color="success"
              size="64"
            />
            <p class="text-grey mt-4">
              Carregando receitas...
            </p>
          </v-col>
        </v-row>

        <!-- Empty State -->
        <v-row v-else-if="filteredReceitas.length === 0">
          <v-col cols="12">
            <v-card
              elevation="2"
              class="text-center pa-12"
            >
              <v-icon
                icon="mdi-cash-plus"
                size="80"
                color="grey-lighten-1"
              />
              <h3 class="text-h5 mt-4 mb-2">
                Nenhuma receita encontrada
              </h3>
              <p class="text-grey mb-4">
                {{ filters.search || filters.status || filters.categoria 
                  ? 'Tente ajustar os filtros ou adicionar uma nova receita' 
                  : 'Adicione sua primeira receita para começar a acompanhar seus ganhos' }}
              </p>
              <v-btn
                color="success"
                size="large"
                prepend-icon="mdi-plus"
                @click="openAddDialog"
              >
                Adicionar Receita
              </v-btn>
            </v-card>
          </v-col>
        </v-row>

        <!-- Receitas Table -->
        <v-row v-else>
          <v-col cols="12">
            <v-card elevation="4">
              <div class="card-gradient card-gradient-success pa-4">
                <div class="d-flex align-center justify-space-between">
                  <div class="d-flex align-center">
                    <v-icon
                      icon="mdi-format-list-bulleted"
                      size="28"
                      color="white"
                      class="mr-3"
                    />
                    <div>
                      <h3 class="text-h6 text-white font-weight-bold">
                        Listagem de Receitas
                      </h3>
                      <p class="text-body-2 text-white opacity-90 mb-0">
                        {{ filteredReceitas.length }} {{ filteredReceitas.length === 1 ? 'receita' : 'receitas' }}
                      </p>
                    </div>
                  </div>
                  <div class="d-flex gap-2">
                    <v-btn
                      variant="text"
                      color="white"
                      prepend-icon="mdi-file-excel"
                      size="small"
                      @click="exportToExcel"
                    >
                      Excel
                    </v-btn>
                    <v-btn
                      variant="text"
                      color="white"
                      prepend-icon="mdi-file-pdf-box"
                      size="small"
                      @click="exportToPDF"
                    >
                      PDF
                    </v-btn>
                  </div>
                </div>
              </div>

              <v-data-table
                :headers="headers"
                :items="filteredReceitas"
                :items-per-page="10"
                class="elevation-0"
                :loading="loading"
              >
                <!-- Data -->
                <template #item.data_vencimento="{ item }">
                  <div class="d-flex align-center">
                    <v-icon 
                      :icon="isVencida(item.data_vencimento) ? 'mdi-alert-circle' : 'mdi-calendar'" 
                      :color="isVencida(item.data_vencimento) ? 'error' : 'grey'"
                      size="18"
                      class="mr-2"
                    />
                    {{ formatDate(item.data_vencimento) }}
                  </div>
                </template>

                <!-- Descrição -->
                <template #item.descricao="{ item }">
                  <div class="py-2">
                    <div class="font-weight-medium">
                      {{ item.descricao }}
                    </div>
                    <div class="text-caption text-grey">
                      {{ item.conta }}
                    </div>
                  </div>
                </template>

                <!-- Categoria -->
                <template #item.categoria="{ item }">
                  <v-chip
                    size="small"
                    variant="tonal"
                    color="success"
                  >
                    <v-icon
                      :icon="getCategoryIcon(item.categoria)"
                      start
                      size="16"
                    />
                    {{ item.categoria }}
                  </v-chip>
                </template>

                <!-- Status -->
                <template #item.status="{ item }">
                  <v-chip
                    size="small"
                    :color="getStatusColor(item.status)"
                    variant="flat"
                    class="font-weight-medium"
                  >
                    <v-icon
                      :icon="getStatusIcon(item.status)"
                      start
                      size="16"
                    />
                    {{ item.status }}
                  </v-chip>
                </template>

                <!-- Valor -->
                <template #item.valor="{ item }">
                  <div class="text-success font-weight-bold text-end">
                    {{ formatCurrency(item.valor) }}
                  </div>
                </template>

                <!-- Actions -->
                <template #item.actions="{ item }">
                  <div class="d-flex gap-1">
                    <v-tooltip
                      text="Marcar como Recebida"
                      location="top"
                    >
                      <template #activator="{ props }">
                        <v-btn
                          v-if="item.status === 'PENDENTE'"
                          icon
                          variant="text"
                          size="small"
                          color="success"
                          v-bind="props"
                          @click="marcarRecebida(item)"
                        >
                          <v-icon icon="mdi-check-circle" />
                        </v-btn>
                      </template>
                    </v-tooltip>
                    
                    <v-tooltip
                      text="Editar"
                      location="top"
                    >
                      <template #activator="{ props }">
                        <v-btn
                          icon
                          variant="text"
                          size="small"
                          color="primary"
                          v-bind="props"
                          @click="editReceita(item)"
                        >
                          <v-icon icon="mdi-pencil" />
                        </v-btn>
                      </template>
                    </v-tooltip>

                    <v-tooltip
                      text="Excluir"
                      location="top"
                    >
                      <template #activator="{ props }">
                        <v-btn
                          icon
                          variant="text"
                          size="small"
                          color="error"
                          v-bind="props"
                          @click="deleteReceita(item)"
                        >
                          <v-icon icon="mdi-delete" />
                        </v-btn>
                      </template>
                    </v-tooltip>
                  </div>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>

        <!-- Add/Edit Dialog -->
        <v-dialog
          v-model="dialog"
          max-width="700"
        >
          <v-card>
            <div class="card-gradient card-gradient-success pa-4">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center">
                  <v-icon
                    icon="mdi-cash-plus"
                    size="32"
                    color="white"
                    class="mr-3"
                  />
                  <h2 class="text-h5 text-white font-weight-bold">
                    {{ editMode ? 'Editar Receita' : 'Nova Receita' }}
                  </h2>
                </div>
                <v-btn
                  icon
                  variant="text"
                  @click="dialog = false"
                >
                  <v-icon
                    icon="mdi-close"
                    color="white"
                  />
                </v-btn>
              </div>
            </div>

            <v-card-text class="pa-6">
              <v-form
                ref="form"
                @submit.prevent="saveReceita"
              >
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

                  <v-col
                    cols="12"
                    md="6"
                  >
                    <v-select
                      v-model="formData.categoria"
                      label="Categoria *"
                      prepend-inner-icon="mdi-tag"
                      variant="outlined"
                      :items="categorias"
                      :rules="[rules.required]"
                    />
                  </v-col>

                  <v-col
                    cols="12"
                    md="6"
                  >
                    <v-select
                      v-model="formData.conta"
                      label="Conta *"
                      prepend-inner-icon="mdi-bank"
                      variant="outlined"
                      :items="contas"
                      :rules="[rules.required]"
                    />
                  </v-col>

                  <v-col
                    cols="12"
                    md="6"
                  >
                    <v-text-field
                      v-model="formData.valor"
                      label="Valor *"
                      prepend-inner-icon="mdi-currency-brl"
                      variant="outlined"
                      type="number"
                      step="0.01"
                      :rules="[rules.required]"
                    />
                  </v-col>

                  <v-col
                    cols="12"
                    md="6"
                  >
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
                      prepend-inner-icon="mdi-note-text"
                      variant="outlined"
                      rows="3"
                    />
                  </v-col>
                </v-row>
              </v-form>
            </v-card-text>

            <v-card-actions class="pa-6 pt-0">
              <v-spacer />
              <v-btn
                variant="text"
                @click="dialog = false"
              >
                Cancelar
              </v-btn>
              <v-btn
                color="success"
                :loading="saving"
                @click="saveReceita"
              >
                {{ editMode ? 'Salvar' : 'Adicionar' }}
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <!-- Snackbar -->
        <v-snackbar
          v-model="snackbar.show"
          :color="snackbar.color"
          :timeout="3000"
          location="top right"
        >
          {{ snackbar.message }}
          <template #actions>
            <v-btn
              variant="text"
              @click="snackbar.show = false"
            >
              Fechar
            </v-btn>
          </template>
        </v-snackbar>
      </v-container>
    </v-main>
  </v-layout>
</template>

<script setup lang="ts">
import axiosInstance from "@/services/http";
import {
  useExpensesStore,
  useRevenuesStore,
  useRolesStore,
  useUserStore,
  useWalletsStore,
} from "@/store";
import { computed, onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";

// Router
const router = useRouter();
const route = useRoute();

// Stores
const rolesStore = useRolesStore();
const revenuesStore = useRevenuesStore();
const walletsStore = useWalletsStore();
const expensesStore = useExpensesStore();
const userStore = useUserStore();

// Drawer state
const drawer = ref(false);

// Menu items
const itensSideBar = ref([
  { name: "Admin", icon: "mdi-shield-crown", route: "admin", adminOnly: true },
  { name: "Trader", icon: "mdi-chart-line", route: "trader", traderOnly: true },
  { name: "Dashboard", icon: "mdi-view-dashboard", route: "dashboard" },
  { name: "Contas", icon: "mdi-bank", route: "contas" },
  { name: "Receitas", icon: "mdi-cash-plus", route: "receitas" },
  { name: "Despesas", icon: "mdi-cash-minus", route: "despesas" },
  { name: "Categorias", icon: "mdi-tag-multiple", route: "categorias" },
  { name: "Cartões de Crédito", icon: "mdi-credit-card-outline", route: "cartoes" },
  { name: "Notificações", icon: "mdi-bell", route: "notificacoes" },
  { name: "Perfil", icon: "mdi-account", route: "perfil" },
]);

const filteredItensSideBar = computed(() => {
  return itensSideBar.value.filter((item) => {
    if (item.adminOnly && !rolesStore.isAdmin) return false;
    if (item.traderOnly && !rolesStore.isTrader) return false;
    return true;
  });
});

const isActiveRoute = (routeName: string): boolean => {
  return route.name === routeName;
};

// States
const loading = ref(true);
const dialog = ref(false);
const editMode = ref(false);
const saving = ref(false);

// Snackbar
const snackbar = ref({
  show: false,
  message: "",
  color: "success"
});

// Filters
const filters = ref({
  search: "",
  status: null,
  categoria: null,
  dataInicio: "",
  dataFim: ""
});

// Data
const receitas = ref<any[]>([]);
const formData = ref({
  id: null,
  descricao: "",
  categoria: "",
  conta: "",
  valor: 0,
  data_vencimento: "",
  status: "PENDENTE",
  observacao: ""
});

// Options
const statusOptions = ["PENDENTE", "RECEBIDO", "ATRASADO"];
const categorias = ["Salário", "Freelance", "Investimentos", "Vendas", "Aluguel", "Outros"];
const contas = ["Nubank", "Banco do Brasil", "Caixa", "Inter"];

// Validation rules
const rules = {
  required: (v: any) => !!v || "Campo obrigatório"
};

// Table headers
const headers = [
  { title: "Data", key: "data_vencimento", sortable: true },
  { title: "Descrição", key: "descricao", sortable: true },
  { title: "Categoria", key: "categoria", sortable: true },
  { title: "Status", key: "status", sortable: true },
  { title: "Valor", key: "valor", sortable: true, align: "end" },
  { title: "Ações", key: "actions", sortable: false, align: "center" }
];



// Computed
const summary = computed(() => {
  // const totalMes = receitas.value.reduce((sum, r) => sum + r.valor, 0)
  const totalMes = revenuesStore.revenuesData?.valueTotalMonth;
  const recebido = revenuesStore.revenuesData?.valuePay;
  const pendente = revenuesStore.revenuesData?.valuePending;
  
  return {
    totalMes,
    recebido,
    pendente,
    mediaMensal: totalMes, // Simplificado - deveria calcular média real
    qtdMes: revenuesStore.revenuesData?.byMonth.length,
    qtdRecebido: receitas.value.filter(r => r.status === "RECEBIDO").length,
    qtdPendente: receitas.value.filter(r => r.status === "PENDENTE").length
  };
});

const filteredReceitas = computed(() => {
  return receitas.value.filter(receita => {
    if (filters.value.search && !receita.descricao.toLowerCase().includes(filters.value.search.toLowerCase())) {
      return false;
    }
    if (filters.value.status && receita.status !== filters.value.status) {
      return false;
    }
    if (filters.value.categoria && receita.categoria !== filters.value.categoria) {
      return false;
    }
    if (filters.value.dataInicio && receita.data_vencimento < filters.value.dataInicio) {
      return false;
    }
    if (filters.value.dataFim && receita.data_vencimento > filters.value.dataFim) {
      return false;
    }
    return true;
  });
});

// Methods
const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL"
  }).format(value / 100);
};

const formatDate = (date: string): string => {
  return new Intl.DateTimeFormat("pt-BR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric"
  }).format(new Date(date));
};

const isVencida = (date: string): boolean => {
  return new Date(date) < new Date();
};

const getCategoryIcon = (categoria: string): string => {
  const icons: Record<string, string> = {
    "Salário": "mdi-briefcase",
    "Freelance": "mdi-laptop",
    "Investimentos": "mdi-chart-line",
    "Vendas": "mdi-cart",
    "Aluguel": "mdi-home",
    "Outros": "mdi-dots-horizontal"
  };
  return icons[categoria] || "mdi-tag";
};

const getStatusColor = (status: string): string => {
  const colors: Record<string, string> = {
    "RECEBIDO": "success",
    "PENDENTE": "warning",
    "ATRASADO": "error"
  };
  return colors[status] || "grey";
};

const getStatusIcon = (status: string): string => {
  const icons: Record<string, string> = {
    "RECEBIDO": "mdi-check-circle",
    "PENDENTE": "mdi-clock-alert",
    "ATRASADO": "mdi-alert-circle"
  };
  return icons[status] || "mdi-help-circle";
};

const openAddDialog = () => {
  editMode.value = false;
  formData.value = {
    id: null,
    descricao: "",
    categoria: "",
    conta: "",
    valor: 0,
    data_vencimento: "",
    status: "PENDENTE",
    observacao: ""
  };
  dialog.value = true;
};

const editReceita = (receita: any) => {
  editMode.value = true;
  formData.value = { ...receita };
  dialog.value = true;
};

const deleteReceita = async (receita: any) => {
  if (!confirm(`Deseja realmente excluir a receita "${receita.descricao}"?`)) return;

  try {
    await axiosInstance.delete(`/receitas/${receita.id}`);
    
    receitas.value = receitas.value.filter(r => r.id !== receita.id);
    
    snackbar.value = {
      show: true,
      message: "Receita excluída com sucesso!",
      color: "success"
    };
  } catch (error: any) {
    console.error("Erro ao excluir receita:", error);
    snackbar.value = {
      show: true,
      message: error.response?.data?.message || "Erro ao excluir receita",
      color: "error"
    };
  }
};

const marcarRecebida = async (receita: any) => {
  try {
    await axiosInstance.patch(`/receitas/${receita.id}/receber`);
    
    const index = receitas.value.findIndex(r => r.id === receita.id);
    if (index !== -1) {
      receitas.value[index].status = "RECEBIDO";
    }
    
    snackbar.value = {
      show: true,
      message: "Receita marcada como recebida!",
      color: "success"
    };
  } catch (error: any) {
    console.error("Erro ao marcar receita:", error);
    snackbar.value = {
      show: true,
      message: error.response?.data?.message || "Erro ao marcar receita",
      color: "error"
    };
  }
};

const saveReceita = async () => {
  try {
    saving.value = true;
    
    if (editMode.value) {
      await axiosInstance.put(`/receitas/${formData.value.id}`, formData.value);
      
      const index = receitas.value.findIndex(r => r.id === formData.value.id);
      if (index !== -1) {
        receitas.value[index] = { ...formData.value };
      }
      
      snackbar.value = {
        show: true,
        message: "Receita atualizada com sucesso!",
        color: "success"
      };
    } else {
      const response = await axiosInstance.post("/receitas", formData.value);
      receitas.value.push(response.data);
      
      snackbar.value = {
        show: true,
        message: "Receita adicionada com sucesso!",
        color: "success"
      };
    }
    
    dialog.value = false;
  } catch (error: any) {
    console.error("Erro ao salvar receita:", error);
    snackbar.value = {
      show: true,
      message: error.response?.data?.message || "Erro ao salvar receita",
      color: "error"
    };
  } finally {
    saving.value = false;
  }
};

const exportToExcel = () => {
  console.log("Exportar para Excel");
  snackbar.value = {
    show: true,
    message: "Funcionalidade em desenvolvimento",
    color: "info"
  };
};

const exportToPDF = () => {
  console.log("Exportar para PDF");
  snackbar.value = {
    show: true,
    message: "Funcionalidade em desenvolvimento",
    color: "info"
  };
};

const fetchReceitas = async () => {
  try {
    loading.value = true;
    
    // Mock data - replace with API call
    receitas.value = [
      {
        id: 1,
        descricao: "Salário Empresa XYZ",
        categoria: "Salário",
        conta: "Nubank",
        valor: 550000, // R$ 5.500,00
        data_vencimento: "2024-10-05",
        status: "RECEBIDO",
        observacao: ""
      },
      {
        id: 2,
        descricao: "Freelance - Projeto Web",
        categoria: "Freelance",
        conta: "Banco do Brasil",
        valor: 250000, // R$ 2.500,00
        data_vencimento: "2024-10-15",
        status: "PENDENTE",
        observacao: "Cliente ABC"
      },
      {
        id: 3,
        descricao: "Dividendos Ações",
        categoria: "Investimentos",
        conta: "Inter",
        valor: 85000, // R$ 850,00
        data_vencimento: "2024-10-20",
        status: "PENDENTE",
        observacao: ""
      },
      {
        id: 4,
        descricao: "Venda Produto",
        categoria: "Vendas",
        conta: "Caixa",
        valor: 120000, // R$ 1.200,00
        data_vencimento: "2024-10-10",
        status: "RECEBIDO",
        observacao: ""
      },
      {
        id: 5,
        descricao: "Aluguel Imóvel",
        categoria: "Aluguel",
        conta: "Nubank",
        valor: 180000, // R$ 1.800,00
        data_vencimento: "2024-10-01",
        status: "ATRASADO",
        observacao: "Inquilino atrasado"
      }
    ];
    
  } catch (error: any) {
    console.error("Erro ao carregar receitas:", error);
    snackbar.value = {
      show: true,
      message: error.response?.data?.message || "Erro ao carregar receitas",
      color: "error"
    };
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchReceitas();
});
</script>

<style scoped>
.receitas-view {
  /* Background será controlado pelo tema global no App.vue */
  min-height: 100vh;
}

/* Header responsivo */
.header-content {
  width: 100%;
}

/* Botão do menu - garantir visibilidade em mobile */
.menu-button {
  display: inline-flex !important;
}

@media (min-width: 1280px) {
  .menu-button {
    display: none !important;
  }
}

.receitas-title {
  font-size: 1.5rem;
}

@media (min-width: 600px) {
  .receitas-title {
    font-size: 2rem;
  }
}

@media (min-width: 960px) {
  .receitas-title {
    font-size: 2.125rem;
  }
}

/* Gap utility */
.gap-3 {
  gap: 12px;
}

/* Card gradients */
.card-gradient {
  background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
  border-radius: 8px 8px 0 0;
}

.card-gradient-success {
  --gradient-start: #4CAF50;
  --gradient-end: #388E3C;
}

.card-gradient-error {
  --gradient-start: #F44336;
  --gradient-end: #D32F2F;
}

.card-gradient-primary {
  --gradient-start: #2196F3;
  --gradient-end: #1976D2;
}

.card-gradient-warning {
  --gradient-start: #FF9800;
  --gradient-end: #F57C00;
}

.card-gradient-info {
  --gradient-start: #00BCD4;
  --gradient-end: #0097A7;
}

/* Summary cards */
.summary-card {
  transition: transform 0.2s;
}

.summary-card:hover {
  transform: translateY(-4px);
}

/* Table styling */
:deep(.v-data-table) {
  background-color: transparent;
}

:deep(.v-data-table thead th) {
  font-weight: 600;
  background-color: rgb(var(--v-theme-surface-variant));
  color: rgb(var(--v-theme-on-surface));
}

:deep(.v-data-table tbody tr:hover) {
  background-color: rgb(var(--v-theme-surface-variant));
}

/* Responsive */
@media (max-width: 960px) {
  .receitas-view {
    padding: 16px !important;
  }
}
</style>
