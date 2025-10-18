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
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

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

const tiposAccount = [
  { title: 'Usuário Comum', value: 'USER' },
  { title: 'Trader', value: 'TRADER' },
  { title: 'Usuário + Trader', value: 'USER_TRADER' }
]

async function handleCadastro() {
  const { valid } = await (form.value as any).validate()
  if (!valid) return

  loading.value = true
  
  // Simular delay de cadastro
  setTimeout(() => {
    // Mock: registra usuário
    localStorage.setItem('userEmail', formData.value.email)
    localStorage.setItem('userName', formData.value.nome)
    localStorage.setItem('userType', formData.value.tipo)
    
    // Redirecionar para dashboard
    router.push({ name: 'dashboard' })
    loading.value = false
  }, 1500)
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
