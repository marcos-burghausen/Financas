<template>
  <div class="box">
    <div class="container__dados">
      <figure class="figure">
        <img
          src="@/assets/img/2.png"
          style="width: 200px"
          alt="logo"
        >
      </figure>
      <h2 class="title">
        Criar Uma Conta
      </h2>

      <ErrorMessage />
      <ErrorsForm />

      <v-form
        v-model="validForm"
        class="form"
        @submit.prevent="create"
      >
        <v-text-field
          v-model="user.name"
          variant="underlined"
          type="text"
          hide-details="auto"
          label="Nome"
          :rules="[rules.requiredName]"
          class="mb-8 imput"
          autofocus
          autocomplete="on"
          prepend-inner-icon="mdi-account-outline"
        />

        <v-text-field
          v-model="user.email"
          variant="underlined"
          type="email"
          hide-details="auto"
          label="Email"
          :rules="[rules.requiredEmail, rules.emailFormat]"
          class="mb-8 imput"
          autocomplete="on"
          prepend-inner-icon="mdi-email-outline"
        />

        <v-text-field
          v-model="user.password"
          variant="underlined"
          :type="mostrarSenha ? 'text' : 'password'"
          hide-details="auto"
          label="Senha"
          :rules="[rules.requiredSenha, rules.passwordFormat]"
          class="mb-8 imput"
          prepend-inner-icon="mdi-lock-outline"
          :append-inner-icon="mostrarSenha ? 'mdi-eye' : 'mdi-eye-off'"
          hint="A senha deve ter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma minúscula, um número e um caractere especial (exceto aspas)"
          @click:append-inner="mostrarSenha = !mostrarSenha"
        />

        <v-text-field
          v-model="user.confirmPassword"
          variant="underlined"
          :type="mostrarSenha ? 'text' : 'password'"
          hide-details="auto"
          label="Confirmar senha"
          :rules="[rules.requiredConfirmarSenha, rules.passwordsMatch]"
          class="mb-8 imput"
          prepend-inner-icon="mdi-lock-outline"
          :append-inner-icon="mostrarSenha ? 'mdi-eye' : 'mdi-eye-off'"
          @click:append-inner="mostrarSenha = !mostrarSenha"
        />

        <div class="y">
          <a
            class="btn__register"
            href="#"
            @click.prevent="emits('nextStep')"
          >
            <span>já tem uma conta </span>conecte-se.
          </a>
        </div>
        <v-btn
          :disabled="loading || !validForm"
          :loading="loading"
          class="btn mt-5 btn__submit"
          block
          size="large"
          type="submit"
        >
          cadastrar
        </v-btn>
      </v-form>
    </div>
  </div>
</template>

<script setup lang="ts">
import ErrorMessage from "@/components/ErrorMessage.vue";
import ErrorsForm from "@/components/ModalErrorsForm.vue";

import { useErrorStore } from "@/store/error";
import http from "@/services/http";

import { ref } from "vue";
import type { AxiosError } from "axios";
import type { FormCadastro } from "@/types/formCadastro";

const emits = defineEmits(["nextStep"]);
const errorStore = useErrorStore();
const user = ref<FormCadastro>({
    name: "",
    email: "",
    password: "",
    confirmPassword: "",
});

let validForm = ref(false);
let mostrarSenha = ref(false); // Changed to false to hide password by default
let loading = ref(false);

async function create() {
    loading.value = true;
    try {
        await http.post("/create", user.value);
        emits("nextStep");
    } catch (error: unknown) {
        if (isAxiosErrorWithData(error)) {
            if (error.response.data.errors) {
                errorStore.setErrorFromForm(error);
            } else {
                errorStore.setErrorFromResponse(error);
            }
        } else {
            console.error("Erro desconhecido:", error);
        }
    } finally {
        loading.value = false;
    }
}

function isAxiosErrorWithData(error: unknown): error is AxiosError<{ errors?: any }> {
    const axiosError = error as AxiosError;
    return axiosError.isAxiosError === true && 
         axiosError.response?.data !== undefined;
}

const rules = {
    requiredName: (value: string) => !!value || "O campo nome é obrigatório",
    requiredEmail: (value: string) => !!value || "O campo email é obrigatório",
    emailFormat: (value: string) => 
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) || "Formato de email inválido",
    requiredSenha: (value: string) => !!value || "O campo senha é obrigatório",
    passwordFormat: (value: string) => {
        const regex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>[\]\\/-])[A-Za-z\d!@#$%^&*(),.?":{}|<>[\]\\/-]{8,}$/;
        return regex.test(value) || 
           "A senha deve ter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma minúscula, um número e um caractere especial (exceto aspas)";
    },
    requiredConfirmarSenha: (value: string) => !!value || "O campo confirmar senha é obrigatório",
    passwordsMatch: (value: string) => 
        value === user.value.password || "As senhas não são iguais",
};
</script>

<style scoped>
.box {
  display: flex;
  width: 90%;
  max-width: 500px;
}

.container__dados {
  border-radius: 10px 0 0 10px;
  width: 100%;
  display: flex;
  flex-direction: column;
  justify-items: center;
}

.title {
  font-size: 30px;
  font-weight: bold;
  color: #fefefe;
  text-align: center;
}

.form {
  display: flex;
  flex-direction: column;
  width: 100% !important;
}

.imput {
  color: #ccc;
  width: 100%;
}

.btn__register {
  display: inline;
  color: #0097a7;
  text-decoration: none;
}

.btn__register span {
  color: #fefefe;
}

.btn {
  text-transform: uppercase;
  color: #fff;
  font-size: 10px;
  cursor: pointer;
  font-weight: bold;
  align-self: center;
  border: none;
  margin-top: 1rem;
  font-size: 20px;
  background-color: #77d08e;
  border: 1px solid #77d08e;
  transition: background-color 0.5s;
}

.btn__submit:hover {
  background-color: #e1e1e1;
  border: 1px solid #77d08e;
  color: #77d08e;
}

.figure {
  display: flex;
  justify-content: space-evenly;
  align-items: center;
}

@media screen and (max-width: 920px) {
  .btn__register {
    display: inline;
    color: #0097a7;
    font-size: 16px;
    margin: 15px 0;
    background: transparent;
    border: none;
  }
}
</style>