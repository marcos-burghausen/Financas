<template>
  <div class="cartao-credito-view">
    <!-- Header Section -->
    <div class="view-header mb-6">
      <div class="d-flex align-center gap-3 mb-2">
        <v-icon icon="mdi-credit-card" size="36" color="error" />
        <div>
          <h1 class="text-h5 font-weight-bold">Meus Cartões de Crédito</h1>
          <p class="text-caption text-medium-emphasis mb-0">Gerencie seus cartões, limites e faturas</p>
        </div>
      </div>
    </div>

    <!-- KPI Cards -->
    <v-row class="mb-6">
      <!-- Card: Total de Cartões -->
      <v-col cols="12" sm="6" lg="3">
        <v-card class="kpi-card pa-4" elevation="1">
          <div class="d-flex align-center justify-space-between">
            <div>
              <p class="text-caption text-medium-emphasis mb-2">Total de Cartões</p>
              <h3 class="text-h6 font-weight-bold">{{ cartoes.length }}</h3>
            </div>
            <v-icon icon="mdi-cards-outline" size="40" color="info" />
          </div>
        </v-card>
      </v-col>

      <!-- Card: Limite Total -->
      <v-col cols="12" sm="6" lg="3">
        <v-card class="kpi-card pa-4" elevation="1">
          <div class="d-flex align-center justify-space-between">
            <div>
              <p class="text-caption text-medium-emphasis mb-2">Limite Total</p>
              <h3 class="text-h6 font-weight-bold">{{ formatCurrency(summary.limiteTotal) }}</h3>
            </div>
            <v-icon icon="mdi-cash-multiple" size="40" color="primary" />
          </div>
        </v-card>
      </v-col>

      <!-- Card: Utilizado -->
      <v-col cols="12" sm="6" lg="3">
        <v-card class="kpi-card pa-4" elevation="1">
          <div class="d-flex align-center justify-space-between">
            <div>
              <p class="text-caption text-medium-emphasis mb-2">Utilizado</p>
              <h3 class="text-h6 font-weight-bold">{{ formatCurrency(summary.utilizado) }}</h3>
              <p class="text-caption text-medium-emphasis mb-0">{{ formatPercentage(summary.percentualUtilizado) }}</p>
            </div>
            <v-icon icon="mdi-percent" size="40" color="warning" />
          </div>
        </v-card>
      </v-col>

      <!-- Card: Disponível -->
      <v-col cols="12" sm="6" lg="3">
        <v-card class="kpi-card pa-4" elevation="1" :class="{ 'positive': summary.disponivel >= 0, 'negative': summary.disponivel < 0 }">
          <div class="d-flex align-center justify-space-between">
            <div>
              <p class="text-caption text-medium-emphasis mb-2">Disponível</p>
              <h3 class="text-h6 font-weight-bold">{{ formatCurrency(summary.disponivel) }}</h3>
            </div>
            <v-icon icon="mdi-check-circle-outline" :size="40" :color="summary.disponivel >= 0 ? 'success' : 'error'" />
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Filtros -->
    <v-card class="filters-card mb-6 pa-4" elevation="0" variant="outlined">
      <div class="d-flex flex-wrap gap-2 align-center">
        <v-text-field
          v-model="search"
          label="Buscar cartões..."
          prepend-inner-icon="mdi-magnify"
          clearable
          density="compact"
          class="flex-grow-1"
          style="min-width: 200px"
        />
        <v-select
          v-model="bandueiraFilter"
          :items="bandeirasPossivel"
          label="Bandeira"
          clearable
          density="compact"
          style="min-width: 150px"
        />
        <v-select
          v-model="statusFilter"
          :items="['ativo', 'inativo', 'bloqueado']"
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

    <!-- Tabela de Cartões -->
    <v-card class="mb-6" elevation="1">
      <v-data-table
        :items="filteredCartoes"
        :headers="headers"
        :loading="loading"
        class="cartoes-table"
        density="comfortable"
      >
        <template #item.nome="{ item }">
          <div class="d-flex align-center gap-2">
            <v-icon icon="mdi-credit-card" color="error" size="32" />
            <div>
              <div class="font-weight-bold">{{ item.nome }}</div>
              <div class="text-caption text-medium-emphasis">{{ item.numero }}</div>
            </div>
          </div>
        </template>

        <template #item.bandeiraira="{ item }">
          <v-chip
            :color="getBandeiraColor(item.bandeiraira)"
            label
            size="small"
          >
            {{ item.bandeiraira }}
          </v-chip>
        </template>

        <template #item.utilizado="{ item }">
          <div class="d-flex flex-column align-center">
            <span class="font-weight-bold">{{ formatCurrency(item.utilizado) }}</span>
            <v-progress-linear
              :value="(item.utilizado / item.limite) * 100"
              :color="getUtilizacaoColor(item.utilizado, item.limite)"
              class="mt-1"
              height="6"
              style="width: 100px"
            />
          </div>
        </template>

        <template #item.limite="{ item }">
          <div class="text-right font-weight-bold">
            {{ formatCurrency(item.limite) }}
          </div>
        </template>

        <template #item.vencimentoFatura="{ item }">
          <div class="text-center">
            <div class="font-weight-bold">{{ formatDate(item.vencimentoFatura) }}</div>
            <div class="text-caption text-medium-emphasis">
              {{ getDiasRestantes(item.vencimentoFatura) }}
            </div>
          </div>
        </template>

        <template #item.status="{ item }">
          <v-chip
            :color="getStatusColor(item.status)"
            label
            size="small"
          >
            {{ getStatusLabel(item.status) }}
          </v-chip>
        </template>

        <template #item.actions="{ item }">
          <div class="d-flex gap-1">
            <v-btn
              icon="mdi-pencil"
              variant="text"
              size="small"
              @click="editCartao(item)"
            />
            <v-btn
              icon="mdi-delete"
              variant="text"
              size="small"
              color="error"
              @click="deleteCartao(item.id)"
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
          {{ editingId ? 'Editar Cartão' : 'Novo Cartão' }}
        </v-card-title>

        <v-card-text class="pa-6 pt-4">
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="form.nome"
                label="Nome do Cartão"
                hint="Ex: Meu Visa"
                :rules="[v => !!v || 'Obrigatório']"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-select
                v-model="form.bandeiraira"
                :items="bandeirasPossivel"
                label="Bandeira"
                :rules="[v => !!v || 'Obrigatório']"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-select
                v-model="form.tipo"
                :items="['credito', 'debito', 'multiplo']"
                label="Tipo"
                :rules="[v => !!v || 'Obrigatório']"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.numero"
                label="Número do Cartão"
                placeholder="**** **** **** 1234"
                hint="Últimos 4 dígitos"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model.number="form.limite"
                label="Limite"
                type="number"
                :rules="[v => v > 0 || 'Deve ser maior que 0']"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model.number="form.utilizado"
                label="Utilizado"
                type="number"
                hint="Valor já gasto"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.vencimentoCartao"
                label="Vencimento do Cartão"
                type="month"
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model="form.vencimentoFatura"
                label="Vencimento da Fatura"
                type="date"
              />
            </v-col>

            <v-col cols="12">
              <v-select
                v-model="form.status"
                :items="['ativo', 'inativo', 'bloqueado']"
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
            @click="saveCartao"
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

interface Cartao {
  id: number
  nome: string
  bandeiraira: string
  tipo: 'credito' | 'debito' | 'multiplo'
  numero: string
  limite: number
  utilizado: number
  vencimentoCartao: string
  vencimentoFatura: string
  status: 'ativo' | 'inativo' | 'bloqueado'
  observacao: string
}

// State
const cartoes = ref<Cartao[]>([
  {
    id: 1,
    nome: 'Meu Visa',
    bandeiraira: 'Visa',
    tipo: 'credito',
    numero: '**** **** **** 1234',
    limite: 5000,
    utilizado: 2500,
    vencimentoCartao: '2027-05',
    vencimentoFatura: '2025-11-15',
    status: 'ativo',
    observacao: 'Principal'
  },
  {
    id: 2,
    nome: 'Mastercard',
    bandeiraira: 'Mastercard',
    tipo: 'credito',
    numero: '**** **** **** 5678',
    limite: 8000,
    utilizado: 1800,
    vencimentoCartao: '2026-08',
    vencimentoFatura: '2025-11-20',
    status: 'ativo',
    observacao: 'Backup'
  },
  {
    id: 3,
    nome: 'ELO',
    bandeiraira: 'ELO',
    tipo: 'multiplo',
    numero: '**** **** **** 9012',
    limite: 3000,
    utilizado: 0,
    vencimentoCartao: '2026-02',
    vencimentoFatura: '2025-11-10',
    status: 'ativo',
    observacao: 'Pouco utilizado'
  },
  {
    id: 4,
    nome: 'Cartão Antigo',
    bandeiraira: 'Visa',
    tipo: 'debito',
    numero: '**** **** **** 3456',
    limite: 1000,
    utilizado: 0,
    vencimentoCartao: '2024-12',
    vencimentoFatura: '2025-12-01',
    status: 'inativo',
    observacao: 'Desativado'
  }
])

const search = ref('')
const bandueiraFilter = ref('')
const statusFilter = ref('')
const dialogOpen = ref(false)
const loading = ref(false)
const editingId = ref<number | null>(null)

const form = ref<Omit<Cartao, 'id'>>({
  nome: '',
  bandeiraira: '',
  tipo: 'credito',
  numero: '',
  limite: 0,
  utilizado: 0,
  vencimentoCartao: '',
  vencimentoFatura: '',
  status: 'ativo',
  observacao: ''
})

const headers = [
  { title: 'Cartão', key: 'nome', align: 'start' as const },
  { title: 'Bandeira', key: 'bandeiraira', align: 'center' as const, width: '120px' },
  { title: 'Utilizado', key: 'utilizado', align: 'center' as const, width: '140px' },
  { title: 'Limite', key: 'limite', align: 'end' as const, width: '130px' },
  { title: 'Vencimento', key: 'vencimentoFatura', align: 'center' as const, width: '140px' },
  { title: 'Status', key: 'status', align: 'center' as const, width: '100px' },
  { title: 'Ações', key: 'actions', sortable: false, align: 'center' as const, width: '100px' }
]

const bandeirasPossivel = ['Visa', 'Mastercard', 'ELO', 'American Express', 'Hipercard', 'Diners']

// Computed
const summary = computed(() => {
  const limiteTotal = cartoes.value.reduce((sum, c) => sum + c.limite, 0)
  const utilizado = cartoes.value.reduce((sum, c) => sum + c.utilizado, 0)
  const disponivel = limiteTotal - utilizado
  return {
    limiteTotal,
    utilizado,
    disponivel,
    percentualUtilizado: limiteTotal > 0 ? (utilizado / limiteTotal) : 0
  }
})

const filteredCartoes = computed(() => {
  return cartoes.value.filter(cartao => {
    const matchSearch = cartao.nome.toLowerCase().includes(search.value.toLowerCase()) ||
                       cartao.numero.toLowerCase().includes(search.value.toLowerCase())
    const matchBandeiraira = !bandueiraFilter.value || cartao.bandeiraira === bandueiraFilter.value
    const matchStatus = !statusFilter.value || cartao.status === statusFilter.value
    return matchSearch && matchBandeiraira && matchStatus
  })
})

// Methods
function formatCurrency(value: number): string {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value)
}

function formatPercentage(value: number): string {
  return `${(value * 100).toFixed(1)}%`
}

function formatDate(date: string): string {
  return new Date(date).toLocaleDateString('pt-BR')
}

function getDiasRestantes(data: string): string {
  const vencimento = new Date(data)
  const hoje = new Date()
  const diff = vencimento.getTime() - hoje.getTime()
  const dias = Math.ceil(diff / (1000 * 3600 * 24))
  
  if (dias < 0) return 'Vencido'
  if (dias === 0) return 'Vence hoje'
  if (dias === 1) return 'Vence amanhã'
  return `Vence em ${dias}d`
}

function getBandeiraColor(bandeiraira: string): string {
  const colors: Record<string, string> = {
    'Visa': 'info',
    'Mastercard': 'warning',
    'ELO': 'success',
    'American Express': 'primary',
    'Hipercard': 'secondary',
    'Diners': 'accent'
  }
  return colors[bandeiraira] || 'default'
}

function getUtilizacaoColor(utilizado: number, limite: number): string {
  const percentual = (utilizado / limite) * 100
  if (percentual >= 80) return 'error'
  if (percentual >= 50) return 'warning'
  return 'success'
}

function getStatusColor(status: string): string {
  const colors: Record<string, string> = {
    'ativo': 'success',
    'inativo': 'secondary',
    'bloqueado': 'error'
  }
  return colors[status] || 'default'
}

function getStatusLabel(status: string): string {
  const labels: Record<string, string> = {
    'ativo': 'Ativo',
    'inativo': 'Inativo',
    'bloqueado': 'Bloqueado'
  }
  return labels[status] || status
}

function openAddDialog() {
  editingId.value = null
  form.value = {
    nome: '',
    bandeiraira: '',
    tipo: 'credito',
    numero: '',
    limite: 0,
    utilizado: 0,
    vencimentoCartao: '',
    vencimentoFatura: '',
    status: 'ativo',
    observacao: ''
  }
  dialogOpen.value = true
}

function closeDialog() {
  dialogOpen.value = false
}

function editCartao(cartao: Cartao) {
  editingId.value = cartao.id
  form.value = { ...cartao }
  dialogOpen.value = true
}

function saveCartao() {
  loading.value = true
  setTimeout(() => {
    if (editingId.value) {
      const index = cartoes.value.findIndex(c => c.id === editingId.value)
      if (index !== -1) {
        cartoes.value[index] = { ...form.value, id: editingId.value }
      }
    } else {
      const newId = Math.max(...cartoes.value.map(c => c.id), 0) + 1
      cartoes.value.push({ ...form.value, id: newId })
    }
    loading.value = false
    closeDialog()
  }, 500)
}

function deleteCartao(id: number) {
  if (confirm('Tem certeza que deseja deletar este cartão?')) {
    cartoes.value = cartoes.value.filter(c => c.id !== id)
  }
}

function clearFilters() {
  search.value = ''
  bandueiraFilter.value = ''
  statusFilter.value = ''
}
</script>

<style scoped lang="scss">
.cartao-credito-view {
  padding: 24px;
}

.view-header {
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
  padding-bottom: 16px;
}

.kpi-card {
  border-left: 4px solid rgb(var(--v-theme-error));
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
  background: rgba(var(--v-theme-error), 0.05);
}

.cartoes-table {
  :deep(.v-data-table) {
    background: rgb(var(--v-theme-background));
  }
}

@media (max-width: 600px) {
  .cartao-credito-view {
    padding: 16px;
  }

  .kpi-card {
    margin-bottom: 8px;
  }
}
</style>
