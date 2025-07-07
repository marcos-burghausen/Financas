<template>
  <div class="box p-3">
    <div class="container__dados">
      <figure class="figure">
        <img
          src="@/assets/img/2.png"
          class="img"
          alt="logo"
        >
      </figure>
      <h2 class="title">
        Bem vido ao Mr Finanças
      </h2>
      <div class="social__media">
        <ul class="list__social__media">
          <a
            class="link__social__media"
            href="#"
            @click="initiateFacebookLogin()"
          >
            <li class="item__social__media">
              <v-icon
                class="icon__modify"
                icon="mdi-facebook"
              />
            </li>
          </a>
        </ul>
      </div>
      <ErrorMessage />
      <v-form
        v-model="validForm"
        class="form"
        @submit.prevent="login"
      >
        <v-combobox
          v-model="user.email"
          variant="underlined"
          type="email"
          hide-details="auto"
          label="Email"
          :rules="[rules.requiredEmail]"
          class="mb-8 imput"
          autofocus
          autocomplete="on"
          prepend-inner-icon="mdi-email-outline"
        />

        <v-text-field
          v-model="user.password"
          variant="underlined"
          :type="mostrarSenha ? 'text' : 'password'"
          hide-details="auto"
          label="Senha"
          :rules="[rules.requiredSenha]"
          class="mb-5 imput"
          prepend-inner-icon="mdi-lock-outline"
          :append-inner-icon="mostrarSenha ? 'mdi-eye-off' : 'mdi-eye'"
          @click:append-inner="mostrarSenha = !mostrarSenha"
        />

        <div class="container__button">
          <a
            class="link"
            href="#"
          >esqueceu sua senha?</a>
          <a
            class="btn__register"
            href="#"
            @click.prevent="emits('nextStep')"
          >
            cadastre-se.
          </a>
        </div>
        <v-btn
          :disabled="loading || !validForm"
          :loading="loading"
          class="btn mt-5"
          type="submit"
          block
          size="large"
        >
          entrar
        </v-btn>
      </v-form>
    </div>
  </div>
  <ErrorsForm />
</template>

<script setup lang="ts">
import ErrorMessage from "@/components/ErrorMessage.vue";
import ErrorsForm from "@/components/ModalErrorsForm.vue";
import {
  useAuthStore,
  useErrorStore,
  useExpensesStore,
  useRevenuesStore,
  useUserStore,
  useWalletsStore,
} from "@/store";
import http from "@/services/http";
import { useRouter } from "vue-router";
import type { FormLogin, LoginResponse, ApiErrorResponse } from "@/types";
import type { AxiosError, AxiosResponse } from "axios";
import { ref } from "vue";

const emits = defineEmits(["nextStep"]);
const useExpenses = useExpensesStore();
const useRevenues = useRevenuesStore();
const useWallets = useWalletsStore();
const useUser = useUserStore();
const errorStore = useErrorStore();
const router = useRouter();
const useAuth = useAuthStore();

// CORREÇÃO: Removidos os dados de usuário hardcoded.
const user = ref<FormLogin>({
  email: "teste@teste.com",
  password: "123456",
});

let validForm = ref(false);
let mostrarSenha = ref(false); // Mudado para false para começar com a senha oculta
let loading = ref(false);

async function initiateFacebookLogin() {
  errorStore.unsetError();
  try {
    loading.value = true;
    const response = await http.get("/auth/redirect");
    window.location.href = response.data.redirect_url;
  } catch (error) {
    console.error("Erro ao iniciar login do Facebook", error);
    loading.value = false; // Garante que o loading para em caso de erro
  }
}

const login = async () => {
  if (!validForm.value) return; // Prevenção extra
  errorStore.unsetError();
  try {
    loading.value = true;
    const response: AxiosResponse<LoginResponse> = await http.post(
      "/auth",
      user.value
    );
    // SUGESTÃO: Esta lógica poderia ser movida para uma única ação na store, como useAuth.loginAndFetchData(...)
    useAuth.setToken(response.data.token);
    useUser.setUserData(response.data.userData);
    useUser.setMesAno(response.data.data.mesAno);
    useExpenses.setExpensesData(response.data.data.expenses);
    useRevenues.setRevenuesData(response.data.data.revenues);
    useWallets.setWalletsData(response.data.data.wallets);

    router.push({ name: "dashboard" });
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

const rules = {
  requiredEmail: (value: string) => !!value || "O campo email é obrigatório",
  requiredSenha: (value: string) => !!value || "O campo senha é obrigatório",
};
</script>
<style scoped>
/* SEU CSS AQUI (sem alterações) */
.box {
  display: flex;
  padding: 0;
  width: 100%;
  max-width: 500px;
}
.container__dados {
  border-radius: 10px 0 0 10px;
  width: 100%;
  display: flex;
  flex-direction: column;
  justify-items: center;
  padding-inline: 10px;
}
.figure {
  display: flex;
  justify-content: space-evenly;
  align-items: center;
}
.img {
  width: 200px;
}
.title {
  font-size: 28px;
  font-weight: bold;
  color: #fefefe;
  text-align: center;
}
.social__media {
  margin: 1rem 0;
  display: flex;
  justify-content: center;
  text-align: center;
}
.list__social__media {
  display: flex;
  list-style-type: none;
}
.link__social__media:not(:first-child) {
  margin-left: 10px;
}
.link__social__media .item__social__media {
  transition: background-color 0.5s;
}
.link__social__media:hover .item__social__media {
  color: #fff !important;
  border-color: #77d08e;
}
.item__social__media {
  border: 1px solid #bdc3c7;
  border-radius: 50%;
  width: 35px;
  height: 35px;
  line-height: 35px;
  text-align: center;
  color: #95a5a6;
}
.icon__modify {
  color: #7f8c8d;
  padding: 0 5px;
  cursor: pointer;
}
.form {
  display: flex;
  flex-direction: column;
  width: 100% !important;
}
.imput {
  height: 40px;
  color: #ccc;
  width: 100%;
}
.container__button {
  text-align: center;
  display: flex;
  justify-content: space-between;
}
.link {
  color: #0097a7;
  font-size: 16px;
  margin: 0;
  text-align: center;
  text-decoration: none;
}
.btn__register {
  color: #0097a7;
  text-decoration: none;
}
.btn {
  text-transform: uppercase;
  color: #fff;
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
.btn:hover {
  background-color: #e1e1e1;
  border: 1px solid #77d08e;
  color: #77d08e;
}
</style>
