<template>
  <router-view />
</template>

<script setup lang="ts">
import { onMounted } from "vue";
import {
  useAuthStore,
  useUserStore,
  useDashboardStore,
  useExpensesStore,
  useRevenuesStore,
  useWalletsStore,
} from "@/store";

const authStore = useAuthStore();
const userStore = useUserStore();
const dashboardStore = useDashboardStore();
const expensesStore = useExpensesStore();
const revenuesStore = useRevenuesStore();
const walletsStore = useWalletsStore();

onMounted(() => {
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
