<template>
  <div class="container">
    <div class="cadastro">
      <div class="logo">
        <router-link :to="{ name: 'home' }">
          <img src="../assets/icons/logo.png" alt="logo" />
        </router-link>
        <h1>Finanças</h1>
      </div>
      <h4 class="bem-vindo">Criar uma conta</h4>

      <div class="form-container">
        <form class="form" @submit.prevent="create">
          <div class="inputSimples">
            <mdicon class="mdicon" name="account" />
            <input
              v-model="user.name"
              class="input"
              id="name"
              name="name"
              type="text"
              required
            />
            <label class="label" for="name">Nome</label>
          </div>
          <div class="error">
            <span v-if="errorsForm['errors'].name" class="span-error">{{
              errorsForm["errors"].name[0]
            }}</span>
          </div>
          <div class="inputSimples">
            <mdicon class="mdicon" name="email" />
            <input
              v-model="user.email"
              class="input"
              id="email"
              name="email"
              type="text"
              required
            />
            <label class="label" for="email">Email</label>
          </div>
          <div class="error">
            <span v-if="errorsForm['errors'].email" class="span-error">{{
              errorsForm["errors"].email[0]
            }}</span>
          </div>
          <div class="inputSimples">
            <mdicon class="mdicon" name="lock" />
            <input
              v-model="user.password"
              class="input"
              id="senha"
              name="password"
              type="password"
              required
            />
            <label for="senha" class="label">Senha</label>
          </div>
          <div class="error">
            <span v-if="errorsForm['errors'].password" class="span-error">{{
              errorsForm["errors"].password[0]
            }}</span>
          </div>
          <div class="inputSimples">
            <mdicon class="mdicon" name="lock-plus" />
            <input
              v-model="user.confirmPassword"
              class="input"
              id="confirmPassword"
              name="confirmPassword"
              type="password"
              required
            />
            <label for="confirmPassword" class="label"
              >Confirme sua senha</label
            >
          </div>
          <div class="error">
            <span v-if="errorsForm['errors'].password" class="span-error">{{
              errorsForm["errors"].password[0]
            }}</span>
          </div>
          <div class="container-termos">
            <div class="container-check">
              <input
                aria-checked="false"
                id="concordar"
                role="checkbox"
                type="checkbox"
                value=""
              />
              <label for="concordar">Concordar com</label>
              <a href=""> termos e politicas de privacidade.</a>
            </div>
          </div>
          <button class="btn-login" type="submit">
            <span>cadastrar</span>
          </button>
          <div class="Conecte">
            Já tem conta ?
            <a href="/login"> Conecte-se </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import http from "@/services/http.js";
import { reactive } from "vue";
import { useRouter } from "vue-router";

const user = reactive({
  // name: "Marcos",
  // email: "marcos@gmail.com",
  // password: "123",
  // confirmPassword: "123",
});
const router = useRouter();
const errorsForm = reactive({
  errors: {},
});

async function create() {
  try {
    const { data } = await http.post("/create", user);
    console.log(data);
    // router.push({ name: "login" });
  } catch (error) {
    errorsForm["errors"] = error.response.data["errors"];
  }
}
</script>

<style scoped>
.container {
  height: calc(100vh - 50px);
  display: flex;
  justify-content: center;
  align-items: center;
}
.cadastro {
  box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
  padding-top: 15px;
  width: 400px;
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
  width: 260px;
  height: 40px;
  background-color: transparent;
  border: 0;
  outline: 0;
}
.input:focus ~ label,
.input:valid ~ label {
  transform: translateY(-32px);
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
.container-termos {
  display: flex;
  justify-content: space-between;
  margin: 15px 0;
}
.container-termos label {
  color: #ccc;
}
.container-termos a {
  text-decoration: none;
  color: #0097a7;
}
.btn-login {
  width: 100%;
  height: 35px;
  border: none;
  border-radius: 5px;
  background-color: hsla(0, 0%, 100%, 0.12);
  margin-bottom: 15px;
}
.Conecte {
  display: flex;
  justify-content: center;
  color: #ccc;
  margin: 20px 0;
}
.Conecte a {
  margin-left: 8px;
  text-decoration: none;
  color: #0097a7;
}
</style>