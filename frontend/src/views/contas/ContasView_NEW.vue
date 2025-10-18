<template>
  <div class="contas-view">
    <!-- Header Section -->
    <div class="view-header mb-6">
      <div class="d-flex align-center gap-3 mb-2">
        <v-icon icon="mdi-bank" size="36" color="primary" />
        <div>
          <h1 class="text-h5 font-weight-bold">Minhas Contas</h1>
          <p class="text-caption text-medium-emphasis mb-0">Gerencie suas contas correntes, poupanças e investimentos</p>
        </div>
      </div>
    </div>

    <!-- KPI Cards -->
    <v-row class="mb-6">
      <!-- Card: Total de Contas -->
      <v-col cols="12" sm="6" lg="3">
        <v-card class="kpi-card pa-4" elevation="1">
          <div class="d-flex align-center justify-space-between">
            <div>
              <p class="text-caption text-medium-emphasis mb-2">Total de Contas</p>
              <h3 class="text-h6 font-weight-bold">{{ contas.length }}</h3>
            </div>
            <v-icon icon="mdi-folder-multiple-outline" size="40" color="info" />
          </div>
        </v-card>
      </v-col>

      <!-- Card: Saldo Total -->
      <v-col cols="12" sm="6" lg="3">
        <v-card class="kpi-card pa-4" elevation="1" :class="{ 'positive': summary.totalBalance >= 0, 'negative': summary.totalBalance < 0 }">
          <div class="d-flex align-center justify-space-between">
            <div>
              <p class="text-caption text-medium-emphasis mb-2">Saldo Total</p>
              <h3 class="text-h6 font-weight-bold">{{ formatCurrency(summary.totalBalance) }}</h3>
            </div>
            <v-icon icon="mdi-wallet" :size="40" :color="summary.totalBalance >= 0 ? 'success' : 'error'" />
          </div>
        </v-card>
      </v-col>

      <!-- Card: Contas Ativas -->
      <v-col cols="12" sm="6" lg="3">
        <v-card class="kpi-card pa-4" elevation="1">
          <div class="d-flex align-center justify-space-between">
            <div>
              <p class="text-caption text-medium-emphasis mb-2">Contas Ativas</p>
              <h3 class="text-h6 font-weight-bold">{{ contasAtivas.length }}</h3>
            </div>
            <v-icon icon="mdi-check-circle-outline" size="40" color="success" />
          </div>
        </v-card>
      </v-col>

      <!-- Card: Limite Disponível -->
      <v-col cols="12" sm="6" lg="3">
        <v-card class="kpi-card pa-4" elevation="1">
          <div class="d-flex align-center justify-space-between">
            <div>
              <p class="text-caption text-medium-emphasis mb-2">Limite Disponível</p>
              <h3 class="text-h6 font-weight-bold">{{ formatCurrency(summary.limiteDisponivel) }}</h3>
            </div>
            <v-icon icon="mdi-credit-card-outline" size="40" color="warning" />
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Filtros -->
    <v-card class="filters-card mb-6 pa-4" elevation="0" variant="outlined">
      <div class="d-flex flex-wrap gap-2 align-center">
        <v-text-field
          v-model="search"
          label="Buscar contas..."
          prepend-inner-icon="mdi-magnify"
          clearable
          density="compact"
          class="flex-grow-1"
          style="min-width: 200px"
        />
        <v-select
          v-model="tipoFilter"
          :items="tiposContaPossivel"
          label="Tipo"
          clearable
          density="compact"
          style="min-width: 150px"
        />
        <v-select
          v-model="statusFilter"
          :items="['ativa', 'inativa']"
          label="Status"
          clearable
          density="compact"
          style="min-width: 150px"
        />
        <v-btn
          variant="outlined"
          @click="clearFilters"
          prepend-icon="mdi-close-circle-outline"
        >
          Limpar
        </v-btn>
      </div>
    </v-card>

    <!-- Tabela de Contas -->
    <v-card class="mb-6" elevation="1">
      <v-data-table
        :items="filteredContas"
        :headers="headers"
        :loading="loading"
        class="contas-table"
        density="comfortable"
      >
        <template #item.nome="{ item }">
          <div class="d-flex align-center gap-2">
            <v-avatar size="32" color="primary" text-color="white">
              {{ item.nome.charAt(0).toUpperCase() }}
            </v-avatar>
            <div>
              <div class="font-weight-bold">{{ item.nome }}</div>
              <div class="text-caption text-medium-emphasis">{{ item.banco }}</div>
            </div>
          </div>
        </template>

        <template #item.tipo="{ item }">
          <v-chip
            :color="getTipoColor(item.tipo)"
            label
            size="small"
          >
            {{ getTipoLabel(item.tipo) }}
          </v-chip>
        </template>

        <template #item.saldo="{ item }">
          <div class="text-right font-weight-bold" :class="item.saldo >= 0 ? 'text-success' : 'text-error'">
            {{ formatCurrency(item.saldo) }}
          </div>
        </template>

        <template #item.status="{ item }">
          <v-chip
            :color="item.status === 'ativa' ? 'success' : 'error'"
            label
            size="small"
          >
            {{ item.status === 'ativa' ? 'Ativa' : 'Inativa' }}
          </v-chip>
        </template>

        <template #item.actions="{ item }">
          <div class="d-flex gap-1">
            <v-btn
              icon="mdi-pencil"
              variant="text"
              size="small"
              @click="editConta(item)"
            />
            <v-btn
              icon="mdi-delete"
              variant="text"
              size="small"
              color="error"
              @click="deleteConta(item.id)"
            />
          </div>
        </template>
      </v-data-table>
    </v-card>

    <!-- Add/Edit Dialog -->
    <v-dialog
      v-model="dialogOpen"
      max-width="600px"
      persistent
    >
      <v-card>
        <v-card-title class="pa-6 pb-4">
          {{ editingId ? 'Editar Conta' : 'Nova Conta' }}
        </v-card-title>

        <v-card-text class="pa-6 pt-4">
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="form.nome"
                label="Nome da Conta"
                hint="Ex: Minha Conta Corrente"
                :rules="[v => !!v || 'Obrigatório']"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-select
                v-model="form.banco"
                :items="bancosPossivel"
                label="Banco"
                :rules="[v => !!v || 'Obrigatório']"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-select
                v-model="form.tipo"
                :items="tiposContaPossivel"
                label="Tipo de Conta"
                :rules="[v => !!v || 'Obrigatório']"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model.number="form.saldo"
                label="Saldo Inicial"
                type="number"
                hint="Saldo atual"
                :rules="[v => v !== null || 'Obrigatório']"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.agencia"
                label="Agência"
                placeholder="Ex: 1234"
              />
            </v-col>

            <v-col cols="12">
              <v-text-field
                v-model="form.numero"
                label="Número da Conta"
                placeholder="Ex: 123456-7"
              />
            </v-col>

            <v-col cols="12">
              <v-select
                v-model="form.status"
                :items="['ativa', 'inativa']"
                label="Status"
                :rules="[v => !!v || 'Obrigatório']"
              />
            </v-col>

            <v-col cols="12">
              <v-textarea
                v-model="form.observacao"
                label="Observações"
                rows="2"
              />
            </v-col>
          </v-row>
        </v-card-text>

        <v-card-actions class="pa-6 pt-0">
          <v-spacer />
          <v-btn
            variant="outlined"
            @click="closeDialog"
          >
            Cancelar
          </v-btn>
          <v-btn
            color="primary"
            @click="saveConta"
            :loading="loading"
          >
            {{ editingId ? 'Atualizar' : 'Adicionar' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'

interface Conta {
  id: number
  nome: string
  banco: string
  tipo: 'corrente' | 'poupanca' | 'investimento'
  numero: string
  agencia: string
  saldo: number
  status: 'ativa' | 'inativa'
  observacao: string
  limite?: number
  dataAbertura?: string
}

// State
const contas = ref<Conta[]>([
  {
    id: 1,
    nome: 'Conta Corrente Principal',
    banco: 'Banco do Brasil',
    tipo: 'corrente',
    numero: '123456-7',
    agencia: '1234',
    saldo: 5200.50,
    status: 'ativa',
    observacao: 'Conta de salário',
    limite: 1000
  },
  {
    id: 2,
    nome: 'Poupança Emergência',
    banco: 'Caixa Econômica',
    tipo: 'poupanca',
    numero: '654321-9',
    agencia: '5678',
    saldo: 15000,
    status: 'ativa',
    observacao: 'Fundo de emergência'
  },
  {
    id: 3,
    nome: 'Investimento Tesouro',
    banco: 'Itaú',
    tipo: 'investimento',
    numero: '789456-3',
    agencia: '9012',
    saldo: 8500,
    status: 'ativa',
    observacao: 'Tesouro Direto'
  },
  {
    id: 4,
    nome: 'Conta Antiga',
    banco: 'Banco Bradesco',
    tipo: 'corrente',
    numero: '321654-8',
    agencia: '3456',
    saldo: 100,
    status: 'inativa',
    observacao: 'Desativada'
  }
])

const search = ref('')
const tipoFilter = ref('')
const statusFilter = ref('')
const dialogOpen = ref(false)
const loading = ref(false)
const editingId = ref<number | null>(null)

const form = ref<Omit<Conta, 'id'>>({
  nome: '',
  banco: '',
  tipo: 'corrente',
  numero: '',
  agencia: '',
  saldo: 0,
  status: 'ativa',
  observacao: ''
})

const headers = [
  { title: 'Conta', key: 'nome', align: 'start' as const },
  { title: 'Tipo', key: 'tipo', align: 'center' as const, width: '120px' },
  { title: 'Saldo', key: 'saldo', align: 'end' as const, width: '150px' },
  { title: 'Status', key: 'status', align: 'center' as const, width: '100px' },
  { title: 'Ações', key: 'actions', sortable: false, align: 'center' as const, width: '100px' }
]

const tiposContaPossivel = ['corrente', 'poupança', 'investimento']
const bancosPossivel = [
  'Banco do Brasil',
  'Caixa Econômica',
  'Itaú',
  'Bradesco',
  'Santander',
  'Nubank',
  'Inter',
  'Itaú',
  'Outro'
]

// Computed
const contasAtivas = computed(() => contas.value.filter(c => c.status === 'ativa'))

const summary = computed(() => ({
  totalBalance: contas.value.reduce((sum, c) => sum + c.saldo, 0),
  contasAtivas: contasAtivas.value.length,
  limiteDisponivel: contas.value.reduce((sum, c) => sum + (c.limite || 0), 0)
}))

const filteredContas = computed(() => {
  return contas.value.filter(conta => {
    const matchSearch = conta.nome.toLowerCase().includes(search.value.toLowerCase()) ||
                       conta.banco.toLowerCase().includes(search.value.toLowerCase())
    const matchTipo = !tipoFilter.value || conta.tipo === tipoFilter.value
    const matchStatus = !statusFilter.value || conta.status === statusFilter.value
    return matchSearch && matchTipo && matchStatus
  })
})

// Methods
function formatCurrency(value: number): string {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value)
}

function getTipoColor(tipo: string): string {
  const colors: Record<string, string> = {
    'corrente': 'primary',
    'poupanca': 'success',
    'investimento': 'info'
  }
  return colors[tipo] || 'secondary'
}

function getTipoLabel(tipo: string): string {
  const labels: Record<string, string> = {
    'corrente': 'Corrente',
    'poupanca': 'Poupança',
    'investimento': 'Investimento'
  }
  return labels[tipo] || tipo
}

function openAddDialog() {
  editingId.value = null
  form.value = {
    nome: '',
    banco: '',
    tipo: 'corrente',
    numero: '',
    agencia: '',
    saldo: 0,
    status: 'ativa',
    observacao: ''
  }
  dialogOpen.value = true
}

function closeDialog() {
  dialogOpen.value = false
}

function editConta(conta: Conta) {
  editingId.value = conta.id
  form.value = { ...conta }
  dialogOpen.value = true
}

function saveConta() {
  loading.value = true
  setTimeout(() => {
    if (editingId.value) {
      const index = contas.value.findIndex(c => c.id === editingId.value)
      if (index !== -1) {
        contas.value[index] = { ...form.value, id: editingId.value }
      }
    } else {
      const newId = Math.max(...contas.value.map(c => c.id), 0) + 1
      contas.value.push({ ...form.value, id: newId })
    }
    loading.value = false
    closeDialog()
  }, 500)
}

function deleteConta(id: number) {
  if (confirm('Tem certeza que deseja deletar esta conta?')) {
    contas.value = contas.value.filter(c => c.id !== id)
  }
}

function clearFilters() {
  search.value = ''
  tipoFilter.value = ''
  statusFilter.value = ''
}
</script>

<style scoped lang="scss">
.contas-view {
  padding: 24px;
}

.view-header {
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
  padding-bottom: 16px;
}

.kpi-card {
  border-left: 4px solid rgb(var(--v-theme-primary));
  transition: all 0.3s ease;

  &:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
  }

  &.positive {
    border-left-color: rgb(var(--v-theme-success));
  }

  &.negative {
    border-left-color: rgb(var(--v-theme-error));
  }
}

.filters-card {
  background: rgba(var(--v-theme-primary), 0.05);
}

.contas-table {
  :deep(.v-data-table) {
    background: rgb(var(--v-theme-background));
  }
}

@media (max-width: 600px) {
  .contas-view {
    padding: 16px;
  }

  .kpi-card {
    margin-bottom: 8px;
  }
}
</style>
