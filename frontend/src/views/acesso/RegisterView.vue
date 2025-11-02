<template>
  <div class="register-container">
    <div class="register-content">
      <!-- Card Principal -->
      <v-card
        class="register-card"
         style="border: solid 5px red; max-width: 600px;"
        elevation="24"
        rounded="xl"
      >
        <!-- Lado Esquerdo - Formulário -->
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
              Criar sua conta
            </h2>
            <p class="form-subtitle">
              Comece a gerenciar suas finanças hoje mesmo
            </p>

            <!-- Mensagem de Erro -->
            <ErrorMessage />
            <ErrorsForm />

            <!-- Formulário -->
            <v-form
              v-model="validForm"
              @submit.prevent="create"
            >
              <v-text-field
                v-model="user.name"
                label="Nome completo"
                type="text"
                variant="outlined"
                prepend-inner-icon="mdi-account-outline"
                :rules="[rules.requiredName]"
                class="mb-4"
                autofocus
                autocomplete="name"
                density="comfortable"
              />

              <v-text-field
                v-model="user.email"
                label="Email"
                type="email"
                variant="outlined"
                prepend-inner-icon="mdi-email-outline"
                :rules="[rules.requiredEmail, rules.emailFormat]"
                class="mb-4"
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
                :rules="[rules.requiredPassword, rules.passwordFormat]"
                :hint="passwordHint"
                persistent-hint
                class="mb-4"
                autocomplete="new-password"
                density="comfortable"
                @click:append-inner="showPassword = !showPassword"
              />

              <v-text-field
                v-model="user.confirmPassword"
                label="Confirmar senha"
                :type="showPassword ? 'text' : 'password'"
                variant="outlined"
                prepend-inner-icon="mdi-lock-check-outline"
                :rules="[rules.requiredConfirmPassword, rules.passwordsMatch]"
                class="mb-2"
                autocomplete="new-password"
                density="comfortable"
              />

              <!-- Password Strength Indicator -->
              <div
                v-if="user.password"
                class="password-strength mb-4"
              >
                <div class="strength-bar">
                  <div
                    class="strength-fill"
                    :class="passwordStrength.class"
                    :style="{ width: passwordStrength.width }"
                  />
                </div>
                <span
                  class="strength-text"
                  :class="passwordStrength.class"
                >
                  {{ passwordStrength.text }}
                </span>
              </div>

              <v-btn
                type="submit"
                color="success"
                size="x-large"
                block
                class="register-btn mt-4"
                :loading="loading"
                :disabled="!validForm || loading"
              >
                Criar conta
              </v-btn>

              <div class="login-link">
                <span>Já tem uma conta? </span>
                <a
                  href="#"
                  @click.prevent="$emit('nextStep')"
                >
                  Entrar
                </a>
              </div>

              <!-- Terms Footer -->
              <!-- <TermsFooter /> -->
            </v-form>
          </div>
        </div>

        <!-- Lado Direito - Informações -->
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
              Junte-se a nós!
            </h1>
            <p class="welcome-subtitle">
              Tenha controle total sobre suas finanças pessoais
            </p>

            <div class="benefits-list">
              <div
                v-for="(benefit, index) in benefits"
                :key="index"
                class="benefit-item"
              >
                <div class="benefit-icon">
                  <v-icon
                    :icon="benefit.icon"
                    color="white"
                    size="28"
                  />
                </div>
                <div class="benefit-text">
                  <h3>{{ benefit.title }}</h3>
                  <p>{{ benefit.description }}</p>
                </div>
              </div>
            </div>

            <div class="testimonial">
              <v-icon
                icon="mdi-format-quote-open"
                color="white"
                size="32"
                class="quote-icon"
              />
              <p class="testimonial-text">
                "Melhor aplicativo de finanças que já usei. Simples, completo e seguro!"
              </p>
              <div class="testimonial-author">
                <strong>João Silva</strong>
                <span>Usuário desde 2023</span>
              </div>
            </div>
          </div>
        </div>
      </v-card>
    </div>
  </div>
</template>

<script setup lang="ts">
import ErrorMessage from "@/components/ErrorMessage.vue";
import ErrorsForm from "@/components/ModalErrorsForm.vue";
import http from "@/services/http";
import { useErrorStore } from "@/store/error";
import type { FormCadastro } from "@/types";
import type { AxiosError } from "axios";
import { computed, ref } from "vue";

const emit = defineEmits(["nextStep"]);

// Store
const errorStore = useErrorStore();

// State
const user = ref<FormCadastro>({
  name: "",
  email: "",
  password: "",
  confirmPassword: "",
});

const validForm = ref(false);
const showPassword = ref(false);
const loading = ref(false);

// Benefits list
const benefits = [
  {
    icon: "mdi-shield-check",
    title: "100% Seguro",
    description: "Seus dados protegidos com criptografia de ponta",
  },
  {
    icon: "mdi-chart-timeline-variant",
    title: "Análises Inteligentes",
    description: "Relatórios detalhados para tomar melhores decisões",
  },
  {
    icon: "mdi-cellphone",
    title: "Multiplataforma",
    description: "Acesse de qualquer lugar, web ou mobile",
  },
];

// Password strength
const passwordStrength = computed(() => {
  const password = user.value.password;
  if (!password) return { width: "0%", class: "", text: "" };

  let strength = 0;
  if (password.length >= 8) strength++;
  if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
  if (/\d/.test(password)) strength++;
  if (/[^a-zA-Z0-9]/.test(password)) strength++;

  const levels = [
    { width: "25%", class: "weak", text: "Fraca" },
    { width: "50%", class: "fair", text: "Regular" },
    { width: "75%", class: "good", text: "Boa" },
    { width: "100%", class: "strong", text: "Forte" },
  ];

  return levels[strength - 1] || levels[0];
});

const passwordHint = computed(() => {
  return "Mínimo 8 caracteres, incluindo letra maiúscula, minúscula, número e símbolo";
});

// Validation Rules
const rules = {
  requiredName: (value: string) => !!value || "Nome é obrigatório",
  requiredEmail: (value: string) => !!value || "Email é obrigatório",
  emailFormat: (value: string) =>
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) || "Formato de email inválido",
  requiredPassword: (value: string) => !!value || "Senha é obrigatória",
  passwordFormat: (value: string) => {
    if (!value) return true;
    const hasMinLength = value.length >= 8;
    const hasUpperCase = /[A-Z]/.test(value);
    const hasLowerCase = /[a-z]/.test(value);
    const hasNumber = /\d/.test(value);
    const hasSpecialChar = /[^a-zA-Z0-9]/.test(value);

    if (!hasMinLength) return "Senha deve ter no mínimo 8 caracteres";
    if (!hasUpperCase) return "Senha deve conter letra maiúscula";
    if (!hasLowerCase) return "Senha deve conter letra minúscula";
    if (!hasNumber) return "Senha deve conter número";
    if (!hasSpecialChar) return "Senha deve conter caractere especial";
    return true;
  },
  requiredConfirmPassword: (value: string) =>
    !!value || "Confirmação de senha é obrigatória",
  passwordsMatch: (value: string) =>
    value === user.value.password || "As senhas não correspondem",
};

interface ApiErrorResponse {
  errors?: Record<string, string[]>;
  message?: string;
}

// Methods
async function create() {
  errorStore.unsetError();
  try {
    loading.value = true;
    await http.post("/create", user.value);
    emit("nextStep");
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
}
</script>

<style scoped>
.register-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
  padding: 20px;
}

.register-content {
  width: 100%;
  max-width: 1100px;
}

.register-card {
  display: flex;
  overflow: hidden;
  min-height: 700px;
}

/* Lado do Formulário */
.form-side {
  flex: 1;
  background: white;
  padding: 60px 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  order: 1;
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

/* Password Strength */
.password-strength {
  margin-top: 8px;
}

.strength-bar {
  height: 4px;
  background: #e2e8f0;
  border-radius: 2px;
  overflow: hidden;
  margin-bottom: 8px;
}

.strength-fill {
  height: 100%;
  transition: all 0.3s ease;
}

.strength-fill.weak {
  background: #f56565;
}

.strength-fill.fair {
  background: #ed8936;
}

.strength-fill.good {
  background: #48bb78;
}

.strength-fill.strong {
  background: #38a169;
}

.strength-text {
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.strength-text.weak {
  color: #f56565;
}

.strength-text.fair {
  color: #ed8936;
}

.strength-text.good {
  color: #48bb78;
}

.strength-text.strong {
  color: #38a169;
}

/* Button */
.register-btn {
  font-weight: 600;
  text-transform: none;
  letter-spacing: 0.5px;
  box-shadow: 0 4px 12px rgba(56, 239, 125, 0.4);
}

.login-link {
  text-align: center;
  margin-top: 24px;
  font-size: 0.9375rem;
  color: #718096;
}

.login-link a {
  color: #11998e;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.2s;
}

.login-link a:hover {
  color: #38ef7d;
}

/* Lado de Informações */
.info-side {
  flex: 1;
  background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
  padding: 60px 50px;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  order: 2;
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

/* Benefits */
.benefits-list {
  display: flex;
  flex-direction: column;
  gap: 30px;
  margin-bottom: 40px;
}

.benefit-item {
  display: flex;
  align-items: flex-start;
  gap: 20px;
}

.benefit-icon {
  flex-shrink: 0;
  width: 56px;
  height: 56px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(10px);
}

.benefit-text h3 {
  font-size: 1.125rem;
  font-weight: 600;
  margin-bottom: 4px;
}

.benefit-text p {
  font-size: 0.9375rem;
  opacity: 0.9;
  line-height: 1.5;
}

/* Testimonial */
.testimonial {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 24px;
  position: relative;
}

.quote-icon {
  opacity: 0.3;
  margin-bottom: 12px;
}

.testimonial-text {
  font-size: 1rem;
  font-style: italic;
  line-height: 1.6;
  margin-bottom: 16px;
}

.testimonial-author {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.testimonial-author strong {
  font-size: 0.9375rem;
}

.testimonial-author span {
  font-size: 0.8125rem;
  opacity: 0.8;
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
    order: 1;
  }

  .register-card {
    min-height: auto;
  }
}

@media (max-width: 600px) {
  .register-container {
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
