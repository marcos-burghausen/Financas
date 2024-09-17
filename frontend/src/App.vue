<template>
  <div class="d-flex h-100">
    <template v-if="useAuth.isAuthenticated">
      <MenuLateral
        :menu-expandido="menuExpandido"
        @expandirMenu="menuExpandido = !menuExpandido"
      />
    </template>

    <div class="containerApp">
      <template class="mobile" v-if="useAuth.isAuthenticated">
        <CabecalhoMobile
          :menu-expandido="menuExpandido"
          @expandirMenu="menuExpandido = !menuExpandido"
        />
      </template>
      <!-- <template class="web" v-if="useAuth.isAuthenticated">
        <Cabecalho
          :menu-expandido="menuExpandido"
          @expandirMenu="menuExpandido = !menuExpandido"
        />
      </template> -->
      <router-view />
    </div>
  </div>
</template>

<script setup lang="ts">
import MenuLateral from "@/components/MenuLateral.vue";
import TesteView from "./components/TesteView.vue";
import Cabecalho from "@/components/Cabecalho.vue";
import CabecalhoMobile from "@/components/mobile/Cabecalho.vue";

import { ref } from "vue";
import { useAuthStore } from "@/store/auth.js";

const useAuth = useAuthStore();

const menuExpandido = ref(false);
</script>

<style scoped>
.containerApp {
  width: 100%;
}

@media screen and (min-width: 501px) {
  .mobile {
    display: none;
  }
}

@media screen and (max-width: 500px) {
  .web {
    display: none;
  }
}
</style>
