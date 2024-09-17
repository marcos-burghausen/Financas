<template>
  <div class="home__mobile">
    <EntrarMobile v-if="step === 0" @next-step="step = 1" />
    <CadastroMobile v-if="step === 1" @next-step="step = 0" />
  </div>
  <div class="home__web">
    <EntrarWeb v-if="step === 0" @next-step="step = 1" />
    <CadastroWeb v-if="step === 1" @next-step="step = 0" />
  </div>
</template>

<script setup lang="ts">
import EntrarMobile from "@/views/mobile/EntrarMobileView.vue";
import EntrarWeb from "@/views/web/EntrarView.vue";
import CadastroMobile from "@/views/mobile/CadastroView.vue";
import CadastroWeb from "@/views/web/CadastroView.vue";
import { ref } from "vue";

const step = ref(0);
</script>
<style scoped>
.home__mobile {
  height: 100%;
  display: flex;
  padding-top: 50px;
  align-items: flex-start;
  justify-content: center;
}
.home__web {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

@media screen and (min-width: 501px) {
  .home__mobile {
    display: none;
  }
}

@media screen and (max-width: 500px) {
  .home__web {
    display: none;
  }
}
</style>
