<template>
  <div class="login-wrapper">
    <v-container fluid class="login-container">
      <v-row class="align-center justify-center">
        <v-col cols="12" sm="10" md="8" lg="6" style="max-width: 600px;">
          <!-- Card Principal -->
          <v-card elevation="8" rounded="lg" class="login-card">
            <!-- Logo Section -->
            <div class="logo-section text-center pa-8 bg-primary">
              <div class="mb-4">
                <v-icon icon="mdi-finance" size="64" color="white" />
              </div>
              <h1 class="text-h4 text-white font-weight-bold mb-2">MrFinanças</h1>
              <p class="text-white text-opacity-70">Gerencie suas finanças com inteligência</p>
            </div>

            <!-- Formulário -->
            <v-card-text class="pa-8">
              <p class="text-subtitle-2 text-medium-emphasis mb-6">
                Bem-vindo de volta! Digite suas credenciais para continuar
              </p>

              <v-form ref="form" @submit.prevent="handleLogin">
                <!-- Email -->
                <v-text-field
                  v-model="formData.email"
                  label="Email"
                  type="email"
                  prepend-inner-icon="mdi-email"
                  variant="outlined"
                  density="compact"
                  :rules="[
                    v => !!v || 'Email é obrigatório',
                    v => /.+@.+\..+/.test(v) || 'Email deve ser válido'
                  ]"
                  class="mb-4"
                />

                <!-- Senha -->
                <v-text-field
                  v-model="formData.password"
                  :label="'Senha'"
                  :type="showPassword ? 'text' : 'password'"
                  prepend-inner-icon="mdi-lock"
                  :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                  @click:append-inner="showPassword = !showPassword"
                  variant="outlined"
                  density="compact"
                  :rules="[v => !!v || 'Senha é obrigatória']"
                  class="mb-2"
                />

                <!-- Lembrar-se -->
                <div class="d-flex justify-space-between align-center mb-6">
                  <v-checkbox
                    v-model="formData.remember"
                    label="Lembrar-me nesta máquina"
                    density="compact"
                  />
                  <v-btn
                    to="/"
                    variant="text"
                    size="small"
                    color="primary"
                  >
                    Esqueceu a senha?
                  </v-btn>
                </div>

                <!-- Botão Login -->
                <v-btn
                  type="submit"
                  color="primary"
                  size="large"
                  block
                  class="mb-4"
                  :loading="loading"
                >
                  <v-icon icon="mdi-login" start />
                  Entrar
                </v-btn>
              </v-form>

              <!-- Divider -->
              <div class="d-flex align-center gap-2 my-6">
                <v-divider />
                <span class="text-caption text-medium-emphasis">ou</span>
                <v-divider />
              </div>

              <!-- Botão Cadastro -->
              <v-btn
                to="/cadastro"
                variant="outlined"
                color="primary"
                size="large"
                block
              >
                <v-icon icon="mdi-account-plus" start />
                Criar nova conta
              </v-btn>
            </v-card-text>

            <!-- Footer with Terms -->
            <TermsFooter />
          </v-card>

          <!-- Indicadores no Fundo -->
          <!-- <div class="text-center mt-6">
            <v-chip
              size="small"
              variant="flat"
              color="success"
              text-color="white"
              class="mr-2"
            >
              <v-icon icon="mdi-check-circle" start size="16" />
              Sistema Online
            </v-chip>
            <v-chip
              size="small"
              variant="flat"
              color="info"
              text-color="white"
            >
              <v-icon icon="mdi-lock-outline" start size="16" />
              Conexão Segura
            </v-chip>
          </div> -->
        </v-col>
      </v-row>
    </v-container>

    <!-- Loading Overlay - Redirecionamento -->
    <v-overlay
      v-model="redirecting"
      class="align-center justify-center"
      persistent
      contained
    >
      <div class="text-center">
        <v-progress-circular
          indeterminate
          size="80"
          width="6"
          color="primary"
          class="mb-6"
        />
        <div class="text-h6 text-white mb-2">
          Bem-vindo de volta!
        </div>
        <div class="text-caption text-white-50">
          Carregando seu painel financeiro...
        </div>
      </div>
    </v-overlay>
  </div>
</template>

<script setup lang="ts">
import TermsFooter from '@/components/dialogs/TermsFooter.vue'
import authService from '@/services/auth.service'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/toast'
import { useUserStore } from '@/store/user'
import { onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const authStore = useAuthStore()
const userStore = useUserStore()
const toastStore = useToastStore()

const formData = ref({
  email: '',
  password: '',
  remember: false
})

const showPassword = ref(false)
const loading = ref(false)
const redirecting = ref(false)
const form = ref()

// Carregar email salvo ao montar o componente
onMounted(() => {
  const rememberMe = localStorage.getItem('rememberMe')
  const rememberedEmail = localStorage.getItem('rememberedEmail')
  
  if (rememberMe === 'true' && rememberedEmail) {
    formData.value.email = rememberedEmail
    formData.value.remember = true
  }
})

// Observar mudanças no checkbox "lembrar-me"
watch(() => formData.value.remember, (newValue) => {
  // Se desmarcar, limpar dados salvos
  if (!newValue) {
    localStorage.removeItem('rememberMe')
    localStorage.removeItem('rememberedEmail')
  }
})

const errorTranslations: { [key: string]: string } = {
  'The provided credentials are incorrect': 'Email ou senha incorretos',
  'These credentials do not match our records': 'Email ou senha incorretos',
  'User not found': 'Usuário não encontrado',
  'Invalid credentials': 'Credenciais inválidas',
  'Network error': 'Erro de conexão com o servidor',
  'Timeout': 'A requisição demorou muito tempo',
  'Cannot read properties of undefined': 'Resposta inválida do servidor'
}

function translateError(error: string): string {
  for (const [en, pt] of Object.entries(errorTranslations)) {
    if (error.includes(en)) {
      return pt
    }
  }
  return error
}

async function handleLogin() {
  const { valid } = await (form.value as any).validate()
  if (!valid) return

  loading.value = true
  
  try {
    const response = await authService.login({
      email: formData.value.email,
      password: formData.value.password
    })

    if (!response) {
      throw new Error('Resposta inválida do servidor')
    }

    // Salvar token no localStorage se fornecido
    if (response.token) {
      localStorage.setItem('sanctum_token', response.token)
      authStore.setToken(response.token)
    }

    // Extrair dados do usuário com fallback para dados do formulário
    const userData = response.user || {
      id: undefined,
      name: formData.value.email.split('@')[0],
      email: formData.value.email,
      type: 'USER'
    }

    // Incluir summary nos dados do usuário se fornecido
    if (response.summary) {
      (userData as any).summary = response.summary
    }

    userStore.setUserData(userData)
    
    // Também salvar mesAno se fornecido
    if (response.mesAno) {
      userStore.setMesAno(response.mesAno)
    }

    // Gerenciar preferência de "lembrar-me"
    if (formData.value.remember) {
      // Salvar email para próximo login
      localStorage.setItem('rememberMe', 'true')
      localStorage.setItem('rememberedEmail', formData.value.email)
    } else {
      // Limpar dados salvos se não marcou lembrar-me
      localStorage.removeItem('rememberMe')
      localStorage.removeItem('rememberedEmail')
    }

    toastStore.addToast({
      message: 'Login realizado com sucesso!',
      color: 'success',
      timeout: 2000,
      icon: 'mdi-check-circle'
    })

    // Ativar estado de redirecionamento
    redirecting.value = true

    // Aguardar um pouco antes de redirecionar
    setTimeout(() => {
      router.push({ name: 'dashboard' })
    }, 1500)
  } catch (error: any) {
    console.error('Erro no login:', error)
    
    // Importar códigos de erro
    const errorCodes = await import('@/assets/errorCodes.json')
    
    // Extrair informações do erro
    const errorData = error || {}
    let errorMessage = 'Erro ao fazer login. Verifique suas credenciais e tente novamente.'
    
    // Prioridade 1: Verificar se há error_code e buscar mensagem amigável
    if (errorData.error_code && errorCodes.default[errorData.error_code as keyof typeof errorCodes.default]) {
      errorMessage = errorCodes.default[errorData.error_code as keyof typeof errorCodes.default]
    }
    // Prioridade 2: Usar response.data.message se disponível
    else if (errorData.response?.data?.message) {
      errorMessage = translateError(errorData.response.data.message)
    }
    // Prioridade 3: Usar response.data.error se disponível
    else if (errorData.response?.data?.error) {
      errorMessage = translateError(errorData.response.data.error)
    }
    // Prioridade 4: Usar message se disponível
    else if (errorData.message) {
      errorMessage = translateError(errorData.message)
    }

    toastStore.addToast({
      message: errorMessage,
      color: 'error',
      timeout: 5000,
      icon: 'mdi-alert-circle'
    })
  } finally {
    loading.value = false
  }
}
</script>

<style scoped lang="scss">
.login-wrapper {
  min-height: 100vh;
  background: linear-gradient(135deg, rgb(var(--v-theme-primary)) 0%, rgba(var(--v-theme-primary), 0.7) 100%);
  padding: 2rem 0;
  position: relative;
}

.login-container {
  padding: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.login-card {
  transition: all 0.3s ease;
  border: none;

  &:hover {
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2) !important;
  }
}

// Fazer scrollable el contenido del card
// Removido para permitir scroll natural de página

// Fazer scrollable el contenido del card
:deep(.v-card__text) {
  overflow-y: auto;
  max-height: calc(100vh - 15rem);
  -webkit-overflow-scrolling: touch;

  @media (max-width: 600px) {
    max-height: calc(100vh - 12rem);
    padding: 1.5rem !important;
  }
}

.indicators-section {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  justify-content: center;
  padding: 0 1rem;
  margin-top: 1.5rem;

  @media (max-width: 600px) {
    padding: 0;
    margin-top: 1rem;
  }
}

// Removido - não necessário para scroll natural

.logo-section {
  background: linear-gradient(135deg, rgb(var(--v-theme-primary)) 0%, rgba(var(--v-theme-primary), 0.8) 100%);
  border-radius: 8px 8px 0 0;
}
</style>
