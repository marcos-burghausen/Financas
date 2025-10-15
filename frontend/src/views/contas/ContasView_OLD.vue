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
                    Minhas Contas
                  </h1>
                  <p class="text-subtitle-1 text-grey mb-0">
                    Gerencie suas contas bancárias
                  </p>
                </div>
              </div>
              <v-btn
                color="primary"
                prepend-icon="mdi-plus"
                @click="openAddDialog"
              >
                Nova Conta
              </v-btn>
            </div>
          </v-col>
        </v-row>

        <!-- Summary Card -->
        <v-row class="mb-4">
          <v-col cols="12">
            <v-card elevation="4">
              <div class="card-gradient card-gradient-primary pa-4">
                <v-row>
                  <v-col cols="12" sm="4" class="text-center">
                    <div class="summary-item">
                      <v-icon icon="mdi-cash-multiple" size="32" color="white" class="mb-2" />
                      <p class="text-body-2 text-white opacity-90 mb-1">Saldo Total</p>
                      <h2 class="text-h5 text-white font-weight-bold">
                        {{ formatCurrency(totalSaldo) }}
                      </h2>
                    </div>
                  </v-col>
                  <v-col cols="12" sm="4" class="text-center">
                    <div class="summary-item">
                      <v-icon icon="mdi-chart-timeline-variant" size="32" color="white" class="mb-2" />
                      <p class="text-body-2 text-white opacity-90 mb-1">Saldo Previsto</p>
                      <h2 class="text-h5 text-white font-weight-bold">
                        {{ formatCurrency(totalPrevisto) }}
                      </h2>
                    </div>
                  </v-col>
                  <v-col cols="12" sm="4" class="text-center">
                    <div class="summary-item">
                      <v-icon icon="mdi-bank-outline" size="32" color="white" class="mb-2" />
                      <p class="text-body-2 text-white opacity-90 mb-1">Total de Contas</p>
                      <h2 class="text-h5 text-white font-weight-bold">
                        {{ contas.length }}
                      </h2>
                    </div>
                  </v-col>
                </v-row>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <!-- Loading -->
        <v-row v-if="loading">
          <v-col cols="12" class="text-center py-12">
            <v-progress-circular indeterminate color="primary" size="64" />
            <p class="text-grey mt-4">Carregando contas...</p>
          </v-col>
        </v-row>

        <!-- Empty State -->
        <v-row v-else-if="contas.length === 0">
          <v-col cols="12">
            <v-card elevation="2" class="text-center pa-12">
              <v-icon icon="mdi-bank-off" size="80" color="grey-lighten-1" />
              <h3 class="text-h5 mt-4 mb-2">Nenhuma conta cadastrada</h3>
              <p class="text-grey mb-4">
                Adicione sua primeira conta bancária para começar a gerenciar suas finanças
              </p>
              <v-btn
                color="primary"
                size="large"
                prepend-icon="mdi-plus"
                @click="openAddDialog"
              >
                Adicionar Primeira Conta
              </v-btn>
            </v-card>
          </v-col>
        </v-row>

        <!-- Accounts Grid -->
        <v-row v-else>
          <v-col
            v-for="conta in contas"
            :key="conta.id"
            cols="12"
            md="6"
            lg="4"
          >
            <v-card elevation="4" class="account-card h-100">
              <!-- Header -->
              <div :class="['card-gradient', getAccountGradient(conta.tipo_conta), 'pa-4']">
                <div class="d-flex justify-space-between align-center mb-3">
                  <div class="d-flex align-center">
                    <v-avatar :color="getAccountColor(conta.tipo_conta)" size="48" class="mr-3">
                      <v-icon :icon="getBankIcon(conta.name)" color="white" size="28" />
                    </v-avatar>
                    <div>
                      <h3 class="text-h6 text-white font-weight-bold mb-0">
                        {{ conta.name }}
                      </h3>
                      <p class="text-caption text-white opacity-90 mb-0">
                        {{ conta.tipo_conta }}
                      </p>
                    </div>
                  </div>
                  <v-menu>
                    <template #activator="{ props }">
                      <v-btn
                        icon
                        variant="text"
                        v-bind="props"
                        size="small"
                      >
                        <v-icon icon="mdi-dots-vertical" color="white" />
                      </v-btn>
                    </template>
                    <v-list>
                      <v-list-item @click="editAccount(conta)">
                        <template #prepend>
                          <v-icon icon="mdi-pencil" />
                        </template>
                        <v-list-item-title>Editar</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="deleteAccount(conta)">
                        <template #prepend>
                          <v-icon icon="mdi-delete" color="error" />
                        </template>
                        <v-list-item-title class="text-error">Excluir</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </div>
                
                <v-chip
                  :color="conta.ativo ? 'success' : 'error'"
                  size="small"
                  variant="flat"
                  class="font-weight-medium"
                >
                  <v-icon :icon="conta.ativo ? 'mdi-check-circle' : 'mdi-close-circle'" start size="16" />
                  {{ conta.ativo ? 'Ativa' : 'Inativa' }}
                </v-chip>
              </div>

              <!-- Body -->
              <v-card-text class="pa-4">
                <div class="mb-4">
                  <div class="d-flex justify-space-between align-center mb-2">
                    <span class="text-body-2 text-grey">Saldo Atual</span>
                    <v-icon icon="mdi-cash" size="18" color="grey" />
                  </div>
                  <h2 :class="['text-h5 font-weight-bold', getSaldoColor(conta.saldo)]">
                    {{ formatCurrency(conta.saldo || 0) }}
                  </h2>
                </div>

                <v-divider class="my-3" />

                <div class="mb-3">
                  <div class="d-flex justify-space-between align-center mb-2">
                    <span class="text-body-2 text-grey">Saldo Previsto</span>
                    <v-icon icon="mdi-chart-timeline-variant" size="18" color="grey" />
                  </div>
                  <h3 class="text-h6 font-weight-bold">
                    {{ formatCurrency(conta.saldo_previsto || 0) }}
                  </h3>
                </div>

                <v-divider class="my-3" />

                <div>
                  <div class="d-flex justify-space-between align-center mb-2">
                    <span class="text-body-2 text-grey">Diferença</span>
                    <v-icon icon="mdi-delta" size="18" color="grey" />
                  </div>
                  <h3 :class="['text-subtitle-1 font-weight-bold', getDiferencaColor(conta)]">
                    {{ formatCurrency((conta.saldo_previsto || 0) - (conta.saldo || 0)) }}
                  </h3>
                </div>
              </v-card-text>

              <!-- Footer -->
              <v-card-actions class="pa-4 pt-0">
                <v-btn
                  block
                  variant="outlined"
                  color="primary"
                  prepend-icon="mdi-eye"
                  @click="viewDetails(conta)"
                >
                  Ver Detalhes
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>

        <!-- Add/Edit Dialog -->
        <v-dialog v-model="dialog" max-width="600">
          <v-card>
            <div class="card-gradient card-gradient-primary pa-4">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center">
                  <v-icon icon="mdi-bank-plus" size="32" color="white" class="mr-3" />
                  <h2 class="text-h5 text-white font-weight-bold">
                    {{ editMode ? 'Editar Conta' : 'Nova Conta' }}
                  </h2>
                </div>
                <v-btn
                  icon
                  variant="text"
                  @click="dialog = false"
                >
                  <v-icon icon="mdi-close" color="white" />
                </v-btn>
              </div>
            </div>

            <v-card-text class="pa-6">
              <v-form ref="form" @submit.prevent="saveConta">
                <v-text-field
                  v-model="formData.name"
                  label="Nome da Conta *"
                  prepend-inner-icon="mdi-bank"
                  variant="outlined"
                  :rules="[rules.required]"
                  class="mb-3"
                />

                <v-select
                  v-model="formData.tipo_conta"
                  label="Tipo de Conta *"
                  prepend-inner-icon="mdi-tag"
                  variant="outlined"
                  :items="tiposConta"
                  :rules="[rules.required]"
                  class="mb-3"
                />

                <v-text-field
                  v-model="formData.saldo"
                  label="Saldo Atual *"
                  prepend-inner-icon="mdi-cash"
                  variant="outlined"
                  type="number"
                  step="0.01"
                  :rules="[rules.required]"
                  class="mb-3"
                />

                <v-switch
                  v-model="formData.ativo"
                  label="Conta Ativa"
                  color="success"
                  class="mb-3"
                />
              </v-form>
            </v-card-text>

            <v-card-actions class="pa-6 pt-0">
              <v-spacer />
              <v-btn
                variant="text"
                @click="dialog = false"
              >
                Cancelar
              </v-btn>
              <v-btn
                color="primary"
                @click="saveConta"
                :loading="saving"
              >
                {{ editMode ? 'Salvar' : 'Adicionar' }}
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <!-- Snackbar -->
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
import axiosInstance from '@/services/http'
import { useRolesStore } from '@/store/roles'
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

// Router
const router = useRouter()
const route = useRoute()

// Stores
const rolesStore = useRolesStore()

// Drawer state
const drawer = ref(false)

// Menu items (same as Dashboard)
const itensSideBar = ref([
  { name: "Admin", icon: "mdi-shield-crown", route: "admin", adminOnly: true },
  { name: "Trader", icon: "mdi-chart-line", route: "trader", traderOnly: true },
  { name: "Dashboard", icon: "mdi-view-dashboard", route: "dashboard" },
  { name: "Contas", icon: "mdi-bank", route: "contas" },
  { name: "Receitas", icon: "mdi-cash-plus", route: "receitas" },
  { name: "Despesas", icon: "mdi-cash-minus", route: "despesas" },
  { name: "Categorias", icon: "mdi-tag-multiple", route: "categorias" },
  { name: "Cartões de Crédito", icon: "mdi-credit-card-outline", route: "contas" },
  { name: "Notificações", icon: "mdi-bell", route: "notificacoes" },
  { name: "Perfil", icon: "mdi-account", route: "perfil" },
])

const filteredItensSideBar = computed(() => {
  return itensSideBar.value.filter((item) => {
    if (item.adminOnly && !rolesStore.isAdmin) return false
    if (item.traderOnly && !rolesStore.isTrader) return false
    return true
  })
})

const isActiveRoute = (routeName: string): boolean => {
  return route.name === routeName
}

// States
const loading = ref(true)
const dialog = ref(false)
const editMode = ref(false)
const saving = ref(false)

// Snackbar
const snackbar = ref({
  show: false,
  message: '',
  color: 'success'
})

// Data
const contas = ref<any[]>([])
const formData = ref({
  id: null,
  name: '',
  tipo_conta: '',
  saldo: 0,
  ativo: true
})

// Validation rules
const rules = {
  required: (v: any) => !!v || 'Campo obrigatório'
}

// Tipos de conta
const tiposConta = [
  'Conta Corrente',
  'Conta Poupança',
  'Conta Salário',
  'Conta Digital',
  'Investimentos',
  'Carteira'
]

// Computed
const totalSaldo = computed(() => {
  return contas.value.reduce((sum, conta) => sum + (conta.saldo || 0), 0)
})

const totalPrevisto = computed(() => {
  return contas.value.reduce((sum, conta) => sum + (conta.saldo_previsto || 0), 0)
})

// Methods
const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value / 100)
}

const getBankIcon = (name: string): string => {
  const icons: Record<string, string> = {
    'Nubank': 'mdi-alpha-n-circle',
    'Banco do Brasil': 'mdi-alpha-b-circle',
    'Caixa': 'mdi-alpha-c-circle',
    'Bradesco': 'mdi-alpha-b-circle',
    'Itaú': 'mdi-alpha-i-circle',
    'Santander': 'mdi-alpha-s-circle',
    'Inter': 'mdi-alpha-i-circle',
    'C6': 'mdi-alpha-c-circle',
    'PicPay': 'mdi-alpha-p-circle',
    'default': 'mdi-bank'
  }
  return icons[name] || icons.default
}

const getAccountGradient = (tipo: string): string => {
  const gradients: Record<string, string> = {
    'Conta Corrente': 'card-gradient-primary',
    'Conta Poupança': 'card-gradient-success',
    'Conta Salário': 'card-gradient-info',
    'Conta Digital': 'card-gradient-warning',
    'Investimentos': 'card-gradient-error',
    'Carteira': 'card-gradient-primary'
  }
  return gradients[tipo] || 'card-gradient-primary'
}

const getAccountColor = (tipo: string): string => {
  const colors: Record<string, string> = {
    'Conta Corrente': 'primary',
    'Conta Poupança': 'success',
    'Conta Salário': 'info',
    'Conta Digital': 'warning',
    'Investimentos': 'error',
    'Carteira': 'primary'
  }
  return colors[tipo] || 'primary'
}

const getSaldoColor = (saldo: number): string => {
  if (saldo > 0) return 'text-success'
  if (saldo < 0) return 'text-error'
  return 'text-grey'
}

const getDiferencaColor = (conta: any): string => {
  const diferenca = (conta.saldo_previsto || 0) - (conta.saldo || 0)
  if (diferenca > 0) return 'text-success'
  if (diferenca < 0) return 'text-error'
  return 'text-grey'
}

const openAddDialog = () => {
  editMode.value = false
  formData.value = {
    id: null,
    name: '',
    tipo_conta: '',
    saldo: 0,
    ativo: true
  }
  dialog.value = true
}

const editAccount = (conta: any) => {
  editMode.value = true
  formData.value = { ...conta }
  dialog.value = true
}

const deleteAccount = async (conta: any) => {
  if (!confirm(`Deseja realmente excluir a conta "${conta.name}"?`)) return

  try {
    // API call here
    await axiosInstance.delete(`/contas/${conta.id}`)
    
    contas.value = contas.value.filter(c => c.id !== conta.id)
    
    snackbar.value = {
      show: true,
      message: 'Conta excluída com sucesso!',
      color: 'success'
    }
  } catch (error: any) {
    console.error('Erro ao excluir conta:', error)
    snackbar.value = {
      show: true,
      message: error.response?.data?.message || 'Erro ao excluir conta',
      color: 'error'
    }
  }
}

const viewDetails = (conta: any) => {
  // Navigate to account details or open detailed dialog
  console.log('Ver detalhes:', conta)
}

const saveConta = async () => {
  try {
    saving.value = true
    
    // API call here
    if (editMode.value) {
      // Update existing account
      await axiosInstance.put(`/contas/${formData.value.id}`, formData.value)
      
      const index = contas.value.findIndex(c => c.id === formData.value.id)
      if (index !== -1) {
        contas.value[index] = { ...formData.value }
      }
      
      snackbar.value = {
        show: true,
        message: 'Conta atualizada com sucesso!',
        color: 'success'
      }
    } else {
      // Create new account
      const response = await axiosInstance.post('/contas', formData.value)
      contas.value.push(response.data)
      
      snackbar.value = {
        show: true,
        message: 'Conta adicionada com sucesso!',
        color: 'success'
      }
    }
    
    dialog.value = false
  } catch (error: any) {
    console.error('Erro ao salvar conta:', error)
    snackbar.value = {
      show: true,
      message: error.response?.data?.message || 'Erro ao salvar conta',
      color: 'error'
    }
  } finally {
    saving.value = false
  }
}

const fetchContas = async () => {
  try {
    loading.value = true
    
    // Mock data - replace with API call
    contas.value = [
      {
        id: 1,
        name: 'Nubank',
        tipo_conta: 'Conta Digital',
        saldo: 125000, // R$ 1.250,00
        saldo_previsto: 180000, // R$ 1.800,00
        ativo: true
      },
      {
        id: 2,
        name: 'Banco do Brasil',
        tipo_conta: 'Conta Corrente',
        saldo: 350000, // R$ 3.500,00
        saldo_previsto: 420000, // R$ 4.200,00
        ativo: true
      },
      {
        id: 3,
        name: 'Caixa',
        tipo_conta: 'Conta Poupança',
        saldo: 580000, // R$ 5.800,00
        saldo_previsto: 580000, // R$ 5.800,00
        ativo: true
      },
      {
        id: 4,
        name: 'Inter',
        tipo_conta: 'Investimentos',
        saldo: 1200000, // R$ 12.000,00
        saldo_previsto: 1350000, // R$ 13.500,00
        ativo: false
      }
    ]
    
  } catch (error: any) {
    console.error('Erro ao carregar contas:', error)
    snackbar.value = {
      show: true,
      message: error.response?.data?.message || 'Erro ao carregar contas',
      color: 'error'
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchContas()
})
</script>

<style scoped>
.contas-view {
  background-color: #f5f5f5;
  min-height: 100vh;
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

/* Account cards */
.account-card {
  transition: transform 0.2s, box-shadow 0.2s;
}

.account-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
}

/* Summary item */
.summary-item {
  border-right: 1px solid rgba(255, 255, 255, 0.2);
}

.summary-item:last-child {
  border-right: none;
}

/* Responsive */
@media (max-width: 600px) {
  .summary-item {
    border-right: none;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding-bottom: 16px;
    margin-bottom: 16px;
  }
  
  .summary-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
    margin-bottom: 0;
  }
}
</style>
