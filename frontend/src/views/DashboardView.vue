<template>
  <div class="dashboard-view">
    <!-- MAIN CONTENT -->
    <div>
      <!-- MONTH NAVIGATION BAR -->
      <v-card
        class="mb-6"
        elevation="1"
      >
        <v-card-text class="pa-4">
          <div class="d-flex align-center justify-center gap-4">
            <v-btn
              icon="mdi-chevron-left"
              color="primary"
              variant="outlined"
              size="small"
              title="Mês anterior"
              @click="navigationMonth('prev')"
            />
            <div
              class="text-center"
              style="min-width: 250px"
            >
              <v-btn
                variant="text"
                :text="monthDisplayFormatted.toUpperCase()"
                :class="{ 'text-primary font-weight-bold': isCurrentMonth }"
                title="Ir para o mês atual"
                @click="navigationMonth('today')"
              />
            </div>
            <v-btn
              icon="mdi-chevron-right"
              color="primary"
              variant="outlined"
              size="small"
              title="Próximo mês"
              @click="navigationMonth('next')"
            />
          </div>
        </v-card-text>
      </v-card>

      <!-- KPI CARDS SECTION -->
      <v-row class="mb-6">
        <!-- Card: Receitas -->
        <v-col
          cols="12"
          sm="6"
          lg="3"
          class="mb-4"
        >
          <v-card
            elevation="1"
            class="kpi-card kpi-success h-100"
          >
            <v-card-item class="pb-0">
              <div class="d-flex justify-space-between align-start mb-4">
                <div class="flex-grow-1">
                  <p class="text-caption text-medium-emphasis mb-2">
                    Receitas {{ monthYearLabel }}
                  </p>
                  <h2 class="kpi-value mb-3">
                    {{ formatCurrency(summary.valorTotalReceitasMes) }}
                  </h2>
                  <div class="d-flex align-center gap-1">
                    <v-icon
                      :icon="(summary.receitasVariacao || 0) >= 0 ? 'mdi-trending-up' : 'mdi-trending-down'"
                      size="16"
                      :color="(summary.receitasVariacao || 0) >= 0 ? 'success' : 'error'"
                    />
                    <p 
                      class="text-caption mb-0"
                      :class="(summary.receitasVariacao || 0) >= 0 ? 'text-success' : 'text-error'"
                    >
                      {{ (summary.receitasVariacao || 0) >= 0 ? '+' : '' }}{{ (summary.receitasVariacao || 0).toFixed(1) }}% vs mês anterior
                    </p>
                  </div>
                </div>
                <v-avatar
                  size="56"
                  color="success"
                  variant="tonal"
                >
                  <v-icon
                    icon="mdi-cash-plus"
                    size="32"
                  />
                </v-avatar>
              </div>
              <!-- Progress bar -->
              <div class="progress-section">
                <p class="text-caption mb-2">
                  {{ summary.qtdReceitasRecebidas }} de {{ summary.qtdReceitasTotal }} recebidas
                </p>
                <v-progress-linear
                  :model-value="(summary.qtdReceitasRecebidas / (summary.qtdReceitasTotal || 1)) * 100"
                  color="success"
                  height="6"
                  rounded
                />
              </div>
            </v-card-item>
          </v-card>
        </v-col>

        <!-- Card: Despesas -->
        <v-col
          cols="12"
          sm="6"
          lg="3"
          class="mb-4"
        >
          <v-card
            elevation="1"
            class="kpi-card kpi-error h-100"
          >
            <v-card-item class="pb-0">
              <div class="d-flex justify-space-between align-start mb-4">
                <div class="flex-grow-1">
                  <p class="text-caption text-medium-emphasis mb-2">
                    Despesas {{ monthYearLabel }}
                  </p>
                  <h2 class="kpi-value mb-3">
                    {{ formatCurrency(summary.valorTotalDespesasMes) }}
                  </h2>
                  <div class="d-flex align-center gap-1">
                    <v-icon
                      :icon="(summary.despesasVariacao || 0) <= 0 ? 'mdi-trending-down' : 'mdi-trending-up'"
                      size="16"
                      :color="(summary.despesasVariacao || 0) <= 0 ? 'success' : 'error'"
                    />
                    <p 
                      class="text-caption mb-0"
                      :class="(summary.despesasVariacao || 0) <= 0 ? 'text-success' : 'text-error'"
                    >
                      {{ (summary.despesasVariacao || 0) >= 0 ? '+' : '' }}{{ (summary.despesasVariacao || 0).toFixed(1) }}% vs mês anterior
                    </p>
                  </div>
                </div>
                <v-avatar
                  size="56"
                  color="error"
                  variant="tonal"
                >
                  <v-icon
                    icon="mdi-cash-remove"
                    size="32"
                  />
                </v-avatar>
              </div>
              <div class="progress-section">
                <p class="text-caption mb-2">
                  {{ summary.qtdDespesasPagas }} de {{ summary.qtdDespesasTotal }} pagas
                </p>
                <v-progress-linear
                  :model-value="(summary.qtdDespesasPagas / (summary.qtdDespesasTotal || 1)) * 100"
                  color="error"
                  height="6"
                  rounded
                />
              </div>
            </v-card-item>
          </v-card>
        </v-col>

        <!-- Card: Saldo -->
        <v-col
          cols="12"
          sm="6"
          lg="3"
          class="mb-4"
        >
          <v-card
            elevation="1"
            class="kpi-card kpi-primary h-100"
          >
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
                    {{ summary.qtd_contasAtivas }} contas ativas
                  </p>
                </div>
                <v-avatar
                  size="56"
                  color="primary"
                  variant="tonal"
                >
                  <v-icon
                    icon="mdi-wallet"
                    size="32"
                  />
                </v-avatar>
              </div>
              <div class="progress-section">
                <p class="text-caption mb-2">
                  Crescimento: +{{ (saldoVariacao || 0).toFixed(1) }}%
                </p>
                <v-progress-linear
                  :value="Math.min(saldoVariacao || 0, 100)"
                  color="primary"
                  height="6"
                  rounded
                />
              </div>
            </v-card-item>
          </v-card>
        </v-col>

        <!-- Card: Pendências -->
        <v-col
          cols="12"
          sm="6"
          lg="3"
          class="mb-4"
        >
          <v-card
            elevation="1"
            class="kpi-card kpi-warning h-100"
          >
            <v-card-item class="pb-0">
              <div class="d-flex justify-space-between align-start mb-4">
                <div class="flex-grow-1">
                  <p class="text-caption text-medium-emphasis mb-2">
                    Pendências
                  </p>
                  <h2 class="kpi-value mb-3">
                    {{ formatCurrency(summary.valorTotalReceitasPendentes) }}
                  </h2>
                  <p class="text-caption text-warning mb-0">
                    {{ summary.qtdReceitasPendentes + summary.qtdDespesasPendentes }} transações
                  </p>
                </div>
                <v-avatar
                  size="56"
                  color="warning"
                  variant="tonal"
                >
                  <v-icon
                    icon="mdi-clock-outline"
                    size="32"
                  />
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
        <v-col
          cols="12"
          lg="8"
          class="mb-4"
        >
          <v-card elevation="1">
            <v-card-item>
              <v-card-title class="text-h6 mb-4">
                <v-icon
                  icon="mdi-chart-bar"
                  class="mr-2"
                />
                Receitas vs Despesas
              </v-card-title>
            </v-card-item>
            <v-divider />
            <v-card-item>
              <div
                v-if="chartSeries.bar.length"
                class="chart-container"
              >
                <apexchart
                  type="bar"
                  :options="chartOptions.bar"
                  :series="chartSeries.bar"
                  height="350"
                />
              </div>
              <div
                v-else
                class="text-center py-8"
              >
                <p class="text-grey">
                  Gráfico não disponível
                </p>
              </div>
            </v-card-item>
          </v-card>
        </v-col>

        <!-- Chart: Distribuição de Despesas e Receitas -->
        <v-col
          cols="12"
          lg="4"
          class="mb-4"
        >
          <v-card elevation="1">
            <v-card-item>
              <v-card-title class="text-h6 mb-2">
                <v-icon
                  icon="mdi-chart-pie"
                  class="mr-2"
                />
                Distribuição
              </v-card-title>
            </v-card-item>
            <v-divider />
            
            <!-- Tabs -->
            <v-tabs
              v-model="distribuicaoTab"
              align-tabs="center"
              class="px-4"
            >
              <v-tab value="despesas">
                <v-icon icon="mdi-cash-remove" start size="18" />
                Despesas
              </v-tab>
              <v-tab value="receitas">
                <v-icon icon="mdi-cash-plus" start size="18" />
                Receitas
              </v-tab>
            </v-tabs>
            
            <v-divider />
            <v-card-item>
              <!-- Despesas Tab -->
              <div v-if="distribuicaoTab === 'despesas'">
                <div
                  v-if="chartSeriesDespesas.length"
                  class="chart-container"
                >
                  <apexchart
                    type="donut"
                    :options="chartOptionsDespesas"
                    :series="chartSeriesDespesas"
                    height="350"
                  />
                </div>
                <div
                  v-else
                  class="text-center py-8"
                >
                  <p class="text-grey">
                    Nenhuma despesa efetivada
                  </p>
                </div>
              </div>
              
              <!-- Receitas Tab -->
              <div v-if="distribuicaoTab === 'receitas'">
                <div
                  v-if="chartSeriesReceitas.length"
                  class="chart-container"
                >
                  <apexchart
                    type="donut"
                    :options="chartOptionsReceitas"
                    :series="chartSeriesReceitas"
                    height="350"
                  />
                </div>
                <div
                  v-else
                  class="text-center py-8"
                >
                  <p class="text-grey">
                    Nenhuma receita efetivada
                  </p>
                </div>
              </div>
            </v-card-item>
          </v-card>
        </v-col>
      </v-row>

      <!-- RECENT TRANSACTIONS AND ALERTS -->
      <v-row>
        <!-- Recent Transactions -->
        <v-col
          cols="12"
          lg="8"
          class="mb-4"
        >
          <v-card elevation="1">
            <v-card-item>
              <v-card-title class="text-h6 mb-4">
                <v-icon
                  icon="mdi-history"
                  class="mr-2"
                />
                Transações Recentes
              </v-card-title>
            </v-card-item>
            <v-divider />
            <v-card-item>
              <div
                v-if="recentTransactions.length"
                class="transactions-list"
              >
                <div
                  v-for="(transaction, idx) in recentTransactions.slice(0, 5)"
                  :key="idx"
                  class="transaction-item"
                >
                  <div class="d-flex align-center justify-space-between">
                    <div class="d-flex align-center gap-3 flex-grow-1 min-width-0">
                      <v-avatar
                        size="40"
                        :color="transaction.tipo_lancamento === 'RECEITA' ? 'success' : 'error'"
                        variant="tonal"
                      >
                        <v-icon :icon="transaction.tipo_lancamento === 'RECEITA' ? 'mdi-cash-plus' : 'mdi-cash-remove'" />
                      </v-avatar>
                      <div class="min-width-0">
                        <p class="text-subtitle-2 mb-0 text-truncate">
                          {{ transaction.descricao }}
                        </p>
                        <p class="text-caption text-grey mb-0">
                          {{ transaction.data_vencimento }}
                        </p>
                      </div>
                    </div>
                    <p
                      class="text-subtitle-2 font-weight-bold mb-0"
                      :class="{ 'text-success': transaction.tipo_lancamento === 'RECEITA', 'text-error': transaction.tipo_lancamento !== 'RECEITA' }"
                    >
                      {{ transaction.tipo_lancamento === 'RECEITA' ? '+' : '-' }}{{ formatCurrency(transaction.valor) }}
                    </p>
                  </div>
                </div>
              </div>
              <div
                v-else
                class="text-center py-8"
              >
                <p class="text-grey">
                  Nenhuma transação recente
                </p>
              </div>
            </v-card-item>
            <v-card-actions v-if="recentTransactions.length">
              <v-btn
                color="primary"
                variant="text"
                block
                @click="openTodasTransacoesDialog"
              >
                Ver todas as transações
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>

        <!-- Alerts & Quick Actions -->
        <v-col
          cols="12"
          lg="4"
        >
          <v-card
            elevation="1"
            class="mb-4"
          >
            <v-card-item>
              <v-card-title class="text-h6 mb-4">
                <v-icon
                  icon="mdi-alert-circle"
                  class="mr-2"
                />
                Alertas
              </v-card-title>
            </v-card-item>
            <v-divider />
            <v-card-item>
              <div
                v-if="alerts.length"
                class="alerts-list"
              >
                <div
                  v-for="(alert, idx) in alerts.slice(0, 3)"
                  :key="idx"
                  class="alert-item mb-3"
                  :class="`alert-${alert.type}`"
                >
                  <div class="d-flex gap-2">
                    <v-icon
                      :icon="alert.icon"
                      size="20"
                    />
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
              <div
                v-else
                class="text-center py-4"
              >
                <v-icon
                  icon="mdi-check-circle"
                  size="48"
                  color="success"
                  class="mb-2"
                />
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
                <v-icon
                  icon="mdi-lightning-bolt"
                  class="mr-2"
                />
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
    <v-dialog
      v-model="showPendenciasDialog"
      max-width="800"
    >
      <v-card>
        <v-card-title class="d-flex align-center gap-2">
          <v-icon
            icon="mdi-clock-alert"
            color="warning"
          />
          Transações Pendentes
        </v-card-title>
        <v-divider />
        <v-card-text class="py-4">
          <div
            v-if="pendenciasTransacoes.length"
            class="pendencias-list"
          >
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
          <div
            v-else
            class="text-center py-8"
          >
            <v-icon
              icon="mdi-check-circle"
              size="64"
              color="success"
              class="mb-4"
            />
            <p class="text-grey">
              Nenhuma transação pendente para este período
            </p>
          </div>
        </v-card-text>
        <v-divider />
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="primary"
            variant="text"
            @click="showPendenciasDialog = false"
          >
            Fechar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- DIALOG: TODAS AS TRANSAÇÕES -->
    <v-dialog
      v-model="showTodasTransacoesDialog"
      max-width="900"
    >
      <v-card>
        <v-card-title class="d-flex align-center gap-2">
          <v-icon
            icon="mdi-history"
            color="primary"
          />
          Todas as Transações
        </v-card-title>
        <v-divider />
        <v-card-text class="py-4">
          <div
            v-if="todasTransacoes.length"
            class="todas-transacoes-list"
          >
            <div
              v-for="(transaction, idx) in todasTransacoes"
              :key="idx"
              class="transacao-item mb-3 pa-3 rounded border"
            >
              <div class="d-flex justify-space-between align-center mb-2">
                <div class="d-flex align-center gap-2">
                  <v-avatar
                    size="40"
                    :color="transaction.tipo_lancamento === 'RECEITA' ? 'success' : 'error'"
                    variant="tonal"
                  >
                    <v-icon
                      :icon="transaction.tipo_lancamento === 'RECEITA' ? 'mdi-cash-plus' : 'mdi-cash-remove'"
                    />
                  </v-avatar>
                  <div>
                    <p class="text-subtitle-2 font-weight-bold mb-0">
                      {{ transaction.descricao || 'Transação' }}
                    </p>
                    <p class="text-caption text-grey mb-0">
                      {{ transaction.data_vencimento || transaction.data || 'Data não disponível' }}
                    </p>
                    <p 
                      v-if="transaction.categoria"
                      class="text-caption text-primary mb-0"
                    >
                      {{ transaction.categoria }}
                    </p>
                  </div>
                </div>
                <div class="text-right">
                  <p
                    class="text-subtitle-2 font-weight-bold mb-0"
                    :class="{
                      'text-success': transaction.tipo_lancamento === 'RECEITA',
                      'text-error': transaction.tipo_lancamento !== 'RECEITA'
                    }"
                  >
                    {{ transaction.tipo_lancamento === 'RECEITA' ? '+' : '-' }}{{
                      formatCurrency(transaction.valor)
                    }}
                  </p>
                  <v-chip
                    size="small"
                    variant="outlined"
                    :color="transaction.status_lancamento === 'EFETIVADA' ? 'success' : transaction.status_lancamento === 'PENDENTE' ? 'warning' : 'info'"
                    class="mt-1"
                  >
                    {{ transaction.status_lancamento }}
                  </v-chip>
                </div>
              </div>
            </div>
          </div>
          <div
            v-else
            class="text-center py-8"
          >
            <v-icon
              icon="mdi-inbox-multiple"
              size="64"
              color="grey"
              class="mb-4"
            />
            <p class="text-grey">
              Nenhuma transação disponível
            </p>
          </div>
        </v-card-text>
        <v-divider />
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="primary"
            variant="text"
            @click="showTodasTransacoesDialog = false"
          >
            Fechar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Loading Overlay - Carregamento Inicial -->
    <v-overlay
      v-model="loading"
      class="align-center justify-center"
      persistent
      contained
    >
      <div class="text-center">
        <v-progress-circular
          indeterminate
          size="80"
          width="6"
          color="primary"
          class="mb-6"
        />
        <div class="text-h6 text-white mb-2">
          Carregando Dashboard...
        </div>
        <div class="text-caption text-white-50">
          Preparando seus dados financeiros
        </div>
      </div>
    </v-overlay>
  </div>
</template>

```

<script setup lang="ts">
import http from "@/services/http";
import { useToastStore } from "@/store/toast";
import { useUserStore } from "@/store/user";
import { computed, onMounted, ref, watch } from "vue";

const userStore = useUserStore();
const toastStore = useToastStore();
const loading = ref(true);

// Summary data
const summary = ref({
  valorTotalReceitasMes: 0,
  valorTotalReceitasPendentes: 0,
  receitasVariacao: 0,
  qtdReceitasTotal: 0,
  qtdReceitasRecebidas: 0,
  qtdReceitasPendentes: 0,
  totalReceitas: 0,
  receitasRecebidas: 0,

  valorTotalDespesasMes: 0,
  despesasVariacao: 0,
  qtdDespesasTotal: 0,
  qtdDespesasPagas: 0,
  qtdDespesasPendentes: 0,
  totalDespesas: 0,
  despesasPagas: 0,

  qtdPendencias: 0,
  totalPendencias: 0,

  saldoAtual: 0,
  qtd_contasAtivas: 0,
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

// Tabs para distribuição (despesas e receitas)
const distribuicaoTab = ref<'despesas' | 'receitas'>('despesas');

// Chart series separadas para cada tipo
const chartSeriesDespesas = ref<any>([]);
const chartSeriesReceitas = ref<any>([]);
const chartOptionsDespesas = ref<any>({});
const chartOptionsReceitas = ref<any>({});

// Valores reais (em centavos) para tooltip dos gráficos de distribuição
const valoresTotaisDespesas = ref<number[]>([]);
const valoresTotaisReceitas = ref<number[]>([]);

// Recent transactions
const recentTransactions = ref<any[]>([]);

// Alerts
const alerts = ref<any[]>([]);

// Dialog de Pendências
const showPendenciasDialog = ref(false);
const pendenciasTransacoes = ref<any[]>([]);

// Dialog de Todas as Transações
const showTodasTransacoesDialog = ref(false);
const todasTransacoes = ref<any[]>([]);

// LOCAL month state - not synced with userStore
// Get current month in local timezone (not UTC)
const getCurrentMonth = (): string => {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, "0");
  return `${year}-${month}`;
};

const currentMonth = ref<string>(getCurrentMonth()); // YYYY-MM

// Month/Year label
const monthDisplay = computed(() => {
  const [year, month] = currentMonth.value.split("-");
  const date = new Date(Number(year), Number(month) - 1, 1);
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
  return currentMonth.value === getCurrentMonth();
});

// Month navigation methods
const navigationMonth = (action: "prev" | "next" | "today") => {
  if (action === "prev") {
    const [year, month] = currentMonth.value.split("-");
    // Converter string em números inteiros
    const yearNum = parseInt(year, 10);
    const monthNum = parseInt(month, 10);
    
    // Calcular mês anterior
    let newMonth = monthNum - 1;
    let newYear = yearNum;
    
    if (newMonth < 1) {
      newMonth = 12;
      newYear = newYear - 1;
    }
    
    currentMonth.value = `${newYear}-${String(newMonth).padStart(2, "0")}`;
  } else if (action === "next") {
    const now = new Date();
    const currentYear = now.getFullYear();
    const currentMonthNum = now.getMonth() + 1; // 1-12
    
    const [year, month] = currentMonth.value.split("-");
    const yearNum = parseInt(year, 10);
    const monthNum = parseInt(month, 10);
    
    // Calcular próximo mês
    let nextMonth = monthNum + 1;
    let nextYear = yearNum;
    
    if (nextMonth > 12) {
      nextMonth = 1;
      nextYear = nextYear + 1;
    }
    
    // Verificar se não ultrapassa o mês atual
    if (nextYear < currentYear || (nextYear === currentYear && nextMonth <= currentMonthNum)) {
      currentMonth.value = `${nextYear}-${String(nextMonth).padStart(2, "0")}`;
    }
  } else if (action === "today") {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, "0");
    currentMonth.value = `${year}-${month}`;
  }
  // Watch cuidará de carregar os dados
};

// Format currency - valores vêm em centavos, dividir por 100
const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
  }).format(value / 100);
};

// Calcular variações percentuais (recebidas de mês anterior)
// const receitasVariacao = computed(() => {
//   // Retorna a variação do summary ou 0 se não definido
//   return summary.value.receitasVariacao || 0;
// });

// const despesasVariacao = computed(() => {
//   // Sem dados de mês anterior, retorna 0
//   return summary.value.despesasVariacao || 0;
// });

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
  // pendenciasTransacoes.value = transactions.filter(t => t.status_lancamento === "PENDENTE");
  showPendenciasDialog.value = true;
};

// Abrir dialog com todas as transações
const openTodasTransacoesDialog = () => {
  todasTransacoes.value = recentTransactions.value;
  showTodasTransacoesDialog.value = true;
};

// Load data
const loadDashboardData = async () => {
  try {
    loading.value = true;
    
    // 1. Carregar todos os lançamentos do mês usando currentMonth
    let allTransactions: any[] = [];
    let lancamentosReceitas: any[] = [];
    let lancamentosDespesas: any[] = [];
    try {
      // Usar http.get com o mês correto
      const response = await http.get("/dashboard/summary", {
        params: {
          mesAno: currentMonth.value, // YYYY-MM format
          limit: 1000
        }
      });

      summary.value.valorTotalReceitasMes = response.data.receitas.valor_total || 0;
      summary.value.valorTotalReceitasPendentes = response.data.receitas.valor_pendente || 0;
      summary.value.receitasVariacao = response.data.receitas.variacao || 0;
      summary.value.qtdReceitasRecebidas = response.data.receitas.qtd_efetivada || 0;
      summary.value.qtdReceitasTotal = response.data.receitas.qtd_total || 0;
      summary.value.qtdReceitasPendentes = response.data.receitas.qtd_pendente || 0;
      summary.value.totalReceitas = response.data.receitas.total || 0;

      summary.value.valorTotalDespesasMes = response.data.despesas.valor_total || 0;
      summary.value.despesasVariacao = response.data.despesas.variacao || 0;
      summary.value.qtdDespesasPagas = response.data.despesas.qtd_efetivada || 0;
      summary.value.qtdDespesasTotal = response.data.despesas.qtd_total || 0;
      summary.value.qtdDespesasPendentes = response.data.despesas.qtd_pendente || 0;
      summary.value.totalDespesas = response.data.despesas.total || 0;
      lancamentosDespesas = response.data.lancamentos?.despesas || [];
      lancamentosReceitas = response.data.lancamentos?.receitas || [];

      summary.value.qtdPendencias = response.data.pendentes.qtd_pendentes || 0;
      summary.value.totalPendencias = response.data.totalPendencias || 0;

      summary.value.saldoAtual = response.data.saldos.atual || 0;
      summary.value.qtd_contasAtivas = response.data.contas.qtd_contas_ativas || 0;
      pendenciasTransacoes.value = response.data.pendentes.lancamentos || [];

      // Mapeamento correto de transações recentes
      recentTransactions.value = [
        ...response.data.transacoes_recentes.receitas,
        ...response.data.transacoes_recentes.despesas
      ] || [];
      console.log(recentTransactions.value);

      allTransactions = [
        ...response.data.lancamentos.receitas,
        ...response.data.lancamentos.despesas
      ] || [];
    } catch (err) {
      console.warn("Erro ao carregar transações:", err);
      try {
        // Fallback: tentar sem o mês
        // allTransactions = await dashboardService.getRecentTransactions(1000);
      } catch (fallbackErr) {
        console.warn("Fallback também falhou:", fallbackErr);
      }
    }

    
  const counters = ref({
    receitasRecebidas: 0,
    receitasPendentes: 0,
    receitasAtrasadas: 0,
    despesasPagas: 0,
    despesasPendentes: 0,
    despesasAtrasadas: 0,
  });


    allTransactions.forEach((item: any) => {
      const data = item.data_vencimento;
      const tipo = item.tipo_lancamento;
      const status = item.status_lancamento;

      if (tipo === "RECEITA") {
        if (status === "EFETIVADA") {
          counters.value.receitasRecebidas++;
        } else if (status === "PENDENTE" && data < new Date()) {
          counters.value.receitasAtrasadas++;
        } else if (status === "PENDENTE") {
          counters.value.receitasPendentes++;
        }
     } else {
        if (status === "EFETIVADA") {
          counters.value.despesasPagas++;
        } else if (status === "PENDENTE" && data < new Date()) {
          counters.value.despesasAtrasadas++;
        } else if (status === "PENDENTE") {
          counters.value.despesasPendentes++;
        }
      }
    });

    // 2.5. Armazenar transações para o dialog
    // pendenciasTransacoes.value = allTransactions;

    // 3. Atualizar summary com dados reais

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
        title: { text: "R$" },
        labels: {
          formatter: (value: number) => {
            // Converter centavos para reais (dividir por 100)
            const reais = value / 100;
            // Se valor >= 1000 reais, mostrar em milhares
            if (reais >= 1000) {
              return `R$ ${(reais / 1000).toFixed(1)}k`;
            }
            // Caso contrário, mostrar em reais com 2 casas decimais
            return `R$ ${reais.toFixed(2)}`;
          },
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
        data: [summary.value.valorTotalReceitasMes || 0],
      },
      {
        name: "Despesas",
        data: [summary.value.valorTotalDespesasMes || 0],
      },
    ];

    // 6. Calcular distribuição de categorias DESPESAS
    try {
      // Usar variável lancamentosDespesas que já foi populada acima
      const categoriaDespesasMap = new Map<string, number>();
      
      lancamentosDespesas.forEach((item: any) => {
        const status = item.status_lancamento || "PENDENTE";
        
        // Somar apenas despesas EFETIVADAS
        if (status === "EFETIVADA") {
          const categoria = item.categoria || "Outros";
          const valor = item.valor || 0;
          categoriaDespesasMap.set(categoria, (categoriaDespesasMap.get(categoria) || 0) + valor);
        }
      });

      // Preparar dados para o gráfico de DESPESAS
      const labelsDespesas = Array.from(categoriaDespesasMap.keys());
      const valuesDespesas = Array.from(categoriaDespesasMap.values());
      
      // Guardar valores reais para usar no tooltip
      valoresTotaisDespesas.value = valuesDespesas;
      
      // Calcular percentuais
      const totalDespesasGraf = valuesDespesas.reduce((a, b) => a + b, 0);
      const percentuaisDespesas = valuesDespesas.map(v => (totalDespesasGraf > 0 ? (v / totalDespesasGraf) * 100 : 0));
      
      chartOptionsDespesas.value = {
        chart: { type: "donut", height: 350 },
        labels: labelsDespesas.length > 0 ? labelsDespesas : ["Sem despesas"],
        colors: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF", "#C9CBCF", "#FF7043"],
        legend: { position: "bottom" },
        dataLabels: {
          enabled: true,
          formatter: (val: number) => `${val.toFixed(1)}%`,
        },
        tooltip: {
          custom: ({ series, seriesIndex, w }: any) => {
            const valor = valoresTotaisDespesas.value[seriesIndex] || 0;
            const percentual = series[seriesIndex] || 0;
            const label = labelsDespesas[seriesIndex] || "Sem dados";
            const valorFormatado = new Intl.NumberFormat("pt-BR", {
              style: "currency",
              currency: "BRL",
            }).format(valor / 100);
            // Pegar a cor da série
            const cores = ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF", "#C9CBCF", "#FF7043"];
            const cor = cores[seriesIndex] || "#666";
            return `<div class="apexcharts-tooltip-custom" style="background-color: ${cor};">
              <span><strong>${label}</strong></span><br/>
              <span>${valorFormatado}</span><br/>
              <span>${percentual.toFixed(1)}%</span>
            </div>`;
          },
        },
      };

      chartSeriesDespesas.value = percentuaisDespesas.length > 0 ? percentuaisDespesas : [100];
    } catch (err) {
      console.warn("Erro ao calcular distribuição de despesas:", err);
      chartOptionsDespesas.value = {
        chart: { type: "donut", height: 350 },
        labels: ["Sem dados"],
        colors: ["#E0E0E0"],
        legend: { position: "bottom" },
      };
      chartSeriesDespesas.value = [100];
    }

    // 6B. Calcular distribuição de categorias RECEITAS
    try {
      // Usar variável lancamentosReceitas que já foi populada acima
      const categoriaReceitasMap = new Map<string, number>();
      
      lancamentosReceitas.forEach((item: any) => {
        const status = item.status_lancamento || "PENDENTE";
        
        // Somar apenas receitas EFETIVADAS
        if (status === "EFETIVADA") {
          const categoria = item.categoria || "Outros";
          const valor = item.valor || 0;
          categoriaReceitasMap.set(categoria, (categoriaReceitasMap.get(categoria) || 0) + valor);
        }
      });

      // Preparar dados para o gráfico de RECEITAS
      const labelsReceitas = Array.from(categoriaReceitasMap.keys());
      const valuesReceitas = Array.from(categoriaReceitasMap.values());
      
      // Guardar valores reais para usar no tooltip
      valoresTotaisReceitas.value = valuesReceitas;
      
      // Calcular percentuais
      const totalReceitasGraf = valuesReceitas.reduce((a, b) => a + b, 0);
      const percentuaisReceitas = valuesReceitas.map(v => (totalReceitasGraf > 0 ? (v / totalReceitasGraf) * 100 : 0));
      
      chartOptionsReceitas.value = {
        chart: { type: "donut", height: 350 },
        labels: labelsReceitas.length > 0 ? labelsReceitas : ["Sem receitas"],
        colors: ["#66BB6A", "#42A5F5", "#AB47BC", "#EC407A", "#29B6F6", "#78909C", "#FFCA28"],
        legend: { position: "bottom" },
        dataLabels: {
          enabled: true,
          formatter: (val: number) => `${val.toFixed(1)}%`,
        },
        tooltip: {
          custom: ({ series, seriesIndex, w }: any) => {
            const valor = valoresTotaisReceitas.value[seriesIndex] || 0;
            const percentual = series[seriesIndex] || 0;
            const label = labelsReceitas[seriesIndex] || "Sem dados";
            const valorFormatado = new Intl.NumberFormat("pt-BR", {
              style: "currency",
              currency: "BRL",
            }).format(valor / 100);
            // Pegar a cor da série
            const cores = ["#66BB6A", "#42A5F5", "#AB47BC", "#EC407A", "#29B6F6", "#78909C", "#FFCA28"];
            const cor = cores[seriesIndex] || "#666";
            return `<div class="apexcharts-tooltip-custom" style="background-color: ${cor};">
              <span><strong>${label}</strong></span><br/>
              <span>${valorFormatado}</span><br/>
              <span>${percentual.toFixed(1)}%</span>
            </div>`;
          },
        },
      };

      chartSeriesReceitas.value = percentuaisReceitas.length > 0 ? percentuaisReceitas : [100];
    } catch (err) {
      console.warn("Erro ao calcular distribuição de receitas:", err);
      chartOptionsReceitas.value = {
        chart: { type: "donut", height: 350 },
        labels: ["Sem dados"],
        colors: ["#E0E0E0"],
        legend: { position: "bottom" },
      };
      chartSeriesReceitas.value = [100];
    }

    // 7. Carregar transações recentes com fallback
    // try {
      // const transactions = await dashboardService.getRecentTransactions(10);
    //   recentTransactions.value = [response.data.transacoes_recentes || []];
    // } catch (err) {
    //   console.warn("Erro ao carregar transações recentes:", err);
    //   recentTransactions.value = [];
    // }

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
  // Reset to current month on mount to ensure fresh data (using local timezone)
  currentMonth.value = getCurrentMonth();
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

/* ApexCharts Custom Tooltip */
:deep(.apexcharts-tooltip-custom) {
  border: none;
  border-radius: 6px;
  padding: 8px 12px;
  font-size: 13px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  display: flex;
  flex-direction: column;
  gap: 4px;
  color: #fff;
  font-weight: 500;

  span {
    margin: 0;
    padding: 0;
    display: block;
    line-height: 1.4;
  }

  strong {
    font-weight: 600;
    color: #fff;
  }
}
</style>
