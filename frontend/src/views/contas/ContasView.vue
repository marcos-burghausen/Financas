<template>
  <div class="contas-view">
    <!-- Header Section -->
    <div class="view-header mb-6">
      <div class="d-flex align-center justify-space-between gap-3 mb-2">
        <div class="d-flex align-center gap-3">
          <v-icon icon="mdi-bank" size="36" color="primary" />
          <div>
            <h1 class="text-h5 font-weight-bold">Minhas Contas</h1>
            <p class="text-caption text-medium-emphasis mb-0">Gerencie suas contas correntes, poupanças e investimentos</p>
          </div>
        </div>
        <v-btn
          color="primary"
          size="large"
          prepend-icon="mdi-plus"
          @click="openDialog"
        >
          Nova Conta
        </v-btn>
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

    <!-- <v-row class="mb-6">
      <v-col
        v-for="conta in filteredContas"
        :key="conta.id"
        cols="12"
        sm="6"
        lg="4"
      >
        <v-card
          class="account-card"
          :class="getBankClass(conta.bank)"
          elevation="4"
          dark
        >
          <div class="card-pattern"></div>

          <v-card-text class="d-flex flex-column justify-space-between fill-height card-content">
            
            <div class="d-flex justify-space-between align-center">
              <span class="text-body-1 font-weight-bold">{{ conta.bank }}</span>
              
              <div class="card-network-logo">
                <div class="circle red"></div>
                <div class="circle yellow"></div>
              </div>
            </div>

            <div>
              <div class="card-chip"></div>
              
              <p class="text-caption text-white mt-3 mb-1">{{ conta.name }}</p>
            </div>

            <div>
              <p class="text-caption text-medium-emphasis mb-0">Saldo Atual</p>
              <h2 class="text-h5 font-weight-bold">
                {{ formatCurrency(conta.balance) }}
              </h2>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row class="mb-6">
      <v-col
        v-for="conta in contas"
        :key="conta.id"
        cols="12"
        sm="6"
        lg="4"
      >
        <v-card
          class="account-card"
          :class="getBankClass(conta.bank)"
          elevation="4"
          dark
        >
          <v-card-text class="d-flex flex-column justify-space-between fill-height">
            <div>
              <div class="d-flex justify-space-between align-center mb-4">
                <span class="text-body-1 font-weight-bold">{{ conta.bank }}</span>
                <v-chip
                  label
                  small
                  dark
                  color="rgba(255, 255, 255, 0.2)"
                >
                  {{ getTipoLabel(conta.type) }}
                </v-chip>
              </div>
              <p class="text-caption text-white mb-1">{{ conta.name }}</p>
            </div>

            <div>
              <p class="text-caption text-medium-emphasis mb-0">Saldo Atual</p>
              <h2 class="text-h5 font-weight-bold">
                {{ formatCurrency(conta.balance) }}
              </h2>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row> -->

    <!-- Tabela de Contas -->
    <v-card class="mb-6" elevation="1">
      <v-data-table
        :items="contas"
        :headers="headers"
        :loading="loading"
        class="contas-table"
        density="comfortable"
      >
        <template #item.name="{ item }">
          <div class="d-flex align-center gap-2">
            <v-avatar 
              size="32" 
              :style="{ backgroundColor: item.color || '#163dc0' }" 
              text-color="white"
            >
              {{ item.name.charAt(0).toUpperCase() }}
            </v-avatar>
            <div>
              <div class="font-weight-bold">{{ item.name }}</div>
              <div class="text-caption text-medium-emphasis">{{ item.bank }}</div>
            </div>
          </div>
        </template>

        <template #item.type="{ item }">
          <v-chip
            :color="getTipoColor(item.type)"
            label
            size="small"
          >
            {{ getTipoLabel(item.type) }}
          </v-chip>
        </template>

        <template #item.balance="{ item }">
          <div class="text-right font-weight-bold" :class="item.balance >= 0 ? 'text-success' : 'text-error'">
            {{ formatCurrency(item.balance) }}
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

    <!-- Form Component -->
    <FormConta
      v-model="dialogOpen"
      :editing-data="editingData"
      @saved="loadContas"
    />

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
          Carregando contas...
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
import FormConta from '@/components/FormConta.vue'
import contasService from '@/services/contas.service'
import { useToastStore } from '@/store/toast'
import { computed, onMounted, ref, watch } from 'vue'

const toastStore = useToastStore()

interface Conta {
  id: number
  name: string
  bank: string
  type: 'corrente' | 'poupanca' | 'investimento'
  number: string
  agency: string
  balance: number
  status: 'ativa' | 'inativa'
  description?: string
  limit?: number
  opening_date?: string
  color?: string
}

// State
const contas = ref([])
const search = ref('')
const tipoFilter = ref('')
const statusFilter = ref('')
const dialogOpen = ref(false)
const loading = ref(false)
const loadingMonth = ref(false)
const loadingForm = ref(false)
const editingId = ref<number | null>(null)
const editingData = ref<Conta | null>(null)

const headers = [
  { title: 'Conta', key: 'name', align: 'start' as const },
  { title: 'Tipo', key: 'type', align: 'center' as const, width: '120px' },
  { title: 'Saldo', key: 'balance', align: 'end' as const, width: '150px' },
  { title: 'Status', key: 'status', align: 'center' as const, width: '100px' },
  { title: 'Ações', key: 'actions', sortable: false, align: 'center' as const, width: '100px' }
]

const tiposContaPossivel = ['corrente', 'poupança', 'investimento']

// Computed
const contasAtivas = computed(() => contas.value.filter(c => c.status === 'ativa'))

const getCurrentMonth = (): string => {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, "0");
  return `${year}-${month}`;
};

const currentMonth = ref<string>(getCurrentMonth());

const summary = computed(() => ({
  totalBalance: contas.value.reduce((sum, c) => sum + c.balance, 0),
  contasAtivas: contasAtivas.value.length,
  limiteDisponivel: contas.value.reduce((sum, c) => sum + (c.limit || 0), 0)
}))

// const filteredContas = computed(() => {
//   return contas.value.filter(conta => {
//     const matchSearch = conta.name.toLowerCase().includes(search.value.toLowerCase()) ||
//                        conta.bank.toLowerCase().includes(search.value.toLowerCase())
//     const matchTipo = !tipoFilter.value || conta.type === tipoFilter.value
//     const matchStatus = !statusFilter.value || conta.status === statusFilter.value
//     return matchSearch && matchTipo && matchStatus
//   })
// })

// Methods
const getBankClass = (bankName: string): string => {
  const normalizedName = bankName.toLowerCase()
  
  if (normalizedName.includes('nubank')) return 'bg-nubank'
  if (normalizedName.includes('inter')) return 'bg-inter'
  if (normalizedName.includes('itaú')) return 'bg-itau'
  if (normalizedName.includes('bradesco')) return 'bg-bradesco'
  if (normalizedName.includes('brasil')) return 'bg-bb'
  if (normalizedName.includes('caixa')) return 'bg-caixa'
  
  return 'bg-default-grad'
}




const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value / 100)
}

const getTipoColor = (tipo: string): string => {
  const colors: Record<string, string> = {
    'corrente': 'primary',
    'poupanca': 'success',
    'investimento': 'info'
  }
  return colors[tipo] || 'secondary'
}

const getTipoLabel = (tipo: string): string => {
  const labels: Record<string, string> = {
    'corrente': 'Corrente',
    'poupanca': 'Poupança',
    'investimento': 'Investimento'
  }
  return labels[tipo] || tipo
}

const loadContas = async () => {
  try {
    loading.value = true
    const mesAno = currentMonth.value;
    const data = await contasService.list(mesAno)

    console.log(data);
    contas.value = data;
    console.log(contas.value);
  } catch (error: any) {
    console.error('Erro ao carregar contas:', error)
    toastStore.error('Erro ao carregar contas')
  } finally {
    loading.value = false
    loadingMonth.value = false
  }
}

const openDialog = () => {
  loadingForm.value = true
  editingId.value = null
  editingData.value = null
  setTimeout(() => {
    dialogOpen.value = true
    loadingForm.value = false
  }, 300)
}

const editConta = (item: Conta) => {
  editingId.value = item.id
  editingData.value = { ...item }
  dialogOpen.value = true
}

const deleteConta = async (id: number) => {
  if (confirm('Tem certeza que deseja deletar esta conta?')) {
    try {
      loading.value = true
      await contasService.delete(id)
      toastStore.success('Conta deletada com sucesso!')
      await loadContas()
    } catch (error: any) {
      console.error('Erro ao deletar conta:', error)
      toastStore.error(error.message || 'Erro ao deletar conta')
    } finally {
      loading.value = false
    }
  }
}

const clearFilters = () => {
  search.value = ''
  tipoFilter.value = ''
  statusFilter.value = ''
}

// Lifecycle
onMounted(() => {
  currentMonth.value = getCurrentMonth();
  loadingMonth.value = true;
  loadContas()
  
  // const mockContas: Conta[] = [
  //   {
  //     id: 1,
  //     name: 'Conta Principal',
  //     bank: 'Nubank',
  //     type: 'corrente',
  //     number: '123456-7',
  //     agency: '0001',
  //     balance: 1530050, // R$ 15.300,50
  //     status: 'ativa',
  //     description: 'Conta do dia a dia'
  //   },
  //   {
  //     id: 2,
  //     name: 'Investimentos',
  //     bank: 'Inter',
  //     type: 'investimento',
  //     number: '98765-4',
  //     agency: '0001',
  //     balance: 8500000, // R$ 85.000,00
  //     status: 'ativa',
  //     description: 'Carteira de Ações e Fundos'
  //   },
  //   {
  //     id: 3,
  //     name: 'Reserva de Emergência',
  //     bank: 'Itaú',
  //     type: 'poupanca',
  //     number: '11223-3',
  //     agency: '1234',
  //     balance: 3200000, // R$ 32.000,00
  //     status: 'ativa',
  //     description: 'Fundo para imprevistos'
  //   },
  //   {
  //     id: 4,
  //     name: 'Conta Salário Antiga',
  //     bank: 'Bradesco',
  //     type: 'corrente',
  //     number: '55667-7',
  //     agency: '4321',
  //     balance: -15000, // -R$ 150,00 (Exemplo negativo)
  //     status: 'inativa',
  //     description: 'Conta antiga, não movimentada'
  //   },
  //   {
  //     id: 5,
  //     name: 'Caixinha',
  //     bank: 'Banco do Brasil',
  //     type: 'poupanca',
  //     number: '77889-9',
  //     agency: '3322',
  //     balance: 50000, // R$ 500,00
  //     status: 'ativa',
  //     description: 'Poupança para viagem'
  //   }
  // ]
  
  // contas.value = mockContas
  loading.value = false
})

watch(() => currentMonth.value, () => {
  loadContas();
}, { immediate: true });
</script>



<style scoped lang="scss">
.account-card {
  border-radius: 16px; // Mais arredondado, como na imagem
  min-height: 210px; // Um pouco mais alto para comportar os elementos
  color: #FFFFFF;
  transition: all 0.3s ease;
  position: relative; // Essencial para o posicionamento dos elementos internos
  overflow: hidden; // Esconde o que vazar do padrão de onda

  &:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25) !important;
  }

  // Conteúdo do card (para ficar acima do padrão)
  .card-content {
    position: relative;
    z-index: 2;
  }

  // Padrão de onda no fundo
  .card-pattern {
    position: absolute;
    bottom: -60px; // Posiciona no fundo, parcialmente visível
    left: -50px;
    right: -50px;
    height: 200px;
    z-index: 1;
    opacity: 0.1; // Muito sutil
    // Este é um SVG de onda embutido
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="white" d="M0,160L48,181.3C96,203,192,245,288,261.3C384,277,480,267,576,234.7C672,203,768,149,864,138.7C960,128,1056,160,1152,170.7C1248,181,1344,171,1392,165.3L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
    background-size: cover;
    background-repeat: no-repeat;
  }

  // Chip do cartão (quadrado prateado)
  .card-chip {
    width: 48px;
    height: 38px;
    background: linear-gradient(135deg, #cfcfcf, #ebebeb);
    border-radius: 6px;
    border: 1px solid rgba(0,0,0,0.1);
  }

  // Logo da "Bandeira" (simulando Mastercard)
  .card-network-logo {
    position: relative;
    width: 40px;
    height: 25px;
    
    .circle {
      width: 25px;
      height: 25px;
      border-radius: 50%;
      position: absolute;
      
      &.red {
        background-color: #EB001B;
        left: 0;
      }
      &.yellow {
        background-color: #F79E1B;
        right: 0;
        opacity: 0.85; // Efeito de sobreposição
      }
    }
  }

  /* --- Classes de Gradiente (Manter as mesmas) --- */
  &.bg-nubank {
    background: linear-gradient(135deg, #612F74, #A13DA8);
  }
  &.bg-inter {
    background: linear-gradient(135deg, #FF7A00, #F5841F);
  }
  &.bg-itau {
    // Gradiente da imagem de inspiração (Laranja e Azul)
    background: linear-gradient(135deg, #EC7000, #0056A3);
  }
  &.bg-bradesco {
    // Vermelho do Bradesco
    background: linear-gradient(135deg, #D9232E, #B91C26);
  }
  &.bg-bb {
    // Azul e Amarelo do BB
    background: linear-gradient(135deg, #0033A0, #FADC01);
  }
  &.bg-caixa {
    // Azul da Caixa
    background: linear-gradient(135deg, #0073B5, #004A7B);
  }
  &.bg-default-grad {
    // Verde da imagem de inspiração (Banco Original?)
    background: linear-gradient(135deg, #16A085, #1E8449);
  }
}






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
