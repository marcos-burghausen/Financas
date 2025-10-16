<template>
  <v-container fluid class="perfil-view pa-6">
    <!-- Header -->
    <v-row>
      <v-col cols="12">
        <v-card elevation="0" class="mb-6">
          <v-card-text>
            <div class="d-flex align-center">
              <v-btn
                icon="mdi-arrow-left"
                variant="text"
                color="primary"
                @click="$router.push({ name: 'dashboard' })"
                class="mr-2"
              />
              <v-avatar size="80" color="primary" class="mr-4">
                <v-icon size="50" color="white">mdi-account</v-icon>
              </v-avatar>
              <div>
                <h2 class="text-h4 mb-2">Meu Perfil</h2>
                <p class="text-subtitle-1 text-grey">
                  Gerencie suas informações pessoais
                </p>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Conteúdo -->
    <v-row>
      <!-- Card de Informações -->
      <v-col cols="12" md="8">
        <v-card elevation="2">
          <v-card-title class="bg-primary">
            <v-icon left>mdi-account-edit</v-icon>
            Informações Pessoais
          </v-card-title>
          
          <v-card-text class="pt-6">
            <v-form ref="formRef" v-model="formValid">
              <v-row>
                <!-- Nome -->
                <v-col cols="12">
                  <v-text-field
                    v-model="userData.name"
                    label="Nome Completo"
                    prepend-inner-icon="mdi-account"
                    :rules="[rules.required]"
                    :readonly="!editing"
                    variant="outlined"
                    density="comfortable"
                  />
                </v-col>

                <!-- Email -->
                <v-col cols="12">
                  <v-text-field
                    v-model="userData.email"
                    label="E-mail"
                    prepend-inner-icon="mdi-email"
                    :rules="[rules.required, rules.email]"
                    :readonly="!editing"
                    variant="outlined"
                    density="comfortable"
                  />
                </v-col>

                <!-- Data de Criação -->
                <v-col cols="12" md="6">
                  <v-text-field
                    :model-value="formatDate(userData.created_at)"
                    label="Membro desde"
                    prepend-inner-icon="mdi-calendar"
                    readonly
                    variant="outlined"
                    density="comfortable"
                  />
                </v-col>

                <!-- Roles -->
                <v-col cols="12" md="6">
                  <v-text-field
                    :model-value="userRoles"
                    label="Permissões"
                    prepend-inner-icon="mdi-shield-account"
                    readonly
                    variant="outlined"
                    density="comfortable"
                  />
                </v-col>
              </v-row>

              <!-- Botões de Ação -->
              <v-row class="mt-2">
                <v-col cols="12" class="d-flex justify-end gap-2">
                  <v-btn
                    v-if="!editing"
                    color="primary"
                    prepend-icon="mdi-pencil"
                    @click="editing = true"
                  >
                    Editar
                  </v-btn>
                  
                  <template v-else>
                    <v-btn
                      color="grey"
                      variant="outlined"
                      prepend-icon="mdi-close"
                      @click="cancelEdit"
                    >
                      Cancelar
                    </v-btn>
                    <v-btn
                      color="success"
                      prepend-icon="mdi-content-save"
                      :loading="saving"
                      :disabled="!formValid"
                      @click="saveProfile"
                    >
                      Salvar
                    </v-btn>
                  </template>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>

        <!-- Card de Alterar Senha -->
        <v-card elevation="2" class="mt-4">
          <v-card-title class="bg-warning">
            <v-icon left>mdi-lock-reset</v-icon>
            Alterar Senha
          </v-card-title>
          
          <v-card-text class="pt-6">
            <v-form ref="passwordFormRef" v-model="passwordFormValid">
              <v-row>
                <!-- Senha Atual -->
                <v-col cols="12">
                  <v-text-field
                    v-model="passwordData.current_password"
                    label="Senha Atual"
                    :type="showCurrentPassword ? 'text' : 'password'"
                    prepend-inner-icon="mdi-lock"
                    :append-inner-icon="showCurrentPassword ? 'mdi-eye-off' : 'mdi-eye'"
                    :rules="[rules.required]"
                    variant="outlined"
                    density="comfortable"
                    @click:append-inner="showCurrentPassword = !showCurrentPassword"
                  />
                </v-col>

                <!-- Nova Senha -->
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="passwordData.new_password"
                    label="Nova Senha"
                    :type="showNewPassword ? 'text' : 'password'"
                    prepend-inner-icon="mdi-lock-plus"
                    :append-inner-icon="showNewPassword ? 'mdi-eye-off' : 'mdi-eye'"
                    :rules="[rules.required, rules.minLength(8)]"
                    variant="outlined"
                    density="comfortable"
                    @click:append-inner="showNewPassword = !showNewPassword"
                  />
                </v-col>

                <!-- Confirmar Nova Senha -->
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="passwordData.new_password_confirmation"
                    label="Confirmar Nova Senha"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    prepend-inner-icon="mdi-lock-check"
                    :append-inner-icon="showConfirmPassword ? 'mdi-eye-off' : 'mdi-eye'"
                    :rules="[rules.required, rules.passwordMatch]"
                    variant="outlined"
                    density="comfortable"
                    @click:append-inner="showConfirmPassword = !showConfirmPassword"
                  />
                </v-col>
              </v-row>

              <!-- Botão Alterar Senha -->
              <v-row>
                <v-col cols="12" class="d-flex justify-end">
                  <v-btn
                    color="warning"
                    prepend-icon="mdi-lock-reset"
                    :loading="changingPassword"
                    :disabled="!passwordFormValid"
                    @click="changePassword"
                  >
                    Alterar Senha
                  </v-btn>
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Sidebar com Estatísticas -->
      <v-col cols="12" md="4">
        <!-- Card de Avatar -->
        <v-card elevation="2" class="mb-4">
          <v-card-text class="text-center">
            <v-avatar size="150" color="primary" class="mb-4">
              <v-icon size="100" color="white">mdi-account</v-icon>
            </v-avatar>
            <h3 class="text-h5 mb-2">{{ userData.name }}</h3>
            <p class="text-subtitle-2 text-grey">{{ userData.email }}</p>
            
            <v-chip
              v-for="role in rolesStore.myRoles"
              :key="role"
              :color="getRoleColor(role)"
              size="small"
              class="ma-1"
            >
              {{ role }}
            </v-chip>
          </v-card-text>
        </v-card>

        <!-- Card de Preferências de Tema -->
        <v-card elevation="2" class="mb-4">
          <v-card-title class="bg-primary">
            <v-icon left>mdi-theme-light-dark</v-icon>
            Aparência
          </v-card-title>
          
          <v-card-text class="pt-4">
            <div class="d-flex flex-column gap-3">
              <p class="text-subtitle-2 text-grey mb-0">
                Escolha o tema da aplicação
              </p>
              
              <v-btn-toggle
                :model-value="themeStore.themeName"
                @update:model-value="changeTheme"
                mandatory
                divided
                variant="outlined"
                color="primary"
                class="theme-toggle"
              >
                <v-btn value="light" class="flex-grow-1">
                  <v-icon start>mdi-white-balance-sunny</v-icon>
                  Claro
                </v-btn>
                <v-btn value="dark" class="flex-grow-1">
                  <v-icon start>mdi-moon-waning-crescent</v-icon>
                  Escuro
                </v-btn>
              </v-btn-toggle>

              <v-alert
                :color="themeStore.isDark ? 'info' : 'warning'"
                variant="tonal"
                density="compact"
                class="mt-2"
              >
                <template #prepend>
                  <v-icon>mdi-information</v-icon>
                </template>
                Tema {{ themeStore.isDark ? 'escuro' : 'claro' }} ativado
              </v-alert>
            </div>
          </v-card-text>
        </v-card>

        <!-- Card de Estatísticas -->
        <v-card elevation="2">
          <v-card-title class="bg-info">
            <v-icon left>mdi-chart-box</v-icon>
            Estatísticas
          </v-card-title>
          
          <v-card-text>
            <v-list>
              <v-list-item>
                <template v-slot:prepend>
                  <v-icon color="success">mdi-bank</v-icon>
                </template>
                <v-list-item-title>Contas Cadastradas</v-list-item-title>
                <v-list-item-subtitle>{{ stats.contas || 0 }}</v-list-item-subtitle>
              </v-list-item>

              <v-divider />

              <v-list-item>
                <template v-slot:prepend>
                  <v-icon color="green">mdi-arrow-up-bold</v-icon>
                </template>
                <v-list-item-title>Receitas</v-list-item-title>
                <v-list-item-subtitle>{{ stats.receitas || 0 }}</v-list-item-subtitle>
              </v-list-item>

              <v-divider />

              <v-list-item>
                <template v-slot:prepend>
                  <v-icon color="red">mdi-arrow-down-bold</v-icon>
                </template>
                <v-list-item-title>Despesas</v-list-item-title>
                <v-list-item-subtitle>{{ stats.despesas || 0 }}</v-list-item-subtitle>
              </v-list-item>

              <v-divider />

              <v-list-item>
                <template v-slot:prepend>
                  <v-icon color="primary">mdi-calendar</v-icon>
                </template>
                <v-list-item-title>Último Acesso</v-list-item-title>
                <v-list-item-subtitle>Hoje</v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Snackbar -->
    <v-snackbar
      v-model="snackbar"
      :color="snackbarColor"
      :timeout="4000"
      location="top"
    >
      {{ snackbarMessage }}
      <template v-slot:actions>
        <v-btn variant="text" @click="snackbar = false">Fechar</v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>

<script setup lang="ts">
import axiosInstance from '@/services/http';
import { useAuthStore } from '@/store/auth';
import { useRolesStore } from '@/store/roles';
import { useThemeStore } from '@/store/theme';
import { computed, onMounted, reactive, ref } from 'vue';
import { useTheme } from 'vuetify';

const authStore = useAuthStore();
const rolesStore = useRolesStore();
const themeStore = useThemeStore();
const vuetifyTheme = useTheme();

// Refs
const editing = ref(false);
const saving = ref(false);
const changingPassword = ref(false);
const formValid = ref(false);
const passwordFormValid = ref(false);
const formRef = ref();
const passwordFormRef = ref();

// Password visibility
const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

// Snackbar
const snackbar = ref(false);
const snackbarMessage = ref('');
const snackbarColor = ref('success');

// User data
const userData = reactive({
  name: '',
  email: '',
  created_at: '',
});

const originalUserData = ref({
  name: '',
  email: '',
});

// Password data
const passwordData = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
});

// Stats
const stats = reactive({
  contas: 0,
  receitas: 0,
  despesas: 0,
});

// Computed
const userRoles = computed(() => {
  return rolesStore.myRoles.join(', ') || 'Nenhuma';
});

// Validation rules
const rules = {
  required: (v: any) => !!v || 'Campo obrigatório',
  email: (v: string) => /.+@.+\..+/.test(v) || 'E-mail inválido',
  minLength: (min: number) => (v: string) => 
    (v && v.length >= min) || `Mínimo ${min} caracteres`,
  passwordMatch: (v: string) => 
    v === passwordData.new_password || 'As senhas não coincidem',
};

// Methods
const formatDate = (date: string) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  });
};

const getRoleColor = (role: string) => {
  const colors: Record<string, string> = {
    FULL: 'purple',
    ADMIN: 'red',
    TRADER: 'green',
    USER_TRADER: 'blue',
    USER: 'grey',
  };
  return colors[role] || 'grey';
};

const showSnackbar = (message: string, color: string = 'success') => {
  snackbarMessage.value = message;
  snackbarColor.value = color;
  snackbar.value = true;
};

const loadUserData = async () => {
  try {
    const response = await axiosInstance.get('/user');
    userData.name = response.data.name;
    userData.email = response.data.email;
    userData.created_at = response.data.created_at;
    
    // Salvar dados originais
    originalUserData.value = {
      name: response.data.name,
      email: response.data.email,
    };
  } catch (error: any) {
    console.error('Erro ao carregar dados:', error);
    showSnackbar('Erro ao carregar dados do usuário', 'error');
  }
};

const loadStats = async () => {
  try {
    const response = await axiosInstance.get('/user/stats');
    stats.contas = response.data.contas || 0;
    stats.receitas = response.data.receitas || 0;
    stats.despesas = response.data.despesas || 0;
  } catch (error: any) {
    console.error('Erro ao carregar estatísticas:', error);
    // Manter valores zerados em caso de erro
  }
};

const cancelEdit = () => {
  editing.value = false;
  userData.name = originalUserData.value.name;
  userData.email = originalUserData.value.email;
};

const saveProfile = async () => {
  if (!formValid.value) return;
  
  saving.value = true;
  try {
    await axiosInstance.put('/user/profile', {
      name: userData.name,
      email: userData.email,
    });
    
    // Atualizar dados originais
    originalUserData.value = {
      name: userData.name,
      email: userData.email,
    };
    
    editing.value = false;
    showSnackbar('Perfil atualizado com sucesso!', 'success');
  } catch (error: any) {
    console.error('Erro ao salvar perfil:', error);
    showSnackbar(
      error.response?.data?.message || 'Erro ao atualizar perfil',
      'error'
    );
  } finally {
    saving.value = false;
  }
};

const changePassword = async () => {
  if (!passwordFormValid.value) return;
  
  changingPassword.value = true;
  try {
    await axiosInstance.put('/user/password', {
      current_password: passwordData.current_password,
      new_password: passwordData.new_password,
      new_password_confirmation: passwordData.new_password_confirmation,
    });
    
    // Limpar campos
    passwordData.current_password = '';
    passwordData.new_password = '';
    passwordData.new_password_confirmation = '';
    
    showSnackbar('Senha alterada com sucesso!', 'success');
  } catch (error: any) {
    console.error('Erro ao alterar senha:', error);
    showSnackbar(
      error.response?.data?.message || 'Erro ao alterar senha',
      'error'
    );
  } finally {
    changingPassword.value = false;
  }
};

const changeTheme = (theme: 'light' | 'dark') => {
  themeStore.setTheme(theme);
  vuetifyTheme.global.name.value = theme;
  showSnackbar(`Tema ${theme === 'dark' ? 'escuro' : 'claro'} ativado!`, 'info');
};

// Lifecycle
onMounted(async () => {
  // Aplicar tema salvo
  vuetifyTheme.global.name.value = themeStore.themeName;
  
  await loadUserData();
  await loadStats();
  
  // Carregar roles se necessário
  if (rolesStore.myRoles.length === 0) {
    await rolesStore.fetchMyPermissions();
  }
});
</script>

<style scoped>
.perfil-view {
  max-width: 1400px;
  margin: 0 auto;
}

.gap-2 {
  gap: 8px;
}

.v-list-item {
  padding: 12px 16px;
}

.v-card-title {
  color: white;
  font-weight: 600;
}

.bg-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-warning {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.bg-info {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.theme-toggle {
  width: 100%;
}

.theme-toggle .v-btn {
  min-height: 48px;
}
</style>
