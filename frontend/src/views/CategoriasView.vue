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
      <v-container fluid class="categorias-view pa-6">
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
                    <v-icon icon="mdi-tag-multiple" size="36" class="mr-3" color="primary" />
                    Categorias
                  </h1>
                  <p class="text-subtitle-1 text-grey mb-0">
                    Gerencie suas categorias de receitas e despesas
                  </p>
                </div>
              </div>
              <v-btn
                color="primary"
                prepend-icon="mdi-plus"
                @click="openAddDialog"
              >
                Nova Categoria
              </v-btn>
            </div>
          </v-col>
        </v-row>

        <!-- Summary Cards -->
        <v-row class="mb-4">
          <v-col cols="12" sm="6" md="3">
            <v-card elevation="4" class="summary-card h-100">
              <div class="card-gradient card-gradient-primary pa-4">
                <div class="d-flex justify-space-between align-start mb-3">
                  <div>
                    <p class="text-body-2 text-white mb-1">Total</p>
                    <h2 class="text-h5 text-white font-weight-bold">
                      {{ totalCategorias }}
                    </h2>
                  </div>
                  <v-avatar color="rgba(255,255,255,0.2)" size="48">
                    <v-icon icon="mdi-tag-multiple" color="white" size="28" />
                  </v-avatar>
                </div>
                <v-chip size="small" color="white" text-color="primary" class="font-weight-medium">
                  <v-icon icon="mdi-view-grid" start size="16" />
                  Todas categorias
                </v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3">
            <v-card elevation="4" class="summary-card h-100">
              <div class="card-gradient card-gradient-success pa-4">
                <div class="d-flex justify-space-between align-start mb-3">
                  <div>
                    <p class="text-body-2 text-white mb-1">Receitas</p>
                    <h2 class="text-h5 text-white font-weight-bold">
                      {{ categoriasReceitas.length }}
                    </h2>
                  </div>
                  <v-avatar color="rgba(255,255,255,0.2)" size="48">
                    <v-icon icon="mdi-cash-plus" color="white" size="28" />
                  </v-avatar>
                </div>
                <v-chip size="small" color="white" text-color="success" class="font-weight-medium">
                  <v-icon icon="mdi-arrow-up" start size="16" />
                  Entradas
                </v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3">
            <v-card elevation="4" class="summary-card h-100">
              <div class="card-gradient card-gradient-error pa-4">
                <div class="d-flex justify-space-between align-start mb-3">
                  <div>
                    <p class="text-body-2 text-white mb-1">Despesas</p>
                    <h2 class="text-h5 text-white font-weight-bold">
                      {{ categoriasDespesas.length }}
                    </h2>
                  </div>
                  <v-avatar color="rgba(255,255,255,0.2)" size="48">
                    <v-icon icon="mdi-cash-minus" color="white" size="28" />
                  </v-avatar>
                </div>
                <v-chip size="small" color="white" text-color="error" class="font-weight-medium">
                  <v-icon icon="mdi-arrow-down" start size="16" />
                  Saídas
                </v-chip>
              </div>
            </v-card>
          </v-col>

          <v-col cols="12" sm="6" md="3">
            <v-card elevation="4" class="summary-card h-100">
              <div class="card-gradient card-gradient-info pa-4">
                <div class="d-flex justify-space-between align-start mb-3">
                  <div>
                    <p class="text-body-2 text-white mb-1">Personalizadas</p>
                    <h2 class="text-h5 text-white font-weight-bold">
                      {{ categoriasPersonalizadas }}
                    </h2>
                  </div>
                  <v-avatar color="rgba(255,255,255,0.2)" size="48">
                    <v-icon icon="mdi-star" color="white" size="28" />
                  </v-avatar>
                </div>
                <v-chip size="small" color="white" text-color="info" class="font-weight-medium">
                  <v-icon icon="mdi-account" start size="16" />
                  Criadas por você
                </v-chip>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <!-- Filter Tabs -->
        <v-row class="mb-4">
          <v-col cols="12">
            <v-card elevation="2">
              <v-tabs
                v-model="activeTab"
                bg-color="transparent"
                color="primary"
                grow
              >
                <v-tab value="todas">
                  <v-icon icon="mdi-view-grid" start />
                  Todas ({{ totalCategorias }})
                </v-tab>
                <v-tab value="receitas">
                  <v-icon icon="mdi-cash-plus" start color="success" />
                  Receitas ({{ categoriasReceitas.length }})
                </v-tab>
                <v-tab value="despesas">
                  <v-icon icon="mdi-cash-minus" start color="error" />
                  Despesas ({{ categoriasDespesas.length }})
                </v-tab>
              </v-tabs>
            </v-card>
          </v-col>
        </v-row>

        <!-- Search -->
        <v-row class="mb-4">
          <v-col cols="12" md="6">
            <v-text-field
              v-model="searchQuery"
              label="Buscar categoria"
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              density="comfortable"
              clearable
              hide-details
            />
          </v-col>
        </v-row>

        <!-- Loading -->
        <v-row v-if="loading">
          <v-col cols="12" class="text-center py-12">
            <v-progress-circular indeterminate color="primary" size="64" />
            <p class="text-grey mt-4">Carregando categorias...</p>
          </v-col>
        </v-row>

        <!-- Empty State -->
        <v-row v-else-if="filteredCategorias.length === 0">
          <v-col cols="12">
            <v-card elevation="2" class="text-center pa-12">
              <v-icon icon="mdi-tag-multiple" size="80" color="grey-lighten-1" />
              <h3 class="text-h5 mt-4 mb-2">Nenhuma categoria encontrada</h3>
              <p class="text-grey mb-4">
                {{ searchQuery 
                  ? 'Tente ajustar sua busca ou adicionar uma nova categoria' 
                  : 'Adicione sua primeira categoria para organizar suas finanças' }}
              </p>
              <v-btn
                color="primary"
                size="large"
                prepend-icon="mdi-plus"
                @click="openAddDialog"
              >
                Adicionar Categoria
              </v-btn>
            </v-card>
          </v-col>
        </v-row>

        <!-- Categories Grid -->
        <v-row v-else>
          <v-col
            v-for="categoria in filteredCategorias"
            :key="categoria.id"
            cols="12"
            sm="6"
            md="4"
            lg="3"
          >
            <v-card
              elevation="4"
              class="categoria-card h-100"
              :class="getCategoryCardClass(categoria.tipo)"
            >
              <div class="card-header pa-4" :class="getCategoryGradientClass(categoria.tipo)">
                <div class="d-flex justify-space-between align-center">
                  <v-avatar
                    :color="categoria.cor"
                    size="56"
                  >
                    <v-icon :icon="categoria.icon" size="32" color="white" />
                  </v-avatar>
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
                      <v-list-item @click="editCategoria(categoria)">
                        <template #prepend>
                          <v-icon icon="mdi-pencil" color="primary" />
                        </template>
                        <v-list-item-title>Editar</v-list-item-title>
                      </v-list-item>
                      <v-list-item
                        v-if="categoria.editable"
                        @click="deleteCategoria(categoria)"
                      >
                        <template #prepend>
                          <v-icon icon="mdi-delete" color="error" />
                        </template>
                        <v-list-item-title>Excluir</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </div>
              </div>

              <v-card-text class="pa-4">
                <h3 class="text-h6 mb-2 font-weight-bold">{{ categoria.name }}</h3>
                <div class="d-flex align-center gap-2 mb-3">
                  <v-chip
                    size="small"
                    :color="categoria.tipo === 'receita' ? 'success' : 'error'"
                    variant="flat"
                  >
                    {{ categoria.tipo === 'receita' ? 'Receita' : 'Despesa' }}
                  </v-chip>
                  <v-chip
                    v-if="!categoria.editable"
                    size="small"
                    color="grey"
                    variant="outlined"
                  >
                    <v-icon icon="mdi-shield-lock" start size="14" />
                    Sistema
                  </v-chip>
                </div>
                <div class="d-flex align-center justify-space-between">
                  <div class="text-caption text-grey">
                    <v-icon icon="mdi-chart-line" size="16" class="mr-1" />
                    {{ categoria.uso || 0 }} lançamentos
                  </div>
                  <v-chip
                    :color="categoria.cor"
                    size="x-small"
                    variant="flat"
                    class="px-3"
                  >
                    {{ categoria.cor }}
                  </v-chip>
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- Add/Edit Dialog -->
        <v-dialog v-model="dialog" max-width="600">
          <v-card>
            <div class="card-gradient card-gradient-primary pa-4">
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center">
                  <v-icon icon="mdi-tag-multiple" size="32" color="white" class="mr-3" />
                  <h2 class="text-h5 text-white font-weight-bold">
                    {{ editMode ? 'Editar Categoria' : 'Nova Categoria' }}
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
              <v-form ref="form" @submit.prevent="saveCategoria">
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      v-model="formData.name"
                      label="Nome da Categoria *"
                      prepend-inner-icon="mdi-text"
                      variant="outlined"
                      :rules="[rules.required]"
                    />
                  </v-col>

                  <v-col cols="12">
                    <v-select
                      v-model="formData.tipo"
                      label="Tipo *"
                      prepend-inner-icon="mdi-tag"
                      variant="outlined"
                      :items="tipoOptions"
                      :rules="[rules.required]"
                    />
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-select
                      v-model="formData.icon"
                      label="Ícone *"
                      prepend-inner-icon="mdi-emoticon"
                      variant="outlined"
                      :items="iconOptions"
                      :rules="[rules.required]"
                    >
                      <template #selection="{ item }">
                        <v-icon :icon="item.value" class="mr-2" />
                        {{ item.title }}
                      </template>
                      <template #item="{ props, item }">
                        <v-list-item v-bind="props">
                          <template #prepend>
                            <v-icon :icon="item.value" />
                          </template>
                        </v-list-item>
                      </template>
                    </v-select>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-select
                      v-model="formData.cor"
                      label="Cor *"
                      prepend-inner-icon="mdi-palette"
                      variant="outlined"
                      :items="colorOptions"
                      :rules="[rules.required]"
                    >
                      <template #selection="{ item }">
                        <v-chip :color="item.value" size="small" class="mr-2" />
                        {{ item.title }}
                      </template>
                      <template #item="{ props, item }">
                        <v-list-item v-bind="props">
                          <template #prepend>
                            <v-chip :color="item.value" size="small" />
                          </template>
                        </v-list-item>
                      </template>
                    </v-select>
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
                color="primary"
                @click="saveCategoria"
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
const activeTab = ref('todas')
const searchQuery = ref('')

// Snackbar
const snackbar = ref({
  show: false,
  message: '',
  color: 'success'
})

// Data
const categoriasDespesas = ref<any[]>([])
const categoriasReceitas = ref<any[]>([])
const formData = ref({
  id: null,
  name: '',
  tipo: 'despesa',
  icon: '',
  cor: '',
  editable: true
})

// Options
const tipoOptions = [
  { title: 'Receita', value: 'receita' },
  { title: 'Despesa', value: 'despesa' }
]

const iconOptions = [
  { title: 'Alimentação', value: 'mdi-food' },
  { title: 'Transporte', value: 'mdi-car' },
  { title: 'Moradia', value: 'mdi-home' },
  { title: 'Saúde', value: 'mdi-heart-pulse' },
  { title: 'Educação', value: 'mdi-school' },
  { title: 'Lazer', value: 'mdi-palm-tree' },
  { title: 'Salário', value: 'mdi-briefcase' },
  { title: 'Freelance', value: 'mdi-laptop' },
  { title: 'Investimento', value: 'mdi-chart-line' },
  { title: 'Vendas', value: 'mdi-cart' },
  { title: 'Presente', value: 'mdi-gift' },
  { title: 'Compras', value: 'mdi-shopping' },
  { title: 'Conta', value: 'mdi-file-document' },
  { title: 'Outros', value: 'mdi-dots-horizontal' }
]

const colorOptions = [
  { title: 'Vermelho', value: '#F44336' },
  { title: 'Rosa', value: '#E91E63' },
  { title: 'Roxo', value: '#9C27B0' },
  { title: 'Azul', value: '#2196F3' },
  { title: 'Ciano', value: '#00BCD4' },
  { title: 'Verde', value: '#4CAF50' },
  { title: 'Lima', value: '#CDDC39' },
  { title: 'Amarelo', value: '#FFEB3B' },
  { title: 'Laranja', value: '#FF9800' },
  { title: 'Marrom', value: '#795548' },
  { title: 'Cinza', value: '#9E9E9E' },
  { title: 'Índigo', value: '#3F51B5' }
]

// Validation rules
const rules = {
  required: (v: any) => !!v || 'Campo obrigatório'
}

// Computed
const totalCategorias = computed(() => {
  return categoriasDespesas.value.length + categoriasReceitas.value.length
})

const categoriasPersonalizadas = computed(() => {
  const despesasPersonalizadas = categoriasDespesas.value.filter(c => c.editable).length
  const receitasPersonalizadas = categoriasReceitas.value.filter(c => c.editable).length
  return despesasPersonalizadas + receitasPersonalizadas
})

const todasCategorias = computed(() => {
  return [...categoriasDespesas.value, ...categoriasReceitas.value]
})

const filteredCategorias = computed(() => {
  let categorias = todasCategorias.value

  // Filter by tab
  if (activeTab.value === 'receitas') {
    categorias = categoriasReceitas.value
  } else if (activeTab.value === 'despesas') {
    categorias = categoriasDespesas.value
  }

  // Filter by search
  if (searchQuery.value) {
    categorias = categorias.filter(c => 
      c.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  return categorias
})

// Methods
const getCategoryCardClass = (tipo: string): string => {
  return tipo === 'receita' ? 'categoria-card-receita' : 'categoria-card-despesa'
}

const getCategoryGradientClass = (tipo: string): string => {
  return tipo === 'receita' ? 'card-gradient-success' : 'card-gradient-error'
}

const openAddDialog = () => {
  editMode.value = false
  formData.value = {
    id: null,
    name: '',
    tipo: activeTab.value === 'receitas' ? 'receita' : 'despesa',
    icon: '',
    cor: '',
    editable: true
  }
  dialog.value = true
}

const editCategoria = (categoria: any) => {
  editMode.value = true
  formData.value = { ...categoria }
  dialog.value = true
}

const deleteCategoria = async (categoria: any) => {
  if (!confirm(`Deseja realmente excluir a categoria "${categoria.name}"?`)) return

  try {
    await axiosInstance.delete(`/categorias/${categoria.id}`)
    
    if (categoria.tipo === 'despesa') {
      categoriasDespesas.value = categoriasDespesas.value.filter(c => c.id !== categoria.id)
    } else {
      categoriasReceitas.value = categoriasReceitas.value.filter(c => c.id !== categoria.id)
    }
    
    snackbar.value = {
      show: true,
      message: 'Categoria excluída com sucesso!',
      color: 'success'
    }
  } catch (error: any) {
    console.error('Erro ao excluir categoria:', error)
    snackbar.value = {
      show: true,
      message: error.response?.data?.message || 'Erro ao excluir categoria',
      color: 'error'
    }
  }
}

const saveCategoria = async () => {
  try {
    saving.value = true
    
    if (editMode.value) {
      await axiosInstance.put(`/categorias/${formData.value.id}`, formData.value)
      
      const lista = formData.value.tipo === 'despesa' ? categoriasDespesas : categoriasReceitas
      const index = lista.value.findIndex(c => c.id === formData.value.id)
      if (index !== -1) {
        lista.value[index] = { ...formData.value }
      }
      
      snackbar.value = {
        show: true,
        message: 'Categoria atualizada com sucesso!',
        color: 'success'
      }
    } else {
      const response = await axiosInstance.post('/categorias', formData.value)
      
      if (formData.value.tipo === 'despesa') {
        categoriasDespesas.value.push(response.data)
      } else {
        categoriasReceitas.value.push(response.data)
      }
      
      snackbar.value = {
        show: true,
        message: 'Categoria adicionada com sucesso!',
        color: 'success'
      }
    }
    
    dialog.value = false
  } catch (error: any) {
    console.error('Erro ao salvar categoria:', error)
    snackbar.value = {
      show: true,
      message: error.response?.data?.message || 'Erro ao salvar categoria',
      color: 'error'
    }
  } finally {
    saving.value = false
  }
}

const fetchCategorias = async () => {
  try {
    loading.value = true
    
    // Mock data - replace with API call
    categoriasDespesas.value = [
      { id: 1, name: 'Alimentação', tipo: 'despesa', icon: 'mdi-food', cor: '#F44336', editable: false, uso: 15 },
      { id: 2, name: 'Transporte', tipo: 'despesa', icon: 'mdi-car', cor: '#2196F3', editable: false, uso: 8 },
      { id: 3, name: 'Moradia', tipo: 'despesa', icon: 'mdi-home', cor: '#9C27B0', editable: false, uso: 12 },
      { id: 4, name: 'Saúde', tipo: 'despesa', icon: 'mdi-heart-pulse', cor: '#E91E63', editable: false, uso: 5 },
      { id: 5, name: 'Educação', tipo: 'despesa', icon: 'mdi-school', cor: '#3F51B5', editable: false, uso: 3 },
      { id: 6, name: 'Lazer', tipo: 'despesa', icon: 'mdi-palm-tree', cor: '#00BCD4', editable: false, uso: 10 },
      { id: 7, name: 'Compras', tipo: 'despesa', icon: 'mdi-shopping', cor: '#FF9800', editable: true, uso: 7 },
      { id: 8, name: 'Outros', tipo: 'despesa', icon: 'mdi-dots-horizontal', cor: '#9E9E9E', editable: false, uso: 4 }
    ]
    
    categoriasReceitas.value = [
      { id: 9, name: 'Salário', tipo: 'receita', icon: 'mdi-briefcase', cor: '#4CAF50', editable: false, uso: 12 },
      { id: 10, name: 'Freelance', tipo: 'receita', icon: 'mdi-laptop', cor: '#00BCD4', editable: false, uso: 8 },
      { id: 11, name: 'Investimentos', tipo: 'receita', icon: 'mdi-chart-line', cor: '#2196F3', editable: false, uso: 6 },
      { id: 12, name: 'Vendas', tipo: 'receita', icon: 'mdi-cart', cor: '#FF9800', editable: false, uso: 5 },
      { id: 13, name: 'Presente', tipo: 'receita', icon: 'mdi-gift', cor: '#E91E63', editable: true, uso: 2 },
      { id: 14, name: 'Outros', tipo: 'receita', icon: 'mdi-dots-horizontal', cor: '#9E9E9E', editable: false, uso: 3 }
    ]
    
  } catch (error: any) {
    console.error('Erro ao carregar categorias:', error)
    snackbar.value = {
      show: true,
      message: error.response?.data?.message || 'Erro ao carregar categorias',
      color: 'error'
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchCategorias()
})
</script>

<style scoped>
.categorias-view {
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

/* Category cards */
.categoria-card {
  transition: all 0.3s ease;
  overflow: hidden;
}

.categoria-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
}

.categoria-card-receita {
  border-top: 3px solid #4CAF50;
}

.categoria-card-despesa {
  border-top: 3px solid #F44336;
}

.card-header {
  border-radius: 8px 8px 0 0;
}

/* Responsive */
@media (max-width: 960px) {
  .categorias-view {
    padding: 16px !important;
  }
}
</style>
