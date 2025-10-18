<template>
  <v-container fluid class="categorias-panel pa-6">
    <!-- Header -->
    <div class="d-flex align-center justify-space-between mb-6">
      <div>
        <div class="d-flex align-center gap-3 mb-2">
          <v-icon icon="mdi-tag-multiple" size="40" color="primary" />
          <h1 class="text-h4 font-weight-bold">Categorias</h1>
        </div>
        <p class="text-subtitle-2 text-medium-emphasis">
          Gerencie suas categorias de receitas e despesas
        </p>
      </div>
      <v-btn
        color="primary"
        size="large"
        prepend-icon="mdi-plus"
        @click="openAddDialog"
      >
        Nova Categoria
      </v-btn>
    </div>

    <!-- KPI Cards -->
    <v-row class="mb-6">
      <v-col cols="12" sm="6" md="3">
        <v-card elevation="2" class="kpi-card">
          <v-card-text class="pa-4">
            <div class="d-flex justify-space-between align-center">
              <div>
                <p class="text-caption text-medium-emphasis mb-2">Total de Categorias</p>
                <h2 class="text-h5 font-weight-bold">{{ categorias.length }}</h2>
              </div>
              <v-icon icon="mdi-tag" size="48" color="primary" opacity="0.2" />
            </div>
            <v-divider class="my-3" />
            <div class="d-flex align-center gap-1">
              <span class="text-caption text-primary font-weight-bold">{{ receitas.length }} receitas</span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card elevation="2" class="kpi-card">
          <v-card-text class="pa-4">
            <div class="d-flex justify-space-between align-center">
              <div>
                <p class="text-caption text-medium-emphasis mb-2">Categorias Receitas</p>
                <h2 class="text-h5 font-weight-bold">{{ receitas.length }}</h2>
              </div>
              <v-icon icon="mdi-plus-circle" size="48" color="success" opacity="0.2" />
            </div>
            <v-divider class="my-3" />
            <div class="d-flex align-center gap-1">
              <v-icon icon="mdi-trending-up" size="16" color="success" />
              <span class="text-caption text-success">Ativas</span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card elevation="2" class="kpi-card">
          <v-card-text class="pa-4">
            <div class="d-flex justify-space-between align-center">
              <div>
                <p class="text-caption text-medium-emphasis mb-2">Categorias Despesas</p>
                <h2 class="text-h5 font-weight-bold">{{ despesas.length }}</h2>
              </div>
              <v-icon icon="mdi-minus-circle" size="48" color="error" opacity="0.2" />
            </div>
            <v-divider class="my-3" />
            <div class="d-flex align-center gap-1">
              <v-icon icon="mdi-trending-down" size="16" color="error" />
              <span class="text-caption text-error">Ativas</span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card elevation="2" class="kpi-card">
          <v-card-text class="pa-4">
            <div class="d-flex justify-space-between align-center">
              <div>
                <p class="text-caption text-medium-emphasis mb-2">Em uso</p>
                <h2 class="text-h5 font-weight-bold">{{ emUso }}</h2>
              </div>
              <v-icon icon="mdi-check-circle" size="48" color="info" opacity="0.2" />
            </div>
            <v-divider class="my-3" />
            <div class="d-flex align-center gap-1">
              <span class="text-caption text-info font-weight-bold">{{ percentualUso }}% utilizadas</span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Filtros -->
    <v-card elevation="2" class="mb-6">
      <v-card-text class="pa-4">
        <v-row class="align-center">
          <v-col cols="12" sm="6" md="4">
            <v-text-field
              v-model="search"
              prepend-inner-icon="mdi-magnify"
              label="Buscar categoria..."
              variant="outlined"
              density="compact"
              clearable
              hide-details
            />
          </v-col>
          <v-col cols="12" sm="6" md="4">
            <v-select
              v-model="tipoFilter"
              :items="tipos"
              label="Tipo"
              variant="outlined"
              density="compact"
              clearable
              hide-details
            />
          </v-col>
          <v-col cols="12" sm="6" md="4">
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

    <!-- Cards Layout -->
    <v-row>
      <v-col v-for="categoria in filteredCategorias" :key="categoria.id" cols="12" sm="6" md="4" lg="3">
        <v-card elevation="2" class="categoria-card h-100">
          <v-card-text class="pa-4">
            <div class="d-flex justify-space-between align-start mb-3">
              <v-avatar :color="getCategoriaColor(categoria.tipo)" size="48">
                <v-icon :icon="getCategoriaIcon(categoria.tipo)" color="white" />
              </v-avatar>
              <v-menu>
                <template #activator="{ props }">
                  <v-btn icon="mdi-dots-vertical" size="x-small" variant="text" v-bind="props" />
                </template>
                <v-list>
                  <v-list-item @click="editCategoria(categoria)">
                    <template #prepend>
                      <v-icon icon="mdi-pencil" color="primary" />
                    </template>
                    <v-list-item-title>Editar</v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="deleteCategoria(categoria.id)" class="text-error">
                    <template #prepend>
                      <v-icon icon="mdi-delete" color="error" />
                    </template>
                    <v-list-item-title>Deletar</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </div>

            <h3 class="text-h6 font-weight-bold mb-1">{{ categoria.nome }}</h3>
            <p class="text-caption text-medium-emphasis mb-3">{{ categoria.descricao }}</p>

            <v-divider class="my-3" />

            <div class="d-flex justify-space-between align-center">
              <v-chip
                :color="getTipoColor(categoria.tipo)"
                size="small"
                variant="flat"
              >
                {{ getTipoLabel(categoria.tipo) }}
              </v-chip>
              <span class="text-caption text-medium-emphasis">{{ categoria.usos }} uso{{ categoria.usos !== 1 ? 's' : '' }}</span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Dialog Editar Categoria -->
    <v-dialog v-model="dialogOpen" max-width="500px">
      <v-card>
        <v-card-title class="d-flex align-center gap-2">
          <v-icon icon="mdi-tag-edit" color="primary" />
          {{ editingId ? 'Editar Categoria' : 'Nova Categoria' }}
        </v-card-title>

        <v-divider />

        <v-card-text class="pa-6">
          <v-form ref="form" @submit.prevent="saveCategoria">
            <v-text-field
              v-model="form.nome"
              label="Nome da categoria"
              prepend-icon="mdi-tag"
              variant="outlined"
              density="compact"
              :rules="[v => !!v || 'Campo obrigatório']"
              class="mb-4"
            />

            <v-textarea
              v-model="form.descricao"
              label="Descrição"
              prepend-icon="mdi-note"
              variant="outlined"
              density="compact"
              rows="2"
              class="mb-4"
            />

            <v-select
              v-model="form.tipo"
              :items="tipos"
              label="Tipo"
              prepend-icon="mdi-folder"
              variant="outlined"
              density="compact"
              :rules="[v => !!v || 'Campo obrigatório']"
              class="mb-4"
            />

            <v-color-picker
              v-model="form.cor"
              label="Cor"
              mode="hex"
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
          <v-btn @click="saveCategoria" variant="flat" color="primary" :loading="loading">
            {{ editingId ? 'Atualizar' : 'Adicionar' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'

interface Categoria {
  id: number
  nome: string
  descricao: string
  tipo: 'receita' | 'despesa'
  cor: string
  usos: number
}

// State
const categorias = ref<Categoria[]>([
  {
    id: 1,
    nome: 'Salário',
    descricao: 'Renda mensal',
    tipo: 'receita',
    cor: '#4caf50',
    usos: 12
  },
  {
    id: 2,
    nome: 'Freelance',
    descricao: 'Trabalhos adicionais',
    tipo: 'receita',
    cor: '#66bb6a',
    usos: 8
  },
  {
    id: 3,
    nome: 'Investimentos',
    descricao: 'Rendimento de investimentos',
    tipo: 'receita',
    cor: '#81c784',
    usos: 5
  },
  {
    id: 4,
    nome: 'Aluguel',
    descricao: 'Despesa de aluguel',
    tipo: 'despesa',
    cor: '#f44336',
    usos: 12
  },
  {
    id: 5,
    nome: 'Alimentação',
    descricao: 'Compras de comida',
    tipo: 'despesa',
    cor: '#ff7043',
    usos: 28
  },
  {
    id: 6,
    nome: 'Transporte',
    descricao: 'Uber, táxi, combustível',
    tipo: 'despesa',
    cor: '#ff6f00',
    usos: 15
  }
])

const search = ref('')
const tipoFilter = ref<string | null>(null)
const dialogOpen = ref(false)
const loading = ref(false)
const editingId = ref<number | null>(null)

const form = ref({
  nome: '',
  descricao: '',
  tipo: 'receita' as const,
  cor: '#2196F3'
})

const tipos = [
  { title: 'Receita', value: 'receita' },
  { title: 'Despesa', value: 'despesa' }
]

// Computed
const receitas = computed(() => categorias.value.filter(c => c.tipo === 'receita'))
const despesas = computed(() => categorias.value.filter(c => c.tipo === 'despesa'))

const emUso = computed(() => {
  return categorias.value.filter(c => c.usos > 0).length
})

const percentualUso = computed(() => {
  return categorias.value.length > 0 ? Math.round((emUso.value / categorias.value.length) * 100) : 0
})

const filteredCategorias = computed(() => {
  return categorias.value.filter(categoria => {
    const matchSearch = categoria.nome.toLowerCase().includes(search.value.toLowerCase()) ||
                       categoria.descricao.toLowerCase().includes(search.value.toLowerCase())
    const matchTipo = !tipoFilter.value || categoria.tipo === tipoFilter.value
    return matchSearch && matchTipo
  })
})

// Methods
function getCategoriaColor(tipo: string): string {
  return tipo === 'receita' ? 'success' : 'error'
}

function getCategoriaIcon(tipo: string): string {
  return tipo === 'receita' ? 'mdi-plus-circle' : 'mdi-minus-circle'
}

function getTipoColor(tipo: string): string {
  return tipo === 'receita' ? 'success' : 'error'
}

function getTipoLabel(tipo: string): string {
  return tipo === 'receita' ? 'Receita' : 'Despesa'
}

function openAddDialog(): void {
  editingId.value = null
  form.value = {
    nome: '',
    descricao: '',
    tipo: 'receita',
    cor: '#2196F3'
  }
  dialogOpen.value = true
}

function editCategoria(categoria: Categoria): void {
  editingId.value = categoria.id
  form.value = { ...categoria }
  dialogOpen.value = true
}

function saveCategoria(): void {
  loading.value = true
  setTimeout(() => {
    if (editingId.value) {
      const idx = categorias.value.findIndex(c => c.id === editingId.value)
      if (idx !== -1) {
        categorias.value[idx] = { ...categorias.value[idx], ...form.value }
      }
    } else {
      categorias.value.push({
        id: Math.max(...categorias.value.map(c => c.id)) + 1,
        ...form.value,
        usos: 0
      })
    }
    closeDialog()
    loading.value = false
  }, 500)
}

function deleteCategoria(id: number): void {
  if (confirm('Tem certeza que deseja deletar esta categoria?')) {
    categorias.value = categorias.value.filter(c => c.id !== id)
  }
}

function clearFilters(): void {
  search.value = ''
  tipoFilter.value = null
}

function closeDialog(): void {
  dialogOpen.value = false
  editingId.value = null
}
</script>

<style scoped lang="scss">
.kpi-card {
  transition: all 0.3s ease;
  border-left: 4px solid rgb(var(--v-theme-primary));

  &:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
    transform: translateY(-2px);
  }
}

.categoria-card {
  transition: all 0.3s ease;
  cursor: pointer;

  &:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
    transform: translateY(-2px);
  }
}
</style>
