<template>
  <div class="container">
    <div class="login">
      <div class="logo">
        <router-link :to="{ name: 'home' }">
          <img src="../assets/icons/logo.png" alt="logo" />
        </router-link>
        <h1>Finanças</h1>
      </div>
      <h4 class="bem-vindo">Olá, bem-vindo de volta!</h4>
      <span v-if="errorsForm['errors'].email" class="span-error">{{
        errorsForm["errors"].email[0]
      }}</span>

      <div class="form-container">
        <form class="form" @submit.prevent="login">
          <div class="inputSimples">
            <mdicon class="mdicon" name="email" />
            <input
              v-model="user.email"
              class="input"
              id="email"
              name="email"
              type="text"
            />
            <label class="label" for="email">Email</label>
          </div>
          <div class="error">
            <span v-if="errorsForm['errors'].email" class="span-error">{{
              errorsForm["errors"].email[0]
            }}</span>
          </div>
          <div class="inputSimples">
            <mdicon class="mdicon" name="key" />
            <input
              v-model="user.password"
              class="input"
              id="password"
              name="password"
              type="text"
            />
            <mdicon class="mdicon" name="eye-off" />
            <label for="password" class="label">Senha</label>
          </div>
          <div class="error">
            <span v-if="errorsForm['errors'].password" class="span-error">{{
              errorsForm["errors"].password[0]
            }}</span>
          </div>
          <div class="container-relembreme">
            <div class="container-check">
              <input
                aria-checked="false"
                id="relembreMe"
                role="checkbox"
                type="checkbox"
                value=""
              />
              <label
                for="relembreMe"
                class="v-label theme--dark"
                style="left: 0px; right: auto; position: relative"
                >Relembreme</label
              >
            </div>
            <a href="">Esqueceu sua senha ? </a>
          </div>
          <button class="btn-login" type="submit">
            <!-- <router-link to="/dashboard"> -->
            <span>Entrar</span>
            <!-- </router-link> -->
          </button>
          <div class="criar-conta">
            Não tem conta ?
            <router-link to="/cadastro"> Criar uma conta </router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuth } from "@/stores/auth.js";
import { useRouter } from "vue-router";
import http from "@/services/http.js";
import { reactive } from "vue";
import { userData } from "@/stores/data.js";

const auth = useAuth();
const router = useRouter();

const user = reactive({
  email: "marcos@gmail.com",
  password: "123",
});
const errorsForm = reactive({ errors: {} });

async function login() {
  const data = userData();
  try {
    const res = await http.post("/auth", user);
    auth.setToken(res.data.token);
    console.log(res);
    const resp = await http.post("/me");
    // console.log(resp.data);
    data.setUserName(resp.data.userName);
    data.setTotalExpenses(resp.data.totalExpenses)
    data.setTotalReveues(resp.data.totalReveues)
    data.setTotalCreditCard(resp.data.totalCreditCard)
    data.setTotalBalance(resp.data.totalBalance)
    auth.setUser(resp.data.userName);
    router.push({ name: "dashboard" });
  } catch (error) {
    console.log(error);
    errorsForm["errors"] = error.response.data;
  }
}
</script>

<style scoped>
.container {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}
.login {
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  padding-top: 15px;
  width: 400px !important;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.logo {
  display: flex;
  justify-content: center;
}
.logo img {
  width: 90px;
}
.logo h1 {
  margin: 0px;
  text-align: center;
  color: #ccc;
  margin: 15px 0 0 10px;
  font-size: 50px;
}
.mdicon {
  color: white;
}
.bem-vindo {
  margin: 10px 0;
  color: #ccc;
}
.form-container {
  display: flex;
  justify-content: center;
  width: 100%;
}
.form {
  width: 90%;
}
.inputSimples {
  background-color: #1e1e1e;
  margin: 20px 0 0 0;
  display: flex;
  align-items: center;
  padding-left: 5px;
  position: relative;
  border-radius: 5px;
}
.error {
  height: 20px;
}
.span-error {
  color: rgb(194, 4, 4);
  position: relative;
  top: 0;
  left: 0;
}
.input {
  color: #ccc;
  width: 300px;
  height: 40px;
  background-color: transparent;
  border: 0;
  outline: 0;
}
.input:focus ~ label,
.input:valid ~ label {
  transform: translateY(-26px);
  opacity: 0.9;
}
.label {
  color: #ccc;
  position: absolute;
  left: 30px;
  top: 12px;
  opacity: 0.4;
  cursor: text;
  transition: 0.5s ease-in-out;
}
.container-relembreme {
  display: flex;
  justify-content: space-between;
  margin: 8px 0 20px 0;
}
.container-relembreme label {
  color: #ccc;
}
.container-relembreme a {
  text-decoration: none;
  color: #0097a7;
}
.btn-login {
  width: 100%;
  height: 35px;
  border: none;
  border-radius: 5px;
  background-color: hsla(0, 0%, 100%, 0.12);
}
.criar-conta {
  color: #ccc;
  margin: 20px 0;
}
.criar-conta a {
  text-decoration: none;
  color: #0097a7;
}
</style>