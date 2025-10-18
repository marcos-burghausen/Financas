<template>
  <main-layout>
    <div class="dashboard-view">
      <!-- LOADING STATE -->
      <v-row v-if="loading" class="py-12">
        <v-col cols="12" class="text-center">
          <v-progress-circular indeterminate color="primary" size="64" />
          <p class="text-grey mt-4">Carregando dados...</p>
        </v-col>
      </v-row>

      <!-- MAIN CONTENT -->
      <v-row v-else class="mb-4" no-gutters>
        <!-- KPI CARDS -->
        <v-col cols="12">
          <v-row class="mb-6">
            <!-- Card: Receitas -->
            <v-col cols="12" sm="6" lg="3" class="mb-4">
              <v-card elevation="0" class="kpi-card kpi-success h-100">
                <v-card-item class="pb-2">
                  <div class="d-flex justify-space-between align-start">
                    <div class="flex-grow-1">
                      <p class="text-caption text-medium-emphasis mb-1">
                        Receitas {{ monthYearLabel }}
                      </p>
                      <h2 class="kpi-value">
                        {{ formatCurrency(15240.50) }}
                      </h2>
                      <p class="text-caption text-success mt-1">
                        <v-icon icon="mdi-trending-up" size="16" class="mr-1" />
                        +12.5% vs mês anterior
                      </p>
                    </div>
                    <v-icon icon="mdi-cash-plus" class="kpi-icon" color="success" />
                  </div>
                </v-card-item>
              </v-card>
            </v-col>

            <!-- Card: Despesas -->
            <v-col cols="12" sm="6" lg="3" class="mb-4">
              <v-card elevation="0" class="kpi-card kpi-error h-100">
                <v-card-item class="pb-2">
                  <div class="d-flex justify-space-between align-start">
                    <div class="flex-grow-1">
                      <p class="text-caption text-medium-emphasis mb-1">
                        Despesas {{ monthYearLabel }}
                      </p>
                      <h2 class="kpi-value">
                        {{ formatCurrency(-8350.75) }}
                      </h2>
                      <p class="text-caption text-error mt-1">
                        <v-icon icon="mdi-trending-down" size="16" class="mr-1" />
                        -5.2% vs mês anterior
                      </p>
                    </div>
                    <v-icon icon="mdi-cash-remove" class="kpi-icon" color="error" />
                  </div>
                </v-card-item>
              </v-card>
            </v-col>

            <!-- Card: Saldo -->
            <v-col cols="12" sm="6" lg="3" class="mb-4">
              <v-card elevation="0" class="kpi-card kpi-primary h-100">
                <v-card-item class="pb-2">
                  <div class="d-flex justify-space-between align-start">
                    <div class="flex-grow-1">
                      <p class="text-caption text-medium-emphasis mb-1">
                        Saldo Total
                      </p>
                      <h2 class="kpi-value">
                        {{ formatCurrency(25890.35) }}
                      </h2>
                      <p class="text-caption text-primary mt-1">
                        3 contas ativas
                      </p>
                    </div>
                    <v-icon icon="mdi-wallet" class="kpi-icon" color="primary" />
                  </div>
                </v-card-item>
              </v-card>
            </v-col>

            <!-- Card: Score Saúde -->
            <v-col cols="12" sm="6" lg="3" class="mb-4">
              <v-card elevation="0" class="kpi-card kpi-info h-100">
                <v-card-item class="pb-2">
                  <div class="d-flex justify-space-between align-start">
                    <div class="flex-grow-1">
                      <p class="text-caption text-medium-emphasis mb-1">
                        Score Saúde Financeira
                      </p>
                      <h2 class="kpi-value">
                        78/100
                      </h2>
                      <div class="mt-2">
                        <v-progress-linear
                          :value="78"
                          color="info"
                          height="4"
                          rounded
                        />
                      </div>
                    </div>
                    <v-icon icon="mdi-heart-pulse" class="kpi-icon" color="info" />
                  </div>
                </v-card-item>
              </v-card>
            </v-col>
          </v-row>
        </v-col>

        <!-- GRÁFICOS E DADOS -->
        <v-col cols="12" lg="8" class="mb-4">
          <v-card elevation="0" class="data-card">
            <v-card-item>
              <v-card-title class="pb-2">
                <div class="d-flex align-center justify-space-between">
                  <span>Fluxo de Caixa</span>
                  <v-menu transition="slide-x-transition">
                    <template #activator="{ props }">
                      <v-btn
                        v-bind="props"
                        icon
                        size="small"
                        variant="text"
                      >
                        <v-icon icon="mdi-dots-vertical" />
                      </v-btn>
                    </template>
                    <v-list>
                      <v-list-item title="Últimos 7 dias" />
                      <v-list-item title="Últimos 30 dias" />
                      <v-list-item title="Este ano" />
                    </v-list>
                  </v-menu>
                </div>
              </v-card-title>
            </v-card-item>

            <v-card-text>
              <div class="chart-placeholder">
                <v-icon icon="mdi-chart-line" size="64" color="primary" opacity="0.5" />
                <p class="text-center text-grey mt-4">
                  Gráfico de fluxo de caixa será exibido aqui
                </p>
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- SIDEBAR DIREITO -->
        <v-col cols="12" lg="4">
          <!-- ÚLTIMAS TRANSAÇÕES -->
          <v-card elevation="0" class="data-card mb-4">
            <v-card-item>
              <v-card-title class="pb-2">
                Últimas Transações
              </v-card-title>
            </v-card-item>

            <v-card-text class="pa-0">
              <div class="transactions-list">
                <div
                  v-for="(tx, idx) in recentTransactions"
                  :key="idx"
                  class="transaction-item"
                  :class="{ 'border-b': idx < recentTransactions.length - 1 }"
                >
                  <div class="transaction-content">
                    <div class="transaction-icon">
                      <v-icon :icon="tx.icon" :color="tx.color" />
                    </div>
                    <div class="transaction-info">
                      <p class="transaction-title">{{ tx.title }}</p>
                      <p class="transaction-category">{{ tx.category }}</p>
                    </div>
                  </div>
                  <div class="transaction-amount" :class="{ positive: tx.amount > 0 }">
                    {{ formatCurrency(tx.amount) }}
                  </div>
                </div>
              </div>
              <v-divider />
              <div class="pa-3 text-center">
                <router-link to="{ name: 'despesas' }" class="text-primary text-decoration-none">
                  Ver todas as transações →
                </router-link>
              </div>
            </v-card-text>
          </v-card>

          <!-- CATEGORIAS TOP -->
          <v-card elevation="0" class="data-card">
            <v-card-item>
              <v-card-title class="pb-2">
                Categorias Top
              </v-card-title>
            </v-card-item>

            <v-card-text>
              <div class="categories-list">
                <div
                  v-for="(cat, idx) in topCategories"
                  :key="idx"
                  class="category-item mb-3"
                >
                  <div class="d-flex justify-space-between align-center mb-1">
                    <span class="category-name">{{ cat.name }}</span>
                    <span class="category-value">{{ formatCurrency(cat.value) }}</span>
                  </div>
                  <v-progress-linear
                    :value="cat.percentage"
                    :color="cat.color"
                    height="6"
                    rounded
                  />
                  <p class="text-caption text-grey mt-1">{{ cat.percentage }}% do total</p>
                </div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- AÇÕES RÁPIDAS -->
      <v-row class="mt-6">
        <v-col cols="12">
          <div class="quick-actions">
            <h3 class="mb-3">Ações Rápidas</h3>
            <div class="actions-grid">
              <v-card
                elevation="0"
                class="action-card"
                @click="$router.push({ name: 'despesas' })"
              >
                <v-card-item class="text-center py-6">
                  <v-icon icon="mdi-plus-circle" size="40" color="error" class="mb-2" />
                  <p class="font-weight-600 mb-0">Nova Despesa</p>
                </v-card-item>
              </v-card>

              <v-card
                elevation="0"
                class="action-card"
                @click="$router.push({ name: 'receitas' })"
              >
                <v-card-item class="text-center py-6">
                  <v-icon icon="mdi-plus-circle" size="40" color="success" class="mb-2" />
                  <p class="font-weight-600 mb-0">Nova Receita</p>
                </v-card-item>
              </v-card>

              <v-card
                elevation="0"
                class="action-card"
                @click="openTransferDialog"
              >
                <v-card-item class="text-center py-6">
                  <v-icon icon="mdi-swap-horizontal-circle" size="40" color="info" class="mb-2" />
                  <p class="font-weight-600 mb-0">Transferência</p>
                </v-card-item>
              </v-card>

              <v-card
                elevation="0"
                class="action-card"
                @click="$router.push({ name: 'contas' })"
              >
                <v-card-item class="text-center py-6">
                  <v-icon icon="mdi-bank-plus" size="40" color="warning" class="mb-2" />
                  <p class="font-weight-600 mb-0">Nova Conta</p>
                </v-card-item>
              </v-card>
            </div>
          </div>
        </v-col>
      </v-row>
    </div>
  </main-layout>
</template>

<script setup lang="ts">
import MainLayout from "@/layouts/MainLayout.vue";
import { computed, ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const loading = ref(false);

// Month/Year Display
const currentDate = ref(new Date());
const monthYearLabel = computed(() => {
  const month = currentDate.value.toLocaleString("pt-BR", { month: "short" });
  const year = currentDate.value.getFullYear();
  const currentYear = new Date().getFullYear();

  if (year !== currentYear) {
    return `de ${month}/${year}`;
  }
  return `de ${month}`;
});

// Mock Data: Transações Recentes
const recentTransactions = ref([
  {
    title: "Supermercado Carrefour",
    category: "Alimentação",
    amount: -125.50,
    icon: "mdi-shopping-cart",
    color: "error",
  },
  {
    title: "Salário Mensal",
    category: "Receita",
    amount: 4500.00,
    icon: "mdi-cash-multiple",
    color: "success",
  },
  {
    title: "Netflix",
    category: "Entretenimento",
    amount: -39.90,
    icon: "mdi-netflix",
    color: "error",
  },
  {
    title: "Freelance Project",
    category: "Receita Extra",
    amount: 850.00,
    icon: "mdi-briefcase",
    color: "success",
  },
  {
    title: "Academia Smart Fit",
    category: "Saúde",
    amount: -99.90,
    icon: "mdi-dumbbell",
    color: "error",
  },
]);

// Mock Data: Categorias Top
const topCategories = ref([
  {
    name: "Alimentação",
    value: -850.00,
    percentage: 45,
    color: "error",
  },
  {
    name: "Transporte",
    value: -320.00,
    percentage: 25,
    color: "warning",
  },
  {
    name: "Entretenimento",
    value: -180.00,
    percentage: 18,
    color: "info",
  },
  {
    name: "Saúde",
    value: -99.90,
    percentage: 12,
    color: "success",
  },
]);

// Utilities
const formatCurrency = (value: number) => {
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
  }).format(value);
};

const openTransferDialog = () => {
  // TODO: Implementar modal de transferência
  console.log("Abrir modal de transferência");
};
</script>

<style scoped lang="scss">
.dashboard-view {
  width: 100%;
}

/* ========================================
   KPI CARDS
   ======================================== */
.kpi-card {
  border: 1px solid rgba(0, 0, 0, 0.08);
  background: rgb(var(--v-theme-surface)) !important;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;

  &::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--color);
  }

  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  }

  &.kpi-success {
    --color: rgb(var(--v-theme-success));
  }

  &.kpi-error {
    --color: rgb(var(--v-theme-error));
  }

  &.kpi-primary {
    --color: rgb(var(--v-theme-primary));
  }

  &.kpi-info {
    --color: rgb(var(--v-theme-info));
  }

  .kpi-value {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0.5rem 0;
    color: rgb(var(--v-theme-on-surface));
  }

  .kpi-icon {
    font-size: 3rem;
    opacity: 0.15;
  }

  @media (max-width: 960px) {
    .kpi-value {
      font-size: 1.5rem;
    }

    .kpi-icon {
      font-size: 2.5rem;
    }
  }
}

/* ========================================
   DATA CARDS
   ======================================== */
.data-card {
  border: 1px solid rgba(0, 0, 0, 0.08);
  background: rgb(var(--v-theme-surface)) !important;
  transition: all 0.3s ease;
  height: 100%;

  &:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  }

  :deep(.v-card-title) {
    font-size: 1.1rem;
    font-weight: 600;
    color: rgb(var(--v-theme-on-surface));
  }
}

/* ========================================
   CHART PLACEHOLDER
   ======================================== */
.chart-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 300px;
  background: rgba(0, 0, 0, 0.02);
  border-radius: 8px;
}

/* ========================================
   TRANSACTIONS LIST
   ======================================== */
.transactions-list {
  max-height: 400px;
  overflow-y: auto;

  &::-webkit-scrollbar {
    width: 4px;
  }

  &::-webkit-scrollbar-track {
    background: transparent;
  }

  &::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 2px;

    &:hover {
      background: rgba(0, 0, 0, 0.2);
    }
  }
}

.transaction-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  transition: background-color 0.2s ease;

  &:hover {
    background: rgba(0, 0, 0, 0.02);
  }

  &.border-b {
    border-bottom: 1px solid rgba(0, 0, 0, 0.04);
  }
}

.transaction-content {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex: 1;
}

.transaction-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.04);
  flex-shrink: 0;
}

.transaction-info {
  min-width: 0;

  .transaction-title {
    font-size: 0.95rem;
    font-weight: 500;
    margin: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .transaction-category {
    font-size: 0.8rem;
    color: rgba(0, 0, 0, 0.6);
    margin: 0.25rem 0 0 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
}

.transaction-amount {
  font-weight: 600;
  font-size: 0.95rem;
  color: rgb(var(--v-theme-error));
  white-space: nowrap;
  margin-left: 1rem;

  &.positive {
    color: rgb(var(--v-theme-success));
  }
}

/* ========================================
   CATEGORIES LIST
   ======================================== */
.categories-list {
  max-height: 300px;
  overflow-y: auto;

  &::-webkit-scrollbar {
    width: 4px;
  }

  &::-webkit-scrollbar-track {
    background: transparent;
  }

  &::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 2px;
  }
}

.category-item {
  .category-name {
    font-weight: 500;
    font-size: 0.95rem;
    color: rgb(var(--v-theme-on-surface));
  }

  .category-value {
    font-weight: 600;
    font-size: 0.9rem;
    color: rgb(var(--v-theme-primary));
  }
}

/* ========================================
   QUICK ACTIONS
   ======================================== */
.quick-actions {
  h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: rgb(var(--v-theme-on-background));
    margin-bottom: 1rem;
  }
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;

  @media (max-width: 600px) {
    grid-template-columns: repeat(2, 1fr);
  }
}

.action-card {
  border: 2px solid rgba(0, 0, 0, 0.08);
  background: rgb(var(--v-theme-surface)) !important;
  cursor: pointer;
  transition: all 0.3s ease;

  &:hover {
    border-color: rgb(var(--v-theme-primary));
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  p {
    color: rgb(var(--v-theme-on-surface));
    font-size: 0.9rem;
  }
}

/* ========================================
   DARK MODE
   ======================================== */
.v-theme--dark {
  .kpi-card,
  .data-card,
  .action-card {
    border-color: rgba(255, 255, 255, 0.1);

    &:hover {
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
    }
  }

  .transaction-item:hover {
    background: rgba(255, 255, 255, 0.05);
  }

  .chart-placeholder {
    background: rgba(255, 255, 255, 0.02);
  }
}
</style>
