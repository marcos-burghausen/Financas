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
      <!-- Cards de Resumo com Gradientes Modernos -->
      <v-row class="mb-6">
        <v-col cols="12" sm="6" md="3">
          <v-card elevation="4" class="stat-card">
            <div class="stat-card-gradient-success pa-4">
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-white mb-1">Portfólio Total</p>
                  <h2 class="text-h4 text-white font-weight-bold mb-2">R$ 45.230</h2>
                  <v-chip size="x-small" color="white" text-color="success" class="font-weight-bold">
                    <v-icon start size="14">mdi-trending-up</v-icon>
                    +12.5% este mês
                  </v-chip>
                </div>
                <v-avatar color="rgba(255,255,255,0.2)" size="56">
                  <v-icon size="32" color="white">mdi-wallet</v-icon>
                </v-avatar>
              </div>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card elevation="4" class="stat-card">
            <div class="stat-card-gradient-primary pa-4">
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-white mb-1">Investimentos Ativos</p>
                  <h2 class="text-h4 text-white font-weight-bold mb-2">12</h2>
                  <v-chip size="x-small" color="white" text-color="primary" class="font-weight-bold">
                    <v-icon start size="14">mdi-folder-multiple</v-icon>
                    5 categorias
                  </v-chip>
                </div>
                <v-avatar color="rgba(255,255,255,0.2)" size="56">
                  <v-icon size="32" color="white">mdi-chart-pie</v-icon>
                </v-avatar>
              </div>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card elevation="4" class="stat-card">
            <div class="stat-card-gradient-info pa-4">
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-white mb-1">Rendimento Mensal</p>
                  <h2 class="text-h4 text-white font-weight-bold mb-2">R$ 1.850</h2>
                  <v-chip size="x-small" color="white" text-color="info" class="font-weight-bold">
                    <v-icon start size="14">mdi-chart-line</v-icon>
                    Média 6 meses
                  </v-chip>
                </div>
                <v-avatar color="rgba(255,255,255,0.2)" size="56">
                  <v-icon size="32" color="white">mdi-trending-up</v-icon>
                </v-avatar>
              </div>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card elevation="4" class="stat-card">
            <div class="stat-card-gradient-warning pa-4">
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-white mb-1">Diversificação</p>
                  <h2 class="text-h4 text-white font-weight-bold mb-2">85%</h2>
                  <v-chip size="x-small" color="white" text-color="warning" class="font-weight-bold">
                    <v-icon start size="14">mdi-check-circle</v-icon>
                    Ótima distribuição
                  </v-chip>
                </div>
                <v-avatar color="rgba(255,255,255,0.2)" size="56">
                  <v-icon size="32" color="white">mdi-chart-scatter-plot</v-icon>
                </v-avatar>
              </div>
            </div>
          </v-card>
        </v-col>

        <!-- Cards Adicionais -->
        <v-col cols="12" sm="6" md="3">
          <v-card elevation="4" class="stat-card">
            <div class="stat-card-gradient-purple pa-4">
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-white mb-1">Retorno Anual</p>
                  <h2 class="text-h4 text-white font-weight-bold mb-2">+32.5%</h2>
                  <v-chip size="x-small" color="white" text-color="purple" class="font-weight-bold">
                    <v-icon start size="14">mdi-trophy</v-icon>
                    Acima da meta
                  </v-chip>
                </div>
                <v-avatar color="rgba(255,255,255,0.2)" size="56">
                  <v-icon size="32" color="white">mdi-chart-timeline-variant</v-icon>
                </v-avatar>
              </div>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card elevation="4" class="stat-card">
            <div class="stat-card-gradient-teal pa-4">
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-white mb-1">Maior Ganho</p>
                  <h2 class="text-h4 text-white font-weight-bold mb-2">+24%</h2>
                  <v-chip size="x-small" color="white" text-color="teal" class="font-weight-bold">
                    <v-icon start size="14">mdi-star</v-icon>
                    PETR4
                  </v-chip>
                </div>
                <v-avatar color="rgba(255,255,255,0.2)" size="56">
                  <v-icon size="32" color="white">mdi-rocket-launch</v-icon>
                </v-avatar>
              </div>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card elevation="4" class="stat-card">
            <div class="stat-card-gradient-indigo pa-4">
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-white mb-1">Dividendos Recebidos</p>
                  <h2 class="text-h4 text-white font-weight-bold mb-2">R$ 856</h2>
                  <v-chip size="x-small" color="white" text-color="indigo" class="font-weight-bold">
                    <v-icon start size="14">mdi-calendar-month</v-icon>
                    Este mês
                  </v-chip>
                </div>
                <v-avatar color="rgba(255,255,255,0.2)" size="56">
                  <v-icon size="32" color="white">mdi-cash-multiple</v-icon>
                </v-avatar>
              </div>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card elevation="4" class="stat-card">
            <div class="stat-card-gradient-error pa-4">
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-white mb-1">Ativos em Queda</p>
                  <h2 class="text-h4 text-white font-weight-bold mb-2">1</h2>
                  <v-chip size="x-small" color="white" text-color="error" class="font-weight-bold">
                    <v-icon start size="14">mdi-alert</v-icon>
                    Requer atenção
                  </v-chip>
                </div>
                <v-avatar color="rgba(255,255,255,0.2)" size="56">
                  <v-icon size="32" color="white">mdi-trending-down</v-icon>
                </v-avatar>
              </div>
            </div>
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
              <div class="d-flex justify-space-between align-center mb-4">
                <h2 class="text-h5">Análises de Mercado</h2>
                <div class="d-flex gap-2">
                  <v-btn color="success" prepend-icon="mdi-microsoft-excel" variant="outlined" @click="exportToExcel">
                    Excel
                  </v-btn>
                  <v-btn color="error" prepend-icon="mdi-file-pdf-box" variant="outlined" @click="exportToPDF">
                    PDF
                  </v-btn>
                  <v-btn color="primary" prepend-icon="mdi-file-delimited" variant="outlined" @click="exportToCSV">
                    CSV
                  </v-btn>
                </div>
              </div>

              <!-- Filtros de Período -->
              <v-card elevation="2" class="mb-4">
                <v-card-text>
                  <v-row>
                    <v-col cols="12" md="3">
                      <v-select
                        v-model="selectedPeriod"
                        :items="periodOptions"
                        label="Período"
                        density="comfortable"
                        variant="outlined"
                        prepend-inner-icon="mdi-calendar-range"
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" md="3">
                      <v-text-field
                        v-model="dateStart"
                        type="date"
                        label="Data Inicial"
                        density="comfortable"
                        variant="outlined"
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" md="3">
                      <v-text-field
                        v-model="dateEnd"
                        type="date"
                        label="Data Final"
                        density="comfortable"
                        variant="outlined"
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" md="3" class="d-flex gap-2">
                      <v-btn color="success" prepend-icon="mdi-filter" @click="applyFilters">
                        Filtrar
                      </v-btn>
                      <v-btn color="grey" prepend-icon="mdi-filter-off" variant="outlined" @click="clearFilters">
                        Limpar
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
              
              <v-row>
                <v-col cols="12" md="6">
                  <v-card elevation="2">
                    <v-card-title class="bg-gradient-success">
                      <v-icon icon="mdi-chart-donut" class="mr-2" />
                      Distribuição por Categoria
                    </v-card-title>
                    <v-card-text style="height: 300px;">
                      <apexchart
                        type="donut"
                        height="280"
                        :options="categoryChartOptions"
                        :series="categoryChartSeries"
                      />
                    </v-card-text>
                  </v-card>
                </v-col>

                <v-col cols="12" md="6">
                  <v-card elevation="2">
                    <v-card-title class="bg-gradient-primary">
                      <v-icon icon="mdi-chart-bar" class="mr-2" />
                      Performance por Ativo
                    </v-card-title>
                    <v-card-text style="height: 300px;">
                      <apexchart
                        type="bar"
                        height="280"
                        :options="performanceChartOptions"
                        :series="performanceChartSeries"
                      />
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
              <div class="d-flex justify-space-between align-center mb-4">
                <h2 class="text-h5">Histórico de Rentabilidade</h2>
                <v-btn-group>
                  <v-btn :variant="rentabilityPeriod === '1M' ? 'flat' : 'outlined'" color="success" @click="rentabilityPeriod = '1M'">1M</v-btn>
                  <v-btn :variant="rentabilityPeriod === '3M' ? 'flat' : 'outlined'" color="success" @click="rentabilityPeriod = '3M'">3M</v-btn>
                  <v-btn :variant="rentabilityPeriod === '6M' ? 'flat' : 'outlined'" color="success" @click="rentabilityPeriod = '6M'">6M</v-btn>
                  <v-btn :variant="rentabilityPeriod === '1A' ? 'flat' : 'outlined'" color="success" @click="rentabilityPeriod = '1A'">1A</v-btn>
                  <v-btn :variant="rentabilityPeriod === 'ALL' ? 'flat' : 'outlined'" color="success" @click="rentabilityPeriod = 'ALL'">Tudo</v-btn>
                </v-btn-group>
              </div>
              
              <v-row class="mb-4">
                <!-- Gráfico Principal: Evolução do Portfólio -->
                <v-col cols="12">
                  <v-card elevation="2">
                    <v-card-title class="bg-gradient-success">
                      <v-icon icon="mdi-chart-line" class="mr-2" />
                      Evolução do Portfólio
                    </v-card-title>
                    <v-card-text style="height: 400px;">
                      <apexchart
                        type="area"
                        height="380"
                        :options="portfolioChartOptions"
                        :series="portfolioChartSeries"
                      />
                    </v-card-text>
                  </v-card>
                </v-col>

                <!-- Gráfico: Retorno Mensal -->
                <v-col cols="12" md="6">
                  <v-card elevation="2">
                    <v-card-title class="bg-gradient-info">
                      <v-icon icon="mdi-chart-bar" class="mr-2" />
                      Retorno Mensal
                    </v-card-title>
                    <v-card-text style="height: 350px;">
                      <apexchart
                        type="bar"
                        height="330"
                        :options="monthlyReturnChartOptions"
                        :series="monthlyReturnChartSeries"
                      />
                    </v-card-text>
                  </v-card>
                </v-col>

                <!-- Gráfico: Alocação de Ativos -->
                <v-col cols="12" md="6">
                  <v-card elevation="2">
                    <v-card-title class="bg-gradient-warning">
                      <v-icon icon="mdi-chart-pie" class="mr-2" />
                      Alocação de Ativos
                    </v-card-title>
                    <v-card-text style="height: 350px;">
                      <apexchart
                        type="pie"
                        height="330"
                        :options="allocationChartOptions"
                        :series="allocationChartSeries"
                      />
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>

              <!-- Cards de Resumo de Rentabilidade -->
              <v-row>
                <v-col cols="12" md="4">
                  <v-card elevation="4" class="stat-card">
                    <div class="stat-card-gradient-success pa-4">
                      <div class="d-flex align-center justify-space-between">
                        <div>
                          <p class="text-caption text-white mb-1">Retorno Último Mês</p>
                          <h2 class="text-h4 text-white font-weight-bold mb-2">+5.2%</h2>
                          <v-chip size="x-small" color="white" text-color="success">
                            <v-icon start size="14">mdi-cash-plus</v-icon>
                            R$ 2.345
                          </v-chip>
                        </div>
                        <v-avatar color="rgba(255,255,255,0.2)" size="56">
                          <v-icon size="32" color="white">mdi-calendar-month</v-icon>
                        </v-avatar>
                      </div>
                    </div>
                  </v-card>
                </v-col>
                <v-col cols="12" md="4">
                  <v-card elevation="4" class="stat-card">
                    <div class="stat-card-gradient-info pa-4">
                      <div class="d-flex align-center justify-space-between">
                        <div>
                          <p class="text-caption text-white mb-1">Retorno Último Ano</p>
                          <h2 class="text-h4 text-white font-weight-bold mb-2">+18.7%</h2>
                          <v-chip size="x-small" color="white" text-color="info">
                            <v-icon start size="14">mdi-trophy</v-icon>
                            R$ 8.456
                          </v-chip>
                        </div>
                        <v-avatar color="rgba(255,255,255,0.2)" size="56">
                          <v-icon size="32" color="white">mdi-calendar-year</v-icon>
                        </v-avatar>
                      </div>
                    </div>
                  </v-card>
                </v-col>
                <v-col cols="12" md="4">
                  <v-card elevation="4" class="stat-card">
                    <div class="stat-card-gradient-purple pa-4">
                      <div class="d-flex align-center justify-space-between">
                        <div>
                          <p class="text-caption text-white mb-1">Retorno Total</p>
                          <h2 class="text-h4 text-white font-weight-bold mb-2">+32.5%</h2>
                          <v-chip size="x-small" color="white" text-color="purple">
                            <v-icon start size="14">mdi-star</v-icon>
                            R$ 14.679
                          </v-chip>
                        </div>
                        <v-avatar color="rgba(255,255,255,0.2)" size="56">
                          <v-icon size="32" color="white">mdi-chart-timeline-variant</v-icon>
                        </v-avatar>
                      </div>
                    </div>
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

// Filtros de Período
const selectedPeriod = ref('30d');
const dateStart = ref('');
const dateEnd = ref('');
const rentabilityPeriod = ref('6M');

const periodOptions = [
  { title: 'Últimos 7 dias', value: '7d' },
  { title: 'Últimos 30 dias', value: '30d' },
  { title: 'Últimos 3 meses', value: '3m' },
  { title: 'Últimos 6 meses', value: '6m' },
  { title: 'Último ano', value: '1y' },
  { title: 'Tudo', value: 'all' },
];

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

// ========== CONFIGURAÇÕES DOS GRÁFICOS ==========

// Gráfico: Distribuição por Categoria (Donut)
const categoryChartOptions = {
  chart: {
    type: 'donut',
    toolbar: { show: false },
  },
  labels: ['Renda Fixa', 'Renda Variável', 'Fundos Imobiliários', 'Criptomoedas'],
  colors: ['#4CAF50', '#2196F3', '#FF9800', '#F44336'],
  legend: {
    position: 'bottom',
  },
  dataLabels: {
    enabled: true,
    formatter: (val: number) => `${val.toFixed(1)}%`,
  },
  plotOptions: {
    pie: {
      donut: {
        size: '65%',
        labels: {
          show: true,
          total: {
            show: true,
            label: 'Total Investido',
            formatter: () => 'R$ 45.230',
          },
        },
      },
    },
  },
};

const categoryChartSeries = [25000, 10200, 8000, 2030];

// Gráfico: Performance por Ativo (Bar)
const performanceChartOptions = {
  chart: {
    type: 'bar',
    toolbar: { show: false },
  },
  plotOptions: {
    bar: {
      horizontal: true,
      borderRadius: 8,
      distributed: true,
    },
  },
  colors: ['#4CAF50', '#2196F3', '#FF9800', '#00BCD4', '#F44336', '#9C27B0'],
  dataLabels: {
    enabled: true,
    formatter: (val: number) => `${val.toFixed(1)}%`,
  },
  xaxis: {
    categories: ['Tesouro Selic', 'PETR4', 'HGLG11', 'CDB', 'Bitcoin', 'VALE3'],
  },
  legend: {
    show: false,
  },
};

const performanceChartSeries = [
  {
    name: 'Performance',
    data: [12.5, 24.0, 12.0, 10.0, -10.0, 22.0],
  },
];

// Gráfico: Evolução do Portfólio (Area)
const portfolioChartOptions = {
  chart: {
    type: 'area',
    toolbar: { show: true },
    zoom: { enabled: true },
  },
  dataLabels: {
    enabled: false,
  },
  stroke: {
    curve: 'smooth',
    width: 3,
  },
  colors: ['#4CAF50'],
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.7,
      opacityTo: 0.3,
    },
  },
  xaxis: {
    categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
  },
  yaxis: {
    labels: {
      formatter: (val: number) => `R$ ${(val / 1000).toFixed(0)}k`,
    },
  },
  tooltip: {
    y: {
      formatter: (val: number) => `R$ ${val.toLocaleString('pt-BR')}`,
    },
  },
};

const portfolioChartSeries = [
  {
    name: 'Valor do Portfólio',
    data: [30000, 32500, 31800, 35200, 37500, 39800, 41200, 42800, 43500, 44200, 45000, 45230],
  },
];

// Gráfico: Retorno Mensal (Bar)
const monthlyReturnChartOptions = {
  chart: {
    type: 'bar',
    toolbar: { show: false },
  },
  plotOptions: {
    bar: {
      borderRadius: 8,
      columnWidth: '60%',
      distributed: false,
    },
  },
  colors: ['#2196F3'],
  dataLabels: {
    enabled: false,
  },
  xaxis: {
    categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
  },
  yaxis: {
    labels: {
      formatter: (val: number) => `${val.toFixed(1)}%`,
    },
  },
  tooltip: {
    y: {
      formatter: (val: number) => `+${val.toFixed(2)}%`,
    },
  },
};

const monthlyReturnChartSeries = [
  {
    name: 'Retorno',
    data: [2.5, 3.2, 1.8, 4.5, 3.8, 4.2, 5.1, 4.8, 3.5, 4.0, 5.2, 5.8],
  },
];

// Gráfico: Alocação de Ativos (Pie)
const allocationChartOptions = {
  chart: {
    type: 'pie',
    toolbar: { show: false },
  },
  labels: ['Tesouro Selic', 'PETR4', 'HGLG11', 'CDB', 'Bitcoin', 'VALE3'],
  colors: ['#4CAF50', '#2196F3', '#FF9800', '#00BCD4', '#F44336', '#9C27B0'],
  legend: {
    position: 'bottom',
  },
  dataLabels: {
    enabled: true,
    formatter: (val: number) => `${val.toFixed(1)}%`,
  },
};

const allocationChartSeries = [11250, 6200, 8960, 16500, 2700, 4880];

// ========== MÉTODOS ==========

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

// Métodos de Filtro
const applyFilters = () => {
  console.log('Aplicando filtros:', {
    period: selectedPeriod.value,
    dateStart: dateStart.value,
    dateEnd: dateEnd.value,
  });
  // TODO: Implementar lógica de filtro com API
};

const clearFilters = () => {
  selectedPeriod.value = '30d';
  dateStart.value = '';
  dateEnd.value = '';
  console.log('Filtros limpos');
};

// Métodos de Exportação
const exportToExcel = () => {
  console.log('Exportando para Excel...');
  // TODO: Implementar exportação para Excel
  alert('Exportação para Excel será implementada com a API');
};

const exportToPDF = () => {
  console.log('Exportando para PDF...');
  // TODO: Implementar exportação para PDF
  alert('Exportação para PDF será implementada com a API');
};

const exportToCSV = () => {
  console.log('Exportando para CSV...');
  // TODO: Implementar exportação para CSV
  alert('Exportação para CSV será implementada com a API');
};
</script>

<style scoped>
.trader-panel {
  background: #f5f5f5;
  min-height: 100vh;
}

.stat-card {
  transition: transform 0.3s, box-shadow 0.3s;
  overflow: hidden;
}

.stat-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

/* Gradientes para Cards de Estatísticas */
.stat-card-gradient-primary {
  background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
  border-radius: 8px;
}

.stat-card-gradient-success {
  background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);
  border-radius: 8px;
}

.stat-card-gradient-info {
  background: linear-gradient(135deg, #00BCD4 0%, #0097A7 100%);
  border-radius: 8px;
}

.stat-card-gradient-warning {
  background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
  border-radius: 8px;
}

.stat-card-gradient-error {
  background: linear-gradient(135deg, #F44336 0%, #D32F2F 100%);
  border-radius: 8px;
}

.stat-card-gradient-purple {
  background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%);
  border-radius: 8px;
}

.stat-card-gradient-teal {
  background: linear-gradient(135deg, #009688 0%, #00796B 100%);
  border-radius: 8px;
}

.stat-card-gradient-indigo {
  background: linear-gradient(135deg, #3F51B5 0%, #303F9F 100%);
  border-radius: 8px;
}

/* Gradientes para Headers de Gráficos */
.bg-gradient-primary {
  background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
  color: white;
}

.bg-gradient-success {
  background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);
  color: white;
}

.bg-gradient-info {
  background: linear-gradient(135deg, #00BCD4 0%, #0097A7 100%);
  color: white;
}

.bg-gradient-warning {
  background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
  color: white;
}

.investment-card {
  height: 100%;
  transition: transform 0.2s, box-shadow 0.2s;
}

.investment-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.tabs-header {
  border-bottom: 1px solid rgba(0,0,0,0.12);
}
</style>
