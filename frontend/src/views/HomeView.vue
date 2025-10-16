<template>
  <div class="home-container">
    <LoginView
      v-if="step === 0"
      @next-step="step = 1"
    />
    <RegisterView
      v-if="step === 1"
      @next-step="step = 0"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import LoginView from "./acesso/LoginView.vue";
import RegisterView from "./acesso/RegisterView.vue";

const step = ref(0);
</script>

<style scoped>
.home-container {
  height: 100vh;
  width: 100%;
  overflow: hidden;
}
</style>
