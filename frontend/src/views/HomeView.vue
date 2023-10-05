<template>
  <div class="row justify-content-center align-items-center h-100">
    <Entrar v-if="step === 0" @next-step="step = 1" />
    <Cadastro v-if="step === 1" @next-step="step = 0" />
  </div>
</template>

<script setup lang="ts">
import Entrar from "@/views/acesso/EntrarView.vue";
import Cadastro from "@/views/acesso/CadastroView.vue";
import { ref } from "vue";

const step = ref(0);
</script>
<style scoped></style>
