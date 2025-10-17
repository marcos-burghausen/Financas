<template>
  <v-container
    fluid
    class="dashboard__view p-0"
  >
    <!-- <v-layout>
      <v-app-bar color="transparent">
        <v-app-bar-nav-icon
          variant="text"
          @click.stop="drawer = !drawer"
        >
          <v-icon
            icon="mdi-menu"
            class="mdicon"
            size="30"
          />
        </v-app-bar-nav-icon>

        <div class="container__mes">
          <v-icon
            icon="mdi-chevron-left"
            class="mdicon"
            size="30"
            @click="mesAnterior"
          />
          <span class="mes"> {{ mesPorExtenso }} </span>
          <v-icon
            icon="mdi-chevron-right"
            class="mdicon"
            size="30"
            @click="proximoMes"
          />
        </div>

        <v-spacer />

        <v-menu>
          <template #activator="{ props }">
            <v-icon
              v-if="useAuth.isAuthenticated"
              icon="mdi-dots-vertical"
              class="mdicon me-3"
              v-bind="props"
              size="30"
            />
          </template>
          <v-list
            style="
              width: 150px;
              background: rgb(38, 38, 39);
              box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
              color: #fefefe;
            "
          >
            <v-list-item
              v-for="(item, index) in items"
              :key="index"
              :value="index"
              @click="item.action"
            >
              <v-list-item-title style="font-size: 20px">
                <v-icon
                  :icon="item.icon"
                  class="me-3 fs-3"
                />
                {{ item.title }}
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>

      </v-app-bar>

      <v-navigation-drawer
        v-model="drawer"
        :location="$vuetify.display.mobile ? 'left' : undefined"
        temporary
        color="#212529"
      >
        <v-list>
          <v-list-item
            v-for="(item, index) in filteredItensSideBar"
            :key="index"
            :class="{ efeitoClick: elementoAtivoSideBar === index }"
            :to="{ name: item.route }"
            @click="item.action"
          >
            <span class="icon">
              <v-icon :icon="item.icon" />
            </span>
            <span class="txt__link">
              {{ item.name }}
            </span>
          </v-list-item>
        </v-list>
      </v-navigation-drawer>

      <v-main class="w-100">
        <div class="container__saldo__conta d-flex justify-content-center">
          <div class="me-4 d-flex flex-column align-center justify-content-end">
            <span class="icon__text">
              <v-icon
                icon="mdi-check-circle-outline"
                size="18"
              />
              Inicial
            </span>
            <div style="display: flex; align-items: center">
              <span
                :style="{ color: saldoInicial < 0 ? '#d45959ff' : saldoInicial > 0 ? 'green' : '#757575' }"
                style="font-size: 13px"
              >
                R$ {{ formatValue(saldoInicial) }}
              </span>
            </div>
          </div>
          <div class="d-flex flex-column align-center justify-content-end">
            <span
              class="fs-5"
              style="color: #757575"
            >
              <v-icon
                :icon="
                  (totalBalance ?? 0) < 0
                    ? 'mdi-minus-circle-outline'
                    : (totalBalance ?? 0) > 0
                      ? 'mdi-heart-circle'
                      : 'mdi-circle-outline'
                "
                size="22"
              />
              Saldo
            </span>
            <div style="display: flex; align-items: center">
              <span
                :style="{
                  color:
                    saldoAtual < 0
                      ? '#d45959ff'
                      : saldoAtual > 0
                        ? 'green'
                        : '#757575',
                }"
                style="font-size: 18px"
              >
                R$ {{ formatValue(saldoAtual) }}
              </span>
            </div>
          </div>
          <div class="ms-4 d-flex flex-column align-center justify-content-end">
            <span class="icon__text">
              <v-icon
                icon="mdi-clock-outline"
                size="20"
              />
              Previsto
            </span>
            <div style="display: flex; align-items: center">
              <span
                :style="{ color: valorPrevisto < 0 ? '#d45959ff' : '#757575' }"
                style="font-size: 13px"
              >
                R$ {{ formatValue(valorPrevisto) }}
              </span>
            </div>
          </div>
        </div>

        <div class="container__visao__geral">
          <div class="header__visao_geral">
            <span style="text-align: start"> Visão geral </span>
            <v-icon
              icon="mdi-dots-vertical"
              class="mdicon"
              size="25"
            />
          </div>
          <router-link
            :to="{ name: 'receitas' }"
            style="display: flex; align-items: center; text-decoration: none"
          >
            <div
              style="
                border-inline-start: solid 5px green;
                margin: 5px 0 5px 0;
                padding: 0 0 0 10px;
                display: flex;
                justify-content: space-between;
                width: 100%;
              "
            >
              <div class="tipo__lancamento">
                <span class="lancamento"> Receitas </span>
                <span class="previsto"> previsto </span>
              </div>
              <div class="tipo__lancamento">
                <span class="lancamento">
                  R$ {{ formatValue(valueReceived) }}
                </span>
                <span class="valor__previsto">
                  R$ {{ formatValue(valueTotalRevenuesMonth) }}
                </span>
              </div>
            </div>
          </router-link>
          <router-link
            :to="{ name: 'despesas' }"
            style="display: flex; align-items: center; text-decoration: none"
          >
            <div
              style="
                border-inline-start: solid 5px red;
                margin: 5px 0 5px 0;
                padding: 0 0 0 10px;
                display: flex;
                justify-content: space-between;
                width: 100%;
              "
            >
              <div class="tipo__lancamento">
                <span class="lancamento"> Despesas </span>
                <span class="previsto"> previsto </span>
              </div>
              <div class="tipo__lancamento">
                <span class="lancamento"> R$ {{ formatValue(valuePay) }} </span>
                <span class="valor__previsto">
                  R$ {{ formatValue(valueTotalExpensesMonth) }}
                </span>
              </div>
            </div>
          </router-link>
        </div>
      </v-main>
    </v-layout> -->


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
        <v-container fluid class="">
          <v-row class="mb-4">
            <v-col cols="12"  md="8">
              <div class="d-flex align-center justify-space-between mb-2">
                <div class="d-flex align-center">
                  <v-btn
                    icon
                    variant="text"
                    @click="drawer = !drawer"
                    class="mr-2"
                  >
                    <v-icon icon="mdi-menu" size="28" />
                  </v-btn>
                  <div>
                    <h1 class="text-h4 mb-1 d-flex align-center">
                      <v-icon icon="mdi-view-dashboard" size="36" class="mr-3" color="primary" />
                      Dashboard Financeiro
                    </h1>
                    <p class="text-subtitle-1 text-grey mb-0">
                      Visão geral das suas finanças
                    </p>
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>

          <v-row v-if="loading">
            <v-col cols="12" class="text-center py-12">
              <v-progress-circular indeterminate color="primary" size="64" />
              <p class="text-grey mt-4">Carregando dados...</p>
            </v-col>
          </v-row>

          <v-row v-else>
            <v-col cols="12" sm="6" md="3">
              <v-card elevation="4" class="summary__card h-100">
                <div class="card-gradient card-gradient-success pa-4">
                  <div class="d-flex justify-space-between align-start mb-3">
                    <div>
                      <p class="text-body-2 text-white mb-1">Receitas do Mês</p>
                      <h2 class="text-h5 text-white font-weight-bold">
                        {{ formatCurrency(receitasMes) }}
                      </h2>
                    </div>
                    <v-avatar color="rgba(255,255,255,0.2)" size="48">
                      <v-icon icon="mdi-arrow-up-circle" color="white" size="28" />
                    </v-avatar>
                  </div>
                  <v-chip size="small" color="white" text-color="success" class="font-weight-medium">
                    <v-icon icon="mdi-trending-up" start size="16" />
                    {{ receitasRecebidas.length }} recebidas
                  </v-chip>
                </div>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6" md="3">
              <v-card elevation="4" class="summary__card h-100">
                <div class="card-gradient card-gradient-error pa-4">
                  <div class="d-flex justify-space-between align-start mb-3">
                    <div>
                      <p class="text-body-2 text-white mb-1">Despesas do Mês</p>
                      <h2 class="text-h5 text-white font-weight-bold">
                        {{ formatCurrency(despesasMes) }}
                      </h2>
                    </div>
                    <v-avatar color="rgba(255,255,255,0.2)" size="48">
                      <v-icon icon="mdi-arrow-down-circle" color="white" size="28" />
                    </v-avatar>
                  </div>
                  <v-chip size="small" color="white" text-color="error" class="font-weight-medium">
                    <v-icon icon="mdi-trending-down" start size="16" />
                    {{ despesasPagas.length }} pagas
                  </v-chip>
                </div>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6" md="3">
              <v-card elevation="4" class="summary__card h-100">
                <div class="card-gradient card-gradient-primary pa-4">
                  <div class="d-flex justify-space-between align-start mb-3">
                    <div>
                      <p class="text-body-2 text-white mb-1">Saldo Atual</p>
                      <h2 class="text-h5 text-white font-weight-bold">
                        {{ formatCurrency(saldoAtual) }}
                      </h2>
                    </div>
                    <v-avatar color="rgba(255,255,255,0.2)" size="48">
                      <v-icon icon="mdi-wallet" color="white" size="28" />
                    </v-avatar>
                  </div>
                  <v-chip 
                    size="small" 
                    :color="saldoAtual >= 0 ? 'white' : 'error'"
                    :text-color="saldoAtual >= 0 ? 'primary' : 'white'" 
                    class="font-weight-medium"
                  >
                    <v-icon 
                      :icon="saldoAtual >= 0 ? 'mdi-check-circle' : 'mdi-alert'" 
                      start 
                      size="16" 
                    />
                    {{ saldoAtual >= 0 ? 'Positivo' : 'Atenção' }}
                  </v-chip>
                </div>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6" md="3">
              <v-card elevation="4" class="summary__card h-100">
                <div class="card-gradient card-gradient-warning pa-4">
                  <div class="d-flex justify-space-between align-start mb-3">
                    <div>
                      <p class="text-body-2 text-white mb-1">Pendências</p>
                      <h2 class="text-h5 text-white font-weight-bold">
                        {{ formatCurrency(111) }}
                      </h2>
                    </div>
                    <v-avatar color="rgba(255,255,255,0.2)" size="48">
                      <v-icon icon="mdi-clock-alert" color="white" size="28" />
                    </v-avatar>
                  </div>
                  <v-chip size="small" color="white" text-color="warning" class="font-weight-medium">
                    <v-icon icon="mdi-alert-circle" start size="16" />
                    {{ 2 }} itens
                  </v-chip>
                </div>
              </v-card>
            </v-col>
          </v-row>

          <v-row v-if="!loading" class="mt-2">
            <v-col cols="12" lg="8">
              <v-card elevation="4" class="h-100">
                <div class="card-gradient card-gradient-primary pa-4">
                  <div class="d-flex align-center">
                    <v-icon icon="mdi-chart-bar" size="28" color="white" class="mr-3" />
                    <div>
                      <h3 class="text-h6 text-white font-weight-bold">Evolução Mensal</h3>
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
                    height="350"
                    :options="chartOptions.bar"
                    :series="chartSeries.bar"
                  />
                  <div v-else class="text-center py-12">
                    <v-icon icon="mdi-chart-bar-stacked" size="64" color="grey-lighten-1" />
                    <p class="text-grey mt-4">Sem dados para exibir</p>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" lg="4">
              <v-card elevation="4" class="h-100">
                <div class="card-gradient card-gradient-error pa-4">
                  <div class="d-flex align-center">
                    <v-icon icon="mdi-chart-pie" size="28" color="white" class="mr-3" />
                    <div>
                      <h3 class="text-h6 text-white font-weight-bold">Por Categoria</h3>
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
                    height="350"
                    :options="chartOptions.pie"
                    :series="chartSeries.pie"
                  />
                  <div v-else class="text-center py-12">
                    <v-icon icon="mdi-chart-donut" size="64" color="grey-lighten-1" />
                    <p class="text-grey mt-4">Sem dados para exibir</p>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>

          <v-row v-if="!loading" class="mt-2">
            <v-col cols="12" lg="8">
              <v-card elevation="4">
                <div class="card-gradient card-gradient-info pa-4">
                  <div class="d-flex align-center justify-space-between">
                    <div class="d-flex align-center">
                      <v-icon icon="mdi-history" size="28" color="white" class="mr-3" />
                      <div>
                        <h3 class="text-h6 text-white font-weight-bold">Últimos Lançamentos</h3>
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
                  <v-table>
                    <thead>
                      <tr>
                        <th>Data</th>
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th>Status</th>
                        <th class="text-right">Valor</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-if="recentTransactions.length === 0">
                        <td colspan="5" class="text-center py-8">
                          <v-icon icon="mdi-file-document-outline" size="48" color="grey-lighten-1" />
                          <p class="text-grey mt-2">Nenhum lançamento encontrado</p>
                        </td>
                      </tr>
                      <tr v-for="transaction in recentTransactions" :key="transaction.id">
                        <td>{{ formatDate(transaction.data_vencimento) }}</td>
                        <td>
                          <div class="d-flex align-center">
                            <v-icon 
                              :icon="transaction.tipo_lancamento === 'RECEITA' ? 'mdi-arrow-up' : 'mdi-arrow-down'" 
                              :color="transaction.tipo_lancamento === 'RECEITA' ? 'success' : 'error'"
                              size="20"
                              class="mr-2"
                            />
                            {{ transaction.descricao }}
                          </div>
                        </td>
                        <td>
                          <v-chip size="small" variant="tonal">
                            {{ transaction.categoria }}
                          </v-chip>
                        </td>
                        <td>
                          <v-chip 
                            size="small" 
                            :color="getStatusColor(transaction.status_lancamento)"
                            variant="flat"
                          >
                            {{ transaction.status_lancamento }}
                          </v-chip>
                        </td>
                        <td class="text-right font-weight-bold" :class="transaction.tipo === 'RECEITA' ? 'text-success' : 'text-error'">
                          {{ formatCurrency(transaction.valor) }}
                        </td>
                      </tr>
                    </tbody>
                  </v-table>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" lg="4">
              <v-card elevation="4" class="mb-4">
                <div class="card-gradient card-gradient-warning pa-4">
                  <div class="d-flex align-center">
                    <v-icon icon="mdi-bell-alert" size="28" color="white" class="mr-3" />
                    <div>
                      <h3 class="text-h6 text-white font-weight-bold">Alertas</h3>
                      <p class="text-body-2 text-white opacity-90 mb-0">
                        Contas a vencer
                      </p>
                    </div>
                  </div>
                </div>
                <v-card-text class="pa-4">
                  <div v-if="alerts.length === 0" class="text-center py-6">
                    <v-icon icon="mdi-check-circle" size="48" color="success" />
                    <p class="text-grey mt-2">Nenhum alerta no momento</p>
                  </div>
                  <v-list v-else density="compact">
                    <v-list-item
                      v-for="alert in alerts"
                      :key="alert.id"
                      class="px-0 mb-2"
                    >
                      <template #prepend>
                        <v-icon :icon="alert.icon" :color="alert.color" />
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
                    <v-icon icon="mdi-lightning-bolt" size="28" color="white" class="mr-3" />
                    <h3 class="text-h6 text-white font-weight-bold">Ações Rápidas</h3>
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



  </v-container>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";

import http from "@/services/http";
import {
  useAuthStore,
  useDashboardStore,
  useExpensesStore,
  useRevenuesStore,
  useUserStore,
  useWalletsStore,
} from "@/store";
import { useRolesStore } from "@/store/roles";

const dashboardStore = useDashboardStore();
const useExpenses = useExpensesStore();
const useRevenues = useRevenuesStore();
const useWallets = useWalletsStore();
const useAuth = useAuthStore();
const userStore  = useUserStore();
const rolesStore = useRolesStore();
const router = useRouter();
const route = useRoute()

// Check if route is active
const isActiveRoute = (routeName: string | undefined): boolean => {
  if (!routeName) return false;
  return route.name === routeName
}

function handleAction(actionName: string) {
  if (actionName === 'logout') {
    logout();
  }
}

// Format currency
const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value / 100)
}

// Format date
const formatDate = (date: string): string => {
  return new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  }).format(new Date(date))
}

// Get status color
const getStatusColor = (status: string): string => {
  const colors: Record<string, string> = {
    'RECEBIDO': 'success',
    'PAGO': 'success',
    'PENDENTE': 'warning',
    'VENCIDO': 'error'
  }
  return colors[status] || 'grey'
}

// Snackbar
const snackbar = ref({
  show: false,
  message: '',
  color: 'success'
})

// Chart data
const chartOptions = ref<any>({
  bar: null,
  pie: null
})

const chartSeries = ref<any>({
  bar: [],
  pie: []
})

// Recent transactions
const recentTransactions = ref(useRevenues.revenuesData.byMonth)

// Alerts
const alerts = ref<any[]>([])

onMounted(async () => {
  try {
    loading.value = true
    // Carrega dados do localStorage
    useAuth.loadFromSession();
    userStore.loadFromSession();
    dashboardStore.loadFromSession();
    useExpenses.loadFromSession();
    useRevenues.loadFromSession();
    useWallets.loadFromSession();
    
    // Verifica se está autenticado antes de buscar dados
    if (!useAuth.isAuthenticated) {
      console.warn('Usuário não autenticado, redirecionando para login');
      router.push({ name: 'home' });
      return;
    }
    
    // Carregar permissões do usuário se ainda não foram carregadas
    if (rolesStore.myRoles.length === 0) {
      try {
        await rolesStore.fetchMyPermissions();
      } catch (error) {
        console.error('Erro ao carregar permissões no dashboard:', error);
      }
    }
    
    // Só busca dados do backend se realmente não tiver nada no localStorage
    const hasExpensesData = useExpenses.expensesData.byMonth && useExpenses.expensesData.byMonth.length > 0;
    const hasRevenuesData = useRevenues.revenuesData.byMonth && useRevenues.revenuesData.byMonth.length > 0;
    
    if (!hasExpensesData && !hasRevenuesData && userStore.mesAno) {
      const response = await buscarDadosMes(userStore.mesAno);
    }

    const months = ['Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']
    chartOptions.value.bar = {
      chart: { type: 'bar', height: 350, toolbar: { show: false } },
      plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 5 } },
      dataLabels: { enabled: false },
      stroke: { show: true, width: 2, colors: ['transparent'] },
      xaxis: { categories: months },
      yaxis: { title: { text: 'R$ (milhares)' }, labels: { formatter: (value: number) => `R$ ${(value / 1000).toFixed(0)}k` } },
      fill: { opacity: 1 },
      tooltip: { y: { formatter: (val: number) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(val / 100) } },
      colors: ['#4CAF50', '#F44336'],
      legend: { position: 'top', horizontalAlign: 'right' }
    }
    chartSeries.value.bar = [ { name: 'Receitas', data: [650000, 720000, 680000, 850000, 790000, 850000] }, { name: 'Despesas', data: [450000, 520000, 480000, 550000, 500000, 520000] } ]
    chartOptions.value.pie = {
      chart: { type: 'donut', height: 350 },
      labels: ['Alimentação', 'Transporte', 'Moradia', 'Lazer', 'Outros'],
      colors: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
      legend: { position: 'bottom' },
      dataLabels: { enabled: true, formatter: (val: number) => `${val.toFixed(1)}%` },
      tooltip: { y: { formatter: (val: number) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(val / 100) } }
    }
    chartSeries.value.pie = [180000, 120000, 150000, 50000, 20000]
    // recentTransactions.value = [
    //   { id: 1, data: '2024-12-15', descricao: 'Salário', categoria: 'Salário', tipo: 'RECEITA', status: 'RECEBIDO', valor: 550000 },
    //   { id: 2, data: '2024-12-10', descricao: 'Aluguel', categoria: 'Moradia', tipo: 'DESPESA', status: 'PAGO', valor: 150000 },
    //   { id: 3, data: '2024-12-08', descricao: 'Supermercado', categoria: 'Alimentação', tipo: 'DESPESA', status: 'PAGO', valor: 35000 },
    //   { id: 4, data: '2024-12-05', descricao: 'Freelance', categoria: 'Receita Extra', tipo: 'RECEITA', status: 'RECEBIDO', valor: 120000 },
    //   { id: 5, data: '2024-12-20', descricao: 'Conta de luz', categoria: 'Moradia', tipo: 'DESPESA', status: 'PENDENTE', valor: 18000 }
    // ]
    alerts.value = [
      { id: 1, icon: 'mdi-alert-circle', color: 'warning', title: 'Conta de água vence em 3 dias', subtitle: 'R$ 85,00' },
      { id: 2, icon: 'mdi-credit-card-alert', color: 'error', title: 'Fatura do cartão vence amanhã', subtitle: 'R$ 1.250,00' },
      { id: 3, icon: 'mdi-calendar-alert', color: 'info', title: 'Parcela do curso vence em 5 dias', subtitle: 'R$ 350,00' }
    ]

    } catch (error: any) {
      console.error('Erro ao carregar dashboard:', error)
      snackbar.value = {
        show: true,
        message: error.response?.data?.message || 'Erro ao carregar dados do dashboard',
        color: 'error'
      }
    } finally {
      loading.value = false
    }
});

// Loading state
const loading = ref(true)

const saldoAtual = computed(() => dashboardStore.summary.saldoAtual);
const receitasMes = computed(() => useRevenues.revenuesData?.valuePay || 0);
const receitasRecebidas = computed(() => useRevenues.revenuesData?.byMonth.filter(receita => receita.status_lancamento === "EFETIVADA"));
const despesasMes = computed(() => useExpenses.expensesData?.valuePay || 0);
const despesasPagas = computed(() => useExpenses.expensesData?.byMonth.filter(despesa => despesa.status_lancamento === "EFETIVADA"));
const saldoPrevisto = computed(() => dashboardStore.saldoPrevisto);

// Variáveis computadas baseadas nos stores
const valueTotalExpensesMonth = computed(() => useExpenses.expensesData?.valueTotalMonth || 0);
const valueTotalRevenuesMonth = computed(() => useRevenues.revenuesData?.valueTotalMonth || 0);
const valuePay = computed(() => useExpenses.expensesData?.valuePay || 0);
const valueReceived = computed(() => useRevenues.revenuesData?.valuePay || 0);
const valuePendingExpenses = computed(() => useExpenses.expensesData?.valuePending || 0);
const valuePendingRevenues = computed(() => useRevenues.revenuesData?.valuePending || 0);
const saldoInicial = computed(() => useWallets.walletsData?.saldo_inicial || 0);

// Valor previsto calculado
const valorPrevisto = computed(() => {
  return saldoInicial.value + valueTotalRevenuesMonth.value - valueTotalExpensesMonth.value;
});

const mesAno = ref<string>(userStore.mesAno || "");
// let valuePendingRevenues = ref(useRevenues.revenuesData?.valuePending);
// let valuePendingExpenses = ref(useExpenses.expensesData?.valuePending);
// let valueReceived = ref(useRevenues.revenuesData?.valuePay);
// let totalBalance = ref(useWallets.walletsData?.contas[0].saldo);
// // let saldoAtual = ref(useWallets.walletsData?.saldo_atual);
// let saldoInicial = ref(useWallets.walletsData?.saldo_inicial);
// let valuePay = ref(useExpenses.expensesData?.valuePay);
// // let name = ref(useUser.userData.name.split(" ")[0]);
// let valorPrevisto = ref(
//   saldoInicial.value +
//     valueTotalRevenuesMonth.value -
//     valueTotalExpensesMonth.value
// );
let elementoAtivoSideBar = ref(0);
// let totalCreditCard = ref(0);

watch(router, (value) => {
  switch (value.currentRoute.value.name) {
    case "admin":
      elementoAtivoSideBar.value = 0;
      break;
    case "trader":
      elementoAtivoSideBar.value = 1;
      break;
    case "dashboard":
      elementoAtivoSideBar.value = 2;
      break;
    case "contas":
      elementoAtivoSideBar.value = 3;
      break;
    case "receitas":
      elementoAtivoSideBar.value = 4;
      break;
    case "despesas":
      elementoAtivoSideBar.value = 5;
      break;
    case "categorias":
      elementoAtivoSideBar.value = 6;
      break;
    case "notificacoes":
      elementoAtivoSideBar.value = 7;
      break;
    case "perfil":
      elementoAtivoSideBar.value = 8;
      break;
  }
});

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
  {
    name: "Sair",
    icon: "mdi-logout",
    action: "logout",
    adminOnly: false,
    traderOnly: false,
  },
])

const filteredItensSideBar = computed(() => {
  const isTrader = rolesStore.myRoles.includes('TRADER') || 
                   rolesStore.myRoles.includes('USER_TRADER') || 
                   rolesStore.myRoles.includes('FULL');

  return itensSideBar.value.filter((item) => {
    if (item.adminOnly && !rolesStore.isAdmin) {
      return false
    }
    if (item.traderOnly && !isTrader) {
      return false
    }
    return true
  })
})

const items = ref([{ title: "Sair", icon: "mdi-power", action: logout }]);

const drawer = ref(false);
const group = ref(null);

watch(group, () => {
  drawer.value = false;
});

// const props = defineProps({
//   mesReferencia: {
//     type: String,
//     default: "",
//   },
// });

async function logout() {
  try {
    // Sanctum: endpoint atualizado
    await http.post("/sanctum/logout");
    
    // Limpa todos os stores
    useAuth.clear();
    userStore.clear();
    dashboardStore.clear();
    useExpenses.clear();
    useRevenues.clear();
    useWallets.clear();
    
    // Força reload completo para limpar tudo da memória
    window.location.href = "/";
  } catch (error) {
    // Se falhar a requisição, ainda limpa localmente
    useAuth.clear();
    userStore.clear();
    dashboardStore.clear();
    useExpenses.clear();
    useRevenues.clear();
    useWallets.clear();
    window.location.href = "/";
  }
}

// const isAllZeros = (arr: string[]) => {
//   return arr.every((value: string) => value === "0,00");
// };

const mesPorExtenso = computed(() => {
  if (!mesAno.value) return "";

  const mes = mesAno.value.split("-")[1];

  const mesesPorExtenso = [
    "Janeiro",
    "Fevereiro",
    "Março",
    "Abril",
    "Maio",
    "Junho",
    "Julho",
    "Agosto",
    "Setembro",
    "Outubro",
    "Novembro",
    "Dezembro",
  ];

  return mesesPorExtenso[parseInt(mes, 10) - 1];
});

const mesAnterior = () => {
    const [ano, mes] = mesAno.value.split("-").map(Number);
    const dataAtual = new Date(ano, mes - 1);
    dataAtual.setMonth(dataAtual.getMonth() - 1);
    mesAno.value = `${dataAtual.getFullYear()}-${String(
      dataAtual.getMonth() + 1
    ).padStart(2, "0")}`;
    buscarDadosMes(mesAno.value);
};

const proximoMes = () => {
    const [ano, mes] = mesAno.value.split("-").map(Number);
    const dataAtual = new Date(ano, mes - 1);
    dataAtual.setMonth(dataAtual.getMonth() + 1);
    mesAno.value = `${dataAtual.getFullYear()}-${String(
      dataAtual.getMonth() + 1
    ).padStart(2, "0")}`;
    buscarDadosMes(mesAno.value);
};

const buscarDadosMes = async (data: string) => {
  try {
    const res = await http.post("/buscar-dados-mes", { mesAno: data });
    userStore.setMesAno(res.data.mesAno);
    useExpenses.setExpensesData(res.data.expenses);
    useRevenues.setRevenuesData(res.data.revenues);
    useWallets.setWalletsData(res.data.wallets);
    mesAno.value = res.data.mesAno;
  } catch (error) {
    console.error("Erro ao buscar dados do mês:", error);
  }
};

// =============================== grafico de barras inicio =============================== //

// let totalYearValueExpenses = ref(Object.values(expensesAddTotalValueMonth.value));
// let totalYearValueRevenues = ref(Object.values(revenuesAddTotalValueMonth.value));

// const options = {
//     chart: {
//         id: "vuechart-example",
//         foreColor: "#fefefe"
//     },
//     title: {
//         text: "receitas & despesas",
//         align: "left",
//         style: {
//             color: "#fefefe"
//         }
//     },
//     dataLabels: {
//         enabled: false,
//     },
//     labels: {
//         style: {
//             color: "#fefefe"
//         }
//     },
//     colors: ["#fb0404", "#77d08e"],
//     xaxis: {
//         categories: ["janeiro", "fevereiro", "março", "abril", "maio", "junho", "julho", "agosto", "setembro", "outubro", "novembro", "dezembro"],
//     },
//     tooltip: {
//         y: {
//             formatter: function (val: Number) {
//                 return "R$ " + val;
//             }
//         }, theme: "dark",
//     }
// };
// const series = [
//     {
//         name: "despesas",
//         data: totalYearValueExpenses.value,
//     },
//     {
//         name: "receitas",
//         data: totalYearValueRevenues.value,
//     }
// ];
// =============================== grafico de barras fim =============================== //

// =============================== grafico de pizza inicio =============================== //
// let category = ref(Object.keys(totalByCategoryExpenses.value));
// let valuesCategory = ref(Object.values(totalByCategoryExpenses.value));

// const options1 = {
//     chart: {
//         id: "vuechart-example",
//         foreColor: "#fefefe"
//     },
//     title: {
//         text: "despesas por categoria",
//         align: "center",
//         style: {
//             color: "#fefefe"
//         }
//     },
//     legend: {
//         position: "bottom"
//     },
//     labels: category.value
// };
// const series1 = valuesCategory.value;
// =============================== grafico de pizza fim =============================== //
</script>

<style scoped>
.dashboard__view {
  min-height: 100vh;
  /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
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
.summary__card {
  transition: transform 0.2s;
}

.summary__card:hover {
  transform: translateY(-4px);
}


.containe__mobile {
  /* position: relative; */
  width: 100%;
  height: 100%;
}

.container__saldo__conta {
  color: #fefefe;
  font-size: 20px;
  background: rgba(150, 150, 150, 0.02);
  /* font-weight: bold; */
  padding: 10px;
  margin-top: 10px;
  margin-inline: 10px;
  border-radius: 10px;
}
.icon__text {
  font-size: 15px;
  color: #757575;
}
.teste2 {
  color: #fefefe;
  font-size: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: rgba(0, 0, 0, 0.1);
  /* font-weight: bold; */
  /* text-align: center; */
  padding-inline: 10px;
  margin-top: 10px;
  /* border: 1px solid #fefefe; */
  height: 60px;
  margin-inline: 10px;
  /* margin-top: -40px; */
  border-radius: 10px;
}
.container__visao__geral {
  color: #fefefe;
  font-size: 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  background: rgba(150, 150, 150, 0.03);
  /* align-items: center; */
  /* font-weight: bold; */
  /* text-align: center; */
  padding: 10px;
  margin-top: 10px;
  /* border: 1px solid #fefefe; */
  /* height: 60px; */
  margin-inline: 10px;
  /* margin-top: -40px; */
  border-radius: 10px;
}
.header__visao_geral {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
  color: #bdbdbd;
}
.cards__lancamentos {
  display: flex;
  justify-content: space-between;
  width: 100%;
}

.tipo__lancamento {
  display: flex;
  flex-direction: column;
}
.lancamento {
  font-size: 15px;
  color: #bdbdbd;
}
.previsto {
  font-size: 12px;
  color: #757575;
}
.valor__previsto {
  font-size: 12px;
  color: #757575;
  display: flex;
  justify-content: end;
}
.valor {
  text-align: center;
}
.opaco {
  color: #6c757d !important;
}

.card__container {
  display: flex;
}

.cards {
  width: 33.33%;
  color: #ccc;
  font-size: 30px;
  background-color: rgba(0, 0, 0, 0.1);
}
.chart__container {
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  margin-top: 10px;
  height: calc(100% - 125px);
  padding: 10px 0 0 0;
}

.chart1 {
  background: transparent;
}

.container__charts {
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  background-color: rgba(0, 0, 0, 0.1);
  display: flex;
  padding: 0;
  height: 100%;
}
.icon {
  /* margin-left: 13px; */
  color: #77d08e;
}
.txt__link {
  margin-left: 20px;
  transition: 0.5s;
  color: #fefefe;
  text-align: center !important;
}
.efeitoClick {
  box-shadow: inset -4px -4px 5px #3e4247, inset 7px 7px 7px #1d1f23;
  border-top-right-radius: 32px;
  border-bottom-right-radius: 32px;
}
.cabecalho {
  padding-inline: 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  /* background-color: rgba(0, 0, 0, 0.1); */
  height: 90px;
}

.mdicon {
  /* color: #77d08e; */
  color: #757575;
  cursor: pointer;
  /* padding: 10px; */
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  border-radius: 20px;
}
.mes {
  font-size: 25px;
  color: #bdbdbd;
}
.container__mes {
  width: 100%;
  display: flex;
  justify-content: space-around;
}
</style>
