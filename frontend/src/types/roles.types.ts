/**
 * Types para Sistema de Roles e Permissões
 */

export interface Role {
  id: number;
  name: string;
  display_name: string;
  description: string;
  permissions: string[];
  created_at?: string;
  updated_at?: string;
  users_count?: number;
}

export interface Permission {
  name: string;
  description: string;
  category: 'lancamentos' | 'contas' | 'investimentos' | 'users' | 'roles' | 'system';
}

export interface UserRole {
  user_id: number;
  role_id: number;
  role: Role;
  assigned_at: string;
}

export interface UserWithRoles {
  id: number;
  name: string;
  email: string;
  email_verified_at?: string | null;
  created_at: string;
  updated_at: string;
  roles: Role[];
  is_active?: boolean;
  last_login?: string | null;
}

export interface RoleAssignmentRequest {
  user_id: number;
  role_ids: number[];
}

export interface UserPermissions {
  user_id: number;
  permissions: string[];
  roles: string[];
}

export interface SystemStats {
  total_users: number;
  active_users: number;
  inactive_users: number;
  users_by_role: {
    role_name: string;
    count: number;
  }[];
  total_lancamentos: number;
  lancamentos_this_month: number;
}

// Constantes de Roles (sincronizar com backend)
export const ROLE_NAMES = {
  USER: 'USER',
  TRADER: 'TRADER',
  USER_TRADER: 'USER_TRADER',
  ADMIN: 'ADMIN',
  FULL: 'FULL',
} as const;

export type RoleName = typeof ROLE_NAMES[keyof typeof ROLE_NAMES];

// Constantes de Permissões (sincronizar com backend)
export const PERMISSIONS = {
  // Lançamentos
  LANCAMENTOS_VIEW: 'lancamentos.view',
  LANCAMENTOS_CREATE: 'lancamentos.create',
  LANCAMENTOS_EDIT: 'lancamentos.edit',
  LANCAMENTOS_DELETE: 'lancamentos.delete',
  LANCAMENTOS_EXPORT: 'lancamentos.export',
  
  // Contas
  CONTAS_VIEW: 'contas.view',
  CONTAS_CREATE: 'contas.create',
  CONTAS_EDIT: 'contas.edit',
  CONTAS_DELETE: 'contas.delete',
  
  // Investimentos
  INVESTIMENTOS_VIEW: 'investimentos.view',
  INVESTIMENTOS_CREATE: 'investimentos.create',
  INVESTIMENTOS_EDIT: 'investimentos.edit',
  INVESTIMENTOS_DELETE: 'investimentos.delete',
  INVESTIMENTOS_TRADE: 'investimentos.trade',
  
  // Usuários
  USERS_VIEW: 'users.view',
  USERS_EDIT: 'users.edit',
  USERS_DELETE: 'users.delete',
  
  // Roles
  ROLES_VIEW: 'roles.view',
  ROLES_ASSIGN: 'roles.assign',
  ROLES_REVOKE: 'roles.revoke',
  
  // Sistema
  SYSTEM_CONFIG: 'system.config',
  SYSTEM_BACKUP: 'system.backup',
  SYSTEM_AUDIT: 'system.audit',
} as const;

export type PermissionName = typeof PERMISSIONS[keyof typeof PERMISSIONS];

// Mapeamento de cores para roles
export const ROLE_COLORS: Record<string, string> = {
  USER: 'primary',
  TRADER: 'info',
  USER_TRADER: 'success',
  ADMIN: 'warning',
  FULL: 'error',
};

// Mapeamento de ícones para roles
export const ROLE_ICONS: Record<string, string> = {
  USER: 'mdi-account',
  TRADER: 'mdi-chart-line',
  USER_TRADER: 'mdi-account-star',
  ADMIN: 'mdi-shield-account',
  FULL: 'mdi-crown',
};

// Descrições amigáveis das permissões
export const PERMISSION_DESCRIPTIONS: Record<string, string> = {
  'lancamentos.view': 'Visualizar lançamentos',
  'lancamentos.create': 'Criar novos lançamentos',
  'lancamentos.edit': 'Editar lançamentos',
  'lancamentos.delete': 'Excluir lançamentos',
  'lancamentos.export': 'Exportar lançamentos',
  'contas.view': 'Visualizar contas',
  'contas.create': 'Criar novas contas',
  'contas.edit': 'Editar contas',
  'contas.delete': 'Excluir contas',
  'investimentos.view': 'Visualizar investimentos',
  'investimentos.create': 'Criar novos investimentos',
  'investimentos.edit': 'Editar investimentos',
  'investimentos.delete': 'Excluir investimentos',
  'investimentos.trade': 'Realizar operações de trade',
  'users.view': 'Visualizar usuários',
  'users.edit': 'Editar usuários',
  'users.delete': 'Excluir usuários',
  'roles.view': 'Visualizar roles',
  'roles.assign': 'Atribuir roles aos usuários',
  'roles.revoke': 'Remover roles dos usuários',
  'system.config': 'Configurar sistema',
  'system.backup': 'Fazer backup do sistema',
  'system.audit': 'Visualizar auditoria',
};
