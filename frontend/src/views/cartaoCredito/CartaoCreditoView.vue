<template>
  <div class="cartao-credito-view">
    <!-- Header Section -->
    <div class="view-header mb-4 mb-md-6">
      <div class="d-flex align-center justify-space-between flex-wrap gap-3 mb-2">
        <div class="d-flex align-center gap-2 gap-md-3 flex-grow-1">
          <v-icon icon="mdi-credit-card" :size="$vuetify.display.xs ? 28 : 36" color="error" />
          <div>
            <h1 :class="$vuetify.display.xs ? 'text-h6' : 'text-h5'" class="font-weight-bold">Meus Cartões de Crédito</h1>
            <p class="text-caption text-medium-emphasis mb-0 d-none d-sm-block">Gerencie seus cartões, limites e faturas</p>
          </div>
        </div>
        <v-btn
          color="error"
          :size="$vuetify.display.xs ? 'default' : 'large'"
          :prepend-icon="$vuetify.display.xs ? undefined : 'mdi-plus'"
          :icon="$vuetify.display.xs ? 'mdi-plus' : undefined"
          @click="openAddDialog"
          class="flex-shrink-0"
        >
          <span class="d-none d-sm-inline">Novo Cartão</span>
        </v-btn>
      </div>
    </div>

    <!-- KPI Cards -->
    <v-row class="mb-4 mb-md-6 kpi-row">
      <!-- Card: Total de Cartões -->
      <v-col cols="6" sm="6" lg="3">
        <v-card class="kpi-card pa-3 pa-md-4" elevation="1">
          <div class="d-flex align-center justify-space-between gap-1 gap-md-2">
            <div class="flex-grow-1 min-width-0 overflow-hidden">
              <p class="text-caption text-medium-emphasis mb-1 mb-md-2 text-no-wrap">Total de Cartões</p>
              <h3 :class="$vuetify.display.xs ? 'text-body-1' : 'text-h6'" class="font-weight-bold">{{ cartoes.length }}</h3>
            </div>
            <v-icon icon="mdi-cards-outline" :size="$vuetify.display.xs ? 24 : 40" color="info" class="flex-shrink-0 ml-1" />
          </div>
        </v-card>
      </v-col>

      <!-- Card: Limite Total -->
      <v-col cols="6" sm="6" lg="3">
        <v-card class="kpi-card pa-3 pa-md-4" elevation="1">
          <div class="d-flex align-center justify-space-between gap-1 gap-md-2">
            <div class="flex-grow-1 min-width-0 overflow-hidden">
              <p class="text-caption text-medium-emphasis mb-1 mb-md-2 text-no-wrap">Limite Total</p>
              <h3 :class="$vuetify.display.xs ? 'text-body-1' : 'text-h6'" class="font-weight-bold text-truncate">{{ formatCurrency(summary.limiteTotal) }}</h3>
            </div>
            <v-icon icon="mdi-cash-multiple" :size="$vuetify.display.xs ? 24 : 40" color="primary" class="flex-shrink-0 ml-1" />
          </div>
        </v-card>
      </v-col>

      <!-- Card: Utilizado -->
      <v-col cols="6" sm="6" lg="3">
        <v-card class="kpi-card pa-3 pa-md-4" elevation="1">
          <div class="d-flex align-center justify-space-between gap-1 gap-md-2">
            <div class="flex-grow-1 min-width-0 overflow-hidden">
              <p class="text-caption text-medium-emphasis mb-1 mb-md-2 text-no-wrap">Utilizado</p>
              <h3 :class="$vuetify.display.xs ? 'text-body-1' : 'text-h6'" class="font-weight-bold text-truncate">{{ formatCurrency(summary.utilizado) }}</h3>
              <p class="text-caption text-medium-emphasis mb-0">{{ formatPercentage(summary.percentualUtilizado) }}</p>
            </div>
            <v-icon icon="mdi-percent" :size="$vuetify.display.xs ? 24 : 40" color="warning" class="flex-shrink-0 ml-1" />
          </div>
        </v-card>
      </v-col>

      <!-- Card: Disponível -->
      <v-col cols="6" sm="6" lg="3">
        <v-card class="kpi-card pa-3 pa-md-4" elevation="1" :class="{ 'positive': summary.disponivel >= 0, 'negative': summary.disponivel < 0 }">
          <div class="d-flex align-center justify-space-between gap-1 gap-md-2">
            <div class="flex-grow-1 min-width-0 overflow-hidden">
              <p class="text-caption text-medium-emphasis mb-1 mb-md-2 text-no-wrap">Disponível</p>
              <h3 :class="$vuetify.display.xs ? 'text-body-1' : 'text-h6'" class="font-weight-bold text-truncate">{{ formatCurrency(summary.disponivel) }}</h3>
            </div>
            <v-icon icon="mdi-check-circle-outline" :size="$vuetify.display.xs ? 24 : 40" :color="summary.disponivel >= 0 ? 'success' : 'error'" class="flex-shrink-0 ml-1" />
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Filtros -->
    <v-card class="filters-card mb-4 mb-md-6 pa-3 pa-md-4" elevation="0" variant="outlined">
      <div class="d-flex flex-column flex-sm-row flex-wrap gap-2 align-stretch align-sm-center">
        <v-text-field
          v-model="search"
          label="Buscar cartões..."
          prepend-inner-icon="mdi-magnify"
          clearable
          density="compact"
          class="flex-grow-1 flex-shrink-1"
          style="min-width: 0"
          hide-details
        />
        <v-select
          v-model="bandueiraFilter"
          :items="bandeirasPossivel"
          label="Bandeira"
          clearable
          density="compact"
          class="filter-select"
          hide-details
        />
        <v-select
          v-model="statusFilter"
          :items="['ativo', 'inativo', 'bloqueado']"
          label="Status"
          clearable
          density="compact"
          class="filter-select"
          hide-details
        />
        <v-btn
          variant="outlined"
          @click="clearFilters"
          :prepend-icon="$vuetify.display.xs ? undefined : 'mdi-close-circle-outline'"
          :icon="$vuetify.display.xs ? 'mdi-close-circle-outline' : undefined"
          :size="$vuetify.display.xs ? 'small' : 'default'"
          class="flex-shrink-0"
        >
          <span class="d-none d-sm-inline">Limpar</span>
        </v-btn>
      </div>
    </v-card>

    <!-- Tabela de Cartões -->
    <v-card class="mb-4 mb-md-6 table-container" elevation="1">
      <div class="table-wrapper">
        <v-data-table
          :items="filteredCartoes"
          :headers="headers"
          :loading="loading"
          class="cartoes-table"
          density="comfortable"
          :mobile-breakpoint="$vuetify.display.xs ? 9999 : 0"
        >
        <template #item.name="{ item }">
          <div class="d-flex align-center gap-2">
            <v-icon icon="mdi-credit-card" color="error" :size="$vuetify.display.xs ? 24 : 32" />
            <div class="min-width-0">
              <div class="font-weight-bold text-truncate">{{ item.name }}</div>
              <div class="text-caption text-medium-emphasis text-truncate d-none d-sm-block">{{ item.descricao || 'Cartão de Crédito' }}</div>
            </div>
          </div>
        </template>

        <template #item.tipo_conta="{ item }">
          <v-chip
            :color="getBandeiraColor(item.tipo_conta)"
            label
            size="small"
          >
            {{ item.tipo_conta }}
          </v-chip>
        </template>

        <template #item.valor_em_aberto="{ item }">
          <div class="d-flex flex-column align-center">
            <span :class="$vuetify.display.xs ? 'text-caption' : 'text-body-2'" class="font-weight-bold">{{ formatCurrency(item.valor_em_aberto || 0) }}</span>
            <v-progress-linear
              :value="((item.valor_em_aberto || 0) / (item.limite || 1)) * 100"
              :color="getUtilizacaoColor(item.valor_em_aberto || 0, item.limite || 1)"
              class="mt-1"
              height="6"
              :style="`width: ${$vuetify.display.xs ? '80px' : '100px'}`"
            />
          </div>
        </template>

        <template #item.limite="{ item }">
          <div class="text-right font-weight-bold" :class="$vuetify.display.xs ? 'text-caption' : 'text-body-2'">
            {{ formatCurrency(item.limite || 0) }}
          </div>
        </template>

        <template #item.data_vencimento="{ item }">
          <div class="text-center">
            <div class="font-weight-bold" :class="$vuetify.display.xs ? 'text-caption' : 'text-body-2'">{{ formatDate(item.data_vencimento || '') }}</div>
            <div class="text-caption text-medium-emphasis d-none d-sm-block">
              {{ getDiasRestantes(item.data_vencimento || '') }}
            </div>
          </div>
        </template>

        <template #item.status_fatura="{ item }">
          <v-chip
            :color="getStatusColor(item.status_fatura || 'INEXISTENTE')"
            label
            size="small"
          >
            {{ getStatusLabel(item.status_fatura || 'INEXISTENTE') }}
          </v-chip>
        </template>

        <template #item.disponivel="{ item }">
          <div class="text-right font-weight-bold" :class="[
            $vuetify.display.xs ? 'text-caption' : 'text-body-2',
            item.disponivel >= 0 ? 'text-success' : 'text-error'
          ]">
            {{ formatCurrency(item.disponivel || 0) }}
          </div>
        </template>

        <template #item.actions="{ item }">
          <div class="d-flex gap-1 justify-center">
            <v-btn
              icon="mdi-pencil"
              variant="text"
              :size="$vuetify.display.xs ? 'x-small' : 'small'"
              @click="editCartao(item)"
            />
            <v-btn
              icon="mdi-delete"
              variant="text"
              :size="$vuetify.display.xs ? 'x-small' : 'small'"
              color="error"
              @click="deleteCartao(item.id)"
            />
          </div>
        </template>
      </v-data-table>
      </div>
    </v-card>

    <!-- Form Cartão Component -->
    
    <!-- Form Cartão Dialog -->
    <v-dialog
      v-model="dialogOpen"
      :max-width="$vuetify.display.xs ? '95vw' : '600px'"
      persistent
      :fullscreen="$vuetify.display.xs"
    >
      <v-card class="dialog-card">
        <v-card-title class="pa-4 pa-md-6 pb-3 pb-md-4 d-flex align-center justify-space-between">
          <span :class="$vuetify.display.xs ? 'text-h6' : 'text-h5'">{{ editingId ? 'Editar Cartão' : 'Novo Cartão' }}</span>
          <v-btn
            v-if="$vuetify.display.xs"
            icon="mdi-close"
            variant="text"
            size="small"
            @click="closeDialog"
          />
        </v-card-title>

        <v-card-text class="pa-4 pa-md-6 pt-3 pt-md-4 card-text-container">
          <v-form
            ref="formRef"
            @submit.prevent="saveCartao"
          >
            <!-- Nome do Cartão com ícone da conta -->
            <v-text-field
              v-model="editingData.name"
              label="Apelido do Cartão"
              variant="outlined"
              :rules="[rules.required]"
              prepend-inner-icon="mdi-text"
            >
              <template #append-inner>
                <!-- Ícone da conta vinculada (apenas exibição) -->
                <div v-if="contaPaiSelecionada" class="d-flex align-center">
                  <v-icon :icon="getBankIcon(contaPaiSelecionada.icon || '')" size="24" />
                </div>
              </template>
            </v-text-field>

            <!-- Conta Vinculada -->
            <v-select
              v-model="editingData.conta_pai_id"
              :items="contas"
              item-title="name"
              item-value="id"
              label="Conta Vinculada"
              variant="outlined"
              :rules="[rules.required]"
              prepend-inner-icon="mdi-bank"
            >
              <template #selection="{ item }">
                <div class="d-flex align-center text-truncate gap-2">
                  <v-icon
                    v-if="item.raw.icon"
                    :icon="getBankIcon(item.raw.icon)"
                    size="20"
                  />
                  <span class="text-truncate">{{ item?.raw?.name ?? 'Nenhuma' }}</span>
                </div>
              </template>
              <template #item="{ props: itemProps, item }">
                <v-list-item
                  v-bind="itemProps"
                  :title="item.raw.name"
                >
                  <template #prepend>
                    <v-icon
                      v-if="item.raw.icon"
                      :icon="getBankIcon(item.raw.icon)"
                    />
                  </template>
                </v-list-item>
              </template>
            </v-select>

            <!-- Limite -->
            <v-text-field
              v-model="editingData.limite"
              label="Limite do Cartão"
              variant="outlined"
              type="tel"
              :rules="[rules.required]"
              prepend-inner-icon="mdi-currency-brl"
              @update:model-value="editingData.limite = formatCurrencyInput($event)"
            />

            <!-- Bandeira -->
            <v-select
              v-model="editingData.icon"
              :items="['Visa', 'Mastercard', 'ELO', 'American Express']"
              label="Bandeira"
              variant="outlined"
              :rules="[rules.required]"
              prepend-inner-icon="mdi-credit-card-outline"
            />

            <!-- Descrição -->
            <v-text-field
              v-model="editingData.descricao"
              label="Descrição"
              variant="outlined"
              prepend-inner-icon="mdi-text-box-outline"
            />

            <!-- Dia Fechamento -->
            <v-menu
              v-model="menuFechamento"
              :close-on-content-click="false"
              location="center"
              offset="10"
            >
              <template #activator="{ props }">
                <v-text-field
                  v-model="editingData.dia_fechamento"
                  label="Dia do Fechamento"
                  variant="outlined"
                  readonly
                  :rules="[rules.required]"
                  prepend-inner-icon="mdi-calendar-remove-outline"
                  v-bind="props"
                />
              </template>
              <v-card class="date-picker-card elevation-8">
                <v-card-title class="p-1 text-center bg-primary text-white" style="font-size:12px">
                  Dia do Fechamento
                </v-card-title>
                <v-card-text class="pa-1">
                  <div class="date-grid">
                    <v-btn
                      v-for="dia in diasDoMes"
                      :key="dia"
                      :active="editingData.dia_fechamento === dia"
                      :variant="editingData.dia_fechamento === dia ? 'flat' : 'outlined'"
                      :color="editingData.dia_fechamento === dia ? 'error' : 'default'"
                      class="date-btn"
                      @click="editingData.dia_fechamento = dia; menuFechamento = false"
                    >
                      {{ String(dia).padStart(2, '0') }}
                    </v-btn>
                  </div>
                </v-card-text>
              </v-card>
            </v-menu>

            <!-- Dia Vencimento -->
            <v-menu
              v-model="menuVencimento"
              :close-on-content-click="false"
              location="center"
              offset="10"
            >
              <template #activator="{ props }">
                <v-text-field
                  v-model="editingData.dia_vencimento"
                  label="Dia do Vencimento"
                  variant="outlined"
                  readonly
                  :rules="[rules.required]"
                  prepend-inner-icon="mdi-calendar-today-outline"
                  v-bind="props"
                />
              </template>
              <v-card class="date-picker-card elevation-8">
                <v-card-title class="pa-1 text-center bg-primary text-white" style="font-size:12px">
                  Dia do Vencimento
                </v-card-title>
                <v-card-text class="pa-1">
                  <div class="date-grid">
                    <v-btn
                      v-for="dia in diasDoMes"
                      :key="dia"
                      :active="editingData.dia_vencimento === dia"
                      :variant="editingData.dia_vencimento === dia ? 'flat' : 'outlined'"
                      :color="editingData.dia_vencimento === dia ? 'error' : 'default'"
                      class="date-btn"
                      @click="editingData.dia_vencimento = dia; menuVencimento = false"
                    >
                      {{ String(dia).padStart(2, '0') }}
                    </v-btn>
                  </div>
                </v-card-text>
              </v-card>
            </v-menu>
          </v-form>
        </v-card-text>

        <v-card-actions class="pa-4 pa-md-6 pt-0">
          <v-spacer />
          <v-btn
            variant="outlined"
            @click="closeDialog"
            :size="$vuetify.display.xs ? 'default' : 'large'"
            class="flex-grow-1 flex-sm-grow-0"
          >
            Cancelar
          </v-btn>
          <v-btn
            type="submit"
            :disabled="!editingData.name || !editingData.conta_pai_id || loading"
            :loading="loading"
            @click="saveCartao"
            :size="$vuetify.display.xs ? 'default' : 'large'"
            class="flex-grow-1 flex-sm-grow-0"
          >
            Salvar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Loading Overlay - Carregamento Inicial -->
    <v-overlay
      v-model="loadingMonth"
      class="align-center justify-center"
      persistent
      contained
    >
      <div class="text-center">
        <v-progress-circular
          indeterminate
          size="64"
          width="5"
          color="primary"
          class="mb-4"
        />
        <div class="text-subtitle-1 text-white mb-1">
          Carregando cartões...
        </div>
        <div class="text-caption text-white-50">
          Preparando seus dados
        </div>
      </div>
    </v-overlay>

    <!-- Loading Overlay - Carregamento do Formulário -->
    <v-overlay
      v-model="loadingForm"
      class="align-center justify-center"
      persistent
      :z-index="9999"
    >
      <div class="text-center">
        <v-progress-circular
          indeterminate
          size="64"
          width="5"
          color="primary"
          class="mb-4"
        />
        <div class="text-subtitle-1 text-white mb-1">
          Carregando formulário...
        </div>
        <div class="text-caption text-white-50">
          Preparando dados
        </div>
      </div>
    </v-overlay>
  </div>
</template>

<script setup lang="ts">
import cartaoCreditoService from '@/services/cartaoCredito.service'
import contasService from '@/services/contas.service'
import { useToastStore } from '@/store/toast'
import { getBankIcon } from '@/utils/iconMapper'
import { computed, onMounted, ref, watch } from 'vue'

const toastStore = useToastStore()

interface Conta {
  id: number
  name: string
  icon?: string
  color?: string
  tipo_conta?: string
}

interface Cartao {
  id: number
  name: string
  icon?: string
  color?: string
  tipo_conta: string
  limite?: string
  saldo?: string
  descricao?: string
  dia_fechamento?: number
  dia_vencimento?: number
  conta_pai_id?: number | null
  conta_pai_name?: string | null
  total_fatura_vigente?: number
  valor_em_aberto?: number
  data_fechamento?: string
  data_vencimento?: string
  status_fatura?: string
  lancamentos_fatura_vigente?: any[]
}

// State
const cartoes = ref<Cartao[]>([])
const contas = ref<Conta[]>([])
const search = ref('')
const bandueiraFilter = ref('')
const statusFilter = ref('')
const dialogOpen = ref(false)
const loading = ref(false)
const loadingMonth = ref(false)
const loadingForm = ref(false)
const editingId = ref<number | null>(null)
const editingData = ref<Partial<Cartao>>({
  name: '',
  tipo_conta: 'Cartão de Crédito',
  icon: 'Visa',
  limite: '0,00',
  dia_fechamento: 10,
  dia_vencimento: 20,
  descricao: '',
  color: '#e53935',
  conta_pai_id: null,
})

const headers = [
  { title: 'Cartão', key: 'name', align: 'start' as const },
  { title: 'Limite', key: 'limite', align: 'end' as const, width: '130px' },
  { title: 'Utilizado', key: 'valor_em_aberto', align: 'center' as const, width: '140px' },
  { title: 'Disponível', key: 'disponivel', align: 'end' as const, width: '130px' },
  { title: 'Vencimento', key: 'data_vencimento', align: 'center' as const, width: '140px' },
  { title: 'Status', key: 'status_fatura', align: 'center' as const, width: '100px' },
  { title: 'Ações', key: 'actions', sortable: false, align: 'center' as const, width: '100px' }
]

const bandeirasPossivel = ['Visa', 'Mastercard', 'ELO', 'American Express', 'Hipercard', 'Diners']

// Helpers para obter mês atual
const getCurrentMonth = (): string => {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, "0");
  return `${year}-${month}`;
};

const currentMonth = ref<string>(getCurrentMonth())

// Refs para form
const formRef = ref()
const menuColor = ref(false)
const menuFechamento = ref(false)
const menuVencimento = ref(false)
const menuContaPai = ref(false)

// Dias do mês para selecionar
const diasDoMes = computed(() => Array.from({ length: 30 }, (_, i) => i + 1))

// Conta pai vinculada
const contaPaiSelecionada = computed(() => {
  if (!editingData.value.conta_pai_id) return null
  return contas.value.find(c => c.id === editingData.value.conta_pai_id)
})

// Cor da conta pai (para usar como cor padrão do cartão)
const corContaPai = computed(() => {
  return contaPaiSelecionada.value?.color || '#e53935'
})

// Regras de validação
const rules = {
  required: (v: any) => !!v || 'Campo obrigatório',
}

// Computed
const summary = computed(() => {
  const limiteTotal = cartoes.value.reduce((sum, c) => sum + (c.limite || 0), 0)
  const utilizado = cartoes.value.reduce((sum, c) => sum + (c.valor_em_aberto || 0), 0)
  const disponivel = limiteTotal - utilizado
  return {
    limiteTotal,
    utilizado,
    disponivel,
    percentualUtilizado: limiteTotal > 0 ? (utilizado / limiteTotal) : 0
  }
})

const filteredCartoes = computed(() => {
  return cartoes.value.map(cartao => ({
    ...cartao,
    disponivel: (cartao.limite || 0) - (cartao.valor_em_aberto || 0)
  })).filter(cartao => {
    const matchSearch = cartao.name.toLowerCase().includes(search.value.toLowerCase()) ||
                       cartao.descricao?.toLowerCase().includes(search.value.toLowerCase())
    const matchBandeiraira = !bandueiraFilter.value || cartao.tipo_conta === bandueiraFilter.value
    const matchStatus = !statusFilter.value || cartao.status_fatura === statusFilter.value
    return matchSearch && matchBandeiraira && matchStatus
  })
})

// Methods
const formatCurrencyInput = (value: string): string => {
  if (!value) return "0,00";
  let digits = value.replace(/\D/g, "");
  digits = digits.replace(/^0+/, "") || "0";
  while (digits.length < 3) digits = "0" + digits;
  const integerPart = digits.slice(0, -2);
  const decimalPart = digits.slice(-2);
  const formattedIntegerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  return `${formattedIntegerPart},${decimalPart}`;
};

function formatCurrency(value: number): string {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value / 100)
}

function formatPercentage(value: number): string {
  return `${(value * 100).toFixed(1)}%`
}

function formatDate(date: string): string {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('pt-BR')
}

function getDiasRestantes(data: string): string {
  if (!data) return '-'
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
    'PAGA': 'success',
    'PENDENTE': 'warning',
    'ATRASADA': 'error',
    'INEXISTENTE': 'secondary'
  }
  return colors[status] || 'default'
}

function getStatusLabel(status: string): string {
  const labels: Record<string, string> = {
    'PAGA': 'Paga',
    'PENDENTE': 'Pendente',
    'ATRASADA': 'Atrasada',
    'INEXISTENTE': 'Sem fatura'
  }
  return labels[status] || status
}

const loadCartoes = async () => {
  try {
    loading.value = true
    const mesAno = currentMonth.value;
    const data = await cartaoCreditoService.list(mesAno)

    console.log('Cartões carregados:', data);
    cartoes.value = data;
  } catch (error: any) {
    console.error('Erro ao carregar cartões:', error)
    toastStore.error('Erro ao carregar cartões')
  } finally {
    loading.value = false
    loadingMonth.value = false
  }
}

const loadContas = async () => {
  try {
    const mesAno = currentMonth.value;
    const data = await contasService.list(mesAno)
    
    
    // Filtrar apenas contas (corrente, poupança, investimento), não cartões de crédito
    const contasFiltradas = data.filter(c => {
      const tipo = c.tipo_conta?.toLowerCase() || '';
      const isNotCreditCard = !tipo.includes('crédito') && !tipo.includes('credit');
      console.log(`Conta: ${c.name} | Tipo: "${c.tipo_conta}" | Include: ${isNotCreditCard}`);
      return isNotCreditCard;
    });
    
    contas.value = contasFiltradas;
    console.log('Contas filtradas (final):', contas.value);
    console.log('=== FIM DEBUG ===');
  } catch (error: any) {
    console.error('Erro ao carregar contas:', error)
    toastStore.error('Erro ao carregar contas')
  }
}

function openAddDialog() {
  loadingForm.value = true
  editingId.value = null
  editingData.value = {
    name: '',
    tipo_conta: 'Cartão de Crédito',
    icon: 'Visa',
    limite: "0,00",
    dia_fechamento: 10,
    dia_vencimento: 20,
    descricao: '',
    color: '#e53935',
    conta_pai_id: null,
  }
  setTimeout(() => {
    dialogOpen.value = true
    loadingForm.value = false
  }, 300)
}

function closeDialog() {
  dialogOpen.value = false;
  editingId.value = null;
  editingData.value = {
    name: '',
    tipo_conta: 'Cartão de Crédito',
    icon: 'Visa',
    limite: "0,00",
    dia_fechamento: 10,
    dia_vencimento: 20,
    descricao: '',
    color: '#e53935',
    conta_pai_id: null,
  };
}

function editCartao(cartao: Cartao) {
  editingId.value = cartao.id;
  
  // Converter limite de centavos para string formatada
  const limiteFormatado = typeof cartao.limite === 'number'
    ? (cartao.limite / 100).toFixed(2).replace('.', ',')
    : cartao.limite;
  
  editingData.value = {
    ...cartao,
    limite: limiteFormatado
  };
  
  dialogOpen.value = true;
}

async function saveCartao() {
  try {
    // Validar formulário
    if (!formRef.value?.validate || !(await formRef.value.validate()).valid) {
      toastStore.error('Preencha todos os campos obrigatórios');
      return;
    }

    if (!editingData.value.name) {
      toastStore.error('Nome do cartão é obrigatório');
      return;
    }

    if (!editingData.value.conta_pai_id) {
      toastStore.error('Selecione uma conta vinculada');
      return;
    }

    loading.value = true;

    // O backend espera receber o limite como string formatada (ex: "9.000,00")
    // porque ele tem um transformMonetaryValue que converte para centavos
    // Então enviamos o valor já formatado do input
    const limiteString = editingData.value.limite?.toString() || "0,00";
    console.log('🔍 DEBUG - Limite String para enviar:', limiteString);

    // Preparar payload
    const payload = {
      name: editingData.value.name,
      icon: editingData.value.icon || 'Visa',
      tipo_conta: 'Cartão de Crédito',
      limite: limiteString, // Envia como string formatada
      descricao: editingData.value.descricao || '',
      dia_fechamento: editingData.value.dia_fechamento || 1,
      dia_vencimento: editingData.value.dia_vencimento || 10,
      color: corContaPai.value || '#e53935',
      conta_pai_id: editingData.value.conta_pai_id,
    };

    console.log('📤 Payload enviado ao backend:', payload);

    if (editingId.value) {
      // Atualizar cartão existente
      await cartaoCreditoService.update(editingId.value, payload);
      toastStore.success('Cartão atualizado com sucesso!');
    } else {
      // Criar novo cartão
      await cartaoCreditoService.create(payload);
      toastStore.success('Cartão criado com sucesso!');
    }

    // Recarregar lista de cartões
    await loadCartoes();
    closeDialog();
  } catch (error: any) {
    console.error('Erro ao salvar cartão:', error);
    toastStore.error(error.message || 'Erro ao salvar cartão');
  } finally {
    loading.value = false;
  }
}

async function deleteCartao(id: number) {
  if (confirm('Tem certeza que deseja deletar este cartão?')) {
    try {
      loading.value = true;
      await cartaoCreditoService.delete(id);
      toastStore.success('Cartão deletado com sucesso!');
      await loadCartoes();
    } catch (error: any) {
      console.error('Erro ao deletar cartão:', error);
      toastStore.error(error.message || 'Erro ao deletar cartão');
    } finally {
      loading.value = false;
    }
  }
}

function clearFilters() {
  search.value = ''
  bandueiraFilter.value = ''
  statusFilter.value = ''
}

// Lifecycle
onMounted(() => {
  currentMonth.value = getCurrentMonth();
  loadingMonth.value = true;
  loadContas();
  loadCartoes()
})

watch(() => currentMonth.value, () => {
  loadContas();
  loadCartoes();
}, { immediate: true });
</script>

<style scoped lang="scss">
// Prevenir overflow horizontal global
html, body {
  overflow-x: hidden;
  max-width: 100vw;
}

.cartao-credito-view {
  padding: 16px;
  width: 100%;
  max-width: 100%;
  overflow-x: hidden;
  box-sizing: border-box;

  @media (min-width: 600px) {
    padding: 20px;
  }

  @media (min-width: 960px) {
    padding: 24px;
  }

  // Garantir que todos os elementos filhos respeitem a largura
  * {
    box-sizing: border-box;
  }

  // Prevenir overflow em v-row
  :deep(.v-row) {
    margin-left: 0 !important;
    margin-right: 0 !important;
    max-width: 100%;
  }

  // Prevenir overflow em v-col
  :deep(.v-col) {
    padding-left: 6px !important;
    padding-right: 6px !important;

    @media (min-width: 600px) {
      padding-left: 12px !important;
      padding-right: 12px !important;
    }
  }

  // Garantir que cards dentro das colunas não ultrapassem
  :deep(.v-card) {
    max-width: 100%;
    width: 100%;
  }

  // Prevenir gap excessivo em telas pequenas
  .kpi-row {
    @media (max-width: 599px) {
      margin-left: -6px !important;
      margin-right: -6px !important;
    }
  }
}

.view-header {
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
  padding-bottom: 12px;
  width: 100%;
  overflow: hidden;

  @media (min-width: 600px) {
    padding-bottom: 16px;
  }

  h1 {
    word-break: break-word;
    overflow-wrap: break-word;
  }
}

.kpi-card {
  border-left: 4px solid rgb(var(--v-theme-error));
  transition: all 0.3s ease;
  min-height: 80px;
  width: 100%;
  overflow: hidden;

  @media (min-width: 600px) {
    min-height: 100px;
  }

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

  .min-width-0 {
    min-width: 0;
  }

  .overflow-hidden {
    overflow: hidden;
  }

  .flex-shrink-0 {
    flex-shrink: 0;
  }

  .text-no-wrap {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
}

.filters-card {
  background: rgba(var(--v-theme-error), 0.05);
  width: 100%;
  overflow: hidden;

  .d-flex {
    width: 100%;
  }

  .filter-select {
    min-width: 0;
    flex: 1 1 auto;

    @media (min-width: 600px) {
      flex: 0 0 auto;
      min-width: 150px;
      max-width: 200px;
    }
  }

  @media (max-width: 599px) {
    .v-text-field,
    .v-select {
      width: 100%;
      min-width: 100% !important;
    }
  }
}

.cartoes-table {
  width: 100%;
  
  :deep(.v-data-table) {
    background: rgb(var(--v-theme-background));
    width: 100%;
  }

  :deep(.v-data-table__td) {
    padding: 8px 4px !important;
    word-break: break-word;

    @media (min-width: 600px) {
      padding: 12px 8px !important;
    }
  }

  :deep(.v-data-table__th) {
    padding: 8px 4px !important;
    font-size: 0.75rem !important;

    @media (min-width: 600px) {
      padding: 12px 8px !important;
      font-size: 0.875rem !important;
    }
  }

  .min-width-0 {
    min-width: 0;
  }
}

.table-container {
  width: 100%;
  overflow: hidden;
}

.table-wrapper {
  width: 100%;
  overflow-x: auto;
  overflow-y: hidden;

  @media (max-width: 959px) {
    -webkit-overflow-scrolling: touch;
  }
}

.color-preview {
  width: 30px;
  height: 30px;
  border-radius: 4px;
  border: 2px solid rgba(0, 0, 0, 0.2);
  cursor: pointer;
  transition: all 0.2s ease;
}

.color-preview:hover {
  transform: scale(1.1);
  border-color: rgba(0, 0, 0, 0.4);
}

/* Estilos para form fields - removido classes de modal fullscreen */

.color-input-activator {
  width: 30px;
  height: 30px;
  border-radius: 4px;
  border: 2px solid rgba(0, 0, 0, 0.2);
  cursor: pointer;
  transition: all 0.2s ease;
}

.color-input-activator:hover {
  transform: scale(1.1);
  border-color: rgba(0, 0, 0, 0.4);
}

/* Forçar tamanho do dialog */
:deep(.v-dialog__content) {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}

:deep(.v-overlay) {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}

/* Card do dialog - tamanho fixo */
.dialog-card {
  width: 100% !important;
  max-width: 100% !important;

  @media (min-width: 600px) {
    width: 600px !important;
    max-width: 600px !important;
    min-width: 600px !important;
  }

  // Prevenir overflow nos campos do formulário
  :deep(.v-input) {
    width: 100%;
    max-width: 100%;
  }

  :deep(.v-field) {
    width: 100%;
    max-width: 100%;
  }

  :deep(.v-text-field),
  :deep(.v-select) {
    width: 100%;
    max-width: 100%;
  }
}

/* Container do card-text para contexto de posicionamento */
.card-text-container {
  position: relative !important;
  max-height: calc(100vh - 200px);
  overflow-y: auto;

  @media (min-width: 600px) {
    max-height: none;
    overflow-y: visible;
  }
}

/* Estilos para Date Picker */
.date-picker-card {
  min-width: 160px !important;
  max-width: 160px !important;
  width: 160px !important;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15) !important;
  border-radius: 12px;

  @media (max-width: 599px) {
    min-width: 280px !important;
    max-width: 280px !important;
    width: 280px !important;
  }
}

.date-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 3px;
  padding: 0;

  @media (max-width: 599px) {
    grid-template-columns: repeat(6, 1fr);
    gap: 6px;
  }
}

.date-btn {
  width: 20px;
  height: 20px;
  border-radius: 4px;
  font-weight: 600;
  font-size: 10px;
  letter-spacing: 0.1px;
  padding: 0 !important;
  min-width: 20px !important;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);

  @media (max-width: 599px) {
    width: 36px;
    height: 36px;
    font-size: 14px;
    min-width: 36px !important;
  }
  
  &:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  &.v-btn--active {
    box-shadow: 0 4px 16px rgba(229, 57, 53, 0.3);
    transform: scale(1.08);
  }
}
</style>
