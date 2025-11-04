<template>
  <div class="orcamento-view">
    <!-- Header Section -->
    <div class="view-header mb-6">
      <div class="d-flex align-center justify-space-between flex-wrap gap-3">
        <div>
          <h1 class="text-h4 font-weight-bold d-flex align-center gap-2 mb-2">
            <v-icon
              icon="mdi-chart-pie"
              size="32"
              color="primary"
            />
            Orçamento Mensal
          </h1>
          <p class="text-subtitle-2 text-medium-emphasis mb-0">
            Planeje e acompanhe seus gastos por categoria
          </p>
        </div>
        <v-btn
          color="primary"
          size="large"
          prepend-icon="mdi-plus"
          class="flex-shrink-0"
          @click="openAddDialog"
        >
          Novo Orçamento
        </v-btn>
      </div>
    </div>

    <!-- Month Navigation -->
    <v-card
      class="mb-6"
      elevation="1"
    >
      <v-card-text class="pa-4">
        <div class="d-flex align-center justify-center gap-4 month-nav">
          <v-btn
            icon="mdi-chevron-left"
            color="primary"
            variant="outlined"
            size="small"
            title="Mês anterior"
            @click="goToPreviousMonth"
          />
          <div class="text-center month-display">
            <v-btn
              variant="text"
              :text="getMonthName(currentMonth).toUpperCase()"
              :class="{ 'text-primary font-weight-bold': currentMonth === new Date().toISOString().slice(0, 7) }"
              title="Ir para o mês atual"
              @click="goToCurrentMonth"
            />
          </div>
          <v-btn
            icon="mdi-chevron-right"
            color="primary"
            variant="outlined"
            size="small"
            title="Próximo mês"
            @click="goToNextMonth"
          />
        </div>
      </v-card-text>
    </v-card>

    <!-- Summary Cards -->
    <v-row class="mb-6">
      <v-col
        cols="12"
        sm="6"
        md="3"
      >
        <v-card
          class="h-100 summary-card primary-card"
          elevation="2"
        >
          <v-card-item>
            <template #prepend>
              <v-avatar
                color="primary"
                icon="mdi-wallet"
              />
            </template>
            <v-card-title class="text-caption text-medium-emphasis">
              Orçamento Total
            </v-card-title>
          </v-card-item>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-primary">
              {{ formatCurrency(summary.totalOrcamento) }}
            </div>
            <p class="text-caption text-medium-emphasis mt-2 mb-0">
              {{ orcamentos.length }} categorias definidas
            </p>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col
        cols="12"
        sm="6"
        md="3"
      >
        <v-card
          class="h-100 summary-card warning-card"
          elevation="2"
        >
          <v-card-item>
            <template #prepend>
              <v-avatar
                color="warning"
                icon="mdi-cash-remove"
              />
            </template>
            <v-card-title class="text-caption text-medium-emphasis">
              Total Gasto
            </v-card-title>
          </v-card-item>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-warning">
              {{ formatCurrency(summary.totalGasto) }}
            </div>
            <p class="text-caption text-medium-emphasis mt-2 mb-0">
              {{ formatPercentage(summary.percentualGasto) }} do orçamento
            </p>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col
        cols="12"
        sm="6"
        md="3"
      >
        <v-card
          class="h-100 summary-card"
          :class="summary.saldoRestante >= 0 ? 'success-card' : 'error-card'"
          elevation="2"
        >
          <v-card-item>
            <template #prepend>
              <v-avatar
                :color="summary.saldoRestante >= 0 ? 'success' : 'error'"
                :icon="summary.saldoRestante >= 0 ? 'mdi-trending-up' : 'mdi-trending-down'"
              />
            </template>
            <v-card-title class="text-caption text-medium-emphasis">
              Saldo Restante
            </v-card-title>
          </v-card-item>
          <v-card-text>
            <div
              class="text-h5 font-weight-bold"
              :class="summary.saldoRestante >= 0 ? 'text-success' : 'text-error'"
            >
              {{ formatCurrency(Math.abs(summary.saldoRestante)) }}
            </div>
            <p class="text-caption text-medium-emphasis mt-2 mb-0">
              {{ summary.saldoRestante >= 0 ? 'Disponível' : 'Extrapolado' }}
            </p>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col
        cols="12"
        sm="6"
        md="3"
      >
        <v-card
          class="h-100 summary-card info-card"
          elevation="2"
        >
          <v-card-item>
            <template #prepend>
              <v-avatar
                color="info"
                icon="mdi-target"
              />
            </template>
            <v-card-title class="text-caption text-medium-emphasis">
              Meta de Economia
            </v-card-title>
          </v-card-item>
          <v-card-text>
            <div class="text-h5 font-weight-bold text-info">
              {{ formatCurrency(summary.metaEconomia) }}
            </div>
            <p class="text-caption text-medium-emphasis mt-2 mb-0">
              Economia planejada
            </p>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Budget Progress Chart -->
    <v-row class="mb-6">
      <v-col cols="12">
        <v-card elevation="1">
          <v-card-item>
            <v-card-title class="text-h6 mb-4">
              <v-icon
                icon="mdi-chart-bar"
                class="mr-2"
              />
              Progresso por Categoria
            </v-card-title>
          </v-card-item>
          <v-divider />
          <v-card-item>
            <div
              v-if="chartSeries.length"
              class="chart-container"
            >
              <apexchart
                type="bar"
                :options="chartOptions"
                :series="chartSeries"
                height="400"
              />
            </div>
            <div
              v-else
              class="text-center py-8"
            >
              <p class="text-grey">
                Nenhum orçamento definido
              </p>
            </div>
          </v-card-item>
        </v-card>
      </v-col>
    </v-row>

    <!-- Budget Categories Grid -->
    <v-row>
      <v-col
        v-for="(orcamento, index) in orcamentos"
        :key="index"
        cols="12"
        sm="6"
        lg="4"
        class="mb-4"
      >
        <v-card
          elevation="2"
          class="h-100 budget-category-card"
          :class="getBudgetCardClass(orcamento)"
        >
          <v-card-item>
            <div class="d-flex justify-space-between align-start">
              <div class="flex-grow-1">
                <v-card-title class="text-h6 pb-2">
                  <v-icon
                    :icon="orcamento.icon"
                    :color="orcamento.color"
                    class="mr-2"
                  />
                  {{ orcamento.categoria }}
                </v-card-title>
                
                <!-- Budget vs Spent -->
                <div class="budget-amounts mb-3">
                  <div class="d-flex justify-space-between mb-1">
                    <span class="text-caption text-medium-emphasis">Orçado:</span>
                    <span class="text-caption font-weight-bold">{{ formatCurrency(orcamento.orcado) }}</span>
                  </div>
                  <div class="d-flex justify-space-between mb-1">
                    <span class="text-caption text-medium-emphasis">Gasto:</span>
                    <span 
                      class="text-caption font-weight-bold"
                      :class="getSpentTextColor(orcamento)"
                    >
                      {{ formatCurrency(orcamento.gasto) }}
                    </span>
                  </div>
                  <div class="d-flex justify-space-between">
                    <span class="text-caption text-medium-emphasis">Restante:</span>
                    <span 
                      class="text-caption font-weight-bold"
                      :class="getRemainingTextColor(orcamento)"
                    >
                      {{ formatCurrency(Math.abs(orcamento.restante)) }}
                    </span>
                  </div>
                </div>

                <!-- Progress Bar -->
                <div class="progress-section">
                  <div class="d-flex justify-space-between align-center mb-2">
                    <span class="text-caption">Progresso</span>
                    <span 
                      class="text-caption font-weight-bold"
                      :class="getProgressTextColor(orcamento.percentual)"
                    >
                      {{ orcamento.percentual.toFixed(1) }}%
                    </span>
                  </div>
                  <v-progress-linear
                    :model-value="Math.min(orcamento.percentual, 100)"
                    :color="getProgressColor(orcamento.percentual)"
                    height="8"
                    rounded
                  />
                </div>
              </div>
            </div>
          </v-card-item>

          <!-- Card Actions -->
          <v-card-actions class="pt-0">
            <v-btn
              size="small"
              variant="text"
              prepend-icon="mdi-pencil"
              @click="editBudget(orcamento)"
            >
              Editar
            </v-btn>
            <v-btn
              size="small"
              variant="text"
              prepend-icon="mdi-delete"
              color="error"
              @click="deleteBudget(orcamento)"
            >
              Excluir
            </v-btn>
            <v-spacer />
            <v-btn
              size="small"
              variant="text"
              prepend-icon="mdi-chart-line"
              @click="viewDetails(orcamento)"
            >
              Detalhes
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>

      <!-- Add New Budget Card -->
      <v-col
        cols="12"
        sm="6"
        lg="4"
        class="mb-4"
      >
        <v-card
          elevation="2"
          class="h-100 add-budget-card"
          @click="openAddDialog"
        >
          <v-card-item class="d-flex flex-column align-center justify-center h-100 text-center pa-8">
            <v-icon
              icon="mdi-plus-circle"
              size="64"
              color="primary"
              class="mb-4"
            />
            <v-card-title class="text-h6 text-primary">
              Adicionar Categoria
            </v-card-title>
            <p class="text-caption text-medium-emphasis mt-2">
              Defina um orçamento para uma nova categoria
            </p>
          </v-card-item>
        </v-card>
      </v-col>
    </v-row>

    <!-- Dialog: Add/Edit Budget -->
    <v-dialog
      v-model="dialog"
      max-width="600"
      persistent
    >
      <v-card>
        <v-card-title class="d-flex align-center gap-2">
          <v-icon
            :icon="editingBudget ? 'mdi-pencil' : 'mdi-plus'"
            :color="editingBudget ? 'warning' : 'primary'"
          />
          {{ editingBudget ? 'Editar Orçamento' : 'Novo Orçamento' }}
        </v-card-title>
        <v-divider />
        
        <v-form
          ref="formRef"
          v-model="formValid"
          @submit.prevent="saveBudget"
        >
          <v-card-text class="py-4">
            <v-row>
              <v-col
                cols="12"
                md="6"
              >
                <v-select
                  v-model="formData.categoria"
                  label="Categoria"
                  :items="categorias"
                  :rules="[rules.required]"
                  variant="outlined"
                  density="compact"
                  prepend-inner-icon="mdi-tag"
                />
              </v-col>
              <v-col
                cols="12"
                md="6"
              >
                <v-text-field
                  v-model="formData.valor"
                  label="Valor Orçado"
                  :rules="[rules.required, rules.positiveNumber]"
                  variant="outlined"
                  density="compact"
                  prepend-inner-icon="mdi-currency-usd"
                  placeholder="0,00"
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12">
                <v-textarea
                  v-model="formData.observacao"
                  label="Observações (opcional)"
                  variant="outlined"
                  density="compact"
                  rows="3"
                  prepend-inner-icon="mdi-note-text"
                  placeholder="Adicione uma descrição ou meta para este orçamento..."
                />
              </v-col>
            </v-row>

            <!-- Preview do orçamento -->
            <v-card
              v-if="formData.valor && parseFloat(formData.valor.replace(',', '.')) > 0"
              variant="tonal"
              color="info"
              class="mt-4"
            >
              <v-card-text>
                <div class="d-flex align-center gap-2 mb-2">
                  <v-icon
                    icon="mdi-information"
                    color="info"
                    size="20"
                  />
                  <span class="text-subtitle-2 font-weight-bold">Preview do Orçamento</span>
                </div>
                <p class="text-caption mb-1">
                  <strong>Categoria:</strong> {{ formData.categoria || 'Não selecionada' }}
                </p>
                <p class="text-caption mb-1">
                  <strong>Valor mensal:</strong> {{ formatCurrency((parseFloat(formData.valor?.replace(',', '.') || '0') * 100)) }}
                </p>
                <p class="text-caption mb-0">
                  <strong>Meta diária:</strong> {{ formatCurrency((parseFloat(formData.valor?.replace(',', '.') || '0') * 100) / 30) }}
                </p>
              </v-card-text>
            </v-card>
          </v-card-text>

          <v-divider />
          <v-card-actions class="pa-4">
            <v-spacer />
            <v-btn
              variant="text"
              @click="closeDialog"
            >
              Cancelar
            </v-btn>
            <v-btn
              :color="editingBudget ? 'warning' : 'primary'"
              :loading="loading"
              :disabled="!formValid"
              type="submit"
            >
              {{ editingBudget ? 'Atualizar' : 'Criar' }}
            </v-btn>
          </v-card-actions>
        </v-form>
      </v-card>
    </v-dialog>

    <!-- Dialog: Budget Details -->
    <v-dialog
      v-model="detailsDialog"
      max-width="800"
    >
      <v-card v-if="selectedBudget">
        <v-card-title class="d-flex align-center gap-2">
          <v-icon
            :icon="selectedBudget.icon"
            :color="selectedBudget.color"
          />
          Detalhes - {{ selectedBudget.categoria }}
        </v-card-title>
        <v-divider />
        
        <v-card-text class="py-4">
          <!-- Budget Overview -->
          <div class="budget-overview mb-6">
            <v-row>
              <v-col
                cols="12"
                sm="4"
              >
                <div class="text-center">
                  <div class="text-h4 font-weight-bold text-primary">
                    {{ formatCurrency(selectedBudget.orcado) }}
                  </div>
                  <div class="text-caption text-medium-emphasis">
                    Orçamento
                  </div>
                </div>
              </v-col>
              <v-col
                cols="12"
                sm="4"
              >
                <div class="text-center">
                  <div 
                    class="text-h4 font-weight-bold"
                    :class="getSpentTextColor(selectedBudget)"
                  >
                    {{ formatCurrency(selectedBudget.gasto) }}
                  </div>
                  <div class="text-caption text-medium-emphasis">
                    Gasto
                  </div>
                </div>
              </v-col>
              <v-col
                cols="12"
                sm="4"
              >
                <div class="text-center">
                  <div 
                    class="text-h4 font-weight-bold"
                    :class="getRemainingTextColor(selectedBudget)"
                  >
                    {{ formatCurrency(Math.abs(selectedBudget.restante)) }}
                  </div>
                  <div class="text-caption text-medium-emphasis">
                    {{ selectedBudget.restante >= 0 ? 'Restante' : 'Excedido' }}
                  </div>
                </div>
              </v-col>
            </v-row>

            <!-- Progress Bar -->
            <div class="mt-4">
              <div class="d-flex justify-space-between align-center mb-2">
                <span class="text-subtitle-2 font-weight-bold">Progresso do Mês</span>
                <span 
                  class="text-subtitle-2 font-weight-bold"
                  :class="getProgressTextColor(selectedBudget.percentual)"
                >
                  {{ selectedBudget.percentual.toFixed(1) }}%
                </span>
              </div>
              <v-progress-linear
                :model-value="Math.min(selectedBudget.percentual, 100)"
                :color="getProgressColor(selectedBudget.percentual)"
                height="12"
                rounded
              />
            </div>
          </div>

          <!-- Recent Transactions -->
          <div class="recent-transactions">
            <h3 class="text-h6 mb-4">
              <v-icon
                icon="mdi-history"
                class="mr-2"
              />
              Transações Recentes
            </h3>
            
            <div
              v-if="selectedBudget.transacoes && selectedBudget.transacoes.length"
              class="transactions-list"
            >
              <div
                v-for="(transacao, idx) in selectedBudget.transacoes"
                :key="idx"
                class="transaction-item mb-2 pa-3 rounded border"
              >
                <div class="d-flex justify-space-between align-center">
                  <div>
                    <p class="text-subtitle-2 font-weight-bold mb-0">
                      {{ transacao.descricao }}
                    </p>
                    <p class="text-caption text-grey mb-0">
                      {{ transacao.data }}
                    </p>
                  </div>
                  <div class="text-right">
                    <p class="text-subtitle-2 font-weight-bold text-error mb-0">
                      -{{ formatCurrency(transacao.valor) }}
                    </p>
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
                Nenhuma transação nesta categoria ainda
              </p>
            </div>
          </div>
        </v-card-text>
        
        <v-divider />
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="primary"
            variant="text"
            @click="detailsDialog = false"
          >
            Fechar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { useBudgetStore } from "@/store/budget";
import { useToastStore } from "@/store/toast";
import type { Budget, BudgetFormData } from "@/types/budget.types";
import { computed, onMounted, ref } from "vue";

const toastStore = useToastStore();
const budgetStore = useBudgetStore();

// State
const dialog = ref(false);
const detailsDialog = ref(false);
const formRef = ref();
const formValid = ref(false);
const loading = ref(false);
const editingBudget = ref<Budget | null>(null);
const selectedBudget = ref<Budget | null>(null);

// Current month management
const currentMonth = ref(new Date().toISOString().slice(0, 7));

// Form data
const formData = ref<BudgetFormData>({
  categoria: "",
  valor: "",
  observacao: "",
});

// Validation rules
const rules = {
  required: (v: any) => !!v || "Campo obrigatório",
  positiveNumber: (v: any) => {
    const num = parseFloat(v?.replace(",", ".") || "0");
    return num > 0 || "Valor deve ser maior que zero";
  },
};

// Categorias disponíveis (hardcoded)
const categorias = [
  "Alimentação",
  "Transporte",
  "Saúde",
  "Educação",
  "Lazer",
  "Moradia",
  "Vestuário",
  "Utilidades",
  "Investimentos",
  "Outros",
];

// Computed para orçamentos do mês atual (usando store)
const orcamentos = computed(() => {
  return budgetStore.budgetsByMonth(currentMonth.value);
});

// Computed properties (usando store)
const summary = computed(() => {
  return budgetStore.summary;
});

// Chart data
const chartSeries = computed(() => [
  {
    name: "Orçado",
    data: orcamentos.value.map(o => o.orcado / 100),
  },
  {
    name: "Gasto",
    data: orcamentos.value.map(o => o.gasto / 100),
  },
]);

const chartOptions = computed(() => ({
  chart: {
    type: "bar",
    height: 400,
    toolbar: { show: false },
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
  xaxis: {
    categories: orcamentos.value.map(o => o.categoria),
  },
  yaxis: {
    title: { text: "R$" },
    labels: {
      formatter: (value: number) => {
        if (value >= 1000) {
          return `R$ ${(value / 1000).toFixed(1)}k`;
        }
        return `R$ ${value.toFixed(0)}`;
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
        }).format(val),
    },
  },
  colors: ["#1976D2", "#FF6B35"],
  legend: {
    position: "top",
    horizontalAlign: "right",
  },
}));

// Methods
const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
  }).format(value / 100);
};

const formatPercentage = (value: number): string => {
  return `${value.toFixed(1)}%`;
};

const getMonthName = (monthStr: string): string => {
  const [year, month] = monthStr.split("-");
  const date = new Date(Number(year), Number(month) - 1, 1);
  return date.toLocaleString("pt-BR", { month: "long", year: "numeric" });
};

const goToPreviousMonth = async () => {
  const [year, month] = currentMonth.value.split("-");
  const yearNum = parseInt(year, 10);
  const monthNum = parseInt(month, 10);
  
  let newMonth = monthNum - 1;
  let newYear = yearNum;
  
  if (newMonth < 1) {
    newMonth = 12;
    newYear = newYear - 1;
  }
  
  currentMonth.value = `${newYear}-${String(newMonth).padStart(2, "0")}`;
  await loadBudgets(); // Carregar orçamentos do novo mês
};

const goToNextMonth = async () => {
  const [year, month] = currentMonth.value.split("-");
  const yearNum = parseInt(year, 10);
  const monthNum = parseInt(month, 10);
  
  let nextMonth = monthNum + 1;
  let nextYear = yearNum;
  
  if (nextMonth > 12) {
    nextMonth = 1;
    nextYear = nextYear + 1;
  }
  
  currentMonth.value = `${nextYear}-${String(nextMonth).padStart(2, "0")}`;
  await loadBudgets(); // Carregar orçamentos do novo mês
};

const goToCurrentMonth = async () => {
  currentMonth.value = new Date().toISOString().slice(0, 7);
  await loadBudgets(); // Carregar orçamentos do mês atual
};

const getBudgetCardClass = (orcamento: any): string => {
  if (orcamento.percentual > 100) return "budget-exceeded";
  if (orcamento.percentual > 80) return "budget-warning";
  return "budget-normal";
};

const getSpentTextColor = (orcamento: any): string => {
  if (orcamento.percentual > 100) return "text-error";
  if (orcamento.percentual > 80) return "text-warning";
  return "text-success";
};

const getRemainingTextColor = (orcamento: any): string => {
  return orcamento.restante >= 0 ? "text-success" : "text-error";
};

const getProgressColor = (percentual: number): string => {
  if (percentual > 100) return "error";
  if (percentual > 80) return "warning";
  return "success";
};

const getProgressTextColor = (percentual: number): string => {
  if (percentual > 100) return "text-error";
  if (percentual > 80) return "text-warning";
  return "text-success";
};

const openAddDialog = () => {
  editingBudget.value = null;
  formData.value = {
    categoria: "",
    valor: "",
    observacao: "",
  };
  dialog.value = true;
};

const editBudget = (budget: any) => {
  editingBudget.value = budget;
  formData.value = {
    categoria: budget.categoria,
    valor: (budget.orcado / 100).toFixed(2).replace(".", ","),
    observacao: budget.observacao || "",
  };
  dialog.value = true;
};

const viewDetails = (budget: any) => {
  selectedBudget.value = budget;
  detailsDialog.value = true;
};

const deleteBudget = async (budget: any) => {
  if (!confirm(`Tem certeza que deseja excluir o orçamento da categoria "${budget.categoria}"?`)) {
    return;
  }

  try {
    const success = await budgetStore.deleteBudget(budget.id);
    if (success) {
      toastStore.success("Orçamento excluído com sucesso!");
    } else {
      toastStore.error(budgetStore.error || "Erro ao excluir orçamento");
    }
  } catch (error) {
    console.error("Erro ao excluir orçamento:", error);
    toastStore.error("Erro ao excluir orçamento");
  }
};

const closeDialog = () => {
  dialog.value = false;
  editingBudget.value = null;
  formData.value = {
    categoria: "",
    valor: "",
    observacao: "",
  };
};

const saveBudget = async () => {
  if (!formRef.value.validate()) return;

  try {
    loading.value = true;
    
    const valorDecimal = parseFloat(formData.value.valor.replace(",", "."));
    
    const budgetFormData = {
      categoria: formData.value.categoria,
      valor_orcado: valorDecimal,
      mes_ano: currentMonth.value,
      observacao: formData.value.observacao || "",
    };
    
    if (editingBudget.value) {
      // Editar orçamento existente
      const success = await budgetStore.updateBudget(editingBudget.value.id, budgetFormData);
      if (success) {
        toastStore.success("Orçamento atualizado com sucesso!");
        closeDialog();
      } else {
        toastStore.error(budgetStore.error || "Erro ao atualizar orçamento");
      }
    } else {
      // Criar novo orçamento
      const novoBudget = await budgetStore.createBudget(budgetFormData);
      if (novoBudget) {
        toastStore.success("Orçamento criado com sucesso!");
        closeDialog();
      } else {
        toastStore.error(budgetStore.error || "Erro ao criar orçamento");
      }
    }
  } catch (error) {
    console.error("Erro ao salvar orçamento:", error);
    toastStore.error("Erro ao salvar orçamento");
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  // Carregar orçamentos do mês atual via API
  await loadBudgets();
});

// Função para carregar orçamentos via API
const loadBudgets = async () => {
  try {
    await budgetStore.fetchBudgets(currentMonth.value);
    console.log("Orçamentos carregados via API:", budgetStore.budgetData);
  } catch (error) {
    console.error("Erro ao carregar orçamentos:", error);
    toastStore.error("Erro ao carregar orçamentos");
  }
};
</script>

<style scoped lang="scss">
.orcamento-view {
  min-height: 100vh;
  background: rgb(var(--v-theme-background));
  width: 100%;
  min-width: 0;
  max-width: 100%;
  overflow-x: hidden;
}

/* Summary Cards */
.summary-card {
  border-radius: 12px;
  border: 1px solid rgba(var(--v-theme-primary), 0.1);
  transition: all 0.3s ease;
  overflow: hidden;

  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  }
}

.primary-card {
  border-left: 4px solid #1976d2;
}

.warning-card {
  border-left: 4px solid #ff9800;
}

.success-card {
  border-left: 4px solid #4caf50;
}

.error-card {
  border-left: 4px solid #f44336;
}

.info-card {
  border-left: 4px solid #2196f3;
}

/* Budget Category Cards */
.budget-category-card {
  border-radius: 12px;
  transition: all 0.3s ease;
  overflow: hidden;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  }

  &.budget-normal {
    border-left: 4px solid #4caf50;
  }

  &.budget-warning {
    border-left: 4px solid #ff9800;
  }

  &.budget-exceeded {
    border-left: 4px solid #f44336;
  }
}

.budget-amounts {
  background: rgba(var(--v-theme-surface), 0.5);
  border-radius: 8px;
  padding: 12px;
}

.progress-section {
  margin-top: 1rem;
}

/* Add Budget Card */
.add-budget-card {
  border-radius: 12px;
  border: 2px dashed rgba(var(--v-theme-primary), 0.3);
  transition: all 0.3s ease;
  cursor: pointer;
  min-height: 280px;

  &:hover {
    border-color: rgba(var(--v-theme-primary), 0.6);
    background: rgba(var(--v-theme-primary), 0.05);
    transform: translateY(-2px);
  }
}

/* Chart Container */
.chart-container {
  width: 100%;
  height: 100%;
}

/* Transactions List */
.transactions-list {
  max-height: 300px;
  overflow-y: auto;
}

.transaction-item {
  background: rgba(var(--v-theme-surface), 0.5);
  border: 1px solid rgba(var(--v-theme-primary), 0.1);
  transition: all 0.2s ease;

  &:hover {
    background: rgba(var(--v-theme-primary), 0.05);
  }
}

/* Month Navigation */
.month-nav {
  flex-wrap: wrap;
  padding: 0 8px;
}

.month-display {
  min-width: auto;
  flex: 1;
  min-width: 150px;
}

/* Responsive */
@media (max-width: 600px) {
  .month-nav {
    padding: 0 4px;
  }
  
  .month-display {
    min-width: 120px;
  }
  
  .budget-category-card {
    margin-bottom: 1rem;
  }
}

@media (max-width: 400px) {
  .month-nav {
    padding: 0 2px;
  }
  
  .month-display {
    min-width: 100px;
  }
}

/* Budget Overview in Details Dialog */
.budget-overview {
  background: rgba(var(--v-theme-surface), 0.3);
  border-radius: 12px;
  padding: 1.5rem;
}
</style>
