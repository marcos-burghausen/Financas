<template>
  <div class="box">
    <div class="container__dados">
      <figure class="figure">
        <img
          src="@/assets/img/2.png"
          class="image"
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
        <v-combobox
          v-model="user.name"
          variant="underlined"
          type="text"
          hide-details="auto"
          label="Nome"
          :rules="[rules.requiredName]"
          class="mb-8 mt-4 imput"
          autofocus
          autocomplete="on"
          prepend-inner-icon="mdi-account-outline"
        />

        <v-combobox
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
          :hint="passwordHint"
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
        />

        <div>
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
          class="btn my-5"
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

import http from "@/services/http";
import { useErrorStore } from "@/store/error";

import type { FormCadastro } from "@/types";
import type { AxiosError } from "axios";
import { computed, ref } from "vue";

const emits = defineEmits(["nextStep"]);
const errorStore = useErrorStore();
const user = ref<FormCadastro>({
  name: "",
  email: "",
  password: "",
  confirmPassword: "",
});

let validForm = ref(false);
let mostrarSenha = ref(false);
let loading = ref(false);

interface ApiErrorResponse {
  errors?: Record<string, string[]>;
  message?: string;
}

async function create() {
  errorStore.unsetError();
  try {
    loading.value = true;
    await http.post("/create", user.value);
    emits("nextStep");
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

const rules = {
  requiredName: (value: string) => !!value || "O campo nome é obrigatório",
  requiredEmail: (value: string) => !!value || "O campo email é obrigatório",
  emailFormat: (value: string) =>
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) || "Formato de email inválido",
  requiredSenha: (value: string) => !!value || "O campo senha é obrigatório",
  passwordFormat: (value: string) => {
    const regex =
      /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>[\]\\/-])[A-Za-z\d!@#$%^&*(),.?":{}|<>[\]\\/-]{8,}$/;
    return (
      regex.test(value) ||
      "A senha deve ter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma minúscula, um número e um caractere especial (exceto aspas)"
    );
  },
  requiredConfirmarSenha: (value: string) =>
    !!value || "O campo confirmar senha é obrigatório",
  passwordsMatch: (value: string) =>
    value === user.value.password || "As senhas não são iguais",
};

const passwordHint = computed(() => {
  const regex =
    /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}<>[\]\\/-])[A-Za-z\d!@#$%^&*(),.?":{}<>[\]\\/-]{8,}$/;
  return regex.test(user.value.password || "")
    ? ""
    : "A senha deve ter pelo menos 8 caracteres sendo uma letra maiúcula, uma minúscula, um número e um caracter especial exeto aspas simples e duplas";
});
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
.figure {
  display: flex;
  justify-content: space-evenly;
  align-items: center;
}
.image {
  width: 200px;
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

/* @media screen and (max-width: 1201px) {
  .container__dados {
    padding: 2rem 4rem;
  }
} */

@media screen and (max-width: 920px) {
  /* .container__dados {
    width: 100%;
    padding: 2rem 4rem;
  } */

  /* .container__decription {
    display: none;
  } */

  /* .btn__register {
    display: inline;
    color: #0097a7;
    font-size: 16px;
    margin: 15px 0;
    background: transparent;
    border: none;
  } */

  /* .container__button {
    display: flex;
    justify-content: space-between;
  } */
}

/* @media screen and (max-width: 740px) {
  .container__dados {
    padding: 2rem 2rem;
  }

  .title {
    font-size: 30px;
  }
} */

/* @media screen and (max-width: 440px) {
  .box {
    width: 90%;
  }

  .container__dados {
    padding: 2rem 1rem;
  }
} */
</style>
