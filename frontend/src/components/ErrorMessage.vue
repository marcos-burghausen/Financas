<template>
  <div
    v-show="errorStore.errorMessage"
    class="container__error"
  >
    <div
      class="erro"
      role="alert"
    >
      <header class="header__erro">
        <v-icon
          class="mdicon__close"
          type="buttom"
          icon="mdi-close"
          @click="errorStore.unsetError()"
        />
      </header>
      <div class="error mb-2">
        <h2 class="titulo__error">
          Ops, algo deu errado
        </h2>
        <span class="error__message">{{ errorStore.errorMessage }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useErrorStore } from "@/store/error";
import { onMounted } from "vue";
const errorStore = useErrorStore();

onMounted(() => {
    errorStore.unsetError();
});
</script>

<style scoped>
.container__error {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 999;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
}
.erro {
  height: auto;
  width: 100%;
  max-width: 350px;
  border-radius: 15px;
  background: #2c2c2e;
  color: #fefefe;
  padding: 0 15px 15px 15px;
}
.header__erro {
  width: 100%;
  padding: 5px 5px 0 0;
  display: flex;
  justify-content: right;
}
.mdicon__close {
  cursor: pointer;
  color: rgba(255, 255, 255, 0.3);
}
.mdicon__close:hover {
  color: #fefefe;
}
.error {
  border-radius: 10px;
  white-space: normal;
  height: auto;
  word-wrap: break-word;
}
.titulo__error {
  color: #f44336;
  text-align: center;
}
.error__message {
  text-align: center;
  /* padding: 5px; */
}
</style>
