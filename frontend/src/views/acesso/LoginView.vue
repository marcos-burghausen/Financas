<template>
  <div class="login-container">
    <div class="login-content">
      <!-- Card Principal -->
      <v-card
        class="login-card"
        elevation="24"
        rounded="xl"
      >
        <!-- Lado Esquerdo - Informações -->
        <div class="info-side">
          <div class="info-content">
            <div class="logo-section">
              <img
                src="@/assets/img/2.png"
                alt="Mr Finanças Logo"
                class="logo-large"
              >
            </div>
            <h1 class="welcome-title">
              Bem-vindo de volta!
            </h1>
            <p class="welcome-subtitle">
              Gerencie suas finanças com inteligência e simplicidade
            </p>
            
            <div class="features-list">
              <div
                v-for="(feature, index) in features"
                :key="index"
                class="feature-item"
              >
                <v-icon
                  :icon="feature.icon"
                  color="white"
                  size="24"
                />
                <span>{{ feature.text }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Lado Direito - Formulário -->
        <div class="form-side">
          <div class="form-content">
            <!-- Logo Mobile -->
            <div class="logo-mobile">
              <img
                src="@/assets/img/2.png"
                alt="Mr Finanças"
                class="logo-small"
              >
            </div>

            <h2 class="form-title">
              Entrar na sua conta
            </h2>
            <p class="form-subtitle">
              Digite suas credenciais para continuar
            </p>

            <!-- Login Social -->
            <div class="social-login">
              <v-btn
                variant="outlined"
                size="large"
                block
                class="social-btn"
                prepend-icon="mdi-facebook"
                @click="initiateFacebookLogin"
              >
                Continuar com Facebook
              </v-btn>
            </div>

            <div class="divider">
              <span class="divider-text">ou entre com email</span>
            </div>

            <!-- Mensagem de Erro -->
            <ErrorMessage />

            <!-- Formulário -->
            <v-form
              v-model="validForm"
              @submit.prevent="login"
            >
              <v-text-field
                v-model="user.email"
                label="Email"
                type="email"
                variant="outlined"
                prepend-inner-icon="mdi-email-outline"
                :rules="[rules.requiredEmail]"
                class="mb-4"
                autofocus
                autocomplete="email"
                density="comfortable"
              />

              <v-text-field
                v-model="user.password"
                label="Senha"
                :type="showPassword ? 'text' : 'password'"
                variant="outlined"
                prepend-inner-icon="mdi-lock-outline"
                :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                :rules="[rules.requiredPassword]"
                class="mb-2"
                autocomplete="current-password"
                density="comfortable"
                @click:append-inner="showPassword = !showPassword"
              />

              <div class="forgot-password">
                <a
                  href="#"
                  class="forgot-link"
                >
                  Esqueceu sua senha?
                </a>
              </div>

              <v-btn
                type="submit"
                color="primary"
                size="x-large"
                block
                class="login-btn mt-6"
                :loading="loading"
                :disabled="!validForm || loading"
              >
                Entrar
              </v-btn>

              <div class="register-link">
                <span>Não tem uma conta? </span>
                <a
                  href="#"
                  @click.prevent="$emit('nextStep')"
                >
                  Cadastre-se
                </a>
              </div>
            </v-form>
          </div>
        </div>
      </v-card>
    </div>

    <!-- Errors Modal -->
    <ErrorsForm />
  </div>
</template>

<script setup lang="ts">
import ErrorMessage from "@/components/ErrorMessage.vue";
import ErrorsForm from "@/components/ModalErrorsForm.vue";
import http from "@/services/http";
import {
    useAuthStore,
    useDashboardStore,
    useErrorStore,
    useUserStore,
} from "@/store";
import { useRolesStore } from "@/store/roles";
import type { ApiErrorResponse, FormLogin, LoginResponse } from "@/types";
import type { AxiosError, AxiosResponse } from "axios";
import { ref } from "vue";
import { useRouter } from "vue-router";

const emit = defineEmits(["nextStep"]);

// Stores
const useUser = useUserStore();
const dashboardStore = useDashboardStore();
const errorStore = useErrorStore();
const router = useRouter();
const useAuth = useAuthStore();
const rolesStore = useRolesStore();

// State
const user = ref<FormLogin>({
  email: "",
  password: "",
});

const validForm = ref(false);
const showPassword = ref(false);
const loading = ref(false);

// Features list
const features = [
  { icon: "mdi-chart-line", text: "Controle total das suas finanças" },
  { icon: "mdi-security", text: "Dados seguros e criptografados" },
  { icon: "mdi-chart-donut", text: "Relatórios e gráficos detalhados" },
  { icon: "mdi-credit-card", text: "Gestão de cartões de crédito" },
];

// Validation Rules
const rules = {
  requiredEmail: (value: string) => !!value || "Email é obrigatório",
  requiredPassword: (value: string) => !!value || "Senha é obrigatória",
};

// Methods
async function initiateFacebookLogin() {
  errorStore.unsetError();
  try {
    loading.value = true;
    const response = await http.get("/auth/redirect");
    window.location.href = response.data.redirect_url;
  } catch (error) {
    console.error("Erro ao iniciar login do Facebook", error);
    loading.value = false;
  }
}

const login = async () => {
  if (!validForm.value) return;
  errorStore.unsetError();

  // Limpa tokens antigos
  localStorage.removeItem("token");
  localStorage.removeItem("sanctum_token");

  try {
    loading.value = true;
    const response: AxiosResponse<LoginResponse> = await http.post(
      "/sanctum/login",
      user.value
    );

    if (response.data.token) {
      useAuth.setToken(response.data.token);
      useUser.setUserData(response.data.user);
      if (response.data.mesAno) {
        useUser.setMesAno(response.data.mesAno);
      }
      dashboardStore.setSummary(response.data.summary);

      // Carregar permissões e roles do usuário após login
      await rolesStore.fetchMyPermissions();

      // Redireciona baseado nas roles
      if (rolesStore.isAdmin) {
        await router.push({ name: "admin" });
      } else if (rolesStore.hasAnyRole(["TRADER", "USER_TRADER"])) {
        await router.push({ name: "trader" });
      } else {
        await router.push({ name: "dashboard" });
      }
    }
  } catch (error) {
    const axiosError = error as AxiosError<ApiErrorResponse>;
    if (axiosError.response?.data.errors) {
      errorStore.setErrorFromForm(axiosError);
    } else {
      errorStore.setErrorFromResponse(axiosError);
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.login-content {
  width: 100%;
  max-width: 1100px;
}

.login-card {
  display: flex;
  overflow: hidden;
  min-height: 650px;
}

/* Lado de Informações */
.info-side {
  flex: 1;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 60px 50px;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
}

.info-content {
  max-width: 400px;
}

.logo-section {
  text-align: center;
  margin-bottom: 40px;
}

.logo-large {
  width: 120px;
  height: auto;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
}

.welcome-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 16px;
  line-height: 1.2;
}

.welcome-subtitle {
  font-size: 1.125rem;
  opacity: 0.9;
  margin-bottom: 40px;
  line-height: 1.6;
}

.features-list {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 16px;
  font-size: 1rem;
  opacity: 0.95;
}

.feature-item .v-icon {
  flex-shrink: 0;
}

/* Lado do Formulário */
.form-side {
  flex: 1;
  background: white;
  padding: 60px 50px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.form-content {
  width: 100%;
  max-width: 400px;
}

.logo-mobile {
  display: none;
  text-align: center;
  margin-bottom: 30px;
}

.logo-small {
  width: 80px;
  height: auto;
}

.form-title {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 8px;
}

.form-subtitle {
  font-size: 1rem;
  color: #718096;
  margin-bottom: 32px;
}

/* Social Login */
.social-login {
  margin-bottom: 24px;
}

.social-btn {
  border: 2px solid #e2e8f0;
  font-weight: 600;
  text-transform: none;
  letter-spacing: 0;
}

/* Divider */
.divider {
  position: relative;
  text-align: center;
  margin: 24px 0;
}

.divider::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background: #e2e8f0;
}

.divider-text {
  position: relative;
  background: white;
  padding: 0 16px;
  color: #718096;
  font-size: 0.875rem;
}

/* Form Elements */
.forgot-password {
  text-align: right;
  margin-top: 8px;
}

.forgot-link {
  color: #667eea;
  font-size: 0.875rem;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.2s;
}

.forgot-link:hover {
  color: #764ba2;
}

.login-btn {
  font-weight: 600;
  text-transform: none;
  letter-spacing: 0.5px;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.register-link {
  text-align: center;
  margin-top: 24px;
  font-size: 0.9375rem;
  color: #718096;
}

.register-link a {
  color: #667eea;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.2s;
}

.register-link a:hover {
  color: #764ba2;
}

/* Responsive */
@media (max-width: 960px) {
  .info-side {
    display: none;
  }

  .logo-mobile {
    display: block;
  }

  .form-side {
    padding: 40px 30px;
  }

  .login-card {
    min-height: auto;
  }
}

@media (max-width: 600px) {
  .login-container {
    padding: 16px;
  }

  .form-side {
    padding: 30px 20px;
  }

  .form-title {
    font-size: 1.5rem;
  }

  .form-subtitle {
    font-size: 0.875rem;
  }
}
</style>
