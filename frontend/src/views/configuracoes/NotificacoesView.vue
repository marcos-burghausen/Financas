<template>
  <v-container fluid class="notifications-view pa-6">
    <v-row>
      <v-col cols="12">
        <div class="d-flex align-center mb-6">
          <v-btn icon variant="text" @click="$router.back()" class="mr-2">
            <v-icon icon="mdi-arrow-left" />
          </v-btn>
          <div>
            <h1 class="text-h4 mb-1 d-flex align-center">
              <v-icon icon="mdi-bell-ring" size="36" class="mr-3" color="primary" />
              Configurações de Notificações
            </h1>
            <p class="text-subtitle-1 text-grey">
              Configure quando e como você deseja receber alertas por e-mail
            </p>
          </div>
        </div>
      </v-col>
    </v-row>

    <!-- Loading -->
    <v-row v-if="loading && !notificationsStore.settings.user_id">
      <v-col cols="12" class="text-center py-12">
        <v-progress-circular indeterminate color="primary" size="64" />
        <p class="mt-4 text-grey">Carregando configurações...</p>
      </v-col>
    </v-row>

    <!-- Conteúdo Principal -->
    <div v-else>
      <!-- Estatísticas -->
      <v-row v-if="stats" class="mb-6">
        <v-col cols="12" md="3">
          <v-card elevation="2" class="stat-card">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-grey">Total Enviadas</p>
                  <h2 class="text-h4">{{ stats.total_enviadas }}</h2>
                </div>
                <v-icon icon="mdi-email-check" size="48" color="primary" class="stat-icon" />
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="3">
          <v-card elevation="2" class="stat-card">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-grey">Hoje</p>
                  <h2 class="text-h4 text-success">{{ stats.enviadas_hoje }}</h2>
                </div>
                <v-icon icon="mdi-calendar-today" size="48" color="success" class="stat-icon" />
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
                  <h2 class="text-h4 text-info">{{ stats.enviadas_mes }}</h2>
                </div>
                <v-icon icon="mdi-calendar-month" size="48" color="info" class="stat-icon" />
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" md="3">
          <v-card elevation="2" class="stat-card">
            <v-card-text>
              <div class="d-flex align-center justify-space-between">
                <div>
                  <p class="text-caption text-grey">Última Notificação</p>
                  <h2 class="text-h6">
                    {{ stats.ultima_notificacao ? formatShortDate(stats.ultima_notificacao.data) : 'N/A' }}
                  </h2>
                </div>
                <v-icon icon="mdi-clock-outline" size="48" color="warning" class="stat-icon" />
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Configurações de Notificações -->
      <v-row>
        <v-col cols="12">
          <v-card elevation="3">
            <v-card-title class="text-h5 pa-6" style="background: rgb(var(--v-theme-surface));">
              <v-icon icon="mdi-cog" class="mr-2" />
              Configurações de Alertas
            </v-card-title>

            <v-divider />

            <v-card-text class="pa-6">
              <!-- Notificação de Vencimento -->
              <v-card elevation="1" class="mb-6">
                <v-card-text>
                  <div class="d-flex align-center justify-space-between mb-4">
                    <div class="d-flex align-center">
                      <v-icon
                        :icon="NOTIFICATION_ICONS.vencimento"
                        :color="NOTIFICATION_COLORS.vencimento"
                        size="32"
                        class="mr-4"
                      />
                      <div>
                        <h3 class="text-h6">Vencimento de Contas</h3>
                        <p class="text-caption text-grey">
                          {{ NOTIFICATION_DESCRIPTIONS.vencimento }}
                        </p>
                      </div>
                    </div>
                    <v-switch
                      v-model="settings.notificar_vencimento"
                      color="primary"
                      hide-details
                      :loading="loading"
                      @update:model-value="updateSetting('notificar_vencimento', $event)"
                    />
                  </div>

                  <v-expand-transition>
                    <div v-if="settings.notificar_vencimento">
                      <v-divider class="mb-4" />
                      <div class="px-4">
                        <p class="text-subtitle-2 mb-2">
                          Antecedência do alerta: <strong>{{ settings.dias_antecedencia }} dias</strong>
                        </p>
                        <v-slider
                          v-model="settings.dias_antecedencia"
                          :min="1"
                          :max="30"
                          :step="1"
                          color="warning"
                          thumb-label
                          :loading="loading"
                          @update:model-value="debouncedUpdateDias"
                        >
                          <template #prepend>
                            <span class="text-caption">1 dia</span>
                          </template>
                          <template #append>
                            <span class="text-caption">30 dias</span>
                          </template>
                        </v-slider>
                        <v-btn
                          variant="outlined"
                          color="warning"
                          size="small"
                          @click="testNotif('vencimento')"
                          :loading="testLoading.vencimento"
                        >
                          <v-icon icon="mdi-send" size="small" class="mr-2" />
                          Testar Notificação
                        </v-btn>
                      </div>
                    </div>
                  </v-expand-transition>
                </v-card-text>
              </v-card>

              <!-- Notificação de Limite de Cartão -->
              <v-card elevation="1" class="mb-6">
                <v-card-text>
                  <div class="d-flex align-center justify-space-between mb-4">
                    <div class="d-flex align-center">
                      <v-icon
                        :icon="NOTIFICATION_ICONS.limite_cartao"
                        :color="NOTIFICATION_COLORS.limite_cartao"
                        size="32"
                        class="mr-4"
                      />
                      <div>
                        <h3 class="text-h6">Limite de Cartão</h3>
                        <p class="text-caption text-grey">
                          {{ NOTIFICATION_DESCRIPTIONS.limite_cartao }}
                        </p>
                      </div>
                    </div>
                    <v-switch
                      v-model="settings.notificar_limite_cartao"
                      color="primary"
                      hide-details
                      :loading="loading"
                      @update:model-value="updateSetting('notificar_limite_cartao', $event)"
                    />
                  </div>

                  <v-expand-transition>
                    <div v-if="settings.notificar_limite_cartao">
                      <v-divider class="mb-4" />
                      <div class="px-4">
                        <p class="text-subtitle-2 mb-2">
                          Alertar quando atingir: <strong>{{ settings.percentual_cartao }}%</strong> do limite
                        </p>
                        <v-slider
                          v-model="settings.percentual_cartao"
                          :min="50"
                          :max="100"
                          :step="5"
                          color="error"
                          thumb-label
                          :loading="loading"
                          @update:model-value="debouncedUpdatePercentual"
                        >
                          <template #prepend>
                            <span class="text-caption">50%</span>
                          </template>
                          <template #append>
                            <span class="text-caption">100%</span>
                          </template>
                        </v-slider>
                        <v-btn
                          variant="outlined"
                          color="error"
                          size="small"
                          @click="testNotif('limite_cartao')"
                          :loading="testLoading.limite_cartao"
                        >
                          <v-icon icon="mdi-send" size="small" class="mr-2" />
                          Testar Notificação
                        </v-btn>
                      </div>
                    </div>
                  </v-expand-transition>
                </v-card-text>
              </v-card>

              <!-- Notificação de Estorno -->
              <v-card elevation="1" class="mb-6">
                <v-card-text>
                  <div class="d-flex align-center justify-space-between">
                    <div class="d-flex align-center">
                      <v-icon
                        :icon="NOTIFICATION_ICONS.estorno"
                        :color="NOTIFICATION_COLORS.estorno"
                        size="32"
                        class="mr-4"
                      />
                      <div>
                        <h3 class="text-h6">Estorno de Lançamentos</h3>
                        <p class="text-caption text-grey">
                          {{ NOTIFICATION_DESCRIPTIONS.estorno }}
                        </p>
                      </div>
                    </div>
                    <div class="d-flex align-center gap-2">
                      <v-btn
                        v-if="settings.notificar_estorno"
                        variant="outlined"
                        color="info"
                        size="small"
                        @click="testNotif('estorno')"
                        :loading="testLoading.estorno"
                        class="mr-2"
                      >
                        <v-icon icon="mdi-send" size="small" class="mr-2" />
                        Testar
                      </v-btn>
                      <v-switch
                        v-model="settings.notificar_estorno"
                        color="primary"
                        hide-details
                        :loading="loading"
                        @update:model-value="updateSetting('notificar_estorno', $event)"
                      />
                    </div>
                  </div>
                </v-card-text>
              </v-card>

              <!-- Notificação de Desvio de Orçamento -->
              <v-card elevation="1" class="mb-4">
                <v-card-text>
                  <div class="d-flex align-center justify-space-between">
                    <div class="d-flex align-center">
                      <v-icon
                        :icon="NOTIFICATION_ICONS.desvio_orcamento"
                        :color="NOTIFICATION_COLORS.desvio_orcamento"
                        size="32"
                        class="mr-4"
                      />
                      <div>
                        <h3 class="text-h6">Desvio de Orçamento</h3>
                        <p class="text-caption text-grey">
                          {{ NOTIFICATION_DESCRIPTIONS.desvio_orcamento }}
                        </p>
                      </div>
                    </div>
                    <div class="d-flex align-center gap-2">
                      <v-btn
                        v-if="settings.notificar_desvio_orcamento"
                        variant="outlined"
                        color="orange"
                        size="small"
                        @click="testNotif('desvio_orcamento')"
                        :loading="testLoading.desvio_orcamento"
                        class="mr-2"
                      >
                        <v-icon icon="mdi-send" size="small" class="mr-2" />
                        Testar
                      </v-btn>
                      <v-switch
                        v-model="settings.notificar_desvio_orcamento"
                        color="primary"
                        hide-details
                        :loading="loading"
                        @update:model-value="updateSetting('notificar_desvio_orcamento', $event)"
                      />
                    </div>
                  </div>
                </v-card-text>
              </v-card>

              <!-- Info -->
              <v-alert type="info" variant="tonal" density="compact" class="mt-6">
                <div class="d-flex align-center">
                  <v-icon icon="mdi-information" class="mr-2" />
                  <div>
                    <strong>Dica:</strong> As notificações são enviadas automaticamente pelo sistema
                    quando as condições configuradas são atingidas. Use os botões "Testar" para
                    verificar se está recebendo os e-mails corretamente.
                  </div>
                </div>
              </v-alert>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Seção de Testes Rápidos -->
      <v-row class="mt-6">
        <v-col cols="12">
          <v-card elevation="3">
            <v-card-title class="text-h5 pa-6" style="background: rgb(var(--v-theme-surface));">
              <v-icon icon="mdi-flask-outline" class="mr-2" />
              Testes Rápidos de E-mail
            </v-card-title>
            <v-card-subtitle class="pa-6 pt-2">
              Envie e-mails de teste para verificar se as notificações estão funcionando corretamente
            </v-card-subtitle>
            
            <v-card-text class="pa-6">
              <v-row>
                <!-- Teste: Vencimento -->
                <v-col cols="12" md="6" lg="3">
                  <v-card 
                    elevation="2" 
                    class="test-card" 
                    :class="{ 'disabled-card': !settings.notificar_vencimento }"
                  >
                    <v-card-text class="text-center pa-6">
                      <v-avatar size="80" color="warning" class="mb-4">
                        <v-icon icon="mdi-calendar-alert" size="48" color="white" />
                      </v-avatar>
                      <h3 class="text-h6 mb-2">Vencimento de Conta</h3>
                      <p class="text-caption text-grey mb-4">
                        Teste o alerta de contas próximas ao vencimento
                      </p>
                      <v-btn
                        block
                        color="warning"
                        variant="flat"
                        @click="testNotif('vencimento')"
                        :loading="testLoading.vencimento"
                        :disabled="!settings.notificar_vencimento"
                      >
                        <v-icon icon="mdi-email-send" class="mr-2" />
                        Enviar Teste
                      </v-btn>
                      <p v-if="!settings.notificar_vencimento" class="text-caption text-error mt-2">
                        ⚠️ Ative a notificação acima para testar
                      </p>
                    </v-card-text>
                  </v-card>
                </v-col>

                <!-- Teste: Limite de Cartão -->
                <v-col cols="12" md="6" lg="3">
                  <v-card 
                    elevation="2" 
                    class="test-card"
                    :class="{ 'disabled-card': !settings.notificar_limite_cartao }"
                  >
                    <v-card-text class="text-center pa-6">
                      <v-avatar size="80" color="error" class="mb-4">
                        <v-icon icon="mdi-credit-card-alert" size="48" color="white" />
                      </v-avatar>
                      <h3 class="text-h6 mb-2">Limite de Cartão</h3>
                      <p class="text-caption text-grey mb-4">
                        Teste o alerta quando cartão atinge limite
                      </p>
                      <v-btn
                        block
                        color="error"
                        variant="flat"
                        @click="testNotif('limite_cartao')"
                        :loading="testLoading.limite_cartao"
                        :disabled="!settings.notificar_limite_cartao"
                      >
                        <v-icon icon="mdi-email-send" class="mr-2" />
                        Enviar Teste
                      </v-btn>
                      <p v-if="!settings.notificar_limite_cartao" class="text-caption text-error mt-2">
                        ⚠️ Ative a notificação acima para testar
                      </p>
                    </v-card-text>
                  </v-card>
                </v-col>

                <!-- Teste: Estorno -->
                <v-col cols="12" md="6" lg="3">
                  <v-card 
                    elevation="2" 
                    class="test-card"
                    :class="{ 'disabled-card': !settings.notificar_estorno }"
                  >
                    <v-card-text class="text-center pa-6">
                      <v-avatar size="80" color="info" class="mb-4">
                        <v-icon icon="mdi-undo-variant" size="48" color="white" />
                      </v-avatar>
                      <h3 class="text-h6 mb-2">Estorno</h3>
                      <p class="text-caption text-grey mb-4">
                        Teste o alerta de estorno de lançamento
                      </p>
                      <v-btn
                        block
                        color="info"
                        variant="flat"
                        @click="testNotif('estorno')"
                        :loading="testLoading.estorno"
                        :disabled="!settings.notificar_estorno"
                      >
                        <v-icon icon="mdi-email-send" class="mr-2" />
                        Enviar Teste
                      </v-btn>
                      <p v-if="!settings.notificar_estorno" class="text-caption text-error mt-2">
                        ⚠️ Ative a notificação acima para testar
                      </p>
                    </v-card-text>
                  </v-card>
                </v-col>

                <!-- Teste: Desvio de Orçamento -->
                <v-col cols="12" md="6" lg="3">
                  <v-card 
                    elevation="2" 
                    class="test-card"
                    :class="{ 'disabled-card': !settings.notificar_desvio_orcamento }"
                  >
                    <v-card-text class="text-center pa-6">
                      <v-avatar size="80" color="orange" class="mb-4">
                        <v-icon icon="mdi-alert-octagon" size="48" color="white" />
                      </v-avatar>
                      <h3 class="text-h6 mb-2">Desvio de Orçamento</h3>
                      <p class="text-caption text-grey mb-4">
                        Teste o alerta de gastos acima do planejado
                      </p>
                      <v-btn
                        block
                        color="orange"
                        variant="flat"
                        @click="testNotif('desvio_orcamento')"
                        :loading="testLoading.desvio_orcamento"
                        :disabled="!settings.notificar_desvio_orcamento"
                      >
                        <v-icon icon="mdi-email-send" class="mr-2" />
                        Enviar Teste
                      </v-btn>
                      <p v-if="!settings.notificar_desvio_orcamento" class="text-caption text-error mt-2">
                        ⚠️ Ative a notificação acima para testar
                      </p>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>

              <!-- Informações sobre os testes -->
              <v-row class="mt-4">
                <v-col cols="12">
                  <v-alert type="success" variant="tonal" density="comfortable">
                    <v-alert-title>
                      <v-icon icon="mdi-check-circle" class="mr-2" />
                      Como funcionam os testes?
                    </v-alert-title>
                    <div class="mt-2">
                      <p class="mb-2">
                        Ao clicar em "Enviar Teste", um e-mail será enviado imediatamente para
                        <strong>{{ settings.user_email || 'seu e-mail cadastrado' }}</strong>:
                      </p>
                      <ul class="ml-4">
                        <li>📧 <strong>Vencimento</strong>: Usa uma despesa pendente real do seu cadastro</li>
                        <li>💳 <strong>Limite de Cartão</strong>: Usa um cartão cadastrado e calcula uso real</li>
                        <li>↩️ <strong>Estorno</strong>: Usa um estorno registrado no sistema</li>
                        <li>📊 <strong>Orçamento</strong>: Simula um desvio com dados de exemplo</li>
                      </ul>
                      <p class="mt-3">
                        <v-icon icon="mdi-clock-outline" size="small" class="mr-1" />
                        <small>
                          <strong>Dica:</strong> Se você não possui o tipo de lançamento necessário
                          (ex: despesa pendente, cartão, estorno), o sistema retornará uma mensagem
                          orientando o que criar primeiro.
                        </small>
                      </p>
                    </div>
                  </v-alert>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>

    <!-- Snackbar -->
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" :timeout="4000">
      {{ snackbar.message }}
      <template #actions>
        <v-btn variant="text" @click="snackbar.show = false">Fechar</v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>

<script setup lang="ts">
import { useNotificationsStore } from '@/store/notifications';
import {
  NOTIFICATION_COLORS,
  NOTIFICATION_DESCRIPTIONS,
  NOTIFICATION_ICONS,
  type NotificationType,
} from '@/types/notifications.types';
import { format } from 'date-fns';
import { ptBR } from 'date-fns/locale';
import { computed, onMounted, ref } from 'vue';

// Store
const notificationsStore = useNotificationsStore();

// State
const snackbar = ref({
  show: false,
  message: '',
  color: 'success',
});

// Debounce timers
let diasTimer: ReturnType<typeof setTimeout>;
let percentualTimer: ReturnType<typeof setTimeout>;

// Computed
const loading = computed(() => notificationsStore.loading);
const settings = computed(() => notificationsStore.settings);
const stats = computed(() => notificationsStore.stats);
const testLoading = computed(() => notificationsStore.testLoading);

// Methods
const loadData = async () => {
  try {
    await notificationsStore.fetchSettings();
    await notificationsStore.fetchStats();
  } catch (error) {
    console.error('Erro ao carregar dados:', error);
    // Não mostrar erro se for 404 (configurações não criadas ainda)
  }
};

const updateSetting = async (key: string, value: boolean) => {
  try {
    await notificationsStore.updateSetting(key as any, value);
    showSnackbar('Configuração atualizada com sucesso', 'success');
  } catch (error) {
    showSnackbar('Erro ao atualizar configuração', 'error');
  }
};

const debouncedUpdateDias = (value: number) => {
  clearTimeout(diasTimer);
  diasTimer = setTimeout(async () => {
    try {
      await notificationsStore.updateSetting('dias_antecedencia', value);
      showSnackbar('Antecedência atualizada com sucesso', 'success');
    } catch (error) {
      showSnackbar('Erro ao atualizar antecedência', 'error');
    }
  }, 1000);
};

const debouncedUpdatePercentual = (value: number) => {
  clearTimeout(percentualTimer);
  percentualTimer = setTimeout(async () => {
    try {
      await notificationsStore.updateSetting('percentual_cartao', value);
      showSnackbar('Percentual atualizado com sucesso', 'success');
    } catch (error) {
      showSnackbar('Erro ao atualizar percentual', 'error');
    }
  }, 1000);
};

const testNotif = async (tipo: NotificationType) => {
  try {
    const response = await notificationsStore.testNotification(tipo);
    
    if (response.notification_sent) {
      showSnackbar(
        `E-mail de teste enviado para ${response.email_sent_to}! Verifique sua caixa de entrada.`,
        'success'
      );
    } else {
      showSnackbar(response.message || 'Notificação de teste enviada', 'info');
    }
    
    // Recarregar stats após enviar teste
    await notificationsStore.fetchStats();
  } catch (error: any) {
    showSnackbar(
      error.response?.data?.message || 'Erro ao enviar notificação de teste',
      'error'
    );
  }
};

const formatShortDate = (dateString: string): string => {
  try {
    return format(new Date(dateString), "dd/MM 'às' HH:mm", { locale: ptBR });
  } catch {
    return 'N/A';
  }
};

const showSnackbar = (message: string, color: string) => {
  snackbar.value = { show: true, message, color };
};

// Lifecycle
onMounted(() => {
  loadData();
});
</script>

<style scoped>
.notifications-view {
  min-height: 100vh;
  background: rgb(var(--v-theme-background));
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

.gap-2 {
  gap: 8px;
}

.test-card {
  transition: all 0.3s ease;
  height: 100%;
}

.test-card:hover:not(.disabled-card) {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}

.disabled-card {
  opacity: 0.6;
  background-color: rgba(0,0,0,0.02);
}

.disabled-card .v-avatar {
  opacity: 0.5;
}
</style>
