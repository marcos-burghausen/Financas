<template>
  <div class="cadastro-wrapper">
    <v-container fluid class="cadastro-container">
      <v-row class="fill-height align-center justify-center">
        <v-col cols="12" sm="10" md="8" lg="6">
          <!-- Card Principal -->
          <v-card elevation="8" rounded="lg" class="cadastro-card">
            <!-- Logo Section -->
            <div class="logo-section text-center pa-8 bg-success">
              <div class="mb-4">
                <v-icon icon="mdi-account-plus" size="64" color="white" />
              </div>
              <h1 class="text-h4 text-white font-weight-bold mb-2">Criar Conta</h1>
              <p class="text-white text-opacity-70">Junte-se à nossa comunidade de financas</p>
            </div>

            <!-- Formulário -->
            <v-card-text class="pa-8">
              <p class="text-subtitle-2 text-medium-emphasis mb-6">
                Preencha os dados abaixo para criar sua conta
              </p>

              <v-form ref="form" @submit.prevent="handleCadastro">
                <!-- Nome Completo -->
                <v-text-field
                  v-model="formData.nome"
                  label="Nome Completo"
                  prepend-inner-icon="mdi-account"
                  variant="outlined"
                  density="compact"
                  :rules="[
                    v => !!v || 'Nome é obrigatório',
                    v => v.length >= 3 || 'Nome deve ter pelo menos 3 caracteres'
                  ]"
                  class="mb-4"
                />

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
                  label="Senha"
                  :type="showPassword ? 'text' : 'password'"
                  prepend-inner-icon="mdi-lock"
                  :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                  @click:append-inner="showPassword = !showPassword"
                  variant="outlined"
                  density="compact"
                  :rules="[
                    v => !!v || 'Senha é obrigatória',
                    v => v.length >= 6 || 'Senha deve ter pelo menos 6 caracteres'
                  ]"
                  hint="Mínimo 6 caracteres"
                  class="mb-4"
                />

                <!-- Confirmar Senha -->
                <v-text-field
                  v-model="formData.confirmPassword"
                  label="Confirmar Senha"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  prepend-inner-icon="mdi-lock-check"
                  :append-inner-icon="showConfirmPassword ? 'mdi-eye-off' : 'mdi-eye'"
                  @click:append-inner="showConfirmPassword = !showConfirmPassword"
                  variant="outlined"
                  density="compact"
                  :rules="[
                    v => !!v || 'Confirmação de senha é obrigatória',
                    v => v === formData.password || 'Senhas não correspondem'
                  ]"
                  class="mb-4"
                />

                <!-- Tipo de Conta -->
                <v-select
                  v-model="formData.tipo"
                  :items="tiposAccount"
                  label="Tipo de Conta"
                  prepend-inner-icon="mdi-account-convert"
                  variant="outlined"
                  density="compact"
                  :rules="[v => !!v || 'Tipo de conta é obrigatório']"
                  class="mb-6"
                />

                <!-- Termos de Uso -->
                <v-checkbox
                  v-model="formData.termos"
                  :rules="[v => !!v || 'Você deve aceitar os termos']"
                  class="mb-6"
                >
                  <template #label>
                    <span class="text-caption">
                      Eu concordo com os
                      <v-btn variant="text" size="x-small" color="primary">
                        Termos de Uso
                      </v-btn>
                      e
                      <v-btn variant="text" size="x-small" color="primary">
                        Política de Privacidade
                      </v-btn>
                    </span>
                  </template>
                </v-checkbox>

                <!-- Botão Cadastro -->
                <v-btn
                  type="submit"
                  color="success"
                  size="large"
                  block
                  class="mb-4"
                  :loading="loading"
                >
                  <v-icon icon="mdi-check-circle" start />
                  Criar Conta
                </v-btn>
              </v-form>

              <!-- Divider -->
              <div class="d-flex align-center gap-2 my-6">
                <v-divider />
                <span class="text-caption text-medium-emphasis">ou</span>
                <v-divider />
              </div>

              <!-- Botão Voltar -->
              <v-btn
                to="/login"
                variant="outlined"
                color="primary"
                size="large"
                block
              >
                <v-icon icon="mdi-login" start />
                Já tenho conta - Entrar
              </v-btn>
            </v-card-text>

            <!-- Footer -->
            <v-divider />
            <v-card-text class="text-center text-caption text-medium-emphasis pa-4">
              Protegemos seus dados com criptografia de ponta a ponta
            </v-card-text>
          </v-card>

          <!-- Benefícios -->
          <v-row class="mt-6 text-center text-white">
            <v-col cols="12" sm="4">
              <div class="d-flex flex-column align-center">
                <v-icon icon="mdi-shield-check" size="32" class="mb-2" />
                <span class="text-caption font-weight-bold">Seguro</span>
              </div>
            </v-col>
            <v-col cols="12" sm="4">
              <div class="d-flex flex-column align-center">
                <v-icon icon="mdi-lightning-bolt" size="32" class="mb-2" />
                <span class="text-caption font-weight-bold">Rápido</span>
              </div>
            </v-col>
            <v-col cols="12" sm="4">
              <div class="d-flex flex-column align-center">
                <v-icon icon="mdi-chart-line" size="32" class="mb-2" />
                <span class="text-caption font-weight-bold">Eficiente</span>
              </div>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import authService, { type RegisterRequest } from '@/services/auth.service'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/toast'
import { useUserStore } from '@/store/user'
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const authStore = useAuthStore()
const userStore = useUserStore()
const toastStore = useToastStore()

const formData = ref({
  nome: '',
  email: '',
  password: '',
  confirmPassword: '',
  tipo: 'USER',
  termos: false
})

const showPassword = ref(false)
const showConfirmPassword = ref(false)
const loading = ref(false)
const form = ref()
const validationErrors = ref<Record<string, string[]>>({})

const tiposAccount = [
  { title: 'Usuário Comum', value: 'USER' },
  { title: 'Trader', value: 'TRADER' },
  { title: 'Usuário + Trader', value: 'USER_TRADER' }
]

/**
 * Obtém a mensagem de erro para um campo
 */
function getFieldError(field: string): string {
  const errors = validationErrors.value[field]
  return errors ? errors[0] : ''
}

/**
 * Limpar erros de validação
 */
function clearValidationErrors() {
  validationErrors.value = {}
}

/**
 * Handler para o cadastro
 */
async function handleCadastro() {
  const { valid } = await (form.value as any).validate()
  if (!valid) return

  clearValidationErrors()
  loading.value = true

  try {
    // Preparar dados para a API
    const registerData: RegisterRequest = {
      name: formData.value.nome,
      email: formData.value.email,
      password: formData.value.password,
      password_confirmation: formData.value.confirmPassword,
      type: formData.value.tipo
    }

    // Chamar o serviço de autenticação
    const response = await authService.register(registerData)

    // Validar resposta da API
    if (!response) {
      throw new Error('Resposta inválida do servidor')
    }

    // Salvar token no localStorage se fornecido (será interceptado pelo http.ts)
    if (response.token) {
      localStorage.setItem('sanctum_token', response.token)
      authStore.setToken(response.token)
    }
    
    // Extrair dados do usuário da resposta (com fallback seguro)
    const userData = response.user || {
      id: undefined,
      name: formData.value.nome,
      email: formData.value.email,
      type: formData.value.tipo
    }
    
    userStore.setUserData({
      id: userData.id,
      name: userData.name,
      email: userData.email,
      type: userData.type
    } as any)

    // Mostrar notificação de sucesso
    toastStore.addToast({
      message: 'Sua conta foi criada com sucesso! Redirecionando...',
      color: 'success',
      timeout: 2000,
      icon: 'mdi-check-circle'
    })

    // Aguardar um pouco antes de redirecionar
    setTimeout(() => {
      router.push({ name: 'dashboard' })
    }, 1000)
  } catch (error: any) {
    console.error('Erro no cadastro:', error)
    
    // Extrair informações do erro
    const errorData = error || {}
    let errorMessage = 'Ocorreu um erro ao criar sua conta. Tente novamente.'
    
    // Mapear erros comuns para português
    const errorTranslations: Record<string, string> = {
      'Resposta inválida do servidor: token não recebido': 'Erro ao processar resposta do servidor. Tente novamente.',
      'Cannot read properties of undefined': 'Erro ao processar dados. Contate o suporte.',
      'Network error': 'Erro de conexão. Verifique sua internet.',
      'Timeout': 'A requisição demorou muito. Tente novamente.',
      'CORS error': 'Erro de acesso. Tente novamente.'
    }
    
    // Prioridade 1: Usar message se disponível
    if (errorData.message) {
      errorMessage = errorData.message
      
      // Traduzir se for um erro em inglês comum
      for (const [en, pt] of Object.entries(errorTranslations)) {
        if (errorMessage.includes(en)) {
          errorMessage = pt
          break
        }
      }
    } 
    // Prioridade 2: Usar error se disponível
    else if (errorData.error) {
      errorMessage = errorData.error
    }
    // Prioridade 3: Se for um erro do JavaScript, traduzir
    else if (error instanceof TypeError) {
      errorMessage = 'Erro ao processar dados. Contate o suporte.'
    }
    else if (error instanceof Error) {
      errorMessage = error.message || 'Erro desconhecido. Tente novamente.'
      
      // Traduzir se necessário
      for (const [en, pt] of Object.entries(errorTranslations)) {
        if (errorMessage.includes(en)) {
          errorMessage = pt
          break
        }
      }
    }
    
    // Se houver erros de validação específicos do campo
    if (errorData.validation_errors && typeof errorData.validation_errors === 'object') {
      validationErrors.value = errorData.validation_errors

      // Mostrar toast com o primeiro erro de validação
      const firstField = Object.keys(errorData.validation_errors)[0]
      const firstError = errorData.validation_errors[firstField]?.[0]

      toastStore.addToast({
        message: firstError || errorMessage,
        color: 'warning',
        timeout: 5000,
        icon: 'mdi-alert-circle'
      })
    } else {
      // Erro geral - sempre mostrar algo para o usuário
      toastStore.addToast({
        message: errorMessage,
        color: 'error',
        timeout: 5000,
        icon: 'mdi-alert'
      })
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped lang="scss">
.cadastro-wrapper {
  min-height: 100vh;
  background: linear-gradient(135deg, rgb(var(--v-theme-success)) 0%, rgba(var(--v-theme-success), 0.7) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.cadastro-container {
  padding: 2rem;
}

.cadastro-card {
  transition: all 0.3s ease;
  border: none;

  &:hover {
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2) !important;
  }
}

.logo-section {
  background: linear-gradient(135deg, rgb(var(--v-theme-success)) 0%, rgba(var(--v-theme-success), 0.8) 100%);
  border-radius: 8px 8px 0 0;
}
</style>
