<template>
  <div class="veiculo-view">
    <!-- Header Section -->
    <div class="view-header mb-4 mb-md-6">
      <div class="d-flex align-center justify-space-between flex-wrap gap-3 mb-2">
        <div class="d-flex align-center gap-2 gap-md-3 flex-grow-1">
          <v-icon icon="mdi-car" :size="$vuetify.display.xs ? 28 : 36" color="primary" />
          <div>
            <h1 :class="$vuetify.display.xs ? 'text-h6' : 'text-h5'" class="font-weight-bold">Meus Veículos</h1>
            <p class="text-caption text-medium-emphasis mb-0 d-none d-sm-block">Gerencie seus veículos e manutenções</p>
          </div>
        </div>
        <v-btn
          color="primary"
          :size="$vuetify.display.xs ? 'default' : 'large'"
          :prepend-icon="$vuetify.display.xs ? undefined : 'mdi-plus'"
          :icon="$vuetify.display.xs ? 'mdi-plus' : undefined"
          @click="openAddVeiculoDialog"
          class="flex-shrink-0"
        >
          <span class="d-none d-sm-inline">Novo Veículo</span>
        </v-btn>
      </div>
    </div>

    <!-- KPI Cards -->
    <v-row class="mb-4 mb-md-6 kpi-row">
      <!-- Card: Total de Veículos -->
      <v-col cols="6" sm="6" lg="3">
        <v-card class="kpi-card pa-3 pa-md-4" elevation="1">
          <div class="d-flex align-center justify-space-between gap-1 gap-md-2">
            <div class="flex-grow-1 min-width-0 overflow-hidden">
              <p class="text-caption text-medium-emphasis mb-1 mb-md-2 text-no-wrap">Total de Veículos</p>
              <h3 :class="$vuetify.display.xs ? 'text-body-1' : 'text-h6'" class="font-weight-bold">{{ veiculos.length }}</h3>
            </div>
            <v-icon icon="mdi-car-multiple" :size="$vuetify.display.xs ? 24 : 40" color="info" class="flex-shrink-0 ml-1" />
          </div>
        </v-card>
      </v-col>

      <!-- Card: Gasto Total em Manutenção -->
      <v-col cols="6" sm="6" lg="3">
        <v-card class="kpi-card pa-3 pa-md-4" elevation="1">
          <div class="d-flex align-center justify-space-between gap-1 gap-md-2">
            <div class="flex-grow-1 min-width-0 overflow-hidden">
              <p class="text-caption text-medium-emphasis mb-1 mb-md-2 text-no-wrap">Gasto Total (Manutenção)</p>
              <h3 :class="$vuetify.display.xs ? 'text-body-1' : 'text-h6'" class="font-weight-bold text-truncate">{{ formatCurrency(summary.gastoTotal) }}</h3>
            </div>
            <v-icon icon="mdi-cash-multiple" :size="$vuetify.display.xs ? 24 : 40" color="warning" class="flex-shrink-0 ml-1" />
          </div>
        </v-card>
      </v-col>

      <!-- Card: Manutenção Próxima -->
      <v-col cols="6" sm="6" lg="3">
        <v-card class="kpi-card pa-3 pa-md-4" elevation="1" :class="{ 'alert-card': summary.proximaManutencao }">
          <div class="d-flex align-center justify-space-between gap-1 gap-md-2">
            <div class="flex-grow-1 min-width-0 overflow-hidden">
              <p class="text-caption text-medium-emphasis mb-1 mb-md-2 text-no-wrap">Próxima Manutenção</p>
              <h3 :class="$vuetify.display.xs ? 'text-body-1' : 'text-h6'" class="font-weight-bold text-truncate">{{ summary.proximaManutencao || 'Não há' }}</h3>
            </div>
            <v-icon icon="mdi-wrench" :size="$vuetify.display.xs ? 24 : 40" color="error" class="flex-shrink-0 ml-1" />
          </div>
        </v-card>
      </v-col>

      <!-- Card: Veículos Ativos -->
      <v-col cols="6" sm="6" lg="3">
        <v-card class="kpi-card pa-3 pa-md-4" elevation="1">
          <div class="d-flex align-center justify-space-between gap-1 gap-md-2">
            <div class="flex-grow-1 min-width-0 overflow-hidden">
              <p class="text-caption text-medium-emphasis mb-1 mb-md-2 text-no-wrap">Veículos Ativos</p>
              <h3 :class="$vuetify.display.xs ? 'text-body-1' : 'text-h6'" class="font-weight-bold">{{ summary.veiculosAtivos }}</h3>
            </div>
            <v-icon icon="mdi-check-circle-outline" :size="$vuetify.display.xs ? 24 : 40" color="success" class="flex-shrink-0 ml-1" />
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Abas de Navegação -->
    <v-tabs v-model="tabAtiva" class="mb-4 mb-md-6">
      <v-tab value="veiculos">
        <v-icon icon="mdi-car" class="mr-2" />
        Veículos
      </v-tab>
      <v-tab value="manutencoes">
        <v-icon icon="mdi-wrench" class="mr-2" />
        Manutenções
      </v-tab>
    </v-tabs>

    <!-- Tab: Veículos -->
    <v-card v-if="tabAtiva === 'veiculos'" class="mb-4 mb-md-6" elevation="1">
      <!-- Filtros -->
      <v-card class="filters-card pa-3 pa-md-4" elevation="0" variant="outlined">
        <div class="d-flex flex-column flex-sm-row flex-wrap gap-2 align-stretch align-sm-center">
          <v-text-field
            v-model="searchVeiculo"
            label="Buscar veículos..."
            prepend-inner-icon="mdi-magnify"
            clearable
            density="compact"
            class="flex-grow-1 flex-shrink-1"
            style="min-width: 0"
            hide-details
          />
          <v-select
            v-model="statusFilterVeiculo"
            :items="['ativo', 'inativo', 'manutenção']"
            label="Status"
            clearable
            density="compact"
            class="filter-select"
            hide-details
          />
          <v-btn
            variant="outlined"
            @click="clearFiltersVeiculo"
            :prepend-icon="$vuetify.display.xs ? undefined : 'mdi-close-circle-outline'"
            :icon="$vuetify.display.xs ? 'mdi-close-circle-outline' : undefined"
            :size="$vuetify.display.xs ? 'small' : 'default'"
            class="flex-shrink-0"
          >
            <span class="d-none d-sm-inline">Limpar</span>
          </v-btn>
        </div>
      </v-card>

      <!-- Grid de Veículos -->
      <v-row class="pa-3 pa-md-4">
        <v-col
          v-for="veiculo in filteredVeiculos"
          :key="veiculo.id"
          cols="12"
          sm="6"
          lg="4"
        >
          <v-card class="veiculo-card h-100 transition-all hover-elevation" elevation="1">
            <!-- Header do Card -->
            <div class="card-header pa-4 d-flex align-center justify-space-between" :style="{ backgroundColor: veiculo.color }">
              <div class="flex-grow-1">
                <h3 class="text-h6 font-weight-bold text-white mb-0">{{ veiculo.marca }} {{ veiculo.modelo }}</h3>
                <p class="text-caption text-white-50 mb-0">{{ veiculo.placa }}</p>
              </div>
              <v-chip
                :color="getStatusColor(veiculo.status)"
                label
                size="small"
                text-color="white"
                class="ml-2"
              >
                {{ getStatusLabel(veiculo.status) }}
              </v-chip>
            </div>

            <!-- Conteúdo do Card -->
            <v-card-text class="pa-4">
              <!-- Informações Básicas -->
              <div class="mb-4">
                <div class="d-flex justify-space-between mb-2">
                  <span class="text-caption text-medium-emphasis">Ano:</span>
                  <span class="text-body-2 font-weight-bold">{{ veiculo.ano }}</span>
                </div>
                <div class="d-flex justify-space-between mb-2">
                  <span class="text-caption text-medium-emphasis">Quilometragem:</span>
                  <span class="text-body-2 font-weight-bold">{{ formatNumber(veiculo.quilometragem) }} km</span>
                </div>
                <div class="d-flex justify-space-between mb-2">
                  <span class="text-caption text-medium-emphasis">Combustível:</span>
                  <span class="text-body-2 font-weight-bold">{{ veiculo.combustivel }}</span>
                </div>
              </div>

              <v-divider class="mb-4" />

              <!-- Última Manutenção -->
              <div class="mb-4">
                <p class="text-caption text-medium-emphasis mb-2">Última Manutenção</p>
                <div class="d-flex align-center gap-2">
                  <v-icon icon="mdi-calendar" size="20" color="primary" />
                  <span class="text-body-2">{{ formatDate(veiculo.ultimaManutencao) }}</span>
                </div>
              </div>

              <!-- Próxima Manutenção -->
              <div class="mb-4">
                <p class="text-caption text-medium-emphasis mb-2">Próxima Manutenção</p>
                <v-progress-linear
                  :value="calculateProxManutencaoProgress(veiculo)"
                  :color="calculateProxManutencaoProgress(veiculo) > 80 ? 'error' : 'success'"
                  class="mb-2"
                />
                <div class="d-flex justify-space-between align-center">
                  <span class="text-caption">{{ veiculo.proximaManutencao }} km</span>
                  <span class="text-caption text-medium-emphasis">{{ calculateProxManutencaoProgress(veiculo) }}%</span>
                </div>
              </div>

              <v-divider class="mb-4" />

              <!-- Ações -->
              <div class="d-flex gap-2">
                <v-btn
                  color="primary"
                  variant="outlined"
                  size="small"
                  prepend-icon="mdi-wrench"
                  @click="openAddManutencaoDialog(veiculo)"
                  class="flex-grow-1"
                >
                  <span class="text-truncate">Manutenção</span>
                </v-btn>
                <v-btn
                  color="info"
                  variant="outlined"
                  icon="mdi-pencil"
                  size="small"
                  @click="editVeiculo(veiculo)"
                />
                <v-btn
                  color="error"
                  variant="outlined"
                  icon="mdi-delete"
                  size="small"
                  @click="deleteVeiculo(veiculo.id)"
                />
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Card Vazio -->
        <v-col v-if="filteredVeiculos.length === 0" cols="12">
          <v-card class="pa-8 text-center" elevation="0" variant="outlined">
            <v-icon icon="mdi-car-off" size="64" color="text-disabled" class="mb-4" />
            <p class="text-body-1 text-medium-emphasis">Nenhum veículo encontrado</p>
            <v-btn
              color="primary"
              @click="openAddVeiculoDialog"
              class="mt-4"
              prepend-icon="mdi-plus"
            >
              Adicionar Veículo
            </v-btn>
          </v-card>
        </v-col>
      </v-row>
    </v-card>

    <!-- Tab: Manutenções -->
    <v-card v-if="tabAtiva === 'manutencoes'" class="mb-4 mb-md-6" elevation="1">
      <!-- Filtros -->
      <v-card class="filters-card pa-3 pa-md-4" elevation="0" variant="outlined">
        <div class="d-flex flex-column flex-sm-row flex-wrap gap-2 align-stretch align-sm-center">
          <v-text-field
            v-model="searchManutencao"
            label="Buscar manutenções..."
            prepend-inner-icon="mdi-magnify"
            clearable
            density="compact"
            class="flex-grow-1 flex-shrink-1"
            style="min-width: 0"
            hide-details
          />
          <v-select
            v-model="tipoManutencaoFilter"
            :items="tiposManutencao"
            label="Tipo"
            clearable
            density="compact"
            class="filter-select"
            hide-details
          />
          <v-btn
            variant="outlined"
            @click="clearFiltersManutencao"
            :prepend-icon="$vuetify.display.xs ? undefined : 'mdi-close-circle-outline'"
            :icon="$vuetify.display.xs ? 'mdi-close-circle-outline' : undefined"
            :size="$vuetify.display.xs ? 'small' : 'default'"
            class="flex-shrink-0"
          >
            <span class="d-none d-sm-inline">Limpar</span>
          </v-btn>
        </div>
      </v-card>

      <!-- Tabela de Manutenções -->
      <div class="table-wrapper">
        <v-data-table
          :items="filteredManutencoes"
          :headers="headersManutencao"
          :loading="loadingManutencao"
          class="manutencoes-table"
          density="comfortable"
          :mobile-breakpoint="$vuetify.display.xs ? 9999 : 0"
        >
          <template #item.veiculo="{ item }">
            <div class="d-flex align-center gap-2">
              <v-icon icon="mdi-car" color="primary" />
              <div>
                <div class="font-weight-bold">{{ getVeiculo(item.veiculoId).marca }} {{ getVeiculo(item.veiculoId).modelo }}</div>
                <div class="text-caption text-medium-emphasis">{{ getVeiculo(item.veiculoId).placa }}</div>
              </div>
            </div>
          </template>

          <template #item.tipo="{ item }">
            <v-chip
              :color="getTipoManutencaoColor(item.tipo)"
              label
              size="small"
            >
              {{ item.tipo }}
            </v-chip>
          </template>

          <template #item.data="{ item }">
            <div class="text-body-2">{{ formatDate(item.data) }}</div>
          </template>

          <template #item.quilometragem="{ item }">
            <div class="text-body-2 font-weight-bold">{{ formatNumber(item.quilometragem) }} km</div>
          </template>

          <template #item.valor="{ item }">
            <div class="text-right font-weight-bold text-error">{{ formatCurrency(item.valor) }}</div>
          </template>

          <template #item.descricao="{ item }">
            <div class="text-caption">{{ item.descricao || '-' }}</div>
          </template>

          <template #item.actions="{ item }">
            <div class="d-flex gap-1">
              <v-btn
                icon="mdi-pencil"
                variant="text"
                size="small"
                @click="editManutencao(item)"
              />
              <v-btn
                icon="mdi-delete"
                variant="text"
                size="small"
                color="error"
                @click="deleteManutencao(item.id)"
              />
            </div>
          </template>
        </v-data-table>
      </div>
    </v-card>

    <!-- Diálogos -->
    <!-- Dialog: Adicionar/Editar Veículo -->
    <v-dialog v-model="dialogVeiculoOpen" max-width="600px" persistent>
      <v-card>
        <v-card-title class="bg-primary text-white">
          {{ editingVeiculo ? 'Editar Veículo' : 'Novo Veículo' }}
        </v-card-title>
        <v-card-text class="pa-6">
          <v-form ref="formVeiculo" @submit.prevent="saveVeiculo">
            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="formVeiculoData.marca"
                  label="Marca"
                  placeholder="Ex: Toyota"
                  outlined
                  dense
                  required
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="formVeiculoData.modelo"
                  label="Modelo"
                  placeholder="Ex: Corolla"
                  outlined
                  dense
                  required
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="formVeiculoData.placa"
                  label="Placa"
                  placeholder="Ex: ABC1234"
                  outlined
                  dense
                  required
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model.number="formVeiculoData.ano"
                  label="Ano"
                  placeholder="Ex: 2023"
                  outlined
                  dense
                  type="number"
                  required
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model.number="formVeiculoData.quilometragem"
                  label="Quilometragem (km)"
                  placeholder="Ex: 10000"
                  outlined
                  dense
                  type="number"
                  required
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-select
                  v-model="formVeiculoData.combustivel"
                  :items="['Gasolina', 'Diesel', 'Etanol', 'Híbrido', 'Elétrico']"
                  label="Combustível"
                  outlined
                  dense
                  required
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model.number="formVeiculoData.proximaManutencao"
                  label="Próxima Manutenção (km)"
                  placeholder="Ex: 40000"
                  outlined
                  dense
                  type="number"
                  required
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-select
                  v-model="formVeiculoData.status"
                  :items="['ativo', 'inativo', 'manutenção']"
                  label="Status"
                  outlined
                  dense
                  required
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12">
                <v-color-picker
                  v-model="formVeiculoData.color"
                  hide-inputs
                  show-swatches
                />
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-6">
          <v-btn @click="closeDialogVeiculo">Cancelar</v-btn>
          <v-spacer />
          <v-btn color="primary" @click="saveVeiculo">
            {{ editingVeiculo ? 'Atualizar' : 'Adicionar' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Dialog: Adicionar/Editar Manutenção -->
    <v-dialog v-model="dialogManutencaoOpen" max-width="600px" persistent>
      <v-card>
        <v-card-title class="bg-primary text-white">
          {{ editingManutencao ? 'Editar Manutenção' : 'Registrar Manutenção' }}
        </v-card-title>
        <v-card-text class="pa-6">
          <v-form ref="formManutencao" @submit.prevent="saveManutencao">
            <v-select
              v-model="formManutencaoData.veiculoId"
              :items="veiculos"
              item-title="marca"
              item-value="id"
              label="Veículo"
              outlined
              dense
              required
            >
              <template #item="{ props, item }">
                <v-list-item v-bind="props" :title="`${item.raw.marca} ${item.raw.modelo}`" :subtitle="item.raw.placa" />
              </template>
            </v-select>

            <v-row class="mt-2">
              <v-col cols="12" sm="6">
                <v-select
                  v-model="formManutencaoData.tipo"
                  :items="tiposManutencao"
                  label="Tipo de Manutenção"
                  outlined
                  dense
                  required
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model="formManutencaoData.data"
                  label="Data"
                  type="date"
                  outlined
                  dense
                  required
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model.number="formManutencaoData.quilometragem"
                  label="Quilometragem (km)"
                  placeholder="Ex: 20000"
                  outlined
                  dense
                  type="number"
                  required
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-model.number="formManutencaoData.valor"
                  label="Valor (R$)"
                  placeholder="Ex: 150.00"
                  outlined
                  dense
                  type="number"
                  step="0.01"
                  required
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12">
                <v-textarea
                  v-model="formManutencaoData.descricao"
                  label="Descrição (opcional)"
                  placeholder="Ex: Troca de óleo, filtros e revisão geral"
                  outlined
                  dense
                  rows="3"
                />
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions class="pa-6">
          <v-btn @click="closeDialogManutencao">Cancelar</v-btn>
          <v-spacer />
          <v-btn color="primary" @click="saveManutencao">
            {{ editingManutencao ? 'Atualizar' : 'Registrar' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'

// Estados
const tabAtiva = ref('veiculos')
const loading = ref(false)
const loadingManutencao = ref(false)
const searchVeiculo = ref('')
const searchManutencao = ref('')
const statusFilterVeiculo = ref('')
const tipoManutencaoFilter = ref('')
const dialogVeiculoOpen = ref(false)
const dialogManutencaoOpen = ref(false)
const editingVeiculo = ref(null)
const editingManutencao = ref(null)

// Dados Hardcoded - Veículos
const veiculos = ref([
  {
    id: 1,
    marca: 'Toyota',
    modelo: 'Corolla',
    placa: 'ABC1234',
    ano: 2020,
    quilometragem: 35000,
    combustivel: 'Gasolina',
    status: 'ativo',
    color: '#163dc0',
    ultimaManutencao: '2025-09-15',
    proximaManutencao: 40000,
  },
  {
    id: 2,
    marca: 'Honda',
    modelo: 'Civic',
    placa: 'XYZ9876',
    ano: 2022,
    quilometragem: 18000,
    combustivel: 'Gasolina',
    status: 'ativo',
    color: '#e63946',
    ultimaManutencao: '2025-08-20',
    proximaManutencao: 50000,
  },
  {
    id: 3,
    marca: 'Volkswagen',
    modelo: 'Gol',
    placa: 'DEF5678',
    ano: 2019,
    quilometragem: 52000,
    combustivel: 'Gasolina',
    status: 'manutenção',
    color: '#f77f00',
    ultimaManutencao: '2025-07-10',
    proximaManutencao: 60000,
  },
])

// Dados Hardcoded - Manutenções
const manutencoes = ref([
  {
    id: 1,
    veiculoId: 1,
    tipo: 'Troca de Óleo',
    data: '2025-09-15',
    quilometragem: 30000,
    valor: 180.00,
    descricao: 'Troca de óleo 5W30 e filtro de óleo',
  },
  {
    id: 2,
    veiculoId: 1,
    tipo: 'Revisão',
    data: '2025-08-01',
    quilometragem: 28000,
    valor: 450.00,
    descricao: 'Revisão completa 30 mil km',
  },
  {
    id: 3,
    veiculoId: 2,
    tipo: 'Troca de Óleo',
    data: '2025-08-20',
    quilometragem: 18000,
    valor: 200.00,
    descricao: 'Troca de óleo 0W20 e filtro',
  },
  {
    id: 4,
    veiculoId: 2,
    tipo: 'Pneu',
    data: '2025-07-05',
    quilometragem: 15000,
    valor: 850.00,
    descricao: 'Troca de 4 pneus Michelin',
  },
  {
    id: 5,
    veiculoId: 3,
    tipo: 'Revisão',
    data: '2025-07-10',
    quilometragem: 50000,
    valor: 520.00,
    descricao: 'Revisão completa 50 mil km',
  },
])

// Tipos de Manutenção
const tiposManutencao = [
  'Troca de Óleo',
  'Revisão',
  'Pneu',
  'Freios',
  'Filtros',
  'Bateria',
  'Velas',
  'Corrente',
  'Suspensão',
  'Ar Condicionado',
  'Limpeza de Injetor',
  'Alinhamento',
]

// Form Data
const formVeiculoData = ref({
  marca: '',
  modelo: '',
  placa: '',
  ano: new Date().getFullYear(),
  quilometragem: 0,
  combustivel: 'Gasolina',
  proximaManutencao: 40000,
  status: 'ativo',
  color: '#163dc0',
})

const formManutencaoData = ref({
  veiculoId: null,
  tipo: '',
  data: new Date().toISOString().split('T')[0],
  quilometragem: 0,
  valor: 0,
  descricao: '',
})

// Headers da Tabela
const headersManutencao = [
  { title: 'Veículo', key: 'veiculo', sortable: true },
  { title: 'Tipo', key: 'tipo', sortable: true },
  { title: 'Data', key: 'data', sortable: true },
  { title: 'Quilometragem', key: 'quilometragem', sortable: true },
  { title: 'Valor', key: 'valor', sortable: true },
  { title: 'Descrição', key: 'descricao', sortable: false },
  { title: 'Ações', key: 'actions', sortable: false },
]

// Computed Properties
const filteredVeiculos = computed(() => {
  return veiculos.value.filter((v) => {
    const matchSearch = searchVeiculo.value === '' || 
      `${v.marca} ${v.modelo} ${v.placa}`.toLowerCase().includes(searchVeiculo.value.toLowerCase())
    const matchStatus = statusFilterVeiculo.value === '' || v.status === statusFilterVeiculo.value
    return matchSearch && matchStatus
  })
})

const filteredManutencoes = computed(() => {
  return manutencoes.value.filter((m) => {
    const veiculo = getVeiculo(m.veiculoId)
    const matchSearch = searchManutencao.value === '' || 
      `${veiculo.marca} ${veiculo.modelo} ${m.tipo}`.toLowerCase().includes(searchManutencao.value.toLowerCase())
    const matchTipo = tipoManutencaoFilter.value === '' || m.tipo === tipoManutencaoFilter.value
    return matchSearch && matchTipo
  })
})

const summary = computed(() => {
  const gastoTotal = manutencoes.value.reduce((sum, m) => sum + m.valor, 0)
  const veiculosAtivos = veiculos.value.filter(v => v.status === 'ativo').length
  
  let proximaManutencao = null
  veiculos.value.forEach((v) => {
    const progress = calculateProxManutencaoProgress(v)
    if (progress > 80 && !proximaManutencao) {
      proximaManutencao = `${v.marca} ${v.modelo}`
    }
  })

  return {
    gastoTotal,
    veiculosAtivos,
    proximaManutencao: proximaManutencao || 'Tudo em dia',
  }
})

// Funções
function formatCurrency(value) {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(value)
}

function formatDate(date) {
  return new Intl.DateTimeFormat('pt-BR').format(new Date(date))
}

function formatNumber(value) {
  return new Intl.NumberFormat('pt-BR').format(value)
}

function getStatusColor(status) {
  const colors = {
    ativo: 'success',
    inativo: 'secondary',
    'manutenção': 'error',
  }
  return colors[status] || 'default'
}

function getStatusLabel(status) {
  const labels = {
    ativo: 'Ativo',
    inativo: 'Inativo',
    'manutenção': 'Em Manutenção',
  }
  return labels[status] || status
}

function getTipoManutencaoColor(tipo) {
  const colors = {
    'Troca de Óleo': 'info',
    'Revisão': 'primary',
    'Pneu': 'warning',
    'Freios': 'error',
    'Filtros': 'secondary',
    'Bateria': 'orange',
    'Velas': 'cyan',
  }
  return colors[tipo] || 'default'
}

function calculateProxManutencaoProgress(veiculo) {
  const proximaManutencao = veiculo.proximaManutencao
  const quilometragem = veiculo.quilometragem
  const baseKm = proximaManutencao - 10000
  
  const progress = Math.round(((quilometragem - baseKm) / 10000) * 100)
  return Math.max(0, Math.min(100, progress))
}

function getVeiculo(id) {
  return veiculos.value.find(v => v.id === id) || {}
}

function openAddVeiculoDialog() {
  editingVeiculo.value = null
  formVeiculoData.value = {
    marca: '',
    modelo: '',
    placa: '',
    ano: new Date().getFullYear(),
    quilometragem: 0,
    combustivel: 'Gasolina',
    proximaManutencao: 40000,
    status: 'ativo',
    color: '#163dc0',
  }
  dialogVeiculoOpen.value = true
}

function closeDialogVeiculo() {
  dialogVeiculoOpen.value = false
  editingVeiculo.value = null
}

function editVeiculo(veiculo) {
  editingVeiculo.value = veiculo.id
  formVeiculoData.value = { ...veiculo }
  dialogVeiculoOpen.value = true
}

function saveVeiculo() {
  if (editingVeiculo.value) {
    const index = veiculos.value.findIndex(v => v.id === editingVeiculo.value)
    if (index > -1) {
      veiculos.value[index] = {
        ...veiculos.value[index],
        ...formVeiculoData.value,
      }
    }
  } else {
    veiculos.value.push({
      id: Math.max(...veiculos.value.map(v => v.id), 0) + 1,
      ...formVeiculoData.value,
      ultimaManutencao: new Date().toISOString().split('T')[0],
    })
  }
  closeDialogVeiculo()
}

function deleteVeiculo(id) {
  if (confirm('Tem certeza que deseja deletar este veículo?')) {
    veiculos.value = veiculos.value.filter(v => v.id !== id)
  }
}

function openAddManutencaoDialog(veiculo = null) {
  editingManutencao.value = null
  formManutencaoData.value = {
    veiculoId: veiculo?.id || null,
    tipo: '',
    data: new Date().toISOString().split('T')[0],
    quilometragem: 0,
    valor: 0,
    descricao: '',
  }
  dialogManutencaoOpen.value = true
}

function closeDialogManutencao() {
  dialogManutencaoOpen.value = false
  editingManutencao.value = null
}

function editManutencao(manutencao) {
  editingManutencao.value = manutencao.id
  formManutencaoData.value = { ...manutencao }
  dialogManutencaoOpen.value = true
}

function saveManutencao() {
  if (editingManutencao.value) {
    const index = manutencoes.value.findIndex(m => m.id === editingManutencao.value)
    if (index > -1) {
      manutencoes.value[index] = {
        ...manutencoes.value[index],
        ...formManutencaoData.value,
      }
    }
  } else {
    manutencoes.value.push({
      id: Math.max(...manutencoes.value.map(m => m.id), 0) + 1,
      ...formManutencaoData.value,
    })
  }
  closeDialogManutencao()
}

function deleteManutencao(id) {
  if (confirm('Tem certeza que deseja deletar este registro de manutenção?')) {
    manutencoes.value = manutencoes.value.filter(m => m.id !== id)
  }
}

function clearFiltersVeiculo() {
  searchVeiculo.value = ''
  statusFilterVeiculo.value = ''
}

function clearFiltersManutencao() {
  searchManutencao.value = ''
  tipoManutencaoFilter.value = ''
}
</script>

<style scoped lang="scss">
.veiculo-view {
  .view-header {
    @media (max-width: 600px) {
      h1 {
        font-size: 1.25rem !important;
      }
    }
  }

  .kpi-row {
    @media (max-width: 600px) {
      .kpi-card {
        h3 {
          font-size: 1rem !important;
        }
      }
    }
  }

  .veiculo-card {
    overflow: hidden;
    cursor: pointer;
    border: 1px solid rgba(0, 0, 0, 0.12);

    &:hover {
      box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15) !important;
      transform: translateY(-4px);
    }

    .card-header {
      position: relative;
      background: linear-gradient(135deg, rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.2) 100%),
        var(--color);
    }

    .text-white-50 {
      color: rgba(255, 255, 255, 0.7);
    }
  }

  .alert-card {
    background-color: rgba(244, 67, 54, 0.05);
  }

  .table-wrapper {
    overflow-x: auto;
  }

  .filters-card {
    background: rgb(var(--v-theme-surface));
    margin-bottom: 1.5rem;

    @media (max-width: 600px) {
      margin-bottom: 1rem;

      .filter-select {
        min-width: 140px;
      }
    }
  }

  .hover-elevation {
    transition: all 0.3s ease;
  }
}
</style>
