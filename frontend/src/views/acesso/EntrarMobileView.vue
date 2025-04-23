<template>
  <div class="box p-3">
    <div class="container__dados">
      <figure class="figure">
        <img src="@/assets/img/2.png" class="img" alt="logo" />
      </figure>
      <h2 class="title">Bem vido ao Mr Finanças</h2>
      <div class="social__media">
        <ul class="list__social__media">
          <a
            class="link__social__media"
            href="#"
            @click="initiateFacebookLogin()"
          >
            <li class="item__social__media">
              <v-icon class="icon__modify" icon="mdi-facebook" />
            </li>
          </a>
          <!-- <a class="link__social__media" href="#">
            <li class="item__social__media">
              <mdicon class="icon__modify" name="google" />
            </li>
          </a>
          <a class="link__social__media" href="#">
            <li class="item__social__media">
              <mdicon class="icon__modify" name="linkedin" />
            </li>
          </a> -->
        </ul>
      </div>
      <!-- <p class="sub__title">ou use sua conta de e-mail:</p> -->
      <ErrorMessage />
      <v-form v-model="validForm" class="form" @submit.prevent="login">
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
          :type="mostrarSenha ? 'password' : 'text'"
          hide-details="auto"
          label="Senha"
          :rules="[rules.requiredSenha]"
          class="mb-5 imput"
          prepend-inner-icon="mdi-lock-outline"
          :append-inner-icon="mostrarSenha ? 'mdi-eye' : 'mdi-eye-off'"
          @click:append-inner="mostrarSenha = !mostrarSenha"
        >
          <!-- <template #prepend-inner>
            <mdicon
              class="icon__modify"
              name="lock"
            />
          </template> -->
          <!-- <template #append-inner>
            <mdicon
              class="icon__modify"
              :name="mostrarSenha ? 'eye' : 'eye-off'"
              @click="mostrarSenha = !mostrarSenha"
            />
          </template> -->
        </v-text-field>

        <div class="container__button">
          <a class="link" href="#">esqueceu sua senha?</a>
          <a class="btn__register" href="#" @click.prevent="emits('nextStep')">
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
import ErrorsForm from "@/components/ModalErrorsForm.vue";
import ErrorMessage from "@/components/ErrorMessage.vue";

import { useExpensesStore } from "@/store/expenses";
import { useRevenuesStore } from "@/store/revenues";
import { useWalletsStore } from "@/store/wallets";
import { useErrorStore } from "@/store/error";
import { useUserStore } from "@/store/user";
import { useAuthStore } from "@/store/auth";
import { useRouter } from "vue-router";
import http from "@/services/http";

import type { AxiosError, AxiosResponse } from "axios";
import type {
  FormLogin,
  LoginResponse,
  User,
  WalletData,
  Category,
} from "@/types";
import { ref } from "vue";

const emits = defineEmits(["nextStep"]);
const useExpenses = useExpensesStore();
const useRevenues = useRevenuesStore();
const useWallets = useWalletsStore();
const useUser = useUserStore();
const errorStore = useErrorStore();
const user = ref<FormLogin>({
  email: "rafaelburghausen@gmail.com",
  password: "Teste123@",
});
const router = useRouter();
const useAuth = useAuthStore();

let validForm = ref(false);
let mostrarSenha = ref(true);
let loading = ref(false);

interface ApiErrorResponse {
  errors?: Record<string, string[]>;
  message?: string;
}

async function initiateFacebookLogin() {
  // errorStore.unsetError();
  try {
    loading.value = true;
    const response = await http.get("/auth/redirect");
    window.location.href = response.data.redirect_url;
  } catch (error) {
    console.error("Erro ao iniciar login do Facebook", error);
  }
}

async function login() {
  errorStore.unsetError();
  try {
    loading.value = true;
    const response: AxiosResponse<LoginResponse> = await http.post(
      "/auth",
      user.value
    );
    useAuth.setToken(response.data.token);
    useUser.setUserData(response.data.user);
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
      // if (error.response.data.errors) {
      //   errorStore.setErrorFromForm(error);
      // } else {
      //   errorStore.setErrorFromResponse(error);
    }
  } finally {
    loading.value = false;
  }
}

const rules = {
  requiredEmail: (value: string) => !!value || "O campo email é obrigatório",
  requiredSenha: (value: string) => !!value || "O campo senha é obrigatório",
};
</script>
<style scoped>
.box {
  display: flex;
  padding: 0;
  width: 100%;
  max-width: 500px;
}
.imput {
  height: 40px;
  color: #ccc;
  width: 100%;
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
.form {
  display: flex;
  flex-direction: column;
  width: 100% !important;
}
.icon__modify {
  color: #7f8c8d;
  padding: 0 5px;
  cursor: pointer;
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
</style>
