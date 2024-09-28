<template>
  <div class="box">
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
              <mdicon class="icon__modify" name="facebook" />
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
        <v-text-field
          v-model="user.email"
          variant="outlined"
          type="email"
          hide-details="auto"
          label="Email"
          :rules="[rules.requiredEmail]"
          class="mb-7 input"
          autofocus
          autocomplete="on"
        >
          <template #prepend-inner>
            <mdicon class="icon__modify" name="email-outline" />
          </template>
        </v-text-field>

        <v-text-field
          v-model="user.password"
          variant="outlined"
          :type="mostrarSenha ? 'password' : 'text'"
          hide-details="auto"
          label="Senha"
          :rules="[rules.requiredSenha]"
          class="mb-5 input"
        >
          <template #prepend-inner>
            <mdicon class="icon__modify" name="lock" />
          </template>
          <template #append-inner>
            <mdicon
              class="icon__modify"
              :name="mostrarSenha ? 'eye' : 'eye-off'"
              @click="mostrarSenha = !mostrarSenha"
            />
          </template>
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
          class="btn btn__submit"
          type="submit"
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
import { userData } from "@/store/data";
import { useAuthStore } from "@/store/auth";
import { useRouter } from "vue-router";
import http from "@/services/http";

import type { FormLogin } from "@/types/formLogin";
import { ref, type Ref } from "vue";
import { AxiosError } from "axios";

const emits = defineEmits(["nextStep"]);
const useExpenses = useExpensesStore();
const useRevenues = useRevenuesStore();
const useWallets = useWalletsStore();
const useUser = useUserStore();
const errorStore = useErrorStore();
const user: Ref<FormLogin> = ref({
  email: "rafaelburghausen@gmail.com",
  password: "Teste123@",
  // email: "",
  // password: "",
});
const router = useRouter();
const data = userData();
const useAuth = useAuthStore();

let validForm = ref(false);
let mostrarSenha = ref(true);
let loading = ref(false);

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
    const response = await http.post("/auth", user.value);
    useAuth.setToken(response.data.token);
    useUser.setUserData(response.data.user);
    useExpenses.setExpensesData(response.data.userData.expensesData);
    useRevenues.setRevenuesData(response.data.userData.revenuesData);
    useWallets.setWalletsData(response.data.userData.walletsData);
    // data.setTotalCreditCard(response.data.userData.totalCreditCard);
    // data.setTotalBalance(response.data.userData.totalBalance);

    router.push({ name: "dashboard" });
  } catch (error) {
    if (error.response.data.errors) {
      errorStore.setErrorFromForm(error);
    } else {
      errorStore.setErrorFromResponse(error);
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
.input {
  background-color: #1e1e1e !important;
  height: 55px;
  color: #ccc;
  width: 100%;
  border: none;
  background-color: transparent;
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
  border-radius: 15px;
  text-transform: uppercase;
  color: #fff;
  font-size: 10px;
  padding: 5px;
  cursor: pointer;
  font-weight: bold;
  width: 200px;
  align-self: center;
  border: none;
  margin-top: 1rem;
  font-size: 20px;
  background-color: #77d08e;
  border: 1px solid #77d08e;
  transition: background-color 0.5s;
}
.btn__submit {
  /* margin-bottom: 80px; */
}
.btn__submit:hover {
  background-color: #e1e1e1;
  border: 1px solid #77d08e;
  color: #77d08e;
}
</style>
