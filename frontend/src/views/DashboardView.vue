<template>
  <div class="dashboard-view">
    <!-- LOADING STATE -->
    <v-row v-if="loading" class="py-12">
      <v-col cols="12" class="text-center">
        <v-progress-circular indeterminate color="primary" size="64" />
        <p class="text-grey mt-4">Carregando dados...</p>
      </v-col>
    </v-row>

    <!-- MAIN CONTENT -->
    <div v-else>
      <!-- MONTH NAVIGATION BAR -->
      <v-card class="mb-6" elevation="1">
        <v-card-text class="pa-4">
          <div class="d-flex align-center justify-center gap-4">
            <v-btn
              icon="mdi-chevron-left"
              color="primary"
              variant="outlined"
              size="small"
              @click="navigationMonth('prev')"
              title="Mês anterior"
            />
            <div class="text-center" style="min-width: 250px">
              <v-btn
                variant="text"
                :text="monthDisplayFormatted.toUpperCase()"
                @click="navigationMonth('today')"
                :class="{ 'text-primary font-weight-bold': isCurrentMonth }"
                title="Ir para o mês atual"
              />
            </div>
            <v-btn
              icon="mdi-chevron-right"
              color="primary"
              variant="outlined"
              size="small"
              @click="navigationMonth('next')"
              title="Próximo mês"
            />
          </div>
        </v-card-text>
      </v-card>

      <!-- KPI CARDS SECTION -->
      <v-row class="mb-6">
        <!-- Card: Receitas -->
        <v-col cols="12" sm="6" lg="3" class="mb-4">
          <v-card elevation="1" class="kpi-card kpi-success h-100">
            <v-card-item class="pb-0">
              <div class="d-flex justify-space-between align-start mb-4">
                <div class="flex-grow-1">
                  <p class="text-caption text-medium-emphasis mb-2">
                    Receitas {{ monthYearLabel }}
                  </p>
                  <h2 class="kpi-value mb-3">
                    {{ formatCurrency(summary.receitasMes) }}
                  </h2>
                  <div class="d-flex align-center gap-1">
                    <v-icon icon="mdi-trending-up" size="16" color="success" />
                    <p class="text-caption text-success mb-0">
                      +{{ receitasVariacao.toFixed(1) }}% vs mês anterior
                    </p>
                  </div>
                </div>
                <v-avatar size="56" color="success" variant="tonal">
                  <v-icon icon="mdi-cash-plus" size="32" />
                </v-avatar>
              </div>
              <!-- Progress bar -->
              <div class="progress-section">
                <p class="text-caption mb-2">
                  {{ summary.receitasRecebidas }} de {{ summary.receitasRecebidas + 2 }} recebidas
                </p>
                <v-progress-linear
                  :value="(summary.receitasRecebidas / (summary.receitasRecebidas + 2)) * 100"
                  color="success"
                  height="6"
                  rounded
                />
              </div>
            </v-card-item>
          </v-card>
        </v-col>

        <!-- Card: Despesas -->
        <v-col cols="12" sm="6" lg="3" class="mb-4">
          <v-card elevation="1" class="kpi-card kpi-error h-100">
            <v-card-item class="pb-0">
              <div class="d-flex justify-space-between align-start mb-4">
                <div class="flex-grow-1">
                  <p class="text-caption text-medium-emphasis mb-2">
                    Despesas {{ monthYearLabel }}
                  </p>
                  <h2 class="kpi-value mb-3">
                    {{ formatCurrency(summary.despesasMes) }}
                  </h2>
                  <div class="d-flex align-center gap-1">
                    <v-icon icon="mdi-trending-down" size="16" color="error" />
                    <p class="text-caption text-error mb-0">
                      -{{ despesasVariacao.toFixed(1) }}% vs mês anterior
                    </p>
                  </div>
                </div>
                <v-avatar size="56" color="error" variant="tonal">
                  <v-icon icon="mdi-cash-remove" size="32" />
                </v-avatar>
              </div>
              <div class="progress-section">
                <p class="text-caption mb-2">
                  {{ summary.despesasPagas }} de {{ summary.despesasPagas + 3 }} pagas
                </p>
                <v-progress-linear
                  :value="(summary.despesasPagas / (summary.despesasPagas + 3)) * 100"
                  color="error"
                  height="6"
                  rounded
                />
              </div>
            </v-card-item>
          </v-card>
        </v-col>

        <!-- Card: Saldo -->
        <v-col cols="12" sm="6" lg="3" class="mb-4">
          <v-card elevation="1" class="kpi-card kpi-primary h-100">
            <v-card-item class="pb-0">
              <div class="d-flex justify-space-between align-start mb-4">
                <div class="flex-grow-1">
                  <p class="text-caption text-medium-emphasis mb-2">
                    Saldo Total
                  </p>
                  <h2 class="kpi-value mb-3">
                    {{ formatCurrency(summary.saldoAtual) }}
                  </h2>
                  <p class="text-caption text-primary mb-0">
                    3 contas ativas
                  </p>
                </div>
                <v-avatar size="56" color="primary" variant="tonal">
                  <v-icon icon="mdi-wallet" size="32" />
                </v-avatar>
              </div>
              <div class="progress-section">
                <p class="text-caption mb-2">
                  Crescimento: +{{ saldoVariacao.toFixed(1) }}%
                </p>
                <v-progress-linear
                  :value="Math.min(saldoVariacao, 100)"
                  color="primary"
                  height="6"
                  rounded
                />
              </div>
            </v-card-item>
          </v-card>
        </v-col>

        <!-- Card: Pendências -->
        <v-col cols="12" sm="6" lg="3" class="mb-4">
          <v-card elevation="1" class="kpi-card kpi-warning h-100">
            <v-card-item class="pb-0">
              <div class="d-flex justify-space-between align-start mb-4">
                <div class="flex-grow-1">
                  <p class="text-caption text-medium-emphasis mb-2">
                    Pendências
                  </p>
                  <h2 class="kpi-value mb-3">
                    {{ formatCurrency(summary.totalPendencias) }}
                  </h2>
                  <p class="text-caption text-warning mb-0">
                    {{ counters.receitasPendentes + counters.despesasPendentes }} transações
                  </p>
                </div>
                <v-avatar size="56" color="warning" variant="tonal">
                  <v-icon icon="mdi-clock-outline" size="32" />
                </v-avatar>
              </div>
              <div class="progress-section">
                <v-btn
                  size="small"
                  color="warning"
                  variant="tonal"
                  block
                  @click="openPendenciasDialog(pendenciasTransacoes)"
                >
                  Ver Pendências
                </v-btn>
              </div>
            </v-card-item>
          </v-card>
        </v-col>
      </v-row>

      <!-- CHARTS AND DATA SECTION -->
      <v-row class="mb-6">
        <!-- Chart: Receitas vs Despesas -->
        <v-col cols="12" lg="8" class="mb-4">
          <v-card elevation="1">
            <v-card-item>
              <v-card-title class="text-h6 mb-4">
                <v-icon icon="mdi-chart-bar" class="mr-2" />
                Receitas vs Despesas
              </v-card-title>
            </v-card-item>
            <v-divider />
            <v-card-item>
              <div v-if="chartSeries.bar.length" class="chart-container">
                <apexchart
                  type="bar"
                  :options="chartOptions.bar"
                  :series="chartSeries.bar"
                  height="350"
                />
              </div>
              <div v-else class="text-center py-8">
                <p class="text-grey">Gráfico não disponível</p>
              </div>
            </v-card-item>
          </v-card>
        </v-col>

        <!-- Chart: Distribuição de Despesas -->
        <v-col cols="12" lg="4" class="mb-4">
          <v-card elevation="1">
            <v-card-item>
              <v-card-title class="text-h6 mb-4">
                <v-icon icon="mdi-chart-pie" class="mr-2" />
                Distribuição
              </v-card-title>
            </v-card-item>
            <v-divider />
            <v-card-item>
              <div v-if="chartSeries.pie.length" class="chart-container">
                <apexchart
                  type="donut"
                  :options="chartOptions.pie"
                  :series="chartSeries.pie"
                  height="350"
                />
              </div>
              <div v-else class="text-center py-8">
                <p class="text-grey">Gráfico não disponível</p>
              </div>
            </v-card-item>
          </v-card>
        </v-col>
      </v-row>

      <!-- RECENT TRANSACTIONS AND ALERTS -->
      <v-row>
        <!-- Recent Transactions -->
        <v-col cols="12" lg="8" class="mb-4">
          <v-card elevation="1">
            <v-card-item>
              <v-card-title class="text-h6 mb-4">
                <v-icon icon="mdi-history" class="mr-2" />
                Transações Recentes
              </v-card-title>
            </v-card-item>
            <v-divider />
            <v-card-item>
              <div v-if="recentTransactions.length" class="transactions-list">
                <div
                  v-for="(transaction, idx) in recentTransactions.slice(0, 5)"
                  :key="idx"
                  class="transaction-item"
                >
                  <div class="d-flex align-center justify-space-between">
                    <div class="d-flex align-center gap-3 flex-grow-1 min-width-0">
                      <v-avatar size="40" :color="transaction.tipo === 'receita' ? 'success' : 'error'" variant="tonal">
                        <v-icon :icon="transaction.tipo === 'receita' ? 'mdi-cash-plus' : 'mdi-cash-remove'" />
                      </v-avatar>
                      <div class="min-width-0">
                        <p class="text-subtitle-2 mb-0 text-truncate">
                          {{ transaction.descricao }}
                        </p>
                        <p class="text-caption text-grey mb-0">
                          {{ transaction.data }}
                        </p>
                      </div>
                    </div>
                    <p
                      class="text-subtitle-2 font-weight-bold mb-0"
                      :class="{ 'text-success': transaction.tipo === 'receita', 'text-error': transaction.tipo !== 'receita' }"
                    >
                      {{ transaction.tipo === 'receita' ? '+' : '-' }}{{ formatCurrency(transaction.valor) }}
                    </p>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8">
                <p class="text-grey">Nenhuma transação recente</p>
              </div>
            </v-card-item>
            <v-card-actions v-if="recentTransactions.length">
              <v-btn color="primary" variant="text" block>
                Ver todas as transações
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>

        <!-- Alerts & Quick Actions -->
        <v-col cols="12" lg="4">
          <v-card elevation="1" class="mb-4">
            <v-card-item>
              <v-card-title class="text-h6 mb-4">
                <v-icon icon="mdi-alert-circle" class="mr-2" />
                Alertas
              </v-card-title>
            </v-card-item>
            <v-divider />
            <v-card-item>
              <div v-if="alerts.length" class="alerts-list">
                <div
                  v-for="(alert, idx) in alerts.slice(0, 3)"
                  :key="idx"
                  class="alert-item mb-3"
                  :class="`alert-${alert.type}`"
                >
                  <div class="d-flex gap-2">
                    <v-icon :icon="alert.icon" size="20" />
                    <div>
                      <p class="text-caption font-weight-bold mb-1">
                        {{ alert.titulo }}
                      </p>
                      <p class="text-caption mb-0">
                        {{ alert.mensagem }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-4">
                <v-icon icon="mdi-check-circle" size="48" color="success" class="mb-2" />
                <p class="text-caption text-grey mb-0">
                  Tudo certo!
                </p>
              </div>
            </v-card-item>
          </v-card>

          <!-- Quick Actions -->
          <v-card elevation="1">
            <v-card-item>
              <v-card-title class="text-h6 mb-4">
                <v-icon icon="mdi-lightning-bolt" class="mr-2" />
                Ações Rápidas
              </v-card-title>
            </v-card-item>
            <v-divider />
            <v-card-item>
              <div class="quick-actions">
                <v-btn
                  color="primary"
                  variant="tonal"
                  block
                  class="mb-2"
                  prepend-icon="mdi-plus"
                >
                  Nova Receita
                </v-btn>
                <v-btn
                  color="error"
                  variant="tonal"
                  block
                  class="mb-2"
                  prepend-icon="mdi-minus"
                >
                  Nova Despesa
                </v-btn>
                <v-btn
                  color="info"
                  variant="tonal"
                  block
                  prepend-icon="mdi-file-chart"
                >
                  Gerar Relatório
                </v-btn>
              </div>
            </v-card-item>
          </v-card>
        </v-col>
      </v-row>
    </div>

    <!-- DIALOG: PENDÊNCIAS -->
    <v-dialog v-model="showPendenciasDialog" max-width="800">
      <v-card>
        <v-card-title class="d-flex align-center gap-2">
          <v-icon icon="mdi-clock-alert" color="warning" />
          Transações Pendentes
        </v-card-title>
        <v-divider />
        <v-card-text class="py-4">
          <div v-if="pendenciasTransacoes.length" class="pendencias-list">
            <div
              v-for="(transaction, idx) in pendenciasTransacoes.filter(t => t.status_lancamento === 'PENDENTE')"
              :key="idx"
              class="pendencia-item mb-3 pa-3 rounded border"
            >
              <div class="d-flex justify-space-between align-center mb-2">
                <div class="d-flex align-center gap-2">
                  <v-avatar
                    size="40"
                    :color="transaction.tipo_lancamento?.toLowerCase() === 'receita' ? 'success' : 'error'"
                    variant="tonal"
                  >
                    <v-icon
                      :icon="transaction.tipo_lancamento?.toLowerCase() === 'receita' ? 'mdi-cash-plus' : 'mdi-cash-remove'"
                    />
                  </v-avatar>
                  <div>
                    <p class="text-subtitle-2 font-weight-bold mb-0">
                      {{ transaction.descricao || 'Transação' }}
                    </p>
                    <p class="text-caption text-grey mb-0">
                      {{ transaction.data_vencimento || transaction.data || 'Data não disponível' }}
                    </p>
                  </div>
                </div>
                <div class="text-right">
                  <p
                    class="text-subtitle-2 font-weight-bold mb-0"
                    :class="{
                      'text-success': transaction.tipo_lancamento?.toLowerCase() === 'receita',
                      'text-error': transaction.tipo_lancamento?.toLowerCase() !== 'receita'
                    }"
                  >
                    {{ transaction.tipo_lancamento?.toLowerCase() === 'receita' ? '+' : '-' }}{{
                      formatCurrency(transaction.valor)
                    }}
                  </p>
                  <v-chip
                    size="small"
                    variant="outlined"
                    :color="transaction.status_lancamento === 'PENDENTE' ? 'warning' : 'info'"
                    class="mt-1"
                  >
                    {{ transaction.status_lancamento }}
                  </v-chip>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8">
            <v-icon icon="mdi-check-circle" size="64" color="success" class="mb-4" />
            <p class="text-grey">Nenhuma transação pendente para este período</p>
          </div>
        </v-card-text>
        <v-divider />
        <v-card-actions>
          <v-spacer />
          <v-btn color="primary" variant="text" @click="showPendenciasDialog = false">
            Fechar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

```

<script setup lang="ts">
import dashboardService from "@/services/dashboard.service";
import http from "@/services/http";
import { useToastStore } from "@/store/toast";
import { useUserStore } from "@/store/user";
import { computed, onMounted, ref, watch } from "vue";

const userStore = useUserStore();
const toastStore = useToastStore();
const loading = ref(true);
const counters = ref({
  receitasRecebidas: 0,
  receitasPendentes: 0,
  receitasAtrasadas: 0,
  despesasPagas: 0,
  despesasPendentes: 0,
  despesasAtrasadas: 0,
});

// Summary data
const summary = ref({
  receitasMes: 0,
  despesasMes: 0,
  saldoAtual: 0,
  saldoInicial: 0,
  totalReceitas: 0,
  totalDespesas: 0,
  pendencias: 0,
  receitasRecebidas: 0,
  despesasPagas: 0,
  totalPendencias: 0,
});

// Chart data
const chartOptions = ref<any>({
  bar: null,
  pie: null,
});

const chartSeries = ref<any>({
  bar: [],
  pie: [],
});

// Recent transactions
const recentTransactions = ref<any[]>([]);

// Alerts
const alerts = ref<any[]>([]);

// Dialog de Pendências
const showPendenciasDialog = ref(false);
const pendenciasTransacoes = ref<any[]>([]);

// LOCAL month state - not synced with userStore
const currentMonth = ref<string>(new Date().toISOString().slice(0, 7)); // YYYY-MM

// Month/Year label
const monthDisplay = computed(() => {
  const [year, month] = currentMonth.value.split('-');
  const date = new Date(`${year}-${month}-01`);
  return date.toLocaleString("pt-BR", { month: "long", year: "numeric" });
});

const monthDisplayFormatted = computed(() => {
  return monthDisplay.value;
});

const monthYearLabel = computed(() => {
  return `de ${monthDisplay.value}`;
});

// Check if current month is today
const isCurrentMonth = computed(() => {
  const today = new Date().toISOString().slice(0, 7);
  return currentMonth.value === today;
});

// Month navigation methods
const navigationMonth = (action: 'prev' | 'next' | 'today') => {
  if (action === 'prev') {
    const [year, month] = currentMonth.value.split('-');
    const date = new Date(`${year}-${month}-01`);
    date.setMonth(date.getMonth() - 1);
    currentMonth.value = date.toISOString().slice(0, 7);
  } else if (action === 'next') {
    const [year, month] = currentMonth.value.split('-');
    const date = new Date(`${year}-${month}-01`);
    date.setMonth(date.getMonth() + 1);
    currentMonth.value = date.toISOString().slice(0, 7);
  } else if (action === 'today') {
    currentMonth.value = new Date().toISOString().slice(0, 7);
  }
  // Watch com immediate: true cuidará de carregar os dados
};

// Format currency - valores vêm em centavos, dividir por 100
const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
  }).format(value / 100);
};

// Calcular variações percentuais (recebidas de mês anterior)
const receitasVariacao = computed(() => {
  // Sem dados de mês anterior, retorna 0
  // Em produção, isso viria do backend comparando meses
  return 0;
});

const despesasVariacao = computed(() => {
  // Sem dados de mês anterior, retorna 0
  // Em produção, isso viria do backend comparando meses
  return 0;
});

const saldoVariacao = computed(() => {
  // Baseado na diferença entre receitas e despesas
  const diferenca = summary.value.totalReceitas - summary.value.totalDespesas;
  if (summary.value.saldoInicial > 0) {
    const percentual = ((diferenca / summary.value.saldoInicial) * 100);
    return Math.max(0, Math.min(percentual, 100));
  }
  return 0;
});

// Abrir dialog de pendências
const openPendenciasDialog = (transactions: any[]) => {
  // Filtrar apenas transações pendentes
  pendenciasTransacoes.value = transactions.filter(t => t.status_lancamento === 'PENDENTE');
  showPendenciasDialog.value = true;
};

// Load data
const loadDashboardData = async () => {
  try {
    loading.value = true;
    
    // 1. Carregar todos os lançamentos do mês usando currentMonth
    let allTransactions: any[] = [];
    try {
      // Usar http.get com o mês correto
      const response = await http.get('/lancamentos', {
        params: {
          mesAno: currentMonth.value, // YYYY-MM format
          limit: 1000
        }
      });
      
      const data = response.data || response;
      allTransactions = data.data || data.lancamentos || [];
    } catch (err) {
      console.warn('Erro ao carregar transações:', err);
      try {
        // Fallback: tentar sem o mês
        allTransactions = await dashboardService.getRecentTransactions(1000);
      } catch (fallbackErr) {
        console.warn('Fallback também falhou:', fallbackErr);
      }
    }

    // 2. Separar receitas e despesas, calcular totais reais
    let totalReceitas = 0;
    let totalDespesas = 0;
    let totalPendencias = 0;
    let receitasRecebidas = 0;
    let receitasPendentes = 0;
    let receitasAtrasadas = 0;
    let despesasPagas = 0;
    let despesasPendentes = 0;
    let despesasAtrasadas = 0;

    allTransactions.forEach((item: any) => {
      const valor = item.valor || 0;
      const tipo = item.tipo_lancamento?.toLowerCase() || 'despesa';
      const status = item.status_lancamento || 'PENDENTE';

      if (tipo === 'receita') {
        if (status === 'EFETIVADA') {
          receitasRecebidas++;
          totalReceitas += valor;
        } else if (status === 'PENDENTE') {
          receitasPendentes++;
          // Adicionar ao total de pendências (independente se atrasada ou futura)
          totalPendencias += valor;
          // Nota: receitasAtrasadas seria determinado pelo backend com data_vencimento
          // Por agora, deixamos todos como "pendentes" genéricos
        }
      } else {
        if (status === 'EFETIVADA') {
          despesasPagas++;
          totalDespesas += valor;
        } else if (status === 'PENDENTE') {
          despesasPendentes++;
          // Adicionar ao total de pendências (independente se atrasada ou futura)
          totalPendencias += valor;
          // Nota: despesasAtrasadas seria determinado pelo backend com data_vencimento
          // Por agora, deixamos todos como "pendentes" genéricos
        }
      }
    });

    // 2.5. Armazenar transações para o dialog
    pendenciasTransacoes.value = allTransactions;

    // 3. Atualizar summary com dados reais
    summary.value = {
      receitasMes: totalReceitas,
      despesasMes: totalDespesas,
      saldoAtual: totalReceitas - totalDespesas,
      saldoInicial: 0,
      totalReceitas: totalReceitas,
      totalDespesas: totalDespesas,
      pendencias: totalPendencias,
      receitasRecebidas: receitasRecebidas,
      despesasPagas: despesasPagas,
      totalPendencias: totalPendencias,
    };

    // 4. Atualizar contadores
    counters.value = {
      receitasRecebidas,
      receitasPendentes,
      receitasAtrasadas,
      despesasPagas,
      despesasPendentes,
      despesasAtrasadas,
    };

    // 5. Configurar chart de barras
    const monthLabel = new Date().toLocaleString("pt-BR", { month: "short" });
    const months = [monthLabel];
    
    chartOptions.value.bar = {
      chart: {
        type: "bar",
        height: 350,
        toolbar: { show: false },
        sparkline: { enabled: false },
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: "55%",
          borderRadius: 5,
          dataLabels: { position: "top" },
        },
      },
      dataLabels: { enabled: false },
      stroke: { show: true, width: 2, colors: ["transparent"] },
      xaxis: { categories: months },
      yaxis: {
        title: { text: "R$ (milhares)" },
        labels: {
          formatter: (value: number) => `R$ ${(value / 1000).toFixed(0)}k`,
        },
      },
      fill: { opacity: 1 },
      tooltip: {
        y: {
          formatter: (val: number) =>
            new Intl.NumberFormat("pt-BR", {
              style: "currency",
              currency: "BRL",
            }).format(val / 100),
        },
      },
      colors: ["#4CAF50", "#F44336"],
      legend: { position: "top", horizontalAlign: "right" },
    };

    chartSeries.value.bar = [
      {
        name: "Receitas",
        data: [summary.value.totalReceitas || 0],
      },
      {
        name: "Despesas",
        data: [summary.value.totalDespesas || 0],
      },
    ];

    // 6. Calcular distribuição de categorias a partir dos dados reais
    try {
      // Agrupar despesas por categoria
      const categoriaMap = new Map<string, number>();
      
      allTransactions.forEach((item: any) => {
        const tipo = item.tipo_lancamento?.toLowerCase() || 'despesa';
        const status = item.status_lancamento || 'PENDENTE';
        
        // Somar apenas despesas EFETIVADAS
        if (tipo === 'despesa' && status === 'EFETIVADA') {
          const categoria = item.categoria || 'Outros';
          const valor = item.valor || 0;
          categoriaMap.set(categoria, (categoriaMap.get(categoria) || 0) + valor);
        }
      });

      // Preparar dados para o gráfico
      const labels = Array.from(categoriaMap.keys());
      const values = Array.from(categoriaMap.values());
      
      // Calcular percentuais
      const totalDespesas = values.reduce((a, b) => a + b, 0);
      const percentuais = values.map(v => (totalDespesas > 0 ? (v / totalDespesas) * 100 : 0));
      
      chartOptions.value.pie = {
        chart: { type: "donut", height: 350 },
        labels: labels.length > 0 ? labels : ['Sem dados'],
        colors: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF", "#C9CBCF", "#4BC0C0"],
        legend: { position: "bottom" },
        dataLabels: {
          enabled: true,
          formatter: (val: number) => `${val.toFixed(1)}%`,
        },
        tooltip: {
          y: {
            formatter: (val: number) =>
              new Intl.NumberFormat("pt-BR", {
                style: "currency",
                currency: "BRL",
              }).format(val / 100),
          },
        },
      };

      chartSeries.value.pie = percentuais.length > 0 ? percentuais : [100];
    } catch (err) {
      console.warn('Erro ao calcular distribuição de categorias:', err);
      // Usar valores padrão
      chartOptions.value.pie = {
        chart: { type: "donut", height: 350 },
        labels: ['Alimentação', 'Transporte', 'Moradia', 'Lazer', 'Outros'],
        colors: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"],
        legend: { position: "bottom" },
        dataLabels: {
          enabled: true,
          formatter: (val: number) => `${val.toFixed(1)}%`,
        },
      };
      chartSeries.value.pie = [25.2, 18.5, 30.1, 15.3, 10.9];
    }

    // 7. Carregar transações recentes com fallback
    try {
      const transactions = await dashboardService.getRecentTransactions(10);
      recentTransactions.value = transactions;
    } catch (err) {
      console.warn('Erro ao carregar transações recentes:', err);
      recentTransactions.value = [];
    }

    // 8. Gerar alertas dinâmicos baseado nos dados
    alerts.value = generateAlerts(counters.value);

    loading.value = false;
  } catch (error) {
    console.error("Erro ao carregar dados da dashboard:", error);
    // Mesmo com erro, mostrar dados vazios ao invés de travar
    loading.value = false;
  }
};

// Gerar alertas com base nos dados
const generateAlerts = (counters: any): any[] => {
  const alerts = [];

  // Alerta de receitas pendentes
  if (counters.receitasPendentes > 0) {
    alerts.push({
      tipo: "warning",
      titulo: "Receitas Pendentes",
      mensagem: `Você tem ${counters.receitasPendentes} receita(s) pendente(s)`,
      icon: "mdi-exclamation-circle",
      type: "warning",
    });
  }

  // Alerta de despesas pendentes
  if (counters.despesasPendentes > 0) {
    alerts.push({
      tipo: "warning",
      titulo: "Despesas Pendentes",
      mensagem: `Você tem ${counters.despesasPendentes} despesa(s) pendente(s)`,
      icon: "mdi-alert-circle",
      type: "warning",
    });
  }

  // Alerta de receitas atrasadas
  if (counters.receitasAtrasadas > 0) {
    alerts.push({
      tipo: "error",
      titulo: "Receitas Atrasadas",
      mensagem: `Atenção: ${counters.receitasAtrasadas} receita(s) atrasada(s)`,
      icon: "mdi-clock-alert",
      type: "error",
    });
  }

  // Se tudo certo, não mostrar nada
  if (alerts.length === 0) {
    alerts.push({
      tipo: "success",
      titulo: "Tudo em dia",
      mensagem: "Não há alertas pendentes",
      icon: "mdi-check-circle",
      type: "success",
    });
  }

  return alerts;
};

// Watch for local month changes - reload dashboard data
// Watch for local month changes - reload dashboard data
onMounted(() => {
  // Reset to current month on mount to ensure fresh data
  currentMonth.value = new Date().toISOString().slice(0, 7);
  // Load data after resetting the month
  loadDashboardData();
});

watch(() => currentMonth.value, () => {
  loadDashboardData();
}, { immediate: false }); // Set to false to avoid double load on mount
</script>

<style scoped lang="scss">
.dashboard-view {
  min-height: 100vh;
  background: rgb(var(--v-theme-background));
}

/* KPI Cards */
.kpi-card {
  border-radius: 12px;
  border: 1px solid rgba(var(--v-theme-primary), 0.1);
  transition: all 0.3s ease;
  overflow: hidden;

  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  }

  .kpi-value {
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1.2;
    color: rgb(var(--v-theme-on-background));
  }

  .progress-section {
    margin-top: 1rem;
  }
}

.kpi-success {
  border-left: 4px solid #4caf50;
}

.kpi-error {
  border-left: 4px solid #f44336;
}

.kpi-primary {
  border-left: 4px solid #1976d2;
}

.kpi-warning {
  border-left: 4px solid #ff9800;
}

/* Chart Container */
.chart-container {
  width: 100%;
  height: 100%;
}

/* Transactions List */
.transactions-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.transaction-item {
  padding: 1rem;
  border-radius: 8px;
  background: rgba(var(--v-theme-primary), 0.03);
  border: 1px solid rgba(var(--v-theme-primary), 0.1);
  transition: all 0.2s ease;

  &:hover {
    background: rgba(var(--v-theme-primary), 0.06);
  }
}

/* Alerts */
.alerts-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.alert-item {
  padding: 0.75rem;
  border-radius: 8px;
  border-left: 3px solid;

  &.alert-warning {
    background: rgba(#ff9800, 0.1);
    border-color: #ff9800;
    color: #e65100;
  }

  &.alert-info {
    background: rgba(#2196f3, 0.1);
    border-color: #2196f3;
    color: #0d47a1;
  }

  &.alert-success {
    background: rgba(#4caf50, 0.1);
    border-color: #4caf50;
    color: #1b5e20;
  }
}

/* Quick Actions */
.quick-actions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

/* Responsive */
@media (max-width: 600px) {
  .kpi-card .kpi-value {
    font-size: 1.5rem;
  }
}

/* Min width utilities */
.min-width-0 {
  min-width: 0;
}

.gap-1 {
  gap: 0.25rem;
}

.gap-2 {
  gap: 0.5rem;
}

.gap-3 {
  gap: 1rem;
}
</style>
