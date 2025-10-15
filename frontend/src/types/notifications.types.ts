/**
 * Types para Sistema de Notificações
 */

export interface NotificationSettings {
  id?: number;
  user_id: number;
  
  // Notificações de Vencimento
  notificar_vencimento: boolean;
  dias_antecedencia: number; // 1-30 dias
  
  // Notificações de Limite de Cartão
  notificar_limite_cartao: boolean;
  percentual_cartao: number; // 50-100%
  
  // Notificações de Estorno
  notificar_estorno: boolean;
  
  // Notificações de Desvio de Orçamento
  notificar_desvio_orcamento: boolean;
  
  created_at?: string;
  updated_at?: string;
}

export interface NotificationStats {
  total_enviadas: number;
  enviadas_hoje: number;
  enviadas_mes: number;
  por_tipo: {
    vencimento: number;
    limite_cartao: number;
    estorno: number;
    desvio_orcamento: number;
  };
  ultima_notificacao?: {
    tipo: string;
    data: string;
  };
}

export interface TestNotificationRequest {
  tipo: 'vencimento' | 'limite_cartao' | 'estorno' | 'desvio_orcamento';
}

export interface TestNotificationResponse {
  success: boolean;
  message: string;
  notification_sent: boolean;
  email_sent_to?: string;
}

// Constantes
export const NOTIFICATION_TYPES = {
  VENCIMENTO: 'vencimento',
  LIMITE_CARTAO: 'limite_cartao',
  ESTORNO: 'estorno',
  DESVIO_ORCAMENTO: 'desvio_orcamento',
} as const;

export type NotificationType = typeof NOTIFICATION_TYPES[keyof typeof NOTIFICATION_TYPES];

// Descrições dos tipos de notificação
export const NOTIFICATION_DESCRIPTIONS = {
  vencimento: 'Notificações sobre contas a vencer',
  limite_cartao: 'Alertas quando o limite do cartão estiver próximo',
  estorno: 'Avisos quando um lançamento for estornado',
  desvio_orcamento: 'Alertas sobre desvios no orçamento mensal',
} as const;

// Ícones dos tipos de notificação
export const NOTIFICATION_ICONS = {
  vencimento: 'mdi-calendar-alert',
  limite_cartao: 'mdi-credit-card-alert',
  estorno: 'mdi-cash-refund',
  desvio_orcamento: 'mdi-chart-line-variant',
} as const;

// Cores dos tipos de notificação
export const NOTIFICATION_COLORS = {
  vencimento: 'warning',
  limite_cartao: 'error',
  estorno: 'info',
  desvio_orcamento: 'orange',
} as const;

// Valores padrão
export const DEFAULT_SETTINGS: NotificationSettings = {
  user_id: 0,
  notificar_vencimento: true,
  dias_antecedencia: 3,
  notificar_limite_cartao: true,
  percentual_cartao: 80,
  notificar_estorno: true,
  notificar_desvio_orcamento: true,
};
