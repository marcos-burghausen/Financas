<template>
  <v-app :theme="themeStore.theme">
    <!-- Toast Notification System -->
    <ToastNotification />
    
    <!-- Renderizar layout se definido na rota -->
    <template v-if="currentLayout">
      <component :is="currentLayout">
        <router-view />
      </component>
    </template>
    
    <!-- Caso contrário, renderizar view diretamente -->
    <router-view v-else />
  </v-app>
</template>

<script setup lang="ts">
import {
    useAuthStore,
    useDashboardStore,
    useExpensesStore,
    useRevenuesStore,
    useThemeStore,
    useUserStore,
    useWalletsStore,
} from "@/store";
import { computed, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import { useTheme } from "vuetify";

const router = useRouter();
const authStore = useAuthStore();
const userStore = useUserStore();
const dashboardStore = useDashboardStore();
const expensesStore = useExpensesStore();
const revenuesStore = useRevenuesStore();
const walletsStore = useWalletsStore();
const themeStore = useThemeStore();
const vuetifyTheme = useTheme();

// Determinar qual layout usar baseado na rota
const currentLayout = computed(() => {
  const layoutMeta = router.currentRoute.value.meta.layout;
  return layoutMeta || null;
});

onMounted(() => {
  // Aplica o tema salvo ao Vuetify
  vuetifyTheme.global.name.value = themeStore.theme;
  
  // Carrega dados da sessão ao inicializar
  authStore.loadFromSession();
  userStore.loadFromSession();
  dashboardStore.loadFromSession();
  expensesStore.loadFromSession();
  revenuesStore.loadFromSession();
  walletsStore.loadFromSession();
});

// Watch para aplicar mudanças de tema em tempo real
watch(() => themeStore.theme, (newTheme) => {
  vuetifyTheme.global.name.value = newTheme;
  console.log("🎨 Tema aplicado:", newTheme);
});
</script>

<style>
/* ========================================
   TEMA GLOBAL - Background adaptativo
   ======================================== */

/* Aplica background do tema no app inteiro */
.v-app {
  background: rgb(var(--v-theme-background)) !important;
  transition: background-color 0.3s ease;
}

/* Garantir que views também herdam o background */
.v-main {
  background: rgb(var(--v-theme-background)) !important;
}

/* Container principal das views */
.v-main > .v-container,
.v-main > div {
  background: transparent;
}

/* Views específicas que precisam de background */
.dashboard-view,
.despesas-view,
.receitas-view,
.contas-view,
.perfil-view,
.home-container {
  background: rgb(var(--v-theme-background)) !important;
  min-height: 100vh;
  transition: background-color 0.3s ease;
}

/* Cards devem usar surface ao invés de background hardcoded */
.v-card {
  transition: background-color 0.3s ease, color 0.3s ease;
}

/* Transições suaves para tema */
* {
  transition-property: background-color, color, border-color;
  transition-duration: 0.3s;
  transition-timing-function: ease;
}

/* Override para backgrounds hardcoded em modo escuro */
.v-theme--dark .dashboard-view,
.v-theme--dark .despesas-view,
.v-theme--dark .receitas-view,
.v-theme--dark .contas-view {
  background: rgb(var(--v-theme-background)) !important;
}
</style>
