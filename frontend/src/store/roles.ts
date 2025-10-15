import axiosInstance from '@/services/http';
import type { ActivityLog, ActivityLogFilters, ActivityLogsResponse } from '@/types/logs.types';
import type {
    Role,
    SystemStats,
    UserPermissions,
    UserWithRoles
} from '@/types/roles.types';
import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

export const useRolesStore = defineStore('roles', () => {
  // State
  const roles = ref<Role[]>([]);
  const users = ref<UserWithRoles[]>([]);
  const myPermissions = ref<string[]>([]);
  const myRoles = ref<string[]>([]);
  const systemStats = ref<SystemStats | null>(null);
  const activityLogs = ref<ActivityLog[]>([]);
  const logsMetadata = ref<Omit<ActivityLogsResponse, 'data'> | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Getters
  const isAdmin = computed(() => {
    return myRoles.value.includes('ADMIN') || myRoles.value.includes('FULL');
  });

  const isFull = computed(() => {
    return myRoles.value.includes('FULL');
  });

  const hasRole = (roleName: string): boolean => {
    return myRoles.value.includes(roleName);
  };

  const hasAnyRole = (roleNames: string[]): boolean => {
    return roleNames.some(roleName => myRoles.value.includes(roleName));
  };

  const hasPermission = (permission: string): boolean => {
    // Se tem role FULL, tem todas as permissões
    if (myRoles.value.includes('FULL')) return true;
    return myPermissions.value.includes(permission);
  };

  const hasAnyPermission = (permissions: string[]): boolean => {
    if (myRoles.value.includes('FULL')) return true;
    return permissions.some(permission => myPermissions.value.includes(permission));
  };

  const hasAllPermissions = (permissions: string[]): boolean => {
    if (myRoles.value.includes('FULL')) return true;
    return permissions.every(permission => myPermissions.value.includes(permission));
  };

  const getRoleById = (roleId: number): Role | undefined => {
    return roles.value.find(role => role.id === roleId);
  };

  const getRoleByName = (roleName: string): Role | undefined => {
    return roles.value.find(role => role.name === roleName);
  };

  const getUserById = (userId: number): UserWithRoles | undefined => {
    return users.value.find(user => user.id === userId);
  };

  // Actions
  const fetchRoles = async (): Promise<void> => {
    loading.value = true;
    error.value = null;
    try {
      console.log('📡 Buscando roles...');
      const response = await axiosInstance.get('/roles');
      console.log('📥 Roles recebidas:', response.data);
      roles.value = response.data;
      console.log('💾 Roles salvas na store:', roles.value);
    } catch (err: any) {
      console.error('❌ Erro ao buscar roles:', err);
      error.value = err.response?.data?.message || 'Erro ao carregar roles';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchMyPermissions = async (): Promise<void> => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axiosInstance.get<UserPermissions>('/me/permissions');
      myPermissions.value = response.data.permissions;
      myRoles.value = response.data.roles;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao carregar permissões';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchUsers = async (): Promise<void> => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axiosInstance.get('/admin/users');
      users.value = response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao carregar usuários';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchUserRoles = async (userId: number): Promise<Role[]> => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axiosInstance.get(`/users/${userId}/roles`);
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao carregar roles do usuário';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const assignRolesToUser = async (userId: number, roleIds: number[]): Promise<void> => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axiosInstance.post(`/users/${userId}/roles`, { role_ids: roleIds });
      
      // Atualizar o usuário na lista local
      const userIndex = users.value.findIndex(u => u.id === userId);
      if (userIndex !== -1) {
        users.value[userIndex].roles = response.data.roles;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao atribuir roles';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const removeRoleFromUser = async (userId: number, roleId: number): Promise<void> => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axiosInstance.delete(`/users/${userId}/roles/${roleId}`);
      
      // Atualizar o usuário na lista local
      const userIndex = users.value.findIndex(u => u.id === userId);
      if (userIndex !== -1) {
        users.value[userIndex].roles = response.data.roles;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao remover role';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchSystemStats = async (): Promise<void> => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axiosInstance.get('/admin/stats');
      systemStats.value = response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao carregar estatísticas';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const toggleUserStatus = async (userId: number): Promise<void> => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axiosInstance.patch(`/admin/users/${userId}/toggle-status`);
      
      // Atualizar o usuário na lista local
      const userIndex = users.value.findIndex(u => u.id === userId);
      if (userIndex !== -1) {
        users.value[userIndex].is_active = response.data.is_active;
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao alterar status do usuário';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Buscar logs de atividades
  const fetchActivityLogs = async (filters?: ActivityLogFilters): Promise<void> => {
    loading.value = true;
    error.value = null;
    try {
      const params = new URLSearchParams();
      
      if (filters?.action) params.append('action', filters.action);
      if (filters?.email) params.append('email', filters.email);
      if (filters?.date_from) params.append('date_from', filters.date_from);
      if (filters?.date_to) params.append('date_to', filters.date_to);
      if (filters?.per_page) params.append('per_page', filters.per_page.toString());
      if (filters?.page) params.append('page', filters.page.toString());

      const response = await axiosInstance.get<ActivityLogsResponse>(
        `/admin/activity-logs?${params.toString()}`
      );
      
      activityLogs.value = response.data.data;
      
      // Salvar metadados da paginação (tudo exceto data)
      const { data, ...metadata } = response.data;
      logsMetadata.value = metadata;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao carregar logs de atividades';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  // Initialize store
  const initialize = async (): Promise<void> => {
    await fetchMyPermissions();
    await fetchRoles();
  };

  return {
    // State
    roles,
    users,
    myPermissions,
    myRoles,
    systemStats,
    activityLogs,
    logsMetadata,
    loading,
    error,
    
    // Getters
    isAdmin,
    isFull,
    
    // Methods
    hasRole,
    hasAnyRole,
    hasPermission,
    hasAnyPermission,
    hasAllPermissions,
    getRoleById,
    getRoleByName,
    getUserById,
    
    // Actions
    fetchRoles,
    fetchMyPermissions,
    fetchUsers,
    fetchUserRoles,
    assignRolesToUser,
    removeRoleFromUser,
    fetchSystemStats,
    toggleUserStatus,
    fetchActivityLogs,
    initialize,
  };
});
