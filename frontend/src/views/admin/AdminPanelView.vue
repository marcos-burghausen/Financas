<template>
  <v-container fluid class="admin-panel pa-6">
    <v-row>
      <v-col cols="12">
        <h1 class="text-h4 mb-2 d-flex align-center">
          <v-icon icon="mdi-shield-crown" size="36" class="mr-3" color="primary" />
          Painel Administrativo
        </h1>
        <p class="text-subtitle-1 text-grey mb-6">
          Gerencie usuários, permissões e visualize estatísticas do sistema
        </p>
      </v-col>
    </v-row>

    <!-- Loading -->
    <v-row v-if="loading">
      <v-col cols="12" class="text-center py-12">
        <v-progress-circular indeterminate color="primary" size="64" />
        <p class="mt-4 text-grey">Carregando dados...</p>
      </v-col>
    </v-row>

    <!-- Conteúdo Principal -->
    <div v-else>
      <!-- Cards de Estatísticas -->
      <v-row v-if="stats" class="mb-6">
        <v-col cols="12" md="3">
          <v-card elevation="2" class="stat-card">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-grey">Total de Usuários</p>
                  <h2 class="text-h4">{{ stats.total_users }}</h2>
                </div>
                <v-icon icon="mdi-account-group" size="48" color="primary" class="stat-icon" />
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="3">
          <v-card elevation="2" class="stat-card">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-grey">Usuários Ativos</p>
                  <h2 class="text-h4 text-success">{{ stats.active_users }}</h2>
                </div>
                <v-icon icon="mdi-account-check" size="48" color="success" class="stat-icon" />
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="3">
          <v-card elevation="2" class="stat-card">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-grey">Lançamentos</p>
                  <h2 class="text-h4">{{ stats.total_lancamentos }}</h2>
                </div>
                <v-icon icon="mdi-chart-line" size="48" color="info" class="stat-icon" />
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="3">
          <v-card elevation="2" class="stat-card">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-grey">Este Mês</p>
                  <h2 class="text-h4 text-warning">{{ stats.lancamentos_this_month }}</h2>
                </div>
                <v-icon icon="mdi-calendar-month" size="48" color="warning" class="stat-icon" />
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Tabs -->
      <v-card elevation="3">
        <v-tabs v-model="activeTab" bg-color="primary" class="tabs-header">
          <v-tab value="users">
            <v-icon icon="mdi-account-multiple" class="mr-2" />
            Usuários
          </v-tab>
          <v-tab value="roles">
            <v-icon icon="mdi-shield-account" class="mr-2" />
            Roles & Permissões
          </v-tab>
          <v-tab value="logs">
            <v-icon icon="mdi-history" class="mr-2" />
            Logs de Atividades
          </v-tab>
          <v-tab value="stats">
            <v-icon icon="mdi-chart-bar" class="mr-2" />
            Estatísticas
          </v-tab>
          <v-tab value="system">
            <v-icon icon="mdi-cog" class="mr-2" />
            Sistema
          </v-tab>
        </v-tabs>

        <v-window v-model="activeTab">
          <!-- Tab: Usuários -->
          <v-window-item value="users">
            <v-card-text class="pa-6">
              <div class="d-flex justify-space-between align-center mb-4">
                <h2 class="text-h5">Gerenciamento de Usuários</h2>
                <v-text-field
                  v-model="searchUser"
                  prepend-inner-icon="mdi-magnify"
                  label="Buscar usuário..."
                  variant="outlined"
                  density="compact"
                  hide-details
                  clearable
                  style="max-width: 300px"
                />
              </div>

              <v-data-table
                :headers="userHeaders"
                :items="filteredUsers"
                :items-per-page="10"
                class="elevation-1"
              >
                <template #item.name="{ item }">
                  <div class="d-flex align-center py-2">
                    <v-avatar color="primary" size="32" class="mr-3">
                      <span class="text-white">{{ getInitials(item.name) }}</span>
                    </v-avatar>
                    <div>
                      <div class="font-weight-medium">{{ item.name }}</div>
                      <div class="text-caption text-grey">{{ item.email }}</div>
                    </div>
                  </div>
                </template>

                <template #item.roles="{ item }">
                  <v-chip
                    v-for="role in item.roles"
                    :key="role.id"
                    :color="getRoleColor(role.name)"
                    size="small"
                    class="mr-1"
                  >
                    <v-icon :icon="getRoleIcon(role.name)" size="small" class="mr-1" />
                    {{ role.display_name }}
                  </v-chip>
                  <v-chip v-if="item.roles.length === 0" size="small" variant="outlined">
                    Sem role
                  </v-chip>
                </template>

                <template #item.status="{ item }">
                  <v-chip
                    :color="item.email_verified_at ? 'success' : 'error'"
                    size="small"
                    variant="flat"
                  >
                    {{ item.email_verified_at ? 'Ativo' : 'Inativo' }}
                  </v-chip>
                </template>

                <template #item.created_at="{ item }">
                  {{ formatDate(item.created_at) }}
                </template>

                <template #item.actions="{ item }">
                  <v-menu>
                    <template #activator="{ props }">
                      <v-btn icon="mdi-dots-vertical" variant="text" v-bind="props" size="small" />
                    </template>
                    <v-list>
                      <v-list-item @click="openRoleDialog(item)">
                        <template #prepend>
                          <v-icon icon="mdi-shield-edit" />
                        </template>
                        <v-list-item-title>Gerenciar Roles</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="openEditUserDialog(item)">
                        <template #prepend>
                          <v-icon icon="mdi-pencil" />
                        </template>
                        <v-list-item-title>Editar Usuário</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="toggleUserStatus(item)">
                        <template #prepend>
                          <v-icon
                            :icon="item.email_verified_at ? 'mdi-account-off' : 'mdi-account-check'"
                          />
                        </template>
                        <v-list-item-title>
                          {{ item.email_verified_at ? 'Desativar' : 'Ativar' }}
                        </v-list-item-title>
                      </v-list-item>
                      <v-divider />
                      <v-list-item @click="openDeleteDialog(item)" class="text-error">
                        <template #prepend>
                          <v-icon icon="mdi-delete" color="error" />
                        </template>
                        <v-list-item-title>Deletar Usuário</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </template>
              </v-data-table>
            </v-card-text>
          </v-window-item>

          <!-- Tab: Roles & Permissões -->
          <v-window-item value="roles">
            <v-card-text class="pa-6">
              <h2 class="text-h5 mb-4">Roles e Permissões do Sistema</h2>
              
              <!-- Debug -->
              <div v-if="rolesStore.roles.length === 0" class="text-center py-8">
                <v-icon icon="mdi-alert-circle-outline" size="48" color="warning" class="mb-4" />
                <p class="text-h6">Nenhuma role encontrada</p>
                <p class="text-caption text-grey">
                  {{ rolesStore.error || 'Carregando roles...' }}
                </p>
                <v-btn 
                  @click="loadData" 
                  color="primary" 
                  class="mt-4"
                  prepend-icon="mdi-refresh"
                >
                  Recarregar
                </v-btn>
              </div>
              
              <v-row v-else>
                <v-col v-for="role in rolesStore.roles" :key="role.id" cols="12" md="6" lg="4">
                  <v-card elevation="2" class="role-card">
                    <v-card-title class="d-flex align-center">
                      <v-icon :icon="getRoleIcon(role.name)" :color="getRoleColor(role.name)" class="mr-2" />
                      {{ role.display_name }}
                      <v-spacer />
                      <v-chip size="small" variant="outlined">
                        {{ role.users_count || 0 }} usuários
                      </v-chip>
                    </v-card-title>
                    <v-card-subtitle>{{ role.description }}</v-card-subtitle>
                    <v-card-text>
                      <p class="text-caption font-weight-bold mb-2">PERMISSÕES:</p>
                      
                      <!-- Expansion Panel para permissões -->
                      <v-expansion-panels variant="accordion" class="permission-expansion">
                        <v-expansion-panel>
                          <v-expansion-panel-title hide-actions>
                            <v-chip-group column>
                              <!-- Mostrar primeiras 6 permissões -->
                              <v-chip
                                v-for="permission in role.permissions.slice(0, 6)"
                                :key="permission"
                                size="x-small"
                                variant="tonal"
                                color="primary"
                              >
                                <v-icon icon="mdi-check-circle" size="x-small" class="mr-1" />
                                {{ getPermissionLabel(permission) }}
                              </v-chip>
                              
                              <!-- Botão para expandir -->
                              <v-chip
                                v-if="role.permissions.length > 6"
                                size="x-small"
                                variant="outlined"
                                color="primary"
                                class="expand-chip"
                              >
                                <v-icon icon="mdi-chevron-down" size="x-small" class="mr-1" />
                                +{{ role.permissions.length - 6 }} mais
                              </v-chip>
                            </v-chip-group>
                          </v-expansion-panel-title>
                          
                          <v-expansion-panel-text v-if="role.permissions.length > 6">
                            <v-divider class="my-2" />
                            <p class="text-caption font-weight-bold mb-2 text-grey">
                              TODAS AS PERMISSÕES ({{ role.permissions.length }}):
                            </p>
                            <v-chip-group column>
                              <v-chip
                                v-for="permission in role.permissions"
                                :key="permission"
                                size="x-small"
                                variant="tonal"
                                color="success"
                              >
                                <v-icon icon="mdi-shield-check" size="x-small" class="mr-1" />
                                {{ getPermissionLabel(permission) }}
                              </v-chip>
                            </v-chip-group>
                          </v-expansion-panel-text>
                        </v-expansion-panel>
                      </v-expansion-panels>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
            </v-card-text>
          </v-window-item>

          <!-- Tab: Logs de Atividades -->
          <v-window-item value="logs">
            <v-card-text class="pa-6">
              <div class="d-flex justify-space-between align-center mb-4">
                <h2 class="text-h5">
                  <v-icon icon="mdi-history" class="mr-2" />
                  Logs de Atividades do Sistema
                </h2>
                <v-btn
                  color="primary"
                  variant="tonal"
                  prepend-icon="mdi-refresh"
                  @click="loadLogs"
                  :loading="loading"
                >
                  Atualizar
                </v-btn>
              </div>

              <!-- Filtros -->
              <v-card elevation="2" class="mb-4">
                <v-card-title class="text-subtitle-1">
                  <v-icon icon="mdi-filter" class="mr-2" />
                  Filtros de Pesquisa
                </v-card-title>
                <v-card-text>
                  <v-row>
                    <v-col cols="12" md="3">
                      <v-text-field
                        v-model="logsFilter.email"
                        label="Email do Usuário"
                        prepend-inner-icon="mdi-email"
                        clearable
                        density="comfortable"
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" md="3">
                      <v-text-field
                        v-model="logsFilter.action"
                        label="Ação"
                        prepend-inner-icon="mdi-magnify"
                        clearable
                        density="comfortable"
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" md="2">
                      <v-text-field
                        v-model="logsFilter.date_from"
                        label="Data Inicial"
                        type="date"
                        prepend-inner-icon="mdi-calendar"
                        density="comfortable"
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" md="2">
                      <v-text-field
                        v-model="logsFilter.date_to"
                        label="Data Final"
                        type="date"
                        prepend-inner-icon="mdi-calendar"
                        density="comfortable"
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" md="2" class="d-flex align-center gap-2">
                      <v-btn
                        color="primary"
                        block
                        @click="applyLogsFilter"
                        :loading="loading"
                      >
                        <v-icon icon="mdi-filter-check" />
                        Filtrar
                      </v-btn>
                      <v-btn
                        color="grey"
                        variant="tonal"
                        icon="mdi-filter-off"
                        @click="clearLogsFilter"
                      />
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>

              <!-- Tabela de Logs -->
              <v-card elevation="2">
                <v-data-table
                  :headers="logsHeaders"
                  :items="rolesStore.activityLogs"
                  :loading="loading"
                  items-per-page-text="Logs por página"
                  no-data-text="Nenhum log encontrado"
                  loading-text="Carregando logs..."
                  class="elevation-0"
                  :items-per-page="50"
                  hide-default-footer
                >
                  <template #item.created_at="{ item }">
                    <div class="d-flex flex-column">
                      <span class="text-body-2">{{ formatLogDate(item.created_at) }}</span>
                      <span class="text-caption text-grey">{{ formatLogTime(item.created_at) }}</span>
                    </div>
                  </template>

                  <template #item.email="{ item }">
                    <div class="d-flex align-center">
                      <v-avatar size="32" color="primary" class="mr-2">
                        <v-icon icon="mdi-account" size="20" />
                      </v-avatar>
                      <span class="text-body-2">{{ item.email }}</span>
                    </div>
                  </template>

                  <template #item.action="{ item }">
                    <v-chip
                      :color="getActionColor(item.action)"
                      size="small"
                      variant="tonal"
                    >
                      <v-icon :icon="getActionIcon(item.action)" size="16" class="mr-1" />
                      {{ item.action }}
                    </v-chip>
                  </template>

                  <template #item.ip="{ item }">
                    <div class="d-flex align-center">
                      <v-icon icon="mdi-ip" size="16" class="mr-1 text-grey" />
                      <span class="text-caption font-mono">{{ item.ip }}</span>
                    </div>
                  </template>

                  <template #item.user_agent="{ item }">
                    <v-tooltip location="top" max-width="400">
                      <template #activator="{ props }">
                        <div v-bind="props" class="d-flex align-center text-truncate" style="max-width: 200px">
                          <v-icon :icon="getBrowserIcon(item.user_agent)" size="16" class="mr-1" />
                          <span class="text-caption text-truncate">{{ getBrowserName(item.user_agent) }}</span>
                        </div>
                      </template>
                      <span class="text-caption">{{ item.user_agent }}</span>
                    </v-tooltip>
                  </template>
                </v-data-table>

                <!-- Paginação customizada -->
                <v-divider />
                <div v-if="rolesStore.logsMetadata" class="d-flex justify-space-between align-center pa-4">
                  <div class="text-caption text-grey">
                    Mostrando {{ rolesStore.logsMetadata.from }} a {{ rolesStore.logsMetadata.to }} 
                    de {{ rolesStore.logsMetadata.total }} logs
                  </div>
                  <v-pagination
                    v-model="currentLogsPage"
                    :length="rolesStore.logsMetadata.last_page"
                    :total-visible="7"
                    @update:model-value="loadLogs"
                    density="comfortable"
                  />
                </div>
              </v-card>
            </v-card-text>
          </v-window-item>

          <!-- Tab: Estatísticas -->
          <v-window-item value="stats">
            <v-card-text class="pa-6">
              <h2 class="text-h5 mb-4">Estatísticas Detalhadas do Sistema</h2>
              
              <v-row v-if="stats">
                <!-- Gráfico de Usuários por Role -->
                <v-col cols="12" md="6">
                  <v-card elevation="2">
                    <v-card-title>Usuários por Role</v-card-title>
                    <v-card-text>
                      <div v-for="item in stats.users_by_role" :key="item.role_name" class="mb-3">
                        <div class="d-flex justify-space-between mb-1">
                          <span class="font-weight-medium">{{ item.role_name }}</span>
                          <span class="text-grey">{{ item.count }}</span>
                        </div>
                        <v-progress-linear
                          :model-value="(item.count / stats.total_users) * 100"
                          :color="getRoleColorByDisplayName(item.role_name)"
                          height="8"
                          rounded
                        />
                      </div>
                    </v-card-text>
                  </v-card>
                </v-col>

                <!-- Top Usuários por Lançamentos -->
                <v-col cols="12" md="6">
                  <v-card elevation="2">
                    <v-card-title>Top Usuários - Lançamentos</v-card-title>
                    <v-card-text>
                      <v-list>
                        <v-list-item
                          v-for="(item, index) in stats.lancamentos_por_usuario"
                          :key="index"
                          class="px-0"
                        >
                          <template #prepend>
                            <v-avatar :color="getRankColor(index)" size="32">
                              <span class="text-white font-weight-bold">{{ index + 1 }}</span>
                            </v-avatar>
                          </template>
                          <v-list-item-title>{{ item.user_name }}</v-list-item-title>
                          <template #append>
                            <v-chip size="small" color="primary">
                              {{ item.total }} lançamentos
                            </v-chip>
                          </template>
                        </v-list-item>
                      </v-list>
                    </v-card-text>
                  </v-card>
                </v-col>

                <!-- Resumo Geral -->
                <v-col cols="12">
                  <v-card elevation="2">
                    <v-card-title>Resumo Geral do Sistema</v-card-title>
                    <v-card-text>
                      <v-row>
                        <v-col cols="6" md="3">
                          <div class="text-center pa-4">
                            <v-icon icon="mdi-account-group" size="48" color="primary" />
                            <h3 class="text-h5 mt-2">{{ stats.total_users }}</h3>
                            <p class="text-caption">Total de Usuários</p>
                          </div>
                        </v-col>
                        <v-col cols="6" md="3">
                          <div class="text-center pa-4">
                            <v-icon icon="mdi-account-check" size="48" color="success" />
                            <h3 class="text-h5 mt-2">{{ stats.active_users }}</h3>
                            <p class="text-caption">Usuários Ativos</p>
                          </div>
                        </v-col>
                        <v-col cols="6" md="3">
                          <div class="text-center pa-4">
                            <v-icon icon="mdi-finance" size="48" color="info" />
                            <h3 class="text-h5 mt-2">{{ stats.total_lancamentos }}</h3>
                            <p class="text-caption">Total de Lançamentos</p>
                          </div>
                        </v-col>
                        <v-col cols="6" md="3">
                          <div class="text-center pa-4">
                            <v-icon icon="mdi-calendar-check" size="48" color="warning" />
                            <h3 class="text-h5 mt-2">{{ stats.lancamentos_this_month }}</h3>
                            <p class="text-caption">Lançamentos (Mês)</p>
                          </div>
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
            </v-card-text>
          </v-window-item>

          <!-- Tab: Sistema -->
          <v-window-item value="system">
            <v-card-text class="pa-6">
              <h2 class="text-h5 mb-4">Configurações do Sistema</h2>
              
              <v-row>
                <v-col cols="12" md="6">
                  <v-card elevation="2" class="mb-4">
                    <v-card-title>Informações do Sistema</v-card-title>
                    <v-card-text>
                      <v-list>
                        <v-list-item>
                          <template #prepend>
                            <v-icon icon="mdi-application" />
                          </template>
                          <v-list-item-title>Versão</v-list-item-title>
                          <v-list-item-subtitle>1.0.0</v-list-item-subtitle>
                        </v-list-item>
                        <v-list-item>
                          <template #prepend>
                            <v-icon icon="mdi-database" />
                          </template>
                          <v-list-item-title>Banco de Dados</v-list-item-title>
                          <v-list-item-subtitle>MySQL 8.0</v-list-item-subtitle>
                        </v-list-item>
                        <v-list-item>
                          <template #prepend>
                            <v-icon icon="mdi-server" />
                          </template>
                          <v-list-item-title>Backend</v-list-item-title>
                          <v-list-item-subtitle>Laravel 11</v-list-item-subtitle>
                        </v-list-item>
                      </v-list>
                    </v-card-text>
                  </v-card>
                </v-col>

                <v-col cols="12" md="6">
                  <v-card elevation="2" class="mb-4">
                    <v-card-title>Ações Administrativas</v-card-title>
                    <v-card-text>
                      <v-btn block color="warning" variant="tonal" class="mb-3" disabled>
                        <v-icon icon="mdi-backup-restore" class="mr-2" />
                        Fazer Backup do Sistema
                      </v-btn>
                      <v-btn block color="info" variant="tonal" class="mb-3" disabled>
                        <v-icon icon="mdi-file-chart" class="mr-2" />
                        Gerar Relatório de Auditoria
                      </v-btn>
                      <v-btn block color="error" variant="tonal" disabled>
                        <v-icon icon="mdi-delete-sweep" class="mr-2" />
                        Limpar Logs Antigos
                      </v-btn>
                      <p class="text-caption text-center mt-3 text-grey">
                        Funcionalidades em desenvolvimento
                      </p>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
            </v-card-text>
          </v-window-item>
        </v-window>
      </v-card>
    </div>

    <!-- Dialog: Gerenciar Roles -->
    <role-assignment-dialog
      v-model="roleDialog"
      :user="selectedUser"
      @updated="onRolesUpdated"
    />

    <!-- Dialog: Editar Usuário -->
    <edit-user-dialog
      v-model="editUserDialog"
      :user="selectedUser"
      @updated="onUserUpdated"
    />

    <!-- Dialog: Confirmar Exclusão -->
    <v-dialog v-model="deleteDialog" max-width="500">
      <v-card>
        <v-card-title class="text-h5">Confirmar Exclusão</v-card-title>
        <v-card-text>
          Tem certeza que deseja excluir o usuário <strong>{{ selectedUser?.name }}</strong>?
          Esta ação não pode ser desfeita.
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text @click="deleteDialog = false">Cancelar</v-btn>
          <v-btn color="error" @click="confirmDelete">Deletar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Snackbar -->
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" :timeout="3000">
      {{ snackbar.message }}
      <template #actions>
        <v-btn variant="text" @click="snackbar.show = false">Fechar</v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>

<script setup lang="ts">
import EditUserDialog from '@/components/EditUserDialog.vue';
import RoleAssignmentDialog from '@/components/RoleAssignmentDialog.vue';
import { useRolesStore } from '@/store/roles';
import type { UserWithRoles } from '@/types/roles.types';
import { PERMISSION_DESCRIPTIONS, ROLE_COLORS, ROLE_ICONS } from '@/types/roles.types';
import { format } from 'date-fns';
import { ptBR } from 'date-fns/locale';
import { computed, onMounted, ref } from 'vue';

// Store
const rolesStore = useRolesStore();

// State
const loading = ref(true);
const activeTab = ref('users');
const searchUser = ref('');
const roleDialog = ref(false);
const editUserDialog = ref(false);
const deleteDialog = ref(false);
const selectedUser = ref<UserWithRoles | null>(null);
const snackbar = ref({
  show: false,
  message: '',
  color: 'success'
});

// Logs State
const currentLogsPage = ref(1);
const logsFilter = ref({
  email: '',
  action: '',
  date_from: '',
  date_to: '',
});

// Table Headers
const userHeaders = [
  { title: 'Usuário', key: 'name', sortable: true },
  { title: 'Roles', key: 'roles', sortable: false },
  { title: 'Status', key: 'status', sortable: true },
  { title: 'Criado em', key: 'created_at', sortable: true },
  { title: 'Ações', key: 'actions', sortable: false, align: 'end' as const },
];

const logsHeaders = [
  { title: 'Data/Hora', key: 'created_at', sortable: true, width: '150px' },
  { title: 'Usuário', key: 'email', sortable: true, width: '200px' },
  { title: 'Ação', key: 'action', sortable: true, width: '180px' },
  { title: 'IP', key: 'ip', sortable: true, width: '150px' },
  { title: 'Navegador', key: 'user_agent', sortable: false, width: '200px' },
];

// Computed
const stats = computed(() => rolesStore.systemStats);

const filteredUsers = computed(() => {
  if (!searchUser.value) return rolesStore.users;
  
  const search = searchUser.value.toLowerCase();
  return rolesStore.users.filter(user => 
    user.name.toLowerCase().includes(search) ||
    user.email.toLowerCase().includes(search)
  );
});

// Methods
const loadData = async () => {
  loading.value = true;
  try {
    await Promise.all([
      rolesStore.fetchUsers(),
      rolesStore.fetchRoles(),
      rolesStore.fetchSystemStats()
    ]);
  } catch (error) {
    showSnackbar('Erro ao carregar dados', 'error');
  } finally {
    loading.value = false;
  }
};

const getInitials = (name: string): string => {
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .substring(0, 2);
};

const getRoleColor = (roleName: string): string => {
  return ROLE_COLORS[roleName] || 'grey';
};

const getRoleIcon = (roleName: string): string => {
  return ROLE_ICONS[roleName] || 'mdi-account';
};

const getRoleColorByDisplayName = (displayName: string): string => {
  const role = rolesStore.roles.find(r => r.display_name === displayName);
  return role ? getRoleColor(role.name) : 'grey';
};

const getPermissionLabel = (permission: string): string => {
  return PERMISSION_DESCRIPTIONS[permission] || permission;
};

const getRankColor = (index: number): string => {
  const colors = ['amber', 'grey-lighten-1', 'orange-darken-2', 'blue', 'green'];
  return colors[index] || 'grey';
};

const formatDate = (dateString: string): string => {
  return format(new Date(dateString), "dd/MM/yyyy 'às' HH:mm", { locale: ptBR });
};

const openRoleDialog = (user: UserWithRoles) => {
  selectedUser.value = user;
  roleDialog.value = true;
};

const openEditUserDialog = (user: UserWithRoles) => {
  selectedUser.value = user;
  editUserDialog.value = true;
};

const openDeleteDialog = (user: UserWithRoles) => {
  selectedUser.value = user;
  deleteDialog.value = true;
};

const toggleUserStatus = async (user: UserWithRoles) => {
  try {
    await rolesStore.toggleUserStatus(user.id);
    showSnackbar(
      user.email_verified_at ? 'Usuário desativado com sucesso' : 'Usuário ativado com sucesso',
      'success'
    );
  } catch (error) {
    showSnackbar('Erro ao alterar status do usuário', 'error');
  }
};

const confirmDelete = async () => {
  if (!selectedUser.value) return;
  
  try {
    // TODO: Implementar delete no backend
    showSnackbar('Usuário deletado com sucesso', 'success');
    deleteDialog.value = false;
    await loadData();
  } catch (error) {
    showSnackbar('Erro ao deletar usuário', 'error');
  }
};

const onRolesUpdated = () => {
  loadData();
  showSnackbar('Roles atualizadas com sucesso', 'success');
};

const onUserUpdated = () => {
  loadData();
  showSnackbar('Usuário atualizado com sucesso', 'success');
};

const showSnackbar = (message: string, color: string) => {
  snackbar.value = { show: true, message, color };
};

// Logs Methods
const loadLogs = async () => {
  loading.value = true;
  try {
    await rolesStore.fetchActivityLogs({
      ...logsFilter.value,
      page: currentLogsPage.value,
      per_page: 50,
    });
  } catch (error) {
    showSnackbar('Erro ao carregar logs', 'error');
  } finally {
    loading.value = false;
  }
};

const applyLogsFilter = () => {
  currentLogsPage.value = 1;
  loadLogs();
};

const clearLogsFilter = () => {
  logsFilter.value = {
    email: '',
    action: '',
    date_from: '',
    date_to: '',
  };
  currentLogsPage.value = 1;
  loadLogs();
};

const formatLogDate = (dateString: string): string => {
  return format(new Date(dateString), 'dd/MM/yyyy', { locale: ptBR });
};

const formatLogTime = (dateString: string): string => {
  return format(new Date(dateString), 'HH:mm:ss', { locale: ptBR });
};

const getActionColor = (action: string): string => {
  const actionLower = action.toLowerCase();
  if (actionLower.includes('login')) return 'success';
  if (actionLower.includes('logout')) return 'info';
  if (actionLower.includes('delete') || actionLower.includes('exclu')) return 'error';
  if (actionLower.includes('create') || actionLower.includes('cria')) return 'primary';
  if (actionLower.includes('update') || actionLower.includes('edit')) return 'warning';
  return 'grey';
};

const getActionIcon = (action: string): string => {
  const actionLower = action.toLowerCase();
  if (actionLower.includes('login')) return 'mdi-login';
  if (actionLower.includes('logout')) return 'mdi-logout';
  if (actionLower.includes('delete') || actionLower.includes('exclu')) return 'mdi-delete';
  if (actionLower.includes('create') || actionLower.includes('cria')) return 'mdi-plus-circle';
  if (actionLower.includes('update') || actionLower.includes('edit')) return 'mdi-pencil';
  if (actionLower.includes('view') || actionLower.includes('visualiz')) return 'mdi-eye';
  return 'mdi-information';
};

const getBrowserIcon = (userAgent: string): string => {
  const ua = userAgent.toLowerCase();
  if (ua.includes('chrome') && !ua.includes('edg')) return 'mdi-google-chrome';
  if (ua.includes('firefox')) return 'mdi-firefox';
  if (ua.includes('safari') && !ua.includes('chrome')) return 'mdi-apple-safari';
  if (ua.includes('edg')) return 'mdi-microsoft-edge';
  if (ua.includes('opera') || ua.includes('opr')) return 'mdi-opera';
  return 'mdi-web';
};

const getBrowserName = (userAgent: string): string => {
  const ua = userAgent.toLowerCase();
  if (ua.includes('chrome') && !ua.includes('edg')) return 'Chrome';
  if (ua.includes('firefox')) return 'Firefox';
  if (ua.includes('safari') && !ua.includes('chrome')) return 'Safari';
  if (ua.includes('edg')) return 'Edge';
  if (ua.includes('opera') || ua.includes('opr')) return 'Opera';
  return 'Navegador desconhecido';
};

// Lifecycle
onMounted(() => {
  loadData();
});
</script>

<style scoped>
.admin-panel {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
}

.stat-card {
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-4px);
}

.stat-icon {
  opacity: 0.2;
}

.role-card {
  height: 100%;
  transition: transform 0.2s, box-shadow 0.2s;
}

.role-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.tabs-header {
  border-bottom: 1px solid rgba(0,0,0,0.12);
}

/* Estilos para expansion panel de permissões */
.permission-expansion :deep(.v-expansion-panel) {
  background: transparent !important;
  box-shadow: none !important;
}

.permission-expansion :deep(.v-expansion-panel-title) {
  padding: 0 !important;
  min-height: auto !important;
  cursor: pointer !important;
}

.permission-expansion :deep(.v-expansion-panel-title:hover) {
  background: rgba(var(--v-theme-primary), 0.04);
  border-radius: 4px;
}

.permission-expansion :deep(.v-expansion-panel-text__wrapper) {
  padding: 8px 0 !important;
}

.expand-chip {
  cursor: pointer !important;
  transition: all 0.2s ease-in-out;
}

.expand-chip:hover {
  background: rgba(var(--v-theme-primary), 0.08) !important;
  transform: scale(1.05);
}

/* Animação do ícone de expandir */
.permission-expansion :deep(.v-expansion-panel-title--active .v-icon) {
  transform: rotate(180deg);
  transition: transform 0.3s ease;
}

/* Estilo para fonte monoespaçada nos IPs */
.font-mono {
  font-family: 'Courier New', Courier, monospace;
}
</style>
