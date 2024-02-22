<template>
  <div
    v-show="errorStore.errorsFormMessage"
    class="container__modal"
  >
    <div class="modal">
      <header class="header__modal">
        <span class="title">Existem erros no formulário</span>
        <mdicon
          class="mdicon__close"
          type="buttom"
          name="close"
          @click="closeModal"
        />
      </header>
      <v-chip
        v-for="(error, index) in errorStore.errorsFormMessage"
        :key="index"
        class="error d-flex mb-2"
        width="100%"
        color="red"
      >
        {{ error[0] }}
      </v-chip>
    </div>
  </div>
</template>
<script setup lang="ts">

import { useErrorStore } from "@/store/error";
const errorStore = useErrorStore();

const closeModal = () => {
    errorStore.unsetErrorsForm;
    console.log("1");
};




</script>

<style scoped>
.container__modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.50);
}

.modal {
    background: #2c2c2e;
    position: fixed;
    color: #fefefe;
    z-index: 999;
    top: 35%;
    left: 50%;
    width: auto;
    height: auto;
    margin-left: -200px;
    border-radius: 20px;
    padding: 15px;
}

.header__modal {
    display: flex;
    justify-content: space-between;
}

.title {
    text-align: center;
}

.mdicon__close {
    cursor: pointer;
    color: rgba(255, 255, 255, 0.3);
}

.mdicon__close:hover {
    color: #fefefe;
}

.error {
    height: 20px;
}

</style>