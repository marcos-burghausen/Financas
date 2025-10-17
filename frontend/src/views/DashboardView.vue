<template>
  <v-layout>
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
          :to="item.route ? { name: item.route } : undefined"
          :class="{ 'bg-primary': isActiveRoute(item.route) }"
          @click="item.action ? handleAction(item.action) : null"
        >
          <template #prepend>
            <v-icon :icon="item.icon" />
          </template>
          <v-list-item-title>{{ item.name }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-main>
      <v-container
        fluid
        class="pa-6"
      >
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
                  <h1 class="dashboard-title mb-1 d-flex align-center">
                    <v-icon
                      icon="mdi-view-dashboard"
                      :size="$vuetify.display.xs ? '24' : '36'"
                      class="mr-2 mr-md-3"
                      color="primary"
                    />
                    <span class="d-none d-sm-inline">Dashboard Financeiro</span>
                    <span class="d-sm-none">Dashboard</span>
                  </h1>
                  <p class="text-caption text-sm-subtitle-1 text-grey mb-0 d-none d-sm-block">
                    Visão geral das suas finanças
                  </p>
                </div>
              </div>
            </div>
          </v-col>
        </v-row>

        <v-row v-if="loading">
          <v-col
            cols="12"
            class="text-center py-12"
          >
            <v-progress-circular
              indeterminate
              color="primary"
              size="64"
            />
            <p class="text-grey mt-4">
              Carregando dados...
            </p>
          </v-col>
        </v-row>

        <v-row v-else>
          <v-col
            cols="12"
            sm="6"
            lg="3"
          >
            <v-card
              elevation="4"
              class="summary-card h-100"
            >
              <div class="card-gradient card-gradient-success pa-3 pa-sm-4">
                <div class="d-flex justify-space-between align-start mb-2 mb-sm-3">
                  <div class="flex-grow-1">
                    <p class="text-caption text-sm-body-2 text-white mb-1">
                      Receitas do Mês
                    </p>
                    <h2 class="summary-value text-white font-weight-bold">
                      {{ formatCurrency(summary.receitasMes) }}
                    </h2>
                  </div>
                  <v-avatar
                    color="rgba(255,255,255,0.2)"
                    :size="$vuetify.display.xs ? '40' : '48'"
                  >
                    <v-icon
                      icon="mdi-arrow-up-circle"
                      color="white"
                      :size="$vuetify.display.xs ? '22' : '28'"
                    />
                  </v-avatar>
                </div>
                <v-chip
                  size="small"
                  color="white"
                  text-color="success"
                  class="font-weight-medium chip-responsive"
                >
                  <v-icon
                    icon="mdi-trending-up"
                    start
                    size="16"
                  />
                  {{ summary.receitasRecebidas }} recebidas
                </v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col
            cols="12"
            sm="6"
            lg="3"
          >
            <v-card
              elevation="4"
              class="summary-card h-100"
            >
              <div class="card-gradient card-gradient-error pa-3 pa-sm-4">
                <div class="d-flex justify-space-between align-start mb-2 mb-sm-3">
                  <div class="flex-grow-1">
                    <p class="text-caption text-sm-body-2 text-white mb-1">
                      Despesas do Mês
                    </p>
                    <h2 class="summary-value text-white font-weight-bold">
                      {{ formatCurrency(summary.despesasMes) }}
                    </h2>
                  </div>
                  <v-avatar
                    color="rgba(255,255,255,0.2)"
                    :size="$vuetify.display.xs ? '40' : '48'"
                  >
                    <v-icon
                      icon="mdi-arrow-down-circle"
                      color="white"
                      :size="$vuetify.display.xs ? '22' : '28'"
                    />
                  </v-avatar>
                </div>
                <v-chip
                  size="small"
                  color="white"
                  text-color="error"
                  class="font-weight-medium chip-responsive"
                >
                  <v-icon
                    icon="mdi-trending-down"
                    start
                    size="16"
                  />
                  {{ summary.despesasPagas }} pagas
                </v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col
            cols="12"
            sm="6"
            lg="3"
          >
            <v-card
              elevation="4"
              class="summary-card h-100"
            >
              <div class="card-gradient card-gradient-primary pa-3 pa-sm-4">
                <div class="d-flex justify-space-between align-start mb-2 mb-sm-3">
                  <div class="flex-grow-1">
                    <p class="text-caption text-sm-body-2 text-white mb-1">
                      Saldo Atual
                    </p>
                    <h2 class="summary-value text-white font-weight-bold">
                      {{ formatCurrency(summary.saldoAtual) }}
                    </h2>
                  </div>
                  <v-avatar
                    color="rgba(255,255,255,0.2)"
                    :size="$vuetify.display.xs ? '40' : '48'"
                  >
                    <v-icon
                      icon="mdi-wallet"
                      color="white"
                      :size="$vuetify.display.xs ? '22' : '28'"
                    />
                  </v-avatar>
                </div>
                <v-chip 
                  size="small" 
                  :color="summary.saldoAtual >= 0 ? 'white' : 'error'"
                  :text-color="summary.saldoAtual >= 0 ? 'primary' : 'white'" 
                  class="font-weight-medium chip-responsive"
                >
                  <v-icon 
                    :icon="summary.saldoAtual >= 0 ? 'mdi-check-circle' : 'mdi-alert'" 
                    start 
                    size="16" 
                  />
                  {{ summary.saldoAtual >= 0 ? 'Positivo' : 'Atenção' }}
                </v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col
            cols="12"
            sm="6"
            lg="3"
          >
            <v-card
              elevation="4"
              class="summary-card h-100"
            >
              <div class="card-gradient card-gradient-warning pa-3 pa-sm-4">
                <div class="d-flex justify-space-between align-start mb-2 mb-sm-3">
                  <div class="flex-grow-1">
                    <p class="text-caption text-sm-body-2 text-white mb-1">
                      Pendências
                    </p>
                    <h2 class="summary-value text-white font-weight-bold">
                      {{ formatCurrency(summary.pendencias) }}
                    </h2>
                  </div>
                  <v-avatar
                    color="rgba(255,255,255,0.2)"
                    :size="$vuetify.display.xs ? '40' : '48'"
                  >
                    <v-icon
                      icon="mdi-clock-alert"
                      color="white"
                      :size="$vuetify.display.xs ? '22' : '28'"
                    />
                  </v-avatar>
                </div>
                <v-chip
                  size="small"
                  color="white"
                  text-color="warning"
                  class="font-weight-medium chip-responsive"
                >
                  <v-icon
                    icon="mdi-alert-circle"
                    start
                    size="16"
                  />
                  {{ summary.totalPendencias }} itens
                </v-chip>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <v-row
          v-if="!loading"
          class="mt-2"
        >
          <v-col
            cols="12"
            lg="8"
          >
            <v-card
              elevation="4"
              class="h-100"
            >
              <div class="card-gradient card-gradient-primary pa-4">
                <div class="d-flex align-center">
                  <v-icon
                    icon="mdi-chart-bar"
                    size="28"
                    color="white"
                    class="mr-3"
                  />
                  <div>
                    <h3 class="text-h6 text-white font-weight-bold">
                      Evolução Mensal
                    </h3>
                    <p class="text-body-2 text-white opacity-90 mb-0">
                      Últimos 6 meses
                    </p>
                  </div>
                </div>
              </div>
              <v-card-text class="pa-4">
                <apexchart
                  v-if="chartOptions.bar"
                  type="bar"
                  :height="$vuetify.display.xs ? '250' : '350'"
                  :options="chartOptions.bar"
                  :series="chartSeries.bar"
                />
                <div
                  v-else
                  class="text-center py-12"
                >
                  <v-icon
                    icon="mdi-chart-bar-stacked"
                    size="64"
                    color="grey-lighten-1"
                  />
                  <p class="text-grey mt-4">
                    Sem dados para exibir
                  </p>
                </div>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col
            cols="12"
            lg="4"
          >
            <v-card
              elevation="4"
              class="h-100"
            >
              <div class="card-gradient card-gradient-error pa-4">
                <div class="d-flex align-center">
                  <v-icon
                    icon="mdi-chart-pie"
                    size="28"
                    color="white"
                    class="mr-3"
                  />
                  <div>
                    <h3 class="text-h6 text-white font-weight-bold">
                      Por Categoria
                    </h3>
                    <p class="text-body-2 text-white opacity-90 mb-0">
                      Despesas do mês
                    </p>
                  </div>
                </div>
              </div>
              <v-card-text class="pa-4">
                <apexchart
                  v-if="chartOptions.pie"
                  type="donut"
                  :height="$vuetify.display.xs ? '250' : '350'"
                  :options="chartOptions.pie"
                  :series="chartSeries.pie"
                />
                <div
                  v-else
                  class="text-center py-12"
                >
                  <v-icon
                    icon="mdi-chart-donut"
                    size="64"
                    color="grey-lighten-1"
                  />
                  <p class="text-grey mt-4">
                    Sem dados para exibir
                  </p>
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <v-row
          v-if="!loading"
          class="mt-2"
        >
          <v-col
            cols="12"
            lg="8"
          >
            <v-card elevation="4">
              <div class="card-gradient card-gradient-info pa-4">
                <div class="d-flex align-center justify-space-between">
                  <div class="d-flex align-center">
                    <v-icon
                      icon="mdi-history"
                      size="28"
                      color="white"
                      class="mr-3"
                    />
                    <div>
                      <h3 class="text-h6 text-white font-weight-bold">
                        Últimos Lançamentos
                      </h3>
                      <p class="text-body-2 text-white opacity-90 mb-0">
                        10 mais recentes
                      </p>
                    </div>
                  </div>
                  <v-btn
                    variant="text"
                    color="white"
                    prepend-icon="mdi-eye"
                    @click="$router.push('/dashboard')"
                  >
                    Ver todos
                  </v-btn>
                </div>
              </div>
              <v-card-text class="pa-0">
                <!-- Tabela para desktop -->
                <v-table class="d-none d-md-table">
                  <thead>
                    <tr>
                      <th>Data</th>
                      <th>Descrição</th>
                      <th>Categoria</th>
                      <th>Status</th>
                      <th class="text-right">
                        Valor
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="recentTransactions.length === 0">
                      <td
                        colspan="5"
                        class="text-center py-8"
                      >
                        <v-icon
                          icon="mdi-file-document-outline"
                          size="48"
                          color="grey-lighten-1"
                        />
                        <p class="text-grey mt-2">
                          Nenhum lançamento encontrado
                        </p>
                      </td>
                    </tr>
                    <tr
                      v-for="transaction in recentTransactions"
                      :key="transaction.id"
                    >
                      <td>{{ formatDate(transaction.data) }}</td>
                      <td>
                        <div class="d-flex align-center">
                          <v-icon 
                            :icon="transaction.tipo === 'RECEITA' ? 'mdi-arrow-up' : 'mdi-arrow-down'" 
                            :color="transaction.tipo === 'RECEITA' ? 'success' : 'error'"
                            size="20"
                            class="mr-2"
                          />
                          {{ transaction.descricao }}
                        </div>
                      </td>
                      <td>
                        <v-chip
                          size="small"
                          variant="tonal"
                        >
                          {{ transaction.categoria }}
                        </v-chip>
                      </td>
                      <td>
                        <v-chip 
                          size="small" 
                          :color="getStatusColor(transaction.status)"
                          variant="flat"
                        >
                          {{ transaction.status }}
                        </v-chip>
                      </td>
                      <td
                        class="text-right font-weight-bold"
                        :class="transaction.tipo === 'RECEITA' ? 'text-success' : 'text-error'"
                      >
                        {{ formatCurrency(transaction.valor) }}
                      </td>
                    </tr>
                  </tbody>
                </v-table>
                
                <!-- Lista para mobile -->
                <v-list class="d-md-none pa-0">
                  <v-list-item
                    v-if="recentTransactions.length === 0"
                    class="text-center py-8"
                  >
                    <v-icon
                      icon="mdi-file-document-outline"
                      size="48"
                      color="grey-lighten-1"
                    />
                    <p class="text-grey mt-2">
                      Nenhum lançamento encontrado
                    </p>
                  </v-list-item>
                  <v-list-item
                    v-for="transaction in recentTransactions"
                    :key="transaction.id"
                    class="px-4 py-3 border-b"
                  >
                    <div class="d-flex flex-column">
                      <div class="d-flex justify-space-between align-center mb-2">
                        <div class="d-flex align-center">
                          <v-icon 
                            :icon="transaction.tipo === 'RECEITA' ? 'mdi-arrow-up' : 'mdi-arrow-down'" 
                            :color="transaction.tipo === 'RECEITA' ? 'success' : 'error'"
                            size="20"
                            class="mr-2"
                          />
                          <span class="font-weight-medium">{{ transaction.descricao }}</span>
                        </div>
                        <span 
                          class="font-weight-bold"
                          :class="transaction.tipo === 'RECEITA' ? 'text-success' : 'text-error'"
                        >
                          {{ formatCurrency(transaction.valor) }}
                        </span>
                      </div>
                      <div class="d-flex justify-space-between align-center">
                        <div class="d-flex gap-2">
                          <v-chip
                            size="x-small"
                            variant="tonal"
                          >
                            {{ transaction.categoria }}
                          </v-chip>
                          <v-chip
                            size="x-small"
                            :color="getStatusColor(transaction.status)"
                            variant="flat"
                          >
                            {{ transaction.status }}
                          </v-chip>
                        </div>
                        <span class="text-caption text-grey">{{ formatDate(transaction.data) }}</span>
                      </div>
                    </div>
                  </v-list-item>
                </v-list>
              </v-card-text>
            </v-card>
          </v-col>

          <v-col
            cols="12"
            lg="4"
          >
            <v-card
              elevation="4"
              class="mb-4"
            >
              <div class="card-gradient card-gradient-warning pa-4">
                <div class="d-flex align-center">
                  <v-icon
                    icon="mdi-bell-alert"
                    size="28"
                    color="white"
                    class="mr-3"
                  />
                  <div>
                    <h3 class="text-h6 text-white font-weight-bold">
                      Alertas
                    </h3>
                    <p class="text-body-2 text-white opacity-90 mb-0">
                      Contas a vencer
                    </p>
                  </div>
                </div>
              </div>
              <v-card-text class="pa-4">
                <div
                  v-if="alerts.length === 0"
                  class="text-center py-6"
                >
                  <v-icon
                    icon="mdi-check-circle"
                    size="48"
                    color="success"
                  />
                  <p class="text-grey mt-2">
                    Nenhum alerta no momento
                  </p>
                </div>
                <v-list
                  v-else
                  density="compact"
                >
                  <v-list-item
                    v-for="alert in alerts"
                    :key="alert.id"
                    class="px-0 mb-2"
                  >
                    <template #prepend>
                      <v-icon
                        :icon="alert.icon"
                        :color="alert.color"
                      />
                    </template>
                    <v-list-item-title class="text-body-2">
                      {{ alert.title }}
                    </v-list-item-title>
                    <v-list-item-subtitle class="text-caption">
                      {{ alert.subtitle }}
                    </v-list-item-subtitle>
                  </v-list-item>
                </v-list>
              </v-card-text>
            </v-card>

            <v-card elevation="4">
              <div class="card-gradient card-gradient-primary pa-4">
                <div class="d-flex align-center">
                  <v-icon
                    icon="mdi-lightning-bolt"
                    size="28"
                    color="white"
                    class="mr-3"
                  />
                  <h3 class="text-h6 text-white font-weight-bold">
                    Ações Rápidas
                  </h3>
                </div>
              </div>
              <v-card-text class="pa-4">
                <v-btn
                  block
                  color="success"
                  prepend-icon="mdi-plus-circle"
                  class="mb-2"
                  @click="$router.push('/receitas')"
                >
                  Nova Receita
                </v-btn>
                <v-btn
                  block
                  color="error"
                  prepend-icon="mdi-minus-circle"
                  class="mb-2"
                  @click="$router.push('/despesas')"
                >
                  Nova Despesa
                </v-btn>
                <v-btn
                  block
                  color="primary"
                  prepend-icon="mdi-bank"
                  class="mb-2"
                  @click="$router.push('/contas')"
                >
                  Gerenciar Contas
                </v-btn>
                <v-btn
                  block
                  color="info"
                  prepend-icon="mdi-tag-multiple"
                  @click="$router.push('/categorias')"
                >
                  Categorias
                </v-btn>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

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
import http from "@/services/http";
import { useAuthStore, useDashboardStore, useExpensesStore, useRevenuesStore, useUserStore, useWalletsStore } from "@/store";
import { useRolesStore } from "@/store/roles";
import { computed, onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";

// Router
const router = useRouter();
const route = useRoute();
const useAuth = useAuthStore();
const userStore  = useUserStore();
const dashboardStore = useDashboardStore();
const useExpenses = useExpensesStore();
const useRevenues = useRevenuesStore();
const useWallets = useWalletsStore();

// Stores
const rolesStore = useRolesStore();

// Drawer state
const drawer = ref(false);

// Menu items
const itensSideBar = ref([
  {
    name: "Admin",
    icon: "mdi-shield-crown",
    route: "admin",
    adminOnly: true,
  },
  {
    name: "Trader",
    icon: "mdi-chart-line",
    route: "trader",
    traderOnly: true,
  },
  {
    name: "Dashboard",
    icon: "mdi-view-dashboard",
    route: "dashboard",
    adminOnly: false,
    traderOnly: false,
  },
  {
    name: "Contas",
    icon: "mdi-bank",
    route: "contas",
    adminOnly: false,
    traderOnly: false,
  },
  {
    name: "Receitas",
    icon: "mdi-cash-plus",
    route: "receitas",
    adminOnly: false,
    traderOnly: false,
  },
  {
    name: "Despesas",
    icon: "mdi-cash-minus",
    route: "despesas",
    adminOnly: false,
    traderOnly: false,
  },
  {
    name: "Categorias",
    icon: "mdi-tag-multiple",
    route: "categorias",
    adminOnly: false,
    traderOnly: false,
  },
  {
    name: "Cartões de Crédito",
    icon: "mdi-credit-card-outline",
    route: "cartoes",
    adminOnly: false,
    traderOnly: false,
  },
  {
    name: "Notificações",
    icon: "mdi-bell",
    route: "notificacoes",
    adminOnly: false,
    traderOnly: false,
  },
  {
    name: "Perfil",
    icon: "mdi-account",
    route: "perfil",
    adminOnly: false,
    traderOnly: false,
  },
  // Item "Sair" modificado para usar 'action' em vez de 'route'
  {
    name: "Sair",
    icon: "mdi-logout", // Ícone alterado para um mais apropriado
    action: "logout",
    adminOnly: false,
    traderOnly: false,
  },
]);

const filteredItensSideBar = computed(() => {
  const isTrader = rolesStore.myRoles.includes("TRADER") || 
                   rolesStore.myRoles.includes("USER_TRADER") || 
                   rolesStore.myRoles.includes("FULL");

  return itensSideBar.value.filter((item) => {
    if (item.adminOnly && !rolesStore.isAdmin) {
      return false;
    }
    if (item.traderOnly && !isTrader) {
      return false;
    }
    return true;
  });
});

// Check if route is active
const isActiveRoute = (routeName: string | undefined): boolean => {
  if (!routeName) return false;
  return route.name === routeName;
};

// Loading state
const loading = ref(true);

// Snackbar
const snackbar = ref({
  show: false,
  message: "",
  color: "success"
});

// Summary data
const summary = ref({
  receitasMes: 0,
  despesasMes: 0,
  saldoAtual: 0,
  pendencias: 0,
  receitasRecebidas: 0,
  despesasPagas: 0,
  totalPendencias: 0
});

// Chart data
const chartOptions = ref<any>({
  bar: null,
  pie: null
});

const chartSeries = ref<any>({
  bar: [],
  pie: []
});

// Recent transactions
const recentTransactions = ref<any[]>([]);

// Alerts
const alerts = ref<any[]>([]);

// Format currency
const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL"
  }).format(value / 100);
};

// Format date
const formatDate = (date: string): string => {
  return new Intl.DateTimeFormat("pt-BR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric"
  }).format(new Date(date));
};

// Get status color
const getStatusColor = (status: string): string => {
  const colors: Record<string, string> = {
    "RECEBIDO": "success",
    "PAGO": "success",
    "PENDENTE": "warning",
    "VENCIDO": "error"
  };
  return colors[status] || "grey";
};

// Fetch dashboard data
const fetchDashboardData = async () => {
  try {
    loading.value = true;

    // Mock data for now
    summary.value = {
      receitasMes: 850000,
      despesasMes: 520000,
      saldoAtual: 330000,
      pendencias: 150000,
      receitasRecebidas: 12,
      despesasPagas: 18,
      totalPendencias: 5
    };
    const months = ["Jul", "Ago", "Set", "Out", "Nov", "Dez"];
    chartOptions.value.bar = {
      chart: { type: "bar", height: 350, toolbar: { show: false } },
      plotOptions: { bar: { horizontal: false, columnWidth: "55%", borderRadius: 5 } },
      dataLabels: { enabled: false },
      stroke: { show: true, width: 2, colors: ["transparent"] },
      xaxis: { categories: months },
      yaxis: { title: { text: "R$ (milhares)" }, labels: { formatter: (value: number) => `R$ ${(value / 1000).toFixed(0)}k` } },
      fill: { opacity: 1 },
      tooltip: { y: { formatter: (val: number) => new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(val / 100) } },
      colors: ["#4CAF50", "#F44336"],
      legend: { position: "top", horizontalAlign: "right" }
    };
    chartSeries.value.bar = [ { name: "Receitas", data: [650000, 720000, 680000, 850000, 790000, 850000] }, { name: "Despesas", data: [450000, 520000, 480000, 550000, 500000, 520000] } ];
    chartOptions.value.pie = {
      chart: { type: "donut", height: 350 },
      labels: ["Alimentação", "Transporte", "Moradia", "Lazer", "Outros"],
      colors: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"],
      legend: { position: "bottom" },
      dataLabels: { enabled: true, formatter: (val: number) => `${val.toFixed(1)}%` },
      tooltip: { y: { formatter: (val: number) => new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(val / 100) } }
    };
    chartSeries.value.pie = [180000, 120000, 150000, 50000, 20000];
    recentTransactions.value = [
      { id: 1, data: "2024-12-15", descricao: "Salário", categoria: "Salário", tipo: "RECEITA", status: "RECEBIDO", valor: 550000 },
      { id: 2, data: "2024-12-10", descricao: "Aluguel", categoria: "Moradia", tipo: "DESPESA", status: "PAGO", valor: 150000 },
      { id: 3, data: "2024-12-08", descricao: "Supermercado", categoria: "Alimentação", tipo: "DESPESA", status: "PAGO", valor: 35000 },
      { id: 4, data: "2024-12-05", descricao: "Freelance", categoria: "Receita Extra", tipo: "RECEITA", status: "RECEBIDO", valor: 120000 },
      { id: 5, data: "2024-12-20", descricao: "Conta de luz", categoria: "Moradia", tipo: "DESPESA", status: "PENDENTE", valor: 18000 }
    ];
    alerts.value = [
      { id: 1, icon: "mdi-alert-circle", color: "warning", title: "Conta de água vence em 3 dias", subtitle: "R$ 85,00" },
      { id: 2, icon: "mdi-credit-card-alert", color: "error", title: "Fatura do cartão vence amanhã", subtitle: "R$ 1.250,00" },
      { id: 3, icon: "mdi-calendar-alert", color: "info", title: "Parcela do curso vence em 5 dias", subtitle: "R$ 350,00" }
    ];

  } catch (error: any) {
    console.error("Erro ao carregar dashboard:", error);
    snackbar.value = {
      show: true,
      message: error.response?.data?.message || "Erro ao carregar dados do dashboard",
      color: "error"
    };
  } finally {
    loading.value = false;
  }
};

// On mounted
onMounted(() => {
  fetchDashboardData();
});

async function logout() {
  try {
    await http.post("/sanctum/logout");
    
    useAuth.clear();
    userStore.clear();
    dashboardStore.clear();
    useExpenses.clear();
    useRevenues.clear();
    useWallets.clear();
    
    window.location.href = "/";
  } catch (error) {
    useAuth.clear();
    userStore.clear();
    dashboardStore.clear();
    useExpenses.clear();
    useRevenues.clear();
    useWallets.clear();
    window.location.href = "/";
  }
}

// Nova função para lidar com ações do menu
function handleAction(actionName: string) {
  if (actionName === "logout") {
    logout();
  }
}
</script>
<style scoped>
.dashboard-view {
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

.dashboard-title {
  font-size: 1.5rem;
}

@media (min-width: 600px) {
  .dashboard-title {
    font-size: 2rem;
  }
}

@media (min-width: 960px) {
  .dashboard-title {
    font-size: 2.125rem;
  }
}

/* Summary cards responsivos */
.summary-value {
  font-size: 1.25rem;
  line-height: 1.2;
  word-break: break-word;
}

@media (min-width: 600px) {
  .summary-value {
    font-size: 1.5rem;
  }
}

/* Chip responsivo */
.chip-responsive {
  font-size: 0.6875rem;
}

@media (min-width: 600px) {
  .chip-responsive {
    font-size: 0.75rem;
  }
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
.v-table {
  background-color: transparent;
}

.v-table thead th {
  font-weight: 600;
  color: rgb(var(--v-theme-on-surface));
  background-color: rgb(var(--v-theme-surface-variant));
}

.v-table tbody tr:hover {
  background-color: rgb(var(--v-theme-surface-variant));
}

/* Border para lista mobile */
.border-b {
  border-bottom: 1px solid rgb(var(--v-theme-surface-variant));
}

.border-b:last-child {
  border-bottom: none;
}

/* Gap utility */
.gap-2 {
  gap: 8px;
}

/* Container padding responsivo */
.v-container {
  padding: 12px;
}

@media (min-width: 600px) {
  .v-container {
    padding: 16px;
  }
}

@media (min-width: 960px) {
  .v-container {
    padding: 24px;
  }
}

/* Ajuste de altura dos gráficos para mobile */
@media (max-width: 599px) {
  .apexcharts-canvas {
    max-width: 100% !important;
  }
}

/* Responsive */
@media (max-width: 960px) {
  .dashboard-view {
    padding: 8px !important;
  }
}
</style>