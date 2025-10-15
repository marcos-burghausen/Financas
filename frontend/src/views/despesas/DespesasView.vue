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
      <v-container fluid class="despesas-view pa-6">
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
                    <v-icon icon="mdi-cash-minus" size="36" class="mr-3" color="error" />
                    Despesas
                  </h1>
                  <p class="text-subtitle-1 text-grey mb-0">
                    Gerencie suas despesas e gastos
                  </p>
                </div>
              </div>
              <v-btn
                color="error"
                prepend-icon="mdi-plus"
                @click="openAddDialog"
              >
                Nova Despesa
              </v-btn>
            </div>
          </v-col>
        </v-row>

        <!-- Summary Cards -->
        <v-row class="mb-4">
          <v-col cols="12" sm="6" md="3">
            <v-card elevation="4" class="summary-card h-100">
              <div class="card-gradient card-gradient-error pa-4">
                <div class="d-flex justify-space-between align-start mb-3">
                  <div>
                    <p class="text-body-2 text-white mb-1">Total do Mês</p>
                    <h2 class="text-h5 text-white font-weight-bold">
                      {{ formatCurrency(summary.totalMes) }}
                    </h2>
                  </div>
                  <v-avatar color="rgba(255,255,255,0.2)" size="48">
                    <v-icon icon="mdi-cash-multiple" color="white" size="28" />
                  </v-avatar>
                </div>
                <v-chip size="small" color="white" text-color="error" class="font-weight-medium">
                  <v-icon icon="mdi-calendar-month" start size="16" />
                  {{ summary.qtdMes }} despesas
                </v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3">
            <v-card elevation="4" class="summary-card h-100">
              <div class="card-gradient card-gradient-info pa-4">
                <div class="d-flex justify-space-between align-start mb-3">
                  <div>
                    <p class="text-body-2 text-white mb-1">Pago</p>
                    <h2 class="text-h5 text-white font-weight-bold">
                      {{ formatCurrency(summary.pago) }}
                    </h2>
                  </div>
                  <v-avatar color="rgba(255,255,255,0.2)" size="48">
                    <v-icon icon="mdi-check-circle" color="white" size="28" />
                  </v-avatar>
                </div>
                <v-chip size="small" color="white" text-color="info" class="font-weight-medium">
                  <v-icon icon="mdi-trending-down" start size="16" />
                  {{ summary.qtdPago }} pagas
                </v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3">
            <v-card elevation="4" class="summary-card h-100">
              <div class="card-gradient card-gradient-warning pa-4">
                <div class="d-flex justify-space-between align-start mb-3">
                  <div>
                    <p class="text-body-2 text-white mb-1">Pendente</p>
                    <h2 class="text-h5 text-white font-weight-bold">
                      {{ formatCurrency(summary.pendente) }}
                    </h2>
                  </div>
                  <v-avatar color="rgba(255,255,255,0.2)" size="48">
                    <v-icon icon="mdi-clock-alert" color="white" size="28" />
                  </v-avatar>
                </div>
                <v-chip size="small" color="white" text-color="warning" class="font-weight-medium">
                  <v-icon icon="mdi-alert-circle" start size="16" />
                  {{ summary.qtdPendente }} a pagar
                </v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3">
            <v-card elevation="4" class="summary-card h-100">
              <div class="card-gradient card-gradient-primary pa-4">
                <div class="d-flex justify-space-between align-start mb-3">
                  <div>
                    <p class="text-body-2 text-white mb-1">Média Mensal</p>
                    <h2 class="text-h5 text-white font-weight-bold">
                      {{ formatCurrency(summary.mediaMensal) }}
                    </h2>
                  </div>
                  <v-avatar color="rgba(255,255,255,0.2)" size="48">
                    <v-icon icon="mdi-chart-line" color="white" size="28" />
                  </v-avatar>
                </div>
                <v-chip size="small" color="white" text-color="primary" class="font-weight-medium">
                  <v-icon icon="mdi-chart-timeline-variant" start size="16" />
                  Últimos 3 meses
                </v-chip>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <!-- Filters and Actions -->
        <v-row class="mb-4">
          <v-col cols="12">
            <v-card elevation="2" class="pa-4">
              <v-row>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="filters.search"
                    label="Buscar"
                    prepend-inner-icon="mdi-magnify"
                    variant="outlined"
                    density="compact"
                    clearable
                    hide-details
                  />
                </v-col>
                <v-col cols="12" md="2">
                  <v-select
                    v-model="filters.status"
                    label="Status"
                    prepend-inner-icon="mdi-filter"
                    variant="outlined"
                    density="compact"
                    :items="statusOptions"
                    clearable
                    hide-details
                  />
                </v-col>
                <v-col cols="12" md="3">
                  <v-select
                    v-model="filters.categoria"
                    label="Categoria"
                    prepend-inner-icon="mdi-tag"
                    variant="outlined"
                    density="compact"
                    :items="categorias"
                    clearable
                    hide-details
                  />
                </v-col>
                <v-col cols="12" md="2">
                  <v-text-field
                    v-model="filters.dataInicio"
                    label="Data Início"
                    type="date"
                    variant="outlined"
                    density="compact"
                    hide-details
                  />
                </v-col>
                <v-col cols="12" md="2">
                  <v-text-field
                    v-model="filters.dataFim"
                    label="Data Fim"
                    type="date"
                    variant="outlined"
                    density="compact"
                    hide-details
                  />
                </v-col>
              </v-row>
            </v-card>
          </v-col>
        </v-row>

        <!-- Loading -->
        <v-row v-if="loading">
          <v-col cols="12" class="text-center py-12">
            <v-progress-circular indeterminate color="error" size="64" />
            <p class="text-grey mt-4">Carregando despesas...</p>
          </v-col>
        </v-row>

        <!-- Empty State -->
        <v-row v-else-if="filteredDespesas.length === 0">
          <v-col cols="12">
            <v-card elevation="2" class="text-center pa-12">
              <v-icon icon="mdi-cash-minus" size="80" color="grey-lighten-1" />
              <h3 class="text-h5 mt-4 mb-2">Nenhuma despesa encontrada</h3>
              <p class="text-grey mb-4">
                {{ filters.search || filters.status || filters.categoria 
                  ? 'Tente ajustar os filtros ou adicionar uma nova despesa' 
                  : 'Adicione sua primeira despesa para começar a controlar seus gastos' }}
              </p>
              <v-btn
                color="error"
                size="large"
                prepend-icon="mdi-plus"
                @click="openAddDialog"
              >
                Adicionar Despesa
              </v-btn>
            </v-card>
          </v-col>
        </v-row>

        <!-- Despesas Table -->
        <v-row v-else>
          <v-col cols="12">
            <v-card elevation="4">
              <div class="card-gradient card-gradient-error pa-4">
                <div class="d-flex align-center justify-space-between">
                  <div class="d-flex align-center">
                    <v-icon icon="mdi-format-list-bulleted" size="28" color="white" class="mr-3" />
                    <div>
                      <h3 class="text-h6 text-white font-weight-bold">Listagem de Despesas</h3>
                      <p class="text-body-2 text-white opacity-90 mb-0">
                        {{ filteredDespesas.length }} {{ filteredDespesas.length === 1 ? 'despesa' : 'despesas' }}
                      </p>
                    </div>
                  </div>
                  <div class="d-flex gap-2">
                    <v-btn
                      variant="text"
                      color="white"
                      prepend-icon="mdi-file-excel"
                      size="small"
                      @click="exportToExcel"
                    >
                      Excel
                    </v-btn>
                    <v-btn
                      variant="text"
                      color="white"
                      prepend-icon="mdi-file-pdf-box"
                      size="small"
                      @click="exportToPDF"
                    >
                      PDF
                    </v-btn>
                  </div>
                </div>
              </div>

              <v-data-table
                :headers="headers"
                :items="filteredDespesas"
                :items-per-page="10"
                class="elevation-0"
                :loading="loading"
              >
                <!-- Data -->
                <template #item.data_vencimento="{ item }">
                  <div class="d-flex align-center">
                    <v-icon 
                      :icon="isVencida(item.data_vencimento) ? 'mdi-alert-circle' : 'mdi-calendar'" 
                      :color="isVencida(item.data_vencimento) ? 'error' : 'grey'"
                      size="18"
                      class="mr-2"
                    />
                    {{ formatDate(item.data_vencimento) }}
                  </div>
                </template>

                <!-- Descrição -->
                <template #item.descricao="{ item }">
                  <div class="py-2">
                    <div class="font-weight-medium">{{ item.descricao }}</div>
                    <div class="text-caption text-grey">{{ item.conta }}</div>
                  </div>
                </template>

                <!-- Categoria -->
                <template #item.categoria="{ item }">
                  <v-chip
                    size="small"
                    variant="tonal"
                    color="error"
                  >
                    <v-icon :icon="getCategoryIcon(item.categoria)" start size="16" />
                    {{ item.categoria }}
                  </v-chip>
                </template>

                <!-- Status -->
                <template #item.status="{ item }">
                  <v-chip
                    size="small"
                    :color="getStatusColor(item.status)"
                    variant="flat"
                    class="font-weight-medium"
                  >
                    <v-icon :icon="getStatusIcon(item.status)" start size="16" />
                    {{ item.status }}
                  </v-chip>
                </template>

                <!-- Valor -->
                <template #item.valor="{ item }">
                  <div class="text-error font-weight-bold text-end">
                    {{ formatCurrency(item.valor) }}
                  </div>
                </template>

                <!-- Actions -->
                <template #item.actions="{ item }">
                  <div class="d-flex gap-1">
                    <v-tooltip text="Marcar como Paga" location="top">
                      <template #activator="{ props }">
                        <v-btn
                          v-if="item.status === 'PENDENTE'"
                          icon
                          variant="text"
                          size="small"
                          color="success"
                          v-bind="props"
                          @click="marcarPaga(item)"
                        >
                          <v-icon icon="mdi-check-circle" />
                        </v-btn>
                      </template>
                    </v-tooltip>
                    
                    <v-tooltip text="Editar" location="top">
                      <template #activator="{ props }">
                        <v-btn
                          icon
                          variant="text"
                          size="small"
                          color="primary"
                          v-bind="props"
                          @click="editDespesa(item)"
                        >
                          <v-icon icon="mdi-pencil" />
                        </v-btn>
                      </template>
                    </v-tooltip>

                    <v-tooltip text="Excluir" location="top">
                      <template #activator="{ props }">
                        <v-btn
                          icon
                          variant="text"
                          size="small"
                          color="error"
                          v-bind="props"
                          @click="deleteDespesa(item)"
                        >
                          <v-icon icon="mdi-delete" />
                        </v-btn>
                      </template>
                    </v-tooltip>
                  </div>
                </template>
              </v-data-table>
            </v-card>
          </v-col>
        </v-row>

        <!-- Add/Edit Dialog -->
        <v-dialog v-model="dialog" max-width="700">
          <v-card>
            <div class="card-gradient card-gradient-error pa-4">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center">
                  <v-icon icon="mdi-cash-minus" size="32" color="white" class="mr-3" />
                  <h2 class="text-h5 text-white font-weight-bold">
                    {{ editMode ? 'Editar Despesa' : 'Nova Despesa' }}
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
              <v-form ref="form" @submit.prevent="saveDespesa">
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      v-model="formData.descricao"
                      label="Descrição *"
                      prepend-inner-icon="mdi-text"
                      variant="outlined"
                      :rules="[rules.required]"
                    />
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-select
                      v-model="formData.categoria"
                      label="Categoria *"
                      prepend-inner-icon="mdi-tag"
                      variant="outlined"
                      :items="categorias"
                      :rules="[rules.required]"
                    />
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-select
                      v-model="formData.conta"
                      label="Conta *"
                      prepend-inner-icon="mdi-bank"
                      variant="outlined"
                      :items="contas"
                      :rules="[rules.required]"
                    />
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.valor"
                      label="Valor *"
                      prepend-inner-icon="mdi-currency-brl"
                      variant="outlined"
                      type="number"
                      step="0.01"
                      :rules="[rules.required]"
                    />
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.data_vencimento"
                      label="Data de Vencimento *"
                      prepend-inner-icon="mdi-calendar"
                      variant="outlined"
                      type="date"
                      :rules="[rules.required]"
                    />
                  </v-col>

                  <v-col cols="12">
                    <v-select
                      v-model="formData.status"
                      label="Status *"
                      prepend-inner-icon="mdi-checkbox-marked-circle"
                      variant="outlined"
                      :items="statusOptions"
                      :rules="[rules.required]"
                    />
                  </v-col>

                  <v-col cols="12">
                    <v-textarea
                      v-model="formData.observacao"
                      label="Observação"
                      prepend-inner-icon="mdi-note-text"
                      variant="outlined"
                      rows="3"
                    />
                  </v-col>
                </v-row>
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
                color="error"
                @click="saveDespesa"
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

// Menu items
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

// Filters
const filters = ref({
  search: '',
  status: null,
  categoria: null,
  dataInicio: '',
  dataFim: ''
})

// Data
const despesas = ref<any[]>([])
const formData = ref({
  id: null,
  descricao: '',
  categoria: '',
  conta: '',
  valor: 0,
  data_vencimento: '',
  status: 'PENDENTE',
  observacao: ''
})

// Options
const statusOptions = ['PENDENTE', 'PAGO', 'ATRASADO']
const categorias = ['Alimentação', 'Transporte', 'Moradia', 'Saúde', 'Educação', 'Lazer', 'Outros']
const contas = ['Nubank', 'Banco do Brasil', 'Caixa', 'Inter']

// Validation rules
const rules = {
  required: (v: any) => !!v || 'Campo obrigatório'
}

// Table headers
const headers = [
  { title: 'Data', key: 'data_vencimento', sortable: true },
  { title: 'Descrição', key: 'descricao', sortable: true },
  { title: 'Categoria', key: 'categoria', sortable: true },
  { title: 'Status', key: 'status', sortable: true },
  { title: 'Valor', key: 'valor', sortable: true, align: 'end' },
  { title: 'Ações', key: 'actions', sortable: false, align: 'center' }
]

// Computed
const summary = computed(() => {
  const totalMes = despesas.value.reduce((sum, d) => sum + d.valor, 0)
  const pago = despesas.value.filter(d => d.status === 'PAGO').reduce((sum, d) => sum + d.valor, 0)
  const pendente = despesas.value.filter(d => d.status === 'PENDENTE').reduce((sum, d) => sum + d.valor, 0)
  
  return {
    totalMes,
    pago,
    pendente,
    mediaMensal: totalMes,
    qtdMes: despesas.value.length,
    qtdPago: despesas.value.filter(d => d.status === 'PAGO').length,
    qtdPendente: despesas.value.filter(d => d.status === 'PENDENTE').length
  }
})

const filteredDespesas = computed(() => {
  return despesas.value.filter(despesa => {
    if (filters.value.search && !despesa.descricao.toLowerCase().includes(filters.value.search.toLowerCase())) {
      return false
    }
    if (filters.value.status && despesa.status !== filters.value.status) {
      return false
    }
    if (filters.value.categoria && despesa.categoria !== filters.value.categoria) {
      return false
    }
    if (filters.value.dataInicio && despesa.data_vencimento < filters.value.dataInicio) {
      return false
    }
    if (filters.value.dataFim && despesa.data_vencimento > filters.value.dataFim) {
      return false
    }
    return true
  })
})

// Methods
const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value / 100)
}

const formatDate = (date: string): string => {
  return new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  }).format(new Date(date))
}

const isVencida = (date: string): boolean => {
  return new Date(date) < new Date()
}

const getCategoryIcon = (categoria: string): string => {
  const icons: Record<string, string> = {
    'Alimentação': 'mdi-food',
    'Transporte': 'mdi-car',
    'Moradia': 'mdi-home',
    'Saúde': 'mdi-heart-pulse',
    'Educação': 'mdi-school',
    'Lazer': 'mdi-palm-tree',
    'Outros': 'mdi-dots-horizontal'
  }
  return icons[categoria] || 'mdi-tag'
}

const getStatusColor = (status: string): string => {
  const colors: Record<string, string> = {
    'PAGO': 'success',
    'PENDENTE': 'warning',
    'ATRASADO': 'error'
  }
  return colors[status] || 'grey'
}

const getStatusIcon = (status: string): string => {
  const icons: Record<string, string> = {
    'PAGO': 'mdi-check-circle',
    'PENDENTE': 'mdi-clock-alert',
    'ATRASADO': 'mdi-alert-circle'
  }
  return icons[status] || 'mdi-help-circle'
}

const openAddDialog = () => {
  editMode.value = false
  formData.value = {
    id: null,
    descricao: '',
    categoria: '',
    conta: '',
    valor: 0,
    data_vencimento: '',
    status: 'PENDENTE',
    observacao: ''
  }
  dialog.value = true
}

const editDespesa = (despesa: any) => {
  editMode.value = true
  formData.value = { ...despesa }
  dialog.value = true
}

const deleteDespesa = async (despesa: any) => {
  if (!confirm(`Deseja realmente excluir a despesa "${despesa.descricao}"?`)) return

  try {
    await axiosInstance.delete(`/despesas/${despesa.id}`)
    
    despesas.value = despesas.value.filter(d => d.id !== despesa.id)
    
    snackbar.value = {
      show: true,
      message: 'Despesa excluída com sucesso!',
      color: 'success'
    }
  } catch (error: any) {
    console.error('Erro ao excluir despesa:', error)
    snackbar.value = {
      show: true,
      message: error.response?.data?.message || 'Erro ao excluir despesa',
      color: 'error'
    }
  }
}

const marcarPaga = async (despesa: any) => {
  try {
    await axiosInstance.patch(`/despesas/${despesa.id}/pagar`)
    
    const index = despesas.value.findIndex(d => d.id === despesa.id)
    if (index !== -1) {
      despesas.value[index].status = 'PAGO'
    }
    
    snackbar.value = {
      show: true,
      message: 'Despesa marcada como paga!',
      color: 'success'
    }
  } catch (error: any) {
    console.error('Erro ao marcar despesa:', error)
    snackbar.value = {
      show: true,
      message: error.response?.data?.message || 'Erro ao marcar despesa',
      color: 'error'
    }
  }
}

const saveDespesa = async () => {
  try {
    saving.value = true
    
    if (editMode.value) {
      await axiosInstance.put(`/despesas/${formData.value.id}`, formData.value)
      
      const index = despesas.value.findIndex(d => d.id === formData.value.id)
      if (index !== -1) {
        despesas.value[index] = { ...formData.value }
      }
      
      snackbar.value = {
        show: true,
        message: 'Despesa atualizada com sucesso!',
        color: 'success'
      }
    } else {
      const response = await axiosInstance.post('/despesas', formData.value)
      despesas.value.push(response.data)
      
      snackbar.value = {
        show: true,
        message: 'Despesa adicionada com sucesso!',
        color: 'success'
      }
    }
    
    dialog.value = false
  } catch (error: any) {
    console.error('Erro ao salvar despesa:', error)
    snackbar.value = {
      show: true,
      message: error.response?.data?.message || 'Erro ao salvar despesa',
      color: 'error'
    }
  } finally {
    saving.value = false
  }
}

const exportToExcel = () => {
  console.log('Exportar para Excel')
  snackbar.value = {
    show: true,
    message: 'Funcionalidade em desenvolvimento',
    color: 'info'
  }
}

const exportToPDF = () => {
  console.log('Exportar para PDF')
  snackbar.value = {
    show: true,
    message: 'Funcionalidade em desenvolvimento',
    color: 'info'
  }
}

const fetchDespesas = async () => {
  try {
    loading.value = true
    
    // Mock data - replace with API call
    despesas.value = [
      {
        id: 1,
        descricao: 'Aluguel Apartamento',
        categoria: 'Moradia',
        conta: 'Nubank',
        valor: 150000, // R$ 1.500,00
        data_vencimento: '2024-10-05',
        status: 'PAGO',
        observacao: ''
      },
      {
        id: 2,
        descricao: 'Supermercado',
        categoria: 'Alimentação',
        conta: 'Banco do Brasil',
        valor: 45000, // R$ 450,00
        data_vencimento: '2024-10-12',
        status: 'PAGO',
        observacao: ''
      },
      {
        id: 3,
        descricao: 'Conta de Luz',
        categoria: 'Moradia',
        conta: 'Caixa',
        valor: 18000, // R$ 180,00
        data_vencimento: '2024-10-20',
        status: 'PENDENTE',
        observacao: ''
      },
      {
        id: 4,
        descricao: 'Gasolina',
        categoria: 'Transporte',
        conta: 'Inter',
        valor: 30000, // R$ 300,00
        data_vencimento: '2024-10-15',
        status: 'PENDENTE',
        observacao: ''
      },
      {
        id: 5,
        descricao: 'Academia',
        categoria: 'Saúde',
        conta: 'Nubank',
        valor: 12000, // R$ 120,00
        data_vencimento: '2024-10-01',
        status: 'ATRASADO',
        observacao: 'Renovar mensalidade'
      },
      {
        id: 6,
        descricao: 'Netflix',
        categoria: 'Lazer',
        conta: 'Nubank',
        valor: 5500, // R$ 55,00
        data_vencimento: '2024-10-08',
        status: 'PAGO',
        observacao: ''
      }
    ]
    
  } catch (error: any) {
    console.error('Erro ao carregar despesas:', error)
    snackbar.value = {
      show: true,
      message: error.response?.data?.message || 'Erro ao carregar despesas',
      color: 'error'
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchDespesas()
})
</script>

<style scoped>
.despesas-view {
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

/* Summary cards */
.summary-card {
  transition: transform 0.2s;
}

.summary-card:hover {
  transform: translateY(-4px);
}

/* Table styling */
:deep(.v-data-table) {
  background-color: transparent;
}

:deep(.v-data-table thead th) {
  font-weight: 600;
  background-color: #fafafa;
}

:deep(.v-data-table tbody tr:hover) {
  background-color: rgba(0, 0, 0, 0.02);
}

/* Responsive */
@media (max-width: 960px) {
  .despesas-view {
    padding: 16px !important;
  }
}
</style>
