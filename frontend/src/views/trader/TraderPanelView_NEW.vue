<template>
  <v-container fluid class="trader-panel pa-6">
    <!-- Header -->
    <div class="d-flex align-center justify-space-between mb-6">
      <div>
        <div class="d-flex align-center gap-3 mb-2">
          <v-icon icon="mdi-chart-line" size="40" color="success" />
          <h1 class="text-h4 font-weight-bold">Painel Trader</h1>
        </div>
        <p class="text-subtitle-2 text-medium-emphasis">
          Monitore seu portfólio e análises de investimentos
        </p>
      </div>
    </div>

    <!-- KPI Cards -->
    <v-row class="mb-6">
      <v-col cols="12" sm="6" md="3">
        <v-card elevation="2" class="kpi-card">
          <v-card-text class="pa-4">
            <div class="d-flex justify-space-between align-center">
              <div>
                <p class="text-caption text-medium-emphasis mb-2">Portfólio Total</p>
                <h2 class="text-h5 font-weight-bold">{{ formatCurrency(summary.portfolioTotal) }}</h2>
              </div>
              <v-icon icon="mdi-wallet" size="48" color="success" opacity="0.2" />
            </div>
            <v-divider class="my-3" />
            <div class="d-flex align-center gap-1">
              <v-icon icon="mdi-trending-up" size="18" :color="summary.rentabilidadeAnual >= 0 ? 'success' : 'error'" />
              <span class="text-caption" :class="`text-${summary.rentabilidadeAnual >= 0 ? 'success' : 'error'}`">
                {{ summary.rentabilidadeAnual >= 0 ? '+' : '' }}{{ summary.rentabilidadeAnual.toFixed(2) }}% anual
              </span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card elevation="2" class="kpi-card">
          <v-card-text class="pa-4">
            <div class="d-flex justify-space-between align-center">
              <div>
                <p class="text-caption text-medium-emphasis mb-2">Investimentos Ativos</p>
                <h2 class="text-h5 font-weight-bold">{{ summary.investimentosAtivos }}</h2>
              </div>
              <v-icon icon="mdi-chart-pie" size="48" color="primary" opacity="0.2" />
            </div>
            <v-divider class="my-3" />
            <div class="d-flex align-center gap-1">
              <span class="text-caption text-primary font-weight-bold">{{ summary.totalCategorias }} categorias</span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card elevation="2" class="kpi-card">
          <v-card-text class="pa-4">
            <div class="d-flex justify-space-between align-center">
              <div>
                <p class="text-caption text-medium-emphasis mb-2">Rendimento Mensal</p>
                <h2 class="text-h5 font-weight-bold">{{ formatCurrency(summary.rendimentoMensal) }}</h2>
              </div>
              <v-icon icon="mdi-cash-multiple" size="48" color="info" opacity="0.2" />
            </div>
            <v-divider class="my-3" />
            <div class="d-flex align-center gap-1">
              <span class="text-caption text-info font-weight-bold">Média 6 meses</span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card elevation="2" class="kpi-card">
          <v-card-text class="pa-4">
            <div class="d-flex justify-space-between align-center">
              <div>
                <p class="text-caption text-medium-emphasis mb-2">Diversificação</p>
                <h2 class="text-h5 font-weight-bold">{{ summary.diversificacao }}%</h2>
              </div>
              <v-icon icon="mdi-chart-scatter-plot" size="48" color="warning" opacity="0.2" />
            </div>
            <v-divider class="my-3" />
            <div class="d-flex align-center gap-1">
              <v-icon icon="mdi-check-circle" size="16" color="success" />
              <span class="text-caption text-success">Ótima distribuição</span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Filtros e Busca -->
    <v-card elevation="2" class="mb-6">
      <v-card-text class="pa-4">
        <v-row class="align-center">
          <v-col cols="12" sm="6" md="3">
            <v-text-field
              v-model="search"
              prepend-inner-icon="mdi-magnify"
              label="Buscar investimento..."
              variant="outlined"
              density="compact"
              clearable
              hide-details
            />
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-select
              v-model="typeFilter"
              :items="tiposInvestimento"
              label="Tipo"
              variant="outlined"
              density="compact"
              clearable
              hide-details
            />
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-select
              v-model="statusFilter"
              :items="statusOptions"
              label="Status"
              variant="outlined"
              density="compact"
              clearable
              hide-details
            />
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-btn
              @click="clearFilters"
              variant="outlined"
              color="secondary"
              block
              prepend-icon="mdi-filter-remove"
            >
              Limpar
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Tabela de Investimentos -->
    <v-card elevation="2">
      <v-data-table
        :headers="headers"
        :items="filteredInvestimentos"
        :items-per-page="10"
        class="investment-table"
        hover
      >
        <!-- Nome -->
        <template #item.nome="{ item }">
          <div class="d-flex align-center gap-3 py-2">
            <v-avatar :color="getTypeColor(item.tipo)" size="36">
              <v-icon :icon="getTypeIcon(item.tipo)" color="white" />
            </v-avatar>
            <div>
              <div class="font-weight-bold">{{ item.nome }}</div>
              <div class="text-caption text-medium-emphasis">{{ item.ticker }}</div>
            </div>
          </div>
        </template>

        <!-- Tipo -->
        <template #item.tipo="{ item }">
          <v-chip
            :color="getTypeColor(item.tipo)"
            variant="flat"
            size="small"
          >
            {{ getTypeLabel(item.tipo) }}
          </v-chip>
        </template>

        <!-- Valor Investido -->
        <template #item.valorInvestido="{ item }">
          <span class="font-weight-bold">{{ formatCurrency(item.valorInvestido) }}</span>
        </template>

        <!-- Valor Atual -->
        <template #item.valorAtual="{ item }">
          <span class="font-weight-bold" :class="item.lucro >= 0 ? 'text-success' : 'text-error'">
            {{ formatCurrency(item.valorAtual) }}
          </span>
        </template>

        <!-- Rentabilidade -->
        <template #item.rentabilidade="{ item }">
          <div class="d-flex align-center gap-2">
            <v-icon
              :icon="item.lucro >= 0 ? 'mdi-trending-up' : 'mdi-trending-down'"
              :color="item.lucro >= 0 ? 'success' : 'error'"
              size="18"
            />
            <span class="font-weight-bold" :class="item.lucro >= 0 ? 'text-success' : 'text-error'">
              {{ item.rentabilidade >= 0 ? '+' : '' }}{{ item.rentabilidade.toFixed(2) }}%
            </span>
          </div>
        </template>

        <!-- Lucro/Prejuizo -->
        <template #item.lucro="{ item }">
          <v-chip
            :color="item.lucro >= 0 ? 'success' : 'error'"
            variant="flat"
            size="small"
          >
            {{ item.lucro >= 0 ? '+' : '' }}{{ formatCurrency(item.lucro) }}
          </v-chip>
        </template>

        <!-- Status -->
        <template #item.status="{ item }">
          <v-chip
            :color="getStatusColor(item.status)"
            variant="flat"
            size="small"
          >
            {{ getStatusLabel(item.status) }}
          </v-chip>
        </template>

        <!-- Ações -->
        <template #item.actions="{ item }">
          <div class="d-flex gap-2">
            <v-btn
              icon="mdi-pencil"
              size="x-small"
              variant="text"
              color="primary"
              @click="editInvestimento(item)"
            />
            <v-btn
              icon="mdi-delete"
              size="x-small"
              variant="text"
              color="error"
              @click="deleteInvestimento(item.id)"
            />
          </div>
        </template>
      </v-data-table>
    </v-card>

    <!-- Dialog Editar Investimento -->
    <v-dialog v-model="dialogOpen" max-width="500px">
      <v-card>
        <v-card-title class="d-flex align-center gap-2">
          <v-icon icon="mdi-chart-line" color="success" />
          {{ editingId ? 'Editar Investimento' : 'Novo Investimento' }}
        </v-card-title>

        <v-divider />

        <v-card-text class="pa-6">
          <v-form ref="form" @submit.prevent="saveInvestimento">
            <v-text-field
              v-model="form.nome"
              label="Nome do investimento"
              prepend-icon="mdi-finance"
              variant="outlined"
              density="compact"
              :rules="[v => !!v || 'Campo obrigatório']"
              class="mb-4"
            />

            <v-text-field
              v-model="form.ticker"
              label="Ticker/Código"
              prepend-icon="mdi-barcode"
              variant="outlined"
              density="compact"
              :rules="[v => !!v || 'Campo obrigatório']"
              class="mb-4"
            />

            <v-select
              v-model="form.tipo"
              :items="tiposInvestimento"
              label="Tipo de investimento"
              prepend-icon="mdi-chart-pie"
              variant="outlined"
              density="compact"
              :rules="[v => !!v || 'Campo obrigatório']"
              class="mb-4"
            />

            <v-text-field
              v-model.number="form.valorInvestido"
              label="Valor investido"
              prepend-icon="mdi-cash"
              variant="outlined"
              density="compact"
              type="number"
              step="0.01"
              :rules="[v => !!v || 'Campo obrigatório']"
              class="mb-4"
            />

            <v-text-field
              v-model.number="form.valorAtual"
              label="Valor atual"
              prepend-icon="mdi-cash-multiple"
              variant="outlined"
              density="compact"
              type="number"
              step="0.01"
              :rules="[v => !!v || 'Campo obrigatório']"
              class="mb-4"
            />

            <v-select
              v-model="form.status"
              :items="statusOptions"
              label="Status"
              prepend-icon="mdi-information"
              variant="outlined"
              density="compact"
              :rules="[v => !!v || 'Campo obrigatório']"
              class="mb-4"
            />

            <v-textarea
              v-model="form.observacao"
              label="Observações"
              prepend-icon="mdi-note"
              variant="outlined"
              density="compact"
              rows="3"
              class="mb-4"
            />
          </v-form>
        </v-card-text>

        <v-divider />

        <v-card-actions class="pa-4">
          <v-spacer />
          <v-btn @click="closeDialog" variant="outlined" color="secondary">
            Cancelar
          </v-btn>
          <v-btn @click="saveInvestimento" variant="flat" color="success" :loading="loading">
            {{ editingId ? 'Atualizar' : 'Adicionar' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'

interface Investimento {
  id: number
  nome: string
  ticker: string
  tipo: 'acoes' | 'fii' | 'renda-fixa' | 'cripto' | 'etf'
  valorInvestido: number
  valorAtual: number
  status: 'ativo' | 'pausado' | 'encerrado'
  observacao: string
}

// State
const investimentos = ref<Investimento[]>([
  {
    id: 1,
    nome: 'Petrobras',
    ticker: 'PETR4',
    tipo: 'acoes',
    valorInvestido: 5000,
    valorAtual: 6240,
    status: 'ativo',
    observacao: 'Ação principal'
  },
  {
    id: 2,
    nome: 'Banco Brasil FII',
    ticker: 'BBPO11',
    tipo: 'fii',
    valorInvestido: 8000,
    valorAtual: 8560,
    status: 'ativo',
    observacao: 'Dividendos mensais'
  },
  {
    id: 3,
    nome: 'Tesouro IPCA+',
    ticker: 'IPCA+10Y',
    tipo: 'renda-fixa',
    valorInvestido: 15000,
    valorAtual: 15850,
    status: 'ativo',
    observacao: 'Longo prazo'
  },
  {
    id: 4,
    nome: 'Bitcoin',
    ticker: 'BTC',
    tipo: 'cripto',
    valorInvestido: 10000,
    valorAtual: 9200,
    status: 'ativo',
    observacao: 'Volatilidade alta'
  }
])

const search = ref('')
const typeFilter = ref<string | null>(null)
const statusFilter = ref<string | null>(null)
const dialogOpen = ref(false)
const loading = ref(false)
const editingId = ref<number | null>(null)
const form = ref({
  nome: '',
  ticker: '',
  tipo: 'acoes' as const,
  valorInvestido: 0,
  valorAtual: 0,
  status: 'ativo' as const,
  observacao: ''
})

const tiposInvestimento = [
  { title: 'Ações', value: 'acoes' },
  { title: 'FII', value: 'fii' },
  { title: 'Renda Fixa', value: 'renda-fixa' },
  { title: 'Criptomoedas', value: 'cripto' },
  { title: 'ETF', value: 'etf' }
]

const statusOptions = [
  { title: 'Ativo', value: 'ativo' },
  { title: 'Pausado', value: 'pausado' },
  { title: 'Encerrado', value: 'encerrado' }
]

// Headers
const headers = [
  { title: 'Nome', key: 'nome', align: 'start' },
  { title: 'Tipo', key: 'tipo', align: 'center', width: '120px' },
  { title: 'Investido', key: 'valorInvestido', align: 'end', width: '130px' },
  { title: 'Atual', key: 'valorAtual', align: 'end', width: '130px' },
  { title: 'Rentabilidade', key: 'rentabilidade', align: 'center', width: '140px' },
  { title: 'Lucro/Prejuizo', key: 'lucro', align: 'center', width: '140px' },
  { title: 'Status', key: 'status', align: 'center', width: '110px' },
  { title: 'Ações', key: 'actions', sortable: false, align: 'center', width: '100px' }
] as const

// Computed
const summary = computed(() => {
  const investidosTotal = investimentos.value.reduce((sum, i) => sum + i.valorInvestido, 0)
  const atuaisTotal = investimentos.value.reduce((sum, i) => sum + i.valorAtual, 0)
  const lucroTotal = atuaisTotal - investidosTotal

  return {
    portfolioTotal: atuaisTotal,
    investimentosAtivos: investimentos.value.filter(i => i.status === 'ativo').length,
    totalCategorias: new Set(investimentos.value.map(i => i.tipo)).size,
    rendimentoMensal: lucroTotal / 6, // Média de 6 meses
    rentabilidadeAnual: investidosTotal > 0 ? (lucroTotal / investidosTotal) * 100 : 0,
    diversificacao: 85
  }
})

const filteredInvestimentos = computed(() => {
  return investimentos.value.map(inv => ({
    ...inv,
    lucro: inv.valorAtual - inv.valorInvestido,
    rentabilidade: inv.valorInvestido > 0 ? ((inv.valorAtual - inv.valorInvestido) / inv.valorInvestido) * 100 : 0
  })).filter(investimento => {
    const matchSearch = investimento.nome.toLowerCase().includes(search.value.toLowerCase()) ||
                       investimento.ticker.toLowerCase().includes(search.value.toLowerCase())
    const matchType = !typeFilter.value || investimento.tipo === typeFilter.value
    const matchStatus = !statusFilter.value || investimento.status === statusFilter.value
    return matchSearch && matchType && matchStatus
  })
})

// Methods
function formatCurrency(value: number): string {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value)
}

function getTypeColor(tipo: string): string {
  const colors: Record<string, string> = {
    acoes: 'primary',
    fii: 'info',
    'renda-fixa': 'success',
    cripto: 'warning',
    etf: 'secondary'
  }
  return colors[tipo] || 'secondary'
}

function getTypeIcon(tipo: string): string {
  const icons: Record<string, string> = {
    acoes: 'mdi-chart-line',
    fii: 'mdi-home-city',
    'renda-fixa': 'mdi-bank',
    cripto: 'mdi-bitcoin',
    etf: 'mdi-basket'
  }
  return icons[tipo] || 'mdi-finance'
}

function getTypeLabel(tipo: string): string {
  const labels: Record<string, string> = {
    acoes: 'Ações',
    fii: 'FII',
    'renda-fixa': 'Renda Fixa',
    cripto: 'Cripto',
    etf: 'ETF'
  }
  return labels[tipo] || tipo
}

function getStatusColor(status: string): string {
  const colors: Record<string, string> = {
    ativo: 'success',
    pausado: 'warning',
    encerrado: 'secondary'
  }
  return colors[status] || 'secondary'
}

function getStatusLabel(status: string): string {
  const labels: Record<string, string> = {
    ativo: 'Ativo',
    pausado: 'Pausado',
    encerrado: 'Encerrado'
  }
  return labels[status] || status
}

function editInvestimento(investimento: Investimento): void {
  editingId.value = investimento.id
  form.value = { ...investimento }
  dialogOpen.value = true
}

function saveInvestimento(): void {
  loading.value = true
  setTimeout(() => {
    if (editingId.value) {
      const idx = investimentos.value.findIndex(i => i.id === editingId.value)
      if (idx !== -1) {
        investimentos.value[idx] = { ...investimentos.value[idx], ...form.value }
      }
    } else {
      investimentos.value.push({
        id: Math.max(...investimentos.value.map(i => i.id)) + 1,
        ...form.value
      })
    }
    closeDialog()
    loading.value = false
  }, 500)
}

function deleteInvestimento(id: number): void {
  if (confirm('Tem certeza que deseja deletar este investimento?')) {
    investimentos.value = investimentos.value.filter(i => i.id !== id)
  }
}

function clearFilters(): void {
  search.value = ''
  typeFilter.value = null
  statusFilter.value = null
}

function closeDialog(): void {
  dialogOpen.value = false
  editingId.value = null
}
</script>

<style scoped lang="scss">
.kpi-card {
  transition: all 0.3s ease;
  border-left: 4px solid rgb(var(--v-theme-success));

  &:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
    transform: translateY(-2px);
  }
}

.investment-table {
  :deep(.v-data-table__tr) {
    &:hover {
      background-color: rgba(var(--v-theme-success), 0.05);
    }
  }
}
</style>
