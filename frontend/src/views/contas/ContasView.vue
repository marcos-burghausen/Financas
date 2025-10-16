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
      <v-container fluid class="contas-view pa-6">
        <!-- Header -->
        <v-row class="mb-4">
          <v-col cols="12">
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
                    <v-icon icon="mdi-bank" size="36" class="mr-3" color="primary" />
                    Minhas Contas Bancárias
                  </h1>
                  <p class="text-subtitle-1 text-grey mb-0">
                    Gerencie suas contas correntes, poupanças e investimentos
                  </p>
                </div>
              </div>
              <v-btn
                color="primary"
                prepend-icon="mdi-plus"
                size="large"
                @click="openAddDialog"
              >
                Nova Conta
              </v-btn>
            </div>
          </v-col>
        </v-row>

        <!-- Cards de Resumo -->
        <v-row class="mb-6">
          <v-col cols="12" sm="6" md="4">
            <v-card elevation="4" class="stat-card">
              <div class="stat-card-gradient-primary pa-4">
                <div class="d-flex align-center justify-space-between">
                  <div>
                    <p class="text-caption text-white mb-1">Saldo Total</p>
                    <h2 class="text-h4 text-white font-weight-bold mb-2">{{ formatCurrency(totalBalance) }}</h2>
                    <v-chip size="x-small" color="white" text-color="primary" class="font-weight-bold">
                      <v-icon start size="14">mdi-trending-up</v-icon>
                      Todas as contas
                    </v-chip>
                  </div>
                  <v-avatar color="rgba(255,255,255,0.2)" size="56">
                    <v-icon size="32" color="white">mdi-cash-multiple</v-icon>
                  </v-avatar>
                </div>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="4">
            <v-card elevation="4" class="stat-card">
              <div class="stat-card-gradient-success pa-4">
                <div class="d-flex align-center justify-space-between">
                  <div>
                    <p class="text-caption text-white mb-1">Contas Ativas</p>
                    <h2 class="text-h4 text-white font-weight-bold mb-2">{{ activeAccounts }}</h2>
                    <v-chip size="x-small" color="white" text-color="success" class="font-weight-bold">
                      <v-icon start size="14">mdi-check-circle</v-icon>
                      Em uso
                    </v-chip>
                  </div>
                  <v-avatar color="rgba(255,255,255,0.2)" size="56">
                    <v-icon size="32" color="white">mdi-bank-check</v-icon>
                  </v-avatar>
                </div>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="4">
            <v-card elevation="4" class="stat-card">
              <div class="stat-card-gradient-info pa-4">
                <div class="d-flex align-center justify-space-between">
                  <div>
                    <p class="text-caption text-white mb-1">Tipos de Conta</p>
                    <h2 class="text-h4 text-white font-weight-bold mb-2">{{ accountTypes }}</h2>
                    <v-chip size="x-small" color="white" text-color="info" class="font-weight-bold">
                      <v-icon start size="14">mdi-folder-multiple</v-icon>
                      Diversificadas
                    </v-chip>
                  </div>
                  <v-avatar color="rgba(255,255,255,0.2)" size="56">
                    <v-icon size="32" color="white">mdi-chart-pie</v-icon>
                  </v-avatar>
                </div>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <!-- Grid de Contas -->
        <v-row>
          <v-col
            v-for="account in accounts"
            :key="account.id"
            cols="12"
            md="6"
            lg="4"
          >
            <v-card elevation="3" class="account-card">
              <div :class="`account-header account-header-${account.type}`">
                <div class="d-flex align-center justify-space-between mb-2">
                  <div class="d-flex align-center">
                    <v-avatar :color="getAccountColor(account.type)" size="48" class="mr-3">
                      <v-icon :icon="getAccountIcon(account.type)" size="28" color="white" />
                    </v-avatar>
                    <div>
                      <h3 class="text-h6 text-white mb-0">{{ account.name }}</h3>
                      <p class="text-caption text-white mb-0" style="opacity: 0.9;">
                        {{ account.bank }}
                      </p>
                    </div>
                  </div>
                  <v-menu>
                    <template #activator="{ props }">
                      <v-btn
                        icon="mdi-dots-vertical"
                        variant="text"
                        size="small"
                        color="white"
                        v-bind="props"
                      />
                    </template>
                    <v-list>
                      <v-list-item @click="editAccount(account)">
                        <template #prepend>
                          <v-icon icon="mdi-pencil" color="primary" />
                        </template>
                        <v-list-item-title>Editar</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="deleteAccount(account)">
                        <template #prepend>
                          <v-icon icon="mdi-delete" color="error" />
                        </template>
                        <v-list-item-title>Excluir</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </div>
              </div>

              <v-card-text class="pt-4">
                <div class="mb-3">
                  <p class="text-caption text-grey mb-1">Saldo Atual</p>
                  <h2 :class="`text-h5 font-weight-bold ${account.balance >= 0 ? 'text-success' : 'text-error'}`">
                    {{ formatCurrency(account.balance) }}
                  </h2>
                </div>

                <v-divider class="my-3" />

                <div class="d-flex justify-space-between mb-2">
                  <span class="text-caption text-grey">Tipo:</span>
                  <v-chip
                    :color="getAccountColor(account.type)"
                    size="small"
                    variant="flat"
                  >
                    {{ getAccountTypeLabel(account.type) }}
                  </v-chip>
                </div>

                <div class="d-flex justify-space-between mb-2">
                  <span class="text-caption text-grey">Status:</span>
                  <v-chip
                    :color="account.active ? 'success' : 'error'"
                    size="small"
                    variant="outlined"
                  >
                    <v-icon
                      :icon="account.active ? 'mdi-check-circle' : 'mdi-close-circle'"
                      start
                      size="14"
                    />
                    {{ account.active ? 'Ativa' : 'Inativa' }}
                  </v-chip>
                </div>

                <div class="d-flex justify-space-between">
                  <span class="text-caption text-grey">Agência:</span>
                  <span class="text-body-2 font-weight-medium">{{ account.agency }}</span>
                </div>

                <div class="d-flex justify-space-between mt-2">
                  <span class="text-caption text-grey">Conta:</span>
                  <span class="text-body-2 font-weight-medium">{{ account.number }}</span>
                </div>
              </v-card-text>

              <v-card-actions>
                <v-btn
                  variant="text"
                  color="primary"
                  prepend-icon="mdi-eye"
                  @click="viewTransactions(account)"
                >
                  Ver Lançamentos
                </v-btn>
                <v-spacer />
                <v-btn
                  icon="mdi-arrow-right"
                  variant="text"
                  color="primary"
                  @click="viewDetails(account)"
                />
              </v-card-actions>
            </v-card>
          </v-col>

          <!-- Empty State -->
          <v-col v-if="accounts.length === 0" cols="12">
            <v-card elevation="2" class="text-center pa-12">
              <v-icon icon="mdi-bank-off" size="120" color="grey" />
              <h2 class="text-h5 mt-4 mb-2">Nenhuma conta cadastrada</h2>
              <p class="text-grey mb-4">
                Adicione sua primeira conta bancária para começar a gerenciar suas finanças
              </p>
              <v-btn
                color="primary"
                prepend-icon="mdi-plus"
                size="large"
                @click="openAddDialog"
              >
                Adicionar Primeira Conta
              </v-btn>
            </v-card>
          </v-col>
        </v-row>

        <!-- Dialog de Adicionar/Editar Conta -->
        <v-dialog v-model="dialog" max-width="600" persistent>
          <v-card>
            <v-card-title class="bg-primary text-white pa-4">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center">
                  <v-icon icon="mdi-bank" class="mr-2" />
                  {{ editMode ? 'Editar Conta' : 'Nova Conta Bancária' }}
                </div>
                <v-btn
                  icon="mdi-close"
                  variant="text"
                  color="white"
                  @click="closeDialog"
                />
              </div>
            </v-card-title>

            <v-card-text class="pa-6">
              <v-form ref="form" v-model="formValid">
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      v-model="formData.name"
                      label="Nome da Conta *"
                      placeholder="Ex: Conta Corrente Principal"
                      prepend-inner-icon="mdi-card-account-details"
                      variant="outlined"
                      :rules="[rules.required]"
                      density="comfortable"
                    />
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-select
                      v-model="formData.type"
                      :items="accountTypeOptions"
                      label="Tipo de Conta *"
                      prepend-inner-icon="mdi-shape"
                      variant="outlined"
                      :rules="[rules.required]"
                      density="comfortable"
                    />
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.bank"
                      label="Banco *"
                      placeholder="Ex: Banco do Brasil"
                      prepend-inner-icon="mdi-bank"
                      variant="outlined"
                      :rules="[rules.required]"
                      density="comfortable"
                    />
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.agency"
                      label="Agência *"
                      placeholder="Ex: 1234-5"
                      prepend-inner-icon="mdi-office-building"
                      variant="outlined"
                      :rules="[rules.required]"
                      density="comfortable"
                    />
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.number"
                      label="Número da Conta *"
                      placeholder="Ex: 12345-6"
                      prepend-inner-icon="mdi-numeric"
                      variant="outlined"
                      :rules="[rules.required]"
                      density="comfortable"
                    />
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.balance"
                      label="Saldo Inicial *"
                      placeholder="0,00"
                      prepend-inner-icon="mdi-currency-usd"
                      variant="outlined"
                      type="number"
                      step="0.01"
                      :rules="[rules.required]"
                      density="comfortable"
                    />
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-switch
                      v-model="formData.active"
                      label="Conta Ativa"
                      color="success"
                      hide-details
                      inset
                    />
                  </v-col>

                  <v-col cols="12">
                    <v-textarea
                      v-model="formData.description"
                      label="Descrição (Opcional)"
                      placeholder="Informações adicionais sobre a conta..."
                      prepend-inner-icon="mdi-text"
                      variant="outlined"
                      rows="3"
                      density="comfortable"
                    />
                  </v-col>
                </v-row>
              </v-form>
            </v-card-text>

            <v-card-actions class="pa-4">
              <v-spacer />
              <v-btn
                variant="text"
                @click="closeDialog"
              >
                Cancelar
              </v-btn>
              <v-btn
                color="primary"
                :disabled="!formValid"
                @click="saveAccount"
              >
                {{ editMode ? 'Salvar Alterações' : 'Adicionar Conta' }}
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <!-- Snackbar -->
        <v-snackbar
          v-model="snackbar"
          :color="snackbarColor"
          :timeout="3000"
          top
        >
          {{ snackbarText }}
          <template #actions>
            <v-btn variant="text" @click="snackbar = false">
              Fechar
            </v-btn>
          </template>
        </v-snackbar>
      </v-container>
    </v-main>
  </v-layout>
</template>

<script setup lang="ts">
import { useRolesStore } from '@/store/roles';
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

// Router
const router = useRouter();
const route = useRoute();

// Stores
const rolesStore = useRolesStore();

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
    if (item.traderOnly && !rolesStore.hasAnyRole(['TRADER', 'USER_TRADER'])) return false;
    return true;
  });
});

const isActiveRoute = (routeName: string): boolean => {
  return route.name === routeName;
};

// State
const dialog = ref(false);
const editMode = ref(false);
const formValid = ref(false);
const snackbar = ref(false);
const snackbarText = ref('');
const snackbarColor = ref('success');

// Form Data
const formData = ref({
  id: null as number | null,
  name: '',
  type: 'corrente',
  bank: '',
  agency: '',
  number: '',
  balance: 0,
  active: true,
  description: '',
});

// Mock Data - Contas Bancárias
const accounts = ref([
  {
    id: 1,
    name: 'Conta Corrente Principal',
    bank: 'Banco do Brasil',
    type: 'corrente',
    agency: '1234-5',
    number: '12345-6',
    balance: 5800.00,
    active: true,
    description: 'Conta principal para recebimentos'
  },
  {
    id: 2,
    name: 'Poupança Reserva',
    bank: 'Caixa Econômica',
    type: 'poupanca',
    agency: '5678-9',
    number: '98765-4',
    balance: 12000.00,
    active: true,
    description: 'Reserva de emergência'
  },
  {
    id: 3,
    name: 'Conta Digital',
    bank: 'Nubank',
    type: 'digital',
    agency: '0001',
    number: '12345678-9',
    balance: 2350.00,
    active: true,
    description: 'Conta para despesas do dia a dia'
  },
  {
    id: 4,
    name: 'Investimentos',
    bank: 'Banco Inter',
    type: 'investimento',
    agency: '0001',
    number: '87654321-0',
    balance: 45000.00,
    active: true,
    description: 'Conta para investimentos'
  },
]);

// Computed
const totalBalance = computed(() => {
  return accounts.value.reduce((sum, acc) => sum + acc.balance, 0);
});

const activeAccounts = computed(() => {
  return accounts.value.filter(acc => acc.active).length;
});

const accountTypes = computed(() => {
  const types = new Set(accounts.value.map(acc => acc.type));
  return types.size;
});

// Options
const accountTypeOptions = [
  { title: 'Conta Corrente', value: 'corrente' },
  { title: 'Conta Poupança', value: 'poupanca' },
  { title: 'Conta Digital', value: 'digital' },
  { title: 'Conta Investimento', value: 'investimento' },
  { title: 'Conta Salário', value: 'salario' },
];

// Validation Rules
const rules = {
  required: (v: any) => !!v || 'Campo obrigatório',
};

// Methods
const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(value);
};

const getAccountColor = (type: string): string => {
  const colors: Record<string, string> = {
    corrente: 'primary',
    poupanca: 'success',
    digital: 'purple',
    investimento: 'info',
    salario: 'warning',
  };
  return colors[type] || 'grey';
};

const getAccountIcon = (type: string): string => {
  const icons: Record<string, string> = {
    corrente: 'mdi-bank',
    poupanca: 'mdi-piggy-bank',
    digital: 'mdi-cellphone',
    investimento: 'mdi-chart-line',
    salario: 'mdi-cash',
  };
  return icons[type] || 'mdi-bank';
};

const getAccountTypeLabel = (type: string): string => {
  const labels: Record<string, string> = {
    corrente: 'Corrente',
    poupanca: 'Poupança',
    digital: 'Digital',
    investimento: 'Investimento',
    salario: 'Salário',
  };
  return labels[type] || type;
};

const openAddDialog = () => {
  editMode.value = false;
  formData.value = {
    id: null,
    name: '',
    type: 'corrente',
    bank: '',
    agency: '',
    number: '',
    balance: 0,
    active: true,
    description: '',
  };
  dialog.value = true;
};

const editAccount = (account: any) => {
  editMode.value = true;
  formData.value = { ...account };
  dialog.value = true;
};

const closeDialog = () => {
  dialog.value = false;
  formData.value = {
    id: null,
    name: '',
    type: 'corrente',
    bank: '',
    agency: '',
    number: '',
    balance: 0,
    active: true,
    description: '',
  };
};

const saveAccount = () => {
  if (editMode.value) {
    const index = accounts.value.findIndex(acc => acc.id === formData.value.id);
    if (index !== -1) {
      accounts.value[index] = { ...formData.value, id: formData.value.id! };
      showSnackbar('Conta atualizada com sucesso!', 'success');
    }
  } else {
    const newAccount = {
      ...formData.value,
      id: Math.max(...accounts.value.map(a => a.id)) + 1,
    };
    accounts.value.push(newAccount);
    showSnackbar('Conta adicionada com sucesso!', 'success');
  }
  closeDialog();
};

const deleteAccount = (account: any) => {
  if (confirm(`Tem certeza que deseja excluir a conta "${account.name}"?`)) {
    const index = accounts.value.findIndex(acc => acc.id === account.id);
    if (index !== -1) {
      accounts.value.splice(index, 1);
      showSnackbar('Conta excluída com sucesso!', 'success');
    }
  }
};

const viewTransactions = (account: any) => {
  console.log('Ver lançamentos da conta:', account.name);
  // TODO: Implementar visualização de lançamentos
  showSnackbar(`Visualizando lançamentos de ${account.name}`, 'info');
};

const viewDetails = (account: any) => {
  console.log('Ver detalhes da conta:', account.name);
  // TODO: Implementar visualização detalhada
  showSnackbar(`Detalhes de ${account.name}`, 'info');
};

const showSnackbar = (text: string, color: string) => {
  snackbarText.value = text;
  snackbarColor.value = color;
  snackbar.value = true;
};

onMounted(() => {
  console.log('ContasView montada');
});
</script>

<style scoped>
.contas-view {
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

/* Account Card Styles */
.account-card {
  transition: transform 0.3s, box-shadow 0.3s;
  overflow: hidden;
}

.account-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.account-header {
  padding: 16px;
  color: white;
}

.account-header-corrente {
  background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
}

.account-header-poupanca {
  background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);
}

.account-header-digital {
  background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%);
}

.account-header-investimento {
  background: linear-gradient(135deg, #00BCD4 0%, #0097A7 100%);
}

.account-header-salario {
  background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
}
</style>
