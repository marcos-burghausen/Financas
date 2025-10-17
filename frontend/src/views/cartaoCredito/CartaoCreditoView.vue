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
        class="cartoes-view pa-6"
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
                  <h1 class="cartoes-title mb-1 d-flex align-center">
                    <v-icon 
                      icon="mdi-credit-card-multiple" 
                      :size="$vuetify.display.xs ? '24' : '36'" 
                      class="mr-2 mr-md-3" 
                      color="warning" 
                    />
                    <span class="d-none d-sm-inline">Meus Cartões de Crédito</span>
                    <span class="d-sm-none">Cartões</span>
                  </h1>
                  <p class="text-caption text-sm-subtitle-1 text-grey mb-0 d-none d-sm-block">
                    Gerencie seus cartões e faturas
                  </p>
                </div>
              </div>
              <v-btn
                color="warning"
                :prepend-icon="$vuetify.display.xs ? '' : 'mdi-plus'"
                :icon="$vuetify.display.xs ? 'mdi-plus' : false"
                :size="$vuetify.display.xs ? 'default' : 'large'"
                class="flex-shrink-0"
                @click="openAddCardDialog"
              >
                <span v-if="!$vuetify.display.xs">Novo Cartão</span>
              </v-btn>
            </div>
          </v-col>
        </v-row>

        <!-- Cards de Resumo -->
        <v-row class="mb-6">
          <v-col
            cols="12"
            sm="6"
            lg="4"
          >
            <v-card
              elevation="4"
              class="stat-card"
            >
              <div class="stat-card-gradient-warning pa-3 pa-sm-4">
                <div class="d-flex align-center justify-space-between">
                  <div class="flex-grow-1">
                    <p class="text-caption text-white mb-1">
                      Fatura Total
                    </p>
                    <h2 class="stat-value text-white font-weight-bold mb-2">
                      {{ formatCurrency(totalFatura) }}
                    </h2>
                    <v-chip
                      size="x-small"
                      color="white"
                      text-color="warning"
                      class="font-weight-bold"
                    >
                      <v-icon
                        start
                        size="14"
                      >
                        mdi-calendar-month
                      </v-icon>
                      <span class="d-none d-sm-inline">Mês atual</span>
                      <span class="d-sm-none">Atual</span>
                    </v-chip>
                  </div>
                  <v-avatar
                    color="rgba(255,255,255,0.2)"
                    :size="$vuetify.display.xs ? '48' : '56'"
                  >
                    <v-icon
                      :size="$vuetify.display.xs ? '28' : '32'"
                      color="white"
                    >
                      mdi-credit-card-check
                    </v-icon>
                  </v-avatar>
                </div>
              </div>
            </v-card>
          </v-col>

          <v-col
            cols="12"
            sm="6"
            lg="4"
          >
            <v-card
              elevation="4"
              class="stat-card"
            >
              <div class="stat-card-gradient-primary pa-3 pa-sm-4">
                <div class="d-flex align-center justify-space-between">
                  <div class="flex-grow-1">
                    <p class="text-caption text-white mb-1">
                      Limite Total
                    </p>
                    <h2 class="stat-value text-white font-weight-bold mb-2">
                      {{ formatCurrency(totalLimite) }}
                    </h2>
                    <v-chip
                      size="x-small"
                      color="white"
                      text-color="primary"
                      class="font-weight-bold"
                    >
                      <v-icon
                        start
                        size="14"
                      >
                        mdi-credit-card-outline
                      </v-icon>
                      <span>{{ creditCards.length }} {{ creditCards.length === 1 ? 'cartão' : 'cartões' }}</span>
                    </v-chip>
                  </div>
                  <v-avatar
                    color="rgba(255,255,255,0.2)"
                    :size="$vuetify.display.xs ? '48' : '56'"
                  >
                    <v-icon
                      :size="$vuetify.display.xs ? '28' : '32'"
                      color="white"
                    >
                      mdi-cash-multiple
                    </v-icon>
                  </v-avatar>
                </div>
              </div>
            </v-card>
          </v-col>

          <v-col
            cols="12"
            sm="6"
            lg="4"
          >
            <v-card
              elevation="4"
              class="stat-card"
            >
              <div class="stat-card-gradient-success pa-3 pa-sm-4">
                <div class="d-flex align-center justify-space-between">
                  <div class="flex-grow-1">
                    <p class="text-caption text-white mb-1">
                      Disponível
                    </p>
                    <h2 class="stat-value text-white font-weight-bold mb-2">
                      {{ formatCurrency(limiteDisponivel) }}
                    </h2>
                    <v-chip
                      size="x-small"
                      color="white"
                      text-color="success"
                      class="font-weight-bold"
                    >
                      <v-icon
                        start
                        size="14"
                      >
                        mdi-trending-up
                      </v-icon>
                      <span>{{ percentualDisponivel }}%</span>
                    </v-chip>
                  </div>
                  <v-avatar
                    color="rgba(255,255,255,0.2)"
                    :size="$vuetify.display.xs ? '48' : '56'"
                  >
                    <v-icon
                      :size="$vuetify.display.xs ? '28' : '32'"
                      color="white"
                    >
                      mdi-wallet-outline
                    </v-icon>
                  </v-avatar>
                </div>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <!-- Lista de Cartões -->
        <v-row>
          <v-col
            v-for="(card, index) in creditCards"
            :key="index"
            cols="12"
            md="6"
            lg="4"
          >
            <v-card
              elevation="4"
              class="credit-card h-100"
              @click="selectCard(card)"
            >
              <!-- Card Header -->
              <div :class="['card-header', `card-header-${card.bandeira.toLowerCase()}`]">
                <div class="d-flex align-items-center justify-space-between">
                  <div class="d-flex align-items-center flex-grow-1">
                    <v-icon
                      icon="mdi-credit-card"
                      size="32"
                      color="white"
                      class="mr-3"
                    />
                    <div>
                      <h3 class="text-h6 text-white mb-0">
                        {{ card.name }}
                      </h3>
                      <div class="d-flex align-items-center mt-1">
                        <v-icon
                          :icon="getBandeiraIcon(card.bandeira)"
                          size="20"
                          color="white"
                          class="mr-1"
                        />
                        <span class="text-caption text-white">{{ card.bandeira }}</span>
                      </div>
                    </div>
                  </div>
                  <v-menu>
                    <template #activator="{ props }">
                      <v-btn
                        icon
                        variant="text"
                        size="small"
                        v-bind="props"
                      >
                        <v-icon
                          icon="mdi-dots-vertical"
                          color="white"
                        />
                      </v-btn>
                    </template>
                    <v-list>
                      <v-list-item @click="addTransaction(card)">
                        <template #prepend>
                          <v-icon icon="mdi-plus" />
                        </template>
                        <v-list-item-title>Nova Despesa</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="viewTransactions(card)">
                        <template #prepend>
                          <v-icon icon="mdi-format-list-bulleted" />
                        </template>
                        <v-list-item-title>Ver Lançamentos</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="editCard(card)">
                        <template #prepend>
                          <v-icon icon="mdi-pencil" />
                        </template>
                        <v-list-item-title>Editar</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </div>
              </div>

              <!-- Card Body -->
              <v-card-text class="pa-3 pa-sm-4">
                <!-- Informações de Limite -->
                <div class="info-grid mb-3">
                  <div class="info-item">
                    <span class="info-label">Limite</span>
                    <span class="info-value">{{ formatCurrency(card.limite) }}</span>
                  </div>
                  <div class="info-item text-center">
                    <span class="info-label">Em Aberto</span>
                    <span class="info-value text-error">{{ formatCurrency(card.saldo) }}</span>
                  </div>
                  <div class="info-item text-right">
                    <span class="info-label">Disponível</span>
                    <span class="info-value text-success">{{ formatCurrency(card.limite - card.saldo) }}</span>
                  </div>
                </div>

                <!-- Barra de Progresso -->
                <div class="progress-container mb-3">
                  <v-progress-linear
                    :model-value="(card.saldo / card.limite) * 100"
                    :color="getProgressColor(card.saldo, card.limite)"
                    height="12"
                    rounded
                  >
                    <template #default>
                      <span class="progress-text">{{ Math.round((card.saldo / card.limite) * 100) }}%</span>
                    </template>
                  </v-progress-linear>
                </div>

                <v-divider class="my-3" />

                <!-- Informações Adicionais -->
                <div class="info-grid mb-3">
                  <div class="info-item">
                    <span class="info-label">Conta</span>
                    <span class="info-value-small">{{ card.conta_pai_name }}</span>
                  </div>
                  <div class="info-item text-center">
                    <span class="info-label">Fechamento</span>
                    <span class="info-value-small">{{ formatDate(card.data_fechamento) }}</span>
                  </div>
                  <div class="info-item text-right">
                    <span class="info-label">Vencimento</span>
                    <span class="info-value-small">{{ formatDate(card.data_vencimento) }}</span>
                  </div>
                </div>

                <v-divider class="my-3" />

                <!-- Fatura Atual -->
                <div class="fatura-section">
                  <div class="d-flex justify-space-between align-items-center mb-2">
                    <span class="text-body-2">Fatura Atual</span>
                    <span class="text-h6 font-weight-bold">{{ formatCurrency(card.total_fatura_vigente) }}</span>
                  </div>
                  <div class="d-flex justify-space-between align-items-center">
                    <v-chip
                      :color="card.status_fatura === 'FECHADA' ? 'error' : 'primary'"
                      size="small"
                      class="font-weight-bold"
                    >
                      <v-icon
                        :icon="card.status_fatura === 'FECHADA' ? 'mdi-lock' : 'mdi-lock-open'"
                        start
                        size="14"
                      />
                      {{ card.status_fatura === 'FECHADA' ? 'Fechada' : 'Aberta' }}
                    </v-chip>
                    <v-btn
                      variant="text"
                      color="primary"
                      size="small"
                      @click.stop="registerPayment(card)"
                    >
                      <v-icon
                        icon="mdi-check-circle"
                        start
                        size="18"
                      />
                      Pagar
                    </v-btn>
                  </div>
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- Dialog de Lançamentos do Cartão -->
        <v-dialog
          v-model="transactionsDialog"
          max-width="900"
          scrollable
        >
          <v-card>
            <v-card-title class="d-flex align-center justify-space-between pa-4">
              <div class="d-flex align-center">
                <v-icon
                  :icon="getBandeiraIcon(selectedCard?.bandeira)"
                  size="28"
                  color="primary"
                  class="mr-2"
                />
                <div>
                  <h3 class="text-h6 mb-0">
                    {{ selectedCard?.name }}
                  </h3>
                  <span class="text-caption text-grey">Lançamentos da Fatura</span>
                </div>
              </div>
              <v-btn
                icon
                variant="text"
                @click="transactionsDialog = false"
              >
                <v-icon icon="mdi-close" />
              </v-btn>
            </v-card-title>

            <v-divider />

            <v-card-text
              class="pa-0"
              style="max-height: 500px;"
            >
              <!-- Desktop: Tabela -->
              <v-table class="d-none d-md-table">
                <thead>
                  <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th>Parcela</th>
                    <th class="text-right">
                      Valor
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(transaction, idx) in mockTransactions"
                    :key="idx"
                  >
                    <td>{{ transaction.date }}</td>
                    <td>{{ transaction.description }}</td>
                    <td>
                      <v-chip
                        :color="transaction.categoryColor"
                        size="small"
                        variant="tonal"
                      >
                        {{ transaction.category }}
                      </v-chip>
                    </td>
                    <td>{{ transaction.installment }}</td>
                    <td class="text-right font-weight-bold text-error">
                      {{ formatCurrency(transaction.amount) }}
                    </td>
                  </tr>
                </tbody>
              </v-table>

              <!-- Mobile: Lista -->
              <v-list class="d-md-none">
                <v-list-item
                  v-for="(transaction, idx) in mockTransactions"
                  :key="idx"
                  class="border-b pa-3"
                >
                  <div class="d-flex flex-column gap-2">
                    <div class="d-flex justify-space-between align-items-start">
                      <div>
                        <div class="font-weight-bold">
                          {{ transaction.description }}
                        </div>
                        <div class="text-caption text-grey">
                          {{ transaction.date }}
                        </div>
                      </div>
                      <div class="font-weight-bold text-error text-right">
                        {{ formatCurrency(transaction.amount) }}
                      </div>
                    </div>
                    <div class="d-flex justify-space-between align-items-center">
                      <v-chip
                        :color="transaction.categoryColor"
                        size="x-small"
                        variant="tonal"
                      >
                        {{ transaction.category }}
                      </v-chip>
                      <span class="text-caption">{{ transaction.installment }}</span>
                    </div>
                  </div>
                </v-list-item>
              </v-list>
            </v-card-text>

            <v-divider />

            <v-card-actions class="pa-4">
              <div class="w-100 d-flex justify-space-between align-items-center">
                <div>
                  <span class="text-body-2 text-grey mr-2">Total:</span>
                  <span class="text-h6 font-weight-bold text-error">{{ formatCurrency(totalTransactions) }}</span>
                </div>
                <v-btn
                  color="primary"
                  variant="elevated"
                  @click="transactionsDialog = false"
                >
                  Fechar
                </v-btn>
              </div>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-container>
    </v-main>
  </v-layout>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import { useRoute } from "vue-router";

// Sidebar
const drawer = ref(false);
const route = useRoute();

interface CreditCard {
  id: number;
  name: string;
  bandeira: string;
  limite: number;
  saldo: number;
  conta_pai_name: string;
  data_fechamento: string;
  data_vencimento: string;
  total_fatura_vigente: number;
  status_fatura: "ABERTA" | "FECHADA";
}

interface Transaction {
  date: string;
  description: string;
  category: string;
  categoryColor: string;
  installment: string;
  amount: number;
}

// Mock Data - Cartões de Crédito
const creditCards = ref<CreditCard[]>([
  {
    id: 1,
    name: "Sicredi Mastercard Gold",
    bandeira: "Mastercard",
    limite: 5000,
    saldo: 2847.50,
    conta_pai_name: "Sicredi C/C",
    data_fechamento: "2024-10-25",
    data_vencimento: "2024-11-05",
    total_fatura_vigente: 2847.50,
    status_fatura: "ABERTA"
  },
  {
    id: 2,
    name: "Nubank Visa Platinum",
    bandeira: "Visa",
    limite: 8000,
    saldo: 4523.80,
    conta_pai_name: "Nubank",
    data_fechamento: "2024-10-20",
    data_vencimento: "2024-11-01",
    total_fatura_vigente: 4523.80,
    status_fatura: "ABERTA"
  },
  {
    id: 3,
    name: "Inter Mastercard Black",
    bandeira: "Mastercard",
    limite: 12000,
    saldo: 8945.20,
    conta_pai_name: "Inter C/C",
    data_fechamento: "2024-10-15",
    data_vencimento: "2024-10-28",
    total_fatura_vigente: 8945.20,
    status_fatura: "FECHADA"
  },
  {
    id: 4,
    name: "C6 Bank Visa Infinite",
    bandeira: "Visa",
    limite: 15000,
    saldo: 3250.00,
    conta_pai_name: "C6 Bank",
    data_fechamento: "2024-10-30",
    data_vencimento: "2024-11-10",
    total_fatura_vigente: 3250.00,
    status_fatura: "ABERTA"
  }
]);

// Mock Data - Lançamentos (será filtrado por cartão selecionado)
const allTransactions: Record<number, Transaction[]> = {
  1: [
    {
      date: "15/10/2024",
      description: "Supermercado Extra",
      category: "Alimentação",
      categoryColor: "success",
      installment: "À vista",
      amount: 345.80
    },
    {
      date: "12/10/2024",
      description: "Netflix",
      category: "Assinaturas",
      categoryColor: "purple",
      installment: "À vista",
      amount: 55.90
    },
    {
      date: "10/10/2024",
      description: "Posto de Gasolina",
      category: "Transporte",
      categoryColor: "warning",
      installment: "À vista",
      amount: 280.00
    },
    {
      date: "08/10/2024",
      description: "Restaurante Jardim",
      category: "Alimentação",
      categoryColor: "success",
      installment: "À vista",
      amount: 156.50
    },
    {
      date: "05/10/2024",
      description: "Academia Fit",
      category: "Saúde",
      categoryColor: "info",
      installment: "À vista",
      amount: 189.90
    },
    {
      date: "03/10/2024",
      description: "Amazon - Livros",
      category: "Compras",
      categoryColor: "primary",
      installment: "3/12",
      amount: 120.00
    },
    {
      date: "01/10/2024",
      description: "Farmácia Panvel",
      category: "Saúde",
      categoryColor: "info",
      installment: "À vista",
      amount: 89.40
    }
  ],
  2: [
    {
      date: "16/10/2024",
      description: "iFood",
      category: "Alimentação",
      categoryColor: "success",
      installment: "À vista",
      amount: 78.90
    },
    {
      date: "14/10/2024",
      description: "Spotify Premium",
      category: "Assinaturas",
      categoryColor: "purple",
      installment: "À vista",
      amount: 21.90
    },
    {
      date: "11/10/2024",
      description: "Loja de Roupas",
      category: "Compras",
      categoryColor: "primary",
      installment: "2/3",
      amount: 450.00
    },
    {
      date: "09/10/2024",
      description: "Uber",
      category: "Transporte",
      categoryColor: "warning",
      installment: "À vista",
      amount: 45.50
    },
    {
      date: "06/10/2024",
      description: "Mercado Livre",
      category: "Compras",
      categoryColor: "primary",
      installment: "5/10",
      amount: 350.00
    }
  ],
  3: [
    {
      date: "17/10/2024",
      description: "Passagem Aérea",
      category: "Viagem",
      categoryColor: "error",
      installment: "6/6",
      amount: 1200.00
    },
    {
      date: "13/10/2024",
      description: "Hotel Resort",
      category: "Viagem",
      categoryColor: "error",
      installment: "3/4",
      amount: 2500.00
    },
    {
      date: "07/10/2024",
      description: "Eletrônicos Loja",
      category: "Compras",
      categoryColor: "primary",
      installment: "8/12",
      amount: 450.00
    }
  ],
  4: [
    {
      date: "16/10/2024",
      description: "Restaurante Fino",
      category: "Alimentação",
      categoryColor: "success",
      installment: "À vista",
      amount: 450.00
    },
    {
      date: "14/10/2024",
      description: "Curso Online",
      category: "Educação",
      categoryColor: "info",
      installment: "1/6",
      amount: 300.00
    },
    {
      date: "10/10/2024",
      description: "Shopping Center",
      category: "Compras",
      categoryColor: "primary",
      installment: "À vista",
      amount: 680.00
    }
  ]
};

// Sidebar Items
const filteredItensSideBar = ref([
  { icon: "mdi-view-dashboard", name: "Dashboard", route: "dashboard" },
  { icon: "mdi-bank", name: "Contas", route: "contas" },
  { icon: "mdi-cash-plus", name: "Receitas", route: "receitas" },
  { icon: "mdi-cash-minus", name: "Despesas", route: "despesas" },
  { icon: "mdi-credit-card", name: "Cartões", route: "cartoes" },
  { icon: "mdi-shape", name: "Categorias", route: "categorias" },
]);

// Dialog state
const transactionsDialog = ref(false);
const selectedCard = ref<CreditCard | null>(null);

// Computed
const totalFatura = computed(() => {
  return creditCards.value.reduce((sum, card) => sum + card.total_fatura_vigente, 0);
});

const totalLimite = computed(() => {
  return creditCards.value.reduce((sum, card) => sum + card.limite, 0);
});

const limiteDisponivel = computed(() => {
  return creditCards.value.reduce((sum, card) => sum + (card.limite - card.saldo), 0);
});

const percentualDisponivel = computed(() => {
  if (totalLimite.value === 0) return 0;
  return Math.round((limiteDisponivel.value / totalLimite.value) * 100);
});

const mockTransactions = computed(() => {
  if (!selectedCard.value) return [];
  return allTransactions[selectedCard.value.id] || [];
});

const totalTransactions = computed(() => {
  return mockTransactions.value.reduce((sum, t) => sum + t.amount, 0);
});

// Methods
const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
  }).format(value);
};

const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleDateString("pt-BR", { day: "2-digit", month: "short" }).replace(".", "").toUpperCase();
};

const getBandeiraIcon = (bandeira: string): string => {
  const icons: Record<string, string> = {
    Mastercard: "mdi-credit-card",
    Visa: "mdi-credit-card-outline",
    Elo: "mdi-credit-card-check",
  };
  return icons[bandeira] || "mdi-credit-card";
};

const getProgressColor = (saldo: number, limite: number): string => {
  const percentual = (saldo / limite) * 100;
  if (percentual >= 80) return "error";
  if (percentual >= 60) return "warning";
  return "success";
};

const isActiveRoute = (routeName: string): boolean => {
  return route.name === routeName;
};

const openAddCardDialog = () => {
  console.log("Abrir formulário de novo cartão");
};

const selectCard = (card: CreditCard) => {
  selectedCard.value = card;
  transactionsDialog.value = true;
};

const addTransaction = (card: CreditCard) => {
  console.log("Adicionar transação para:", card.name);
};

const viewTransactions = (card: CreditCard) => {
  selectedCard.value = card;
  transactionsDialog.value = true;
};

const editCard = (card: CreditCard) => {
  console.log("Editar cartão:", card.name);
};

const registerPayment = (card: CreditCard) => {
  console.log("Registrar pagamento para:", card.name);
};
</script>

<style scoped>
.cartoes-view {
  min-height: 100vh;
  padding-bottom: 24px;
}

/* Garantir que o container possa crescer */
:deep(.v-main) {
  overflow-y: auto;
  height: 100vh;
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

.cartoes-title {
  font-size: 1.5rem;
}

@media (min-width: 600px) {
  .cartoes-title {
    font-size: 2rem;
  }
}

@media (min-width: 960px) {
  .cartoes-title {
    font-size: 2.125rem;
  }
}

/* Gap utility */
.gap-3 {
  gap: 12px;
}

/* Stat Cards */
.stat-value {
  font-size: 1.5rem;
  line-height: 1.2;
  word-break: break-word;
}

@media (min-width: 600px) {
  .stat-value {
    font-size: 2.125rem;
  }
}

.stat-card {
  transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

/* Gradientes para Cards de Estatísticas */
.stat-card-gradient-warning {
  background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
  border-radius: 8px;
}

.stat-card-gradient-primary {
  background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
  border-radius: 8px;
}

.stat-card-gradient-success {
  background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);
  border-radius: 8px;
}

/* Credit Card Styles */
.credit-card {
  transition: transform 0.3s, box-shadow 0.3s;
  cursor: pointer;
}

.credit-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.card-header {
  padding: 16px;
  color: white;
}

@media (max-width: 599px) {
  .card-header {
    padding: 12px;
  }
}

.card-header-mastercard {
  background: linear-gradient(135deg, #eb001b 0%, #f79e1b 100%);
}

.card-header-visa {
  background: linear-gradient(135deg, #1a1f71 0%, #0066b2 100%);
}

.card-header-elo {
  background: linear-gradient(135deg, #ffcb05 0%, #000000 100%);
}

/* Info Grid */
.info-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.info-item {
  display: flex;
  flex-direction: column;
}

.info-label {
  font-size: 0.75rem;
  color: rgb(var(--v-theme-on-surface-variant));
  margin-bottom: 4px;
}

.info-value {
  font-size: 0.875rem;
  font-weight: 600;
  color: rgb(var(--v-theme-on-surface));
}

.info-value-small {
  font-size: 0.75rem;
  font-weight: 500;
  color: rgb(var(--v-theme-on-surface));
}

/* Progress Container */
.progress-container {
  position: relative;
}

.progress-text {
  font-size: 0.625rem;
  font-weight: 700;
  color: white;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

/* Fatura Section */
.fatura-section {
  background: rgb(var(--v-theme-surface-variant));
  padding: 12px;
  border-radius: 8px;
}

/* Table styling */
:deep(.v-table) {
  background-color: transparent;
}

:deep(.v-table thead th) {
  font-weight: 600;
  background-color: rgb(var(--v-theme-surface-variant));
  color: rgb(var(--v-theme-on-surface));
}

:deep(.v-table tbody tr:hover) {
  background-color: rgb(var(--v-theme-surface-variant));
}

/* Border para lista mobile */
.border-b {
  border-bottom: 1px solid rgb(var(--v-theme-surface-variant));
}

.border-b:last-child {
  border-bottom: none;
}

/* Gap utility para mobile */
.gap-2 {
  gap: 8px;
}

/* Container padding responsivo */
@media (max-width: 599px) {
  .cartoes-view {
    padding: 12px !important;
  }
  
  .info-grid {
    gap: 8px;
  }
  
  .info-label {
    font-size: 0.7rem;
  }
  
  .info-value {
    font-size: 0.8rem;
  }
}

@media (min-width: 600px) and (max-width: 959px) {
  .cartoes-view {
    padding: 16px !important;
  }
}

@media (min-width: 960px) {
  .cartoes-view {
    padding: 24px !important;
  }
}

/* Dialog responsivo */
@media (max-width: 599px) {
  :deep(.v-dialog) {
    margin: 16px;
  }
}

/* Utilities */
.text-error {
  color: rgb(var(--v-theme-error)) !important;
}

.text-success {
  color: rgb(var(--v-theme-success)) !important;
}

.text-right {
  text-align: right;
}

.text-center {
  text-align: center;
}

.w-100 {
  width: 100%;
}
</style>
