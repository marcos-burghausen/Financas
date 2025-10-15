<template>
  <div
    class="fundo__menu"
    :class="{ displayNone: !props.menuExpandido }"
  >
    <nav
      class="menu-lateral"
    >
      <!-- :class="{ expandido: props.menuExpandido }" -->
      <div class="container__logo">
        <router-link :to="{ name: 'dashboard' }">
          <img
            src="@/assets/img/2.png"
            alt="logo"
          >
        </router-link>
        <h1
          :class="{ displayNone: !props.menuExpandido }"
          class="title"
        >
          Gerenciador Financeiro
        </h1>
        <mdicon
          name="menu-open"
          class="mdicon"
          @click="$emit('expandirMenu')"
        />
      </div>
      <ul>
        <li
          v-for="(item, index) in itensSideBar"
          :key="index"
          :class="{ efeitoClick: elementoAtivoSideBar === index }"
          @click="$emit('expandirMenu')"
        >
          <router-link
            :to="{ name: item.route }"
          >
            <span class="icon">
              <mdicon :name="item.icon" />
            </span>
            <span
              class="txt__link"
              :class="{ displayNone: !props.menuExpandido }"
            >{{
              item.name
            }}</span>
          </router-link>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup lang="ts">
import { useRolesStore } from "@/store/roles";
import { computed, onMounted, ref, watch } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const rolesStore = useRolesStore();

let elementoAtivoSideBar = ref();

watch(route, (value) => {
    switch (value.name) {
    case "dashboard":
        elementoAtivoSideBar.value = 0;
        break;
    case "contas":
        elementoAtivoSideBar.value = 1;
        break;
    case "receitas":
        elementoAtivoSideBar.value = 2;
        break;
    case "despesas":
        elementoAtivoSideBar.value = 3;
        break;
    case "categorias":
        elementoAtivoSideBar.value = 4;
        break;
    case "notificacoes":
        elementoAtivoSideBar.value = 5;
        break;
    case "perfil":
        elementoAtivoSideBar.value = 6;
        break;
    case "admin":
        elementoAtivoSideBar.value = 7;
        break;
    case "trader":
        elementoAtivoSideBar.value = 8;
        break;
    }

});

const props = defineProps({
    menuExpandido: Boolean
});

const baseItems = [
    { name: "DashBoard", icon: "view-dashboard", route: "dashboard" },
    { name: "Contas", icon: "bank-outline", route: "contas" },
    { name: "Receitas", icon: "arrow-top-right-bold-outline", route: "receitas" },
    { name: "Despesas", icon: "arrow-bottom-right-bold-outline", route: "despesas" },
    { name: "Categorias", icon: "bookmark-minus-outline", route: "categorias" },
    { name: "Notificações", icon: "bell-ring", route: "notificacoes" },
    { name: "Perfil", icon: "account-circle", route: "perfil" },
];

const itensSideBar = computed(() => {
    const items = [...baseItems];
    
    // Forçar reatividade acessando os valores diretamente
    const roles = rolesStore.myRoles;
    const adminStatus = rolesStore.isAdmin;
    
    // DEBUG: Verificar itens do menu
    console.log('🔍 MenuLateral - Base Items:', baseItems.length);
    console.log('🔍 MenuLateral - Items com base:', items.map(i => i.name));
    
    // Adicionar item Admin se usuário for admin
    if (adminStatus) {
        items.push({ name: "Admin", icon: "shield-crown", route: "admin" });
        console.log('✅ MenuLateral - Admin adicionado');
    }
    
    // Adicionar item Trader se usuário tiver permissão
    const hasTraderRole = roles.includes('TRADER') || 
                         roles.includes('USER_TRADER') || 
                         roles.includes('FULL');
    if (hasTraderRole) {
        items.push({ name: "Trader", icon: "chart-line", route: "trader" });
        console.log('✅ MenuLateral - Trader adicionado');
    }
    
    console.log('🎯 MenuLateral - Items finais:', items.length, items.map(i => i.name));
    
    return items;
});

// Carregar permissões ao montar o componente
onMounted(async () => {
    if (rolesStore.myRoles.length === 0) {
        try {
            await rolesStore.fetchMyPermissions();
        } catch (error) {
            console.error('Erro ao carregar permissões:', error);
        }
    }
});

// Watch para monitorar mudanças nas roles
watch(() => rolesStore.myRoles, (newRoles) => {
    // Reativa o computed quando as roles mudam
}, { deep: true });
</script>

<style scoped>
.menu-lateral {
  background-color: rgb(44, 44, 46);
  /* margin: 0 15px 0 0; */
  width: 280px;
  padding: 10px 8px 40px 0;
  /* box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23; */
  /* transition: 0.5s; */
  height: 100%;
  overflow-y: auto; /* ADICIONADO: Permitir scroll */
  overflow-x: hidden;
}

/* ADICIONADO: Estilizar scrollbar */
.menu-lateral::-webkit-scrollbar {
  width: 6px;
}

.menu-lateral::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
}

.menu-lateral::-webkit-scrollbar-thumb {
  background: #77d08e;
  border-radius: 10px;
}

.menu-lateral::-webkit-scrollbar-thumb:hover {
  background: #5fb876;
}

.fundo__menu {
  background-color: rgba(0, 0, 0, 0.6);
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1000;
  /* transition: transform 10s ease;
  transform: translateY(0%); */
}

.container__logo {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;

}

.container__logo img {
    margin-left: 10px;
    width: 70px;
}

.displayNone {
    display: none;
}

.title {
    color: #fefefe;
    font-size: 20px;
    margin: 0;
    text-align: center;
    padding: 10px 0;
}
.mdicon {
    color: #77d08e;
    cursor: pointer;
    /* padding: 10px; */
    box-shadow: -4px -4px 5px #3e4247, 7px 7px 7px #1d1f23;
    border-radius: 50px;
    width: 70px;
    height: 50px;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 10px 10px 0 0;
}

.efeitoClick {
    box-shadow: inset -4px -4px 5px #3e4247, inset 7px 7px 7px #1d1f23;
    border-top-right-radius: 32px;
    border-bottom-right-radius: 32px;
}

.menu-lateral ul {
    padding-left: 0;
}

.icon {
    margin-left: 13px;
    color: #77d08e;
}

.txt__link {
    margin-left: 20px;
    transition: 0.5s;
    color: #fefefe;
    text-align: center !important;
}

.menu-lateral ul li a {
    text-decoration: none;
    font-size: 20px;
    padding: 10px 7%;
    display: flex;
}

.expandido {
    width: 220px;
}
</style>
