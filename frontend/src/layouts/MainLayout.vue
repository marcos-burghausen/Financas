<template>
  <div class="main-layout">
    <!-- HEADER FIXO GLOBAL -->
    <header class="layout-header">
      <div class="header-container">
        <!-- Menu Button + Logo -->
        <div class="header-left">
          <v-btn
            icon
            variant="text"
            @click="drawer = !drawer"
            class="menu-toggle d-lg-none"
          >
            <v-icon icon="mdi-menu" size="24" />
          </v-btn>
          
          <div class="logo-section">
            <v-icon icon="mdi-cash-multiple" size="32" color="primary" />
            <span class="logo-text d-none d-sm-inline">MrFinancas</span>
          </div>
        </div>

        <!-- Título da Página -->
        <div class="header-center">
          <h1 class="page-title">{{ pageTitle }}</h1>
        </div>

        <!-- Header Right: Notificações, Tema, Perfil -->
        <div class="header-right">
          <!-- Theme Toggle -->
          <v-btn
            icon
            variant="text"
            @click="toggleTheme"
            title="Alternar tema"
          >
            <v-icon :icon="themeStore.theme === 'light' ? 'mdi-moon-waning-crescent' : 'mdi-white-balance-sunny'" />
          </v-btn>

          <!-- Notifications Badge -->
          <v-menu
            v-model="showNotificationMenu"
            transition="slide-x-transition"
            offset-y
          >
            <template #activator="{ props }">
              <v-btn
                v-bind="props"
                icon
                variant="text"
                title="Notificações"
              >
                <v-badge
                  :content="notificationCount"
                  :value="notificationCount > 0"
                  color="error"
                >
                  <v-icon icon="mdi-bell" />
                </v-badge>
              </v-btn>
            </template>

            <!-- Notification Menu Content -->
            <v-list class="notification-menu">
              <v-list-item
                title="Notificações"
                subtitle="Você tem 3 notificações novas"
              >
                <template #prepend>
                  <v-icon icon="mdi-bell" color="warning" />
                </template>
              </v-list-item>

              <v-divider />

              <!-- Sample Notifications -->
              <v-list-item
                title="Nova despesa registrada"
                subtitle="Há 2 minutos"
              >
                <template #prepend>
                  <v-icon icon="mdi-cash-remove" size="small" />
                </template>
              </v-list-item>

              <v-list-item
                title="Novo cartão adicionado"
                subtitle="Há 1 hora"
              >
                <template #prepend>
                  <v-icon icon="mdi-credit-card" size="small" />
                </template>
              </v-list-item>

              <v-list-item
                title="Saldo atualizado"
                subtitle="Há 3 horas"
              >
                <template #prepend>
                  <v-icon icon="mdi-bank" size="small" />
                </template>
              </v-list-item>

              <v-divider />

              <v-list-item @click="goToNotifications">
                <v-btn
                  block
                  variant="tonal"
                  color="primary"
                  size="small"
                  append-icon="mdi-arrow-right"
                >
                  Ver todas as notificações
                </v-btn>
              </v-list-item>
            </v-list>
          </v-menu>

          <!-- Profile Menu -->
          <v-menu
            transition="slide-x-transition"
            offset-y
          >
            <template #activator="{ props }">
              <v-btn
                v-bind="props"
                icon
                variant="text"
              >
                <!-- <v-avatar
                  :image="userStore.user?.avatar || 'https://via.placeholder.com/40'"
                  size="32"
                /> -->
              </v-btn>
            </template>

            <v-list>
              <v-list-item :title="`${userStore.user?.name}`" :subtitle="userStore.user?.email" disabled />
              <v-divider />
              <v-list-item
                title="Perfil"
                prepend-icon="mdi-account"
                @click="$router.push({ name: 'perfil' })"
              />
              <v-list-item
                title="Configurações"
                prepend-icon="mdi-cog"
                @click="$router.push({ name: 'notificacoes' })"
              />
              <v-divider />
              <v-list-item
                title="Sair"
                prepend-icon="mdi-logout"
                @click="logout"
              />
            </v-list>
          </v-menu>
        </div>
      </div>
    </header>

    <!-- LAYOUT COM MENU LATERAL E CONTEÚDO -->
    <div class="layout-wrapper">
      <!-- SIDEBAR NAVIGATION -->
      <aside class="layout-sidebar" :class="{ 'sidebar-open': drawer }">
        <nav class="sidebar-nav">
          <div class="nav-section">
            <p class="nav-label">Menu Principal</p>
            <div class="nav-items">
              <router-link
                v-for="item in mainMenuItems"
                :key="item.route"
                :to="{ name: item.route }"
                class="nav-item"
                :class="{ active: isActiveRoute(item.route) }"
                @click="drawer = false"
              >
                <v-icon :icon="item.icon" size="20" />
                <span>{{ item.label }}</span>
              </router-link>
            </div>
          </div>

          <v-divider class="my-3" />

          <div class="nav-section">
            <p class="nav-label">Controle</p>
            <div class="nav-items">
              <router-link
                v-for="item in controlMenuItems"
                :key="item.route"
                :to="{ name: item.route }"
                class="nav-item"
                :class="{ active: isActiveRoute(item.route) }"
                @click="drawer = false"
              >
                <v-icon :icon="item.icon" size="20" />
                <span>{{ item.label }}</span>
              </router-link>
            </div>
          </div>

          <v-divider class="my-3" />

          <div class="nav-section">
            <p class="nav-label">Administrativo</p>
            <div class="nav-items">
              <router-link
                v-for="item in filteredAdminMenuItems"
                :key="item.route"
                :to="{ name: item.route }"
                class="nav-item"
                :class="{ active: isActiveRoute(item.route) }"
                @click="drawer = false"
              >
                <v-icon :icon="item.icon" size="20" />
                <span>{{ item.label }}</span>
              </router-link>
            </div>
          </div>

          <v-divider class="my-3" />

          <!-- PERFIL E LOGOUT -->
          <div class="nav-section profile-section">
            <div class="user-profile">
              <v-avatar size="40" color="primary">
                {{ userInitial }}
              </v-avatar>
              <div class="user-info">
                <p class="user-name">{{ userStore.userData?.name || "Usuário" }}</p>
                <p class="user-type">{{ userTypeLabel }}</p>
              </div>
            </div>
            <v-btn
              block
              variant="outlined"
              size="small"
              prepend-icon="mdi-logout"
              @click="logout"
              class="mt-3"
            >
              Sair
            </v-btn>
          </div>
        </nav>
      </aside>

      <!-- OVERLAY MOBILE -->
      <div
        v-if="drawer"
        class="sidebar-overlay d-lg-none"
        @click="drawer = false"
      />

      <!-- MAIN CONTENT -->
      <main class="layout-main">
        <div class="content-wrapper">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore, useThemeStore, useUserStore } from "@/store";
import { computed, onMounted, ref, watch } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const userStore = useUserStore();
const authStore = useAuthStore();
const themeStore = useThemeStore();

const drawer = ref(false);
const notificationCount = ref(3); // Dados fictícios
const showNotificationMenu = ref(false);

// Menu Items
const mainMenuItems = [
  { route: "dashboard", label: "Dashboard", icon: "mdi-view-dashboard" },
  { route: "despesas", label: "Despesas", icon: "mdi-cash-remove" },
  { route: "receitas", label: "Receitas", icon: "mdi-cash-plus" },
  { route: "contas", label: "Contas", icon: "mdi-bank" },
  { route: "cartoes", label: "Cartões", icon: "mdi-credit-card" },
  { route: "veiculos", label: "Veículos", icon: "mdi-car" },
];

const controlMenuItems = [
  { route: "categorias", label: "Categorias", icon: "mdi-label-multiple", requiresAuth: false },
  { route: "notificacoes", label: "Notificações", icon: "mdi-bell", requiresAuth: false },
];

const adminMenuItems = [
  { route: "perfil", label: "Perfil", icon: "mdi-account", requiresAuth: false },
  { route: "admin", label: "Painel Admin", icon: "mdi-shield-crown", role: "admin", requiresAuth: true },
  { route: "trader", label: "Painel Trader", icon: "mdi-chart-line", role: "trader", requiresAuth: true },
];

// Page Title (será atualizado pela rota)
const pageTitle = computed(() => {
  const routeName = router.currentRoute.value.name;
  const menuItem = [
    ...mainMenuItems,
    ...controlMenuItems,
    ...adminMenuItems,
  ].find((item) => item.route === routeName);
  return menuItem?.label || "Dashboard";
});

// Month/Year Display
const monthDisplay = computed(() => {
  const mesAno = userStore.getMesAno();
  const [year, month] = mesAno.split('-');
  const date = new Date(`${year}-${month}-01`);
  const monthName = date.toLocaleString("pt-BR", { month: "long" });
  const currentYear = new Date().getFullYear();

  if (parseInt(year) !== currentYear) {
    return `${monthName.substring(0, 3).toUpperCase()}.${year}`;
  }
  return monthName.charAt(0).toUpperCase() + monthName.slice(1);
});

const isCurrentMonth = computed(() => {
  const mesAno = userStore.getMesAno();
  const today = new Date();
  const todayMesAno = today.toISOString().slice(0, 7);
  return mesAno === todayMesAno;
});

// Month Navigation
const previousMonth = () => {
  const mesAno = userStore.getMesAno();
  const [year, month] = mesAno.split('-');
  const current = new Date(`${year}-${month}-01`);
  current.setMonth(current.getMonth() - 1);
  userStore.setMesAno(current.toISOString().slice(0, 7));
};

const nextMonth = () => {
  const mesAno = userStore.getMesAno();
  const [year, month] = mesAno.split('-');
  const current = new Date(`${year}-${month}-01`);
  current.setMonth(current.getMonth() + 1);
  userStore.setMesAno(current.toISOString().slice(0, 7));
};

const goToToday = () => {
  const today = new Date();
  userStore.setMesAno(today.toISOString().slice(0, 7));
};

// Utilities
const toggleTheme = () => {
  themeStore.toggleTheme();
};

const goToNotifications = () => {
  showNotificationMenu.value = false;
  router.push({ name: "notificacoes" });
};

const isActiveRoute = (routeName: string) => {
  return router.currentRoute.value.name === routeName;
};

// User profile computeds
const userInitial = computed(() => {
  const name = userStore.userData?.name || "U";
  return name.charAt(0).toUpperCase();
});

const userTypeLabel = computed(() => {
  const type = userStore.userData?.type || "user";
  const typeMap: Record<string, string> = {
    user: "Usuário",
    USER: "Usuário",
    trader: "Trader",
    TRADER: "Trader",
    admin: "Administrador",
    ADMIN: "Administrador",
    user_trader: "Usuário + Trader",
    USER_TRADER: "Usuário + Trader",
    full: "Full Access",
    FULL: "Full Access",
  };
  return typeMap[type] || type;
});

// Função reativa para verificar acesso (usando computed para reatividade)
const canAccessAdmin = computed(() => {
  if (!userStore.userData) {
    return false;
  }
  const userRole = (userStore.userData?.type || "user").toUpperCase();
  const hasAccess = userRole === "ADMIN" || userRole === "FULL";
  return hasAccess;
});

const canAccessTrader = computed(() => {
  if (!userStore.userData) {
    return false;
  }
  const userRole = (userStore.userData?.type || "user").toUpperCase();
  const hasAccess = userRole === "TRADER" || userRole === "USER_TRADER" || userRole === "FULL";
  return hasAccess;
});

// Filtrar adminMenuItems baseado em role do usuário
const filteredAdminMenuItems = computed(() => {
  return adminMenuItems.filter((item) => {
    if (!item.requiresAuth) return true; // Perfil sempre mostra
    if (item.role === "admin") return canAccessAdmin.value;
    if (item.role === "trader") return canAccessTrader.value;
    return true;
  });
});

const logout = async () => {
  authStore.clear();
  userStore.clear();
  router.push({ name: "home" });
};

// Close drawer when route changes (mobile)
watch(
  () => router.currentRoute.value,
  () => {
    if (drawer.value) {
      drawer.value = false;
    }
  }
);

// Carregar dados do usuário ao montar o layout
onMounted(() => {
  // Se o userData não está carregado, tenta carregar da sessão
  if (!userStore.userData) {
    userStore.loadFromSession();
  } else {
  }
});

// Watch para atualizar o menu quando userData muda
watch(
  () => userStore.userData,
  (newUserData) => {
  }
);
</script>

<style scoped lang="scss">
.main-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  // background: rgb(var(--v-theme-background));
  transition: background-color 0.3s ease;
}

/* ========================================
HEADER FIXO GLOBAL
======================================== */
.layout-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: rgb(var(--v-theme-surface));
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s ease;

  &:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
}

.header-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1rem;
  height: 64px;
  max-width: 100%;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-shrink: 0;
}

.menu-toggle {
  transition: transform 0.2s ease;

  &:active {
    transform: scale(0.95);
  }
}

.logo-section {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: rgb(var(--v-theme-primary));
  cursor: pointer;

  .logo-text {
    font-size: 1.1rem;
    white-space: nowrap;
  }

  @media (max-width: 600px) {
    gap: 0.25rem;
  }
}

.header-center {
  flex: 1;
  text-align: center;
  min-width: 0;

  .page-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: rgb(var(--v-theme-on-background));

    @media (max-width: 600px) {
      font-size: 1rem;
    }
  }
}

.header-right {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-shrink: 0;
}

/* ========================================
   MONTH SELECTOR BAR
   ======================================== */
.month-selector-bar {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 0.75rem;
  background: rgb(var(--v-theme-surface));
  border-bottom: 1px solid rgba(0, 0, 0, 0.08);
  height: 56px;
}

.month-selector {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: rgba(var(--v-theme-primary), 0.05);
  padding: 0.5rem 1rem;
  border-radius: 24px;
  border: 1px solid rgba(var(--v-theme-primary), 0.2);
}

.month-display {
  min-width: 150px;
  text-align: center;

  .month-text {
    font-size: 1.1rem;
    font-weight: 600;
    color: rgb(var(--v-theme-on-background));
    display: inline-block;
    letter-spacing: 0.5px;
  }
}

/* ========================================
   LAYOUT WRAPPER (Header + Content)
   ======================================== */
.layout-wrapper {
  display: flex;
  flex: 1;
  margin-top: 70px; // 64px header + 56px month bar
}

/* ========================================
   SIDEBAR NAVIGATION
   ======================================== */
.layout-sidebar {
  position: fixed;
  left: 0;
  top: 60px;
  width: 250px;
  height: calc(100vh - 65px);
  background: rgb(var(--v-theme-surface));
  border-right: 1px solid rgba(0, 0, 0, 0.08);
  overflow-y: auto;
  z-index: 999;
  transition: all 0.3s ease;

  @media (max-width: 1024px) {
    position: fixed;
    width: 280px;
    transform: translateX(-100%);
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);

    &.sidebar-open {
      transform: translateX(0);
    }
  }

  @media (max-width: 600px) {
    width: 100%;
    max-width: 280px;
  }
}

.sidebar-overlay {
  position: fixed;
  top: 120px;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 998;
}

.sidebar-nav {
  padding: 1rem 0;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.nav-section {
  margin-bottom: 1.5rem;
}

.nav-label {
  padding: 0 1.5rem;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: rgba(var(--v-theme-on-background), 0.5);
  margin: 0.5rem 0;
}

.nav-items {
  display: flex;
  flex-direction: column;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem 1rem;
  margin: 0.25rem 0.5rem;
  color: rgb(var(--v-theme-on-background));
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.2s ease;
  font-size: 0.95rem;

  &:hover {
    background: rgba(var(--v-theme-primary), 0.08);
    color: rgb(var(--v-theme-primary));
    transform: translateX(4px);
  }

  &.active {
    background: rgba(var(--v-theme-primary), 0.12);
    color: rgb(var(--v-theme-primary));
    font-weight: 600;
    box-shadow: inset 3px 0 0 rgb(var(--v-theme-primary));
  }

  .v-icon {
    transition: transform 0.2s ease;
  }

  &:active .v-icon {
    transform: scale(1.1);
  }
}

/* ========================================
   PROFILE SECTION
   ======================================== */
.profile-section {
  margin-top: auto;
  padding: 1rem;
  border-top: 1px solid rgba(0, 0, 0, 0.08);
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  background: rgba(var(--v-theme-primary), 0.05);
  border-radius: 8px;
}

.user-info {
  flex: 1;
  min-width: 0;

  .user-name {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: rgb(var(--v-theme-on-background));
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .user-type {
    margin: 0.25rem 0 0 0;
    font-size: 0.75rem;
    color: rgba(var(--v-theme-on-background), 0.6);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
}

/* ========================================
   MAIN CONTENT AREA
   ======================================== */
.layout-main {
  flex: 1;
  margin-left: 250px;
  background: rgb(var(--v-theme-background));
  transition: margin-left 0.3s ease, background-color 0.3s ease;

  @media (max-width: 1024px) {
    margin-left: 0;
  }
}

.content-wrapper {
  padding: 2rem;
  min-height: calc(100vh - 120px);
  background: rgb(var(--v-theme-background));
  transition: background-color 0.3s ease;

  @media (max-width: 960px) {
    padding: 1.5rem;
  }

  @media (max-width: 600px) {
    padding: 1rem;
  }
}

/* ========================================
   SCROLLBAR STYLING
   ======================================== */
.layout-sidebar::-webkit-scrollbar {
  width: 6px;
}

.layout-sidebar::-webkit-scrollbar-track {
  background: transparent;
}

.layout-sidebar::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.2);
  border-radius: 3px;

  &:hover {
    background: rgba(0, 0, 0, 0.3);
  }
}

/* ========================================
   DARK MODE
   ======================================== */
.v-theme--dark {
  .layout-header {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  }

  .nav-item:hover {
    background: rgba(var(--v-theme-primary), 0.12);
  }

  .sidebar-overlay {
    background: rgba(0, 0, 0, 0.7);
  }

  .notification-menu {
    max-width: 350px;
  }
}

/* ========================================
   NOTIFICATION MENU
   ======================================== */
.notification-menu {
  min-width: 350px;
  max-width: 350px;

  .v-list-item {
    transition: background-color 0.2s ease;

    &:hover {
      background: rgba(var(--v-theme-primary), 0.08);
    }
  }
}
</style>
