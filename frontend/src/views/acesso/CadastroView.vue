<template>
  <Loading v-if="isLoading" />
  <div
    v-else
    class="box"
  >
    <div class="container__decription">
      <figure class="figure">
        <img
          src="@/assets/img/Mr.png"
          style="width: 200px;"
          alt="logo"
        >
        <h2 class="title__2 mt-5 me-2">
          Bem vindo
        </h2>
      </figure>
      <h4 class="sub__title__2">
        Ao seu gerenciador de finaças!
      </h4>
      <p class="sub__title__2 mt-5">
        Insira seus dados pessoais
      </p>
      <p class="sub__title__2">
        e comece a jornada conosco
      </p>
      <button
        class="btn btn__link"
        @click="emits('nextStep')"
      >
        entrar
      </button>
    </div>
    <div class="container__dados">
      <h2 class="title">
        Criar Uma Conta
      </h2>
      <div class="social__media">
        <ul class="list__social__media">
          <a
            class="link__social__media"
            href="#"
          >
            <li class="item__social__media">
              <mdicon
                class="icon__modify"
                name="facebook"
              />
            </li>
          </a>
          <a
            class="link__social__media"
            href="#"
          >
            <li class="item__social__media">
              <mdicon
                class="icon__modify"
                name="google"
              />
            </li>
          </a>
          <a
            class="link__social__media"
            href="#"
          >
            <li class="item__social__media">
              <mdicon
                class="icon__modify"
                name="linkedin"
              />
            </li>
          </a>
        </ul>
      </div>
      <p class="sub__title">
        ou use seu e-mail para inscrição:
      </p>
      <ErrorMessage />
      <form
        class="form"
        @submit.prevent="create"
      >
        <div class="container__input">
          <label
            class="label"
            for="name"
          >Nome</label>
          <mdicon
            class="icon__modify"
            name="account-outline"
          />
          <input
            id="name"
            v-model="user.name"
            class="form-control"
            type="text"
            autocomplete="off"
            name="name"
          >
        </div>
        <div class="error">
          <span
            v-if="errorsForm.name"
            class="span__error"
          >{{
            errorsForm.name[0]
          }}</span>
        </div>
        <div class="container__input">
          <label
            class="label"
            for="email"
          >Email</label>
          <mdicon
            class="icon__modify"
            name="email-outline"
          />
          <input
            id="email"
            v-model="user.email"
            class="form-control"
            type="email"
            autocomplete="off"
            name="email"
          >
        </div>
        <div class="error">
          <span
            v-if="errorsForm.email"
            class="span__error"
          >{{
            errorsForm.email[0]
          }}</span>
        </div>
        <div class="container__input">
          <label
            class="label"
            for="password"
          >Password</label>
          <mdicon
            class="icon__modify"
            name="lock"
          />
          <input
            id="password"
            v-model="user.password"
            class="form-control"
            type="password"
            autocomplete="off"
            name="password"
          >
        </div>
        <div class="error">
          <span
            v-if="errorsForm.password"
            class="span__error"
          >{{
            errorsForm.password[0]
          }}</span>
        </div>
        <div class="container__button">
          <button
            class="btn__register"
            @click.prevent="emits('nextStep')"
          >
            <span>já tem uma
              conta </span>conecte-se.
          </button>
        </div>
        <button
          class="btn btn__submit"
          type="submit"
        >
          cadastro
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import ErrorMessage from "@/components/ErrorMessage.vue";
import Loading from "@/components/Loading.vue";

import { useErrorStore } from "@/store/error";
import http from "@/services/http";

import { ref } from "vue";
import type { FormCadastro } from "@/types/formCadastro";

const errorsForm: FormCadastro = ref({});
const emits = defineEmits(["nextStep"]);
const errorStore = useErrorStore();
const user: FormCadastro = ref({});
const isLoading = ref(false);

async function create() {
    isLoading.value = true;
    try {
        await http.post("/create", user.value);
        emits("nextStep");
    } catch (error: unknown) {
        if (error.response?.data?.errors) {
            errorsForm.value = error.response?.data?.errors;
        } else {
            console.log(error);
            errorStore.setErrorFromResponse(error);
        }
    } finally {
        isLoading.value = false;
    }
}
</script>
<style scoped>
.box {
    display: flex;
    box-shadow: 1px 1px 10px 5px #77d08e;
    border-radius: 10px;
    padding: 0;
    width: 80%;
    max-width: 1250px;
}

.container__dados {
    border-radius: 10px 0 0 10px;
    width: 60%;
    display: flex;
    flex-direction: column;
    justify-items: center;
    padding: 2rem 6rem;
}

.title {
    font-size: 35px;
    font-weight: bold;
    color: #fefefe;
    text-align: center;
}

.title__2 {
    font-size: 40px;
    font-weight: bold;
    color: #515050;
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
    transition: background-color .5s;
}

.link__social__media:hover .item__social__media {
    /* background-color: #0097a7; */
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
}

.sub__title {
    color: #fefefe;
    text-align: center;
}

.sub__title__2 {
    color: #515050;
    text-align: center;

}

.form {
    display: flex;
    flex-direction: column;
    width: 100% !important;
}

.container__input {
    background-color: #1e1e1e;
    margin: 20px 0 0 0;
    display: flex;
    align-items: center;
    padding-left: 5px;
    position: relative;
    border-radius: 5px;
}

.container__input input {
    height: 55px;
    color: #ccc;
    width: 100%;
    border: none;
    background-color: transparent;
}

.container__input input:focus {
    box-shadow: 0 0 0 0.15rem #0096a72f !important;
}

.label {
    color: #fefefe;
    background-color: transparent;
    position: absolute;
    left: 10px;
    top: -25px;
    opacity: 0.4;
    cursor: text;
    transition: 0.5s ease-in-out;
}

.error {
    height: 20px;
}

.span__error {
    color: rgb(194, 4, 4);
    position: relative;
    top: 0;
    left: 0;
}

.container__button {
    text-align: center;
}

.link {
    color: #0097a7;
    font-size: 16px;
    margin: 15px 0;
    text-align: center;
}

.btn__register {
    display: none;
}

.btn__register span {
    color: #fefefe;
}

.container__decription {
    width: 40%;
    background: #77d08e;
    border-radius: 10px 0 0 10px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.figure {
    display: flex;
    justify-content: space-evenly;
    align-items: center;
    margin-bottom: 50px;
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
    transition: background-color .5s;
}

.btn__submit:hover {
    background-color: #e1e1e1;
    border: 1px solid #77d08e;
    color: #77d08e;
}

.btn__link {
    margin-top: 60px;
    background-color: transparent;
    border: 1px solid #fff;
    transition: background-color .5s;
}

.btn__link:hover {
    background-color: #fff;
    color: #58af9b;
}

@media screen and (max-width: 1201px) {

    .box {
        width: 90%;
    }

    .container__dados {
        padding: 2rem 4rem;
    }


}

@media screen and (max-width: 920px) {

    .container__dados {
        width: 100%;
        padding: 2rem 4rem;
    }

    .container__decription {
        display: none;
    }

    .btn__register {
        display: inline;
        color: #0097a7;
        font-size: 16px;
        margin: 15px 0;
        background: transparent;
        border: none;
    }

    .container__button {
        display: flex;
        justify-content: space-between;
    }

}

@media screen and (max-width: 740px) {
    .container__dados {
        padding: 2rem 2rem;
    }

    .title {
        font-size: 30px;
    }
}

@media screen and (max-width: 440px) {
    .box {
        width: 95%;
    }

    .container__dados {
        padding: 2rem 1rem;
    }
}
</style>