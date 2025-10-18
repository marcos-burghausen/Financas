<template>
  <v-container fluid class="perfil-panel pa-6">
    <!-- Header -->
    <div class="d-flex align-center justify-space-between mb-6">
      <div>
        <div class="d-flex align-center gap-3 mb-2">
          <v-icon icon="mdi-account" size="40" color="primary" />
          <h1 class="text-h4 font-weight-bold">Meu Perfil</h1>
        </div>
        <p class="text-subtitle-2 text-medium-emphasis">
          Gerencie suas informações e preferências
        </p>
      </div>
    </div>

    <!-- Cards de Informações -->
    <v-row class="mb-6">
      <!-- Avatar Section -->
      <v-col cols="12" md="3">
        <v-card elevation="2" class="text-center pa-6">
          <v-avatar
            :image="avatarUrl"
            size="120"
            color="primary"
            class="mx-auto mb-4"
          >
            <v-icon icon="mdi-account" color="white" size="60" />
          </v-avatar>
          <h2 class="text-h6 font-weight-bold mb-1">{{ userData.nome }}</h2>
          <p class="text-caption text-medium-emphasis mb-4">{{ getTypeLabel(userData.type) }}</p>
          <v-btn
            variant="outlined"
            color="primary"
            size="small"
            prepend-icon="mdi-upload"
            block
          >
            Trocar Foto
          </v-btn>
        </v-card>

        <!-- Estatísticas -->
        <v-card elevation="2" class="mt-4 pa-4">
          <div class="text-center mb-4">
            <p class="text-caption text-medium-emphasis mb-1">Membro desde</p>
            <p class="text-h6 font-weight-bold">{{ formatDate(userData.dataCriacao) }}</p>
          </div>
          <v-divider class="my-4" />
          <div class="text-center">
            <p class="text-caption text-medium-emphasis mb-1">Último acesso</p>
            <p class="text-h6 font-weight-bold">{{ formatUltimoAcesso() }}</p>
          </div>
        </v-card>
      </v-col>

      <!-- Formulário Principal -->
      <v-col cols="12" md="9">
        <v-card elevation="2">
          <v-tabs v-model="activeTab" bg-color="primary" class="tabs-header">
            <v-tab value="dados">
              <v-icon icon="mdi-account-edit" class="mr-2" />
              Dados Pessoais
            </v-tab>
            <v-tab value="seguranca">
              <v-icon icon="mdi-lock" class="mr-2" />
              Segurança
            </v-tab>
            <v-tab value="preferencias">
              <v-icon icon="mdi-cog" class="mr-2" />
              Preferências
            </v-tab>
          </v-tabs>

          <v-window v-model="activeTab">
            <!-- Tab: Dados Pessoais -->
            <v-window-item value="dados">
              <v-card-text class="pa-6">
                <v-form ref="formDados" @submit.prevent="saveDados">
                  <v-row>
                    <!-- Nome -->
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="formData.nome"
                        label="Nome Completo"
                        prepend-icon="mdi-account"
                        variant="outlined"
                        density="compact"
                        :rules="[v => !!v || 'Campo obrigatório']"
                        class="mb-4"
                      />
                    </v-col>

                    <!-- Email -->
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="formData.email"
                        label="Email"
                        type="email"
                        prepend-icon="mdi-email"
                        variant="outlined"
                        density="compact"
                        :rules="[v => !!v || 'Campo obrigatório']"
                        class="mb-4"
                      />
                    </v-col>

                    <!-- Telefone -->
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="formData.telefone"
                        label="Telefone"
                        prepend-icon="mdi-phone"
                        variant="outlined"
                        density="compact"
                        class="mb-4"
                      />
                    </v-col>

                    <!-- CPF -->
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="formData.cpf"
                        label="CPF"
                        prepend-icon="mdi-identifier"
                        variant="outlined"
                        density="compact"
                        class="mb-4"
                      />
                    </v-col>

                    <!-- Data Nascimento -->
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="formData.dataNascimento"
                        label="Data de Nascimento"
                        type="date"
                        prepend-icon="mdi-calendar"
                        variant="outlined"
                        density="compact"
                        class="mb-4"
                      />
                    </v-col>

                    <!-- Profissão -->
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="formData.profissao"
                        label="Profissão"
                        prepend-icon="mdi-briefcase"
                        variant="outlined"
                        density="compact"
                        class="mb-4"
                      />
                    </v-col>

                    <!-- Biografia -->
                    <v-col cols="12">
                      <v-textarea
                        v-model="formData.biografia"
                        label="Biografia"
                        prepend-icon="mdi-note"
                        variant="outlined"
                        density="compact"
                        rows="3"
                        class="mb-4"
                      />
                    </v-col>

                    <!-- Botões -->
                    <v-col cols="12" class="d-flex gap-2">
                      <v-btn
                        type="submit"
                        color="primary"
                        prepend-icon="mdi-content-save"
                        :loading="loading"
                      >
                        Salvar Alterações
                      </v-btn>
                      <v-btn
                        variant="outlined"
                        color="secondary"
                        @click="resetFormDados"
                      >
                        Cancelar
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-form>
              </v-card-text>
            </v-window-item>

            <!-- Tab: Segurança -->
            <v-window-item value="seguranca">
              <v-card-text class="pa-6">
                <v-form ref="formSeguranca" @submit.prevent="saveSeguranca">
                  <h3 class="text-h6 font-weight-bold mb-4">Alterar Senha</h3>

                  <v-row>
                    <!-- Senha Atual -->
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="formSeguranca.senhaAtual"
                        label="Senha Atual"
                        :type="showSenhaAtual ? 'text' : 'password'"
                        prepend-icon="mdi-lock"
                        :append-inner-icon="showSenhaAtual ? 'mdi-eye-off' : 'mdi-eye'"
                        @click:append-inner="showSenhaAtual = !showSenhaAtual"
                        variant="outlined"
                        density="compact"
                        :rules="[v => !!v || 'Campo obrigatório']"
                        class="mb-4"
                      />
                    </v-col>

                    <!-- Senha Nova -->
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="formSeguranca.senhaNova"
                        label="Nova Senha"
                        :type="showSenhaNova ? 'text' : 'password'"
                        prepend-icon="mdi-lock-plus"
                        :append-inner-icon="showSenhaNova ? 'mdi-eye-off' : 'mdi-eye'"
                        @click:append-inner="showSenhaNova = !showSenhaNova"
                        variant="outlined"
                        density="compact"
                        :rules="[
                          v => !!v || 'Campo obrigatório',
                          v => v.length >= 6 || 'Mínimo 6 caracteres'
                        ]"
                        class="mb-4"
                      />
                    </v-col>

                    <!-- Confirmar Senha -->
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model="formSeguranca.confirmarSenha"
                        label="Confirmar Nova Senha"
                        :type="showConfirmarSenha ? 'text' : 'password'"
                        prepend-icon="mdi-lock-check"
                        :append-inner-icon="showConfirmarSenha ? 'mdi-eye-off' : 'mdi-eye'"
                        @click:append-inner="showConfirmarSenha = !showConfirmarSenha"
                        variant="outlined"
                        density="compact"
                        :rules="[
                          v => !!v || 'Campo obrigatório',
                          v => v === formSeguranca.senhaNova || 'Senhas não correspondem'
                        ]"
                        class="mb-4"
                      />
                    </v-col>

                    <!-- Botões -->
                    <v-col cols="12" class="d-flex gap-2">
                      <v-btn
                        type="submit"
                        color="warning"
                        prepend-icon="mdi-shield-check"
                        :loading="loading"
                      >
                        Alterar Senha
                      </v-btn>
                      <v-btn
                        variant="outlined"
                        color="secondary"
                        @click="resetFormSeguranca"
                      >
                        Cancelar
                      </v-btn>
                    </v-col>
                  </v-row>

                  <!-- Sessões Ativas -->
                  <v-divider class="my-6" />
                  <h3 class="text-h6 font-weight-bold mb-4">Sessões Ativas</h3>
                  <v-row>
                    <v-col v-for="sessao in sessoes" :key="sessao.id" cols="12" md="6">
                      <v-card elevation="1" class="pa-4">
                        <div class="d-flex justify-space-between align-start">
                          <div class="flex-grow-1">
                            <p class="text-h6 font-weight-bold mb-1">{{ sessao.dispositivo }}</p>
                            <p class="text-caption text-medium-emphasis mb-1">{{ sessao.localizacao }}</p>
                            <p class="text-caption text-medium-emphasis">{{ formatDataSessao(sessao.ultimoAcesso) }}</p>
                          </div>
                          <v-chip
                            :color="sessao.ativa ? 'success' : 'secondary'"
                            size="small"
                            text-color="white"
                          >
                            {{ sessao.ativa ? 'Ativa' : 'Inativa' }}
                          </v-chip>
                        </div>
                      </v-card>
                    </v-col>
                  </v-row>
                </v-form>
              </v-card-text>
            </v-window-item>

            <!-- Tab: Preferências -->
            <v-window-item value="preferencias">
              <v-card-text class="pa-6">
                <v-form ref="formPreferencias" @submit.prevent="savePreferencias">
                  <h3 class="text-h6 font-weight-bold mb-4">Preferências Gerais</h3>

                  <v-row>
                    <!-- Idioma -->
                    <v-col cols="12" md="6">
                      <v-select
                        v-model="formPreferencias.idioma"
                        :items="idiomas"
                        label="Idioma"
                        prepend-icon="mdi-translate"
                        variant="outlined"
                        density="compact"
                        class="mb-4"
                      />
                    </v-col>

                    <!-- Moeda -->
                    <v-col cols="12" md="6">
                      <v-select
                        v-model="formPreferencias.moeda"
                        :items="moedas"
                        label="Moeda Padrão"
                        prepend-icon="mdi-currency-brl"
                        variant="outlined"
                        density="compact"
                        class="mb-4"
                      />
                    </v-col>

                    <!-- Notificações por Email -->
                    <v-col cols="12">
                      <v-checkbox
                        v-model="formPreferencias.emailNotificacoes"
                        label="Receber notificações por email"
                        class="mb-4"
                      />
                    </v-col>

                    <!-- Relatórios Mensais -->
                    <v-col cols="12">
                      <v-checkbox
                        v-model="formPreferencias.relatóriosMensais"
                        label="Receber relatórios mensais"
                        class="mb-4"
                      />
                    </v-col>

                    <!-- Alertas de Transações -->
                    <v-col cols="12">
                      <v-checkbox
                        v-model="formPreferencias.alertasTransacoes"
                        label="Alertas de transações acima de R$ 1.000"
                        class="mb-4"
                      />
                    </v-col>

                    <!-- Tema -->
                    <v-col cols="12" md="6">
                      <v-select
                        v-model="formPreferencias.tema"
                        :items="temas"
                        label="Tema"
                        prepend-icon="mdi-palette"
                        variant="outlined"
                        density="compact"
                        class="mb-4"
                      />
                    </v-col>

                    <!-- Botões -->
                    <v-col cols="12" class="d-flex gap-2">
                      <v-btn
                        type="submit"
                        color="primary"
                        prepend-icon="mdi-content-save"
                        :loading="loading"
                      >
                        Salvar Preferências
                      </v-btn>
                      <v-btn
                        variant="outlined"
                        color="secondary"
                        @click="resetFormPreferencias"
                      >
                        Cancelar
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-form>
              </v-card-text>
            </v-window-item>
          </v-window>
        </v-card>
      </v-col>
    </v-row>

    <!-- Zona de Risco -->
    <v-row class="mt-6">
      <v-col cols="12" md="9" offset-md="3">
        <v-card elevation="2" border="2" border-color="error" class="pa-6">
          <h3 class="text-h6 font-weight-bold mb-4 text-error">
            <v-icon icon="mdi-alert-circle" start />
            Zona de Risco
          </h3>
          <p class="text-caption text-medium-emphasis mb-4">
            Estas ações são irreversíveis. Use com cuidado.
          </p>
          <div class="d-flex gap-2">
            <v-btn
              color="error"
              variant="outlined"
              prepend-icon="mdi-download"
            >
              Baixar Meus Dados
            </v-btn>
            <v-btn
              color="error"
              variant="outlined"
              prepend-icon="mdi-delete"
            >
              Deletar Conta
            </v-btn>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const activeTab = ref('dados')
const loading = ref(false)

const userData = ref({
  nome: 'João Silva',
  email: 'joao@example.com',
  type: 'FULL',
  dataCriacao: '2025-01-15',
  ultimoAcesso: '2025-10-17T14:30:00'
})

const avatarUrl = ref('')

const formData = ref({
  nome: userData.value.nome,
  email: userData.value.email,
  telefone: '(11) 99999-9999',
  cpf: '123.456.789-00',
  dataNascimento: '1990-01-15',
  profissao: 'Desenvolvedor',
  biografia: 'Apaixonado por finanças e tecnologia'
})

const formSeguranca = ref({
  senhaAtual: '',
  senhaNova: '',
  confirmarSenha: ''
})

const formPreferencias = ref({
  idioma: 'pt-BR',
  moeda: 'BRL',
  emailNotificacoes: true,
  relatóriosMensais: true,
  alertasTransacoes: true,
  tema: 'light'
})

const showSenhaAtual = ref(false)
const showSenhaNova = ref(false)
const showConfirmarSenha = ref(false)

const idiomas = [
  { title: 'Português (Brasil)', value: 'pt-BR' },
  { title: 'English', value: 'en-US' },
  { title: 'Español', value: 'es-ES' }
]

const moedas = [
  { title: 'Real (R$)', value: 'BRL' },
  { title: 'Dólar (US$)', value: 'USD' },
  { title: 'Euro (€)', value: 'EUR' }
]

const temas = [
  { title: 'Claro', value: 'light' },
  { title: 'Escuro', value: 'dark' },
  { title: 'Automático', value: 'auto' }
]

const sessoes = ref([
  {
    id: 1,
    dispositivo: 'Chrome no Windows',
    localizacao: 'São Paulo, BR',
    ultimoAcesso: new Date(),
    ativa: true
  },
  {
    id: 2,
    dispositivo: 'Safari no iPhone',
    localizacao: 'São Paulo, BR',
    ultimoAcesso: new Date(Date.now() - 86400000),
    ativa: false
  }
])

function formatDate(date: string): string {
  return new Date(date).toLocaleDateString('pt-BR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

function formatUltimoAcesso(): string {
  const diff = Date.now() - new Date(userData.value.ultimoAcesso).getTime()
  const minutos = Math.floor(diff / 60000)
  
  if (minutos < 1) return 'Agora'
  if (minutos < 60) return `${minutos}m atrás`
  
  const horas = Math.floor(minutos / 60)
  if (horas < 24) return `${horas}h atrás`
  
  const dias = Math.floor(horas / 24)
  return `${dias}d atrás`
}

function formatDataSessao(date: Date): string {
  return date.toLocaleDateString('pt-BR', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function getTypeLabel(type: string): string {
  const labels: Record<string, string> = {
    USER: 'Usuário',
    TRADER: 'Trader',
    ADMIN: 'Administrador',
    USER_TRADER: 'Usuário + Trader',
    FULL: 'Full Access'
  }
  return labels[type] || type
}

async function saveDados() {
  loading.value = true
  setTimeout(() => {
    loading.value = false
    // Mock: salvar dados
  }, 500)
}

function resetFormDados() {
  formData.value = {
    nome: userData.value.nome,
    email: userData.value.email,
    telefone: '(11) 99999-9999',
    cpf: '123.456.789-00',
    dataNascimento: '1990-01-15',
    profissao: 'Desenvolvedor',
    biografia: 'Apaixonado por finanças e tecnologia'
  }
}

async function saveSeguranca() {
  loading.value = true
  setTimeout(() => {
    loading.value = false
    resetFormSeguranca()
  }, 500)
}

function resetFormSeguranca() {
  formSeguranca.value = {
    senhaAtual: '',
    senhaNova: '',
    confirmarSenha: ''
  }
  showSenhaAtual.value = false
  showSenhaNova.value = false
  showConfirmarSenha.value = false
}

async function savePreferencias() {
  loading.value = true
  setTimeout(() => {
    loading.value = false
  }, 500)
}

function resetFormPreferencias() {
  formPreferencias.value = {
    idioma: 'pt-BR',
    moeda: 'BRL',
    emailNotificacoes: true,
    relatóriosMensais: true,
    alertasTransacoes: true,
    tema: 'light'
  }
}
</script>

<style scoped lang="scss">
.perfil-panel {
  max-width: 1400px;
}

.tabs-header {
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
}
</style>
