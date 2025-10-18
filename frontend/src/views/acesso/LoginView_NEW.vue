<template>
  <div class="login-wrapper">
    <v-container fluid class="login-container">
      <v-row class="fill-height align-center justify-center">
        <v-col cols="12" sm="10" md="8" lg="6">
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
              <h2 class="text-h5 font-weight-bold mb-2">Entrar na sua conta</h2>
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

            <!-- Footer -->
            <v-divider />
            <v-card-text class="text-center text-caption text-medium-emphasis pa-4">
              Ao fazer login, você concorda com nossos
              <v-btn variant="text" size="x-small" color="primary">Termos de Uso</v-btn>
              e
              <v-btn variant="text" size="x-small" color="primary">Política de Privacidade</v-btn>
            </v-card-text>
          </v-card>

          <!-- Indicadores no Fundo -->
          <div class="text-center mt-6">
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
          </div>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import { useToast } from '@/composables/useToast'
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const toast = useToast()

const formData = ref({
  email: '',
  password: '',
  remember: false
})

const showPassword = ref(false)
const loading = ref(false)
const form = ref()

async function handleLogin() {
  const { valid } = await (form.value as any).validate()
  if (!valid) return

  loading.value = true
  
  // Simular delay de login
  setTimeout(() => {
    // Mock: aceita email/senha (demo)
    if (formData.value.email && formData.value.password) {
      // Salvar dados mock no localStorage
      localStorage.setItem('userEmail', formData.value.email)
      localStorage.setItem('userName', 'Usuário Teste')
      
      // Mostrar toast de sucesso
      toast.success('Login realizado com sucesso! 🎉')
      
      // Redirecionar para dashboard
      setTimeout(() => {
        router.push({ name: 'dashboard' })
      }, 500)
    }
    loading.value = false
  }, 1500)
}
</script>

<style scoped lang="scss">
.login-wrapper {
  min-height: 100vh;
  background: linear-gradient(135deg, rgb(var(--v-theme-primary)) 0%, rgba(var(--v-theme-primary), 0.7) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.login-container {
  padding: 2rem;
}

.login-card {
  transition: all 0.3s ease;
  border: none;

  &:hover {
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2) !important;
  }
}

.logo-section {
  background: linear-gradient(135deg, rgb(var(--v-theme-primary)) 0%, rgba(var(--v-theme-primary), 0.8) 100%);
  border-radius: 8px 8px 0 0;
}
</style>
