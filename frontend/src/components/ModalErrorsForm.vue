<template>
  <div
    v-show="errorStore.errorsFormMessage"
    class="container__modal"
  >
    <div
      class="erro"
      role="alert"
    >
      <header class="header__modal">
        <v-icon
          class="mdicon__close"
          type="buttom"
          icon="mdi-close"
          @click="errorStore.unsetErrorsForm()"
        />
      </header>
      <v-chip
        v-for="(message, index) in errorStore.errorsFormMessage"
        :key="index"
        class="error d-flex mb-2"
        width="100%"
        color="red"
      >
        <span class="error__message">{{ message[0] }}</span>
      </v-chip>
    </div>
  </div>
</template>
<script setup lang="ts">

import { useErrorStore } from "@/store/error";

const errorStore = useErrorStore();

</script>

<style scoped>
.container__modal {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 999;
    background: rgba(0, 0, 0, 0.50);
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

.header__modal {
  width: 100%;
  padding: 5px 0 0 0;
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
.error__message {
  padding: 5px;
}
</style>