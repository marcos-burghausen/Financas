import axiosInstance from '@/services/http';
import type {
    NotificationSettings,
    NotificationStats,
    NotificationType,
    TestNotificationResponse
} from '@/types/notifications.types';
import { DEFAULT_SETTINGS } from '@/types/notifications.types';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useNotificationsStore = defineStore('notifications', () => {
  // State
  const settings = ref<NotificationSettings>({ ...DEFAULT_SETTINGS });
  const stats = ref<NotificationStats | null>(null);
  const loading = ref(false);
  const error = ref<string | null>(null);
  const testLoading = ref<Record<NotificationType, boolean>>({
    vencimento: false,
    limite_cartao: false,
    estorno: false,
    desvio_orcamento: false,
  });

  // Actions
  const fetchSettings = async (): Promise<void> => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axiosInstance.get<NotificationSettings>('/notification-settings');
      settings.value = { ...DEFAULT_SETTINGS, ...response.data };
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao carregar configurações';
      // Se não houver configurações, usar defaults
      if (err.response?.status === 404) {
        settings.value = { ...DEFAULT_SETTINGS };
      }
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const saveSettings = async (newSettings: Partial<NotificationSettings>): Promise<void> => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axiosInstance.post<NotificationSettings>(
        '/notification-settings',
        newSettings
      );
      settings.value = { ...DEFAULT_SETTINGS, ...response.data };
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao salvar configurações';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const fetchStats = async (): Promise<void> => {
    loading.value = true;
    error.value = null;
    try {
      const response = await axiosInstance.get<NotificationStats>('/notification-settings/stats');
      stats.value = response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao carregar estatísticas';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const testNotification = async (tipo: NotificationType): Promise<TestNotificationResponse> => {
    testLoading.value[tipo] = true;
    error.value = null;
    try {
      const endpoint = `/notification-settings/test-${tipo.replace('_', '-')}`;
      const response = await axiosInstance.post<TestNotificationResponse>(endpoint);
      return response.data;
    } catch (err: any) {
      error.value = err.response?.data?.message || `Erro ao testar notificação de ${tipo}`;
      throw err;
    } finally {
      testLoading.value[tipo] = false;
    }
  };

  const updateSetting = async (
    key: keyof NotificationSettings,
    value: boolean | number
  ): Promise<void> => {
    const updatedSettings = { ...settings.value, [key]: value };
    await saveSettings(updatedSettings);
  };

  // Initialize store
  const initialize = async (): Promise<void> => {
    try {
      await fetchSettings();
    } catch (error) {
      // Ignorar erro se não houver configurações ainda
      console.warn('Configurações de notificação não encontradas, usando defaults');
    }
  };

  return {
    // State
    settings,
    stats,
    loading,
    error,
    testLoading,

    // Actions
    fetchSettings,
    saveSettings,
    fetchStats,
    testNotification,
    updateSetting,
    initialize,
  };
});
