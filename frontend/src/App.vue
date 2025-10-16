<template>
  <router-view />
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
import { onMounted } from "vue";
import { useTheme } from "vuetify";

const authStore = useAuthStore();
const userStore = useUserStore();
const dashboardStore = useDashboardStore();
const expensesStore = useExpensesStore();
const revenuesStore = useRevenuesStore();
const walletsStore = useWalletsStore();
const themeStore = useThemeStore();
const vuetifyTheme = useTheme();

onMounted(() => {
  // Aplica o tema salvo
  vuetifyTheme.global.name.value = themeStore.themeName;
  
  // Carrega dados da sessão ao inicializar
  authStore.loadFromSession();
  userStore.loadFromSession();
  dashboardStore.loadFromSession();
  expensesStore.loadFromSession();
  revenuesStore.loadFromSession();
  walletsStore.loadFromSession();
});
</script>

<style scoped></style>
