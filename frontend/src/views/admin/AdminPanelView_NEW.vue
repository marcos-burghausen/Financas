<template>
  <v-container fluid class="admin-panel pa-6">
    <!-- Header -->
    <div class="d-flex align-center justify-space-between mb-6">
      <div>
        <div class="d-flex align-center gap-3 mb-2">
          <v-icon icon="mdi-shield-admin" size="40" color="primary" />
          <h1 class="text-h4 font-weight-bold">Painel Administrativo</h1>
        </div>
        <p class="text-subtitle-2 text-medium-emphasis">
          Gerencie usuários, permissões e monitore o sistema
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
                <p class="text-caption text-medium-emphasis mb-2">Total de Usuários</p>
                <h2 class="text-h5 font-weight-bold">{{ summary.totalUsers }}</h2>
              </div>
              <v-icon icon="mdi-account-multiple" size="48" color="info" opacity="0.2" />
            </div>
            <v-divider class="my-3" />
            <div class="d-flex align-center gap-1">
              <v-icon icon="mdi-trending-up" size="18" color="success" />
              <span class="text-caption text-success">+{{ summary.newUsersThisMonth }} este mês</span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card elevation="2" class="kpi-card">
          <v-card-text class="pa-4">
            <div class="d-flex justify-space-between align-center">
              <div>
                <p class="text-caption text-medium-emphasis mb-2">Usuários Ativos</p>
                <h2 class="text-h5 font-weight-bold">{{ summary.activeUsers }}</h2>
              </div>
              <v-icon icon="mdi-account-check" size="48" color="success" opacity="0.2" />
            </div>
            <v-divider class="my-3" />
            <div class="d-flex align-center gap-1">
              <span class="text-caption text-success font-weight-bold">{{ summary.activePercentage }}% ativo</span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card elevation="2" class="kpi-card">
          <v-card-text class="pa-4">
            <div class="d-flex justify-space-between align-center">
              <div>
                <p class="text-caption text-medium-emphasis mb-2">Total Lançamentos</p>
                <h2 class="text-h5 font-weight-bold">{{ formatNumber(summary.totalLancamentos) }}</h2>
              </div>
              <v-icon icon="mdi-chart-line" size="48" color="warning" opacity="0.2" />
            </div>
            <v-divider class="my-3" />
            <div class="d-flex align-center gap-1">
              <span class="text-caption text-warning font-weight-bold">{{ summary.lancamentosThisMonth }} este mês</span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card elevation="2" class="kpi-card">
          <v-card-text class="pa-4">
            <div class="d-flex justify-space-between align-center">
              <div>
                <p class="text-caption text-medium-emphasis mb-2">Taxa de Atividade</p>
                <h2 class="text-h5 font-weight-bold">{{ summary.activityRate }}%</h2>
              </div>
              <v-icon icon="mdi-chart-donut" size="48" color="primary" opacity="0.2" />
            </div>
            <v-divider class="my-3" />
            <div class="d-flex align-center gap-1">
              <v-icon icon="mdi-information" size="16" color="primary" />
              <span class="text-caption text-primary">Engajamento geral</span>
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
              label="Buscar usuário..."
              variant="outlined"
              density="compact"
              clearable
              hide-details
            />
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-select
              v-model="typeFilter"
              :items="userTypes"
              label="Tipo de usuário"
              variant="outlined"
              density="compact"
              clearable
              hide-details
            />
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-select
              v-model="statusFilter"
              :items="statuses"
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

    <!-- Tabela de Usuários -->
    <v-card elevation="2">
      <v-data-table
        :headers="headers"
        :items="filteredUsers"
        :items-per-page="10"
        class="user-table"
        hover
      >
        <!-- Nome -->
        <template #item.nome="{ item }">
          <div class="d-flex align-center gap-3 py-2">
            <v-avatar :color="getAvatarColor(item.id)" size="36">
              <span class="text-white font-weight-bold">{{ getInitials(item.nome) }}</span>
            </v-avatar>
            <div>
              <div class="font-weight-bold">{{ item.nome }}</div>
              <div class="text-caption text-medium-emphasis">{{ item.email }}</div>
            </div>
          </div>
        </template>

        <!-- Tipo -->
        <template #item.type="{ item }">
          <v-chip
            :color="getTypeColor(item.type)"
            variant="flat"
            size="small"
          >
            {{ getTypeLabel(item.type) }}
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

        <!-- Data Criação -->
        <template #item.dataCriacao="{ item }">
          <span class="text-caption">{{ formatDate(item.dataCriacao) }}</span>
        </template>

        <!-- Ações -->
        <template #item.actions="{ item }">
          <div class="d-flex gap-2">
            <v-btn
              icon="mdi-pencil"
              size="x-small"
              variant="text"
              color="primary"
              @click="editUser(item)"
            />
            <v-btn
              icon="mdi-delete"
              size="x-small"
              variant="text"
              color="error"
              @click="deleteUser(item.id)"
            />
          </div>
        </template>
      </v-data-table>
    </v-card>

    <!-- Dialog Editar Usuário -->
    <v-dialog v-model="dialogOpen" max-width="500px">
      <v-card>
        <v-card-title class="d-flex align-center gap-2">
          <v-icon icon="mdi-account-edit" color="primary" />
          {{ editingId ? 'Editar Usuário' : 'Novo Usuário' }}
        </v-card-title>

        <v-divider />

        <v-card-text class="pa-6">
          <v-form ref="form" @submit.prevent="saveUser">
            <v-text-field
              v-model="form.nome"
              label="Nome completo"
              prepend-icon="mdi-account"
              variant="outlined"
              density="compact"
              :rules="[v => !!v || 'Campo obrigatório']"
              class="mb-4"
            />

            <v-text-field
              v-model="form.email"
              label="Email"
              prepend-icon="mdi-email"
              variant="outlined"
              density="compact"
              type="email"
              :rules="[v => !!v || 'Campo obrigatório']"
              class="mb-4"
            />

            <v-select
              v-model="form.type"
              :items="userTypes"
              label="Tipo de usuário"
              prepend-icon="mdi-shield-account"
              variant="outlined"
              density="compact"
              :rules="[v => !!v || 'Campo obrigatório']"
              class="mb-4"
            />

            <v-select
              v-model="form.status"
              :items="statuses"
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
          <v-btn @click="saveUser" variant="flat" color="primary" :loading="loading">
            {{ editingId ? 'Atualizar' : 'Adicionar' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'

interface Usuario {
  id: number
  nome: string
  email: string
  type: 'USER' | 'TRADER' | 'ADMIN' | 'USER_TRADER' | 'FULL'
  status: 'ativo' | 'inativo' | 'bloqueado'
  dataCriacao: string
  observacao: string
}

// State
const usuarios = ref<Usuario[]>([
  {
    id: 1,
    nome: 'João Silva',
    email: 'joao@example.com',
    type: 'FULL',
    status: 'ativo',
    dataCriacao: '2025-01-15',
    observacao: 'Admin master'
  },
  {
    id: 2,
    nome: 'Maria Santos',
    email: 'maria@example.com',
    type: 'TRADER',
    status: 'ativo',
    dataCriacao: '2025-02-20',
    observacao: 'Trader ativo'
  },
  {
    id: 3,
    nome: 'Pedro Costa',
    email: 'pedro@example.com',
    type: 'USER',
    status: 'ativo',
    dataCriacao: '2025-03-10',
    observacao: 'Usuário comum'
  },
  {
    id: 4,
    nome: 'Ana Oliveira',
    email: 'ana@example.com',
    type: 'USER_TRADER',
    status: 'inativo',
    dataCriacao: '2025-01-25',
    observacao: 'Inativo há 30 dias'
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
  email: '',
  type: 'USER' as const,
  status: 'ativo' as const,
  observacao: ''
})

const userTypes = [
  { title: 'Usuário', value: 'USER' },
  { title: 'Trader', value: 'TRADER' },
  { title: 'Admin', value: 'ADMIN' },
  { title: 'Usuário + Trader', value: 'USER_TRADER' },
  { title: 'Full Access', value: 'FULL' }
]

const statuses = [
  { title: 'Ativo', value: 'ativo' },
  { title: 'Inativo', value: 'inativo' },
  { title: 'Bloqueado', value: 'bloqueado' }
]

// Headers
const headers = [
  { title: 'Nome', key: 'nome', align: 'start' },
  { title: 'Tipo', key: 'type', align: 'center', width: '120px' },
  { title: 'Status', key: 'status', align: 'center', width: '120px' },
  { title: 'Criado em', key: 'dataCriacao', align: 'center', width: '130px' },
  { title: 'Ações', key: 'actions', sortable: false, align: 'center', width: '100px' }
] as const

// Computed
const summary = computed(() => {
  const total = usuarios.value.length
  const active = usuarios.value.filter(u => u.status === 'ativo').length
  const lancamentos = Math.floor(Math.random() * 1000) + 500
  const lancamentosMonth = Math.floor(lancamentos / 3)

  return {
    totalUsers: total,
    newUsersThisMonth: Math.floor(total * 0.25),
    activeUsers: active,
    activePercentage: total > 0 ? Math.round((active / total) * 100) : 0,
    totalLancamentos: lancamentos,
    lancamentosThisMonth: lancamentosMonth,
    activityRate: 87
  }
})

const filteredUsers = computed(() => {
  return usuarios.value.filter(usuario => {
    const matchSearch = usuario.nome.toLowerCase().includes(search.value.toLowerCase()) ||
                       usuario.email.toLowerCase().includes(search.value.toLowerCase())
    const matchType = !typeFilter.value || usuario.type === typeFilter.value
    const matchStatus = !statusFilter.value || usuario.status === statusFilter.value
    return matchSearch && matchType && matchStatus
  })
})

// Methods
function formatNumber(n: number): string {
  return n.toLocaleString('pt-BR')
}

function formatDate(date: string): string {
  return new Date(date).toLocaleDateString('pt-BR')
}

function getInitials(nome: string): string {
  return nome
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

function getAvatarColor(id: number): string {
  const colors = ['primary', 'success', 'warning', 'error', 'info']
  return colors[id % colors.length]
}

function getTypeColor(type: string): string {
  const colors: Record<string, string> = {
    FULL: 'primary',
    TRADER: 'warning',
    ADMIN: 'error',
    USER_TRADER: 'info',
    USER: 'secondary'
  }
  return colors[type] || 'secondary'
}

function getTypeLabel(type: string): string {
  const labels: Record<string, string> = {
    FULL: 'Full Access',
    TRADER: 'Trader',
    ADMIN: 'Admin',
    USER_TRADER: 'Usuário + Trader',
    USER: 'Usuário'
  }
  return labels[type] || type
}

function getStatusColor(status: string): string {
  const colors: Record<string, string> = {
    ativo: 'success',
    inativo: 'secondary',
    bloqueado: 'error'
  }
  return colors[status] || 'secondary'
}

function getStatusLabel(status: string): string {
  const labels: Record<string, string> = {
    ativo: 'Ativo',
    inativo: 'Inativo',
    bloqueado: 'Bloqueado'
  }
  return labels[status] || status
}

function openAddDialog(): void {
  editingId.value = null
  form.value = {
    nome: '',
    email: '',
    type: 'USER',
    status: 'ativo',
    observacao: ''
  }
  dialogOpen.value = true
}

function editUser(usuario: Usuario): void {
  editingId.value = usuario.id
  form.value = { ...usuario }
  dialogOpen.value = true
}

function saveUser(): void {
  loading.value = true
  setTimeout(() => {
    if (editingId.value) {
      const idx = usuarios.value.findIndex(u => u.id === editingId.value)
      if (idx !== -1) {
        usuarios.value[idx] = { ...usuarios.value[idx], ...form.value }
      }
    } else {
      usuarios.value.push({
        id: Math.max(...usuarios.value.map(u => u.id)) + 1,
        ...form.value
      })
    }
    closeDialog()
    loading.value = false
  }, 500)
}

function deleteUser(id: number): void {
  if (confirm('Tem certeza que deseja deletar este usuário?')) {
    usuarios.value = usuarios.value.filter(u => u.id !== id)
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
  border-left: 4px solid rgb(var(--v-theme-primary));

  &:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
    transform: translateY(-2px);
  }
}

.user-table {
  :deep(.v-data-table__tr) {
    &:hover {
      background-color: rgba(var(--v-theme-primary), 0.05);
    }
  }
}
</style>
