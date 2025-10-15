<template>
  <v-container fluid class="trader-panel pa-6">
    <v-row>
      <v-col cols="12">
        <div class="d-flex align-center mb-2">
          <v-btn
            icon="mdi-arrow-left"
            variant="text"
            color="success"
            @click="$router.push({ name: 'dashboard' })"
            class="mr-3"
          />
          <h1 class="text-h4 d-flex align-center">
            <v-icon icon="mdi-chart-line" size="36" class="mr-3" color="success" />
            Painel do Trader
          </h1>
        </div>
        <p class="text-subtitle-1 text-grey mb-6 ml-14">
          Acompanhe seus investimentos e análises do mercado
        </p>
      </v-col>
    </v-row>

    <!-- Loading -->
    <v-row v-if="loading">
      <v-col cols="12" class="text-center py-12">
        <v-progress-circular indeterminate color="success" size="64" />
        <p class="mt-4 text-grey">Carregando dados...</p>
      </v-col>
    </v-row>

    <!-- Conteúdo Principal -->
    <div v-else>
      <!-- Cards de Resumo -->
      <v-row class="mb-6">
        <v-col cols="12" md="3">
          <v-card elevation="2" class="stat-card">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-grey">Portfólio Total</p>
                  <h2 class="text-h5 text-success">R$ 45.230,00</h2>
                  <p class="text-caption text-success">+12.5% este mês</p>
                </div>
                <v-icon icon="mdi-wallet" size="48" color="success" class="stat-icon" />
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="3">
          <v-card elevation="2" class="stat-card">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-grey">Investimentos Ativos</p>
                  <h2 class="text-h5">12</h2>
                  <p class="text-caption text-grey">Em 5 categorias</p>
                </div>
                <v-icon icon="mdi-chart-pie" size="48" color="primary" class="stat-icon" />
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="3">
          <v-card elevation="2" class="stat-card">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-grey">Rendimento Mensal</p>
                  <h2 class="text-h5 text-info">R$ 1.850,00</h2>
                  <p class="text-caption text-grey">Média últimos 6 meses</p>
                </div>
                <v-icon icon="mdi-trending-up" size="48" color="info" class="stat-icon" />
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="3">
          <v-card elevation="2" class="stat-card">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-grey">Diversificação</p>
                  <h2 class="text-h5">85%</h2>
                  <p class="text-caption text-success">Ótima distribuição</p>
                </div>
                <v-icon icon="mdi-chart-scatter-plot" size="48" color="warning" class="stat-icon" />
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Tabs de Conteúdo -->
      <v-card elevation="3">
        <v-tabs v-model="activeTab" bg-color="success" class="tabs-header">
          <v-tab value="investimentos">
            <v-icon icon="mdi-bank" class="mr-2" />
            Meus Investimentos
          </v-tab>
          <v-tab value="analises">
            <v-icon icon="mdi-chart-box" class="mr-2" />
            Análises
          </v-tab>
          <v-tab value="rentabilidade">
            <v-icon icon="mdi-chart-timeline-variant" class="mr-2" />
            Rentabilidade
          </v-tab>
          <v-tab value="alertas">
            <v-icon icon="mdi-bell-alert" class="mr-2" />
            Alertas
          </v-tab>
        </v-tabs>

        <v-window v-model="activeTab">
          <!-- Tab: Meus Investimentos -->
          <v-window-item value="investimentos">
            <v-card-text class="pa-6">
              <div class="d-flex justify-space-between align-center mb-4">
                <h2 class="text-h5">Portfolio de Investimentos</h2>
                <v-btn color="success" prepend-icon="mdi-plus">
                  Novo Investimento
                </v-btn>
              </div>

              <!-- Lista de Investimentos -->
              <v-row>
                <v-col v-for="investment in mockInvestments" :key="investment.id" cols="12" md="6" lg="4">
                  <v-card elevation="2" class="investment-card">
                    <v-card-title class="d-flex align-center">
                      <v-avatar :color="investment.color" size="40" class="mr-3">
                        <v-icon :icon="investment.icon" color="white" />
                      </v-avatar>
                      <div>
                        <div class="text-subtitle-1">{{ investment.name }}</div>
                        <div class="text-caption text-grey">{{ investment.type }}</div>
                      </div>
                    </v-card-title>
                    <v-card-text>
                      <div class="mb-2">
                        <span class="text-caption text-grey">Valor Investido:</span>
                        <div class="text-h6">{{ formatCurrency(investment.invested) }}</div>
                      </div>
                      <div class="mb-2">
                        <span class="text-caption text-grey">Valor Atual:</span>
                        <div class="text-h6" :class="investment.profit >= 0 ? 'text-success' : 'text-error'">
                          {{ formatCurrency(investment.current) }}
                        </div>
                      </div>
                      <v-divider class="my-2" />
                      <div class="d-flex justify-space-between align-center">
                        <div>
                          <span class="text-caption text-grey">Rentabilidade:</span>
                          <div :class="investment.profit >= 0 ? 'text-success' : 'text-error'" class="font-weight-bold">
                            {{ investment.profit >= 0 ? '+' : '' }}{{ investment.profit.toFixed(2) }}%
                          </div>
                        </div>
                        <v-chip :color="investment.profit >= 0 ? 'success' : 'error'" size="small" variant="flat">
                          {{ investment.profit >= 0 ? '↑' : '↓' }} 
                          {{ formatCurrency(investment.current - investment.invested) }}
                        </v-chip>
                      </div>
                    </v-card-text>
                    <v-card-actions>
                      <v-btn variant="text" size="small" color="primary">
                        <v-icon icon="mdi-chart-line" class="mr-1" />
                        Ver Detalhes
                      </v-btn>
                      <v-spacer />
                      <v-btn icon="mdi-dots-vertical" variant="text" size="small" />
                    </v-card-actions>
                  </v-card>
                </v-col>
              </v-row>
            </v-card-text>
          </v-window-item>

          <!-- Tab: Análises -->
          <v-window-item value="analises">
            <v-card-text class="pa-6">
              <h2 class="text-h5 mb-4">Análises de Mercado</h2>
              
              <v-row>
                <v-col cols="12" md="6">
                  <v-card elevation="2">
                    <v-card-title>Distribuição por Categoria</v-card-title>
                    <v-card-text>
                      <div class="text-center py-8">
                        <v-icon icon="mdi-chart-donut" size="120" color="grey" />
                        <p class="text-grey mt-4">Gráfico de distribuição em desenvolvimento</p>
                      </div>
                    </v-card-text>
                  </v-card>
                </v-col>

                <v-col cols="12" md="6">
                  <v-card elevation="2">
                    <v-card-title>Performance Histórica</v-card-title>
                    <v-card-text>
                      <div class="text-center py-8">
                        <v-icon icon="mdi-chart-areaspline" size="120" color="grey" />
                        <p class="text-grey mt-4">Gráfico de evolução em desenvolvimento</p>
                      </div>
                    </v-card-text>
                  </v-card>
                </v-col>

                <v-col cols="12">
                  <v-card elevation="2">
                    <v-card-title>Comparativo de Ativos</v-card-title>
                    <v-card-text>
                      <v-data-table
                        :headers="analysisHeaders"
                        :items="analysisData"
                        items-per-page="5"
                      >
                        <template #item.performance="{ item }">
                          <v-chip
                            :color="item.performance >= 0 ? 'success' : 'error'"
                            size="small"
                            variant="flat"
                          >
                            {{ item.performance >= 0 ? '+' : '' }}{{ item.performance }}%
                          </v-chip>
                        </template>
                        <template #item.risk="{ item }">
                          <v-chip
                            :color="getRiskColor(item.risk)"
                            size="small"
                            variant="outlined"
                          >
                            {{ item.risk }}
                          </v-chip>
                        </template>
                      </v-data-table>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
            </v-card-text>
          </v-window-item>

          <!-- Tab: Rentabilidade -->
          <v-window-item value="rentabilidade">
            <v-card-text class="pa-6">
              <h2 class="text-h5 mb-4">Histórico de Rentabilidade</h2>
              
              <v-card elevation="2" class="mb-4">
                <v-card-text>
                  <div class="text-center py-8">
                    <v-icon icon="mdi-chart-line-variant" size="150" color="grey" />
                    <p class="text-h6 text-grey mt-4">Gráfico de Rentabilidade</p>
                    <p class="text-grey">Visualização detalhada em desenvolvimento</p>
                  </div>
                </v-card-text>
              </v-card>

              <v-row>
                <v-col cols="12" md="4">
                  <v-card elevation="2">
                    <v-card-text>
                      <p class="text-caption text-grey">Retorno Último Mês</p>
                      <h2 class="text-h4 text-success">+5.2%</h2>
                      <p class="text-caption">R$ 2.345,00</p>
                    </v-card-text>
                  </v-card>
                </v-col>
                <v-col cols="12" md="4">
                  <v-card elevation="2">
                    <v-card-text>
                      <p class="text-caption text-grey">Retorno Último Ano</p>
                      <h2 class="text-h4 text-success">+18.7%</h2>
                      <p class="text-caption">R$ 8.456,00</p>
                    </v-card-text>
                  </v-card>
                </v-col>
                <v-col cols="12" md="4">
                  <v-card elevation="2">
                    <v-card-text>
                      <p class="text-caption text-grey">Retorno Total</p>
                      <h2 class="text-h4 text-success">+32.5%</h2>
                      <p class="text-caption">R$ 14.679,00</p>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
            </v-card-text>
          </v-window-item>

          <!-- Tab: Alertas -->
          <v-window-item value="alertas">
            <v-card-text class="pa-6">
              <div class="d-flex justify-space-between align-center mb-4">
                <h2 class="text-h5">Alertas e Notificações</h2>
                <v-btn color="primary" prepend-icon="mdi-plus" variant="outlined">
                  Novo Alerta
                </v-btn>
              </div>

              <v-row>
                <v-col cols="12" md="6" lg="4" v-for="alert in mockAlerts" :key="alert.id">
                  <v-card :color="alert.type" variant="tonal" elevation="2">
                    <v-card-text>
                      <div class="d-flex align-center mb-2">
                        <v-icon :icon="alert.icon" :color="alert.type" class="mr-2" />
                        <span class="font-weight-bold">{{ alert.title }}</span>
                      </div>
                      <p class="text-body-2">{{ alert.message }}</p>
                      <div class="d-flex align-center justify-space-between mt-3">
                        <span class="text-caption">{{ alert.time }}</span>
                        <v-btn :color="alert.type" size="small" variant="flat">
                          Ver Detalhes
                        </v-btn>
                      </div>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>

              <v-divider class="my-6" />

              <h3 class="text-h6 mb-3">Configurar Alertas</h3>
              <v-card elevation="2">
                <v-card-text>
                  <v-list>
                    <v-list-item>
                      <v-list-item-title>Alerta de Valorização</v-list-item-title>
                      <v-list-item-subtitle>Notificar quando um ativo valorizar mais de 5%</v-list-item-subtitle>
                      <template #append>
                        <v-switch color="success" hide-details />
                      </template>
                    </v-list-item>
                    <v-divider />
                    <v-list-item>
                      <v-list-item-title>Alerta de Desvalorização</v-list-item-title>
                      <v-list-item-subtitle>Notificar quando um ativo desvalorizar mais de 3%</v-list-item-subtitle>
                      <template #append>
                        <v-switch color="error" hide-details />
                      </template>
                    </v-list-item>
                    <v-divider />
                    <v-list-item>
                      <v-list-item-title>Relatório Mensal</v-list-item-title>
                      <v-list-item-subtitle>Receber relatório consolidado todo dia 1º</v-list-item-subtitle>
                      <template #append>
                        <v-switch color="info" hide-details />
                      </template>
                    </v-list-item>
                  </v-list>
                </v-card-text>
              </v-card>
            </v-card-text>
          </v-window-item>
        </v-window>
      </v-card>
    </div>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue';

// State
const activeTab = ref('investimentos');
const loading = ref(false);

// Mock data para investimentos
const mockInvestments = [
  {
    id: 1,
    name: 'Tesouro Selic 2027',
    type: 'Renda Fixa',
    icon: 'mdi-bank',
    color: 'success',
    invested: 10000,
    current: 11250,
    profit: 12.5,
  },
  {
    id: 2,
    name: 'Ações PETR4',
    type: 'Renda Variável',
    icon: 'mdi-chart-line',
    color: 'primary',
    invested: 5000,
    current: 6200,
    profit: 24.0,
  },
  {
    id: 3,
    name: 'FII HGLG11',
    type: 'Fundos Imobiliários',
    icon: 'mdi-office-building',
    color: 'warning',
    invested: 8000,
    current: 8960,
    profit: 12.0,
  },
  {
    id: 4,
    name: 'CDB Banco Inter',
    type: 'Renda Fixa',
    icon: 'mdi-cash-multiple',
    color: 'info',
    invested: 15000,
    current: 16500,
    profit: 10.0,
  },
  {
    id: 5,
    name: 'Bitcoin',
    type: 'Criptomoeda',
    icon: 'mdi-bitcoin',
    color: 'orange',
    invested: 3000,
    current: 2700,
    profit: -10.0,
  },
  {
    id: 6,
    name: 'Ações VALE3',
    type: 'Renda Variável',
    icon: 'mdi-chart-line',
    color: 'error',
    invested: 4000,
    current: 4880,
    profit: 22.0,
  },
];

// Headers para tabela de análise
const analysisHeaders = [
  { title: 'Ativo', value: 'name' },
  { title: 'Categoria', value: 'category' },
  { title: 'Performance', value: 'performance' },
  { title: 'Risco', value: 'risk' },
  { title: 'Liquidez', value: 'liquidity' },
];

const analysisData = [
  { name: 'Tesouro Selic', category: 'Renda Fixa', performance: 12.5, risk: 'Baixo', liquidity: 'Alta' },
  { name: 'PETR4', category: 'Ações', performance: 24.0, risk: 'Alto', liquidity: 'Alta' },
  { name: 'HGLG11', category: 'FII', performance: 12.0, risk: 'Médio', liquidity: 'Média' },
  { name: 'CDB', category: 'Renda Fixa', performance: 10.0, risk: 'Baixo', liquidity: 'Baixa' },
  { name: 'Bitcoin', category: 'Cripto', performance: -10.0, risk: 'Muito Alto', liquidity: 'Alta' },
];

// Mock alerts
const mockAlerts = [
  {
    id: 1,
    type: 'success',
    icon: 'mdi-trending-up',
    title: 'Valorização Detectada',
    message: 'PETR4 subiu 5.2% nas últimas 24h',
    time: 'Há 2 horas',
  },
  {
    id: 2,
    type: 'warning',
    icon: 'mdi-alert',
    title: 'Atenção ao Mercado',
    message: 'Alta volatilidade detectada em criptomoedas',
    time: 'Há 5 horas',
  },
  {
    id: 3,
    type: 'info',
    icon: 'mdi-information',
    title: 'Dividendos Recebidos',
    message: 'R$ 156,00 creditados de HGLG11',
    time: 'Ontem',
  },
];

// Methods
const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(value);
};

const getRiskColor = (risk: string): string => {
  const colors: Record<string, string> = {
    'Baixo': 'success',
    'Médio': 'warning',
    'Alto': 'error',
    'Muito Alto': 'error',
  };
  return colors[risk] || 'grey';
};
</script>

<style scoped>
.trader-panel {
  background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
  min-height: 100vh;
}

.stat-card {
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-4px);
}

.stat-icon {
  opacity: 0.2;
}

.investment-card {
  height: 100%;
  transition: transform 0.2s, box-shadow 0.2s;
}

.investment-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.tabs-header {
  border-bottom: 1px solid rgba(0,0,0,0.12);
}
</style>
